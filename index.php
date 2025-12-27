<?php
// index.php
session_start();

// 1. Kết nối CSDL
require_once __DIR__ . '/app/config/ConnectDB.php';
// 2. Load base classes
require_once __DIR__ . '/app/config/BaseClasses.php';

// 2. Lấy tham số controller và action từ URL
// Ví dụ: index.php?controller=home&action=index
$controllerName = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// 3. Điều hướng (Routing)
switch (strtolower($controllerName)) {
    case 'home':
        // Xử lý OPcache reset nếu có request
        if (isset($_GET['opcache_reset']) && function_exists('opcache_reset')) {
            opcache_reset();
            if (function_exists('opcache_invalidate')) {
                opcache_invalidate(__DIR__ . '/app/controllers/Home.php', true);
            }
            header("Location: index.php?controller=home&action=index");
            exit();
        }
        
        // Clear OPcache và invalidate file cụ thể
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate(__DIR__ . '/app/controllers/Home.php', true);
        }
        
        // Load file Home.php - dùng require thay vì require_once để force reload
        $homeFile = __DIR__ . '/app/controllers/Home.php';
        if (!file_exists($homeFile)) {
            die("File không tồn tại: $homeFile");
        }
        
        // Nếu class đã tồn tại nhưng thiếu methods, báo lỗi rõ ràng
        if (class_exists('Home')) {
            $reflection = new ReflectionClass('Home');
            $hasModel = $reflection->hasMethod('model');
            $hasView = $reflection->hasMethod('view');
            
            if (!$hasModel || !$hasView) {
                $methods = $reflection->getMethods();
                $methodNames = array_map(function($m) { return $m->getName(); }, $methods);
                die("Lỗi: Class Home đã được load trước đó nhưng thiếu methods.<br>
                     Methods hiện có: " . implode(', ', $methodNames) . "<br>
                     Vui lòng <a href='?controller=home&action=index&opcache_reset=1'>click vào đây để reset cache</a> hoặc restart Apache/XAMPP.");
            }
        } else {
            // Load file nếu class chưa tồn tại
            require_once $homeFile;
        }
        
        $controller = new Home($conn); // Truyền $conn vào controller
        
        if ($action == 'index') {
            $user_id = $_GET['user_id'] ?? null;
            $controller->Get_data($user_id); // Truyền user_id nếu có
        } elseif ($action == 'detail') {
            $id = $_GET['id'] ?? 0;
            $user_id = $_GET['user_id'] ?? '';
            $controller->detail_Sanpham($id, $user_id);
        }
        break;

    case 'login':
        require_once __DIR__ . '/app/controllers/Login.php';
        $controller = new Login($conn);
        
        if ($action == 'index') {
            $controller->Get_data();
        } elseif ($action == 'process') {
            $controller->processLogin();
        }
        break;

    case 'user':
        require_once __DIR__ . '/app/controllers/User.php';
        $controller = new User($conn);
        
        if ($action == 'profile') {
            $id = $_GET['id'] ?? 0;
            $loggedInId = $_GET['user_id'] ?? '';
            $controller->Profile($id, $loggedInId);
        } elseif ($action == 'update') {
            $controller->Update();
        }
        break;

    case 'auth':
         // Xử lý logout...
         if ($action == 'logout') {
             session_destroy();
             header("Location: index.php");
         }
         break;

    default:
        echo "404 - Controller not found";
        break;
}
?>