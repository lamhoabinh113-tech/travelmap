<?php
session_start();
require_once '../config/database.php';

try {
    $db = (new Database())->getConnection();
    
    echo "<h3>Thông tin Session hiện tại:</h3>";
    echo "<pre>";
    print_r($_SESSION);
    echo "</pre>";

    echo "<h3>Danh sách tài khoản trong CSDL:</h3>";
    $stmt = $db->query("SELECT id, username, full_name, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Full Name</th><th>Role</th></tr>";
    foreach ($users as $u) {
        echo "<tr>";
        echo "<td>" . $u['id'] . "</td>";
        echo "<td>" . htmlspecialchars($u['username']) . "</td>";
        echo "<td>" . htmlspecialchars($u['full_name']) . "</td>";
        echo "<td>" . htmlspecialchars($u['role']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>
