<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Duyệt Sản Phẩm</title>
    <!-- <link rel="stylesheet" href="public/css/style.css"> -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            padding: 20px;
        }

        .navbar {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }

        .navbar-link {
            padding: 10px 15px;
            background: #3498db;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.2s;
        }

        .navbar-link:hover {
            background: #2980b9;
        }

        .admin-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .header h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .stats {
            display: flex;
            gap: 20px;
            font-size: 14px;
            color: #666;
        }

        .stat-item {
            display: flex;
            gap: 5px;
        }

        .stat-number {
            font-weight: bold;
            color: #e74c3c;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 16px;
            margin-bottom: 30px;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .product-image {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #f0f0f0;
        }

        .product-info {
            padding: 12px;
        }

        .product-name {
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 8px;
            color: #333;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 32px;
        }

        .product-price {
            color: #e74c3c;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .product-category {
            color: #999;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .product-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .btn-small {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            transition: background 0.2s;
        }

        .btn-detail {
            background: #3498db;
            color: white;
        }

        .btn-detail:hover {
            background: #2980b9;
        }

        .btn-approve {
            background: #27ae60;
            color: white;
        }

        .btn-approve:hover {
            background: #229954;
        }

        .btn-reject {
            background: #e74c3c;
            color: white;
        }

        .btn-reject:hover {
            background: #c0392b;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 30px;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .pagination button, .pagination a {
            padding: 10px 12px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: bold;
            color: #333;
        }

        .pagination button:hover, .pagination a:hover {
            background: #f0f0f0;
            border-color: #999;
        }

        .pagination .active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 8px;
            color: #999;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            overflow-y: auto;
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #999;
            background: none;
            border: none;
        }

        .modal-close:hover {
            color: #333;
        }

        .detail-header {
            margin-bottom: 20px;
        }

        .detail-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .detail-price {
            color: #e74c3c;
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 10px;
            margin: 15px 0;
        }

        .gallery img {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #ddd;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .gallery img:hover {
            transform: scale(1.05);
            border-color: #3498db;
        }

        .detail-section {
            margin: 15px 0;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .detail-section:last-child {
            border-bottom: none;
        }

        .section-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }

        .section-value {
            color: #666;
            line-height: 1.6;
        }

        .attributes-list {
            background: #f9f9f9;
            padding: 12px;
            border-radius: 4px;
            margin-top: 8px;
        }

        .attribute-item {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        .attribute-item:last-child {
            border-bottom: none;
        }

        .attr-name {
            font-weight: bold;
            color: #333;
        }

        .attr-value {
            color: #666;
            margin-left: 10px;
        }

        .lightbox {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.9);
        }

        .lightbox.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lightbox-content {
            position: relative;
            max-width: 95vw;
            max-height: 95vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .lightbox-image {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
        }

        .lightbox-close {
            position: absolute;
            top: 10px;
            right: 20px;
            color: black;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }

        @media (max-width: 1200px) {
            .product-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 992px) {
            .product-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-title">🛍️ Quản lý Sản phẩm</div>
        <a href="?controller=post&action=index" class="navbar-link">← Quay lại Đăng tin</a>
    </div>

    <div class="admin-container">
        <div class="header">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <h1 style="margin: 0;">Danh sách sản phẩm chờ duyệt</h1>
                <button onclick="approveAllProducts()" style="padding: 10px 15px; background: #27ae60; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 14px; transition: background 0.2s;" onmouseover="this.style.background='#229954'" onmouseout="this.style.background='#27ae60'">✓ Duyệt tất cả</button>
            </div>
            <div class="stats">
                <div class="stat-item">
                    <span>Tổng:</span>
                    <span class="stat-number" id="totalCount">0</span>
                </div>
                <div class="stat-item">
                    <span>Trang:</span>
                    <span class="stat-number"><span id="currentPage">1</span>/<span id="totalPages">1</span></span>
                </div>
            </div>
        </div>

        <div class="product-grid" id="productList">
            <!-- Sản phẩm sẽ được load qua JS -->
        </div>

        <div class="pagination" id="pagination">
            <!-- Pagination sẽ được tạo qua JS -->
        </div>
    </div>

    <!-- Modal chi tiết sản phẩm -->
    <div class="modal" id="detailModal">
        <div class="modal-content">
            <button class="modal-close" id="closeModal">&times;</button>
            <div id="modalBody"></div>
        </div>
    </div>

    <!-- Modal từ chối -->
    <div class="modal" id="rejectModal">
        <div class="modal-content">
            <button class="modal-close" id="closeRejectModal">&times;</button>
            <h3>Từ chối sản phẩm</h3>
            <p id="rejectProductName" style="margin: 15px 0; color: #666;"></p>
            <p style="color: #666; margin-bottom: 20px;">Bạn có chắc muốn từ chối sản phẩm này?</p>
            <div style="margin-top: 15px; display: flex; gap: 10px;">
                <button class="btn-small btn-reject" id="confirmReject">Xác nhận từ chối</button>
                <button class="btn-small" style="background: #95a5a6; color: white;" id="cancelReject">Hủy</button>
            </div>
        </div>
    </div>

    <!-- Lightbox ảnh -->
    <div class="lightbox" id="lightbox">
        <div class="lightbox-content">
            <span class="lightbox-close" id="closeLightbox">&times;</span>
            <img id="lightboxImage" class="lightbox-image" src="" alt="">
        </div>
    </div>

    <script>
        let allProducts = [];
        let currentProductId = null;
        const ITEMS_PER_PAGE = 10;
        let currentPage = 1;

        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();

            document.getElementById('closeModal').addEventListener('click', () => {
                document.getElementById('detailModal').classList.remove('active');
            });

            document.getElementById('closeRejectModal').addEventListener('click', () => {
                document.getElementById('rejectModal').classList.remove('active');
            });

            document.getElementById('cancelReject').addEventListener('click', () => {
                document.getElementById('rejectModal').classList.remove('active');
            });

            document.getElementById('confirmReject').addEventListener('click', rejectProduct);

            document.getElementById('closeLightbox').addEventListener('click', () => {
                document.getElementById('lightbox').classList.remove('active');
            });

            document.getElementById('lightbox').addEventListener('click', (e) => {
                if (e.target === document.getElementById('lightbox')) {
                    document.getElementById('lightbox').classList.remove('active');
                }
            });
        });

        function loadProducts() {
            fetch('?controller=admin&action=getPendingProducts')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        allProducts = data.data;
                        renderPage(1);
                    } else {
                        alert('Lỗi: ' + data.error);
                    }
                })
                .catch(err => alert('Lỗi load sản phẩm: ' + err.message));
        }

        function renderPage(page) {
            currentPage = page;
            const startIdx = (page - 1) * ITEMS_PER_PAGE;
            const endIdx = startIdx + ITEMS_PER_PAGE;
            const pageProducts = allProducts.slice(startIdx, endIdx);

            displayProducts(pageProducts);
            renderPagination();
        }

        function displayProducts(products) {
            const productList = document.getElementById('productList');
            const totalCount = document.getElementById('totalCount');
            const currentPageEl = document.getElementById('currentPage');

            totalCount.textContent = allProducts.length;
            currentPageEl.textContent = currentPage;

            if (products.length === 0 && allProducts.length === 0) {
                productList.innerHTML = '<div class="empty-state" style="grid-column: 1/-1;">Không có sản phẩm nào chờ duyệt</div>';
                return;
            }

            productList.innerHTML = products.map(product => `
                <div class="product-card">
                    <img src="${product.avatar}" alt="${product.ten_sanpham}" class="product-image" onclick="viewDetail(${product.id_sanpham})">
                    <div class="product-info">
                        <div class="product-name">${product.ten_sanpham}</div>
                        <div class="product-price">${formatPrice(product.gia)} VNĐ</div>
                        <div class="product-category">${product.ten_danhmuc}</div>
                        <div class="product-actions">
                            <button class="btn-small btn-detail" onclick="viewDetail(${product.id_sanpham})">Chi tiết</button>
                            <button class="btn-small btn-approve" onclick="approveProduct(${product.id_sanpham})">Duyệt</button>
                            <button class="btn-small btn-reject" onclick="openRejectModal(${product.id_sanpham}, '${product.ten_sanpham}')">Từ chối</button>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function renderPagination() {
            const totalPages = Math.ceil(allProducts.length / ITEMS_PER_PAGE);
            const pagination = document.getElementById('pagination');
            const totalPagesEl = document.getElementById('totalPages');

            totalPagesEl.textContent = totalPages;

            if (totalPages <= 1) {
                pagination.innerHTML = '';
                return;
            }

            let html = '';
            
            if (currentPage > 1) {
                html += `<button onclick="renderPage(1)">« Đầu</button>`;
                html += `<button onclick="renderPage(${currentPage - 1})">‹ Trước</button>`;
            }

            for (let i = Math.max(1, currentPage - 2); i <= Math.min(totalPages, currentPage + 2); i++) {
                html += `<button onclick="renderPage(${i})" class="${i === currentPage ? 'active' : ''}">${i}</button>`;
            }

            if (currentPage < totalPages) {
                html += `<button onclick="renderPage(${currentPage + 1})">Sau ›</button>`;
                html += `<button onclick="renderPage(${totalPages})">Cuối »</button>`;
            }

            pagination.innerHTML = html;
        }

        function viewDetail(id_sanpham) {
            fetch('?controller=admin&action=getProductDetail&id_sanpham=' + id_sanpham)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        displayDetail(data);
                    } else {
                        alert('Lỗi: ' + data.error);
                    }
                });
        }

        function displayDetail(data) {
            const product = data.product;
            const images = data.images || [];
            const attributes = data.attributes || [];

            let html = `
                <div class="detail-header">
                    <div class="detail-title">${product.ten_sanpham}</div>
                    <div class="detail-price">${formatPrice(product.gia)} VNĐ</div>
                </div>

                <div class="detail-section">
                    <div class="section-title">Ảnh sản phẩm:</div>
                    <div class="gallery">
            `;

            images.forEach(img => {
                html += `<img src="${img.url_anh}" alt="Ảnh" onclick="openLightbox('${img.url_anh}')">`;
            });

            html += `
                    </div>
                </div>

                <div class="detail-section">
                    <div class="section-title">Danh mục:</div>
                    <div class="section-value">${product.ten_danhmuc}</div>
                </div>

                <div class="detail-section">
                    <div class="section-title">Mô tả:</div>
                    <div class="section-value">${product.mota}</div>
                </div>

                <div class="detail-section">
                    <div class="section-title">Người bán:</div>
                    <div class="section-value">
                        <strong>${product.hoten}</strong><br>
                        📱 ${product.sdt || 'Chưa cập nhật'}<br>
                        📍 ${product.diachi || 'Chưa cập nhật'}
                    </div>
                </div>
            `;

            if (attributes.length > 0) {
                html += `
                    <div class="detail-section">
                        <div class="section-title">Thuộc tính sản phẩm:</div>
                        <div class="attributes-list">
                `;
                attributes.forEach(attr => {
                    html += `
                        <div class="attribute-item">
                            <span class="attr-name">${attr.ten_thuoctinh}:</span>
                            <span class="attr-value">${attr.giatri}</span>
                        </div>
                    `;
                });
                html += `
                        </div>
                    </div>
                `;
            }

            document.getElementById('modalBody').innerHTML = html;
            document.getElementById('detailModal').classList.add('active');
        }

        function openLightbox(imageSrc) {
            document.getElementById('lightboxImage').src = imageSrc;
            document.getElementById('lightbox').classList.add('active');
        }

        function approveProduct(id_sanpham) {
            if (!confirm('Duyệt sản phẩm này?')) return;

            const formData = new FormData();
            formData.append('id_sanpham', id_sanpham);

            fetch('?controller=admin&action=approve', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    loadProducts();
                } else {
                    alert('Lỗi: ' + data.error);
                }
            })
            .catch(err => alert('Lỗi: ' + err.message));
        }

        function approveAllProducts() {
            if (!confirm('Bạn có chắc muốn duyệt TẤT CẢ sản phẩm chờ duyệt?')) return;

            fetch('?controller=admin&action=approveAll', {
                method: 'POST'
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    loadProducts();
                } else {
                    alert('Lỗi: ' + data.error);
                }
            })
            .catch(err => alert('Lỗi: ' + err.message));
        }

        function openRejectModal(id_sanpham, product_name) {
            currentProductId = id_sanpham;
            document.getElementById('rejectProductName').textContent = 'Sản phẩm: ' + product_name;
            document.getElementById('rejectModal').classList.add('active');
        }

        function rejectProduct() {
            if (!currentProductId) return;

            const formData = new FormData();
            formData.append('id_sanpham', currentProductId);
            formData.append('reason', '');

            fetch('?controller=admin&action=reject', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    document.getElementById('rejectModal').classList.remove('active');
                    loadProducts();
                } else {
                    alert('Lỗi: ' + data.error);
                }
            })
            .catch(err => alert('Lỗi: ' + err.message));
        }

        function formatPrice(price) {
            return new Intl.NumberFormat('vi-VN').format(price);
        }
    </script>
</body>
</html>
