<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Travel Memory Map</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #1a73e8;
            --bg-color: #f3f4f6;
            --surface: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --safe-area-bottom: 20px;
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
            align-items: flex-start;
            min-height: 100vh;
        }

        /* Mobile Container Simulation for Desktop */
        .app-container {
            width: 100%;
            max-width: 414px; /* iPhone Max width */
            background-color: var(--surface);
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        /* --- 1. Map Area --- */
        .map-section {
            position: relative;
            height: 280px;
            background-image: url('https://images.unsplash.com/photo-1524661135-423995f22d0b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80');
            background-size: cover;
            background-position: center;
        }

        /* Profile Badge */
        .profile-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(37, 99, 235, 0.9);
            backdrop-filter: blur(8px);
            padding: 6px 16px 6px 6px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .profile-badge img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }
        .profile-info {
            display: flex;
            flex-direction: column;
        }
        .profile-name { font-weight: 700; font-size: 14px; line-height: 1.2; }
        .profile-level { font-size: 11px; opacity: 0.9; }

        /* Map UI Elements */
        .chat-map-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 42px;
            height: 42px;
            background: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-size: 20px;
            color: var(--text-main);
        }

        .map-marker {
            position: absolute;
            background: white;
            padding: 4px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        .map-marker img {
            width: 50px;
            height: 40px;
            border-radius: 4px;
            object-fit: cover;
        }
        .marker-1 { top: 80px; left: 130px; }
        .marker-2 { top: 110px; right: 50px; }
        .marker-3 { top: 170px; left: 60px; }

        .you-are-here {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            background: #2563eb;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
        }
        .you-are-here i { font-size: 16px; }

        /* --- 2. Main Content Area --- */
        .main-content {
            flex: 1;
            background: white;
            border-radius: 24px 24px 0 0;
            margin-top: -24px;
            position: relative;
            z-index: 10;
            padding: 0 20px 100px 20px; /* padding bottom for nav bar */
        }

        /* Tabs */
        .tabs {
            display: flex;
            justify-content: space-between;
            padding: 20px 10px;
            border-bottom: 1px solid var(--border-color);
        }
        .tab-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
            position: relative;
        }
        .tab-item.active {
            color: var(--primary);
        }
        .tab-item i { font-size: 20px; }
        .tab-item.active::after {
            content: '';
            position: absolute;
            bottom: -21px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px 3px 0 0;
        }

        /* AI Section */
        .ai-section {
            background: #f8fafc;
            border-radius: 16px;
            margin-top: 20px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }
        .ai-header {
            background: var(--primary);
            color: white;
            padding: 12px 16px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .ai-body {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .user-chat {
            background: white;
            border: 1px solid var(--border-color);
            padding: 12px 16px;
            border-radius: 20px;
            font-size: 14px;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .ai-avatar {
            width: 32px;
            height: 32px;
            background: #3b82f6;
            color: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: bold;
            font-size: 14px;
            flex-shrink: 0;
        }
        .ai-response {
            background: white;
            border: 1px solid var(--border-color);
            padding: 16px;
            border-radius: 16px;
            font-size: 14px;
            color: var(--text-main);
            line-height: 1.5;
            text-align: center;
        }
        .ai-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            margin-top: 12px;
            cursor: pointer;
        }

        /* Album Section */
        .album-section {
            margin-top: 24px;
        }
        .album-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }
        .album-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-main);
        }
        .album-link {
            font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
        }
        
        .album-card {
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 16px;
            background: white;
        }
        .album-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }
        .album-card-title {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 15px;
            color: var(--primary);
        }
        .album-card-date {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
            margin-left: 28px;
        }
        
        /* Scrollable Photos */
        .photo-scroll {
            display: flex;
            gap: 12px;
            overflow-x: auto;
            padding-bottom: 8px;
            /* Hide scrollbar */
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .photo-scroll::-webkit-scrollbar { display: none; }
        
        .photo-item {
            min-width: 110px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .photo-item img {
            width: 110px;
            height: 110px;
            border-radius: 12px;
            object-fit: cover;
        }
        .photo-caption {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-main);
            text-align: center;
        }

        /* --- 3. Bottom Navigation --- */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            max-width: 414px;
            background: white;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 24px calc(12px + var(--safe-area-bottom));
            z-index: 100;
            border-radius: 24px 24px 0 0;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
        }
        .nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 500;
            text-decoration: none;
        }
        .nav-item.active {
            color: var(--text-main);
        }
        .nav-item i { font-size: 22px; }
        
        /* Center Camera Button */
        .nav-item-camera {
            width: 56px;
            height: 56px;
            background: #1e293b;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 24px;
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(30, 41, 59, 0.3);
        }
    </style>
</head>
<body>

<div class="app-container">
    
    <!-- 1. Map Section -->
    <div class="map-section">
        <!-- Profile Badge -->
        <div class="profile-badge">
            <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Avatar">
            <div class="profile-info">
                <span class="profile-name">aa12</span>
                <span class="profile-level">Explorer Lv.1</span>
            </div>
        </div>

        <!-- Chat Button -->
        <div class="chat-map-btn">
            <i class="bi bi-chat-text"></i>
        </div>

        <!-- Map Markers -->
        <div class="map-marker marker-1">
            <img src="https://images.unsplash.com/photo-1555400038-63f5ba517a47?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Photo 1">
        </div>
        <div class="map-marker marker-2">
            <img src="https://images.unsplash.com/photo-1506744626753-1fa44df14c28?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Photo 2">
        </div>
        <div class="map-marker marker-3">
            <img src="https://images.unsplash.com/photo-1517021897933-0e0319cfbc28?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" alt="Photo 3">
        </div>

        <!-- You Are Here -->
        <div class="you-are-here">
            <i class="bi bi-geo-alt-fill"></i>
            You Are Here
        </div>
    </div>

    <!-- 2. Main Content -->
    <div class="main-content">
        <!-- Tabs -->
        <div class="tabs">
            <div class="tab-item active">
                <i class="bi bi-signpost-2" style="color: #3b82f6;"></i>
                Lịch trình
            </div>
            <div class="tab-item">
                <i class="bi bi-images" style="color: #8b5cf6;"></i>
                Ảnh
            </div>
            <div class="tab-item">
                <i class="bi bi-journal-album" style="color: #f59e0b;"></i>
                Album
            </div>
            <div class="tab-item">
                <i class="bi bi-people-fill" style="color: #3b82f6;"></i>
                Bạn Bè
            </div>
        </div>

        <!-- AI Chat Section -->
        <div class="ai-section">
            <div class="ai-header">
                <i class="bi bi-robot"></i>
                Travel Memory AI
            </div>
            <div class="ai-body">
                <div class="user-chat">
                    <div class="ai-avatar">Ai</div>
                    <span>Quán ăn ngon gần đây có gì ngon vậy?</span>
                </div>
                
                <div class="ai-response">
                    Gợi ý đây! Hãy thử bún chả Hương Liên —<br>một quán ngon nổi tiếng gần đây!
                    <button class="ai-btn">Đề xuất địa điểm</button>
                </div>
            </div>
        </div>

        <!-- Album Section -->
        <div class="album-section">
            <div class="album-header">
                <div class="album-title">Album Hành Trình Của Bạn</div>
                <a href="#" class="album-link">Xem thêm</a>
            </div>

            <div class="album-card">
                <div class="album-card-header">
                    <div>
                        <div class="album-card-title">
                            <i class="bi bi-folder-fill"></i>
                            Chuyến Đi Tam Đảo 🌟
                        </div>
                        <div class="album-card-date">22/05/2026 - 24/05/2026</div>
                    </div>
                    <i class="bi bi-three-dots" style="color: var(--text-muted);"></i>
                </div>

                <div class="photo-scroll">
                    <div class="photo-item">
                        <img src="https://images.unsplash.com/photo-1596711677353-9184df46399e?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Tam Đảo">
                        <div class="photo-caption">Tam Đảo</div>
                    </div>
                    <div class="photo-item">
                        <img src="https://images.unsplash.com/photo-1621251919597-2a543599a80e?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Đèo đá mây">
                        <div class="photo-caption">Đèo đá mây</div>
                    </div>
                    <div class="photo-item">
                        <img src="https://images.unsplash.com/photo-1559925393-8be0ec4767c8?ixlib=rb-4.0.3&auto=format&fit=crop&w=200&q=80" alt="Quán cafe view đẹp">
                        <div class="photo-caption">Quán cafe view đẹp</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- 3. Bottom Navigation -->
    <div class="bottom-nav">
        <a href="#" class="nav-item active">
            <i class="bi bi-house-door-fill"></i>
            Home
        </a>
        <a href="#" class="nav-item">
            <i class="bi bi-geo-alt-fill"></i>
            Map
        </a>
        <a href="#" class="nav-item-camera">
            <i class="bi bi-camera-fill"></i>
        </a>
        <a href="#" class="nav-item">
            <i class="bi bi-images"></i>
            Album
        </a>
        <a href="#" class="nav-item">
            <i class="bi bi-people-fill"></i>
            Friends
        </a>
    </div>

</div>

</body>
</html>
