<?php
// Base class cho Controller
class controller
{
    protected $conn;
    
    public function __construct($conn)
    {
        $this->conn = $conn;
    }
    
    /**
     * Load model
     */
    protected function model($modelName)
    {
        $modelFile = __DIR__ . '/../models/' . $modelName . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            $model = new $modelName($this->conn);
            return $model;
        } else {
            die("Model $modelName không tồn tại!");
        }
    }
    
    /**
     * Load view
     */
    protected function view($viewName, $data = [])
    {
        $viewFile = __DIR__ . '/../views/' . $viewName . '.php';
        if (file_exists($viewFile)) {
            // Extract data array thành các biến
            extract($data);
            require_once $viewFile;
        } else {
            die("View $viewName không tồn tại!");
        }
    }
}

// Base class cho Model
class connectDB
{
    protected $con;
    
    public function __construct($conn = null)
    {
        // Nếu không truyền $conn, sử dụng global $conn từ ConnectDB.php
        if ($conn === null) {
            global $conn;
        }
        $this->con = $conn;
    }
}
?>

