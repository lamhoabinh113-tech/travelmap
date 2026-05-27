<?php
/**
 * Front Controller
 * Điều phối mọi request trong hệ thống MVC
 */

session_start();

// Tự động load các file cần thiết
require_once '../config/database.php';

// Định nghĩa base path (điều chỉnh nếu bạn để trong thư mục con của htdocs)
define('BASE_URL', ''); 

// Lấy controller và action từ URL (ví dụ: index.php?url=auth/login)
$url = isset($_GET['url']) ? $_GET['url'] : 'home';
$url = rtrim($url, '/');
$parts = explode('/', $url);

$controllerName = 'LocationController';
$action = 'index';

// Phân tích URL để xác định Controller và Action
if (isset($parts[0]) && $parts[0] != 'home') {
    $controllerName = ucfirst($parts[0]) . 'Controller';
    if (isset($parts[1])) {
        $action = $parts[1];
    }
    
    $controllerPath = "../app/controllers/" . $controllerName . ".php";
    if (file_exists($controllerPath)) {
        require_once $controllerPath;
        $controller = new $controllerName();
        if (method_exists($controller, $action)) {
            $controller->$action();
        } else {
            echo "404 - Action not found!";
        }
    } else {
        echo "404 - Controller not found!";
    }
} else {
    // Hiển thị Landing Page (Trang chủ)
    require_once "../app/views/home.php";
}
?>
