<?php
// $currentView: tên file page (home, ...) được controller truyền sang
// $data: mảng dữ liệu dùng trong các page
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Home - Cho Tot Clone</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <link rel="stylesheet" href="/baitaplon/Public/css/home_css.css?v=<?php echo time(); ?>">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light shadow-sm">
    <div class="container">
        <?php
        // 1. Lấy ID người dùng hiện tại
        $currentUserId = isset($data['user_id']) ? $data['user_id'] : '';
        
        // 2. Tạo Link Logo (Về trang chủ)
        // Nếu có ID -> Về /Home/index/ID, ngược lại về /Home
        $homeLink = "/baitaplon/Home";
        if (!empty($currentUserId)) {
            $homeLink .= "/index/" . urlencode($currentUserId);
        }
        ?>

        <a class="navbar-brand" href="<?php echo $homeLink; ?>">
            Rì Cũng Bán
        </a>

        <form class="d-flex flex-grow-1 mx-3 search-bar align-items-center gap-2" method="GET" action="/baitaplon/Home/index" id="searchForm">
            
            <?php if(!empty($currentUserId)): ?>
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($currentUserId); ?>">
            <?php endif; ?>

            <?php
            $keyword  = isset($data['keyword']) ? $data['keyword'] : '';
            $category = isset($data['category']) ? $data['category'] : '';
            // Sử dụng cây danh mục mới
            $categoryTree = isset($data['categoryTree']) ? $data['categoryTree'] : [];
            $currentCatName = isset($data['currentCatName']) ? $data['currentCatName'] : 'Danh mục';
            $address  = isset($data['address']) ? $data['address'] : '';
            ?>
            
            <input type="hidden" name="danhmuc" id="inputDanhmuc" value="<?php echo htmlspecialchars($category); ?>">

            <div class="dropdown">
                <button class="btn dropdown-toggle custom-dropdown-btn" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="min-width: 160px;">
                    <span class="text-truncate" style="max-width: 140px;"><?php echo htmlspecialchars($currentCatName); ?></span>
                </button>
                
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="#" onclick="selectCategory('', 'Tất cả danh mục'); return false;">Tất cả danh mục</a></li>
                    
                    <li><hr class="dropdown-divider"></li>

                    <?php foreach ($categoryTree as $parent): ?>
                        <?php if (!empty($parent['children'])): ?>
                            <li class="dropdown-item-parent">
                                <a class="dropdown-item" href="#" onclick="selectCategory('<?php echo $parent['id_danhmuc']; ?>', '<?php echo $parent['ten_danhmuc']; ?>'); return false;">
                                    <?php echo htmlspecialchars($parent['ten_danhmuc']); ?>
                                </a>
                                <ul class="dropdown-menu submenu">
                                    <?php foreach ($parent['children'] as $child): ?>
                                        <li>
                                            <a class="dropdown-item" href="#" onclick="selectCategory('<?php echo $child['id_danhmuc']; ?>', '<?php echo $child['ten_danhmuc']; ?>'); return false;">
                                                <?php echo htmlspecialchars($child['ten_danhmuc']); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php else: ?>
                            <li>
                                <a class="dropdown-item" href="#" onclick="selectCategory('<?php echo $parent['id_danhmuc']; ?>', '<?php echo $parent['ten_danhmuc']; ?>'); return false;">
                                    <?php echo htmlspecialchars($parent['ten_danhmuc']); ?>
                                </a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <input class="form-control" type="text" name="q" placeholder="Tìm kiếm..." value="<?php echo htmlspecialchars($keyword); ?>">
            <div class="dropdown" id="nav-location-dropdown">
                <input type="hidden" name="diachi" id="nav-input-diachi" value="<?php echo htmlspecialchars($address); ?>">
                
                <button class="btn btn-outline-secondary dropdown-toggle text-truncate bg-white" type="button" 
                        id="navLocationBtn" data-bs-toggle="dropdown" aria-expanded="false" 
                        style="min-width: 150px; max-width: 200px; height: 100%;">
                    <?php echo !empty($address) ? htmlspecialchars($address) : 'Toàn quốc'; ?>
                </button>
                
                <div class="dropdown-menu p-2 shadow mt-1" aria-labelledby="navLocationBtn" style="width: 300px;">
                    <div class="input-group mb-2">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                        <input type="text" class="form-control border-start-0" id="nav-search-box" placeholder="Nhập Huyện hoặc Tỉnh..." autocomplete="off">
                    </div>
                    
                    <div class="list-group list-group-flush" id="nav-location-list" style="max-height: 300px; overflow-y: auto;">
                    </div>
                </div>
</div>
            <button class="btn btn-warning btn-search" type="submit">Tìm kiếm</button>
        </form>

        <div class="d-flex align-items-center gap-3">
            <?php if (isset($data['isLoggedIn']) && $data['isLoggedIn']): ?>
                <button type="button" class="btn btn-warning fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#postModal">
                    <i class="bi bi-pencil-square"></i> Đăng tin
                </button>

                <a href="/baitaplon/Chat/index/0/<?php echo $currentUserId; ?>" class="text-secondary position-relative text-decoration-none" title="Tin nhắn">
                    <i class="bi bi-chat-dots-fill" style="font-size: 1.5rem;"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                </a>

                <a href="/baitaplon/User/Profile/<?php echo urlencode($currentUserId); ?>" class="text-secondary text-decoration-none" title="Trang cá nhân">
                    <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
                </a>

                <a href="/baitaplon/Home?logout=1" class="text-muted small text-decoration-none">Đăng xuất</a>

            <?php else: ?>
                <a href="/baitaplon/Login" class="btn btn-outline-secondary me-2">Đăng nhập</a>
                <a href="/baitaplon/Login" class="btn btn-warning">Đăng ký</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container my-4">
    <?php
        if (isset($data["page"]) && $data["page"]) {
            $pageFile = __DIR__ . "/Page/" . $data["page"] . ".php";
            
            if (file_exists($pageFile)) {
                require_once $pageFile;
            } else {
                echo '<div class="alert alert-danger">Không tìm thấy page: ' . htmlspecialchars($data["page"]) . ' (Path: ' . $pageFile . ')</div>';
            }
        } else {
            echo '<div class="alert alert-warning">Không có trang nào được chọn!</div>';
        }
    ?>
</div>

<div class="modal fade" id="postModal" tabindex="-1" aria-labelledby="postModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="postModalLabel">Đăng tin rao vặt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="postForm" enctype="multipart/form-data">
                    <input type="hidden" name="id_user_posted" value="<?php echo isset($currentUserId) ? $currentUserId : ''; ?>">

                    <div class="form-group mb-3">
                        <label>Hình ảnh sản phẩm <span style="color:red">*</span></label>
                        <div class="upload-grid" id="uploadGrid">
                            <div class="upload-box-btn" id="addImgBtn">
                                <span>+</span>
                                <p>Đăng ảnh</p>
                                <input type="file" id="imageInput" multiple accept="image/*">
                            </div>
                        </div>
                        <div class="error-msg text-danger" id="err-images">Vui lòng chọn ít nhất 1 ảnh.</div>
                    </div>

                    <div class="form-group mb-3">
                        <label>Danh mục chính <span style="color:red">*</span></label>
                        <select id="catLevel1" name="catLevel1" class="form-control">
                            <option value="">-- Chọn danh mục --</option>
                        </select>
                        <div class="error-msg text-danger" id="err-catLevel1">Vui lòng chọn danh mục.</div>
                    </div>

                    <div class="form-group mb-3" id="group-catLevel2" style="display:none;">
                        <label>Loại sản phẩm <span style="color:red">*</span></label>
                        <select id="catLevel2" name="catLevel2" class="form-control">
                            <option value="">-- Chọn loại sản phẩm --</option>
                        </select>
                        <div class="error-msg text-danger" id="err-catLevel2">Vui lòng chọn loại sản phẩm.</div>
                    </div>

                    <div class="form-group mb-3">
                        <label>Tiêu đề tin <span style="color:red">*</span></label>
                        <input type="text" id="title" name="title" class="form-control" placeholder="VD: Bán xe Vios cũ...">
                        <div class="error-msg text-danger" id="err-title">Nhập tiêu đề.</div>
                    </div>

                    <div class="form-group mb-3">
                        <label>Giá bán (VNĐ) <span style="color:red">*</span></label>
                        <input type="number" id="price" name="price" class="form-control" placeholder="Nhập giá">
                        <div class="error-msg text-danger" id="err-price">Nhập giá.</div>
                    </div>

                    <div id="dynamic-attributes"></div>

                    <div class="form-group mb-3">
                        <label>Mô tả chi tiết <span style="color:red">*</span></label>
                        <textarea id="description" name="description" class="form-control" rows="4" placeholder="Mô tả chi tiết..."></textarea>
                        <div class="error-msg text-danger" id="err-description">Mô tả quá ngắn (tối thiểu 10 từ).</div>
                    </div>

                   <div class="form-group mb-3">
                        <label class="form-label fw-bold">Địa chỉ giao dịch <span class="text-danger">*</span></label>
                        
                        <div class="dropdown" id="address-picker-container">
                            <button class="btn border w-100 text-start d-flex justify-content-between align-items-center bg-white" 
                                    type="button" 
                                    id="addressDropdownBtn" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-auto-close="outside"
                                    aria-expanded="false"
                                    style="height: 45px;">
                                <span id="address-label" class="text-muted">Chọn Tỉnh/Thành, Quận/Huyện...</span>
                                <i class="bi bi-chevron-down"></i>
                            </button>

                            <div class="dropdown-menu w-100 p-2 shadow mt-1" aria-labelledby="addressDropdownBtn">
                                
                                <div class="mb-2">
                                    <div id="district-header" class="d-none mb-2 pb-2 border-bottom">
                                        <a href="#" class="text-decoration-none text-primary small fw-bold" id="btn-back-province">
                                            <i class="bi bi-arrow-left"></i> Chọn lại Tỉnh
                                        </a>
                                        <div class="mt-1 fw-bold text-dark" id="selected-province-display"></div>
                                    </div>

                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                                        <input type="text" class="form-control border-start-0" id="address-search-box" placeholder="Tìm Tỉnh/Thành...">
                                    </div>
                                </div>

                                <div id="address-list-area" class="list-group list-group-flush" style="max-height: 250px; overflow-y: auto;">
                                    </div>
                            </div>
                        </div>

                        <input type="hidden" name="address" id="address-hidden-input">
                        <div class="error-msg text-danger mt-1 small" id="err-address" style="display:none;">Vui lòng chọn địa chỉ.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" form="postForm" class="btn btn-warning">Đăng tin ngay</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="/baitaplon/Public/js/home_js.js?v=<?php echo time(); ?>"></script>

</body>
</html>