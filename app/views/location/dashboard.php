<!DOCTYPE html>
<html lang="vi">
<head>
    <script>
        // Global error catcher to display JS errors directly on screen for debugging
        window.onerror = function(message, source, lineno, colno, error) {
            const errorDiv = document.createElement('div');
            errorDiv.style.position = 'fixed';
            errorDiv.style.top = '10px';
            errorDiv.style.left = '10px';
            errorDiv.style.right = '10px';
            errorDiv.style.background = 'rgba(239, 68, 68, 0.95)';
            errorDiv.style.color = 'white';
            errorDiv.style.padding = '15px';
            errorDiv.style.borderRadius = '10px';
            errorDiv.style.zIndex = '999999';
            errorDiv.style.fontSize = '12px';
            errorDiv.style.fontFamily = 'monospace';
            errorDiv.style.whiteSpace = 'pre-wrap';
            errorDiv.innerHTML = `<strong>JS Error:</strong> ${message}<br>at ${source}:${lineno}:${colno}`;
            document.body.appendChild(errorDiv);
            return false;
        };
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hành Trình Của Bạn - Travel Memory Map</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS Dependencies (Sử dụng cdnjs cho ổn định) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css?v=3.7">
    <style>
        /* Force display for AI Chat Window */
        .ai-chat-window.active, .ai-chat-window.open { display: flex !important; z-index: 5000 !important; }
        
        /* Force styling for Leaflet Custom Markers */
        .custom-map-marker {
            position: relative !important;
            width: 48px !important;
            height: 48px !important;
            border-radius: 50% !important;
            background: white !important;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.16) !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
            overflow: visible !important;
        }
        .custom-map-marker:hover {
            transform: scale(1.2) translateY(-6px) !important;
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.24) !important;
        }
        .custom-marker-img {
            width: 100% !important;
            height: 100% !important;
            border-radius: 50% !important;
            object-fit: cover !important;
            display: block !important;
        }
        .custom-marker-feeling-badge {
            position: absolute !important;
            bottom: -2px !important;
            right: -2px !important;
            background: white !important;
            border-radius: 50% !important;
            width: 20px !important;
            height: 20px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 11px !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
            border: 1px solid rgba(0, 0, 0, 0.05) !important;
            z-index: 10 !important;
        }
        .custom-marker-user-badge {
            position: absolute !important;
            top: -2px !important;
            left: -2px !important;
            width: 18px !important;
            height: 18px !important;
            border-radius: 50% !important;
            border: 1.5px solid white !important;
            object-fit: cover !important;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15) !important;
            z-index: 10 !important;
        }
        .custom-marker-pulse {
            position: absolute !important;
            inset: -6px !important;
            border-radius: 50% !important;
            border: 2px solid #6366f1 !important;
            opacity: 0 !important;
            pointer-events: none !important;
            animation: customMarkerPulse 2.5s infinite !important;
        }
        @keyframes customMarkerPulse {
            0% { transform: scale(0.9); opacity: 0.6; }
            100% { transform: scale(1.4); opacity: 0; }
        }
        /* Locket Mini Widget CSS */
        .locket-mini-widget {
            background: rgba(255, 255, 255, 0.45) !important;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.08) !important;
        }
        .btn-circle {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        /* Locket Card Stack CSS */
        .locket-stack-container {
            perspective: 800px;
            position: relative;
            padding-bottom: 10px;
        }
        .locket-stack-card {
            background: #ffffff !important;
            border: 6px solid #ffffff !important;
            border-bottom: 26px solid #ffffff !important;
            border-radius: 12px !important;
            box-shadow: 0 8px 20px rgba(0,0,0,0.12) !important;
            transform-origin: bottom center !important;
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.2) !important;
        }
        /* Dynamic symmetrical fan-out hover effect based on current stack DOM order */
        .locket-stack-container:hover .locket-stack-card:nth-child(1) {
            transform: translateY(-8px) rotate(0deg) scale(1.02) !important;
            z-index: 10 !important;
        }
        .locket-stack-container:hover .locket-stack-card:nth-child(2) {
            transform: translateX(65px) translateY(-5px) rotate(10deg) scale(0.98) !important;
            z-index: 9 !important;
        }
        .locket-stack-container:hover .locket-stack-card:nth-child(3) {
            transform: translateX(-65px) translateY(-5px) rotate(-10deg) scale(0.98) !important;
            z-index: 8 !important;
        }
        .locket-stack-container:hover .locket-stack-card:nth-child(4) {
            transform: translateX(120px) translateY(5px) rotate(20deg) scale(0.94) !important;
            z-index: 7 !important;
        }
        .locket-stack-card img {
            width: 100% !important;
            height: 100% !important;
            object-fit: contain !important;
            background-color: #0f172a !important;
            border-radius: 6px !important;
        }
        
        /* Floating Emoji CSS */
        .floating-emoji {
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            opacity: 1;
            transform: translate(-50%, -50%);
            animation: emojiFloatUp 1.2s cubic-bezier(0.075, 0.82, 0.165, 1) forwards;
        }
        @keyframes emojiFloatUp {
            0% {
                transform: translate(-50%, -50%) scale(0.3) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
                transform: translate(-50%, -100%) scale(1) rotate(0deg);
            }
            100% {
                transform: translate(calc(-50% + var(--drift)), -250px) scale(0.8) rotate(var(--rotate));
                opacity: 0;
            }
        }
        
        /* Leaflet Marker Ripple CSS */
        .leaflet-marker-ripple {
            width: 40px;
            height: 40px;
            margin-left: -20px;
            margin-top: -20px;
            border-radius: 50%;
            border: 3px solid #6366f1;
            background: rgba(99, 102, 241, 0.2);
            animation: rippleWave 1s cubic-bezier(0.1, 0.8, 0.3, 1) forwards;
            pointer-events: none;
        }
        @keyframes rippleWave {
            0% {
                transform: scale(0.2);
                opacity: 1;
            }
            100% {
                transform: scale(3.5);
                opacity: 0;
            }
        }
        .hover-bg-light:hover {
            background-color: rgba(0, 0, 0, 0.05) !important;
        }
        .cursor-pointer {
            cursor: pointer;
        }
        .avatar-group {
            display: flex;
            align-items: center;
        }
        .avatar-item {
            transition: transform 0.2s ease, z-index 0.2s ease;
        }
        .avatar-item:hover {
            transform: translateY(-4px) scale(1.1);
            z-index: 10;
        }
        .text-premium-gradient {
            background: linear-gradient(45deg, #6366f1, #22d3ee);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        /* Map Filter Dropdown Panel */
        .map-filter-dropdown {
            position: absolute;
            right: 70px;
            top: 16px;
            width: 220px;
            background: rgba(255, 255, 255, 0.45) !important;
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(255, 255, 255, 0.25) !important;
            border-radius: 20px;
            padding: 16px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12) !important;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.25);
            transform: translateX(15px) scale(0.9);
            opacity: 0;
            pointer-events: none;
        }
        .map-filter-dropdown.show {
            transform: translateX(0) scale(1);
            opacity: 1;
            pointer-events: auto;
        }
        /* Mobile adjustment to match map-mode-pill media query */
        @media (max-width: 768px) {
            .map-filter-dropdown {
                top: 24px;
                right: 76px;
            }
        }
        
        /* Achievements Badges CSS */
        .badge-grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(78px, 1fr));
            gap: 12px;
            justify-content: center;
        }
        .achievement-badge-item {
            background: rgba(255, 255, 255, 0.45);
            border: 1px solid rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(5px);
            border-radius: 12px;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            position: relative;
        }
        .achievement-badge-item.locked {
            opacity: 0.55;
            background: rgba(0, 0, 0, 0.05);
            filter: grayscale(100%);
        }
        .achievement-badge-item.unlocked {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(34, 211, 238, 0.1));
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
        }
        .achievement-badge-item:hover {
            transform: translateY(-4px) scale(1.05);
        }
        .achievement-badge-item.unlocked:hover {
            animation: bounceShake 0.5s ease-in-out;
        }
        @keyframes bounceShake {
            0%, 100% { transform: translateY(-4px) rotate(0deg); }
            20% { transform: translateY(-6px) rotate(-3deg); }
            40% { transform: translateY(-2px) rotate(3deg); }
            60% { transform: translateY(-5px) rotate(-1.5deg); }
            80% { transform: translateY(-3px) rotate(1.5deg); }
        }
        .badge-locked-overlay {
            position: absolute;
            top: 4px;
            right: 4px;
            font-size: 10px;
            color: #94a3b8;
        }
    </style>
    
    <!-- PWA Support -->
    <link rel="manifest" href="manifest.json">
    <script>
        // Disable and clear service workers/caches to prevent frozen pages and layout bugs
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.getRegistrations().then(function(registrations) {
                for (let registration of registrations) {
                    registration.unregister();
                }
            });
        }
        if ('caches' in window) {
            caches.keys().then(function(names) {
                for (let name of names) {
                    caches.delete(name);
                }
            });
        }
    </script>
    
    <!-- Script forcing HTTPS removed to allow local network IP access -->
    
    
    <link rel="stylesheet" href="css/dashboard_mobile.css?v=3.7">
</head>
<body>
<?php
    $journey_count = count($locations ?? []);
    $photo_count = 0;
    $place_names = [];
    $top_place = 'Chưa có dữ liệu';
    $total_distance = 0;
    $previous_point = null;
    $mood_counts = [];

    foreach (($locations ?? []) as $journey) {
        if (!empty($journey['image'])) {
            $photo_count++;
        }

        $place = trim($journey['place_name'] ?? '');
        if ($place !== '') {
            $place_names[] = $place;
        }

        $mood = trim($journey['feeling'] ?? 'Khac');
        $mood_counts[$mood] = ($mood_counts[$mood] ?? 0) + 1;

        if ($previous_point && isset($journey['latitude'], $journey['longitude'])) {
            $earth_radius = 6371;
            $lat1 = deg2rad((float)$previous_point['latitude']);
            $lon1 = deg2rad((float)$previous_point['longitude']);
            $lat2 = deg2rad((float)$journey['latitude']);
            $lon2 = deg2rad((float)$journey['longitude']);
            $dlat = $lat2 - $lat1;
            $dlon = $lon2 - $lon1;
            $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
            $total_distance += 2 * $earth_radius * asin(min(1, sqrt($a)));
        }
        $previous_point = $journey;
    }

    if (!empty($place_names)) {
        $place_frequency = array_count_values($place_names);
        arsort($place_frequency);
        $top_place = array_key_first($place_frequency);
    }

    arsort($mood_counts);
    $dominant_mood = array_key_first($mood_counts) ?: 'Dang cho ky uc moi';
    $explorer_level = max(1, min(9, (int)ceil($journey_count / 5)));

    // =========================================================
    // Helper function: render ảnh/video nhất quán toàn app
    // $context: 'card' (timeline/friends) | 'album' (album grid)
    // =========================================================
    function renderMedia(string $filename, int $height = 160, string $context = 'card'): string {
        if (empty($filename)) {
            // Placeholder không ảnh — dùng class gốc của CSS
            return '<div class="memory-img-placeholder"><i class="bi bi-camera"></i></div>';
        }
        $url   = UPLOADS_URL . '/' . htmlspecialchars($filename);
        $ext   = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $isVid = in_array($ext, ['mp4', 'webm', 'ogg', 'mov']);
        $mime  = $ext === 'mov' ? 'mp4' : $ext;

        if ($context === 'album') {
            // Album grid: img/video tự fill theo CSS .album-cell img
            if ($isVid) {
                return '<video muted preload="none"><source src="' . $url . '" type="video/' . $mime . '"></video>'
                     . '<div class="play-icon"><i class="bi bi-play-circle-fill"></i></div>';
            }
            return '<img src="' . $url . '" alt="" loading="lazy">';
        }

        // Timeline / friend card: dùng class .memory-img gốc (aspect-ratio 4/3 từ CSS)
        if ($isVid) {
            return '<video class="memory-img" muted preload="none">'
                 . '<source src="' . $url . '" type="video/' . $mime . '">'
                 . '</video>'
                 . '<div class="album-badge" style="bottom:50%;right:50%;transform:translate(50%,50%);font-size:24px;background:rgba(0,0,0,.35);color:#fff;padding:12px;border-radius:50%;">'
                 . '<i class="bi bi-play-circle-fill"></i></div>';
        }
        return '<img src="' . $url . '" class="memory-img" alt="" loading="lazy"'
             . ' onerror="this.style.display=\'none\'">';
    }

    function renderReactionBtn($id, $is_liked, $like_count, $reaction_type) {
        $icon = '<i class="bi bi-heart"></i>';
        $color = '#64748b';
        if ($is_liked) {
            $type = $reaction_type ?: 'heart';
            if ($type === 'like') { $icon = '👍'; $color = '#3b5998'; }
            elseif ($type === 'heart') { $icon = '<i class="bi bi-heart-fill"></i>'; $color = '#ef4444'; }
            elseif ($type === 'haha') { $icon = '😂'; $color = '#f59e0b'; }
            elseif ($type === 'wow') { $icon = '😮'; $color = '#f59e0b'; }
            elseif ($type === 'sad') { $icon = '😢'; $color = '#f59e0b'; }
        }

        return '
        <div class="reaction-container" onclick="event.stopPropagation()">
            <button class="btn btn-sm btn-light rounded-pill py-0 px-2 reaction-btn" style="font-size:12px; color: '.$color.';" onclick="toggleReactionMenu(this)">
                <span class="r-icon">'.$icon.'</span>
                <span class="like-count ms-1">'.$like_count.'</span>
            </button>
            <div class="reaction-popup">
                <span class="reaction-icon" onclick="toggleLike('.$id.', \'like\', this.closest(\'.reaction-container\').querySelector(\'.reaction-btn\'))">👍</span>
                <span class="reaction-icon" onclick="toggleLike('.$id.', \'heart\', this.closest(\'.reaction-container\').querySelector(\'.reaction-btn\'))">❤️</span>
                <span class="reaction-icon" onclick="toggleLike('.$id.', \'haha\', this.closest(\'.reaction-container\').querySelector(\'.reaction-btn\'))">😂</span>
                <span class="reaction-icon" onclick="toggleLike('.$id.', \'wow\', this.closest(\'.reaction-container\').querySelector(\'.reaction-btn\'))">😮</span>
                <span class="reaction-icon" onclick="toggleLike('.$id.', \'sad\', this.closest(\'.reaction-container\').querySelector(\'.reaction-btn\'))">😢</span>
            </div>
        </div>';
    }
?>


<div class="app-container">
    
    <!-- 1. Map Section -->
    <div class="map-section">
        <div id="map"></div>
        
        <!-- (Old Map Filter Bar Removed) -->
        
        <!-- Profile Badge -->
        <div class="profile-badge" data-bs-toggle="modal" data-bs-target="#profileModal" style="cursor: pointer;">
            <div class="avatar-placeholder" style="width: 36px; height: 36px; border-radius: 50%; overflow: hidden; border: 2px solid white; display: flex; align-items: center; justify-content: center; background: #ddd; color: #555;">
                <?php if (!empty($_SESSION['avatar'])): ?>
                    <img src="<?php echo htmlspecialchars($_SESSION['avatar']); ?>" alt="Avatar" style="width:100%; height:100%; object-fit: cover;">
                <?php else: ?>
                    <img src="https://cdn-icons-png.flaticon.com/512/4140/4140044.png" alt="AI icon" style="width:100%; height:100%; object-fit: cover;">
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <span class="profile-name"><?php echo htmlspecialchars(!empty($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['username']); ?></span>
                <span class="profile-level"><?php echo isset($badge_name) ? $badge_name : 'Explorer Lv.1'; ?> • <?php echo isset($user_xp) ? $user_xp : 0; ?> XP</span>
            </div>
        </div>

        <form id="avatarUploadForm" action="index.php?url=location/uploadAvatar" method="POST" enctype="multipart/form-data" style="display:none;">
            <input type="file" id="avatarInput" name="avatar_file" accept="image/*" onchange="submitAvatarForm()">
        </form>

        <!-- Chat Button (Floating over map) -->
        <div class="chat-map-btn" onclick="toggleAIChat()">
            <i class="bi bi-chat-text"></i>
        </div>

        <div class="map-mode-pill">
            <button type="button" class="active" id="darkMapBtn" onclick="setMapTheme('dark')" title="Bản đồ tối"><i class="bi bi-moon-stars-fill"></i></button>
            <button type="button" id="lightMapBtn" onclick="setMapTheme('light')" title="Bản đồ sáng"><i class="bi bi-brightness-high-fill"></i></button>
            <button type="button" id="satelliteMapBtn" onclick="setMapTheme('satellite')" title="Bản đồ vệ tinh"><i class="bi bi-globe"></i></button>
            <button type="button" id="mapFilterToggleBtn" onclick="toggleMapFilterPanel()" title="Bộ lọc bản đồ"><i class="bi bi-funnel-fill"></i></button>
            <button type="button" class="active" id="followLocationBtn" onclick="toggleFollowLocation()" title="Theo dõi vị trí thực tế"><i class="bi bi-crosshair"></i></button>
            <button type="button" onclick="refreshMyLocation()" title="Định vị lại (GPS chính xác)"><i class="bi bi-arrow-clockwise"></i></button>
        </div>

        <div id="mapFilterDropdown" class="map-filter-dropdown">
            <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom" style="border-color: rgba(0,0,0,0.08) !important;">
                <span class="small fw-bold text-premium-gradient"><i class="bi bi-funnel-fill"></i> Bộ Lọc Bản Đồ</span>
                <button type="button" class="btn-close" style="font-size: 8px; padding: 2px;" onclick="toggleMapFilterPanel()"></button>
            </div>
            <div class="mb-2">
                <label class="form-label text-muted mb-1" style="font-size: 10px; font-weight: 600;">CẢM XÚC</label>
                <select id="mapFeelingFilter" class="form-select form-select-sm rounded-pill" onchange="applyMapFilterDirect()" style="font-size: 11px; padding: 4px 12px; background-color: rgba(255,255,255,0.85);">
                    <option value="">-- Tất cả cảm xúc --</option>
                    <option value="Hạnh phúc">😊 Hạnh phúc</option>
                    <option value="Tuyệt vời">🤩 Tuyệt vời</option>
                    <option value="Bình yên">🧘 Bình yên</option>
                    <option value="Thú vị">🎈 Thú vị</option>
                    <option value="Nhớ nhung">🥺 Nhớ nhung</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted mb-1" style="font-size: 10px; font-weight: 600;">CHUYẾN ĐI</label>
                <select id="mapTripDropdownFilter" class="form-select form-select-sm rounded-pill" onchange="applyMapFilterDirect()" style="font-size: 11px; padding: 4px 12px; background-color: rgba(255,255,255,0.85);">
                    <option value="">-- Tất cả chuyến đi --</option>
                    <?php if(!empty($trips)): ?>
                        <?php foreach($trips as $t): ?>
                            <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['title']); ?></option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                </select>
            </div>
            <button type="button" class="btn btn-xs btn-light rounded-pill w-100 fw-bold border" onclick="resetAllMapFilters()" style="font-size: 10px; padding: 6px;">Đặt lại bộ lọc</button>
        </div>

        <div id="liveLocationHud" class="live-location-hud" style="display:none;">
            <i class="bi bi-geo-alt-fill text-danger"></i>
            <span id="liveLocationHudText">Đang định vị...</span>
        </div>
    </div>

    <!-- 2. Main Content -->
    <div class="main-content">
        <!-- Tabs -->


        <!-- TAB 1: Timeline -->
        <div id="tab-timeline" class="tab-content-section active">
            <!-- New from Friends -->
            <?php if(!isset($is_friend_view) && !empty($friend_locations)): ?>
                <div class="mb-4">
                    <h6 class="small fw-bold text-muted mb-3 d-flex align-items-center">
                        <i class="bi bi-people-fill me-2 text-primary"></i> MỚI TỪ BẠN BÈ
                    </h6>
                    <?php 
                    $top_friends = array_slice($friend_locations, 0, 3);
                    foreach($top_friends as $floc): 
                    ?>
                        <div class="memory-item" style="background:#f0f7ff;" onclick="focusMemory(<?php echo (int)$floc['id']; ?>)">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="avatar-placeholder" style="width:25px;height:25px;font-size:10px;border-radius:8px;">
                                    <?php echo strtoupper(substr($floc['username'], 0, 1)); ?>
                                </div>
                                <span class="small fw-bold text-primary"><?php echo htmlspecialchars($floc['full_name']); ?></span>
                                <small class="text-muted ms-auto" style="font-size:10px;"><?php echo date('d/m/Y', strtotime($floc['created_at'])); ?></small>
                            </div>
                            <div class="memory-img-wrapper">
                                <?= renderMedia($floc['image'], 130) ?>
                            </div>
                            <h6 class="fw-bold mb-1 small mt-2"><?php echo htmlspecialchars($floc['place_name']); ?></h6>
                            <p class="small text-muted mb-0 text-truncate" style="font-size:11px;"><?php echo htmlspecialchars($floc['description']); ?></p>
                            <div class="mt-2 d-flex justify-content-between align-items-center">
                                <?php echo renderReactionBtn($floc['id'], $floc['is_liked'], $floc['like_count'], $floc['reaction_type'] ?? null); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['trip_id']) && isset($current_trip)): ?>
                <!-- Trip Summary Banner -->
                <div class="mb-4 bg-primary text-white rounded-4 p-3 shadow-sm" style="background: linear-gradient(135deg, var(--primary), var(--secondary)) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="fw-bold mb-0 text-white"><i class="bi bi-geo-fill me-2"></i> <?php echo htmlspecialchars($current_trip['title']); ?></h5>
                        <a href="index.php?url=location/dashboard" class="btn btn-sm btn-light rounded-pill px-3" style="font-size:12px; font-weight:bold;">
                            <i class="bi bi-x-lg"></i> Xem tất cả
                        </a>
                    </div>
                    <p class="small mb-2" style="opacity:0.9;"><?php echo htmlspecialchars($current_trip['description']); ?></p>
                    <div class="d-flex gap-3 mt-3">
                        <div class="text-center bg-white bg-opacity-25 rounded-3 p-2 flex-fill">
                            <div class="small text-uppercase" style="font-size:10px; opacity:0.8;">Quãng đường</div>
                            <div class="fw-bold fs-6"><?php echo number_format($total_distance ?? 0, 1); ?> km</div>
                        </div>
                        <div class="text-center bg-white bg-opacity-25 rounded-3 p-2 flex-fill">
                            <div class="small text-uppercase" style="font-size:10px; opacity:0.8;">Điểm dừng</div>
                            <div class="fw-bold fs-6"><?php echo count($locations); ?> điểm</div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3 pt-3 border-top border-white border-opacity-25">
                        <button class="btn btn-sm btn-light rounded-pill flex-fill fw-bold text-primary" style="font-size:11px;" onclick="openCameraForTrip(<?php echo $current_trip['id']; ?>, <?php echo htmlspecialchars(json_encode($current_trip['title']), ENT_QUOTES, 'UTF-8'); ?>)">
                            <i class="bi bi-camera-fill me-1"></i> Chụp Locket
                        </button>
                        <button class="btn btn-sm btn-light rounded-pill flex-fill fw-bold text-success" style="font-size:11px;" onclick="addMemoryForTrip(<?php echo $current_trip['id']; ?>)">
                            <i class="bi bi-plus-lg me-1"></i> Thêm Check-in
                        </button>
                    </div>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                <h6 class="small fw-bold text-muted mb-0">
                    <i class="bi bi-signpost-split-fill me-2 text-primary"></i> 
                    DÒNG THỜI GIAN
                </h6>
                <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addMemoryModal">
                    <i class="bi bi-plus-lg"></i> Thêm mới
                </button>
            </div>

            <div class="journey-timeline">
                <?php if (!isset($is_friend_view)): ?>
                <!-- Locket Mini Widget -->
                <div class="locket-mini-widget p-3 rounded-4 border mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <!-- Circular camera widget preview -->
                        <div class="locket-preview-circle border border-3 border-primary shadow" style="width: 70px; height: 70px; border-radius: 50%; overflow: hidden; flex-shrink: 0; position: relative; background: #111; cursor: pointer;" onclick="toggleLocketWidgetCamera()">
                            <video id="widgetVideo" autoplay playsinline muted style="width: 100%; height: 100%; object-fit: cover; display: none; transform: scaleX(-1);"></video>
                            <canvas id="widgetCanvas" style="display: none;"></canvas>
                            <div id="widgetPlaceholder" class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white text-opacity-75">
                                <i class="bi bi-camera-fill fs-4 text-primary"></i>
                                <span style="font-size: 8px; font-weight: bold; text-transform: uppercase;">Locket</span>
                            </div>
                            <img id="widgetCapturedImg" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                        </div>
                        
                        <!-- Details -->
                        <div class="flex-grow-1 overflow-hidden" id="widgetControls">
                            <h6 class="fw-bold mb-1" style="font-size: 13px;">Chụp nhanh Locket!</h6>
                            <p class="text-muted mb-0 small text-truncate">Chạm ống kính tròn để bật camera</p>
                        </div>
                        
                        <!-- Panel control buttons -->
                        <div class="d-flex flex-column gap-2 align-items-end" id="widgetActionPanel" style="display: none !important;">
                            <button class="btn btn-sm btn-primary rounded-circle btn-circle" id="widgetSnapBtn" onclick="snapWidgetPhoto()" title="Chụp"><i class="bi bi-camera"></i></button>
                            <button class="btn btn-sm btn-success rounded-circle btn-circle" id="widgetPostBtn" onclick="postWidgetLocket()" style="display: none;" title="Đăng"><i class="bi bi-send-fill"></i></button>
                            <button class="btn btn-sm btn-danger rounded-circle btn-circle" id="widgetResetBtn" onclick="resetWidgetLocket()" style="display: none;" title="Chụp lại"><i class="bi bi-arrow-counterclockwise"></i></button>
                        </div>
                    </div>
                    
                    <!-- Caption & Trip choice overlay -->
                    <div class="mt-2" id="widgetCaptionArea" style="display: none;">
                        <div class="position-relative mb-2">
                            <input type="text" id="widgetCaptionInput" class="form-control form-control-sm rounded-pill text-center bg-black text-white" placeholder="Viết chú thích trên ảnh..." style="border: none; font-size: 12px; font-weight: bold; background: rgba(0,0,0,0.85) !important;">
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <select id="widgetTripId" class="form-select form-select-sm rounded-pill" style="font-size: 11px;">
                                <option value="">-- Check-in tự do --</option>
                                <?php if(!empty($trips)): ?>
                                    <?php foreach($trips as $t): ?>
                                        <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['title']); ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php
                // Group check-ins and prepare timeline items
                $timeline_items = [];
                $trips_by_id = [];
                if (!empty($trips)) {
                    foreach ($trips as $t) {
                        $trips_by_id[$t['id']] = $t;
                    }
                }

                // Group $locations by trip_id
                foreach (($locations ?? []) as $loc) {
                    if (!empty($loc['trip_id'])) {
                        $t_id = $loc['trip_id'];
                        if (!isset($timeline_items['trip_' . $t_id])) {
                            $trip_details = $trips_by_id[$t_id] ?? null;
                            if (!$trip_details) {
                                // Fetch trip details from database directly
                                $q_trip = "SELECT * FROM trips WHERE id = :tid";
                                $s_trip = $this->db->prepare($q_trip);
                                $s_trip->execute([':tid' => $t_id]);
                                $trip_details = $s_trip->fetch(PDO::FETCH_ASSOC);
                                if ($trip_details) {
                                    $trips_by_id[$t_id] = $trip_details;
                                }
                            }

                            if ($trip_details) {
                                $timeline_items['trip_' . $t_id] = [
                                    'type' => 'trip',
                                    'trip_id' => $t_id,
                                    'title' => $trip_details['title'],
                                    'description' => $trip_details['description'],
                                    'start_date' => $trip_details['start_date'],
                                    'end_date' => $trip_details['end_date'],
                                    'owner_id' => $trip_details['user_id'],
                                    'checkins' => [],
                                    'visit_date' => $loc['visit_date'],
                                ];
                            }
                        }
                        if (isset($timeline_items['trip_' . $t_id])) {
                            $timeline_items['trip_' . $t_id]['checkins'][] = $loc;
                            if (strtotime($loc['visit_date']) > strtotime($timeline_items['trip_' . $t_id]['visit_date'])) {
                                $timeline_items['trip_' . $t_id]['visit_date'] = $loc['visit_date'];
                            }
                        }
                    } else {
                        $timeline_items['loc_' . $loc['id']] = [
                            'type' => 'standalone',
                            'visit_date' => $loc['visit_date'],
                            'loc' => $loc
                        ];
                    }
                }

                // Add empty trips (created by user or where user is member) so they appear on timeline immediately
                foreach (($trips ?? []) as $t) {
                    if (!isset($timeline_items['trip_' . $t['id']])) {
                        $timeline_items['trip_' . $t['id']] = [
                            'type' => 'trip',
                            'trip_id' => $t['id'],
                            'title' => $t['title'],
                            'description' => $t['description'],
                            'start_date' => $t['start_date'],
                            'end_date' => $t['end_date'],
                            'owner_id' => $t['user_id'],
                            'checkins' => [],
                            'visit_date' => $t['start_date'] ?: $t['created_at'],
                        ];
                    }
                }

                if (!function_exists('getFeelingEmojiPhp')) {
                    function getFeelingEmojiPhp($feeling) {
                        $map = [
                            'Hạnh phúc' => '😊',
                            'Tuyệt vời' => '🤩',
                            'Bình yên' => '🧘',
                            'Thú vị' => '🎈',
                            'Nhớ nhung' => '🥺'
                        ];
                        return $map[$feeling] ?? '📍';
                    }
                }

                // Sort timeline items by visit_date DESC
                uasort($timeline_items, function($a, $b) {
                    return strtotime($b['visit_date']) - strtotime($a['visit_date']);
                });
                ?>

                <?php if(empty($timeline_items)): ?>
                    <div class="text-center py-5 opacity-50">
                        <i class="bi bi-geo-alt display-4"></i>
                        <p class="mt-2">Chưa có kỷ niệm nào.</p>
                    </div>
                <?php endif; ?>

                <?php foreach($timeline_items as $item): ?>
                    <?php if ($item['type'] === 'standalone'): ?>
                        <?php $loc = $item['loc']; ?>
                        <div class="memory-item" data-trip-id="<?php echo $loc['trip_id'] ?? 0; ?>" <?php if (!empty($loc['latitude']) && !empty($loc['longitude'])): ?>onclick="focusMap(<?php echo $loc['latitude']; ?>, <?php echo $loc['longitude']; ?>, true)"<?php endif; ?>>
                            <div class="memory-img-wrapper" onclick="event.stopPropagation(); openAlbum(<?php echo $loc['id']; ?>, <?php echo htmlspecialchars(json_encode($loc['place_name'] ?? 'Album'), ENT_QUOTES, 'UTF-8'); ?>)">
                                <?= renderMedia($loc['image'], 160) ?>
                                <?php if($loc['image']): ?>
                                    <?php $ext = strtolower(pathinfo($loc['image'], PATHINFO_EXTENSION)); ?>
                                    <div class="album-badge">
                                        <?php if(in_array($ext, ['mp4','webm','ogg','mov'])): ?>
                                            <i class="bi bi-film me-1"></i> Video
                                        <?php else: ?>
                                            <i class="bi bi-images me-1"></i> Album
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="badge bg-secondary-subtle text-secondary mb-1" style="font-size: 10px;"><i class="bi bi-geo-alt-fill"></i> Check-in đơn lẻ</span>
                                    <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($loc['place_name']); ?></h6>
                                </div>
                                <?php if(!isset($is_friend_view)): ?>
                                <div class="d-flex gap-2">
                                    <a href="javascript:void(0)" class="text-primary opacity-50" onclick="event.stopPropagation(); openEditModal(<?php echo htmlspecialchars(json_encode($loc), ENT_QUOTES, 'UTF-8'); ?>)"><i class="bi bi-pencil-square"></i></a>
                                    <a href="index.php?url=location/delete&id=<?php echo $loc['id']; ?>" class="text-danger opacity-50" onclick="event.stopPropagation(); return confirm('Xóa kỷ niệm này?')"><i class="bi bi-trash"></i></a>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="memory-meta-grid">
                                <span class="memory-chip"><i class="bi bi-calendar3 text-primary"></i> <?php echo date('d/m/Y', strtotime($loc['visit_date'])); ?></span>
                                <span class="memory-chip"><i class="bi bi-emoji-smile text-warning"></i> <?php echo getFeelingEmojiPhp($loc['feeling']) . ' ' . htmlspecialchars($loc['feeling']); ?></span>
                            </div>
                            <p class="small text-muted mb-2 text-truncate"><?php echo htmlspecialchars($loc['description']); ?></p>
                            <div class="d-flex justify-content-between align-items-center border-top pt-2 mt-2">
                                <?php echo renderReactionBtn($loc['id'], $loc['is_liked'], $loc['like_count'], $loc['reaction_type'] ?? null); ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- TRIP ALBUM CARD -->
                        <div class="trip-album-card p-3 bg-white rounded-4 shadow-sm border mb-4" data-trip-id="<?php echo $item['trip_id']; ?>">
                            <!-- Trip Header -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="overflow-hidden">
                                    <span class="badge bg-primary-subtle text-primary mb-1" style="font-size: 10px;"><i class="bi bi-briefcase-fill me-1"></i> Album chuyến đi</span>
                                    <h5 class="fw-bold mb-1 text-premium-gradient text-truncate"><?php echo htmlspecialchars($item['title']); ?></h5>
                                    <p class="small text-muted mb-0" style="font-size: 11px;"><i class="bi bi-calendar3 text-primary"></i> <?php 
                                        $start = $item['start_date'] ? date('d/m/Y', strtotime($item['start_date'])) : null;
                                        $end = $item['end_date'] ? date('d/m/Y', strtotime($item['end_date'])) : null;
                                        if ($start && $end) {
                                            echo $start . ' - ' . $end;
                                        } elseif ($start) {
                                            echo 'Từ ' . $start;
                                        } else {
                                            echo 'Chưa đặt ngày';
                                        }
                                    ?></p>
                                    <?php 
                                    $feelings = [];
                                    foreach ($item['checkins'] as $c) {
                                        if (!empty($c['feeling'])) {
                                            $feelings[] = $c['feeling'];
                                        }
                                    }
                                    $feelings = array_unique($feelings);
                                    if (!empty($feelings)):
                                    ?>
                                        <div class="d-flex flex-wrap gap-1 mt-2">
                                            <?php foreach ($feelings as $f): ?>
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-pill" style="font-size: 10px;">
                                                    <?php echo getFeelingEmojiPhp($f); ?> <?php echo htmlspecialchars($f); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Overlapping avatars of members -->
                                <div class="d-flex align-items-center flex-shrink-0">
                                    <div class="avatar-group me-1">
                                        <?php 
                                        $mems = $trip_members_data[$item['trip_id']] ?? [];
                                        $limit = 4;
                                        $count = 0;
                                        foreach ($mems as $m_uid => $m): 
                                            if ($count >= $limit) break;
                                            $count++;
                                            $avatar_url = $m['avatar'] ? UPLOADS_URL . '/avatars/' . $m['avatar'] : 'https://cdn-icons-png.flaticon.com/512/4140/4140044.png';
                                        ?>
                                            <div class="avatar-item" title="<?php echo htmlspecialchars($m['full_name']); ?> (@<?php echo htmlspecialchars($m['username']); ?>)" style="width: 26px; height: 26px; border-radius: 50%; overflow: hidden; border: 2px solid white; margin-left: -8px; background: #eee; position: relative;" data-bs-toggle="tooltip">
                                                <img src="<?php echo $avatar_url; ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://cdn-icons-png.flaticon.com/512/4140/4140044.png'">
                                            </div>
                                        <?php endforeach; ?>
                                        <?php if (count($mems) > $limit): ?>
                                            <div class="avatar-item-more d-flex align-items-center justify-content-center text-white bg-secondary small fw-bold" style="width: 26px; height: 26px; border-radius: 50%; border: 2px solid white; margin-left: -8px; font-size: 9px; line-height: 22px;">
                                                +<?php echo (count($mems) - $limit); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Trip settings / actions -->
                                    <?php if ($item['owner_id'] == $_SESSION['user_id'] && !isset($is_friend_view)): ?>
                                        <button class="btn btn-link text-muted p-1" onclick="event.stopPropagation(); openInviteModal(<?php echo $item['trip_id']; ?>)" title="Mời bạn bè">
                                            <i class="bi bi-person-plus-fill" style="font-size: 14px;"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- Trip Description -->
                            <?php if (!empty($item['description'])): ?>
                                <p class="small text-secondary mb-3 text-truncate"><?php echo htmlspecialchars($item['description']); ?></p>
                            <?php endif; ?>

                            <!-- Photos Tinder Stack -->
                            <?php 
                            $photos = $trip_photos_data[$item['trip_id']] ?? [];
                            $photo_count = count($photos);
                            if ($photo_count > 0): 
                            ?>
                                <div class="locket-stack-container mb-3 position-relative" style="height: 320px; margin-top: 15px; margin-bottom: 25px;" data-trip-id="<?php echo $item['trip_id']; ?>">
                                    <?php 
                                    foreach ($photos as $idx => $photo): 
                                        if ($idx >= 4) continue; // visually stack up to 4 cards
                                        $z_index = $photo_count - $idx;
                                        $offset_y = $idx * 6;
                                        $scale = 1 - ($idx * 0.04);
                                        $rot = ($idx % 2 == 0 ? 1 : -1) * min(3, $idx * 1.5);
                                        
                                        $photo_path = is_array($photo) ? $photo['image_path'] : $photo;
                                        $place_name = is_array($photo) ? $photo['place_name'] : '';
                                        $feeling = is_array($photo) ? $photo['feeling'] : '';
                                    ?>
                                        <div class="locket-stack-card position-absolute w-100 h-100 rounded-4 overflow-hidden shadow cursor-grab d-flex flex-column" 
                                             style="z-index: <?php echo $z_index; ?>; transform: translateY(<?php echo $offset_y; ?>px) scale(<?php echo $scale; ?>) rotate(<?php echo $rot; ?>deg); touch-action: none;"
                                             data-index="<?php echo $idx; ?>">
                                             <div class="position-relative flex-grow-1" style="background: #0f172a;">
                                                 <img src="<?php echo UPLOADS_URL . '/' . htmlspecialchars($photo_path); ?>" class="w-100 h-100 object-fit-contain rounded-2" style="pointer-events: none;">
                                                 <?php if (!empty($place_name)): ?>
                                                     <!-- Premium overlay for place name & feeling -->
                                                     <div class="position-absolute bottom-0 start-0 end-0 p-2 text-white d-flex align-items-center justify-content-between" style="background: linear-gradient(transparent, rgba(15, 23, 42, 0.95)); z-index: 2;">
                                                         <span class="small fw-bold text-truncate" style="max-width: 70%; font-size: 10px;">
                                                             <i class="bi bi-geo-alt-fill text-danger me-1"></i><?php echo htmlspecialchars($place_name); ?>
                                                         </span>
                                                         <?php if (!empty($feeling)): ?>
                                                             <span class="badge bg-dark bg-opacity-75 text-warning rounded-pill" style="font-size: 9px; padding: 3px 6px;">
                                                                 <?php echo getFeelingEmojiPhp($feeling); ?> <?php echo htmlspecialchars($feeling); ?>
                                                             </span>
                                                         <?php endif; ?>
                                                     </div>
                                                 <?php endif; ?>
                                             </div>
                                             <?php if ($idx == 0 && $photo_count > 1): ?>
                                                 <div class="position-absolute bg-black bg-opacity-75 text-white px-2 py-1 rounded-pill d-flex align-items-center gap-1" style="font-size: 9px; pointer-events: none; opacity: 0.85; bottom: -20px; left: 50%; transform: translateX(-50%); z-index: 5;">
                                                     <i class="bi bi-hand-index-thumb-fill text-warning"></i> Vuốt để lật ảnh
                                                 </div>
                                             <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="border border-dashed rounded-4 p-4 text-center text-muted mb-3 bg-light bg-opacity-50">
                                    <i class="bi bi-images display-6 mb-2 text-primary opacity-50"></i>
                                    <p class="small mb-0">Chưa có hình ảnh nào trong chuyến đi này.</p>
                                </div>
                            <?php endif; ?>

                            <!-- Check-ins inside the trip -->
                            <?php if (!empty($item['checkins'])): ?>
                                <div class="trip-checkins-list mt-3 border-top pt-3">
                                    <h6 class="small fw-bold text-muted mb-2">CÁC ĐIỂM DỪNG CHÂN (<?php echo count($item['checkins']); ?>)</h6>
                                    <div class="d-flex flex-column gap-2" style="max-height: 200px; overflow-y: auto; scrollbar-width: none;">
                                        <?php foreach ($item['checkins'] as $c): ?>
                                            <div class="p-2 border rounded-3 bg-light bg-opacity-50 d-flex align-items-center justify-content-between cursor-pointer hover-bg-light" <?php if (!empty($c['latitude']) && !empty($c['longitude'])): ?>onclick="focusMap(<?php echo $c['latitude']; ?>, <?php echo $c['longitude']; ?>, true)"<?php endif; ?>>
                                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                    <div class="avatar-placeholder" style="width: 22px; height: 22px; border-radius: 50%; font-size: 9px; display:flex; align-items:center; justify-content:center; background:#e0f2fe; color:#0d6efd; flex-shrink:0;">
                                                        <?php if ($c['user_avatar']): ?>
                                                            <img src="<?php echo UPLOADS_URL . '/avatars/' . $c['user_avatar']; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;" onerror="this.parentElement.innerHTML = '<?php echo strtoupper(substr($c['username'], 0, 1)); ?>'">
                                                        <?php else: ?>
                                                            <?php echo strtoupper(substr($c['username'], 0, 1)); ?>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="overflow-hidden">
                                                        <div class="fw-bold small text-truncate text-dark" style="font-size: 12px;"><?php echo htmlspecialchars($c['place_name']); ?></div>
                                                        <div class="text-muted text-truncate" style="font-size: 10px;">
                                                            <?php echo htmlspecialchars($c['full_name'] ?: $c['username']); ?> - <?php echo getFeelingEmojiPhp($c['feeling']); ?> <?php echo htmlspecialchars($c['feeling']); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2" onclick="event.stopPropagation()">
                                                    <?php if ($c['user_id'] == $_SESSION['user_id'] && !isset($is_friend_view)): ?>
                                                        <a href="javascript:void(0)" class="text-primary opacity-75 me-1" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($c), ENT_QUOTES, 'UTF-8'); ?>)" title="Sửa kỷ niệm">
                                                            <i class="bi bi-pencil-square" style="font-size:13px;"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php echo renderReactionBtn($c['id'], $c['is_liked'], $c['like_count'], $c['reaction_type'] ?? null); ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Trip Actions -->
                            <?php if (!isset($is_friend_view)): ?>
                            <div class="d-flex gap-2 mt-3 pt-2 border-top">
                                <button class="btn btn-sm btn-outline-primary rounded-pill flex-fill" style="font-size:11px;" onclick="event.stopPropagation(); openCameraForTrip(<?php echo $item['trip_id']; ?>, <?php echo htmlspecialchars(json_encode($item['title']), ENT_QUOTES, 'UTF-8'); ?>)">
                                    <i class="bi bi-camera-fill me-1"></i> Chụp Locket
                                </button>
                                <button class="btn btn-sm btn-outline-success rounded-pill flex-fill" style="font-size:11px;" onclick="event.stopPropagation(); addMemoryForTrip(<?php echo $item['trip_id']; ?>)">
                                    <i class="bi bi-plus-lg me-1"></i> Thêm Check-in
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- TAB 2: Bạn Bè -->
        <div id="tab-friends" class="tab-content-section">
            <h5 class="fw-bold mb-4">Bạn Bè & Kết nối</h5>
            <?php if(isset($_GET['friend_success'])): ?>
                <div class="alert alert-success border-0 rounded-4 py-2 small mb-3">Kết bạn thành công!</div>
            <?php endif; ?>
            <?php if(isset($_GET['friend_error'])): ?>
                <div class="alert alert-danger border-0 rounded-4 py-2 small mb-3">Lỗi kết bạn!</div>
            <?php endif; ?>
            
            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">MÃ KẾT BẠN CỦA BẠN</label>
                <div class="p-3 border rounded-4 text-center bg-light">
                    <div id="qrcode" class="mb-2 d-flex justify-content-center"></div>
                    <small class="text-primary fw-bold">Quét mã bằng điện thoại</small>
                </div>
            </div>

            <div class="mb-4">
                <button class="btn btn-light w-100 rounded-pill py-2 small fw-bold mb-2" onclick="copyInviteLink()">
                    <i class="bi bi-link-45deg me-1"></i> Sao chép link mời
                </button>
                <form action="index.php?url=friend/add" method="POST">
                    <div class="input-group">
                        <input type="text" name="invite_link" class="form-control form-control-sm rounded-start-pill" placeholder="Dán link bạn bè vào đây...">
                        <button class="btn btn-primary btn-sm rounded-end-pill px-3" type="submit">Thêm</button>
                    </div>
                </form>
            </div>

            <h6 class="small fw-bold text-muted mb-3">DANH SÁCH BẠN BÈ (<?php echo count($friends); ?>)</h6>
            <div class="friend-list">
                <?php if(empty($friends)): ?>
                    <div class="text-center py-4 opacity-50 small">Chưa có bạn bè nào.<br>Hãy mời bạn bè tham gia!</div>
                <?php else: ?>
                    <?php foreach($friends as $friend): ?>
                        <div class="friend-card">
                            <div class="avatar-placeholder" style="width: 35px; height: 35px; border-radius: 50%; font-size: 14px; display:flex; align-items:center; justify-content:center; background:#ccc;">
                                <?php echo strtoupper(substr($friend['username'], 0, 1)); ?>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-bold small text-truncate"><?php echo $friend['full_name']; ?></div>
                                <div class="text-muted" style="font-size: 11px;">@<?php echo $friend['username']; ?></div>
                            </div>
                            <a href="index.php?url=location/friend_map&id=<?php echo $friend['id']; ?>" class="btn btn-primary btn-sm rounded-circle p-1" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-geo-alt-fill" style="font-size: 12px;"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- TAB 3: Album -->
        <div id="tab-album" class="tab-content-section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">Tất cả ảnh &amp; Video</h5>
                <small class="text-muted"><?php echo $photo_count; ?> mục</small>
            </div>
            <div class="album-grid">
                <?php
                $has_images = false;
                foreach($locations as $loc) {
                    if(!empty($loc['image'])) {
                        $has_images = true;
                        echo '<div class="album-cell" onclick="openAlbum('.$loc['id'].', '.htmlspecialchars(json_encode($loc['place_name'] ?? 'Album'), ENT_QUOTES, 'UTF-8').')">';
                        echo renderMedia($loc['image'], 120, 'album');
                        echo '</div>';
                    }
                }
                if(!$has_images) {
                    echo '<div style="grid-column:1/-1;text-align:center;padding:48px 0;color:#94a3b8;"><i class="bi bi-images" style="font-size:48px;opacity:.4;"></i><br><br>Chưa có ảnh nào. Hãy thêm kỷ niệm đầu tiên!</div>';
                }
                ?>
            </div>
        </div>

        <!-- TAB 4: Chuyến đi -->
        <div id="tab-trips" class="tab-content-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">Hành Trình Của Bạn</h5>
                <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" onclick="createNewTrip()">
                    <i class="bi bi-plus-lg"></i> Thêm chuyến đi
                </button>
            </div>
            
            <div class="row g-3">
                <?php if(empty($trips)): ?>
                    <div class="col-12 text-center py-5 opacity-50">
                        <i class="bi bi-briefcase display-4"></i>
                        <p class="mt-2">Bạn chưa tạo chuyến đi nào.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($trips as $t): ?>
                        <div class="col-12">
                            <div class="p-3 bg-white rounded-4 shadow-sm border" style="cursor:pointer" onclick="filterMapByTrip(<?php echo $t['id']; ?>)">
                                <h6 class="fw-bold text-primary mb-1"><?php echo htmlspecialchars($t['title']); ?></h6>
                                <p class="small text-muted mb-2"><?php echo htmlspecialchars($t['description']); ?></p>
                                
                                <?php if (!empty($t['photos'])): ?>
                                                    <div class="d-flex gap-2 mb-3 overflow-auto py-1" onclick="event.stopPropagation()" style="scrollbar-width: none;">
                                                        <?php foreach ($t['photos'] as $p): ?>
                                                            <?php 
                                                            $photo_path = is_array($p) ? $p['image_path'] : $p;
                                                            ?>
                                                            <div class="trip-card-photo" style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden; border: 1px solid rgba(0,0,0,0.05); flex-shrink: 0; background: #eee;">
                                                                <img src="<?php echo UPLOADS_URL . '/' . htmlspecialchars($photo_path); ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://cdn-icons-png.flaticon.com/512/4140/4140044.png'">
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>

                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="badge bg-light text-dark"><i class="bi bi-calendar3"></i> <?php echo $t['start_date'] ? date('d/m/Y', strtotime($t['start_date'])) : 'N/A'; ?></small>
                                    <?php if ($t['user_id'] == $_SESSION['user_id']): ?>
                                        <button class="btn btn-sm btn-outline-primary rounded-pill py-0 px-2" style="font-size:12px;" onclick="event.stopPropagation(); openInviteModal(<?php echo $t['id']; ?>)">
                                            <i class="bi bi-person-plus-fill"></i> Mời
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-secondary text-white" style="font-size:10px;"><i class="bi bi-people-fill"></i> Chung</span>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex gap-2 mt-3 border-top pt-2" onclick="event.stopPropagation()">
                                    <button class="btn btn-sm btn-outline-primary rounded-pill flex-fill" style="font-size:11px;" onclick="openCameraForTrip(<?php echo $t['id']; ?>, <?php echo htmlspecialchars(json_encode($t['title']), ENT_QUOTES, 'UTF-8'); ?>)">
                                        <i class="bi bi-camera-fill me-1"></i> Chụp Locket
                                    </button>
                                    <button class="btn btn-sm btn-outline-success rounded-pill flex-fill" style="font-size:11px;" onclick="addMemoryForTrip(<?php echo $t['id']; ?>)">
                                        <i class="bi bi-plus-lg me-1"></i> Thêm Check-in
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="mt-4 pt-3 border-top text-center">
                <?php if (isset($_GET['trip_id'])): ?>
                    <a href="index.php?url=location/stats&trip_id=<?php echo $_GET['trip_id']; ?>" class="btn btn-outline-primary rounded-pill w-100 fw-bold">
                        <i class="bi bi-bar-chart-fill me-1"></i> Xem Thống Kê Chuyến Đi Này
                    </a>
                <?php else: ?>
                    <a href="index.php?url=location/stats" class="btn btn-outline-primary rounded-pill w-100 fw-bold">
                        <i class="bi bi-bar-chart-fill me-1"></i> Xem Thống Kê Tổng Hợp
                    </a>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- 3. Bottom Navigation (Grid: 2 + FAB + 2) -->
    <div class="bottom-nav">
        <div class="nav-item active" id="nav-timeline" onclick="switchTab('timeline'); setActiveNav('nav-timeline')">
            <i class="bi bi-house-door-fill"></i>
            <span>Trang chủ</span>
        </div>
        <div class="nav-item" id="nav-album" onclick="switchTab('album'); setActiveNav('nav-album')">
            <i class="bi bi-images"></i>
            <span>Album</span>
        </div>
        <div class="nav-item-camera" onclick="openLocketCamera()" title="Chụp ảnh">
            <i class="bi bi-camera-fill"></i>
        </div>
        <div class="nav-item" id="nav-trips" onclick="switchTab('trips'); setActiveNav('nav-trips')">
            <i class="bi bi-briefcase-fill"></i>
            <span>Chuyến đi</span>
        </div>
        <div class="nav-item" id="nav-friends" onclick="switchTab('friends'); setActiveNav('nav-friends')">
            <i class="bi bi-people-fill"></i>
            <span>Bạn bè</span>
        </div>
    </div>
</div>

<!-- Modal Mời Bạn Bè Vào Chuyến Đi -->
<div class="modal fade" id="inviteTripModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0 p-4">
                <h5 class="modal-title fw-bold">Mời Tham Gia Hành Trình</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <input type="hidden" id="invite_trip_id">
                
                <h6 class="small fw-bold text-muted mb-3"><i class="bi bi-people-fill me-2 text-primary"></i> CHỌN TỪ DANH SÁCH BẠN BÈ</h6>
                <div class="list-group list-group-flush mb-4" style="max-height: 200px; overflow-y: auto;">
                    <?php if(!empty($friends)): ?>
                        <?php foreach($friends as $f): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-placeholder" style="width:30px;height:30px;font-size:12px;border-radius:50%;">
                                        <?php echo strtoupper(substr($f['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div class="fw-bold small"><?php echo htmlspecialchars($f['full_name']); ?></div>
                                        <div class="text-muted" style="font-size:10px;">@<?php echo htmlspecialchars($f['username']); ?></div>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-primary rounded-pill" style="font-size:11px;" onclick="sendTripInvite(<?php echo htmlspecialchars(json_encode($f['username']), ENT_QUOTES, 'UTF-8'); ?>)">
                                    Mời
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted small">Bạn chưa kết bạn với ai.</div>
                    <?php endif; ?>
                </div>

                <h6 class="small fw-bold text-muted mb-2"><i class="bi bi-search me-2 text-primary"></i> TÌM & MỜI THÊM BẠN MỚI</h6>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                    <input type="text" id="invite_username_input" class="form-control bg-light border-0" placeholder="Nhập tên hoặc username..." oninput="searchUsersForInvite(this.value)">
                    <button class="btn btn-primary" type="button" onclick="sendTripInvite(document.getElementById('invite_username_input').value)">Mời</button>
                </div>
                <div id="invite_search_results" class="list-group list-group-flush mt-2" style="max-height: 150px; overflow-y: auto;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hồ Sơ & Hành Trang -->
<div class="modal fade" id="profileModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0 p-4">
                <h5 class="modal-title fw-bold">Hành Trang Của Bạn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="position-relative d-inline-block mb-3">
                    <div style="width: 100px; height: 100px; border-radius: 50%; overflow: hidden; border: 3px solid var(--primary); margin: 0 auto; box-shadow: var(--neu-shadow-flat);">
                        <?php if (!empty($_SESSION['avatar'])): ?>
                            <img src="<?php echo htmlspecialchars($_SESSION['avatar']); ?>" alt="Avatar" style="width:100%; height:100%; object-fit: cover;">
                        <?php else: ?>
                            <img src="https://cdn-icons-png.flaticon.com/512/4140/4140044.png" alt="AI icon" style="width:100%; height:100%; object-fit: cover;">
                        <?php endif; ?>
                    </div>
                    <button class="btn btn-sm btn-primary rounded-circle position-absolute" style="bottom: 0; right: 0; width: 32px; height: 32px; padding: 0;" onclick="openAvatarUploader()" title="Đổi ảnh đại diện">
                        <i class="bi bi-camera-fill"></i>
                    </button>
                </div>
                
                <h4 class="fw-bold mb-1"><?php echo htmlspecialchars(!empty($_SESSION['full_name']) ? $_SESSION['full_name'] : $_SESSION['username']); ?></h4>
                <?php if (!empty($_SESSION['full_name'])): ?>
                    <div class="text-muted small mb-2">@<?php echo htmlspecialchars($_SESSION['username']); ?></div>
                <?php endif; ?>
                <div class="badge bg-primary fs-6 mb-3"><?php echo isset($badge_name) ? $badge_name : 'Explorer Lv.1'; ?></div>
                
                <div class="bg-light rounded-4 p-3 mb-4 text-start">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold text-muted">KINH NGHIỆM TÍCH LŨY</span>
                        <span class="small fw-bold text-primary"><?php echo isset($user_xp) ? $user_xp : 0; ?> XP</span>
                    </div>
                    <?php 
                        $xp = isset($user_xp) ? $user_xp : 0;
                        $next_level_xp = 100;
                        if ($xp >= 1000) $next_level_xp = $xp; // Max level
                        elseif ($xp >= 500) $next_level_xp = 1000;
                        elseif ($xp >= 100) $next_level_xp = 500;
                        $progress = ($xp >= 1000) ? 100 : min(100, ($xp / $next_level_xp) * 100);
                    ?>
                    <div class="progress" style="height: 10px; border-radius: 5px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: <?php echo $progress; ?>%"></div>
                    </div>
                    <small class="text-muted" style="font-size: 11px;">
                        <?php if($xp >= 1000): ?>
                            Bạn đã đạt cấp độ cao nhất!
                        <?php else: ?>
                            Cần <?php echo ($next_level_xp - $xp); ?> XP nữa để thăng cấp.
                        <?php endif; ?>
                    </small>
                </div>

                <div class="text-start">
                    <h6 class="fw-bold small text-muted mb-3"><i class="bi bi-award-fill me-2 text-warning"></i> CÁC MỐC DANH HIỆU</h6>
                    <ul class="list-group list-group-flush small mb-3">
                        <li class="list-group-item bg-transparent d-flex justify-content-between px-0 <?php echo $xp < 100 ? 'fw-bold text-primary' : ''; ?>">
                            <span><i class="bi <?php echo $xp >= 0 ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted'; ?> me-2"></i> Explorer Lv.1</span>
                            <span class="text-muted">0 XP</span>
                        </li>
                        <li class="list-group-item bg-transparent d-flex justify-content-between px-0 <?php echo $xp >= 100 && $xp < 500 ? 'fw-bold text-primary' : ''; ?>">
                            <span><i class="bi <?php echo $xp >= 100 ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted'; ?> me-2"></i> 🎒 Tân binh xê dịch</span>
                            <span class="text-muted">100 XP</span>
                        </li>
                        <li class="list-group-item bg-transparent d-flex justify-content-between px-0 <?php echo $xp >= 500 && $xp < 1000 ? 'fw-bold text-primary' : ''; ?>">
                            <span><i class="bi <?php echo $xp >= 500 ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted'; ?> me-2"></i> 🗺️ Kẻ lang thang</span>
                            <span class="text-muted">500 XP</span>
                        </li>
                        <li class="list-group-item bg-transparent d-flex justify-content-between px-0 border-bottom-0 <?php echo $xp >= 1000 ? 'fw-bold text-primary' : ''; ?>">
                            <span><i class="bi <?php echo $xp >= 1000 ? 'bi-check-circle-fill text-success' : 'bi-circle text-muted'; ?> me-2"></i> 👑 Thánh Check-in</span>
                            <span class="text-muted">1000 XP</span>
                        </li>
                    </ul>
                </div>

                <div class="text-start mt-3 pt-3 border-top">
                    <h6 class="fw-bold small text-muted mb-3"><i class="bi bi-patch-check-fill me-2 text-primary"></i> HUY CHƯƠNG THÀNH TỰU</h6>
                    <div class="badge-grid-container" id="badgesGrid">
                        <!-- Badges will be generated dynamically by JavaScript -->
                        <div class="achievement-badge-item text-center p-2 locked" id="badge-explorer" title="Nhà thám hiểm (Check-in 3+ địa điểm)">
                            <div class="badge-icon fs-3">🧭</div>
                            <div style="font-size: 8px; font-weight: bold;" class="text-truncate mt-1">Thám Hiểm</div>
                            <div class="badge-locked-overlay"><i class="bi bi-lock-fill"></i></div>
                        </div>
                        <div class="achievement-badge-item text-center p-2 locked" id="badge-locket" title="Thánh Locket (Đăng 3+ locket)">
                            <div class="badge-icon fs-3">📸</div>
                            <div style="font-size: 8px; font-weight: bold;" class="text-truncate mt-1">Locket Master</div>
                            <div class="badge-locked-overlay"><i class="bi bi-lock-fill"></i></div>
                        </div>
                        <div class="achievement-badge-item text-center p-2 locked" id="badge-night" title="Cú Đêm (Check-in sau 22h tối)">
                            <div class="badge-icon fs-3">🦉</div>
                            <div style="font-size: 8px; font-weight: bold;" class="text-truncate mt-1">Cú Đêm</div>
                            <div class="badge-locked-overlay"><i class="bi bi-lock-fill"></i></div>
                        </div>
                        <div class="achievement-badge-item text-center p-2 locked" id="badge-climber" title="Chinh Phục Đèo Dốc (Check-in tại Núi/Đèo/Sapa...)">
                            <div class="badge-icon fs-3">⛰️</div>
                            <div style="font-size: 8px; font-weight: bold;" class="text-truncate mt-1">Leo Núi</div>
                            <div class="badge-locked-overlay"><i class="bi bi-lock-fill"></i></div>
                        </div>
                        <div class="achievement-badge-item text-center p-2 locked" id="badge-triky" title="Tri Kỷ Hành Trình (Có ít nhất 1 bạn bè)">
                            <div class="badge-icon fs-3">🤝</div>
                            <div style="font-size: 8px; font-weight: bold;" class="text-truncate mt-1">Tri Kỷ</div>
                            <div class="badge-locked-overlay"><i class="bi bi-lock-fill"></i></div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 pt-3 border-top d-grid">
                    <a href="index.php?url=auth/logout" class="btn btn-danger rounded-pill fw-bold py-2"><i class="bi bi-box-arrow-right me-2"></i>Đăng xuất</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Kỷ Niệm (Album Support) -->
<div class="modal fade" id="addMemoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0 p-4">
                <h5 class="modal-title fw-bold">Lưu Giữ Kỷ Niệm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addMemoryForm" action="index.php?url=location/save" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <input type="hidden" name="latitude" id="lat">
                    <input type="hidden" name="longitude" id="lng">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">TÊN ĐỊA ĐIỂM</label>
                        <input type="text" name="place_name" class="form-control form-control-premium" placeholder="Ví dụ: Bãi biển Mỹ Khê..." required>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">NGÀY GHÉ THĂM</label>
                            <input type="date" name="visit_date" class="form-control form-control-premium" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">CẢM XÚC</label>
                            <select name="feeling" class="form-select form-control-premium">
                                <option value="Hạnh phúc">😊 Hạnh phúc</option>
                                <option value="Tuyệt vời">🤩 Tuyệt vời</option>
                                <option value="Bình yên">🧘 Bình yên</option>
                                <option value="Thú vị">🎈 Thú vị</option>
                                <option value="Nhớ nhung">🥺 Nhớ nhung</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">CHUYẾN ĐI (TÙY CHỌN)</label>
                        <select name="trip_id" class="form-select form-control-premium">
                            <option value="">-- Không thuộc chuyến đi nào --</option>
                            <?php if(!empty($trips)): ?>
                                <?php foreach($trips as $t): ?>
                                    <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['title']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">MÔ TẢ NGẮN</label>
                        <textarea name="description" class="form-control form-control-premium" rows="3" placeholder="Ghi lại đôi dòng cảm xúc của bạn..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">QUYỀN RIÊNG TƯ (CHẾ ĐỘ HIỂN THỊ)</label>
                        <select name="privacy" id="add_privacy" class="form-select form-control-premium" onchange="toggleSpecificFriends('add')">
                            <option value="public">🌐 Công khai (Ai cũng có thể xem trên bảng tin)</option>
                            <option value="friends" selected>👥 Bạn bè (Chỉ những người đã kết bạn mới thấy)</option>
                            <option value="specific_friends">👥 Bạn bè cụ thể (Chọn người được xem)</option>
                            <option value="private">🔒 Chỉ mình tôi (Ẩn hoàn toàn khỏi bảng tin)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="addSpecificFriendsContainer" style="display: none; max-height: 150px; overflow-y: auto; background: rgba(255,255,255,0.05); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                        <label class="form-label small fw-bold text-primary mb-2">CHỌN BẠN BÈ ĐƯỢC XEM</label>
                        <?php if(!empty($friends)): ?>
                            <?php foreach($friends as $f): ?>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="visible_friends[]" value="<?php echo $f['id']; ?>" id="add_friend_<?php echo $f['id']; ?>">
                                    <label class="form-check-label text-white-50 small" for="add_friend_<?php echo $f['id']; ?>">
                                        <?php echo htmlspecialchars($f['full_name']); ?> (@<?php echo htmlspecialchars($f['username']); ?>)
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-muted small">Chưa có bạn bè nào để chọn.</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">ALBUM ẢNH/VIDEO (CHỌN NHIỀU)</label>
                        <input type="file" name="images[]" class="form-control form-control-premium" accept="image/*,video/*" multiple>
                        <small class="text-muted">Hỗ trợ ảnh và video (mp4, webm). Ảnh/Video đầu tiên sẽ là đại diện.</small>
                    </div>

                    <div class="alert alert-primary border-0 rounded-4 d-flex align-items-center py-2" id="location-status">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        <small>Đang lấy vị trí hiện tại...</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-premium rounded-pill px-4 shadow"><i class="bi bi-send-fill me-1"></i> Đăng Kỷ Niệm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Album (Lightbox) -->
<div class="modal fade album-lightbox" id="albumModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 bg-dark">
            <div class="modal-header border-0 pb-0 p-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="modal-title fw-bold text-white" id="albumTitle">Album Ảnh</h5>
                    <a href="#" id="manageAlbumLink" class="btn btn-sm btn-white-glass rounded-pill px-3">
                        <i class="bi bi-gear-fill me-1"></i> Quản lý
                    </a>
                </div>
                <div class="album-toolbar ms-auto me-3">
                    <button type="button" id="albumDeletePhotoBtn" onclick="deleteCurrentAlbumPhoto()" title="Xóa ảnh này" class="text-danger me-2" style="display: none; background: none; border: none;"><i class="bi bi-trash3-fill" style="font-size: 16px;"></i></button>
                    <button type="button" onclick="toggleAlbumSlideshow()" title="Auto slideshow"><i class="bi bi-play-fill" id="albumPlayIcon"></i></button>
                    <button type="button" onclick="openFullscreenAlbum()" title="Fullscreen"><i class="bi bi-arrows-fullscreen"></i></button>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="album-stage">
                <div id="albumCarousel" class="carousel slide carousel-fade" data-bs-ride="false">
                    <div class="carousel-inner rounded-4" id="albumItems">
                        <!-- Images will be injected here -->
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#albumCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#albumCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
                <div class="album-thumbs text-start" id="albumThumbs"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chỉnh Sửa Kỷ Niệm -->
<div class="modal fade" id="editMemoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Chỉnh Sửa Kỷ Niệm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editMemoryForm" action="index.php?url=location/update" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">TÊN ĐỊA ĐIỂM</label>
                        <input type="text" name="place_name" id="edit_place_name" class="form-control form-control-premium" required>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">NGÀY GHÉ THĂM</label>
                            <input type="date" name="visit_date" id="edit_visit_date" class="form-control form-control-premium" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">CẢM XÚC</label>
                            <select name="feeling" id="edit_feeling" class="form-select form-control-premium">
                                <option value="Hạnh phúc">😊 Hạnh phúc</option>
                                <option value="Tuyệt vời">🤩 Tuyệt vời</option>
                                <option value="Bình yên">🧘 Bình yên</option>
                                <option value="Thú vị">🎈 Thú vị</option>
                                <option value="Nhớ nhung">🥺 Nhớ nhung</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">CHUYẾN ĐI (TÙY CHỌN)</label>
                        <select name="trip_id" id="edit_trip_id" class="form-select form-control-premium">
                            <option value="">-- Không thuộc chuyến đi nào --</option>
                            <?php if(!empty($trips)): ?>
                                <?php foreach($trips as $t): ?>
                                    <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['title']); ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">MÔ TẢ NGẮN</label>
                        <textarea name="description" id="edit_description" class="form-control form-control-premium" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">QUYỀN RIÊNG TƯ (CHẾ ĐỘ HIỂN THỊ)</label>
                        <select name="privacy" id="edit_privacy" class="form-select form-control-premium" onchange="toggleSpecificFriends('edit')">
                            <option value="public">🌐 Công khai (Ai cũng có thể xem trên bảng tin)</option>
                            <option value="friends">👥 Bạn bè (Chỉ những người đã kết bạn mới thấy)</option>
                            <option value="specific_friends">👥 Bạn bè cụ thể (Chọn người được xem)</option>
                            <option value="private">🔒 Chỉ mình tôi (Ẩn hoàn toàn khỏi bảng tin)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="editSpecificFriendsContainer" style="display: none; max-height: 150px; overflow-y: auto; background: rgba(255,255,255,0.05); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                        <label class="form-label small fw-bold text-primary mb-2">CHỌN BẠN BÈ ĐƯỢC XEM</label>
                        <?php if(!empty($friends)): ?>
                            <?php foreach($friends as $f): ?>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="visible_friends[]" value="<?php echo $f['id']; ?>" id="edit_friend_<?php echo $f['id']; ?>">
                                    <label class="form-check-label text-white-50 small" for="edit_friend_<?php echo $f['id']; ?>">
                                        <?php echo htmlspecialchars($f['full_name']); ?> (@<?php echo htmlspecialchars($f['username']); ?>)
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-muted small">Chưa có bạn bè nào để chọn.</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">THÊM ẢNH/VIDEO VÀO ALBUM (CHỌN NHIỀU)</label>
                        <input type="file" name="new_images[]" class="form-control form-control-premium" accept="image/*,video/*" multiple>
                        <small class="text-muted">Các tệp này sẽ được thêm vào album hiện tại của bạn.</small>
                        <div id="edit_current_image" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-premium rounded-pill px-4"><i class="bi bi-send-fill me-1"></i> Cập Nhật & Đăng</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Modal Tạo Chuyến Đi Mới -->
<div class="modal fade" id="createTripModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0 p-4">
                <h5 class="modal-title fw-bold">Tạo Chuyến Đi Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createTripForm" action="index.php?url=trip/create" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">TÊN CHUYẾN ĐI</label>
                        <input type="text" name="title" class="form-control form-control-premium" placeholder="VD: Mùa hè rực rỡ ở Đà Nẵng" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">NGÀY BẮT ĐẦU</label>
                        <input type="date" name="start_date" class="form-control form-control-premium" value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">MÔ TẢ NGẮN (TÙY CHỌN)</label>
                        <textarea name="description" class="form-control form-control-premium" rows="2" placeholder="Ghi chú thêm về chuyến đi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-premium rounded-pill px-4"><i class="bi bi-plus-lg me-1"></i> Tạo Chuyến Đi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CAMERA OVERLAY -->
<div id="locketCameraUI" class="locket-camera-overlay">
    <div class="camera-header">
        <div class="camera-close" onclick="closeLocketCamera()"><i class="bi bi-x-lg"></i></div>
        <div class="camera-close" onclick="switchCamera()"><i class="bi bi-arrow-repeat"></i></div>
    </div>
    <div class="camera-viewfinder">
        <video id="cameraVideo" autoplay playsinline></video>
        <canvas id="photoPreviewCanvas"></canvas>
        <div id="cameraError">
            <i class="bi bi-exclamation-triangle-fill fs-1 text-warning mb-2"></i>
            <div id="cameraErrorMsg">Lỗi Camera</div>
        </div>
    </div>
    
    <div id="cameraControls" class="camera-controls">
        <div class="capture-btn-outer" onclick="capturePhoto()">
            <div class="capture-btn-inner"></div>
        </div>
        <div id="camLocationStatus" style="position: absolute; bottom: 10px; width: 100%; text-align: center; color: rgba(255,255,255,0.7); font-size: 11px;"></div>
    </div>

    <div id="postControls" class="post-controls">
        <div class="d-flex gap-2 mb-3">
            <button class="btn btn-secondary flex-fill rounded-pill" onclick="retakePhoto()"><i class="bi bi-arrow-counterclockwise"></i> Chụp lại</button>
            <button class="btn btn-primary flex-fill rounded-pill fw-bold" onclick="postLocketPhoto()"><i class="bi bi-send-fill"></i> Đăng ngay</button>
        </div>
        <input type="text" id="locketCaption" class="caption-input" placeholder="Thêm mô tả cho khoảnh khắc này...">
        <div class="d-flex gap-2 overflow-auto pb-2" style="scrollbar-width: none;">
            <button class="btn btn-outline-light rounded-pill btn-sm text-nowrap" onclick="postLocketPhoto('Bạn Bè')">Đăng vào Bạn Bè</button>
            <button class="btn btn-outline-light rounded-pill btn-sm text-nowrap" onclick="postLocketPhoto('Gia đình')">Đăng vào Gia đình</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js"></script>
<!-- Leaflet Control Geocoder -->
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<!-- Inject server-side URL base vào JS -->
<script>
    const uploadsUrl = '<?= defined("UPLOADS_URL") ? UPLOADS_URL : "../uploads" ?>';
</script>

<script>
    // HTTPS force removed
    // Initialize Map
    var map = L.map('map', {
        zoomControl: false
    }).setView([10.762622, 106.660172], 13);

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    // Add Geocoder (Search Bar)
    var geocoder = L.Control.geocoder({
        defaultMarkGeocode: false,
        placeholder: "Tìm kiếm địa điểm..."
    })
    .on('markgeocode', function(e) {
        var bbox = e.geocode.bbox;
        var poly = L.polygon([
            bbox.getSouthEast(),
            bbox.getNorthEast(),
            bbox.getNorthWest(),
            bbox.getSouthWest()
        ]);
        map.fitBounds(poly.getBounds());
        
        // Cập nhật marker vị trí hiện tại
        updateLocationFromMapClick(e.geocode.center);
    })
    .addTo(map);

    // Xử lý sự kiện click trên bản đồ để chọn vị trí
    map.on('click', function(e) {
        updateLocationFromMapClick(e.latlng);
    });

    function updateLocationFromMapClick(latlng) {
        userManuallySetLocation = true;
        followLiveLocation = false;
        
        const btn = document.getElementById('followLocationBtn');
        if (btn) btn.classList.remove('active');
        
        const latEl = document.getElementById('lat');
        const lngEl = document.getElementById('lng');
        if (latEl) latEl.value = latlng.lat;
        if (lngEl) lngEl.value = latlng.lng;
        
        locketLat = latlng.lat;
        locketLng = latlng.lng;

        const timeStr = new Date().toLocaleTimeString('vi-VN');
        const dragPopupHtml = `<strong>Vị trí đã chọn</strong><br>
            <small>${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}</small><br>
            <small>${timeStr}</small>`;
        
        if (!currentLocationMarker) {
            currentLocationMarker = L.marker([latlng.lat, latlng.lng], { 
                icon: liveLocationIcon, 
                zIndexOffset: 2000,
                draggable: true 
            }).addTo(map).bindPopup(dragPopupHtml).openPopup();
            
            // Re-bind dragend event
            currentLocationMarker.on('dragend', function(e) {
                updateLocationFromMapClick(e.target.getLatLng());
            });

            currentLocationCircle = L.circle([latlng.lat, latlng.lng], {
                radius: 10,
                color: '#22d3ee',
                fillColor: '#22d3ee',
                fillOpacity: 0.12,
                weight: 1,
                interactive: false
            }).addTo(map);
        } else {
            currentLocationMarker.setLatLng(latlng);
            currentLocationMarker.setPopupContent(dragPopupHtml).openPopup();
            if (currentLocationCircle) {
                currentLocationCircle.setLatLng(latlng);
                currentLocationCircle.setRadius(10);
            }
        }
        
        updateLocationStatus(
            '<i class="bi bi-pin-map-fill me-2"></i> <small>Đã ghim vị trí thủ công</small>',
            'success'
        );
        
        const hudText = document.getElementById('liveLocationHudText');
        if (hudText) {
            hudText.innerHTML = `<span class="accuracy-good">Ghim thủ công</span><br>${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`;
        }
    }

    const mapLayers = {
        dark: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            maxZoom: 20
        }),
        light: L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            attribution: '&copy; Google Maps',
            maxZoom: 20
        }),
        satellite: L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community',
            maxZoom: 19
        })
    };
    let activeMapLayer = mapLayers.light.addTo(map);

    // Helper functions for custom markers
    function getFeelingEmoji(feeling) {
        const map = {
            'Hạnh phúc': '😊',
            'Tuyệt vời': '🤩',
            'Bình yên': '🧘',
            'Thú vị': '🎈',
            'Nhớ nhung': '🥺'
        };
        return map[feeling] || '📍';
    }

    function createCustomMarkerHtml(loc, isFriend) {
        const hasImage = !!loc.image;
        const mainImg = hasImage ? `${uploadsUrl}/${loc.image}` : (loc.user_avatar ? `${uploadsUrl}/avatars/${loc.user_avatar}` : 'https://cdn-icons-png.flaticon.com/512/4140/4140044.png');
        const emoji = getFeelingEmoji(loc.feeling);
        const userAvatar = loc.user_avatar ? `${uploadsUrl}/avatars/${loc.user_avatar}` : 'https://cdn-icons-png.flaticon.com/512/4140/4140044.png';
        const borderColor = isFriend ? '#6366f1' : '#22d3ee';
        
        return `
            <div class="custom-map-marker" style="border: 3px solid ${borderColor};">
                <img src="${mainImg}" class="custom-marker-img" onerror="this.src='https://cdn-icons-png.flaticon.com/512/4140/4140044.png'" />
                <div class="custom-marker-feeling-badge">${emoji}</div>
                ${hasImage ? `<img src="${userAvatar}" class="custom-marker-user-badge" onerror="this.src='https://cdn-icons-png.flaticon.com/512/4140/4140044.png'" />` : ''}
                <div class="custom-marker-pulse" style="border-color: ${borderColor};"></div>
            </div>
        `;
    }

    // Markers Data
    var savedLocations = <?php echo json_encode($locations); ?>;
    var friendLocations = <?php echo json_encode((!isset($is_friend_view) && isset($friend_locations)) ? $friend_locations : []); ?>;
    var markers = [];
    var routeLine = null;
    window.journeyCircles = [];
    var journeyPoints = [];

    function drawMapMarkers() {
        // Clear existing markers
        for (var id in markers) {
            if (markers[id]) {
                map.removeLayer(markers[id]);
            }
        }
        markers = [];

        // Clear route line
        if (routeLine) {
            map.removeLayer(routeLine);
            routeLine = null;
        }

        // Clear journey circles
        if (window.journeyCircles) {
            window.journeyCircles.forEach(function(c) {
                if (c) map.removeLayer(c);
            });
        }
        window.journeyCircles = [];

        // Draw new markers for savedLocations
        savedLocations.forEach(function(loc) {
            if (!loc.latitude || !loc.longitude) return;
            
            var customIcon = L.divIcon({
                className: 'photo-marker-wrapper',
                html: createCustomMarkerHtml(loc, false),
                iconSize: [48, 48],
                iconAnchor: [24, 48],
                popupAnchor: [0, -48]
            });

            var marker = L.marker([loc.latitude, loc.longitude], { icon: customIcon }).addTo(map);
            marker.on('click', function(e) {
                triggerBumpRipple(e.latlng.lat, e.latlng.lng);
            });
            
            var popupContent = `
                <div class="p-2" style="width: 240px">
                    ${loc.image ? `<img src="${uploadsUrl}/${loc.image}" class="img-fluid rounded-3 mb-2 shadow-sm" onclick="openAlbum(${loc.id}, decodeURIComponent('${encodeURIComponent(loc.place_name || 'Album').replace(/'/g, "%27")}'))" style="cursor:pointer">` : ''}
                    <h6 class="fw-bold mb-1">${loc.place_name}</h6>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <small class="text-muted"><i class="bi bi-calendar-event"></i> ${loc.visit_date}</small>
                        <span class="badge bg-primary-subtle text-primary rounded-pill">${loc.feeling}</span>
                    </div>
                    <p class="small text-secondary mb-0">${loc.description || 'Không có mô tả'}</p>
                    <button class="btn btn-primary btn-sm w-100 mt-2 rounded-pill" onclick="openAlbum(${loc.id}, decodeURIComponent('${encodeURIComponent(loc.place_name || 'Album').replace(/'/g, "%27")}'))">Xem Album</button>
                </div>
            `;
            
            marker.bindPopup(popupContent, {
                className: 'premium-popup',
                maxWidth: 300
            });
            
            markers[loc.id] = marker;
        });

        // Draw new markers for friendLocations
        friendLocations.forEach(function(loc) {
            if (!loc.latitude || !loc.longitude) return;
            
            var friendIcon = L.divIcon({
                className: 'photo-marker-wrapper',
                html: createCustomMarkerHtml(loc, true),
                iconSize: [48, 48],
                iconAnchor: [24, 48],
                popupAnchor: [0, -48]
            });

            var friendMarker = L.marker([loc.latitude, loc.longitude], { icon: friendIcon }).addTo(map);
            friendMarker.on('click', function(e) {
                triggerBumpRipple(e.latlng.lat, e.latlng.lng);
            });
            var friendPopup = `
                <div class="p-2" style="width: 250px">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="avatar-placeholder" style="width:28px;height:28px;font-size:12px;border-radius:50%;background:#e0f2fe;color:#0d6efd;">
                            ${(loc.username || '?').charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <div class="small fw-bold text-primary">${loc.full_name || 'Bạn bè'}</div>
                            <div class="text-muted" style="font-size:11px;">@${loc.username || ''}</div>
                        </div>
                    </div>
                    ${loc.image ? `<img src="${uploadsUrl}/${loc.image}" class="img-fluid rounded-3 mb-2 shadow-sm" onclick="openAlbum(${loc.id}, decodeURIComponent('${encodeURIComponent(loc.place_name || 'Album').replace(/'/g, "%27")}'))" style="cursor:pointer;max-height:150px;object-fit:cover;width:100%;">` : ''}
                    <h6 class="fw-bold mb-1">${loc.place_name}</h6>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <small class="text-muted"><i class="bi bi-calendar-event"></i> ${loc.visit_date}</small>
                        <span class="badge bg-primary-subtle text-primary rounded-pill">${loc.feeling || ''}</span>
                    </div>
                    <p class="small text-secondary mb-0">${loc.description || 'Không có mô tả'}</p>
                    <button class="btn btn-primary btn-sm w-100 mt-2 rounded-pill" onclick="openAlbum(${loc.id}, decodeURIComponent('${encodeURIComponent(loc.place_name || 'Album').replace(/'/g, "%27")}'))">
                        <i class="bi bi-images me-1"></i> Xem album
                    </button>
                </div>
            `;

            friendMarker.bindPopup(friendPopup, {
                className: 'premium-popup',
                maxWidth: 310
            });

            markers[loc.id] = friendMarker;
        });

        // Calculate journey points
        journeyPoints = savedLocations
            .filter(loc => loc.latitude && loc.longitude)
            .map(loc => [Number(loc.latitude), Number(loc.longitude)])
            .reverse();

        if (journeyPoints.length > 1) {
            routeLine = L.polyline(journeyPoints, {
                color: '#22d3ee',
                weight: 4,
                opacity: 0.78,
                dashArray: '10 12'
            }).addTo(map);

            // Marching ants animation loop using requestAnimationFrame
            let offset = 0;
            function animateMarchingAnts() {
                if (!routeLine || !map.hasLayer(routeLine)) return;
                offset = (offset - 1) % 22;
                const el = routeLine.getElement();
                if (el) {
                    el.style.strokeDashoffset = offset + 'px';
                    requestAnimationFrame(animateMarchingAnts);
                }
            }
            setTimeout(() => {
                requestAnimationFrame(animateMarchingAnts);
            }, 500);
        }

        // Draw circles/pulses for stop points
        journeyPoints.forEach((point, index) => {
            var circ = L.circleMarker(point, {
                radius: 26 + Math.min(index * 3, 18),
                stroke: false,
                fillColor: '#f43f5e',
                fillOpacity: 0.12
            }).addTo(map);
            window.journeyCircles.push(circ);
        });
    }

    // Draw markers initially
    drawMapMarkers();

    function setMapTheme(theme) {
        map.removeLayer(activeMapLayer);
        activeMapLayer = mapLayers[theme].addTo(map);
        document.getElementById('darkMapBtn').classList.toggle('active', theme === 'dark');
        document.getElementById('lightMapBtn').classList.toggle('active', theme === 'light');
        if (document.getElementById('satelliteMapBtn')) {
            document.getElementById('satelliteMapBtn').classList.toggle('active', theme === 'satellite');
        }
        
        // Toggle Global UI Theme
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('uiTheme', 'dark');
        } else if (theme === 'light') {
            document.documentElement.removeAttribute('data-theme');
            localStorage.setItem('uiTheme', 'light');
        }
    }
    
    // Khôi phục theme UI khi tải trang
    if (localStorage.getItem('uiTheme') === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    }

    function fitJourneyRoute() {
        var localJourneyPoints = savedLocations
            .filter(loc => loc.latitude && loc.longitude)
            .map(loc => [Number(loc.latitude), Number(loc.longitude)])
            .reverse();
        if (routeLine) {
            map.fitBounds(routeLine.getBounds(), { padding: [60, 60] });
        } else if (localJourneyPoints.length === 1) {
            map.flyTo(localJourneyPoints[0], 15);
        }
    }

    // Theo dõi vị trí thực tế — GPS ưu tiên, bộ lọc độ chính xác thông minh
    let currentLocationMarker = null;
    let currentLocationCircle = null;
    let locationWatchId = null;
    let followLiveLocation = true;
    let bestAccuracy = Infinity;
    let locationFixCount = 0;
    let userManuallySetLocation = false;
    let _lastRawLat = null, _lastRawLng = null;
    let _accuracyRetryTimer = null;

    // Options chính: yêu cầu độ chính xác cao
    const geoOptions = {
        enableHighAccuracy: true,
        maximumAge: 3000,    // Cho phép cache tối đa 3 giây để phản hồi nhanh hơn
        timeout: 20000       // Chờ tối đa 20s để lấy GPS xịn nhất
    };
    // Options nhanh: dùng network/wifi location trước để phản hồi tức thì
    const geoOptionsFast = {
        enableHighAccuracy: false,
        maximumAge: 10000,   // Cho phép cache 10 giây
        timeout: 8000        // Timeout nhanh 8s
    };

    const liveLocationIcon = L.divIcon({
        className: 'live-location-marker',
        html: '<div class="live-location-dot"><div class="live-location-pulse"></div></div>',
        iconSize: [18, 18],
        iconAnchor: [9, 9]
    });

    /** Sửa trường hợp lat/lng bị đảo (hay gặp khi lưu sai) */
    function fixCoordinates(lat, lng) {
        lat = parseFloat(lat);
        lng = parseFloat(lng);
        if (!isFinite(lat) || !isFinite(lng)) return null;
        const latLooksLikeLng = lat > 50 && lat < 180;
        const lngLooksLikeLat = lng > -90 && lng < 50;
        if (latLooksLikeLng && lngLooksLikeLat) {
            return { lat: lng, lng: lat };
        }
        return { lat, lng };
    }

    function zoomForAccuracy(accuracy) {
        if (accuracy <= 20) return 18;
        if (accuracy <= 50) return 17;
        if (accuracy <= 120) return 16;
        return 15;
    }

    function updateLocationHud(lat, lng, accuracy) {
        const hud = document.getElementById('liveLocationHud');
        const hudText = document.getElementById('liveLocationHudText');
        if (!hud || !hudText) return;
        hud.style.display = 'block';
        const accClass = accuracy <= 40 ? 'accuracy-good' : (accuracy <= 150 ? 'accuracy-mid' : 'accuracy-low');
        const accLabel = accuracy <= 40 ? 'GPS tốt' : (accuracy <= 150 ? 'GPS trung bình' : 'Độ chính xác thấp — bấm định vị lại');
        hudText.innerHTML = `<span class="${accClass}">${accLabel}</span><br>${lat.toFixed(6)}, ${lng.toFixed(6)} · ±${Math.round(accuracy)}m`;
    }

    function updateLocationStatus(message, type = 'success') {
        const statusEl = document.getElementById('location-status');
        if (!statusEl) return;
        const alertClass = type === 'success' ? 'alert-success' : (type === 'warning' ? 'alert-warning' : 'alert-danger');
        statusEl.className = `alert ${alertClass} border-0 rounded-4 d-flex align-items-center py-2 animate-fade-in`;
        statusEl.innerHTML = message;
    }

    let lastFlyLatLng = null;

    function updateCurrentPosition(position) {
        if (userManuallySetLocation) return;

        const fixed = fixCoordinates(position.coords.latitude, position.coords.longitude);
        if (!fixed) return;

        const lat = fixed.lat;
        const lng = fixed.lng;
        const accuracy = position.coords.accuracy || 999;
        const improved = accuracy < bestAccuracy;
        bestAccuracy = Math.min(bestAccuracy, accuracy);
        locationFixCount++;

        const latEl = document.getElementById('lat');
        const lngEl = document.getElementById('lng');
        if (latEl) latEl.value = lat;
        if (lngEl) lngEl.value = lng;

        const updatedAt = new Date().toLocaleTimeString('vi-VN');
        updateLocationStatus(
            `<i class="bi bi-geo-alt-fill me-2"></i> <small>GPS · ${updatedAt} · ${lat.toFixed(5)}, ${lng.toFixed(5)} · ±${Math.round(accuracy)}m</small>`,
            accuracy > 200 ? 'warning' : 'success'
        );
        updateLocationHud(lat, lng, accuracy);

        const popupHtml = `<strong>Vị trí của bạn</strong><br>
            <small>${lat.toFixed(6)}, ${lng.toFixed(6)}</small><br>
            <small>±${Math.round(accuracy)}m · ${updatedAt}</small><br>
            <span class="badge bg-warning mt-1 text-dark">Kéo thả marker để sửa vị trí</span>`;

        if (!currentLocationMarker) {
            currentLocationMarker = L.marker([lat, lng], { 
                icon: liveLocationIcon, 
                zIndexOffset: 2000,
                draggable: true 
            }).addTo(map).bindPopup(popupHtml);

            currentLocationMarker.on('dragend', function(e) {
                const newPos = e.target.getLatLng();
                userManuallySetLocation = true;
                followLiveLocation = false;
                
                const btn = document.getElementById('followLocationBtn');
                if (btn) btn.classList.remove('active');
                
                const latEl = document.getElementById('lat');
                const lngEl = document.getElementById('lng');
                if (latEl) latEl.value = newPos.lat;
                if (lngEl) lngEl.value = newPos.lng;
                
                locketLat = newPos.lat;
                locketLng = newPos.lng;

                const timeStr = new Date().toLocaleTimeString('vi-VN');
                const dragPopupHtml = `<strong>Vị trí đã ghim tay</strong><br>
                    <small>${newPos.lat.toFixed(6)}, ${newPos.lng.toFixed(6)}</small><br>
                    <small>${timeStr}</small>`;
                
                currentLocationMarker.setPopupContent(dragPopupHtml).openPopup();
                if (currentLocationCircle) {
                    currentLocationCircle.setLatLng(newPos);
                    currentLocationCircle.setRadius(8);
                }
                
                updateLocationStatus(
                    '<i class="bi bi-pin-map-fill me-2"></i> <small>Đã ghim vị trí thủ công</small>',
                    'success'
                );
                
                const hudText = document.getElementById('liveLocationHudText');
                if (hudText) {
                    hudText.innerHTML = `<span class="accuracy-good">Ghim thủ công</span><br>${newPos.lat.toFixed(6)}, ${newPos.lng.toFixed(6)}`;
                }
                
                const camStatus = document.getElementById('camLocationStatus');
                if (camStatus) {
                    camStatus.innerHTML = `<i class="bi bi-pin-map-fill text-success me-1"></i> Đã ghim tay`;
                }
            });

            currentLocationCircle = L.circle([lat, lng], {
                radius: Math.max(accuracy, 8),
                color: '#22d3ee',
                fillColor: '#22d3ee',
                fillOpacity: 0.12,
                weight: 1,
                interactive: false
            }).addTo(map);
        } else {
            currentLocationMarker.setLatLng([lat, lng]);
            currentLocationMarker.setPopupContent(popupHtml);
            currentLocationCircle.setLatLng([lat, lng]);
            currentLocationCircle.setRadius(Math.max(accuracy, 8));
        }

        if (followLiveLocation) {
            const here = L.latLng(lat, lng);
            const movedM = lastFlyLatLng ? lastFlyLatLng.distanceTo(here) : 999;
            if (locationFixCount <= 3 || improved || movedM > 25) {
                const zoom = zoomForAccuracy(accuracy);
                map.flyTo([lat, lng], zoom, { duration: locationFixCount <= 2 ? 1.2 : 0.6 });
                lastFlyLatLng = here;
            }
        }

        const cameraUi = document.getElementById('locketCameraUI');
        if (cameraUi && cameraUi.style.display === 'flex') {
            locketLat = lat;
            locketLng = lng;
            const camStatus = document.getElementById('camLocationStatus');
            if (camStatus) {
                camStatus.innerHTML = `<i class="bi bi-geo-alt-fill text-success me-1"></i> GPS · ±${Math.round(accuracy)}m`;
            }
        }
    }

    function onLocationError(error) {
        const messages = {
            1: 'Vui lòng cho phép truy cập vị trí (biểu tượng ổ khóa trên thanh địa chỉ).',
            2: 'Không xác định được GPS. Hãy bật Vị trí (Location Services) trên thiết bị.',
            3: 'Đang tìm tín hiệu GPS chính xác... Hãy ra ngoài trời nếu có thể.'
        };
        updateLocationStatus(
            `<i class="bi bi-exclamation-triangle-fill me-2"></i> <small>${messages[error.code] || 'Lỗi định vị'}</small>`,
            'warning'
        );
        const hudText = document.getElementById('liveLocationHudText');
        if (hudText) hudText.textContent = messages[error.code] || 'Đang tìm tín hiệu GPS...';
    }

    function requestAccurateLocation() {
        if (!navigator.geolocation) return;
        
        updateLocationStatus('<i class="bi bi-geo-alt me-2"></i> <small>Đang lấy vị trí thực tế mới nhất...</small>', 'warning');
        const hudText = document.getElementById('liveLocationHudText');
        if (hudText) hudText.textContent = 'Đang định vị lại...';

        // Bước 1: Lấy vị trí nhanh bằng network/wifi (phản hồi <3s)
        navigator.geolocation.getCurrentPosition(
            updateCurrentPosition,
            function(fastErr) {
                // Nếu lấy nhanh thất bại, thử lại với high accuracy
                navigator.geolocation.getCurrentPosition(
                    updateCurrentPosition,
                    onLocationError,
                    geoOptions
                );
            },
            geoOptionsFast
        );
        // Bước 2: Đồng thời lấy vị trí chính xác cao hơn (GPS hardware)
        navigator.geolocation.getCurrentPosition(
            updateCurrentPosition,
            function() {},  // Bỏ qua lỗi ở bước 2 vì bước 1 đã xử lý
            geoOptions
        );
    }

    function startLiveLocationTracking() {
        if (!navigator.geolocation) {
            updateLocationStatus('<i class="bi bi-exclamation-triangle-fill me-2"></i> <small>Trình duyệt không hỗ trợ định vị</small>', 'warning');
            return;
        }

        // Reset nếu đang có watch cũ để tránh bị return sớm
        if (locationWatchId !== null) {
            navigator.geolocation.clearWatch(locationWatchId);
            locationWatchId = null;
        }
        if (_accuracyRetryTimer !== null) {
            clearInterval(_accuracyRetryTimer);
            _accuracyRetryTimer = null;
        }

        const hudEl = document.getElementById('liveLocationHud');
        const hudText = document.getElementById('liveLocationHudText');
        if (hudEl) hudEl.style.display = 'block';
        if (hudText) hudText.textContent = 'Đang lấy vị trí...';

        // Bước 1: Lấy vị trí nhanh ngay lập tức (network/wifi, không cần GPS hardware)
        navigator.geolocation.getCurrentPosition(
            function(pos) {
                updateCurrentPosition(pos);
                // Sau khi có vị trí nhanh, tiếp tục lấy GPS chính xác
                navigator.geolocation.getCurrentPosition(
                    updateCurrentPosition,
                    function() {},  // Im lặng nếu GPS cao thất bại
                    geoOptions
                );
            },
            function(fastErr) {
                // Nếu lấy nhanh thất bại, thử ngay với high accuracy
                navigator.geolocation.getCurrentPosition(
                    updateCurrentPosition,
                    onLocationError,
                    geoOptions
                );
            },
            geoOptionsFast  // <-- Nhanh: không cần GPS hardware
        );

        // watchPosition để liên tục cập nhật theo thời gian thực
        locationWatchId = navigator.geolocation.watchPosition(
            updateCurrentPosition,
            onLocationError,
            geoOptions
        );

        // Retry nếu chưa lấy được GPS xịn sau 25 giây
        _accuracyRetryTimer = setInterval(() => {
            if (!userManuallySetLocation && bestAccuracy > 100) {
                console.log('Đang thử nâng cao độ chính xác GPS...');
                requestAccurateLocation();
            }
        }, 25000);
    }

    function stopLiveLocationTracking() {
        if (locationWatchId !== null) {
            navigator.geolocation.clearWatch(locationWatchId);
            locationWatchId = null;
        }
        if (_accuracyRetryTimer !== null) {
            clearInterval(_accuracyRetryTimer);
            _accuracyRetryTimer = null;
        }
    }

    function refreshMyLocation() {
        bestAccuracy = Infinity;
        locationFixCount = 0;
        lastFlyLatLng = null;
        userManuallySetLocation = false;
        document.getElementById('liveLocationHudText').textContent = 'Đang định vị lại...';
        followLiveLocation = true;
        const followBtn = document.getElementById('followLocationBtn');
        if (followBtn) followBtn.classList.add('active');
        requestAccurateLocation();
    }

    function toggleFollowLocation() {
        followLiveLocation = !followLiveLocation;
        const btn = document.getElementById('followLocationBtn');
        if (btn) btn.classList.toggle('active', followLiveLocation);

        if (followLiveLocation && currentLocationMarker) {
            const pos = currentLocationMarker.getLatLng();
            map.flyTo(pos, 17, { duration: 1 });
            currentLocationMarker.openPopup();
        }
    }

    // === PREMIUM GEOLOCATION PERMISSION FLOW ===
    function showLocationPromptModal() {
        hideLocationModals();
        const modalHtml = `
            <div id="locationPermissionModal" class="premium-location-modal">
                <div class="premium-location-modal-card animate-scale-in">
                    <div class="location-icon-container mb-3">
                        <i class="bi bi-geo-alt-fill text-danger animate-pulse"></i>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark">Bật vị trí GPS của bạn</h5>
                    <p class="text-muted small mb-4 px-2" style="line-height: 1.5;">
                        Travel Map cần truy cập vị trí của bạn để hiển thị chính xác vị trí trên bản đồ và giúp bạn chụp ảnh đăng bài nhanh.
                    </p>
                    <button type="button" class="btn btn-premium rounded-pill w-100 py-2.5 fw-bold mb-2 shadow" onclick="triggerLocationGrant()">
                        <i class="bi bi-check-circle-fill me-2"></i>Cho phép truy cập
                    </button>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    function showLocationNoticeModal(message, type = 'warning') {
        hideLocationModals();
        const icon = type === 'danger' ? 'bi-shield-slash-fill text-danger' : 'bi-exclamation-triangle-fill text-warning';
        const title = type === 'danger' ? 'Yêu cầu kết nối bảo mật' : 'GPS đã bị chặn';
        const modalHtml = `
            <div id="locationPermissionModal" class="premium-location-modal">
                <div class="premium-location-modal-card animate-scale-in">
                    <div class="location-icon-container mb-3">
                        <i class="bi ${icon} animate-bounce"></i>
                    </div>
                    <h5 class="fw-bold mb-2 text-dark">${title}</h5>
                    <div class="text-muted small mb-4 px-2" style="line-height: 1.5;">
                        ${message}
                    </div>
                    <button type="button" class="btn btn-secondary rounded-pill w-100 py-2.5 fw-bold" onclick="hideLocationModals()">
                        Đóng thông báo
                    </button>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    function hideLocationModals() {
        const el = document.getElementById('locationPermissionModal');
        if (el) el.remove();
    }

    function triggerLocationGrant() {
        hideLocationModals();
        // Reset trạng thái để đảm bảo tracking bắt đầu lại từ đầu
        bestAccuracy = Infinity;
        locationFixCount = 0;
        lastFlyLatLng = null;
        userManuallySetLocation = false;
        followLiveLocation = true;
        startLiveLocationTracking();
    }

    window.triggerLocationGrant = triggerLocationGrant;
    window.hideLocationModals = hideLocationModals;

    function initializeDefaultMarker() {
        if (!currentLocationMarker) {
            // Khởi tạo vị trí mặc định tại trung tâm bản đồ hiện tại (mặc định TP.HCM hoặc Hà Nội)
            const mapCenter = map.getCenter();
            const timeStr = new Date().toLocaleTimeString('vi-VN');
            const dragPopupHtml = `<strong>Vị trí mặc định (Ghim tay)</strong><br>
                <small>${mapCenter.lat.toFixed(6)}, ${mapCenter.lng.toFixed(6)}</small><br>
                <span class="badge bg-warning mt-1 text-dark">Kéo thả marker để sửa vị trí</span>`;
            
            currentLocationMarker = L.marker(mapCenter, { 
                icon: liveLocationIcon, 
                zIndexOffset: 2000,
                draggable: true 
            }).addTo(map).bindPopup(dragPopupHtml);

            currentLocationMarker.on('dragend', function(e) {
                const newPos = e.target.getLatLng();
                userManuallySetLocation = true;
                followLiveLocation = false;
                
                const btn = document.getElementById('followLocationBtn');
                if (btn) btn.classList.remove('active');
                
                const latEl = document.getElementById('lat');
                const lngEl = document.getElementById('lng');
                if (latEl) latEl.value = newPos.lat;
                if (lngEl) lngEl.value = newPos.lng;
                
                locketLat = newPos.lat;
                locketLng = newPos.lng;

                const timeStr = new Date().toLocaleTimeString('vi-VN');
                const dragPopupHtml = `<strong>Vị trí đã ghim tay</strong><br>
                    <small>${newPos.lat.toFixed(6)}, ${newPos.lng.toFixed(6)}</small><br>
                    <small>${timeStr}</small>`;
                
                currentLocationMarker.setPopupContent(dragPopupHtml).openPopup();
                if (currentLocationCircle) {
                    currentLocationCircle.setLatLng(newPos);
                    currentLocationCircle.setRadius(8);
                }
                
                updateLocationStatus(
                    '<i class="bi bi-pin-map-fill me-2"></i> <small>Đã ghim vị trí thủ công</small>',
                    'success'
                );
                
                const hudText = document.getElementById('liveLocationHudText');
                if (hudText) {
                    hudText.innerHTML = `<span class="accuracy-good">Ghim thủ công</span><br>${newPos.lat.toFixed(6)}, ${newPos.lng.toFixed(6)}`;
                }
            });

            currentLocationCircle = L.circle(mapCenter, {
                radius: 10,
                color: '#22d3ee',
                fillColor: '#22d3ee',
                fillOpacity: 0.12,
                weight: 1,
                interactive: false
            }).addTo(map);

            // Gán giá trị mặc định cho form luôn để tránh bị trống
            const latEl = document.getElementById('lat');
            const lngEl = document.getElementById('lng');
            if (latEl) latEl.value = mapCenter.lat;
            if (lngEl) lngEl.value = mapCenter.lng;
            locketLat = mapCenter.lat;
            locketLng = mapCenter.lng;
        }
    }

    function checkAndRequestLocationPermission() {
        if (!navigator.geolocation) {
            showLocationNoticeModal("Thiết bị hoặc trình duyệt của bạn không hỗ trợ định vị GPS tự động. Bạn vẫn có thể click lên bản đồ hoặc kéo thả marker để chọn vị trí.", "warning");
            initializeDefaultMarker();
            return;
        }

        // Kiểm tra xem có đang ở môi trường bảo mật không (HTTPS hoặc localhost)
        if (window.location.protocol !== 'https:' && window.location.hostname !== 'localhost' && window.location.hostname !== '127.0.0.1') {
            showLocationNoticeModal(`
                <h6 class="fw-bold text-dark mb-3">Chế độ Ghim Vị Trí Thủ Công</h6>
                Do thiết bị kết nối qua mạng nội bộ HTTP (không có bảo mật HTTPS), trình duyệt điện thoại sẽ chặn tính năng tự động lấy GPS.<br><br>
                <div class="alert alert-info py-2 small border-0 text-start">
                    💡 <strong>Cách dùng:</strong> Nhấn nút bên dưới để đóng bảng này. Sau đó bạn có thể <strong>kéo thả chấm tròn xanh</strong> hoặc <strong>click trực tiếp lên bản đồ</strong> để chọn vị trí của mình!
                </div>
                <button type="button" class="btn btn-primary rounded-pill w-100 py-2.5 fw-bold" onclick="hideLocationModals()">
                    Đã hiểu, tôi sẽ ghim tay
                </button>
            `, "warning");
            
            initializeDefaultMarker();
            return;
        }

        if (navigator.permissions && navigator.permissions.query) {
            navigator.permissions.query({ name: 'geolocation' }).then(function(result) {
                if (result.state === 'prompt') {
                    showLocationPromptModal();
                } else if (result.state === 'denied') {
                    showLocationNoticeModal("Truy cập vị trí bị chặn! Vui lòng chạm vào biểu tượng ổ khóa 🔒 trên thanh địa chỉ trình duyệt, sau đó chọn Cho phép (Allow) quyền Vị trí.", "warning");
                    initializeDefaultMarker();
                } else {
                    startLiveLocationTracking();
                }

                result.onchange = function() {
                    if (result.state === 'granted') {
                        hideLocationModals();
                        startLiveLocationTracking();
                    } else if (result.state === 'denied') {
                        showLocationNoticeModal("Truy cập vị trí bị chặn! Vui lòng chạm vào biểu tượng ổ khóa 🔒 trên thanh địa chỉ trình duyệt, sau đó chọn Cho phép (Allow) quyền Vị trí.", "warning");
                        initializeDefaultMarker();
                    }
                };
            }).catch(function(err) {
                startLiveLocationTracking();
            });
        } else {
            // Fallback cho trình duyệt không hỗ trợ Permissions query
            startLiveLocationTracking();
        }
    }

    // Thực hiện hỏi quyền vị trí của người dùng ngay khi tải trang
    checkAndRequestLocationPermission();

    window.addEventListener('beforeunload', stopLiveLocationTracking);

    // Invalidate map size on load and resize to prevent gray tiles
    setTimeout(() => { map.invalidateSize(true); }, 300);
    setTimeout(() => { map.invalidateSize(true); }, 800);

    // Debounced resize handler to fix map on DevTools toggle
    let _resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(_resizeTimer);
        _resizeTimer = setTimeout(() => {
            map.invalidateSize(true);
        }, 200);
    });

    // Also invalidate when visibility changes (e.g. switching tabs)
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            setTimeout(() => map.invalidateSize(true), 200);
        }
    });

    function focusMap(lat, lng, openPopup = false) {
        map.flyTo([lat, lng], 16, {
            duration: 1.5
        });
        triggerBumpRipple(lat, lng);
        
        if (openPopup) {
            savedLocations.concat(friendLocations).forEach(loc => {
                if(loc.latitude == lat && loc.longitude == lng) {
                    setTimeout(() => {
                        if (markers[loc.id]) {
                            markers[loc.id].openPopup();
                        }
                    }, 1500);
                }
            });
        }
    }

    function focusMemory(locationId) {
        const marker = markers[locationId];
        const loc = savedLocations.concat(friendLocations).find(item => Number(item.id) === Number(locationId));
        if (!marker || !loc) return;

        map.flyTo([loc.latitude, loc.longitude], 16, {
            duration: 1.5
        });
        triggerBumpRipple(loc.latitude, loc.longitude);
        setTimeout(() => marker.openPopup(), 1500);
    }

    // Album Functionality (Supports Photos & Videos)
    function openAlbum(id, title) {
        document.getElementById('albumTitle').innerText = title;
        const itemsContainer = document.getElementById('albumItems');
        const thumbsContainer = document.getElementById('albumThumbs');
        itemsContainer.innerHTML = '<div class="text-white py-5"><div class="spinner-border text-primary"></div></div>';
        thumbsContainer.innerHTML = '';
        // Cập nhật link quản lý và nút xóa ảnh (chỉ hiện nếu là của mình)
        const manageLink = document.getElementById('manageAlbumLink');
        const deletePhotoBtn = document.getElementById('albumDeletePhotoBtn');
        <?php if(isset($is_friend_view) && $is_friend_view): ?>
            manageLink.style.display = 'none';
            if (deletePhotoBtn) deletePhotoBtn.style.display = 'none';
        <?php else: ?>
            manageLink.style.display = 'inline-block';
            manageLink.href = `index.php?url=location/manageAlbum&id=${id}`;
            if (deletePhotoBtn) deletePhotoBtn.style.display = 'inline-block';
        <?php endif; ?>

        var albumModal = new bootstrap.Modal(document.getElementById('albumModal'));
        albumModal.show();
        
        const videoExtensions = ['mp4', 'webm', 'ogg', 'mov'];

        fetch(`index.php?url=location/getAlbum&id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    itemsContainer.innerHTML = data.map((item, index) => {
                        const ext = item.image_path.split('.').pop().toLowerCase();
                        const isVideo = videoExtensions.includes(ext);
                        
                        if (isVideo) {
                            return `
                                <div class="carousel-item ${index === 0 ? 'active' : ''}" data-image-id="${item.id}">
                                    <video controls class="d-block w-100 rounded-4" style="max-height: 70vh; background: #000;">
                                        <source src="${uploadsUrl}/${item.image_path}" type="video/${ext === 'mov' ? 'mp4' : ext}">
                                        Trình duyệt của bạn không hỗ trợ xem video.
                                    </video>
                                </div>
                            `;
                        } else {
                            return `
                                <div class="carousel-item ${index === 0 ? 'active' : ''}" data-image-id="${item.id}">
                                    <img src="${uploadsUrl}/${item.image_path}" class="d-block w-100 rounded-4" style="max-height: 70vh; object-fit: contain;">
                                </div>
                            `;
                        }
                    }).join('');
                    thumbsContainer.innerHTML = data.map((item, index) => {
                        const ext = item.image_path.split('.').pop().toLowerCase();
                        const isVideo = videoExtensions.includes(ext);
                        const media = isVideo
                            ? `<video muted><source src="${uploadsUrl}/${item.image_path}" type="video/${ext === 'mov' ? 'mp4' : ext}"></video>`
                            : `<img src="${uploadsUrl}/${item.image_path}" alt="Album item ${index + 1}">`;

                        return `<button class="album-thumb ${index === 0 ? 'active' : ''}" type="button" data-bs-target="#albumCarousel" data-bs-slide-to="${index}" onclick="setActiveAlbumThumb(${index})">${media}</button>`;
                    }).join('');
                } else {
                    itemsContainer.innerHTML = '<div class="text-white py-5">Chưa có ảnh hoặc video trong album này.</div>';
                    thumbsContainer.innerHTML = '';
                    if (deletePhotoBtn) deletePhotoBtn.style.display = 'none';
                }
            });
    }

    // Hàm xóa ảnh trực tiếp từ modal Album
    window.deleteCurrentAlbumPhoto = function() {
        const activeItem = document.querySelector('#albumCarousel .carousel-item.active');
        if (!activeItem) return;
        const imageId = activeItem.getAttribute('data-image-id');
        if (!imageId) {
            alert("Không tìm thấy thông tin ảnh.");
            return;
        }

        if (confirm("Bạn có chắc chắn muốn xóa ảnh/video này khỏi kỷ niệm?")) {
            fetch(`index.php?url=location/deleteAlbumImageJson&id=${imageId}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert("Đã xóa ảnh thành công!");
                        window.location.reload(); // Tải lại trang để đồng bộ tất cả các tab
                    } else {
                        alert("Xóa thất bại: " + data.message);
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert("Có lỗi xảy ra khi kết nối máy chủ.");
                });
        }
    }

    function openTripGallery(tripId, startPhotoIndex, title = "Album Chuyến đi") {
        document.getElementById('albumTitle').innerText = title;
        const itemsContainer = document.getElementById('albumItems');
        const thumbsContainer = document.getElementById('albumThumbs');
        itemsContainer.innerHTML = '<div class="text-white py-5"><div class="spinner-border text-primary"></div></div>';
        thumbsContainer.innerHTML = '';
        
        // Ẩn nút quản lý và nút xóa đối với album chuyến đi
        const manageLink = document.getElementById('manageAlbumLink');
        if (manageLink) manageLink.style.display = 'none';
        const deletePhotoBtn = document.getElementById('albumDeletePhotoBtn');
        if (deletePhotoBtn) deletePhotoBtn.style.display = 'none';

        var albumModal = new bootstrap.Modal(document.getElementById('albumModal'));
        albumModal.show();
        
        const videoExtensions = ['mp4', 'webm', 'ogg', 'mov'];

        fetch(`index.php?url=location/getTripPhotos&trip_id=${tripId}`)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    itemsContainer.innerHTML = data.map((item, index) => {
                        const ext = item.image_path.split('.').pop().toLowerCase();
                        const isVideo = videoExtensions.includes(ext);
                        
                        if (isVideo) {
                            return `
                                <div class="carousel-item ${index === startPhotoIndex ? 'active' : ''}" data-image-id="${item.id}">
                                    <video controls class="d-block w-100 rounded-4" style="max-height: 70vh; background: #000;">
                                        <source src="${uploadsUrl}/${item.image_path}" type="video/${ext === 'mov' ? 'mp4' : ext}">
                                        Trình duyệt của bạn không hỗ trợ xem video.
                                    </video>
                                </div>
                            `;
                        } else {
                            return `
                                <div class="carousel-item ${index === startPhotoIndex ? 'active' : ''}" data-image-id="${item.id}">
                                    <img src="${uploadsUrl}/${item.image_path}" class="d-block w-100 rounded-4" style="max-height: 70vh; object-fit: contain;">
                                </div>
                            `;
                        }
                    }).join('');
                    thumbsContainer.innerHTML = data.map((item, index) => {
                        const ext = item.image_path.split('.').pop().toLowerCase();
                        const isVideo = videoExtensions.includes(ext);
                        const media = isVideo
                            ? `<video muted><source src="${uploadsUrl}/${item.image_path}" type="video/${ext === 'mov' ? 'mp4' : ext}"></video>`
                            : `<img src="${uploadsUrl}/${item.image_path}" alt="Album item ${index + 1}">`;

                        return `<button class="album-thumb ${index === startPhotoIndex ? 'active' : ''}" type="button" data-bs-target="#albumCarousel" data-bs-slide-to="${index}" onclick="setActiveAlbumThumb(${index})">${media}</button>`;
                    }).join('');
                    
                    // Kích hoạt đúng item trong carousel
                    const carousel = new bootstrap.Carousel(document.getElementById('albumCarousel'));
                    carousel.to(startPhotoIndex);
                } else {
                    itemsContainer.innerHTML = '<div class="text-white py-5">Chưa có ảnh hoặc video trong chuyến đi này.</div>';
                    thumbsContainer.innerHTML = '';
                }
            });
    }

    let albumSlideshow = null;
    function setActiveAlbumThumb(index) {
        document.querySelectorAll('.album-thumb').forEach((thumb, thumbIndex) => {
            thumb.classList.toggle('active', thumbIndex === index);
        });
    }

    document.getElementById('albumCarousel').addEventListener('slid.bs.carousel', function (event) {
        setActiveAlbumThumb(event.to);
    });

    function toggleAlbumSlideshow() {
        const carouselElement = document.getElementById('albumCarousel');
        const icon = document.getElementById('albumPlayIcon');
        if (albumSlideshow) {
            albumSlideshow.pause();
            albumSlideshow = null;
            icon.className = 'bi bi-play-fill';
            return;
        }
        albumSlideshow = bootstrap.Carousel.getOrCreateInstance(carouselElement, {
            interval: 2600,
            ride: false,
            wrap: true,
            touch: true
        });
        albumSlideshow.cycle();
        icon.className = 'bi bi-pause-fill';
    }

    function openFullscreenAlbum() {
        const modalContent = document.querySelector('#albumModal .modal-content');
        if (modalContent && modalContent.requestFullscreen) {
            modalContent.requestFullscreen();
        }
    }

    // Social Functionality
    const inviteLink = window.location.origin + window.location.pathname + "?url=friend/add&token=<?php echo $_SESSION['user_id'] * 12345; ?>";
    
    new QRCode(document.getElementById("qrcode"), {
        text: inviteLink,
        width: 120,
        height: 120,
        colorDark : "#6366f1",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    function copyInviteLink() {
        navigator.clipboard.writeText(inviteLink).then(() => {
            alert("Đã sao chép link mời kết bạn!");
        });
    }

    function openAvatarUploader() {
        const input = document.getElementById('avatarInput');
        if (input) {
            input.click();
        }
    }

    function submitAvatarForm() {
        const form = document.getElementById('avatarUploadForm');
        if (form) {
            form.submit();
        }
    }

    function toggleSpecificFriends(mode) {
        const privacy = document.getElementById(mode + '_privacy').value;
        const container = document.getElementById(mode + 'SpecificFriendsContainer');
        if (container) {
            container.style.display = (privacy === 'specific_friends') ? 'block' : 'none';
        }
    }

    function openEditModal(loc) {
        document.getElementById('edit_id').value = loc.id;
        document.getElementById('edit_place_name').value = loc.place_name;
        document.getElementById('edit_visit_date').value = loc.visit_date;
        document.getElementById('edit_feeling').value = loc.feeling;
        document.getElementById('edit_description').value = loc.description;
        document.getElementById('edit_privacy').value = loc.privacy || 'public';
        document.getElementById('edit_trip_id').value = loc.trip_id || '';
        
        // Reset checkboxes
        const checkboxes = document.querySelectorAll('input[name="visible_friends[]"]');
        checkboxes.forEach(cb => cb.checked = false);

        // Pre-check allowed friends
        if (loc.visible_friends) {
            try {
                let allowedIds = [];
                if (typeof loc.visible_friends === 'string') {
                    allowedIds = JSON.parse(loc.visible_friends);
                } else if (Array.isArray(loc.visible_friends)) {
                    allowedIds = loc.visible_friends;
                }
                if (Array.isArray(allowedIds)) {
                    allowedIds.forEach(id => {
                        const cb = document.getElementById('edit_friend_' + id);
                        if (cb) cb.checked = true;
                    });
                }
            } catch (e) {
                console.error("Error parsing visible_friends", e);
            }
        }
        
        // Toggle specific friends container
        toggleSpecificFriends('edit');

        const currentImageDiv = document.getElementById('edit_current_image');
        if (loc.image) {
            currentImageDiv.innerHTML = `<img src="${uploadsUrl}/${loc.image}" class="img-fluid rounded-3" style="max-height: 100px;">`;
        } else {
            currentImageDiv.innerHTML = '';
        }

        var editModal = new bootstrap.Modal(document.getElementById('editMemoryModal'));
        editModal.show();
    }

    // --- Locket Camera Logic ---
    let cameraStream = null;
    let currentFacingMode = "environment"; // user or environment
    const videoElem = document.getElementById('cameraVideo');
    const canvasElem = document.getElementById('photoPreviewCanvas');
    const ctx = canvasElem.getContext('2d');
    let capturedPhotoBase64 = null;
    let locketLat = null;
    let locketLng = null;
    let locketTripId = null;

    async function openLocketCamera() {
        document.getElementById('locketCameraUI').style.display = 'flex';
        resetCameraUI();
        await startCamera(currentFacingMode);

        // Vị trí được cập nhật liên tục qua watchPosition (updateCurrentPosition)
        if (currentLocationMarker) {
            const pos = currentLocationMarker.getLatLng();
            locketLat = pos.lat;
            locketLng = pos.lng;
            document.getElementById('camLocationStatus').innerHTML = '<i class="bi bi-geo-alt-fill text-success me-1"></i> GPS live';
        } else {
            document.getElementById('camLocationStatus').innerHTML = '<i class="bi bi-geo-alt-fill text-warning me-1"></i> Đang tìm vị trí...';
        }
    }

    function closeLocketCamera() {
        document.getElementById('locketCameraUI').style.display = 'none';
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
            videoElem.srcObject = null;
        }
        locketTripId = null;
    }

    async function startCamera(facingMode) {
        const errorDiv = document.getElementById('cameraError');
        const errorMsg = document.getElementById('cameraErrorMsg');
        errorDiv.style.display = 'none';
        videoElem.style.display = 'block';

        // Dừng stream cũ nếu có
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }

        // Kiểm tra trình duyệt có hỗ trợ không
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            errorMsg.textContent = 'Trình duyệt không hỗ trợ Camera API. Hãy dùng Chrome/Firefox mới nhất.';
            errorDiv.style.display = 'block';
            videoElem.style.display = 'none';
            return;
        }

        // Kiểm tra HTTPS (camera chỉ hoạt động trên HTTPS hoặc localhost)
        if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
            errorMsg.innerHTML = 'Camera yêu cầu kết nối <strong>HTTPS</strong> hoặc chạy trên <strong>localhost</strong>.<br>Hãy truy cập qua <code>http://localhost/...</code>';
            errorDiv.style.display = 'block';
            videoElem.style.display = 'none';
            return;
        }

        try {
            // Thử constraints đơn giản nhất trước
            const constraints = {
                video: {
                    facingMode: { ideal: facingMode },
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            };

            cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
            videoElem.srcObject = cameraStream;
            videoElem.muted = true; // Đảm bảo muted để autoplay hoạt động

            // Bắt buộc gọi play() thủ công
            const playPromise = videoElem.play();
            if (playPromise !== undefined) {
                playPromise
                    .then(() => {
                        console.log('Camera đang phát video thành công!');
                        errorDiv.style.display = 'none';
                    })
                    .catch(playErr => {
                        console.error('Lỗi play():', playErr);
                        // Thử lại không muted
                        videoElem.muted = false;
                        videoElem.play().catch(e => console.error(e));
                    });
            }
        } catch (err) {
            console.error('Camera error:', err);
            videoElem.style.display = 'none';

            // Phân tích lỗi cụ thể
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                errorMsg.innerHTML = 'Bạn đã từ chối quyền camera.<br>Vào <strong>Settings > Site Permissions > Camera</strong> để cấp quyền lại.';
            } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                // Thử lại với facingMode đơn giản hơn
                try {
                    cameraStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                    videoElem.srcObject = cameraStream;
                    videoElem.muted = true;
                    await videoElem.play();
                    console.log('Camera khởi động với fallback constraints!');
                    return;
                } catch (fallbackErr) {
                    errorMsg.innerHTML = 'Không tìm thấy camera trên thiết bị này.';
                }
            } else if (err.name === 'NotReadableError') {
                errorMsg.innerHTML = 'Camera đang được ứng dụng khác sử dụng. Đóng ứng dụng đó và thử lại.';
            } else if (err.name === 'OverconstrainedError') {
                // Thử lại với constraints tối thiểu
                try {
                    cameraStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                    videoElem.srcObject = cameraStream;
                    videoElem.muted = true;
                    await videoElem.play();
                    return;
                } catch (e2) {
                    errorMsg.innerHTML = 'Camera không hỗ trợ cấu hình được yêu cầu.';
                }
            } else {
                errorMsg.innerHTML = 'Lỗi camera: ' + err.message;
            }
            errorDiv.style.display = 'block';
        }
    }

    function switchCamera() {
        currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
        startCamera(currentFacingMode);
    }

    function capturePhoto() {
        canvasElem.width = videoElem.videoWidth;
        canvasElem.height = videoElem.videoHeight;
        
        // Không lật ảnh nữa để giống hệt như những gì nhìn thấy trên camera
        
        ctx.drawImage(videoElem, 0, 0, canvasElem.width, canvasElem.height);
        capturedPhotoBase64 = canvasElem.toDataURL('image/jpeg', 0.8);
        
        // Hide video, show canvas
        videoElem.style.display = 'none';
        canvasElem.style.display = 'block';
        
        // Swap controls
        document.getElementById('cameraControls').style.display = 'none';
        document.getElementById('postControls').style.display = 'flex';
    }

    function retakePhoto() {
        resetCameraUI();
    }

    function resetCameraUI() {
        videoElem.style.display = 'block';
        canvasElem.style.display = 'none';
        document.getElementById('cameraControls').style.display = 'flex';
        document.getElementById('postControls').style.display = 'none';
        document.getElementById('locketCaption').value = '';
        capturedPhotoBase64 = null;
        const overlay = document.getElementById('modalTextOverlay');
        if (overlay) overlay.remove();
    }

    function postLocketPhoto(albumName = '') {
        if (!capturedPhotoBase64) return;
        if (!locketLat || !locketLng) {
            alert("Đang lấy vị trí, vui lòng đợi...");
            return;
        }

        const caption = document.getElementById('locketCaption') ? document.getElementById('locketCaption').value : 'Một khoảnh khắc tuyệt vời';
        const privacyElement = document.getElementById('locketPrivacy');
        const privacy = privacyElement ? privacyElement.value : 'friends';
        
        const postBtn = document.querySelector('button[onclick="postLocketPhoto()"]');
        const oldBtnText = postBtn ? postBtn.innerHTML : '';
        if (postBtn) {
            postBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang đăng...';
            postBtn.disabled = true;
        }

        fetch('index.php?url=location/saveLocket', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                image: capturedPhotoBase64,
                caption: caption,
                lat: locketLat,
                lng: locketLng,
                privacy: privacy,
                album_name: albumName, // Nếu rỗng, tự động lấy ngày
                trip_id: locketTripId  // Truyền trip_id liên kết với chuyến đi
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Save coordinates to sessionStorage so we can focus after reload
                sessionStorage.setItem('just_posted_loc', JSON.stringify({
                    lat: locketLat,
                    lng: locketLng
                }));
                
                closeLocketCamera();
                showToast("Đã đăng khoảnh khắc thành công! Đang tải lại...", "success");
                setTimeout(() => {
                    window.location.href = `index.php?url=location/dashboard&success=1&new_id=${data.location_id}&lat=${locketLat}&lng=${locketLng}`;
                }, 1000);
            } else {
                alert("Lỗi: " + data.message);
                if (postBtn) {
                    postBtn.innerHTML = oldBtnText;
                    postBtn.disabled = false;
                }
            }
        })
        .catch(err => {
            console.error(err);
            postBtn.innerHTML = '<i class="bi bi-send-fill"></i> Đăng ngay lên bản đồ';
            postBtn.disabled = false;
        });
    }

    function saveToAlbumPrompt() {
        const albumName = prompt("Nhập tên album (ví dụ: Hành trình Hà Giang):", "Hành trình - " + new Date().toLocaleDateString('vi-VN'));
        if (albumName) {
            postLocketPhoto(albumName);
        }
    }

    // Auto Refresh markers for Real-time update (Polling every 10s)
    setInterval(() => {
        fetch('index.php?url=location/getUpdates')
            .then(res => res.json())
            .then(data => {
                if(data.has_updates) {
                    console.log("New updates found, reloading...");
                    // For simplicity, we just reload if there's a new post. 
                    // Better approach: fetch new locations and append markers dynamically
                    // window.location.reload(); 
                }
            })
            .catch(e => console.log(e));
    }, 10000);

    // Old AI Chat Widget code removed. Using toggleAIChat instead.

    // Mobile Navigation Logic
    const sidebar = document.querySelector('.sidebar');
    const socialSidebar = document.querySelector('.social-sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function closeAllSidebars() {
        if (sidebar) sidebar.classList.remove('mobile-open');
        if (socialSidebar) socialSidebar.classList.remove('mobile-open');
        if (overlay) overlay.classList.remove('show');
        
        // Reset nav active state to Map
        document.querySelectorAll('.bottom-nav-item').forEach(el => el.classList.remove('active'));
        const mapTab = document.querySelector('.bottom-nav-item:first-child');
        if (mapTab) mapTab.classList.add('active');
        
        // Ensure map layout is correct after resizing
        setTimeout(() => { if (typeof map !== 'undefined' && map) map.invalidateSize(); }, 300);
    }

    if (overlay) {
        overlay.addEventListener('click', closeAllSidebars);
    }

    function switchMobileTab(tab, element) {
        document.querySelectorAll('.bottom-nav-item').forEach(el => el.classList.remove('active'));
        if (element) {
            element.classList.add('active');
        }

        if (tab === 'map') {
            closeAllSidebars();
        } else if (tab === 'profile') {
            if (socialSidebar) socialSidebar.classList.remove('mobile-open');
            if (sidebar) sidebar.classList.add('mobile-open');
            if (overlay) overlay.classList.add('show');
        } else if (tab === 'social') {
            if (sidebar) sidebar.classList.remove('mobile-open');
            if (socialSidebar) socialSidebar.classList.add('mobile-open');
            if (overlay) overlay.classList.add('show');
        }
    }

    // 6. Interactive Map Filtering & Polyline Redrawing
    function toggleMapFilterPanel() {
        const dropdown = document.getElementById('mapFilterDropdown');
        if (dropdown) {
            dropdown.classList.toggle('show');
        }
    }

    function resetAllMapFilters() {
        const feelingFilter = document.getElementById('mapFeelingFilter');
        const tripFilter = document.getElementById('mapTripDropdownFilter');
        if (feelingFilter) feelingFilter.value = '';
        if (tripFilter) tripFilter.value = '';
        applyMapFilterDirect();
    }

    function applyMapFilterDirect() {
        const feelingFilter = document.getElementById('mapFeelingFilter');
        const tripFilter = document.getElementById('mapTripDropdownFilter');
        
        const feelingVal = feelingFilter ? feelingFilter.value : '';
        const tripVal = tripFilter ? tripFilter.value : '';

        const filterBtn = document.getElementById('mapFilterToggleBtn');
        if (filterBtn) {
            if (feelingVal || tripVal) {
                filterBtn.classList.add('active');
            } else {
                filterBtn.classList.remove('active');
            }
        }

        let boundsPoints = [];
        const combinedLocations = savedLocations.concat(friendLocations);

        combinedLocations.forEach(loc => {
            const marker = markers[loc.id];
            if (!marker) return;

            let show = true;
            if (feelingVal && loc.feeling !== feelingVal) show = false;
            if (tripVal && Number(loc.trip_id) !== Number(tripVal)) show = false;

            if (show) {
                if (!map.hasLayer(marker)) {
                    marker.addTo(map);
                }
                boundsPoints.push([loc.latitude, loc.longitude]);
            } else {
                if (map.hasLayer(marker)) {
                    map.removeLayer(marker);
                }
            }
        });

        if (routeLine) {
            map.removeLayer(routeLine);
            routeLine = null;
        }

        const visibleOwnPoints = savedLocations
            .filter(loc => {
                if (!loc.latitude || !loc.longitude) return false;
                let show = true;
                if (feelingVal && loc.feeling !== feelingVal) show = false;
                if (tripVal && Number(loc.trip_id) !== Number(tripVal)) show = false;
                return show;
            })
            .map(loc => [Number(loc.latitude), Number(loc.longitude)]);

        if (visibleOwnPoints.length >= 2) {
            routeLine = L.polyline(visibleOwnPoints, {
                color: '#6366f1',
                weight: 5,
                opacity: 0.8,
                dashArray: '10, 15',
                className: 'marching-ants-path'
            }).addTo(map);
        }

        if (boundsPoints.length > 0) {
            map.fitBounds(boundsPoints, { padding: [50, 50], maxZoom: 16 });
        }
    }

    // Đóng dropdown khi click ra ngoài
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('mapFilterDropdown');
        const toggleBtn = document.getElementById('mapFilterToggleBtn');
        if (dropdown && toggleBtn) {
            if (!dropdown.contains(e.target) && !toggleBtn.contains(e.target)) {
                dropdown.classList.remove('show');
            }
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        if (localStorage.getItem('activeTab')) {
            switchTab(localStorage.getItem('activeTab'));
            setActiveNav('nav-' + localStorage.getItem('activeTab'));
            localStorage.removeItem('activeTab');
        }
    });

</script>




<script>
    // Switch tab content
    function switchTab(tabId) {
        // Tab indicators (top tabs)
        document.querySelectorAll('.tab-item').forEach(el => el.classList.remove('active'));
        const tabEl = document.querySelector(`.tab-item[onclick*="'${tabId}'"]`);
        if (tabEl) tabEl.classList.add('active');

        // Tab content panels
        document.querySelectorAll('.tab-content-section').forEach(el => el.classList.remove('active'));
        const target = document.getElementById('tab-' + tabId);
        if (target) target.classList.add('active');

        // Re-render map tiles if needed
        if (typeof map !== 'undefined') setTimeout(() => map.invalidateSize(), 300);
    }

    // Highlight the correct bottom-nav button
    function setActiveNav(id) {
        document.querySelectorAll('.bottom-nav .nav-item').forEach(el => el.classList.remove('active'));
        const el = document.getElementById(id);
        if (el) el.classList.add('active');
    }

    // Scroll page to top so map is visible
    function scrollToMap() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
        if (typeof map !== 'undefined') setTimeout(() => map.invalidateSize(), 400);
    }

    // --- TRIPS FUNCTIONALITY ---
    function createNewTrip() {
        var modal = new bootstrap.Modal(document.getElementById('createTripModal'));
        modal.show();
    }

    document.querySelector('#createTripModal form').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const submitBtn = this.querySelector('button[type="submit"]');
        const oldText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Đang tạo...';

        fetch('index.php?url=trip/create', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                localStorage.setItem('activeTab', 'trips');
                window.location.reload();
            } else {
                alert("Lỗi: " + data.message);
                submitBtn.disabled = false;
                submitBtn.innerHTML = oldText;
            }
        })
        .catch(err => {
            console.error(err);
            alert("Lỗi kết nối mạng!");
            submitBtn.disabled = false;
            submitBtn.innerHTML = oldText;
        });
    });

    function filterMapByTrip(tripId) {
        window.location.href = 'index.php?url=location/dashboard&trip_id=' + tripId;
    }

    function openInviteModal(tripId) {
        document.getElementById('invite_trip_id').value = tripId;
        var modal = new bootstrap.Modal(document.getElementById('inviteTripModal'));
        modal.show();
    }

    function sendTripInvite(username) {
        const tripId = document.getElementById('invite_trip_id').value;
        if (!username || !username.trim()) {
            alert("Vui lòng nhập Username hợp lệ.");
            return;
        }

        const formData = new FormData();
        formData.append('trip_id', tripId);
        formData.append('username', username.trim());

        fetch('index.php?url=trip/addMember', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert("Mời thành công!");
                bootstrap.Modal.getInstance(document.getElementById('inviteTripModal')).hide();
                // Optionally reload or visually indicate success
            } else {
                alert("Lỗi: " + data.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert("Đã xảy ra lỗi mạng.");
        });
    }

    let searchTimeout;
    function searchUsersForInvite(query) {
        const resultsContainer = document.getElementById('invite_search_results');
        if (!query || query.trim() === '') {
            resultsContainer.innerHTML = '';
            return;
        }

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            fetch('index.php?url=friend/searchUsers&q=' + encodeURIComponent(query))
            .then(res => res.json())
            .then(data => {
                resultsContainer.innerHTML = '';
                if (data.success && data.data.length > 0) {
                    data.data.forEach(user => {
                        resultsContainer.innerHTML += `
                            <div class="list-group-item d-flex justify-content-between align-items-center px-2 py-1 bg-transparent border-0 mb-1 rounded" style="background-color: #f8f9fa!important;">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-placeholder" style="width:24px;height:24px;font-size:10px;border-radius:50%;background:#e2e8f0;display:flex;align-items:center;justify-content:center;">
                                        ${user.username.charAt(0).toUpperCase()}
                                    </div>
                                    <div>
                                        <div class="fw-bold" style="font-size:12px;">${user.full_name}</div>
                                        <div class="text-muted" style="font-size:10px;">@${user.username}</div>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-outline-primary rounded-pill py-0 px-2" style="font-size:10px;" onclick="sendTripInvite('${user.username}')">
                                    Mời
                                </button>
                            </div>
                        `;
                    });
                } else {
                    resultsContainer.innerHTML = '<div class="text-muted small px-2">Không tìm thấy ai.</div>';
                }
            })
            .catch(err => console.error(err));
        }, 300);
    }

    function toggleReactionMenu(btn) {
        // Đóng tất cả các popup khác trước
        document.querySelectorAll('.reaction-popup.show').forEach(p => {
            if (p !== btn.nextElementSibling) p.classList.remove('show');
        });
        // Bật/tắt popup hiện tại
        btn.nextElementSibling.classList.toggle('show');
    }

    // Đóng popup khi click ra ngoài
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.reaction-container')) {
            document.querySelectorAll('.reaction-popup.show').forEach(p => p.classList.remove('show'));
        }
    });

    function toggleLike(locationId, type, btnElement) {
        event.stopPropagation(); // Ngăn click lan ra memory-item
        const formData = new FormData();
        formData.append('location_id', locationId);
        formData.append('reaction_type', type);

        fetch('index.php?url=location/toggleLike', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const iconSpan = btnElement.querySelector('.r-icon');
                const countSpan = btnElement.querySelector('.like-count');
                let count = parseInt(countSpan.innerText);

                let iconHtml = '<i class="bi bi-heart"></i>';
                let color = '#64748b';
                
                if (data.action === 'liked') {
                    count++;
                } else if (data.action === 'unliked') {
                    count = Math.max(0, count - 1);
                }

                if (data.action === 'liked' || data.action === 'updated') {
                    if (data.type === 'like') { iconHtml = '👍'; color = '#3b5998'; }
                    else if (data.type === 'heart') { iconHtml = '<i class="bi bi-heart-fill"></i>'; color = '#ef4444'; }
                    else if (data.type === 'haha') { iconHtml = '😂'; color = '#f59e0b'; }
                    else if (data.type === 'wow') { iconHtml = '😮'; color = '#f59e0b'; }
                    else if (data.type === 'sad') { iconHtml = '😢'; color = '#f59e0b'; }
                    
                    // Trigger emoji burst animation
                    createEmojiBurst(btnElement, data.type);
                }

                iconSpan.innerHTML = iconHtml;
                btnElement.style.color = color;
                countSpan.innerText = count;
                btnElement.nextElementSibling.classList.remove('show');
            } else {
                console.error(data.message);
            }
        })
        .catch(err => console.error(err));
    }
</script>


    <!-- Floating Map Button (Mobile only) -->
    <div class="mobile-map-fab" onclick="scrollToMap()">
        <i class="bi bi-map-fill"></i>
    </div>

    <!-- Floating AI Chat -->
    <div class="ai-chat-fab" onclick="toggleAIChat()">
        <i class="bi bi-robot"></i>
    </div>
    
    <div class="ai-chat-window" id="aiChatWindow">
        <div class="ai-chat-header">
            <div><i class="bi bi-robot"></i> Travel Memory AI</div>
            <div class="close-btn" onclick="toggleAIChat()"><i class="bi bi-x-lg"></i></div>
        </div>
        <div class="ai-chat-body" id="aiChatBody">
            <div class="ai-msg bot">Chào bạn! Tôi có thể giúp gì cho chuyến đi của bạn? (Gợi ý lộ trình, phương tiện đi lại, đặc sản, lịch trình...)</div>
        </div>
        <div class="ai-chat-quick-chips">
            <div class="ai-chat-chip" onclick="setAIChatQuestion('Lịch trình phượt Hà Nội - Hà Giang 3 ngày')">Lộ trình Hà Giang</div>
            <div class="ai-chat-chip" onclick="setAIChatQuestion('Từ Sài Gòn đi Đà Lạt bằng xe máy hết mấy tiếng?')">Sài Gòn - Đà Lạt</div>
            <div class="ai-chat-chip" onclick="setAIChatQuestion('Lịch trình 3 ngày 2 đêm Đà Nẵng ăn uống check-in')">Tour Đà Nẵng</div>
            <div class="ai-chat-chip" onclick="setAIChatQuestion('Đặc sản gì ngon nên thử ở Hải Dương?')">Ẩm thực Hải Dương</div>
            <div class="ai-chat-chip" onclick="setAIChatQuestion('Viết hộ caption chill ghim lên bản đồ')">Caption du lịch</div>
        </div>
        <div class="ai-chat-input">
            <input type="text" id="aiChatInput" placeholder="Hỏi AI..." onkeypress="if(event.key === 'Enter') sendAIMessage()">
            <button onclick="sendAIMessage()"><i class="bi bi-send-fill"></i></button>
        </div>
    </div>
    
    <script>
    function toggleAIChat() {
        const win = document.getElementById('aiChatWindow');
        if (win) {
            win.classList.toggle('active');
            win.classList.toggle('open');
            if (win.classList.contains('active') || win.classList.contains('open')) {
                const input = document.getElementById('aiChatInput');
                if (input) {
                    setTimeout(() => input.focus(), 100);
                }
            }
        }
    }

    function setAIChatQuestion(text) {
        const input = document.getElementById('aiChatInput');
        if (input) {
            input.value = text;
            input.focus();
        }
    }
    
    function sendAIMessage() {
        const input = document.getElementById('aiChatInput');
        const text = input.value.trim();
        if(!text) return;
        
        const body = document.getElementById('aiChatBody');
        
        // Add User Message
        const userMsg = document.createElement('div');
        userMsg.className = 'ai-msg user';
        userMsg.textContent = text;
        body.appendChild(userMsg);
        
        input.value = '';
        body.scrollTop = body.scrollHeight;
        
        // Add Bot Loading
        const botMsg = document.createElement('div');
        botMsg.className = 'ai-msg bot';
        botMsg.innerHTML = '<i>Đang suy nghĩ...</i>';
        body.appendChild(botMsg);
        body.scrollTop = body.scrollHeight;
        
        // Lấy tọa độ GPS hiện tại từ các ô input ẩn nếu có
        const latVal = document.getElementById('lat') ? document.getElementById('lat').value : '';
        const lngVal = document.getElementById('lng') ? document.getElementById('lng').value : '';
        
        // Gọi API thật của AiController -> ask
        setTimeout(() => {
            fetch('index.php?url=ai/ask', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    question: text,
                    latitude: latVal,
                    longitude: lngVal
                })
            })
            .then(res => res.json())
            .then(data => {
                botMsg.innerHTML = data.success ? linkifyText(data.message) : 'Có lỗi: ' + escapeHTMLText(data.message);
                body.scrollTop = body.scrollHeight;
            })
            .catch(() => {
                botMsg.innerHTML = 'Không thể kết nối đến dịch vụ AI. Vui lòng thử lại sau.';
                body.scrollTop = body.scrollHeight;
            });
        }, 1000);
    }

    function escapeHTMLText(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function linkifyText(text) {
        const escaped = escapeHTMLText(text);
        // Replace newlines with <br> and format markdown bullet points slightly
        let formatted = escaped.replace(/\n/g, '<br>');
        // Bold markdown format
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');
        // Link format
        return formatted.replace(/(https?:\/\/[^\s<]+)/g, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>');
    }

    // Lightbox Implementation
    let lightboxMediaList = [];
    let lightboxCurrentIndex = 0;

    function initLightbox() {
        const mediaSelectors = '.memory-img, .media-preview, .feed-image, .album-cell img, .album-cell video';
        document.body.addEventListener('click', function(e) {
            const target = e.target.closest(mediaSelectors);
            if (!target) return;
            
            e.preventDefault();
            e.stopPropagation();
            
            const allMedia = Array.from(document.querySelectorAll(mediaSelectors));
            lightboxMediaList = allMedia.map((el) => {
                const isVid = el.tagName.toLowerCase() === 'video' || el.closest('.album-badge') || el.closest('.media-thumb--video');
                let src = el.src;
                if (el.tagName.toLowerCase() === 'video') {
                    const source = el.querySelector('source');
                    src = source ? source.src : el.src;
                }
                
                let caption = "";
                const card = el.closest('.memory-item, .album-item, .feed-card');
                if (card) {
                    const heading = card.querySelector('h6, .fw-bold');
                    if (heading) caption = heading.innerText;
                }
                
                return { src, isVid, caption };
            });
            
            lightboxCurrentIndex = allMedia.indexOf(target);
            if (lightboxCurrentIndex === -1) {
                const sourceEl = target.querySelector('source');
                const targetSrc = target.tagName.toLowerCase() === 'video' ? ((sourceEl ? sourceEl.src : null) || target.src) : target.src;
                lightboxCurrentIndex = lightboxMediaList.findIndex(m => m.src === targetSrc);
            }
            
            showLightbox();
        });
    }

    function showLightbox() {
        let lightbox = document.getElementById('custom-lightbox');
        if (!lightbox) {
            lightbox = document.createElement('div');
            lightbox.id = 'custom-lightbox';
            lightbox.className = 'custom-lightbox';
            lightbox.innerHTML = `
                <span class="lightbox-close" onclick="closeLightbox()">&times;</span>
                <span class="lightbox-arrow lightbox-arrow-left" onclick="prevLightboxImage()"><i class="bi bi-chevron-left"></i></span>
                <span class="lightbox-arrow lightbox-arrow-right" onclick="nextLightboxImage()"><i class="bi bi-chevron-right"></i></span>
                <div class="lightbox-content-wrapper">
                    <img id="lightbox-img" src="" alt="" />
                    <video id="lightbox-video" src="" controls style="display:none;"></video>
                    <div class="lightbox-caption" id="lightbox-caption"></div>
                </div>
            `;
            document.body.appendChild(lightbox);
            
            // Swipe gestures
            let startX = 0;
            lightbox.addEventListener('touchstart', e => startX = e.touches[0].clientX, {passive: true});
            lightbox.addEventListener('touchend', e => {
                const diffX = e.changedTouches[0].clientX - startX;
                if (diffX > 50) prevLightboxImage();
                else if (diffX < -50) nextLightboxImage();
            }, {passive: true});
            
            lightbox.onclick = function(e) {
                if (e.target === lightbox || e.target.classList.contains('lightbox-content-wrapper')) {
                    closeLightbox();
                }
            };
        }
        
        lightbox.style.display = 'flex';
        lightbox.offsetHeight; // Force reflow
        lightbox.classList.add('show');
        
        updateLightboxContent();
        
        document.onkeydown = function(e) {
            if (e.key === 'Escape') closeLightbox();
            else if (e.key === 'ArrowLeft') prevLightboxImage();
            else if (e.key === 'ArrowRight') nextLightboxImage();
        };
    }

    function updateLightboxContent() {
        const media = lightboxMediaList[lightboxCurrentIndex];
        if (!media) return;
        
        const img = document.getElementById('lightbox-img');
        const video = document.getElementById('lightbox-video');
        const caption = document.getElementById('lightbox-caption');
        
        if (media.isVid) {
            img.style.display = 'none';
            video.style.display = 'block';
            video.src = media.src;
            video.play().catch(() => {});
        } else {
            video.style.display = 'none';
            video.src = "";
            img.style.display = 'block';
            img.src = media.src;
        }
        
        caption.innerText = media.caption || "";
    }

    function closeLightbox() {
        const lightbox = document.getElementById('custom-lightbox');
        if (!lightbox) return;
        lightbox.classList.remove('show');
        const video = document.getElementById('lightbox-video');
        if (video) {
            video.pause();
            video.src = "";
        }
        setTimeout(() => {
            lightbox.style.display = 'none';
        }, 300);
        document.onkeydown = null;
    }

    function nextLightboxImage() {
        if (lightboxMediaList.length <= 1) return;
        const video = document.getElementById('lightbox-video');
        if (video) video.pause();
        lightboxCurrentIndex = (lightboxCurrentIndex + 1) % lightboxMediaList.length;
        updateLightboxContent();
    }

    function prevLightboxImage() {
        if (lightboxMediaList.length <= 1) return;
        const video = document.getElementById('lightbox-video');
        if (video) video.pause();
        lightboxCurrentIndex = (lightboxCurrentIndex - 1 + lightboxMediaList.length) % lightboxMediaList.length;
        updateLightboxContent();
    }

    // ==========================================
    // LOCKET & BUMP INTERACTIVE UPGRADES
    // ==========================================

    // 5. Travel Stats & Achievements Badges Logic
    const friendsCount = <?php echo count($friends ?? []); ?>;

    function calculateAchievements() {
        if (!savedLocations) return;

        // a. Explorer Badge (3+ checkins)
        const badgeExplorer = document.getElementById('badge-explorer');
        if (badgeExplorer) {
            if (savedLocations.length >= 3) {
                badgeExplorer.classList.remove('locked');
                badgeExplorer.classList.add('unlocked');
                const lockIcon = badgeExplorer.querySelector('.badge-locked-overlay');
                if (lockIcon) lockIcon.remove();
            }
        }

        // b. Locket Master Badge (3+ lockets)
        const badgeLocket = document.getElementById('badge-locket');
        if (badgeLocket) {
            const locketCount = savedLocations.filter(loc => loc.image && loc.image.includes('_locket_')).length;
            if (locketCount >= 3) {
                badgeLocket.classList.remove('locked');
                badgeLocket.classList.add('unlocked');
                const lockIcon = badgeLocket.querySelector('.badge-locked-overlay');
                if (lockIcon) lockIcon.remove();
            }
        }

        // c. Night Owl Badge (Check-in between 22:00 and 05:00)
        const badgeNight = document.getElementById('badge-night');
        if (badgeNight) {
            let hasNight = false;
            savedLocations.forEach(loc => {
                if (loc.created_at) {
                    const match = loc.created_at.match(/\s(\d{2}):/);
                    if (match) {
                        const hour = parseInt(match[1]);
                        if (hour >= 22 || hour < 5) hasNight = true;
                    }
                }
            });
            if (hasNight) {
                badgeNight.classList.remove('locked');
                badgeNight.classList.add('unlocked');
                const lockIcon = badgeNight.querySelector('.badge-locked-overlay');
                if (lockIcon) lockIcon.remove();
            }
        }

        // d. Alpinist Climber Badge (Mountain, Pass, etc.)
        const badgeClimber = document.getElementById('badge-climber');
        if (badgeClimber) {
            const hasClimbed = savedLocations.some(loc => {
                const place = (loc.place_name || '').toLowerCase();
                const desc = (loc.description || '').toLowerCase();
                const keywords = ['núi', 'đèo', 'đỉnh', 'dốc', 'sapa', 'hà giang', 'fansipan', 'mã pí lèng', 'khau phạ', 'ô quy hồ', 'tây bắc'];
                const keywordsMatches = keywords.some(k => place.includes(k) || desc.includes(k));
                return keywordsMatches || loc.feeling === 'Khám phá';
            });
            if (hasClimbed) {
                badgeClimber.classList.remove('locked');
                badgeClimber.classList.add('unlocked');
                const lockIcon = badgeClimber.querySelector('.badge-locked-overlay');
                if (lockIcon) lockIcon.remove();
            }
        }

        // e. Companion Badge (1+ friends)
        const badgeTriky = document.getElementById('badge-triky');
        if (badgeTriky) {
            if (friendsCount >= 1) {
                badgeTriky.classList.remove('locked');
                badgeTriky.classList.add('unlocked');
                const lockIcon = badgeTriky.querySelector('.badge-locked-overlay');
                if (lockIcon) lockIcon.remove();
            }
        }
    }

    // (applyMapFilter moved to map script block)

    // 1. Leaflet Marker Ripple
    function triggerBumpRipple(lat, lng) {
        if (!map) return;
        const rippleIcon = L.divIcon({
            className: 'ripple-container',
            html: '<div class="leaflet-marker-ripple"></div>',
            iconSize: [0, 0]
        });
        const rippleMarker = L.marker([lat, lng], { icon: rippleIcon }).addTo(map);
        setTimeout(() => {
            map.removeLayer(rippleMarker);
        }, 1000);
    }

    // 2. Floating Emoji Burst
    function createEmojiBurst(btnElement, type) {
        if (!btnElement) return;
        const rect = btnElement.getBoundingClientRect();
        const startX = rect.left + rect.width / 2;
        const startY = rect.top + rect.height / 2;

        let emoji = '❤️';
        if (type === 'like') emoji = '👍';
        else if (type === 'heart') emoji = '❤️';
        else if (type === 'haha') emoji = '😂';
        else if (type === 'wow') emoji = '😮';
        else if (type === 'sad') emoji = '😢';

        for (let i = 0; i < 8; i++) {
            const el = document.createElement('div');
            el.className = 'floating-emoji';
            el.textContent = emoji;
            el.style.left = `${startX}px`;
            el.style.top = `${startY}px`;
            
            const drift = (Math.random() - 0.5) * 120;
            const rotate = (Math.random() - 0.5) * 60;
            const delay = Math.random() * 0.3;
            const size = 18 + Math.random() * 14;
            
            el.style.setProperty('--drift', `${drift}px`);
            el.style.setProperty('--rotate', `${rotate}deg`);
            el.style.animationDelay = `${delay}s`;
            el.style.fontSize = `${size}px`;

            document.body.appendChild(el);

            el.addEventListener('animationend', () => {
                el.remove();
            });
        }
    }

    // 3. Locket Mini Widget Camera Logic
    let widgetStream = null;
    let widgetCapturedBase64 = null;

    async function toggleLocketWidgetCamera() {
        const video = document.getElementById('widgetVideo');
        const placeholder = document.getElementById('widgetPlaceholder');
        const capturedImg = document.getElementById('widgetCapturedImg');
        const actionPanel = document.getElementById('widgetActionPanel');
        const controls = document.getElementById('widgetControls');
        const captionArea = document.getElementById('widgetCaptionArea');

        if (widgetStream) {
            return; 
        }

        // Initialize location
        if (currentLocationMarker) {
            const pos = currentLocationMarker.getLatLng();
            locketLat = pos.lat;
            locketLng = pos.lng;
        } else if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                pos => {
                    locketLat = pos.coords.latitude;
                    locketLng = pos.coords.longitude;
                },
                err => {
                    console.warn("Could not get geo location", err);
                }
            );
        }

        try {
            controls.innerHTML = `<h6 class="fw-bold mb-1" style="font-size: 13px;">Camera đang bật...</h6><p class="text-muted mb-0 small text-truncate">Chạm nút chụp tròn bên phải</p>`;
            widgetStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: 320, height: 320 }
            });
            video.srcObject = widgetStream;
            video.style.display = 'block';
            placeholder.style.display = 'none';
            capturedImg.style.display = 'none';

            actionPanel.style.setProperty('display', 'flex', 'important');
            document.getElementById('widgetSnapBtn').style.display = 'flex';
            document.getElementById('widgetPostBtn').style.display = 'none';
            document.getElementById('widgetResetBtn').style.display = 'none';
            captionArea.style.display = 'none';
        } catch (err) {
            console.error("Camera access failed", err);
            controls.innerHTML = `<h6 class="fw-bold mb-1 text-danger" style="font-size: 13px;">Lỗi Camera!</h6><p class="text-muted mb-0 small">Không thể truy cập camera của bạn.</p>`;
        }
    }

    function snapWidgetPhoto() {
        const video = document.getElementById('widgetVideo');
        const canvas = document.getElementById('widgetCanvas');
        const capturedImg = document.getElementById('widgetCapturedImg');
        const snapBtn = document.getElementById('widgetSnapBtn');
        const postBtn = document.getElementById('widgetPostBtn');
        const resetBtn = document.getElementById('widgetResetBtn');
        const captionArea = document.getElementById('widgetCaptionArea');
        const controls = document.getElementById('widgetControls');

        if (!widgetStream) return;

        const size = Math.min(video.videoWidth, video.videoHeight) || 320;
        canvas.width = size;
        canvas.height = size;
        const ctx = canvas.getContext('2d');

        const sx = (video.videoWidth - size) / 2;
        const sy = (video.videoHeight - size) / 2;

        ctx.translate(size, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, sx, sy, size, size, 0, 0, size, size);
        ctx.setTransform(1, 0, 0, 1, 0, 0);

        widgetCapturedBase64 = canvas.toDataURL('image/jpeg', 0.8);
        capturedImg.src = widgetCapturedBase64;
        capturedImg.style.display = 'block';
        video.style.display = 'none';

        if (widgetStream) {
            widgetStream.getTracks().forEach(track => track.stop());
            widgetStream = null;
        }

        snapBtn.style.display = 'none';
        postBtn.style.display = 'flex';
        resetBtn.style.display = 'flex';
        captionArea.style.display = 'block';

        controls.innerHTML = `<h6 class="fw-bold mb-1" style="font-size: 13px;">Tuyệt vời!</h6><p class="text-muted mb-0 small">Bạn có thể thêm chữ và đăng ngay.</p>`;
    }

    function resetWidgetLocket() {
        const video = document.getElementById('widgetVideo');
        const placeholder = document.getElementById('widgetPlaceholder');
        const capturedImg = document.getElementById('widgetCapturedImg');
        const snapBtn = document.getElementById('widgetSnapBtn');
        const postBtn = document.getElementById('widgetPostBtn');
        const resetBtn = document.getElementById('widgetResetBtn');
        const captionArea = document.getElementById('widgetCaptionArea');
        const controls = document.getElementById('widgetControls');

        widgetCapturedBase64 = null;
        capturedImg.style.display = 'none';
        video.style.display = 'none';
        placeholder.style.display = 'flex';
        snapBtn.style.display = 'flex';
        postBtn.style.display = 'none';
        resetBtn.style.display = 'none';
        captionArea.style.display = 'none';

        const textOverlay = document.getElementById('widgetTextOverlay');
        if (textOverlay) textOverlay.remove();
        document.getElementById('widgetCaptionInput').value = '';

        controls.innerHTML = `<h6 class="fw-bold mb-1" style="font-size: 13px;">Chụp nhanh Locket!</h6><p class="text-muted mb-0 small text-truncate">Chạm ống kính tròn để bật camera</p>`;

        if (widgetStream) {
            widgetStream.getTracks().forEach(track => track.stop());
            widgetStream = null;
        }
    }

    function postWidgetLocket() {
        if (!widgetCapturedBase64) return;
        if (!locketLat || !locketLng) {
            if (map) {
                const center = map.getCenter();
                locketLat = center.lat;
                locketLng = center.lng;
            } else {
                alert("Vui lòng đợi thiết bị cập nhật vị trí GPS...");
                return;
            }
        }

        const captionInput = document.getElementById('widgetCaptionInput');
        const caption = captionInput ? captionInput.value.trim() : 'Locket khoảnh khắc';
        const tripIdVal = document.getElementById('widgetTripId').value;

        const postBtn = document.getElementById('widgetPostBtn');
        const oldHtml = postBtn.innerHTML;
        postBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        postBtn.disabled = true;

        fetch('index.php?url=location/saveLocket', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                image: widgetCapturedBase64,
                caption: caption,
                lat: locketLat,
                lng: locketLng,
                privacy: 'friends',
                album_name: '',
                trip_id: tripIdVal || null
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Save coordinates to sessionStorage
                sessionStorage.setItem('just_posted_loc', JSON.stringify({
                    lat: locketLat,
                    lng: locketLng
                }));

                resetWidgetLocket();
                showToast("Đã đăng khoảnh khắc thành công! Đang tải lại...", "success");
                setTimeout(() => {
                    window.location.href = `index.php?url=location/dashboard&success=1&new_id=${data.location_id}&lat=${locketLat}&lng=${locketLng}`;
                }, 1000);
            } else {
                alert("Lỗi khi đăng: " + data.message);
                postBtn.innerHTML = oldHtml;
                postBtn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert("Lỗi mạng!");
            postBtn.innerHTML = oldHtml;
            postBtn.disabled = false;
        });
    }

    // 4. Tinder-Style Card Stack Swipe mechanics
    function initCardSwipe() {
        const containers = document.querySelectorAll('.locket-stack-container');
        containers.forEach(container => {
            if (container.dataset.swipeInitialized) return;
            container.dataset.swipeInitialized = 'true';

            let cards = Array.from(container.querySelectorAll('.locket-stack-card'));
            if (cards.length <= 1) return;

            setupTopCardSwipe(container);
        });
    }

    function setupTopCardSwipe(container) {
        let cards = Array.from(container.querySelectorAll('.locket-stack-card'));
        if (cards.length <= 1) return;

        cards.sort((a, b) => parseInt(a.dataset.index) - parseInt(b.dataset.index));

        const topCard = cards[0];
        let isDragging = false;
        let startX = 0;
        let startY = 0;
        let currentX = 0;
        let currentY = 0;

        const origTransform = topCard.style.transform || '';

        const onStart = (clientX, clientY) => {
            isDragging = true;
            startX = clientX;
            startY = clientY;
            topCard.style.transition = 'none';
            topCard.style.cursor = 'grabbing';
        };

        const onMove = (clientX, clientY) => {
            if (!isDragging) return;
            currentX = clientX - startX;
            currentY = clientY - startY;

            const rotate = currentX * 0.08;
            topCard.style.transform = `translate(${currentX}px, ${currentY}px) rotate(${rotate}deg)`;
        };

        const onEnd = () => {
            if (!isDragging) return;
            isDragging = false;
            topCard.style.cursor = 'grab';

            const threshold = 100;
            if (Math.abs(currentX) > threshold) {
                const direction = currentX > 0 ? 1 : -1;
                topCard.style.transition = 'transform 0.4s cubic-bezier(0.1, 0.8, 0.3, 1)';
                topCard.style.transform = `translate(${direction * window.innerWidth}px, ${currentY}px) rotate(${direction * 45}deg)`;

                setTimeout(() => {
                    container.appendChild(topCard);
                    updateStackLayout(container);
                    setupTopCardSwipe(container);
                }, 400);
            } else {
                topCard.style.transition = 'transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
                topCard.style.transform = origTransform;
            }

            currentX = 0;
            currentY = 0;
        };

        const clone = topCard.cloneNode(true);
        topCard.parentNode.replaceChild(clone, topCard);

        clone.addEventListener('mousedown', (e) => {
            if (e.target.closest('button')) return;
            let clickTime = Date.now();
            onStart(e.clientX, e.clientY);

            const upHandler = () => {
                document.removeEventListener('mousemove', moveHandler);
                document.removeEventListener('mouseup', upHandler);
                onEnd();
                
                if (Date.now() - clickTime < 250 && Math.abs(currentX) < 10 && Math.abs(currentY) < 10) {
                    const tripId = container.dataset.tripId;
                    const idx = clone.dataset.index;
                    const cardEl = container.closest('.trip-album-card');
                    const titleEl = cardEl ? cardEl.querySelector('.fw-bold') : null;
                    const title = titleEl ? titleEl.textContent.trim() : 'Album Chuyến đi';
                    openTripGallery(tripId, parseInt(idx), title);
                }
            };

            const moveHandler = (moveEvt) => {
                onMove(moveEvt.clientX, moveEvt.clientY);
            };

            document.addEventListener('mousemove', moveHandler);
            document.addEventListener('mouseup', upHandler);
        });

        clone.addEventListener('touchstart', (e) => {
            if (e.target.closest('button')) return;
            let clickTime = Date.now();
            const touch = e.touches[0];
            onStart(touch.clientX, touch.clientY);

            const moveHandler = (moveEvt) => {
                const t = moveEvt.touches[0];
                onMove(t.clientX, t.clientY);
            };

            const endHandler = () => {
                clone.removeEventListener('touchmove', moveHandler);
                clone.removeEventListener('touchend', endHandler);
                onEnd();

                if (Date.now() - clickTime < 250 && Math.abs(currentX) < 15 && Math.abs(currentY) < 15) {
                    const tripId = container.dataset.tripId;
                    const idx = clone.dataset.index;
                    const cardEl = container.closest('.trip-album-card');
                    const titleEl = cardEl ? cardEl.querySelector('.fw-bold') : null;
                    const title = titleEl ? titleEl.textContent.trim() : 'Album Chuyến đi';
                    openTripGallery(tripId, parseInt(idx), title);
                }
            };

            clone.addEventListener('touchmove', moveHandler, { passive: true });
            clone.addEventListener('touchend', endHandler, { passive: true });
        }, { passive: true });
    }

    function updateStackLayout(container) {
        let cards = Array.from(container.querySelectorAll('.locket-stack-card'));
        const count = cards.length;
        
        cards.forEach((card, idx) => {
            const z_index = count - idx;
            const offset_y = idx * 6;
            const scale = 1 - (idx * 0.04);
            const rot = (idx % 2 == 0 ? 1 : -1) * Math.min(3, idx * 1.5);

            card.style.transition = 'transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.2)';
            card.style.zIndex = z_index;
            card.style.transform = `translateY(${offset_y}px) scale(${scale}) rotate(${rot}deg)`;
            
            const hint = card.querySelector('.position-absolute');
            if (hint) {
                if (idx === 0) {
                    hint.style.display = 'flex';
                } else {
                    hint.style.display = 'none';
                }
            }
        });
    }

    // Draggable caption overlays
    document.addEventListener('DOMContentLoaded', () => {
        // Widget Camera text input
        const widgetCaptionInput = document.getElementById('widgetCaptionInput');
        if (widgetCaptionInput) {
            widgetCaptionInput.addEventListener('input', function(e) {
                const text = e.target.value;
                let overlay = document.getElementById('widgetTextOverlay');
                const circle = document.querySelector('.locket-preview-circle');
                if (!circle) return;

                if (!overlay && text) {
                    overlay = document.createElement('div');
                    overlay.id = 'widgetTextOverlay';
                    overlay.style.position = 'absolute';
                    overlay.style.left = '0';
                    overlay.style.right = '0';
                    overlay.style.top = '50%';
                    overlay.style.transform = 'translateY(-50%)';
                    overlay.style.background = 'rgba(0,0,0,0.65)';
                    overlay.style.color = '#fff';
                    overlay.style.fontSize = '8px';
                    overlay.style.fontWeight = 'bold';
                    overlay.style.textAlign = 'center';
                    overlay.style.padding = '2px 4px';
                    overlay.style.pointerEvents = 'auto';
                    overlay.style.cursor = 'grab';
                    overlay.style.userSelect = 'none';
                    overlay.style.zIndex = '5';
                    
                    let isDragging = false;
                    let startY = 0;
                    let startTop = 50;
                    
                    const onStart = (clientY) => {
                        isDragging = true;
                        startY = clientY;
                        const styleTop = overlay.style.top;
                        startTop = parseFloat(styleTop) || 50;
                        overlay.style.cursor = 'grabbing';
                    };
                    
                    const onMove = (clientY) => {
                        if (!isDragging) return;
                        const rect = circle.getBoundingClientRect();
                        const deltaY = clientY - startY;
                        const deltaPercent = (deltaY / rect.height) * 100;
                        let newTop = startTop + deltaPercent;
                        newTop = Math.max(15, Math.min(85, newTop));
                        overlay.style.top = `${newTop}%`;
                    };
                    
                    const onEnd = () => {
                        isDragging = false;
                        overlay.style.cursor = 'grab';
                    };
                    
                    overlay.addEventListener('mousedown', (evt) => {
                        evt.stopPropagation();
                        onStart(evt.clientY);
                    });
                    document.addEventListener('mousemove', (evt) => {
                        onMove(evt.clientY);
                    });
                    document.addEventListener('mouseup', () => {
                        onEnd();
                    });
                    
                    overlay.addEventListener('touchstart', (evt) => {
                        evt.stopPropagation();
                        if (evt.touches.length > 0) onStart(evt.touches[0].clientY);
                    }, { passive: true });
                    document.addEventListener('touchmove', (evt) => {
                        if (evt.touches.length > 0) onMove(evt.touches[0].clientY);
                    }, { passive: true });
                    document.addEventListener('touchend', () => {
                        onEnd();
                    });
                    
                    circle.appendChild(overlay);
                }
                
                if (overlay) {
                    overlay.textContent = text;
                    if (!text) overlay.remove();
                }
            });
        }

        // Full Screen Modal Camera text input
        const locketCaptionInput = document.getElementById('locketCaption');
        if (locketCaptionInput) {
            locketCaptionInput.addEventListener('input', function(e) {
                const text = e.target.value;
                let overlay = document.getElementById('modalTextOverlay');
                const viewfinder = document.querySelector('.camera-viewfinder');
                if (!viewfinder) return;

                if (!overlay && text) {
                    overlay = document.createElement('div');
                    overlay.id = 'modalTextOverlay';
                    overlay.style.position = 'absolute';
                    overlay.style.left = '0';
                    overlay.style.right = '0';
                    overlay.style.top = '50%';
                    overlay.style.transform = 'translateY(-50%)';
                    overlay.style.background = 'rgba(0,0,0,0.65)';
                    overlay.style.color = '#fff';
                    overlay.style.fontSize = '18px';
                    overlay.style.fontWeight = 'bold';
                    overlay.style.textAlign = 'center';
                    overlay.style.padding = '8px 12px';
                    overlay.style.pointerEvents = 'auto';
                    overlay.style.cursor = 'grab';
                    overlay.style.userSelect = 'none';
                    overlay.style.zIndex = '10';
                    
                    let isDragging = false;
                    let startY = 0;
                    let startTop = 50;
                    
                    const onStart = (clientY) => {
                        isDragging = true;
                        startY = clientY;
                        const styleTop = overlay.style.top;
                        startTop = parseFloat(styleTop) || 50;
                        overlay.style.cursor = 'grabbing';
                    };
                    
                    const onMove = (clientY) => {
                        if (!isDragging) return;
                        const rect = viewfinder.getBoundingClientRect();
                        const deltaY = clientY - startY;
                        const deltaPercent = (deltaY / rect.height) * 100;
                        let newTop = startTop + deltaPercent;
                        newTop = Math.max(10, Math.min(90, newTop));
                        overlay.style.top = `${newTop}%`;
                    };
                    
                    const onEnd = () => {
                        isDragging = false;
                        overlay.style.cursor = 'grab';
                    };
                    
                    overlay.addEventListener('mousedown', (evt) => {
                        evt.stopPropagation();
                        onStart(evt.clientY);
                    });
                    document.addEventListener('mousemove', (evt) => {
                        onMove(evt.clientY);
                    });
                    document.addEventListener('mouseup', () => {
                        onEnd();
                    });
                    
                    overlay.addEventListener('touchstart', (evt) => {
                        evt.stopPropagation();
                        if (evt.touches.length > 0) onStart(evt.touches[0].clientY);
                    }, { passive: true });
                    document.addEventListener('touchmove', (evt) => {
                        if (evt.touches.length > 0) onMove(evt.touches[0].clientY);
                    }, { passive: true });
                    document.addEventListener('touchend', () => {
                        onEnd();
                    });
                    
                    viewfinder.appendChild(overlay);
                }
                
                if (overlay) {
                    overlay.textContent = text;
                    if (!text) overlay.remove();
                }
            });
        }
    });

    // Helper to show a premium overlay toast
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `toast-popup px-4 py-2 rounded-pill text-white fw-bold shadow-lg d-flex align-items-center gap-2`;
        toast.style.position = 'fixed';
        toast.style.bottom = '80px';
        toast.style.left = '50%';
        toast.style.transform = 'translateX(-50%) translateY(20px)';
        toast.style.opacity = '0';
        toast.style.transition = 'all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275)';
        toast.style.zIndex = '10000';
        toast.style.background = type === 'success' ? 'rgba(16, 185, 129, 0.95)' : 'rgba(239, 68, 68, 0.95)';
        toast.style.backdropFilter = 'blur(10px)';
        toast.style.border = '1px solid rgba(255,255,255,0.2)';
        toast.style.fontSize = '12px';
        
        const icon = type === 'success' ? '<i class="bi bi-check-circle-fill"></i>' : '<i class="bi bi-exclamation-triangle-fill"></i>';
        toast.innerHTML = `${icon} <span>${message}</span>`;
        
        document.body.appendChild(toast);
        toast.offsetHeight; // force reflow
        toast.style.transform = 'translateX(-50%) translateY(0)';
        toast.style.opacity = '1';
        
        setTimeout(() => {
            toast.style.transform = 'translateX(-50%) translateY(20px)';
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }

    // Initialize lightbox and swipe stacks on load
    document.addEventListener('DOMContentLoaded', () => {
        initLightbox();
        initCardSwipe();
        calculateAchievements();

        // Function to refresh dashboard components dynamically without page reload
        window.refreshDashboardState = function() {
            return fetch('index.php?url=location/dashboard')
                .then(res => res.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // Swap timeline/tabs content
                    const newTimeline = doc.getElementById('tab-timeline');
                    const oldTimeline = document.getElementById('tab-timeline');
                    if (newTimeline && oldTimeline) {
                        oldTimeline.innerHTML = newTimeline.innerHTML;
                    }

                    // Swap profile badge
                    const newBadge = doc.querySelector('.profile-badge');
                    const oldBadge = document.querySelector('.profile-badge');
                    if (newBadge && oldBadge) {
                        oldBadge.innerHTML = newBadge.innerHTML;
                    }

                    // Re-extract locations arrays from script content
                    const scripts = doc.querySelectorAll('script');
                    scripts.forEach(script => {
                        const content = script.textContent;
                        if (content.includes('var savedLocations =')) {
                            const savedMatch = content.match(/var savedLocations = ([^;]+);/);
                            const friendMatch = content.match(/var friendLocations = ([^;]+);/);
                            if (savedMatch) {
                                savedLocations = JSON.parse(savedMatch[1]);
                            }
                            if (friendMatch) {
                                friendLocations = JSON.parse(friendMatch[1]);
                            }
                        }
                    });

                    // Re-draw map markers
                    if (typeof drawMapMarkers === 'function') {
                        drawMapMarkers();
                    }

                    // Re-initialize Tinder swiping for stacked albums
                    if (typeof initCardSwipe === 'function') {
                        const containers = document.querySelectorAll('.locket-stack-container');
                        containers.forEach(c => delete c.dataset.swipeInitialized);
                        initCardSwipe();
                    }

                    // Sync choice elements/select boxes
                    const newTripSelects = doc.querySelectorAll('#addMemoryForm select[name="trip_id"], #editMemoryForm select[name="trip_id"]');
                    newTripSelects.forEach(newSelect => {
                        const targetId = newSelect.closest('form').id;
                        const oldSelect = document.querySelector(`#${targetId} select[name="trip_id"]`);
                        if (oldSelect) {
                            oldSelect.innerHTML = newSelect.innerHTML;
                        }
                    });
                })
                .catch(err => console.error("Error refreshing dashboard state:", err));
        };

        // Submit new memory with AJAX
        const addMemForm = document.getElementById('addMemoryForm');
        if (addMemForm) {
            addMemForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    if (btn.disabled) return;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang đăng...';
                    btn.disabled = true;
                }

                const formData = new FormData(this);
                formData.append('ajax', '1');

                fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.reset();
                        const modalEl = document.getElementById('addMemoryModal');
                        if (modalEl) {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }
                        
                        showToast('Lưu kỷ niệm thành công!');
                        
                        refreshDashboardState().then(() => {
                            if (data.latitude && data.longitude && map) {
                                map.setView([data.latitude, data.longitude], 15);
                                if (typeof triggerBumpRipple === 'function') {
                                    triggerBumpRipple(data.latitude, data.longitude);
                                }
                                if (markers && markers[data.new_id]) {
                                    setTimeout(() => {
                                        markers[data.new_id].openPopup();
                                    }, 800);
                                }
                            }
                        });
                    } else {
                        showToast(data.message || 'Lỗi lưu kỷ niệm', 'error');
                        if (btn) {
                            btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Đăng Kỷ Niệm';
                            btn.disabled = false;
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Có lỗi xảy ra khi gửi yêu cầu.', 'error');
                    if (btn) {
                        btn.innerHTML = '<i class="bi bi-send-fill me-1"></i> Đăng Kỷ Niệm';
                        btn.disabled = false;
                    }
                });
            });
        }

        // Edit memory with AJAX
        const editMemForm = document.getElementById('editMemoryForm');
        if (editMemForm) {
            editMemForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    if (btn.disabled) return;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang cập nhật...';
                    btn.disabled = true;
                }

                const formData = new FormData(this);
                formData.append('ajax', '1');

                fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.reset();
                        const modalEl = document.getElementById('editMemoryModal');
                        if (modalEl) {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }
                        
                        showToast('Cập nhật kỷ niệm thành công!');
                        refreshDashboardState();
                    } else {
                        showToast(data.message || 'Lỗi cập nhật', 'error');
                        if (btn) {
                            btn.innerHTML = 'Lưu Thay Đổi';
                            btn.disabled = false;
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Có lỗi xảy ra khi gửi yêu cầu.', 'error');
                    if (btn) {
                        btn.innerHTML = 'Lưu Thay Đổi';
                        btn.disabled = false;
                    }
                });
            });
        }

        // Create new trip with AJAX
        const createTripForm = document.getElementById('createTripForm');
        if (createTripForm) {
            createTripForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    if (btn.disabled) return;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang tạo...';
                    btn.disabled = true;
                }

                const formData = new FormData(this);
                fetch(this.action, {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.reset();
                        const modalEl = document.getElementById('createTripModal');
                        if (modalEl) {
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            if (modal) modal.hide();
                        }
                        
                        showToast('Tạo chuyến đi thành công!');
                        refreshDashboardState();
                    } else {
                        showToast(data.message || 'Lỗi tạo chuyến đi', 'error');
                        if (btn) {
                            btn.innerHTML = 'Tạo Hành Trình';
                            btn.disabled = false;
                        }
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Có lỗi xảy ra.', 'error');
                    if (btn) {
                        btn.innerHTML = 'Tạo Hành Trình';
                        btn.disabled = false;
                    }
                });
            });
        }

        // Event delegation for deleting memories via AJAX
        document.body.addEventListener('click', function(e) {
            const deleteLink = e.target.closest('a[href*="url=location/delete"]');
            if (deleteLink) {
                e.preventDefault();
                e.stopPropagation();
                if (confirm('Xóa kỷ niệm này?')) {
                    const url = deleteLink.href + '&ajax=1';
                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                showToast('Đã xóa kỷ niệm!');
                                refreshDashboardState();
                            } else {
                                showToast(data.message || 'Lỗi xóa kỷ niệm', 'error');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            showToast('Lỗi kết nối khi xóa', 'error');
                        });
                }
            }
        });
        
        // Lấy thông tin check-in mới từ URL params (Ưu tiên)
        const urlParams = new URLSearchParams(window.location.search);
        const newId = urlParams.get('new_id');
        const newLat = parseFloat(urlParams.get('lat'));
        const newLng = parseFloat(urlParams.get('lng'));

        if (newId && !isNaN(newLat) && !isNaN(newLng)) {
            setTimeout(() => {
                if (map) {
                    // Di chuyển êm dịu, không phóng quá sâu dối mắt
                    map.setView([newLat, newLng], 15);
                    triggerBumpRipple(newLat, newLng);
                    
                    // Tự động mở bong bóng popup của check-in vừa đăng
                    if (markers && markers[newId]) {
                        markers[newId].openPopup();
                    }
                }
            }, 1000);

            // Dọn dẹp query parameters trên URL để URL sạch đẹp và tránh lặp lại khi F5
            const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + "?url=location/dashboard";
            window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
        } else {
            // Fallback sang sessionStorage (dành cho các logic cũ nếu có)
            const justPosted = sessionStorage.getItem('just_posted_loc');
            if (justPosted) {
                try {
                    const loc = JSON.parse(justPosted);
                    sessionStorage.removeItem('just_posted_loc');
                    
                    setTimeout(() => {
                        if (map) {
                            map.setView([loc.lat, loc.lng], 15);
                            triggerBumpRipple(loc.lat, loc.lng);
                            if (loc.id && markers && markers[loc.id]) {
                                markers[loc.id].openPopup();
                            }
                        }
                    }, 1000);
                } catch (e) {
                    console.error("Failed to parse just_posted_loc", e);
                }
            }
        }
    });

    // Trip Action Helpers
    function openCameraForTrip(tripId, tripTitle) {
        locketTripId = tripId;
        openLocketCamera();
        const locketCaption = document.getElementById('locketCaption');
        if (locketCaption) {
            locketCaption.placeholder = `Chụp ảnh cho chuyến đi: ${tripTitle}...`;
        }
    }

    function addMemoryForTrip(tripId) {
        const selectEl = document.querySelector('#addMemoryModal select[name="trip_id"]');
        if (selectEl) {
            selectEl.value = tripId;
            selectEl.dispatchEvent(new Event('change'));
        }
        var addModal = new bootstrap.Modal(document.getElementById('addMemoryModal'));
        addModal.show();
    }
    </script>
    
</body>
</html>
