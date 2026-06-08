<?php
/**
 * Trip Controller
 * Xử lý các yêu cầu liên quan đến quản lý chuyến đi (nhóm địa điểm)
 */

require_once '../app/models/TripModel.php';

class TripController {
    private $db;
    private $tripModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/login");
            exit();
        }
        $database = new Database();
        $this->db = $database->getConnection();
        $this->tripModel = new TripModel($this->db);
    }

    // Xử lý tạo mới chuyến đi (AJAX)
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->tripModel->user_id = $_SESSION['user_id'];
            $this->tripModel->title = $_POST['title'] ?? 'Chuyến đi mới';
            $this->tripModel->description = $_POST['description'] ?? '';
            $this->tripModel->start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : null;
            $this->tripModel->end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;

            if ($this->tripModel->create()) {
                echo json_encode(['success' => true, 'id' => $this->tripModel->id, 'title' => $this->tripModel->title]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi lưu chuyến đi']);
            }
            exit();
        }
    }

    // Lấy danh sách chuyến đi (AJAX)
    public function list() {
        $trips = $this->tripModel->getByUser($_SESSION['user_id']);
        echo json_encode(['success' => true, 'data' => $trips]);
        exit();
    }

    // Mời bạn bè vào chuyến đi (AJAX)
    public function addMember() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $trip_id = $_POST['trip_id'] ?? 0;
            $username = $_POST['username'] ?? '';
            $user_id = $_SESSION['user_id'];

            // Kiểm tra quyền sở hữu chuyến đi
            $q_check = "SELECT id FROM trips WHERE id = :tid AND user_id = :uid";
            $s_check = $this->db->prepare($q_check);
            $s_check->execute([':tid' => $trip_id, ':uid' => $user_id]);
            if ($s_check->rowCount() == 0) {
                echo json_encode(['success' => false, 'message' => 'Bạn không có quyền quản lý chuyến đi này']);
                exit();
            }

            // Tìm user qua username
            $q_user = "SELECT id FROM users WHERE username = :uname";
            $s_user = $this->db->prepare($q_user);
            $s_user->execute([':uname' => $username]);
            $member = $s_user->fetch(PDO::FETCH_ASSOC);

            if (!$member) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy người dùng này']);
                exit();
            }

            if ($member['id'] == $user_id) {
                echo json_encode(['success' => false, 'message' => 'Bạn đã là chủ chuyến đi rồi']);
                exit();
            }

            try {
                $q_ins = "INSERT INTO trip_members (trip_id, user_id, role) VALUES (:tid, :uid, 'member')";
                $s_ins = $this->db->prepare($q_ins);
                $s_ins->execute([':tid' => $trip_id, ':uid' => $member['id']]);
                echo json_encode(['success' => true, 'message' => 'Đã thêm thành viên thành công']);
            } catch (Exception $e) {
                // Nếu đã tồn tại
                echo json_encode(['success' => false, 'message' => 'Người dùng này đã ở trong chuyến đi']);
            }
            exit();
        }
    }
}
?>
