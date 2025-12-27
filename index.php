<?php
session_start();

// 1. Kết nối CSDL (luôn cần thiết)
require_once __DIR__ . '/app/config/ConnectDB.php';

// 2. Lấy URL từ .htaccess truyền vào
// Mặc định nếu không có url thì về Home/index
$url = isset($_GET['url']) ? $_GET['url'] : 'Home/index';

// 3. Xử lý chuỗi URL: Xóa khoảng trắng, lọc ký tự lạ, cắt thành mảng
$url = rtrim($url, '/');
$urlArr = explode('/', filter_var($url, FILTER_SANITIZE_URL));

// --- BƯỚC A: XÁC ĐỊNH CONTROLLER ---
$controllerName = 'Home'; // Mặc định là Home
if (!empty($urlArr[0])) {
    // Viết hoa chữ cái đầu để khớp tên file (ví dụ: user -> User)
    $controllerName = ucfirst($urlArr[0]);
}

// Kiểm tra file controller có tồn tại không
$controllerFile = __DIR__ . '/app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Khởi tạo Controller và truyền $conn vào
    if (class_exists($controllerName)) {
        $controller = new $controllerName($conn);
    } else {
        die("Lỗi: Không tìm thấy class '$controllerName' trong file.");
    }
} else {
    // Xử lý khi gõ sai tên Controller (Ví dụ: /Abcxyz)
    die("Lỗi 404: Không tìm thấy trang (Controller '$controllerName' not found).");
}

// --- BƯỚC B: XÁC ĐỊNH ACTION (HÀM) ---
$actionName = 'index'; // Mặc định tên hàm là index
if (!empty($urlArr[1])) {
    $actionName = $urlArr[1];
}

// --- BƯỚC C: XÁC ĐỊNH THAM SỐ (PARAMS) ---
// Lấy tất cả phần còn lại của URL làm tham số (từ vị trí thứ 2 trở đi)
$params = array_slice($urlArr, 2);

// --- BƯỚC D: GỌI HÀM ---
if (method_exists($controller, $actionName)) {
    // Gọi hàm $actionName trong $controller và truyền mảng $params vào
    call_user_func_array([$controller, $actionName], $params);
} else {
    // [Hỗ trợ code cũ]: Nếu gọi hàm 'index' mà không thấy, 
    // thử tìm hàm 'Get_data' (vì các controller cũ của bạn dùng Get_data)
    if ($actionName == 'index' && method_exists($controller, 'Get_data')) {
        call_user_func_array([$controller, 'Get_data'], $params);
    } else {
        die("Lỗi: Chức năng '$actionName' không tồn tại trong '$controllerName'.");
    }
}
?>