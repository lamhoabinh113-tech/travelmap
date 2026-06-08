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
            --primary: #6366f1;
            --secondary: #0ea5e9;
            --surface: rgba(255, 255, 255, 0.08);
            --border: rgba(255, 255, 255, 0.12);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            min-height: 100vh;
            background: radial-gradient(circle at top right, #1e1b4b 0%, #0f172a 60%, #020617 100%);
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .app-container {
            width: 100%;
            max-width: 420px;
            background: var(--surface);
            border: 1px solid var(--border);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-radius: 32px;
            position: relative;
            box-shadow: 0 30px 70px rgba(0,0,0,0.5);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 40px 24px;
        }

        .login-header {
            text-align: center;
            position: relative;
            z-index: 10;
            margin-bottom: 30px;
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
            filter: blur(50px);
            opacity: 0.35;
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
            filter: blur(60px);
            opacity: 0.25;
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
            margin: 0 auto 20px;
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.4);
            transform: rotate(-10deg);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(-10deg); }
            50% { transform: translateY(-8px) rotate(-5deg); }
        }

        .title {
            font-family: 'Outfit', sans-serif;
            font-size: 30px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            font-size: 14px;
            color: var(--text-muted);
        }

        .login-form {
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
            font-size: 11px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
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
            border: 1px solid rgba(255,255,255,0.08);
            background: rgba(255, 255, 255, 0.05);
            font-size: 15px;
            color: var(--text-main);
            transition: all 0.3s ease;
            outline: none;
        }

        .input-field:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 24px rgba(99, 102, 241, 0.35);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px rgba(99, 102, 241, 0.45);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }

        .footer-links {
            text-align: center;
            margin-top: 30px;
        }

        .footer-links p {
            font-size: 14px;
            color: var(--text-muted);
        }

        .footer-links a {
            color: var(--secondary);
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }
        .footer-links a:hover {
            color: var(--primary);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(239, 68, 68, 0.2);
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

            <form action="index.php?url=auth/login" method="POST" autocomplete="off" id="loginForm">
                <div class="input-group">
                    <label>Tên đăng nhập</label>
                    <div class="input-wrapper">
                        <i class="bi bi-person"></i>
                        <input type="text" name="login_username" id="username_field" class="input-field" placeholder="Nhập username của bạn" required autocomplete="username">
                    </div>
                </div>

                <div class="input-group">
                    <label>Mật khẩu</label>
                    <div class="input-wrapper">
                        <i class="bi bi-lock"></i>
                        <input type="password" name="login_password" id="password_field" class="input-field" placeholder="••••••••" required autocomplete="current-password">
                    </div>
                </div>

                <button type="submit" class="btn-login">
                    Bắt đầu hành trình <i class="bi bi-arrow-right"></i>
                </button>
            </form>

            <div class="footer-links">
                <p>Chưa có tài khoản? <a href="index.php?url=auth/register">Đăng ký ngay</a></p>
                <p style="margin-top: 15px;"><a href="index.php?url=auth/login" style="color:var(--text-muted); font-size:12px;"><i class="bi bi-shield-lock"></i> Làm mới trang</a></p>
            </div>
        </div>
    </div>
</body>
</html>
