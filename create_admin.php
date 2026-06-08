<?php
/**
 * Script tạo admin account với password hash đúng
 * Truy cập: http://localhost/travel-memory-map/create_admin.php
 * XÓA FILE NÀY SAU KHI CHẠY XONG!
 */

require_once 'config/database.php';

$db = (new Database())->getConnection();

if (!$db) {
    die("❌ Không kết nối được database. Hãy import SQL trước!");
}

// Kiểm tra xem đã có admin chưa
$check = $db->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();

if ($check > 0) {
    echo "✅ Đã có tài khoản admin rồi.<br>";
    echo "👉 <a href='public/index.php?url=auth/login'>Đến trang đăng nhập</a>";
} else {
    // Tạo admin mới
    $password = 'Admin@123';
    $hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $db->prepare("INSERT INTO users (full_name, username, email, password, role) VALUES (?, ?, ?, ?, 'admin')");
    $ok = $stmt->execute(['Administrator', 'admin', 'admin@travelmap.local', $hash]);

    if ($ok) {
        echo "✅ Đã tạo tài khoản admin thành công!<br>";
        echo "👤 Username: <strong>admin</strong><br>";
        echo "🔑 Password: <strong>Admin@123</strong><br><br>";
        echo "⚠️ Hãy xóa file này ngay sau khi đăng nhập!<br><br>";
        echo "👉 <a href='public/index.php?url=auth/login'>Đến trang đăng nhập</a>";
    } else {
        echo "❌ Tạo admin thất bại.";
    }
}
?>
