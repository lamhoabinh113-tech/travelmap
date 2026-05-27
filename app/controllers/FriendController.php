<?php
/**
 * Friend Controller
 * Quản lý các mối quan hệ bạn bè
 */

class FriendController {
    private $db;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/login");
            exit();
        }
        $database = new Database();
        $this->db = $database->getConnection();
    }

    // Thêm bạn qua link/token
    public function add() {
        if (isset($_GET['token']) || isset($_POST['invite_link'])) {
            $token = isset($_GET['token']) ? $_GET['token'] : "";
            
            // Nếu dán link, trích xuất token
            if (isset($_POST['invite_link'])) {
                $parts = parse_url($_POST['invite_link']);
                if (isset($parts['query'])) {
                    parse_str($parts['query'], $query);
                    $token = isset($query['token']) ? $query['token'] : "";
                }
            }

            if ($token != "") {
                // Giải mã token (trong ví dụ này token = user_id * 12345)
                $friend_id = $token / 12345;
                $user_id = $_SESSION['user_id'];

                if ($friend_id == $user_id) {
                    header("Location: index.php?url=location/dashboard&friend_error=self");
                    exit();
                }

                // Kiểm tra xem đã là bạn chưa
                $check_query = "SELECT * FROM friendships WHERE (user_id = :u1 AND friend_id = :f1) OR (user_id = :f2 AND friend_id = :u2)";
                $stmt = $this->db->prepare($check_query);
                $stmt->execute([':u1' => $user_id, ':f1' => $friend_id, ':f2' => $friend_id, ':u2' => $user_id]);
                
                if ($stmt->rowCount() == 0) {
                    $query = "INSERT INTO friendships (user_id, friend_id, status) VALUES (:user_id, :friend_id, 'accepted')";
                    $stmt = $this->db->prepare($query);
                    if ($stmt->execute([':user_id' => $user_id, ':friend_id' => $friend_id])) {
                        header("Location: index.php?url=location/dashboard&friend_success=1");
                    } else {
                        header("Location: index.php?url=location/dashboard&friend_error=1");
                    }
                } else {
                    header("Location: index.php?url=location/dashboard&friend_error=exists");
                }
                exit();
            }
        }
    }
}
