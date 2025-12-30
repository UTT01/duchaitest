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

    if (!cat1 || !form) { return; } // Removed error log to clean up console if elements missing

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

    // ===== LỌC SẢN PHẨM - Danh mục cha-con =====
    // Note: Phần này được giữ lại từ code gốc, nhưng cần kiểm tra xem trong HTML có id 'filterCat1' không.
    const filterCat1 = document.getElementById('filterCat1');
    const filterCat2 = document.getElementById('filterCat2');
    
    if (filterCat1) {
        // Load danh mục cha
        fetch(apiUrl + '/getParentCategories')
            .then(res => res.json())
            .then(data => {
                if(data.error) return;
                data.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id_danhmuc;
                    opt.textContent = item.ten_danhmuc;
                    filterCat1.appendChild(opt);
                });
            });

        filterCat1.addEventListener('change', function() {
            const idParent = this.value;
            if(filterCat2) {
                filterCat2.innerHTML = '<option value="">-- Chọn loại sản phẩm --</option>';
                filterCat2.style.display = 'none';
                
                if (!idParent) return;
                
                fetch(apiUrl + `/getSubCategories/${idParent}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            filterCat2.style.display = 'block';
                            data.forEach(item => {
                                const opt = document.createElement('option');
                                opt.value = item.id_danhmuc;
                                opt.textContent = item.ten_danhmuc;
                                filterCat2.appendChild(opt);
                            });
                        }
                    });
            }
        });
    }

    // Hàm submitFilterForm có thể được gọi từ HTML
    window.submitFilterForm = function() {
        const keywordInput = document.querySelector('input[name="q"]');
        const addressInput = document.querySelector('input[name="diachi"]');
        
        const keyword = keywordInput ? keywordInput.value : '';
        const address = addressInput ? addressInput.value : '';
        
        // Cần đảm bảo filterCat1/2 tồn tại trước khi lấy value
        const cat1Val = filterCat1 ? filterCat1.value : '';
        const cat2Val = filterCat2 ? filterCat2.value : '';
        const category = cat2Val || cat1Val;
        
        let url = '/baitaplon/Home?';
        if (keyword) url += '&q=' + encodeURIComponent(keyword);
        if (category) url += '&danhmuc=' + encodeURIComponent(category);
        if (address) url += '&diachi=' + encodeURIComponent(address);
        
        window.location.href = url;
    }
});

// Hàm xử lý khi chọn danh mục từ Dropdown (Đặt ngoài để gọi từ onclick HTML)
function selectCategory(id, name) {
    // 1. Cập nhật giá trị vào input hidden
    const inputEl = document.getElementById('inputDanhmuc');
    if(inputEl) inputEl.value = id;
    
    // 2. Cập nhật text hiển thị trên nút
    const btnText = document.querySelector('#dropdownMenuButton span');
    if(btnText) btnText.textContent = name;

    // 3. Submit form tìm kiếm
    const searchForm = document.getElementById('searchForm');
    if(searchForm) searchForm.submit();
}