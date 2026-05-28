<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Đăng nhập - Travel Memory Map</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #4f46e5;
            --secondary: #0ea5e9;
            --surface: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: #e2e8f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .app-container {
            width: 100%;
            max-width: 414px; /* Mobile width */
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            min-height: 100vh;
            position: relative;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .login-header {
            padding: 60px 30px 30px;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        /* Decorative background blobs */
        .blob-1 {
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 50%;
            filter: blur(40px);
            opacity: 0.3;
            z-index: 1;
        }
        
        .blob-2 {
            position: absolute;
            bottom: -50px;
            left: -50px;
            width: 250px;
            height: 250px;
            background: linear-gradient(135deg, #f43f5e, #fb923c);
            border-radius: 50%;
            filter: blur(50px);
            opacity: 0.2;
            z-index: 1;
        }

        .logo-box {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 24px;
            box-shadow: 0 15px 30px rgba(79, 70, 229, 0.3);
            transform: rotate(-10deg);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(-10deg); }
            50% { transform: translateY(-10px) rotate(-5deg); }
        }

        .title {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
        }

        .subtitle {
            font-size: 14px;
            color: var(--text-muted);
            margin-bottom: 40px;
        }

        .login-form {
            padding: 0 30px;
            position: relative;
            z-index: 10;
            flex: 1;
        }

        .input-group {
            margin-bottom: 24px;
            position: relative;
        }

        .input-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            color: #94a3b8;
            font-size: 18px;
        }

        .input-field {
            width: 100%;
            padding: 16px 16px 16px 45px;
            border-radius: 16px;
            border: 2px solid transparent;
            background: #f1f5f9;
            font-size: 15px;
            color: var(--text-main);
            transition: all 0.3s ease;
            outline: none;
        }

        .input-field:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.25);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px rgba(79, 70, 229, 0.35);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }

        .footer-links {
            text-align: center;
            margin-top: 30px;
            padding-bottom: 30px;
        }

        .footer-links p {
            font-size: 14px;
            color: var(--text-muted);
        }

        .footer-links a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
        }

        .alert-error {
            background: #fee2e2;
            color: #ef4444;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #fecaca;
        }
    </style>
</head>
<body>

    <div class="app-container">
        <div class="blob-1"></div>
        <div class="blob-2"></div>
        
        <div class="login-header">
            <div class="logo-box">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <h1 class="title">Travel Memory</h1>
            <p class="subtitle">Lưu giữ mọi khoảnh khắc hành trình</p>
        </div>

        <div class="login-form">
            <?php if(!empty($error)): ?>
                <div class="alert-error">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form action="index.php?url=auth/login" method="POST">
                <div class="input-group">
                    <label>Tên đăng nhập</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person"></i>
                        <input type="text" name="username" class="input-field" placeholder="Nhập username của bạn" required>
                    </div>
                </div>

                <div class="input-group">
                    <label>Mật khẩu</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="password" class="input-field" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    Bắt đầu hành trình <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div class="footer-links">
                <p>Chưa có tài khoản? <a href="index.php?url=auth/register">Đăng ký ngay</a></p>
                <p style="margin-top: 15px;"><a href="#" style="color:var(--text-muted); font-size:12px;"><i class="bi bi-shield-lock"></i> Đăng nhập Admin</a></p>
            </div>
        </div>
    </div>

</body>
</html>
