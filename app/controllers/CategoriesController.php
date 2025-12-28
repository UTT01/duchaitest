<?php
// 1. Load Model thủ công vì chưa có autoload
require_once __DIR__ . '/../models/CategoriesModel.php';

class CategoriesController {
    private $categoriesModel;

    public function __construct($conn) {
        $this->categoriesModel = new CategoriesModel($conn);
    }

    public function getParentCategories() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            echo json_encode($this->categoriesModel->getParentCategories());
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // 2. SỬA: Thêm tham số $id_parent vào hàm để nhận từ URL (do index.php truyền vào)
    // Ví dụ URL: /Categories/getSubCategories/dienthoai -> $id_parent = 'dienthoai'
    public function getSubCategories($id_parent = null) {
        header('Content-Type: application/json; charset=utf-8');
        try {
            // Ưu tiên lấy từ tham số truyền vào (URL path), nếu không có thì thử lấy từ $_GET (query string)
            $id = $id_parent ?? ($_GET['id_parent'] ?? null);

            if ($id) {
                echo json_encode($this->categoriesModel->getSubCategories($id));
            } else {
                echo json_encode([]);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    // Hàm này giữ nguyên vì bên JS gọi kiểu ?id_danhmuc=... (Query String) nên $_GET vẫn bắt được
    public function getAttributes() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $id_danhmuc = $_GET['id_danhmuc'] ?? null;
            if ($id_danhmuc) {
                echo json_encode($this->categoriesModel->getAttributesByCategory($id_danhmuc));
            } else {
                echo json_encode([]);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
?>