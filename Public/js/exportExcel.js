/**
 * public/js/exportExcel.js
 * Cách mới: Xuất bảng dưới dạng HTML Table.
 * Excel sẽ tự động hiểu các thẻ <tr> <td> và chia cột chuẩn 100% trên mọi máy.
 */
function exportToExcel() {
    // 1. Lấy bảng gốc
    var table = document.getElementById("reportTable");
    if (!table) {
        alert("Không tìm thấy dữ liệu để xuất!");
        return;
    }

    // 2. Tạo nội dung HTML cho file Excel
    // Thêm các meta tag này để Excel nhận diện Font chữ UTF-8 (Tiếng Việt)
    var excelHTML = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="utf-8">
            <style>
                table { border-collapse: collapse; width: 100%; }
                td, th { border: 1px solid #000000; padding: 5px; text-align: left; vertical-align: middle; }
                th { background-color: #f0f0f0; font-weight: bold; }
                .text-danger { color: red; } 
            </style>
        </head>
        <body>
            <table>
    `;

    // 3. Duyệt qua các dòng của bảng gốc để xây dựng bảng mới (Bỏ cột Hành động)
    var rows = table.rows;

    for (var i = 0; i < rows.length; i++) {
        excelHTML += "<tr>";
        
        var cols = rows[i].querySelectorAll("td, th");
        
        // Duyệt qua các cột (TRỪ CỘT CUỐI CÙNG - Hành động)
        for (var j = 0; j < cols.length - 1; j++) {
            // Lấy nội dung HTML bên trong (để giữ màu sắc nếu cần) hoặc innerText
            // Ở đây dùng innerText để lấy dữ liệu thô sạch sẽ
            var data = cols[j].innerText.trim();
            
            // Nếu là cột tiêu đề (hàng 0) thì dùng <th>, ngược lại dùng <td>
            var tag = (i === 0) ? "th" : "td";
            
            // Thêm style format text cho đẹp (ép về dạng text để tránh lỗi số 0 đầu)
            // mso-number-format:"\@" là ép kiểu Text trong Excel
            excelHTML += `<${tag} style='mso-number-format:"\\@"'>${data}</${tag}>`;
        }
        
        excelHTML += "</tr>";
    }

    excelHTML += `
            </table>
        </body>
        </html>
    `;

    // 4. Tạo file Blob
    var blob = new Blob([excelHTML], { type: "application/vnd.ms-excel" });
    
    // 5. Tải file về
    var link = document.createElement("a");
    var url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    
    var date = new Date().toISOString().slice(0,10);
    link.setAttribute("download", "Danh_Sach_Bao_Cao_" + date + ".xls");
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}