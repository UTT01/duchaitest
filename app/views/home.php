<?php
// $currentView: tên file page (home, ...) được controller truyền sang
// $data: mảng dữ liệu dùng trong các page
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Trang chủ - Chợ Tốt Clone</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Tổng thể giao diện: tông màu dịu, nhẹ mắt */
        body {
            background: #f1f5f9;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #0f172a;
        }

        /* Navbar */
        .navbar {
            background: #ffffffcc;
            backdrop-filter: blur(10px);
            border-bottom: 1px solid #e2e8f0;
        }

        .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.02em;
            color: #f59e0b !important;
        }

        /* Form tìm kiếm trên navbar */
        .search-bar {
            background: #f8fafc;
            border-radius: 999px;
            padding: 6px 10px;
            border: 1px solid #e2e8f0;
        }

        .search-bar .form-control,
        .search-bar .form-select {
            border: none;
            background: transparent;
            box-shadow: none !important;
        }

        .search-bar .form-control::placeholder {
            color: #94a3b8;
        }

        .search-bar .btn-search {
            border-radius: 999px;
            padding-inline: 20px;
            font-weight: 600;
        }

        /* Nút màu vàng nhưng giảm độ chói */
        .btn-warning,
        .btn-warning:hover,
        .btn-warning:focus {
            background-color: #facc15;
            border-color: #eab308;
            color: #1f2933;
        }

        /* Thẻ sản phẩm */
        .product-card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            overflow: hidden;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
            background: #ffffff;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 30px rgba(15, 23, 42, 0.08);
            border-color: #cbd5f5;
        }

        .product-card img {
            border-bottom: 1px solid #e2e8f0;
        }

        .badge-category {
            background-color: #f97316;
            color: #ffffff;
            border-radius: 999px;
            font-weight: 500;
            font-size: 0.75rem;
            padding-inline: 10px;
        }

        /* Tiêu đề khối nội dung chính */
        .page-header-title {
            font-size: 1.2rem;
            font-weight: 600;
        }

        .page-header-title small {
            font-weight: 400;
        }

        /* Nút phân trang */
        .pagination .page-link {
            border-radius: 999px !important;
            margin-inline: 2px;
        }

        .pagination .page-item.active .page-link {
            background-color: #0f172a;
            border-color: #0f172a;
        }

        /* Bo góc & nền nhẹ cho container chính */
        .main-container {
            background: #ffffff;
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }

        @media (max-width: 768px) {
            .search-bar {
                border-radius: 16px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>

<!-- Thanh navbar trên cùng -->
<nav class="navbar navbar-expand-lg navbar-light shadow-sm">
    <div class="container">
        <?php
        // 1. Xử lý logic đường dẫn chung cho cả Logo và Form tìm kiếm
        $currentUserId = isset($data['user_id']) ? $data['user_id'] : '';
        
        // Đường dẫn gốc
        $baseUrl = "http://localhost/baitaplon/Home";
        
        // Nếu đã đăng nhập (có user_id), nối thêm vào URL để giữ trạng thái đăng nhập
        $actionUrl = $baseUrl;
        if (!empty($currentUserId)) {
            $actionUrl .= "/Get_data/" . $currentUserId;
        }
        ?>

        <a class="navbar-brand" href="<?php echo htmlspecialchars($actionUrl); ?>">
            Chợ Tốt Clone
        </a>

        <form class="d-flex flex-grow-1 mx-3 search-bar align-items-center gap-2" method="GET" action="<?php echo htmlspecialchars($actionUrl); ?>">
            <?php
            $keyword  = isset($data['keyword']) ? $data['keyword'] : '';
            $category = isset($data['category']) ? $data['category'] : '';
            $categories = isset($data['categories']) ? $data['categories'] : [];
            $address  = isset($data['address']) ? $data['address'] : '';
            ?>
            
            <select class="form-select" name="danhmuc" onchange="this.form.submit()">
                <option value="">Tất cả danh mục</option>
                <?php foreach ($categories as $cat): ?>
                    <option
                        value="<?php echo htmlspecialchars($cat['id_danhmuc']); ?>"
                        <?php echo ($category === $cat['id_danhmuc']) ? 'selected' : ''; ?>
                    >
                        <?php echo htmlspecialchars($cat['ten_danhmuc']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input
                class="form-control"
                type="text"
                name="q"
                placeholder="Tìm kiếm sản phẩm, từ khóa..."
                value="<?php echo htmlspecialchars($keyword); ?>"
            >
            <input
                class="form-control"
                type="text"
                name="diachi"
                placeholder="Tìm theo khu vực / địa chỉ"
                value="<?php echo htmlspecialchars($address); ?>"
            >
            <button class="btn btn-warning btn-search" type="submit">Tìm kiếm</button>
        </form>

        <div class="d-flex align-items-center gap-3">
            <?php if (isset($data['isLoggedIn']) && $data['isLoggedIn']): ?>
                <a href="?url=Sanpham/DangTin" class="btn btn-warning fw-bold text-dark">
                    <i class="bi bi-pencil-square"></i> Đăng tin
                </a>

                <a href="?url=Chat" class="text-secondary position-relative text-decoration-none" title="Tin nhắn">
                    <i class="bi bi-chat-dots-fill" style="font-size: 1.5rem;"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                </a>

                <a href="?url=User/Profile/<?php echo htmlspecialchars($currentUserId); ?>" class="text-secondary text-decoration-none" title="Tài khoản cá nhân">
                    <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
                </a>

                <a href="?url=Auth/Logout" class="text-muted small text-decoration-none" style="font-size: 0.8rem;">
                    Đăng xuất
                </a>

            <?php else: ?>
                <a href="/baitaplon/Login/Get_data/" class="btn btn-outline-secondary me-2">Đăng nhập</a>
                <a href="/baitaplon/Auth/Register" class="btn btn-warning">Đăng ký</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Nội dung chính -->
<div class="container my-4">
    <?php
    if (isset($data["page"]) && $data["page"]) {
        if (file_exists("./MVC/Views/Page/".$data["page"].".php")) {
            require_once "./MVC/Views/Page/".$data["page"].".php";
        } else {
            echo '<div class="alert alert-danger">Không tìm thấy page: ' . htmlspecialchars($data["page"]) . '</div>';
        }
    } else {
        echo '<div class="alert alert-warning">Không có trang nào được chọn!</div>';
    }
    ?>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>


