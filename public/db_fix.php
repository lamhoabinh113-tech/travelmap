<?php
/**
 * Database Migration Fix Helper
 * Tự động kiểm tra và thêm các cột còn thiếu trên database host InfinityFree.
 */

// Bật hiển thị lỗi để thấy kết quả chạy
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h3>Bắt đầu kiểm tra và cập nhật cấu trúc Database...</h3>";
    
    // 1. Kiểm tra cột reaction_type trong bảng likes
    $stmt = $db->query("SHOW COLUMNS FROM likes LIKE 'reaction_type'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE likes ADD COLUMN reaction_type VARCHAR(20) DEFAULT 'heart' AFTER location_id");
        echo "✅ Đã thêm cột <b>reaction_type</b> vào bảng <b>likes</b>.<br>";
    } else {
        echo "ℹ️ Cột <b>reaction_type</b> đã tồn tại trong bảng <b>likes</b>.<br>";
    }
    
    // 2. Kiểm tra cột xp trong bảng users
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'xp'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE users ADD COLUMN xp INT DEFAULT 0 AFTER role");
        echo "✅ Đã thêm cột <b>xp</b> vào bảng <b>users</b>.<br>";
    } else {
        echo "ℹ️ Cột <b>xp</b> đã tồn tại trong bảng <b>users</b>.<br>";
    }

    // 3. Kiểm tra cột trip_id trong bảng locations
    $stmt = $db->query("SHOW COLUMNS FROM locations LIKE 'trip_id'");
    if ($stmt->rowCount() == 0) {
        $db->exec("ALTER TABLE locations ADD COLUMN trip_id INT DEFAULT NULL AFTER privacy");
        echo "✅ Đã thêm cột <b>trip_id</b> vào bảng <b>locations</b>.<br>";
    } else {
        echo "ℹ️ Cột <b>trip_id</b> đã tồn tại trong bảng <b>locations</b>.<br>";
    }

    // 4. Tạo bảng trips nếu chưa có
    $db->exec("CREATE TABLE IF NOT EXISTS `trips` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `description` TEXT DEFAULT NULL,
        `start_date` DATE DEFAULT NULL,
        `end_date` DATE DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "ℹ️ Đã kiểm tra bảng <b>trips</b>.<br>";

    // 5. Tạo bảng trip_members nếu chưa có
    $db->exec("CREATE TABLE IF NOT EXISTS `trip_members` (
        `id`         INT AUTO_INCREMENT PRIMARY KEY,
        `trip_id`    INT NOT NULL,
        `user_id`    INT NOT NULL,
        `role`       ENUM('member','admin') DEFAULT 'member',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY `unique_member` (`trip_id`, `user_id`),
        FOREIGN KEY (`trip_id`) REFERENCES `trips`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    echo "ℹ️ Đã kiểm tra bảng <b>trip_members</b>.<br>";
    
    // 6. Tự động tính toán lại XP của tất cả người dùng dựa trên số lượng kỷ niệm đã đăng
    $db->exec("UPDATE users u SET u.xp = (SELECT COUNT(*) * 20 FROM locations l WHERE l.user_id = u.id)");
    echo "✅ Đã tính toán lại và khôi phục <b>XP tích lũy</b> cho toàn bộ người dùng dựa trên số địa điểm đã check-in.<br>";

    echo "<br><h4 style='color:green;'>🎉 Cập nhật cấu trúc database và khôi phục XP thành công! Hãy tải lại trang dashboard để kiểm tra.</h4>";
    
} catch (PDOException $e) {
    echo "<h4 style='color:red;'>❌ Lỗi nâng cấp: " . $e->getMessage() . "</h4>";
}
?>
