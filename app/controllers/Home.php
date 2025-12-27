<?php
require_once __DIR__ . '/../models/SanphamModel.php';
class Home
{
    public function Get_data($user_id = null)
    {
        $sanphamModel = $this->model('SanphamModel');

        // Lấy user_id từ parameter (từ URL routing) hoặc từ $_GET (fallback)
        if ($user_id === null) {
            $user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';
        }
        $userId = !empty($user_id) ? $user_id : '';

        // Lấy tham số lọc/tìm kiếm từ URL
        $keyword  = isset($_GET['q']) ? trim($_GET['q']) : '';
        $category = isset($_GET['danhmuc']) ? trim($_GET['danhmuc']) : '';
        $address  = isset($_GET['diachi']) ? trim($_GET['diachi']) : '';
        $page     = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($page < 1) $page = 1;

        // Debug: Uncomment to see what values are being received
        // error_log("Search params - keyword: '$keyword', category: '$category', address: '$address'");

        $limit  = 12; // số sản phẩm / 1 trang
        $offset = ($page - 1) * $limit;

        // Đếm tổng sản phẩm để phân trang
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
            'page'          => 'list_sanpham', // Tên page để layout.php include
            'pageNum'       => $page, // Số trang phân trang
            'totalPages'    => $totalPages,
            'totalProducts' => $totalProducts,
            'user_id'       => $user_id,
            'isLoggedIn'    => !empty($user_id)
        ];

        $this->view('home', $data);
    }
    public function detail_Sanpham($id_sanpham,$user_id = ''){
        $productModel = $this->model('SanphamModel');
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
        
        $this->view('home', $data);
    }
}
?>

