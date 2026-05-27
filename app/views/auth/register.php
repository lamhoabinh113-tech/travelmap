<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Travel Memory Map</title>
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
            background: linear-gradient(135deg, #10b981 0%, #3b82f6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-card {
            width: 100%;
            max-width: 500px;
            padding: 40px;
            border-radius: 30px;
            animation: fadeIn 0.8s ease;
        }
    </style>
</head>
<body>
    <div class="register-card glass">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-white">Bắt Đầu Hành Trình</h3>
            <p class="text-white opacity-75">Tạo tài khoản để lưu giữ kỷ niệm</p>
        </div>
        
        <div class="card-body">
            <?php if(!empty($error)): ?>
                <div class="alert alert-danger border-0 rounded-4 mb-4" style="background: rgba(220, 53, 69, 0.2); color: #ff8e9a;">
                    <i class="bi bi-exclamation-circle me-2"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="index.php?url=auth/register" method="POST">
                <div class="mb-3">
                    <label class="form-label text-white small fw-semibold">HỌ VÀ TÊN</label>
                    <input type="text" name="full_name" class="form-control form-control-premium" placeholder="Nguyễn Văn A" required>
                </div>
                
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-white small fw-semibold">USERNAME</label>
                        <input type="text" name="username" class="form-control form-control-premium" placeholder="user123" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-white small fw-semibold">EMAIL</label>
                        <input type="email" name="email" class="form-control form-control-premium" placeholder="email@example.com" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label text-white small fw-semibold">MẬT KHẨU</label>
                    <input type="password" name="password" class="form-control form-control-premium" placeholder="********" required>
                </div>

                <button type="submit" class="btn btn-premium w-100 py-3 mb-3" style="background: linear-gradient(135deg, #10b981, #059669);">
                    Tạo Tài Khoản <i class="bi bi-person-plus-fill ms-2"></i>
                </button>
                
                <div class="text-center">
                    <span class="text-white opacity-75">Đã có tài khoản? <a href="index.php?url=auth/login" class="text-white fw-bold text-decoration-none">Đăng nhập ngay</a></span>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
