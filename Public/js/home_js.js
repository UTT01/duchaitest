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
const locationData = {
    "Tuyên Quang": ["Thành phố Hà Giang", "Bắc Mê", "Bắc Quang", "Đồng Văn", "Hoàng Su Phì", "Mèo Vạc", "Quản Bạ", "Quang Bình", "Vị Xuyên", "Xín Mần", "Yên Minh", "Thành phố Tuyên Quang", "Chiêm Hóa", "Hàm Yên", "Lâm Bình", "Na Hang", "Sơn Dương", "Yên Sơn"],
    "Cao Bằng": ["Thành phố Cao Bằng", "Bảo Lạc", "Bảo Lâm", "Hạ Lang", "Hà Quảng", "Hòa An", "Nguyên Bình", "Quảng Hòa", "Thạch An", "Trùng Khánh"],
    "Lai Châu": ["Thành phố Lai Châu", "Mường Tè", "Nậm Nhùn", "Phong Thổ", "Sìn Hồ", "Tam Đường", "Tân Uyên", "Than Uyên"],
    "Lào Cai": ["Thành phố Lào Cai", "Thị xã Sa Pa", "Bát Xát", "Bảo Thắng", "Bảo Yên", "Bắc Hà", "Mường Khương", "Si Ma Cai", "Văn Bàn", "Thành phố Yên Bái", "Thị xã Nghĩa Lộ", "Lục Yên", "Mù Cang Chải", "Trạm Tấu", "Trấn Yên", "Văn Chấn", "Văn Yên", "Yên Bình"],
    "Thái Nguyên": ["Thành phố Bắc Kạn", "Ba Bể", "Bạch Thông", "Chợ Đồn", "Chợ Mới", "Na Rì", "Ngân Sơn", "Pác Nặm", "Thành phố Thái Nguyên", "Thành phố Phổ Yên", "Thành phố Sông Công", "Đại Từ", "Định Hóa", "Đồng Hỷ", "Phú Bình", "Phú Lương", "Võ Nhai"],
    "Điện Biên": ["Thành phố Điện Biên Phủ", "Thị xã Mường Lay", "Điện Biên", "Điện Biên Đông", "Mường Ảng", "Mường Chà", "Mường Nhé", "Nậm Pồ", "Tủa Chùa", "Tuần Giáo"],
    "Lạng Sơn": ["Thành phố Lạng Sơn", "Bắc Sơn", "Bình Gia", "Cao Lộc", "Chi Lăng", "Đình Lập", "Hữu Lũng", "Lộc Bình", "Tràng Định", "Văn Lãng", "Văn Quan"],
    "Sơn La": ["Thành phố Sơn La", "Bắc Yên", "Mai Sơn", "Mộc Châu", "Mường La", "Phù Yên", "Quỳnh Nhai", "Sông Mã", "Sốp Cộp", "Thuận Châu", "Vân Hồ", "Yên Châu"],
    "Phú Thọ": ["Thành phố Hòa Bình", "Cao Phong", "Đà Bắc", "Kim Bôi", "Lạc Sơn", "Lạc Thủy", "Lương Sơn", "Mai Châu", "Tân Lạc", "Yên Thủy", "Thành phố Vĩnh Yên", "Thành phố Phúc Yên", "Bình Xuyên", "Lập Thạch", "Sông Lô", "Tam Đảo", "Tam Dương", "Vĩnh Tường", "Yên Lạc", "Thành phố Việt Trì", "Thị xã Phú Thọ", "Cẩm Khê", "Đoan Hùng", "Hạ Hòa", "Lâm Thao", "Phù Ninh", "Tam Nông", "Tân Sơn", "Thanh Ba", "Thanh Sơn", "Thanh Thủy", "Yên Lập"],
    "Bắc Ninh": ["Thành phố Bắc Giang", "Thị xã Việt Yên", "Hiệp Hòa", "Lạng Giang", "Lục Nam", "Lục Ngạn", "Sơn Động", "Tân Yên", "Yên Dũng", "Yên Thế", "Thành phố Bắc Ninh", "Thành phố Từ Sơn", "Gia Bình", "Lương Tài", "Quế Võ", "Thuận Thành", "Tiên Du", "Yên Phong"],
    "Quảng Ninh": ["Thành phố Hạ Long", "Thành phố Móng Cái", "Thành phố Uông Bí", "Thành phố Cẩm Phả", "Thị xã Đông Triều", "Thị xã Quảng Yên", "Ba Chẽ", "Bình Liêu", "Cô Tô", "Đầm Hà", "Hải Hà", "Tiên Yên", "Vân Đồn"],
    "TP. Hà Nội": ["Ba Đình", "Hoàn Kiếm", "Tây Hồ", "Long Biên", "Cầu Giấy", "Đống Đa", "Hai Bà Trưng", "Hoàng Mai", "Thanh Xuân", "Nam Từ Liêm", "Bắc Từ Liêm", "Hà Đông", "Thị xã Sơn Tây", "Ba Vì", "Chương Mỹ", "Đan Phượng", "Đông Anh", "Gia Lâm", "Hoài Đức", "Mê Linh", "Mỹ Đức", "Phú Xuyên", "Phúc Thọ", "Quốc Oai", "Sóc Sơn", "Thạch Thất", "Thanh Oai", "Thanh Trì", "Thường Tín", "Ứng Hòa"],
    "TP. Hải Phòng": ["Thành phố Hải Dương", "Thành phố Chí Linh", "Thị xã Kinh Môn", "Bình Giang", "Cẩm Giàng", "Gia Lộc", "Kim Thành", "Nam Sách", "Ninh Giang", "Thanh Hà", "Thanh Miện", "Tứ Kỳ", "Hồng Bàng", "Ngô Quyền", "Lê Chân", "Hải An", "Kiến An", "Đồ Sơn", "Dương Kinh", "Thủy Nguyên", "An Dương", "An Lão", "Kiến Thụy", "Tiên Lãng", "Vĩnh Bảo", "Cát Hải", "Bạch Long Vĩ"],
    "Hưng Yên": ["Thành phố Thái Bình", "Đông Hưng", "Hưng Hà", "Kiến Xương", "Quỳnh Phụ", "Thái Thụy", "Tiền Hải", "Vũ Thư", "Thành phố Hưng Yên", "Thị xã Mỹ Hào", "Ân Thi", "Khoái Châu", "Kim Động", "Phù Cừ", "Tiên Lữ", "Văn Giang", "Văn Lâm", "Yên Mỹ"],
    "Ninh Bình": ["Thành phố Phủ Lý", "Thị xã Duy Tiên", "Bình Lục", "Kim Bảng", "Lý Nhân", "Thanh Liêm", "Thành phố Ninh Bình", "Thành phố Tam Điệp", "Gia Viễn", "Hoa Lư", "Kim Sơn", "Nho Quan", "Yên Khánh", "Yên Mô", "Thành phố Nam Định", "Giao Thủy", "Hải Hậu", "Mỹ Lộc", "Nam Trực", "Nghĩa Hưng", "Trực Ninh", "Vụ Bản", "Xuân Trường", "Ý Yên"],
    "Thanh Hóa": ["Thành phố Thanh Hóa", "Thành phố Sầm Sơn", "Thị xã Bỉm Sơn", "Thị xã Nghi Sơn", "Bá Thước", "Cẩm Thủy", "Đông Sơn", "Hà Trung", "Hậu Lộc", "Hoằng Hóa", "Lang Chánh", "Mường Lát", "Nga Sơn", "Ngọc Lặc", "Như Thanh", "Như Xuân", "Nông Cống", "Quan Hóa", "Quan Sơn", "Quảng Xương", "Thạch Thành", "Thiệu Hóa", "Thọ Xuân", "Thường Xuân", "Triệu Sơn", "Vĩnh Lộc", "Yên Định"],
    "Nghệ An": ["Thành phố Vinh", "Thị xã Cửa Lò", "Thị xã Hoàng Mai", "Thị xã Thái Hòa", "Anh Sơn", "Con Cuông", "Diễn Châu", "Đô Lương", "Hưng Nguyên", "Kỳ Sơn", "Nam Đàn", "Nghi Lộc", "Nghĩa Đàn", "Quế Phong", "Quỳ Châu", "Quỳ Hợp", "Quỳnh Lưu", "Tân Kỳ", "Thanh Chương", "Tương Dương", "Yên Thành"],
    "Hà Tĩnh": ["Thành phố Hà Tĩnh", "Thị xã Hồng Lĩnh", "Thị xã Kỳ Anh", "Cẩm Xuyên", "Can Lộc", "Đức Thọ", "Hương Khê", "Hương Sơn", "Kỳ Anh", "Lộc Hà", "Nghi Xuân", "Thạch Hà", "Vũ Quang"],
    "Quảng Trị": ["Thành phố Đồng Hới", "Thị xã Ba Đồn", "Bố Trạch", "Lệ Thủy", "Minh Hóa", "Quảng Ninh", "Quảng Trạch", "Tuyên Hóa", "Thành phố Đông Hà", "Thị xã Quảng Trị", "Cam Lộ", "Cồn Cỏ", "Đa Krông", "Gio Linh", "Hải Lăng", "Hướng Hóa", "Triệu Phong", "Vĩnh Linh"],
    "TP. Huế (Thừa Thiên Huế)": ["Thành phố Huế", "Thị xã Hương Thủy", "Thị xã Hương Trà", "A Lưới", "Nam Đông", "Phong Điền", "Phú Lộc", "Phú Vang", "Quảng Điền"],
    "TP. Đà Nẵng": ["Thành phố Tam Kỳ", "Thành phố Hội An", "Thị xã Điện Bàn", "Bắc Trà My", "Đại Lộc", "Đông Giang", "Duy Xuyên", "Hiệp Đức", "Nam Giang", "Nam Trà My", "Nông Sơn", "Núi Thành", "Phú Ninh", "Phước Sơn", "Quế Sơn", "Tây Giang", "Thăng Bình", "Tiên Phước", "Quận Hải Châu", "Cẩm Lệ", "Thanh Khê", "Liên Chiểu", "Ngũ Hành Sơn", "Sơn Trà", "Huyện Hòa Vang", "Hoàng Sa"],
    "Quảng Ngãi": ["Thành phố Quảng Ngãi", "Thị xã Đức Phổ", "Ba Tơ", "Bình Sơn", "Lý Sơn", "Minh Long", "Mộ Đức", "Nghĩa Hành", "Sơn Hà", "Sơn Tây", "Sơn Tịnh", "Trà Bồng", "Tư Nghĩa", "Thành phố Kon Tum", "Đăk Glei", "Đăk Hà", "Đăk Tô", "Ia H'Drai", "Kon Plông", "Kon Rẫy", "Ngọc Hồi", "Sa Thầy", "Tu Mơ Rông"],
    "Gia Lai": ["Thành phố Pleiku", "Thị xã An Khê", "Thị xã Ayun Pa", "Chư Păh", "Chư Prông", "Chư Pưh", "Chư Sê", "Đăk Đoa", "Đăk Pơ", "Đức Cơ", "Ia Grai", "Ia Pa", "KBang", "Kông Chro", "Krông Pa", "Mang Yang", "Phú Thiện", "Thành phố Quy Nhơn", "Thị xã An Nhơn", "Thị xã Hoài Nhơn", "An Lão", "Hoài Ân", "Phù Cát", "Phù Mỹ", "Tây Sơn", "Tuy Phước", "Vân Canh", "Vĩnh Thạnh"],
    "Đắk Lắk": ["Thành phố Tuy Hòa", "Thị xã Đông Hòa", "Thị xã Sông Cầu", "Đồng Xuân", "Phú Hòa", "Sơn Hòa", "Sông Hinh", "Tây Hòa", "Tuy An", "Thành phố Buôn Ma Thuột", "Thị xã Buôn Hồ", "Buôn Đôn", "Cư Kuin", "Cư M'gar", "Ea H'leo", "Ea Kar", "Ea Súp", "Krông Ana", "Krông Bông", "Krông Búk", "Krông Năng", "Krông Pắc", "Lắk", "M'Drăk"],
    "Khánh Hoà": ["Thành phố Nha Trang", "Thành phố Cam Ranh", "Thị xã Ninh Hòa", "Cam Lâm", "Diên Khánh", "Khánh Sơn", "Khánh Vĩnh", "Trường Sa", "Vạn Ninh", "Thành phố Phan Rang - Tháp Chàm", "Bác Ái", "Ninh Hải", "Ninh Phước", "Ninh Sơn", "Thuận Bắc", "Thuận Nam"],
    "Lâm Đồng": ["Thành phố Gia Nghĩa", "Cư Jút", "Đắk Glong", "Đắk Mil", "Đắk R'lấp", "Đắk Song", "Krông Nô", "Tuy Đức", "Thành phố Đà Lạt", "Thành phố Bảo Lộc", "Bảo Lâm", "Cát Tiên", "Đạ Huoai", "Đạ Tẻh", "Đam Rông", "Di Linh", "Đơn Dương", "Đức Trọng", "Lạc Dương", "Lâm Hà", "Thành phố Phan Thiết", "Thị xã La Gi", "Bắc Bình", "Đức Linh", "Hàm Tân", "Hàm Thuận Bắc", "Hàm Thuận Nam", "Phú Quý", "Tánh Linh", "Tuy Phong"],
    "Đồng Nai": ["Thành phố Đồng Xoài", "Thị xã Bình Long", "Thị xã Chơn Thành", "Thị xã Phước Long", "Bù Đăng", "Bù Đốp", "Bù Gia Mập", "Đồng Phú", "Hớn Quản", "Lộc Ninh", "Phú Riềng", "Thành phố Biên Hòa", "Thành phố Long Khánh", "Cẩm Mỹ", "Định Quán", "Long Thành", "Nhơn Trạch", "Tân Phú", "Thống Nhất", "Trảng Bom", "Vĩnh Cửu", "Xuân Lộc"],
    "Tây Ninh": ["Thành phố Tân An", "Thị xã Kiến Tường", "Bến Lức", "Cần Đước", "Cần Giuộc", "Châu Thành (Long An)", "Đức Huệ", "Đức Hòa", "Mộc Hóa", "Tân Hưng", "Tân Thạnh", "Tân Trụ", "Thạnh Hóa", "Thủ Thừa", "Vĩnh Hưng", "Thành phố Tây Ninh", "Thị xã Hòa Thành", "Thị xã Trảng Bàng", "Bến Cầu", "Châu Thành (Tây Ninh)", "Dương Minh Châu", "Gò Dầu", "Tân Biên", "Tân Châu"],
    "TP. Hồ Chí Minh": ["Thành phố Thủ Dầu Một", "Thành phố Dĩ An", "Thành phố Thuận An", "Thành phố Tân Uyên", "Thành phố Bến Cát", "Bắc Tân Uyên", "Bàu Bàng", "Dầu Tiếng", "Phú Giáo", "Thành phố Thủ Đức", "Quận 1", "3", "4", "5", "6", "7", "8", "10", "11", "12", "Bình Tân", "Bình Thạnh", "Gò Vấp", "Phú Nhuận", "Tân Bình", "Tân Phú", "Bình Chánh", "Cần Giờ", "Củ Chi", "Hóc Môn", "Nhà Bè", "Thành phố Vũng Tàu", "Thành phố Bà Rịa", "Thị xã Phú Mỹ", "Châu Đức", "Côn Đảo", "Đất Đỏ", "Long Điền", "Xuyên Mộc"],
    "Đồng Tháp": ["Thành phố Mỹ Tho", "Thành phố Gò Công", "Thị xã Cai Lậy", "Cái Bè", "Huyện Cai Lậy", "Châu Thành (Tiền Giang)", "Chợ Gạo", "Gò Công Đông", "Gò Công Tây", "Tân Phước", "Tân Phú Đông", "Thành phố Cao Lãnh", "Thành phố Sa Đéc", "Thành phố Hồng Ngự", "Huyện Cao Lãnh", "Châu Thành (Đồng Tháp)", "Huyện Hồng Ngự", "Lai Vung", "Lấp Vò", "Tam Nông", "Tân Hồng", "Thanh Bình", "Tháp Mười"],
    "An Giang": ["Thành phố Rạch Giá", "Thành phố Hà Tiên", "Thành phố Phú Quốc", "An Biên", "An Minh", "Châu Thành (Kiên Giang)", "Giang Thành", "Giồng Riềng", "Gò Quao", "Hòn Đất", "Kiên Hải", "Kiên Lương", "Tân Hiệp", "U Minh Thượng", "Vĩnh Thuận", "Thành phố Long Xuyên", "Thành phố Châu Đốc", "Thị xã Tân Châu", "Thị xã Tịnh Biên", "An Phú", "Châu Phú", "Châu Thành (An Giang)", "Chợ Mới", "Phú Tân", "Thoại Sơn", "Tri Tôn"],
    "Vĩnh Long": ["Thành phố Bến Tre", "Ba Tri", "Bình Đại", "Châu Thành (Bến Tre)", "Chợ Lách", "Giồng Trôm", "Mỏ Cày Bắc", "Mỏ Cày Nam", "Thạnh Phú", "Thành phố Vĩnh Long", "Thị xã Bình Minh", "Bình Tân", "Long Hồ", "Mang Thít", "Tam Bình", "Trà Ôn", "Vũng Liêm", "Thành phố Trà Vinh", "Thị xã Duyên Hải", "Càng Long", "Cầu Kè", "Cầu Ngang", "Châu Thành (Trà Vinh)", "Huyện Duyên Hải", "Tiểu Cần", "Trà Cú"],
    "TP. Cần Thơ": ["Thành phố Sóc Trăng", "Thị xã Ngã Năm", "Thị xã Vĩnh Châu", "Châu Thành (Sóc Trăng)", "Cù Lao Dung", "Kế Sách", "Long Phú", "Mỹ Tú", "Mỹ Xuyên", "Thạnh Trị", "Trần Đề", "Thành phố Vị Thanh", "Thành phố Ngã Bảy", "Thị xã Long Mỹ", "Châu Thành (Hậu Giang)", "Châu Thành A", "Huyện Long Mỹ", "Phụng Hiệp", "Vị Thủy", "Quận Ninh Kiều", "Bình Thủy", "Cái Răng", "Ô Môn", "Thốt Nốt", "Huyện Cờ Đỏ", "Phong Điền", "Thới Lai", "Vĩnh Thạnh"],
    "Cà Mau": ["Thành phố Bạc Liêu", "Thị xã Giá Rai", "Đông Hải", "Hòa Bình", "Hồng Dân", "Phước Long", "Vĩnh Lợi", "Thành phố Cà Mau", "Cái Nước", "Đầm Dơi", "Năm Căn", "Ngọc Hiển", "Phú Tân", "Thới Bình", "Trần Văn Thời", "U Minh"]
};

$(document).ready(function() {
    setupPostModalLocation();
    setupNavbarLocation();
});

// === LOGIC 1: MODAL ĐĂNG TIN (Chọn Tỉnh -> Chọn Huyện) ===
function setupPostModalLocation() {
    let currentProvince = "";
    const addressListArea = $('#address-list-area');
    const searchBox = $('#address-search-box');
    const districtHeader = $('#district-header');
    const selectedProvinceDisplay = $('#selected-province-display');
    const addressLabel = $('#address-label');
    const hiddenInput = $('#address-hidden-input');

    if (addressListArea.length === 0) return; // Nếu không có modal thì bỏ qua

    function renderList(items, isProvince = true) {
        addressListArea.empty();
        if (items.length === 0) {
            addressListArea.append('<div class="list-group-item text-muted text-center small">Không tìm thấy kết quả</div>');
            return;
        }

        items.forEach(item => {
            const el = $(`<button type="button" class="list-group-item list-group-item-action">${item}</button>`);
            el.click(function(e) {
                e.preventDefault(); e.stopPropagation();
                if (isProvince) {
                    selectProvince(item);
                } else {
                    selectDistrict(item);
                }
            });
            addressListArea.append(el);
        });
    }

    function showProvinces() {
        currentProvince = "";
        searchBox.val('').attr('placeholder', 'Tìm Tỉnh/Thành...');
        districtHeader.addClass('d-none');
        renderList(Object.keys(locationData), true);
    }

    function selectProvince(provinceName) {
        currentProvince = provinceName;
        searchBox.val('').attr('placeholder', 'Tìm Quận/Huyện...');
        searchBox.focus();
        districtHeader.removeClass('d-none');
        selectedProvinceDisplay.text(provinceName);
        renderList(locationData[provinceName], false);
    }

    function selectDistrict(districtName) {
        const fullAddress = `${districtName}, ${currentProvince}`;
        addressLabel.text(fullAddress).removeClass('text-muted').addClass('fw-bold');
        hiddenInput.val(fullAddress);
        $('#err-address').hide();
        const bsDropdown = bootstrap.Dropdown.getInstance(document.getElementById('addressDropdownBtn'));
        if (bsDropdown) bsDropdown.hide();
    }

    searchBox.on('input', function() {
        const keyword = $(this).val().toLowerCase();
        let sourceArray = (currentProvince === "") ? Object.keys(locationData) : locationData[currentProvince];
        const filtered = sourceArray.filter(item => item.toLowerCase().includes(keyword));
        renderList(filtered, currentProvince === "");
    });

    $('#btn-back-province').click(function(e) {
        e.preventDefault(); e.stopPropagation();
        showProvinces();
    });

    showProvinces();
}

// === LOGIC 2: NAVBAR SEARCH (Tìm kiếm thông minh: Huyện trước, Tỉnh sau) ===
function setupNavbarLocation() {
    const navBtn = $('#navLocationBtn');
    const navSearch = $('#nav-search-box');
    const navList = $('#nav-location-list');
    const navInput = $('#nav-input-diachi');

    if(navBtn.length === 0) return;

    // 1. Tạo danh sách phẳng để tìm kiếm (Flatten Data)
    // Cấu trúc: { name: "Tên hiển thị", type: "district/province", value: "Giá trị form" }
    let flatData = [];

    // Thêm các Tỉnh
    Object.keys(locationData).forEach(prov => {
        flatData.push({ name: prov, type: 'province', value: prov });
        
        // Thêm các Huyện thuộc Tỉnh đó
        locationData[prov].forEach(dist => {
            flatData.push({ 
                name: `${dist}, ${prov}`, 
                type: 'district', 
                value: `${dist}, ${prov}` 
            });
        });
    });

    // Hàm render Navbar List
    function renderNavList(items) {
        navList.empty();
        if (items.length === 0) {
            navList.append('<div class="list-group-item text-muted small">Không tìm thấy địa điểm</div>');
            return;
        }

        items.forEach(item => {
            // Icon khác nhau cho Tỉnh và Huyện để dễ nhìn
            const icon = item.type === 'province' 
                ? '<i class="bi bi-geo-alt-fill text-danger me-2"></i>' 
                : '<i class="bi bi-geo-alt text-secondary me-2"></i>';
            
            const el = $(`<button type="button" class="list-group-item list-group-item-action small py-2 text-start">${icon}${item.name}</button>`);
            
            el.click(function(e) {
                e.preventDefault();
                // Cập nhật giá trị
                navInput.val(item.value);
                navBtn.text(item.name);
                // Reset search box
                navSearch.val('');
                renderNavList(flatData.filter(i => i.type === 'province')); // Reset về list tỉnh
            });
            navList.append(el);
        });
    }

    // Sự kiện tìm kiếm
    navSearch.on('input', function() {
        const keyword = $(this).val().trim().toLowerCase();

        if (keyword === '') {
            // Nếu rỗng: chỉ hiện danh sách Tỉnh (để gọn)
            renderNavList(flatData.filter(i => i.type === 'province'));
        } else {
            // Tìm trong toàn bộ dữ liệu (cả Tỉnh và Huyện)
            const matches = flatData.filter(i => i.name.toLowerCase().includes(keyword));

            // SẮP XẾP: Ưu tiên Huyện lên trước (type === 'district')
            matches.sort((a, b) => {
                if (a.type === b.type) return a.name.localeCompare(b.name);
                return a.type === 'district' ? -1 : 1; // District lên đầu
            });

            renderNavList(matches);
        }
    });

    // Mặc định ban đầu: Hiển thị danh sách Tỉnh
    renderNavList(flatData.filter(i => i.type === 'province'));
}