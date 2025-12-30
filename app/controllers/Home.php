<?php
// app/controllers/Home.php
require_once __DIR__ . '/../models/SanphamModel.php';
require_once __DIR__ . '/../models/DuyetSPModel.php';
// Thêm model Categories để lấy danh mục cha/con
require_once __DIR__ . '/../models/CategoriesModel.php';

class Home
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function index($user_id = null)
    {
        if (isset($_GET['logout']) && $_GET['logout'] == '1') {
            session_destroy();
            header("Location: /baitaplon/Home");
            exit();
        }
        
        $sanphamModel = new SanphamModel($this->conn);
        // Khởi tạo model Categories
        $cateModel = new CategoriesModel($this->conn);

        if ($user_id === null) {
            $user_id = isset($_GET['user_id']) ? trim($_GET['user_id']) : '';
        }
        
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

        // --- BẮT ĐẦU SỬA: Lấy cấu trúc danh mục Cha - Con ---
        $parents = $cateModel->getParentCategories();
        $categoryTree = [];
        foreach ($parents as $p) {
            // Lấy con của từng cha
            $p['children'] = $cateModel->getSubCategories($p['id_danhmuc']);
            $categoryTree[] = $p;
        }
        // --- KẾT THÚC SỬA ---

        // Tìm tên danh mục hiện tại để hiển thị lên nút
        $currentCategoryName = 'Tất cả danh mục';
        if (!empty($category)) {
            // Logic đơn giản để tìm tên danh mục đang chọn (có thể tối ưu hơn trong Model)
            foreach ($categoryTree as $cat) {
                if ($cat['id_danhmuc'] == $category) {
                    $currentCategoryName = $cat['ten_danhmuc'];
                    break;
                }
                foreach ($cat['children'] as $child) {
                    if ($child['id_danhmuc'] == $category) {
                        $currentCategoryName = $child['ten_danhmuc'];
                        break;
                    }
                }
            }
        }

        $data = [
            'products'      => $products,
            'categoryTree'  => $categoryTree, // Truyền cây danh mục sang View
            'currentCatName'=> $currentCategoryName, // Tên danh mục đang chọn
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

        require_once __DIR__ . '/../views/home.php';
    }

    // ... (Giữ nguyên các hàm khác như detail_Sanpham)
    public function detail_Sanpham($id_sanpham, $user_id = '')
    {
        $productModel = new SanphamModel($this->conn);
        $duyetSPModel = new DuyetSPModel($this->conn);
        $product = $productModel->getProductById($id_sanpham);
        $productImages = $productModel->getProductImages($id_sanpham);
        $productAttributes = $duyetSPModel->getProductAttributes($id_sanpham);
        
        $userId = isset($user_id) ? $user_id : '';

        $data = [
            'product'       => $product,
            'productImages' => $productImages,
            'productAttributes' => $productAttributes,
            'page'          => 'detail_sanpham',
            'user_id'       => $userId,
            'isLoggedIn'    => !empty($userId)
        ];
        
        require_once __DIR__ . '/../views/home.php';
    }
}
?>