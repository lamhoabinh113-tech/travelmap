<?php
/**
 * Database Configuration
 * Quản lý kết nối MySQL sử dụng PDO
 */

class Database {
    private $host = "sql207.infinityfree.com";
    private $db_name = "if0_41993995_travel_memory_map";
    private $username = "if0_41993995";
    private $password = "hoabinh2004";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8mb4");
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $exception) {
            echo "Kết nối database thất bại: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>
