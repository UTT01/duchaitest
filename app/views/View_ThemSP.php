<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Tin</title>
    
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

    <div style="position: fixed; top: 10px; right: 10px; z-index: 9999;">
        <a href="?controller=admin&action=index" style="display: inline-block; padding: 10px 15px; background: #2c3e50; color: white; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13px;">→ Vào Admin</a>
    </div>

    <button class="btn-dang-tin" id="openModalBtn">Đăng tin ngay</button>

    <div class="modal-overlay" id="modalOverlay">
        <div class="modal-box">
            <span class="close-btn" id="closeModalBtn">&times;</span>
            <div class="modal-header">Đăng tin rao vặt</div>

            <form id="postForm" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label>Hình ảnh sản phẩm <span style="color:red">*</span></label>
                    <div class="upload-grid" id="uploadGrid">
                        <div class="upload-box-btn" id="addImgBtn">
                            <span>+</span>
                            <p>Đăng ảnh</p>
                            <input type="file" id="imageInput" multiple accept="image/*">
                        </div>
                    </div>
                    <div class="error-msg" id="err-images">Vui lòng chọn ít nhất 1 ảnh.</div>
                </div>

                <div class="form-group">
                    <label>Danh mục chính <span style="color:red">*</span></label>
                    <select id="catLevel1" name="catLevel1">
                        <option value="">-- Chọn danh mục --</option>
                    </select>
                    <div class="error-msg" id="err-catLevel1">Vui lòng chọn danh mục.</div>
                </div>

                <div class="form-group" id="group-catLevel2" style="display:none;">
                    <label>Loại sản phẩm <span style="color:red">*</span></label>
                    <select id="catLevel2" name="catLevel2">
                        <option value="">-- Chọn loại sản phẩm --</option>
                    </select>
                    <div class="error-msg" id="err-catLevel2">Vui lòng chọn loại sản phẩm.</div>
                </div>

                <div class="form-group">
                    <label>Tiêu đề tin <span style="color:red">*</span></label>
                    <input type="text" id="title" name="title" placeholder="VD: Bán xe Vios cũ...">
                    <div class="error-msg" id="err-title">Nhập tiêu đề.</div>
                </div>

                <div class="form-group">
                    <label>Giá bán (VNĐ) <span style="color:red">*</span></label>
                    <input type="number" id="price" name="price" placeholder="Nhập giá">
                    <div class="error-msg" id="err-price">Nhập giá.</div>
                </div>

                <div id="dynamic-attributes"></div>

                <div class="form-group">
                    <label>Mô tả chi tiết <span style="color:red">*</span></label>
                    <textarea id="description" name="description" rows="4" placeholder="Mô tả chi tiết..."></textarea>
                    <div class="error-msg" id="err-description">Mô tả quá ngắn (tối thiểu 10 từ).</div>
                </div>

                <div class="form-group">
                    <label>Địa chỉ giao dịch <span style="color:red">*</span></label>
                    <input type="text" id="address" name="address" placeholder="Địa chỉ cụ thể...">
                    <div class="error-msg" id="err-address">Nhập địa chỉ.</div>
                </div>

                <button type="submit" class="btn-submit">Đăng tin</button>
            </form>
        </div>
    </div>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const modal = document.getElementById('modalOverlay');
        const openBtn = document.getElementById('openModalBtn');
        const closeBtn = document.getElementById('closeModalBtn');
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

        if(openBtn) openBtn.addEventListener('click', () => modal.classList.add('active'));
        if(closeBtn) closeBtn.addEventListener('click', () => modal.classList.remove('active'));
        if(modal) modal.addEventListener('click', (e) => {
            if(e.target === modal) modal.classList.remove('active');
        });

        // Lấy danh mục cha
        fetch('?controller=categories&action=getParentCategories')
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
            // Lấy danh mục con
            fetch(`?controller=categories&action=getSubCategories&id_parent=${idParent}`)
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
            // Lấy thuộc tính sản phẩm
            fetch(`?controller=categories&action=getAttributes&id_danhmuc=${idDanhmuc}`)
                .then(res => res.json())
                .then(attributes => {
                    attributes.forEach(attr => {
                        const div = document.createElement('div');
                        div.className = 'form-group';
                        const label = document.createElement('label');
                        label.textContent = attr.ten_thuoctinh;
                        div.appendChild(label);
                        let inputEl;
                        if (attr.options && attr.options.length > 0) {
                            inputEl = document.createElement('select');
                            inputEl.innerHTML = `<option value="">-- Chọn ${attr.ten_thuoctinh} --</option>`;
                            attr.options.forEach(opt => {
                                const option = document.createElement('option');
                                option.value = opt.id_option;
                                option.textContent = opt.gia_tri_option;
                                inputEl.appendChild(option);
                            });
                        } else {
                            inputEl = document.createElement('input');
                            inputEl.type = 'text';
                            inputEl.placeholder = `Nhập ${attr.ten_thuoctinh}...`;
                        }
                        inputEl.className = 'dynamic-field';
                        inputEl.name = `thuoctinh[${attr.id_thuoctinh}]`;
                        inputEl.style.width = '100%';
                        const errMsg = document.createElement('div');
                        errMsg.className = 'error-msg';
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
                        <img src="${e.target.result}">
                        <div class="btn-remove-img" onclick="removeImage(this, '${file.name}')">&times;</div>
                    `;
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
            let isValid = true;
            const toggleError = (id, condition) => {
                const el = document.getElementById(id);
                const err = document.getElementById('err-' + id);
                if(el && err) {
                    if(condition) { el.classList.add('input-error'); err.style.display = 'block'; isValid = false; }
                    else { el.classList.remove('input-error'); err.style.display = 'none'; }
                }
            };
            toggleError('catLevel1', !cat1.value);
            if(groupCat2.style.display !== 'none') toggleError('catLevel2', !cat2.value);
            toggleError('title', !document.getElementById('title').value.trim());
            toggleError('price', !document.getElementById('price').value.trim());
            toggleError('address', !document.getElementById('address').value.trim());
            const desc = document.getElementById('description');
            if(desc) toggleError('description', desc.value.trim().split(/\s+/).filter(w=>w).length < 10);
            document.querySelectorAll('.dynamic-field').forEach(field => {
                if(!field.value.trim()) {
                    field.classList.add('input-error');
                    if(field.nextElementSibling) field.nextElementSibling.style.display='block';
                    isValid=false;
                } else {
                    field.classList.remove('input-error');
                    if(field.nextElementSibling) field.nextElementSibling.style.display='none';
                }
            });
            const errImg = document.getElementById('err-images');
            if(selectedFiles.length === 0) { errImg.style.display = 'block'; isValid = false; } else { errImg.style.display = 'none'; }

            if(isValid) {
                const formData = new FormData(form);
                selectedFiles.forEach((file) => { formData.append('images[]', file); });
                
                // Submit dữ liệu đến PostController action 'add'
                fetch('?controller=post&action=add', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        form.reset();
                        selectedFiles = [];
                        uploadGrid.innerHTML = '<div class="upload-box-btn" id="addImgBtn"><span>+</span><p>Đăng ảnh</p><input type="file" id="imageInput" multiple accept="image/*"></div>';
                        modal.classList.remove('active');
                        // Tự động reload lại trang sau 1.5 giây
                        setTimeout(() => {
                            location.reload();
                        }, 100);
                    } else {
                        alert('Lỗi: ' + data.error);
                    }
                })
                .catch(err => alert('Lỗi submit: ' + err.message));
            }
        });
    });
    </script>
</body>
</html>