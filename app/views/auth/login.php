<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Travel Memory Map</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 30px;
            animation: fadeIn 0.8s ease;
        }
        .logo-box {
            width: 60px;
            height: 60px;
            background: var(--primary);
            color: white;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3);
        }
    </style>
</head>
<body>
    <div class="login-card glass">
        <div class="text-center mb-4">
            <div class="logo-box">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <h3 class="fw-bold text-white">Chào Mừng Trở Lại</h3>
            <p class="text-white opacity-75">Tiếp tục hành trình của bạn</p>
        </div>
        
        <div class="card-body">
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger border-0 rounded-4 mb-4" style="background: rgba(220, 53, 69, 0.2); color: #ff8e9a;">
                    <i class="bi bi-exclamation-circle me-2"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="index.php?url=auth/login" method="POST">
                <div class="mb-3">
                    <label class="form-label text-white small fw-semibold">TÊN ĐĂNG NHẬP</label>
                    <input type="text" name="username" class="form-control form-control-premium" placeholder="Nhập username" required>
                </div>
                <div class="mb-4">
                    <label class="form-label text-white small fw-semibold">MẬT KHẨU</label>
                    <input type="password" name="password" class="form-control form-control-premium" placeholder="********" required>
                </div>
                <button type="submit" class="btn btn-premium w-100 py-3 mb-3">Đăng Nhập <i class="bi bi-arrow-right ms-2"></i></button>
                <div class="text-center">
                    <span class="text-white opacity-75">Chưa có tài khoản? <a href="index.php?url=auth/register" class="text-white fw-bold text-decoration-none">Đăng ký ngay</a></span>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
