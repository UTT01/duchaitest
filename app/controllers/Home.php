<?php
// app/controllers/Home.php
require_once __DIR__ . '/../models/SanphamModel.php';

class Home
{
    private $conn;

    // 1. Nhận $conn từ index.php
    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function index($user_id = null)
    {
        // Xử lý logout nếu có
        if (isset($_GET['logout']) && $_GET['logout'] == '1') {
            session_destroy();
            header("Location: /baitaplon/Home");
            exit();
        }
        
        // 2. Khởi tạo Model trực tiếp với $this->conn
        $sanphamModel = new SanphamModel($this->conn);

        if ($user_id === null) {
            $user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';
        }
        $userId = !empty($user_id) ? $user_id : '';

        $keyword  = isset($_GET['q']) ? trim($_GET['q']) : '';
        $category = isset($_GET['danhmuc']) ? trim($_GET['danhmuc']) : '';
        $address  = isset($_GET['diachi']) ? trim($_GET['diachi']) : '';
        $page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        $limit  = 12;
        $offset = ($page - 1) * $limit;

        $totalProducts = $sanphamModel->countProducts($keyword, $category, $address, '');
        $totalPages    = ($totalProducts > 0) ? ceil($totalProducts / $limit) : 1;

        $products = $sanphamModel->getProducts($keyword, $category, $address, $offset, $limit, '');
        $categories = $sanphamModel->getAllCategories();

        $data = [
            'products'      => $products,
            'categories'    => $categories,
            'keyword'       => $keyword,
            'category'      => $category,
            'address'       => $address,
            'page'          => 'list_sanpham',
            'pageNum'       => $page,
            'totalPages'    => $totalPages,
            'totalProducts' => $totalProducts,
            'user_id'       => $user_id,
            'isLoggedIn'    => !empty($user_id)
        ];

        // 3. Gọi View trực tiếp (không qua hàm trung gian)
        // Biến $data sẽ được dùng trong view home.php
        require_once __DIR__ . '/../views/home.php';
    }

    public function detail_Sanpham($id_sanpham, $user_id = '')
    {
        $productModel = new SanphamModel($this->conn);
        $product = $productModel->getProductById($id_sanpham);
        $productImages = $productModel->getProductImages($id_sanpham);
        
        $userId = isset($user_id) ? $user_id : '';

        $data = [
            'product'       => $product,
            'productImages' => $productImages,
            'page'          => 'detail_sanpham',
            'user_id'       => $userId,
            'isLoggedIn'    => !empty($userId)
        ];
        
        require_once __DIR__ . '/../views/home.php';
    }
}
?>