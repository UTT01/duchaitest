<?php

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

    public function getSubCategories() {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $id_parent = $_GET['id_parent'] ?? null;
            if ($id_parent) {
                echo json_encode($this->categoriesModel->getSubCategories($id_parent));
            } else {
                echo json_encode([]);
            }
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

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
