<?php
/**
 * Front Controller
 * Điều phối mọi request trong hệ thống MVC
 */

session_start();

// Ngăn trình duyệt lưu cache trang HTML để tránh lỗi lưu thông tin tài khoản cũ
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Tự động load các file cần thiết
require_once '../config/database.php';

// ============================================================
// Tự động phát hiện BASE_PATH từ filesystem — chính xác 100%
// Không bị ảnh hưởng bởi mod_rewrite hay URL rewriting
// ============================================================
$docRoot  = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
$projRoot = rtrim(str_replace('\\', '/', dirname(__DIR__)), '/');  // thư mục gốc project (trên public/)
$basePath = str_replace($docRoot, '', $projRoot);                  // vd: /travel-memory-map

define('BASE_PATH',   $basePath);
define('UPLOADS_URL', $basePath . '/uploads');                     // /travel-memory-map/uploads
define('BASE_URL',    $basePath . '/public/index.php');

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
