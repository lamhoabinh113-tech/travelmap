<?php
/**
 * Admin Controller
 * Xử lý toàn bộ chức năng trang quản trị
 */
require_once '../app/models/AdminModel.php';

class AdminController {
    private $db;
    private $adminModel;
    const PER_PAGE = 20;

    public function __construct() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/login");
            exit();
        }
        // Kiểm tra quyền admin
        $database = new Database();
        $this->db = $database->getConnection();

        $stmt = $this->db->prepare("SELECT role, is_locked FROM users WHERE id = :id");
        $stmt->execute([':id' => $_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !in_array($user['role'], ['admin', 'moderator']) || $user['is_locked']) {
            header("Location: index.php?url=location/dashboard&error=access_denied");
            exit();
        }

        $_SESSION['admin_role'] = $user['role'];
        $this->adminModel = new AdminModel($this->db);
    }

    // ----- HELPER -----
    private function log($action, $target_type = '', $target_id = 0, $detail = '') {
        $this->adminModel->addActivityLog($_SESSION['user_id'], $action, $target_type, $target_id, $detail);
    }

    private function isAdmin() {
        return $_SESSION['admin_role'] === 'admin';
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    // ===================== DASHBOARD =====================

    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $stats        = $this->adminModel->getOverviewStats();
        $posts_chart  = $this->adminModel->getPostsPerDay(14);
        $logins_chart = $this->adminModel->getLoginsPerDay(14);
        $top_users    = $this->adminModel->getTopActiveUsers(8);
        $feelings     = $this->adminModel->getFeelingDistribution();
        $recent_logs  = $this->adminModel->getActivityLog(10);
        require_once '../app/views/admin/dashboard.php';
    }

    // ===================== USER MANAGEMENT =====================

    public function users() {
        $search  = trim($_GET['search'] ?? '');
        $role    = $_GET['role'] ?? '';
        $status  = $_GET['status'] ?? '';
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $offset  = ($page - 1) * self::PER_PAGE;

        $users      = $this->adminModel->getAllUsers($search, $role, $status, self::PER_PAGE, $offset);
        $total      = $this->adminModel->countUsers($search, $role, $status);
        $totalPages = ceil($total / self::PER_PAGE);

        require_once '../app/views/admin/users.php';
    }

    public function editUser() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?url=admin/users");
            exit();
        }
        $id   = (int)$_POST['id'];
        $data = [
            'full_name' => $_POST['full_name'],
            'email'     => $_POST['email'],
            'role'      => $_POST['role'],
            'is_locked' => (int)($_POST['is_locked'] ?? 0),
        ];
        $this->adminModel->updateUser($id, $data);
        $this->log('Chỉnh sửa tài khoản', 'user', $id, "Cập nhật: " . json_encode($data));
        header("Location: index.php?url=admin/users&success=updated");
        exit();
    }

    public function deleteUser() {
        if (!$this->isAdmin()) { $this->jsonResponse(['success'=>false,'msg'=>'Không đủ quyền']); }
        $id = (int)($_GET['id'] ?? 0);
        if ($id === (int)$_SESSION['user_id']) { $this->jsonResponse(['success'=>false,'msg'=>'Không thể tự xóa chính mình']); }
        $this->adminModel->deleteUser($id);
        $this->log('Xóa tài khoản', 'user', $id);
        $this->jsonResponse(['success'=>true]);
    }

    public function toggleLock() {
        $id = (int)($_GET['id'] ?? 0);
        if ($id === (int)$_SESSION['user_id']) { $this->jsonResponse(['success'=>false,'msg'=>'Không thể tự khóa chính mình']); }
        $this->adminModel->toggleLock($id);
        $this->log('Khóa/Mở khóa tài khoản', 'user', $id);
        $this->jsonResponse(['success'=>true]);
    }

    public function resetPassword() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->jsonResponse(['success'=>false]); }
        $id       = (int)$_POST['id'];
        $new_pass = $_POST['new_password'];
        if (strlen($new_pass) < 6) { $this->jsonResponse(['success'=>false,'msg'=>'Mật khẩu tối thiểu 6 ký tự']); }
        $hashed = password_hash($new_pass, PASSWORD_BCRYPT);
        $this->adminModel->resetPassword($id, $hashed);
        $this->log('Reset mật khẩu', 'user', $id);
        $this->jsonResponse(['success'=>true]);
    }

    // ===================== POST MANAGEMENT =====================

    public function posts() {
        $search  = trim($_GET['search'] ?? '');
        $user_id = (int)($_GET['user_id'] ?? 0);
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $offset  = ($page - 1) * self::PER_PAGE;

        $posts      = $this->adminModel->getAllPosts($search, $user_id ?: '', self::PER_PAGE, $offset);
        $total      = $this->adminModel->countPosts($search, $user_id ?: '');
        $totalPages = ceil($total / self::PER_PAGE);

        require_once '../app/views/admin/posts.php';
    }

    public function deletePost() {
        $id     = (int)($_GET['id'] ?? 0);
        $images = $this->adminModel->deletePost($id);
        foreach ($images as $img) {
            $path = "../uploads/$img";
            if (file_exists($path)) unlink($path);
        }
        $this->log('Xóa bài đăng', 'post', $id);
        $this->jsonResponse(['success'=>true]);
    }

    public function toggleHidePost() {
        $id = (int)($_GET['id'] ?? 0);
        $this->adminModel->toggleHidePost($id);
        $this->log('Ẩn/Hiện bài đăng', 'post', $id);
        $this->jsonResponse(['success'=>true]);
    }

    // ===================== LOGIN LOGS =====================

    public function loginLogs() {
        $user_filter = trim($_GET['user'] ?? '');
        $date_from   = $_GET['from'] ?? '';
        $date_to     = $_GET['to'] ?? '';
        $page        = max(1, (int)($_GET['page'] ?? 1));
        $offset      = ($page - 1) * 30;

        $logs       = $this->adminModel->getLoginLogs($user_filter, $date_from, $date_to, 30, $offset);
        $total      = $this->adminModel->countLoginLogs($user_filter, $date_from, $date_to);
        $totalPages = ceil($total / 30);

        require_once '../app/views/admin/login_logs.php';
    }

    // ===================== INTERACTIONS =====================

    public function interactions() {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $offset  = ($page - 1) * self::PER_PAGE;

        $friendships  = $this->adminModel->getAllFriendships(self::PER_PAGE, $offset);
        $total        = $this->db->query("SELECT COUNT(*) FROM friendships")->fetchColumn();
        $totalPages   = ceil($total / self::PER_PAGE);
        $likes_total  = $this->db->query("SELECT COUNT(*) FROM likes")->fetchColumn();

        require_once '../app/views/admin/interactions.php';
    }

    public function deleteFriendship() {
        $id = (int)($_GET['id'] ?? 0);
        $this->adminModel->deleteFriendship($id);
        $this->log('Hủy kết bạn', 'friendship', $id);
        $this->jsonResponse(['success'=>true]);
    }

    // ===================== ACTIVITY LOG =====================

    public function activityLog() {
        $page    = max(1, (int)($_GET['page'] ?? 1));
        $offset  = ($page - 1) * 30;

        $logs       = $this->adminModel->getActivityLog(30, $offset);
        $total      = $this->adminModel->countActivityLog();
        $totalPages = ceil($total / 30);

        require_once '../app/views/admin/activity_log.php';
    }

    // ===================== SETTINGS =====================

    public function settings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->isAdmin()) {
                header("Location: index.php?url=admin/settings&error=no_permission");
                exit();
            }
            $keys = [
                'allow_register','max_upload_size','max_images_per_album',
                'allow_sharing','map_default_zoom','realtime_interval'
            ];
            foreach ($keys as $key) {
                if (isset($_POST[$key])) {
                    $this->adminModel->setSetting($key, $_POST[$key]);
                }
            }
            $this->log('Cập nhật cài đặt hệ thống', 'settings', 0, json_encode($_POST));
            header("Location: index.php?url=admin/settings&success=saved");
            exit();
        }

        $settings = $this->adminModel->getAllSettings();
        require_once '../app/views/admin/settings.php';
    }

    // ===================== STATS API =====================

    public function statsApi() {
        $type = $_GET['type'] ?? 'overview';
        if ($type === 'overview') {
            $this->jsonResponse($this->adminModel->getOverviewStats());
        } elseif ($type === 'posts_chart') {
            $this->jsonResponse($this->adminModel->getPostsPerDay(14));
        } elseif ($type === 'logins_chart') {
            $this->jsonResponse($this->adminModel->getLoginsPerDay(14));
        }
    }
}
?>
