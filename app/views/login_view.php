<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Chợ Tốt Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* (Giữ nguyên CSS cũ) */
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: system-ui, -apple-system, sans-serif; }
        .login-container { background: white; border-radius: 20px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); overflow: hidden; max-width: 450px; width: 100%; }
        .login-header { background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); color: white; padding: 30px; text-align: center; }
        .login-header h2 { margin: 0; font-weight: 700; font-size: 1.8rem; }
        .login-header p { margin: 10px 0 0 0; opacity: 0.9; font-size: 0.95rem; }
        .login-body { padding: 40px; }
        .form-group { margin-bottom: 25px; }
        .form-label { font-weight: 600; color: #334155; margin-bottom: 8px; display: flex; align-items: center; gap: 8px; }
        .form-control { border: 2px solid #e2e8f0; border-radius: 10px; padding: 12px 15px; font-size: 1rem; transition: all 0.3s ease; }
        .form-control:focus { border-color: #f59e0b; box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.25); }
        .btn-login { background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%); border: none; border-radius: 10px; padding: 12px; font-weight: 600; font-size: 1.1rem; color: white; width: 100%; transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(245, 158, 11, 0.3); color: white; }
        .alert { border-radius: 10px; border: none; }
        .back-link { text-align: center; margin-top: 20px; }
        .back-link a { color: #64748b; text-decoration: none; font-size: 0.95rem; transition: color 0.3s ease; }
        .back-link a:hover { color: #f59e0b; }
        .input-icon { position: relative; }
        .input-icon i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #94a3b8; }
        .input-icon .form-control { padding-left: 45px; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2><i class="bi bi-box-arrow-in-right"></i> Đăng nhập</h2>
            <p>Chào mừng bạn trở lại!</p>
        </div>
        <div class="login-body">
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/baitaplon/Login/processLogin">
                <div class="form-group">
                    <label for="username" class="form-label">
                        <i class="bi bi-person"></i> Tên đăng nhập
                    </label>
                    <div class="input-icon">
                        <i class="bi bi-person-fill"></i>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="username" 
                            name="username" 
                            placeholder="Nhập tên đăng nhập"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock"></i> Mật khẩu
                    </label>
                    <div class="input-icon">
                        <i class="bi bi-lock-fill"></i>
                        <input 
                            type="password" 
                            class="form-control" 
                            id="password" 
                            name="password" 
                            placeholder="Nhập mật khẩu"
                            required
                        >
                    </div>
                </div>

                <button type="submit" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
                </button>
            </form>

            <div class="back-link">
                <a href="/baitaplon/Home">
                    <i class="bi bi-arrow-left"></i> Quay lại trang chủ
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>