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

    public function index($id_user = null)
    {
        // Xử lý logout nếu có
        if (isset($_GET['logout']) && $_GET['logout'] == '1') {
            session_destroy();
            header("Location: /LapTrinhWeb/baitaplon/Home");
            exit();
        }
        
        // 2. Khởi tạo Model trực tiếp với $this->conn
        $sanphamModel = new SanphamModel($this->conn);

        if ($id_user === null) {
            $id_user = isset($_GET['id_user']) ? trim($_GET['id_user']) : '';
        }
        $userId = !empty($id_user) ? $id_user : '';

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
            'id_user'       => $id_user,
            'isLoggedIn'    => !empty($id_user)
        ];

        // 3. Gọi View trực tiếp (không qua hàm trung gian)
        // Biến $data sẽ được dùng trong view home.php
        require_once __DIR__ . '/../views/home.php';
    }

    public function detail_Sanpham($id_sanpham, $id_user = '')
    {
        $productModel = new SanphamModel($this->conn);
        $product = $productModel->getProductById($id_sanpham);
        $productImages = $productModel->getProductImages($id_sanpham);
        
        $userId = isset($id_user) ? $id_user : '';

        $data = [
            'product'       => $product,
            'productImages' => $productImages,
            'page'          => 'detail_sanpham',
            'id_user'       => $userId,
            'isLoggedIn'    => !empty($userId)
        ];
        
        require_once __DIR__ . '/../views/home.php';
    }
}
?>