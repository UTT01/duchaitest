<?php

class AdminController {
    private $duyetSPModel;

    public function __construct($conn) {
        $this->duyetSPModel = new DuyetSPModel($conn);
    }

    // Hiển thị danh sách sản phẩm chờ duyệt
    public function index() {
        try {
            $products = $this->duyetSPModel->getPendingProducts();
            require_once __DIR__ . '/../views/Admin_DuyetSanPham.php';
        } catch (Exception $e) {
            echo "Lỗi: " . $e->getMessage();
        }
    }

    // API: Lấy danh sách sản phẩm chờ duyệt (JSON)
    public function getPendingProducts() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $products = $this->duyetSPModel->getPendingProducts();
            echo json_encode([
                'success' => true,
                'data' => $products
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    // API: Lấy chi tiết sản phẩm
    public function getProductDetail() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!isset($_GET['id_sanpham'])) {
                throw new Exception("Thiếu ID sản phẩm");
            }

            $id_sanpham = intval($_GET['id_sanpham']);
            $product = $this->duyetSPModel->getProductDetail($id_sanpham);
            $images = $this->duyetSPModel->getProductImages($id_sanpham);
            $attributes = $this->duyetSPModel->getProductAttributes($id_sanpham);

            if (!$product) {
                throw new Exception("Sản phẩm không tồn tại");
            }

            echo json_encode([
                'success' => true,
                'product' => $product,
                'images' => $images,
                'attributes' => $attributes
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    // Duyệt sản phẩm
    public function approve() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!isset($_POST['id_sanpham'])) {
                throw new Exception("Thiếu ID sản phẩm");
            }

            $id_sanpham = intval($_POST['id_sanpham']);
            $this->duyetSPModel->approveProduct($id_sanpham);

            echo json_encode([
                'success' => true,
                'message' => 'Duyệt sản phẩm thành công!'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    // Duyệt tất cả sản phẩm
    public function approveAll() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $this->duyetSPModel->approveAllProducts();

            echo json_encode([
                'success' => true,
                'message' => 'Duyệt tất cả sản phẩm thành công!'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    // Từ chối sản phẩm
    public function reject() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            if (!isset($_POST['id_sanpham'])) {
                throw new Exception("Thiếu ID sản phẩm");
            }

            $id_sanpham = intval($_POST['id_sanpham']);
            $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
            
            $this->duyetSPModel->rejectProduct($id_sanpham, $reason);

            echo json_encode([
                'success' => true,
                'message' => 'Từ chối sản phẩm thành công!'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
?>
