<?php
/**
 * User Model
 * Quản lý các thao tác liên quan đến bảng users
 */

class UserModel {
    private $conn;
    private $table_name = "users";

    public $id;
    public $full_name;
    public $username;
    public $email;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Đăng ký người dùng mới
    public function register() {
        $query = "INSERT INTO " . $this->table_name . " 
                (full_name, username, email, password, created_at) 
                VALUES (:full_name, :username, :email, :password, NOW())";
        
        $stmt = $this->conn->prepare($query);

        // Làm sạch dữ liệu
        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->username = htmlspecialchars(strip_tags($this->username));
        $this->email = htmlspecialchars(strip_tags($this->email));
        
        // Hash mật khẩu
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);

        $stmt->bindParam(":full_name", $this->full_name);
        $stmt->bindParam(":username", $this->username);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password", $password_hash);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Kiểm tra đăng nhập
    public function login($username, $password) {
        $query = "SELECT id, full_name, username, password, role, is_locked 
                  FROM " . $this->table_name . " 
                  WHERE username = :username LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $row['password'])) {
                return $row; // Trả về đầy đủ: id, full_name, username, role, is_locked
            }
        }
        return false;
    }

    // Kiểm tra username đã tồn tại chưa
    public function isUsernameExists($username) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
