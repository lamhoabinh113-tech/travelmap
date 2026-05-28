<?php
// Nếu đã đăng nhập → chuyển thẳng sang Dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php?url=location/dashboard");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>Travel Memory Map – Lưu Giữ Hành Trình Của Bạn</title>
    <meta name="description" content="Ứng dụng lưu giữ ký ức hành trình, đánh dấu địa điểm, chia sẻ album ảnh cùng bạn bè trên bản đồ.">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --indigo: #4f46e5;
            --cyan: #06b6d4;
            --rose: #f43f5e;
            --amber: #f59e0b;
            --dark: #0f172a;
            --text: #1e293b;
            --muted: #64748b;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0f172a;
            color: white;
            overflow-x: hidden;
        }

        /* ─── ANIMATED BACKGROUND ─── */
        .bg-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            animation: drift 18s ease-in-out infinite alternate;
        }
        .orb-1 { width: 600px; height: 600px; background: rgba(79,70,229,0.35); top: -150px; left: -150px; animation-delay: 0s; }
        .orb-2 { width: 500px; height: 500px; background: rgba(6,182,212,0.25); bottom: -100px; right: -100px; animation-delay: -6s; }
        .orb-3 { width: 350px; height: 350px; background: rgba(244,63,94,0.2); top: 40%; left: 40%; animation-delay: -12s; }
        @keyframes drift {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(60px, 40px) scale(1.15); }
        }

        /* Floating particles */
        .particles { position: fixed; inset: 0; z-index: 0; pointer-events: none; }
        .particle {
            position: absolute;
            width: 4px; height: 4px;
            background: rgba(255,255,255,0.5);
            border-radius: 50%;
            animation: floatUp linear infinite;
        }
        @keyframes floatUp {
            0%   { transform: translateY(0) scale(1); opacity: 0.6; }
            100% { transform: translateY(-100vh) scale(0.3); opacity: 0; }
        }

        /* ─── NAVBAR ─── */
        nav {
            position: fixed; top: 0; left: 0; width: 100%; z-index: 100;
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 60px;
            background: rgba(15,23,42,0.6);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .nav-logo {
            display: flex; align-items: center; gap: 12px;
            font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 800;
            color: white; text-decoration: none;
        }
        .nav-logo .logo-icon {
            width: 42px; height: 42px;
            background: linear-gradient(135deg, var(--indigo), var(--cyan));
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            box-shadow: 0 8px 20px rgba(79,70,229,0.4);
        }
        .nav-links { display: flex; gap: 12px; align-items: center; }
        .btn-ghost {
            padding: 10px 22px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.15);
            color: rgba(255,255,255,0.85); font-size: 14px; font-weight: 500;
            cursor: pointer; text-decoration: none;
            transition: all 0.2s;
            background: transparent;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.3); color: white; }
        .btn-primary-sm {
            padding: 10px 22px; border-radius: 12px; border: none;
            background: linear-gradient(135deg, var(--indigo), var(--cyan));
            color: white; font-size: 14px; font-weight: 600;
            cursor: pointer; text-decoration: none;
            transition: all 0.25s;
            box-shadow: 0 4px 16px rgba(79,70,229,0.35);
        }
        .btn-primary-sm:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(79,70,229,0.5); color: white; }

        /* ─── HERO ─── */
        .hero {
            position: relative; z-index: 1;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 120px 30px 60px;
            text-align: center;
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 18px; border-radius: 999px;
            background: rgba(79,70,229,0.2);
            border: 1px solid rgba(79,70,229,0.4);
            font-size: 13px; font-weight: 600; color: #a5b4fc;
            margin-bottom: 28px;
        }
        .hero-badge .dot { width: 8px; height: 8px; border-radius: 50%; background: #a5b4fc; animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:0.4;} }

        .hero h1 {
            font-family: 'Outfit', sans-serif;
            font-size: clamp(40px, 8vw, 80px);
            font-weight: 900;
            line-height: 1.08;
            letter-spacing: -2px;
            margin-bottom: 24px;
        }
        .hero h1 .gradient-text {
            background: linear-gradient(135deg, #a5b4fc 0%, #67e8f9 50%, #fb7185 100%);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-desc {
            font-size: 18px; color: rgba(255,255,255,0.65); max-width: 560px; margin: 0 auto 44px;
            line-height: 1.75;
        }
        .hero-actions {
            display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;
            margin-bottom: 60px;
        }
        .btn-hero {
            padding: 16px 36px; border-radius: 16px; font-size: 16px; font-weight: 700;
            cursor: pointer; text-decoration: none;
            transition: all 0.3s;
            display: inline-flex; align-items: center; gap: 10px;
        }
        .btn-hero-primary {
            background: linear-gradient(135deg, var(--indigo), var(--cyan));
            color: white; border: none;
            box-shadow: 0 12px 36px rgba(79,70,229,0.4);
        }
        .btn-hero-primary:hover { transform: translateY(-3px); box-shadow: 0 20px 48px rgba(79,70,229,0.55); color: white; }
        .btn-hero-outline {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.2);
            color: white;
            backdrop-filter: blur(8px);
        }
        .btn-hero-outline:hover { background: rgba(255,255,255,0.12); transform: translateY(-3px); color: white; }

        /* Hero stats */
        .hero-stats {
            display: flex; justify-content: center; gap: 48px; flex-wrap: wrap;
        }
        .stat-item { text-align: center; }
        .stat-item .num {
            font-family: 'Outfit', sans-serif; font-size: 32px; font-weight: 800;
            background: linear-gradient(135deg, #a5b4fc, #67e8f9);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .stat-item .label { font-size: 12px; color: rgba(255,255,255,0.5); margin-top: 4px; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; }

        /* ─── PHONE MOCKUP ─── */
        .mockup-section {
            position: relative; z-index: 1;
            padding: 60px 30px;
            display: flex; justify-content: center;
        }
        .phone-mockup {
            width: 280px; height: 560px;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border-radius: 44px;
            border: 3px solid rgba(255,255,255,0.1);
            box-shadow: 0 40px 100px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.08);
            overflow: hidden;
            position: relative;
            animation: floatPhone 6s ease-in-out infinite;
        }
        @keyframes floatPhone {
            0%,100% { transform: translateY(0) rotate(-2deg); }
            50%      { transform: translateY(-20px) rotate(1deg); }
        }
        .phone-notch {
            position: absolute; top: 0; left: 50%; transform: translateX(-50%);
            width: 100px; height: 28px; background: #0f172a; border-radius: 0 0 18px 18px; z-index: 10;
        }
        .phone-screen { width: 100%; height: 100%; overflow: hidden; }
        .phone-map {
            width: 100%; height: 55%;
            background: linear-gradient(135deg, #1a3a5c 0%, #0e4d5c 50%, #1a2a4a 100%);
            position: relative; overflow: hidden;
        }
        /* Fake map grid lines */
        .phone-map::before {
            content: ''; position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(255,255,255,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.04) 1px, transparent 1px);
            background-size: 30px 30px;
        }
        .map-pin {
            position: absolute; width: 32px; height: 32px;
            background: linear-gradient(135deg, var(--indigo), var(--cyan));
            border-radius: 50% 50% 50% 0; transform: rotate(-45deg);
            box-shadow: 0 4px 12px rgba(79,70,229,0.6);
        }
        .map-pin-1 { top: 40%; left: 30%; }
        .map-pin-2 { top: 25%; left: 60%; background: linear-gradient(135deg, var(--rose), #fb923c); }
        .map-pin-3 { top: 55%; left: 55%; background: linear-gradient(135deg, #10b981, #06b6d4); }
        .phone-ui {
            padding: 14px;
            background: linear-gradient(180deg, rgba(15,23,42,0.95) 0%, #0f172a 100%);
            height: 45%;
        }
        .phone-tabs {
            display: flex; gap: 8px; margin-bottom: 12px;
        }
        .phone-tab {
            flex: 1; padding: 6px; border-radius: 10px; font-size: 9px; font-weight: 700;
            text-align: center; color: rgba(255,255,255,0.4); background: rgba(255,255,255,0.05);
        }
        .phone-tab.active { background: linear-gradient(135deg, var(--indigo), var(--cyan)); color: white; }
        .phone-card {
            border-radius: 12px; padding: 10px;
            background: rgba(255,255,255,0.05);
            display: flex; align-items: center; gap: 10px; margin-bottom: 8px;
        }
        .phone-card-img { width: 40px; height: 40px; border-radius: 10px; overflow: hidden; flex-shrink: 0; }
        .phone-card-img img { width: 100%; height: 100%; object-fit: cover; }
        .phone-card-text { flex: 1; }
        .phone-card-title { font-size: 10px; font-weight: 700; color: white; margin-bottom: 3px; }
        .phone-card-sub { font-size: 9px; color: rgba(255,255,255,0.4); }

        /* ─── FEATURES ─── */
        .section {
            position: relative; z-index: 1;
            padding: 100px 30px;
        }
        .section-label {
            text-align: center;
            font-size: 12px; font-weight: 700; color: #a5b4fc;
            text-transform: uppercase; letter-spacing: 3px; margin-bottom: 16px;
        }
        .section-title {
            font-family: 'Outfit', sans-serif; font-size: clamp(28px, 5vw, 48px);
            font-weight: 800; text-align: center; margin-bottom: 16px; line-height: 1.2;
        }
        .section-desc { text-align: center; color: rgba(255,255,255,0.55); font-size: 16px; max-width: 500px; margin: 0 auto 64px; line-height: 1.75; }

        .features-grid {
            display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px; max-width: 1100px; margin: 0 auto;
        }
        .feature-card {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px; padding: 32px;
            transition: all 0.35s ease;
            position: relative; overflow: hidden;
        }
        .feature-card::before {
            content: ''; position: absolute; inset: 0; border-radius: 24px;
            background: linear-gradient(135deg, rgba(79,70,229,0.08), rgba(6,182,212,0.05));
            opacity: 0; transition: opacity 0.35s;
        }
        .feature-card:hover { transform: translateY(-8px); border-color: rgba(79,70,229,0.3); }
        .feature-card:hover::before { opacity: 1; }
        .feature-icon {
            width: 56px; height: 56px; border-radius: 16px; margin-bottom: 24px;
            display: flex; align-items: center; justify-content: center; font-size: 24px;
        }
        .feature-title { font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 700; margin-bottom: 12px; }
        .feature-desc { color: rgba(255,255,255,0.55); font-size: 14px; line-height: 1.75; }

        /* ─── HOW IT WORKS ─── */
        .steps {
            display: flex; flex-direction: column; gap: 80px;
            max-width: 900px; margin: 0 auto;
        }
        .step {
            display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center;
        }
        .step:nth-child(even) .step-content { order: 2; }
        .step:nth-child(even) .step-visual { order: 1; }
        .step-number {
            font-family: 'Outfit', sans-serif;
            font-size: 80px; font-weight: 900;
            line-height: 1;
            background: linear-gradient(135deg, rgba(79,70,229,0.3), rgba(6,182,212,0.2));
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; margin-bottom: 16px;
        }
        .step-title { font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 700; margin-bottom: 12px; }
        .step-desc { color: rgba(255,255,255,0.6); font-size: 15px; line-height: 1.8; }
        .step-visual {
            height: 260px; border-radius: 24px; overflow: hidden;
            background: linear-gradient(135deg, rgba(79,70,229,0.15), rgba(6,182,212,0.1));
            border: 1px solid rgba(255,255,255,0.08);
            display: flex; align-items: center; justify-content: center;
            font-size: 80px;
            position: relative;
        }
        .step-visual::after {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(79,70,229,0.05), rgba(6,182,212,0.05));
        }

        /* ─── CTA ─── */
        .cta-section {
            position: relative; z-index: 1;
            padding: 100px 30px;
            text-align: center;
        }
        .cta-card {
            max-width: 700px; margin: 0 auto;
            background: linear-gradient(135deg, rgba(79,70,229,0.2), rgba(6,182,212,0.15));
            border: 1px solid rgba(79,70,229,0.3);
            border-radius: 36px; padding: 64px 48px;
            position: relative; overflow: hidden;
        }
        .cta-card::before {
            content: ''; position: absolute; top: -100px; left: 50%; transform: translateX(-50%);
            width: 400px; height: 400px; border-radius: 50%;
            background: radial-gradient(circle, rgba(79,70,229,0.2) 0%, transparent 70%);
        }
        .cta-title { font-family: 'Outfit', sans-serif; font-size: clamp(28px, 5vw, 44px); font-weight: 800; margin-bottom: 16px; }
        .cta-desc { color: rgba(255,255,255,0.65); font-size: 16px; margin-bottom: 40px; }

        /* ─── FOOTER ─── */
        footer {
            position: relative; z-index: 1;
            padding: 40px 60px;
            border-top: 1px solid rgba(255,255,255,0.06);
            display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;
        }
        footer p { color: rgba(255,255,255,0.4); font-size: 13px; }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 768px) {
            nav { padding: 16px 20px; }
            .step { grid-template-columns: 1fr; gap: 30px; }
            .step:nth-child(even) .step-content { order: 0; }
            .step:nth-child(even) .step-visual { order: 0; }
            footer { padding: 30px 20px; justify-content: center; text-align: center; }
        }
    </style>
</head>
<body>

<!-- Animated Background -->
<div class="bg-canvas">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
</div>

<!-- Particles -->
<div class="particles" id="particles"></div>

<!-- Navbar -->
<nav>
    <a href="#" class="nav-logo">
        <div class="logo-icon"><i class="bi bi-geo-alt-fill"></i></div>
        TravelMap
    </a>
    <div class="nav-links">
        <a href="#features" class="btn-ghost">Tính năng</a>
        <a href="index.php?url=auth/login" class="btn-ghost">Đăng nhập</a>
        <a href="index.php?url=auth/register" class="btn-primary-sm">Bắt đầu miễn phí →</a>
    </div>
</nav>

<!-- Hero Section -->
<section class="hero">
    <div>
        <div class="hero-badge">
            <span class="dot"></span>
            Ứng dụng bản đồ ký ức hành trình #1
        </div>
        <h1>
            Mỗi chuyến đi<br>
            là một <span class="gradient-text">ký ức</span><br>
            không thể nào quên
        </h1>
        <p class="hero-desc">
            Đánh dấu từng địa điểm, lưu giữ từng khoảnh khắc, chia sẻ album ảnh cùng bạn bè — tất cả trên một bản đồ sống động.
        </p>
        <div class="hero-actions">
            <a href="index.php?url=auth/register" class="btn-hero btn-hero-primary">
                <i class="bi bi-rocket-takeoff-fill"></i> Khám phá ngay
            </a>
            <a href="index.php?url=auth/login" class="btn-hero btn-hero-outline">
                <i class="bi bi-box-arrow-in-right"></i> Đăng nhập
            </a>
        </div>
        <div class="hero-stats">
            <div class="stat-item"><div class="num">500+</div><div class="label">Địa điểm lưu trữ</div></div>
            <div class="stat-item"><div class="num">1.2K</div><div class="label">Bức ảnh ký ức</div></div>
            <div class="stat-item"><div class="num">100%</div><div class="label">Miễn phí</div></div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="section" id="features">
    <div class="section-label">Tính năng nổi bật</div>
    <h2 class="section-title">Tất cả những gì bạn cần cho<br>một chuyến đi đáng nhớ</h2>
    <p class="section-desc">Từ bản đồ thực tế đến album ảnh và kết nối bạn bè — Travel Memory Map có đủ cả.</p>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon" style="background: linear-gradient(135deg,rgba(79,70,229,0.3),rgba(6,182,212,0.2));">🗺️</div>
            <div class="feature-title">Bản đồ tương tác</div>
            <div class="feature-desc">Đánh dấu địa điểm bạn đã đặt chân, xem lại hành trình trên bản đồ sống động với ảnh thu nhỏ tại từng pin vị trí.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background: linear-gradient(135deg,rgba(244,63,94,0.3),rgba(251,146,60,0.2));">📸</div>
            <div class="feature-title">Album ảnh Locket</div>
            <div class="feature-desc">Chụp ảnh ngay trong ứng dụng với camera locket, tự động gắn vị trí GPS và lưu vào album ký ức của bạn.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background: linear-gradient(135deg,rgba(16,185,129,0.3),rgba(6,182,212,0.2));">👥</div>
            <div class="feature-title">Kết nối bạn bè</div>
            <div class="feature-desc">Theo dõi hành trình của bạn bè, xem album ảnh của họ, thả cảm xúc và nhắn tin trực tiếp — ngay trên bản đồ.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background: linear-gradient(135deg,rgba(139,92,246,0.3),rgba(79,70,229,0.2));">🤖</div>
            <div class="feature-title">AI Du lịch thông minh</div>
            <div class="feature-desc">Hỏi AI để gợi ý địa điểm hay, lập lịch trình tối ưu, tìm ẩm thực địa phương — tất cả được tùy chỉnh theo sở thích của bạn.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background: linear-gradient(135deg,rgba(245,158,11,0.3),rgba(244,63,94,0.2));">📊</div>
            <div class="feature-title">Thống kê hành trình</div>
            <div class="feature-desc">Xem tổng số địa điểm đã ghé thăm, số bức ảnh đã chụp, quãng đường đã đi — và nhận huy hiệu Explorer độc đáo.</div>
        </div>
        <div class="feature-card">
            <div class="feature-icon" style="background: linear-gradient(135deg,rgba(6,182,212,0.3),rgba(16,185,129,0.2));">📅</div>
            <div class="feature-title">Dòng thời gian</div>
            <div class="feature-desc">Lịch trình tất cả các chuyến đi được hiển thị theo dòng thời gian đẹp mắt, dễ dàng tìm lại ký ức theo ngày tháng.</div>
        </div>
    </div>
</section>

<!-- How It Works -->
<section class="section">
    <div class="section-label">Cách hoạt động</div>
    <h2 class="section-title">Chỉ 3 bước đơn giản</h2>
    <div class="steps">
        <div class="step">
            <div class="step-content">
                <div class="step-number">01</div>
                <div class="step-title">Đăng ký & Đăng nhập</div>
                <div class="step-desc">Tạo tài khoản miễn phí chỉ trong 30 giây. Không cần thẻ tín dụng, không phí ẩn. Đăng nhập và bắt đầu ngay hành trình số của bạn.</div>
            </div>
            <div class="step-visual">🚀</div>
        </div>
        <div class="step">
            <div class="step-visual">📍</div>
            <div class="step-content">
                <div class="step-number">02</div>
                <div class="step-title">Đánh dấu & Chụp ảnh</div>
                <div class="step-desc">Khi đến một địa điểm mới, mở app và bấm ghim bản đồ. Chụp ảnh ngay lúc đó — GPS sẽ tự động gắn vào kỷ niệm của bạn.</div>
            </div>
        </div>
        <div class="step">
            <div class="step-content">
                <div class="step-number">03</div>
                <div class="step-title">Chia sẻ & Kết nối</div>
                <div class="step-desc">Kết bạn và chia sẻ album hành trình. Cùng bình luận, thả cảm xúc và tạo nên những ký ức chung không thể quên.</div>
            </div>
            <div class="step-visual">❤️</div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="cta-card">
        <h2 class="cta-title">Sẵn sàng bắt đầu<br>hành trình của bạn?</h2>
        <p class="cta-desc">Tham gia cùng hàng ngàn Explorer đang lưu giữ kỷ niệm trên Travel Memory Map.</p>
        <div style="display:flex;gap:16px;justify-content:center;flex-wrap:wrap;">
            <a href="index.php?url=auth/register" class="btn-hero btn-hero-primary">
                <i class="bi bi-person-plus-fill"></i> Đăng ký ngay — Miễn phí
            </a>
            <a href="index.php?url=auth/login" class="btn-hero btn-hero-outline">
                Đã có tài khoản? Đăng nhập
            </a>
        </div>
    </div>
</section>

<!-- Footer -->
<footer>
    <div class="nav-logo">
        <div class="logo-icon" style="width:32px;height:32px;font-size:15px;"><i class="bi bi-geo-alt-fill"></i></div>
        TravelMap
    </div>
    <p>© 2025 Travel Memory Map. Làm với ❤️ để lưu giữ mọi hành trình.</p>
    <p style="font-size:12px;color:rgba(255,255,255,0.3);">v2.0 · Made in Vietnam 🇻🇳</p>
</footer>

<script>
// Generate floating particles
const container = document.getElementById('particles');
for (let i = 0; i < 50; i++) {
    const p = document.createElement('div');
    p.className = 'particle';
    p.style.left = Math.random() * 100 + '%';
    p.style.top = Math.random() * 100 + '%';
    p.style.width = p.style.height = (Math.random() * 3 + 1) + 'px';
    p.style.animationDuration = (Math.random() * 20 + 10) + 's';
    p.style.animationDelay = (Math.random() * 10) + 's';
    p.style.opacity = Math.random() * 0.6;
    container.appendChild(p);
}

// Smooth scroll for nav links
document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
        const id = a.getAttribute('href').slice(1);
        const el = document.getElementById(id);
        if (el) { e.preventDefault(); el.scrollIntoView({ behavior: 'smooth' }); }
    });
});
</script>

</body>
</html>
