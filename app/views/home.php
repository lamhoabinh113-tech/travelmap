<?php
/**
 * Home Page - Tự động chuyển hướng đến Dashboard nếu đã đăng nhập
 */

// Nếu đã đăng nhập → chuyển thẳng sang Dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php?url=location/dashboard");
    exit();
}

// Nếu chưa đăng nhập → chuyển đến trang đăng nhập
header("Location: index.php?url=auth/login");
exit();
?>
