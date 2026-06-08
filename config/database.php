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
    public  $conn;

    public function getConnection() {
        $this->conn = null;
        
        // Tải cấu hình từ file db_config.php riêng tư (không đưa lên Git)
        $configFile = __DIR__ . '/db_config.php';
        if (file_exists($configFile)) {
            $config = require $configFile;
            $host     = $config['host'] ?? 'localhost';
            $db_name  = $config['db_name'] ?? 'travel_memory_map';
            $username = $config['username'] ?? 'root';
            $password = $config['password'] ?? '';
        } else {
            // Fallback mặc định cho XAMPP
            $host     = 'localhost';
            $db_name  = 'travel_memory_map';
            $username = 'root';
            $password = '';
        }

        try {
            $this->conn = new PDO(
                "mysql:host=" . $host . ";dbname=" . $db_name . ";charset=utf8mb4",
                $username,
                $password
            );
            $this->conn->exec("SET NAMES utf8mb4");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            die("❌ Kết nối database thất bại: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
?>
