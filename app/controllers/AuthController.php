<?php
/**
 * Auth Controller
 * Xử lý yêu cầu từ View và gọi Model tương ứng
 */

require_once '../app/models/UserModel.php';

class AuthController {
    private $db;
    private $userModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->userModel = new UserModel($this->db);
    }

    // Lấy IP thực của người dùng
    private function getRealIP() {
        foreach (['HTTP_CLIENT_IP','HTTP_X_FORWARDED_FOR','REMOTE_ADDR'] as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = explode(',', $_SERVER[$key])[0];
                return trim($ip);
            }
        }
        return '0.0.0.0';
    }

    // Ghi log đăng nhập (có IP, user agent, status)
    private function writeLoginLog($user_id, $status = 'success') {
        $stmt = $this->db->prepare("
            INSERT INTO login_logs (user_id, login_time, ip_address, user_agent, status)
            VALUES (:uid, NOW(), :ip, :ua, :status)
        ");
        $stmt->execute([
            ':uid'    => $user_id,
            ':ip'     => $this->getRealIP(),
            ':ua'     => $_SERVER['HTTP_USER_AGENT'] ?? '',
            ':status' => $status,
        ]);
        return $this->db->lastInsertId();
    }

    // Hiển thị trang đăng nhập
    public function login() {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?url=location/dashboard");
            exit();
        }

        $error = "";
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->userModel->login($username, $password);
            if ($user) {
                // Kiểm tra tài khoản bị khóa
                if ($user['is_locked'] ?? 0) {
                    $this->writeLoginLog($user['id'], 'failed');
                    $error = "Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.";
                } else {
                    $_SESSION['user_id']   = $user['id'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['username']  = $user['username'];

                    // Ghi log đăng nhập thành công
                    $log_id = $this->writeLoginLog($user['id'], 'success');
                    $_SESSION['login_log_id'] = $log_id;

                    // Chuyển hướng admin lên trang admin dashboard
                    if (in_array($user['role'] ?? 'user', ['admin', 'moderator'])) {
                        header("Location: index.php?url=admin/dashboard");
                    } else {
                        header("Location: index.php?url=location/dashboard");
                    }
                    exit();
                }
            } else {
                $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
            }
        }
        require_once '../app/views/auth/login.php';
    }

    // Hiển thị trang đăng ký
    public function register() {
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?url=location/dashboard");
            exit();
        }

        // Kiểm tra setting allow_register
        try {
            $allow = $this->db->query("SELECT value FROM system_settings WHERE `key` = 'allow_register'")->fetchColumn();
            if ($allow === '0') {
                require_once '../app/views/auth/register_closed.php';
                exit();
            }
        } catch (Exception $e) {
            // Bảng chưa tồn tại thì bỏ qua
        }

        $error = "";
        $success = "";

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->userModel->full_name = $_POST['full_name'];
            $this->userModel->username  = $_POST['username'];
            $this->userModel->email     = $_POST['email'];
            $this->userModel->password  = $_POST['password'];

            if ($this->userModel->isUsernameExists($_POST['username'])) {
                $error = "Tên đăng nhập đã tồn tại!";
            } else {
                if ($this->userModel->register()) {
                    $success = "Đăng ký thành công! Vui lòng đăng nhập.";
                } else {
                    $error = "Có lỗi xảy ra, vui lòng thử lại.";
                }
            }
        }
        require_once '../app/views/auth/register.php';
    }

    // Đăng xuất
    public function logout() {
        // Ghi logout_time vào login_logs
        if (isset($_SESSION['login_log_id']) && $_SESSION['login_log_id']) {
            try {
                $stmt = $this->db->prepare("UPDATE login_logs SET logout_time = NOW() WHERE id = :id");
                $stmt->execute([':id' => $_SESSION['login_log_id']]);
            } catch (Exception $e) {}
        }
        session_destroy();
        header("Location: index.php?url=auth/login");
        exit();
    }
}
?>
