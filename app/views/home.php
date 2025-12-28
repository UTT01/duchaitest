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

        /* Styles for post modal form */
        .upload-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 10px;
        }

        .upload-box-btn {
            width: 100px;
            height: 100px;
            border: 2px dashed #ddd;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }

        .upload-box-btn:hover {
            border-color: #f59e0b;
            background: #fef3c7;
        }

        .upload-box-btn span {
            font-size: 24px;
            color: #f59e0b;
            font-weight: bold;
        }

        .upload-box-btn p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
        }

        .upload-box-btn input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .upload-box-btn {
            position: relative;
        }

        .image-item {
            position: relative;
            display: inline-block;
        }

        .image-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .btn-remove-img {
            position: absolute;
            top: -5px;
            right: -5px;
            background: red;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
        }

        .error-msg {
            display: none;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
        }

        .input-error {
            border-color: #dc3545 !important;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }
    </style>
</head>
<body>

<!-- Thanh navbar trên cùng -->
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
            Chợ Tốt Clone
        </a>

        <form class="d-flex flex-grow-1 mx-3 search-bar align-items-center gap-2" method="GET" action="/baitaplon/Home/index">
            
            <?php if(!empty($currentUserId)): ?>
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($currentUserId); ?>">
            <?php endif; ?>

            <?php
            $keyword  = isset($data['keyword']) ? $data['keyword'] : '';
            $category = isset($data['category']) ? $data['category'] : '';
            $categories = isset($data['categories']) ? $data['categories'] : [];
            $address  = isset($data['address']) ? $data['address'] : '';
            ?>
            
            <select class="form-select" name="danhmuc" onchange="this.form.submit()">
                <option value="">Tất cả danh mục</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['id_danhmuc']); ?>"
                        <?php echo ($category === $cat['id_danhmuc']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['ten_danhmuc']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input class="form-control" type="text" name="q" placeholder="Tìm kiếm..." value="<?php echo htmlspecialchars($keyword); ?>">
            <input class="form-control" type="text" name="diachi" placeholder="Khu vực..." value="<?php echo htmlspecialchars($address); ?>">
            <button class="btn btn-warning btn-search" type="submit">Tìm kiếm</button>
        </form>

        <div class="d-flex align-items-center gap-3">
            <?php if (isset($data['isLoggedIn']) && $data['isLoggedIn']): ?>
                <button type="button" class="btn btn-warning fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#postModal">
                    <i class="bi bi-pencil-square"></i> Đăng tin
                </button>

                <a href="<?php echo $homeLink; ?>" class="text-secondary position-relative text-decoration-none">
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

<!-- Nội dung chính -->
<div class="container my-4">
    <?php
        if (isset($data["page"]) && $data["page"]) {
            // Sửa thành đường dẫn tương đối theo thư mục hiện tại (__DIR__)
            // __DIR__ là app/views, nối thêm /Page/ là đúng
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

<!-- Modal Đăng tin -->
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
                        <label>Địa chỉ giao dịch <span style="color:red">*</span></label>
                        <input type="text" id="address" name="address" class="form-control" placeholder="Địa chỉ cụ thể...">
                        <div class="error-msg text-danger" id="err-address">Nhập địa chỉ.</div>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Auto open post modal if URL has open_post_modal=1
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('open_post_modal') === '1') {
        const modal = new bootstrap.Modal(document.getElementById('postModal'));
        modal.show();
        // Clean URL
        const url = new URL(window.location);
        url.searchParams.delete('open_post_modal');
        window.history.replaceState({}, '', url);
    }
});
</script>

<script>
// Form handling JavaScript
document.addEventListener("DOMContentLoaded", function() {
    const cat1 = document.getElementById('catLevel1');
    const cat2 = document.getElementById('catLevel2');
    const groupCat2 = document.getElementById('group-catLevel2');
    const dynamicDiv = document.getElementById('dynamic-attributes');
    const form = document.getElementById('postForm');
    const imageInput = document.getElementById('imageInput');
    const uploadGrid = document.getElementById('uploadGrid');
    const addImgBtn = document.getElementById('addImgBtn');
    let selectedFiles = [];

    if (!cat1 || !form) { console.error("LỖI HTML"); return; }

    // Fetch API cần sửa đường dẫn nếu đang ở URL cấp 2 (/index/id)
    // Cách tốt nhất là dùng đường dẫn gốc /baitaplon/...
    const apiUrl = '/baitaplon/CategoriesController'; 

// Lấy danh mục cha
    fetch(apiUrl + '/getParentCategories')
            .then(res => res.json())
            .then(data => {
            if(data.error) return;
            data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id_danhmuc;
                opt.textContent = item.ten_danhmuc;
                cat1.appendChild(opt);
            });
        });

    cat1.addEventListener('change', function() {
        const idParent = this.value;
        cat2.innerHTML = '<option value="">-- Chọn loại sản phẩm --</option>';
        groupCat2.style.display = 'none';
        dynamicDiv.innerHTML = '';
        if (!idParent) return;
        fetch(apiUrl + `/getSubCategories/${idParent}`)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    groupCat2.style.display = 'block';
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id_danhmuc;
                        opt.textContent = item.ten_danhmuc;
                        cat2.appendChild(opt);
                    });
                }
            });
    });

    cat2.addEventListener('change', function() {
        const idDanhmuc = this.value;
        dynamicDiv.innerHTML = '';
        if (!idDanhmuc) return;
        fetch(apiUrl + `/getAttributes?id_danhmuc=${idDanhmuc}`)
            .then(res => res.json())
            .then(attributes => {
                attributes.forEach(attr => {
                    const div = document.createElement('div');
                    div.className = 'form-group mb-3';
                    const label = document.createElement('label');
                    label.textContent = attr.ten_thuoctinh;
                    div.appendChild(label);
                    let inputEl;
                    if (attr.options && attr.options.length > 0) {
                        inputEl = document.createElement('select');
                        inputEl.className = 'form-control';
                        inputEl.innerHTML = `<option value="">-- Chọn ${attr.ten_thuoctinh} --</option>`;
                        attr.options.forEach(opt => {
                            const option = document.createElement('option');
                            option.value = opt.id_option;
                            option.textContent = opt.gia_tri_option;
                            inputEl.appendChild(option);
                        });
                    } else {
                        inputEl = document.createElement('input');
                        inputEl.className = 'form-control';
                        inputEl.type = 'text';
                        inputEl.placeholder = `Nhập ${attr.ten_thuoctinh}...`;
                    }
                    inputEl.className += ' dynamic-field';
                    inputEl.name = `thuoctinh[${attr.id_thuoctinh}]`;
                    const errMsg = document.createElement('div');
                    errMsg.className = 'error-msg text-danger';
                    errMsg.textContent = `Vui lòng nhập/chọn ${attr.ten_thuoctinh}`;
                    div.appendChild(inputEl);
                    div.appendChild(errMsg);
                    dynamicDiv.appendChild(div);
                });
            });
    });

    imageInput.addEventListener('change', function() {
        const files = Array.from(this.files);
        files.forEach(file => {
            selectedFiles.push(file);
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'image-item';
                div.innerHTML = `
                    <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">
                    <div class="btn-remove-img" onclick="removeImage(this, '${file.name}')" style="position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px;">&times;</div>
                `;
                div.style.position = 'relative';
                div.style.display = 'inline-block';
                div.style.margin = '5px';
                uploadGrid.insertBefore(div, addImgBtn);
            }
            reader.readAsDataURL(file);
        });
        this.value = '';
    });

    window.removeImage = function(btn, fileName) {
        selectedFiles = selectedFiles.filter(f => f.name !== fileName);
        btn.parentElement.remove();
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // ... (Giữ nguyên phần validate form) ...
        let isValid = true;
        const toggleError = (id, condition) => {
            const el = document.getElementById(id);
            const err = document.getElementById('err-' + id);
            if(el && err) {
                if(condition) { el.classList.add('is-invalid'); err.style.display = 'block'; isValid = false; }
                else { el.classList.remove('is-invalid'); err.style.display = 'none'; }
            }
        };
        toggleError('catLevel1', !cat1.value);
        if(groupCat2.style.display !== 'none') toggleError('catLevel2', !cat2.value);
        toggleError('title', !document.getElementById('title').value.trim());
        toggleError('price', !document.getElementById('price').value.trim());
        toggleError('address', !document.getElementById('address').value.trim());
        const desc = document.getElementById('description');
        if(desc) toggleError('description', desc.value.trim().split(/\s+/).filter(w=>w).length < 10);

        const errImg = document.getElementById('err-images');
        if(selectedFiles.length === 0) { errImg.style.display = 'block'; isValid = false; } else { errImg.style.display = 'none'; }
        // ... (Kết thúc phần validate) ...

        if(isValid) {
            const formData = new FormData(form);
            selectedFiles.forEach((file) => { formData.append('images[]', file); });

            fetch('/baitaplon/PostController/add', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    // Close modal and reload page
                    const modal = bootstrap.Modal.getInstance(document.getElementById('postModal'));
                    modal.hide();
                    window.location.reload();
                } else {
                    alert('Lỗi: ' + data.error);
                }
            })
            .catch(err => alert('Lỗi submit: ' + err.message));
        }
    });

    // Reset form when modal is closed
    document.getElementById('postModal').addEventListener('hidden.bs.modal', function () {
        form.reset();
        document.getElementById('uploadGrid').innerHTML = `
            <div class="upload-box-btn" id="addImgBtn">
                <span>+</span>
                <p>Đăng ảnh</p>
                <input type="file" id="imageInput" multiple accept="image/*">
            </div>
        `;
        selectedFiles = [];
        dynamicDiv.innerHTML = '';
        groupCat2.style.display = 'none';
        // Hide all error messages
        document.querySelectorAll('.error-msg').forEach(el => el.style.display = 'none');
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    });
});
</script>

</body>
</html>


