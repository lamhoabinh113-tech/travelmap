<?php
/**
 * Database Configuration
 * Quản lý kết nối MySQL sử dụng PDO
 * 
 * ===== XAMPP LOCAL =====
 * Đang kết nối tới MySQL local (XAMPP)
 * Để deploy lên host: đổi lại host, db_name, username, password
 */

class Database {
    private $host     = "localhost";
    private $db_name  = "travel_memory_map";
    private $username = "root";
    private $password = "";          // XAMPP mặc định không có password
    public  $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->exec("SET NAMES utf8mb4");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            // Hiển thị lỗi chi tiết khi đang develop local
            die("❌ Kết nối database thất bại: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
?>
