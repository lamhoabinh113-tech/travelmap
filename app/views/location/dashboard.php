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
    <link rel="stylesheet" href="css/style.css?v=3.6">
    <style>
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
        /* Collage Grid CSS */
        .collage-grid.collage-1 {
            grid-template-columns: 1fr;
        }
        .collage-grid.collage-2 {
            grid-template-columns: 1fr 1fr;
        }
        .collage-grid.collage-3 {
            grid-template-columns: 2fr 1fr;
            grid-template-rows: 1fr 1fr;
        }
        .collage-grid.collage-4 {
            grid-template-columns: 2fr 1fr;
            grid-template-rows: 1fr 1fr 1fr;
        }
        .collage-item {
            position: relative;
            overflow: hidden;
            cursor: pointer;
            background: #000;
        }
        .collage-item img, .collage-item video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .collage-item:hover img, .collage-item:hover video {
            transform: scale(1.05);
        }
        .collage-overlay {
            background: rgba(0, 0, 0, 0.5);
            font-size: 1.25rem;
            pointer-events: none;
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
    
    
    <link rel="stylesheet" href="css/dashboard_mobile.css?v=3.6">
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
        <div class="chat-map-btn" onclick="toggleChatWidget()">
            <i class="bi bi-chat-text"></i>
        </div>

        <div class="map-mode-pill">
            <button type="button" class="active" id="darkMapBtn" onclick="setMapTheme('dark')" title="Bản đồ tối"><i class="bi bi-moon-stars-fill"></i></button>
            <button type="button" id="lightMapBtn" onclick="setMapTheme('light')" title="Bản đồ sáng"><i class="bi bi-brightness-high-fill"></i></button>
            <button type="button" class="active" id="followLocationBtn" onclick="toggleFollowLocation()" title="Theo dõi vị trí thực tế"><i class="bi bi-crosshair"></i></button>
            <button type="button" onclick="refreshMyLocation()" title="Định vị lại (GPS chính xác)"><i class="bi bi-arrow-clockwise"></i></button>
            <button type="button" onclick="window.location.href='index.php?url=auth/logout'" title="Đăng xuất" style="background: rgba(239, 68, 68, 0.8);"><i class="bi bi-box-arrow-right"></i></button>
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
                        <div class="memory-item" data-trip-id="<?php echo $loc['trip_id'] ?? 0; ?>" onclick="focusMap(<?php echo $loc['latitude']; ?>, <?php echo $loc['longitude']; ?>, true)">
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
                                <span class="memory-chip"><i class="bi bi-emoji-smile text-warning"></i> <?php echo htmlspecialchars($loc['feeling']); ?></span>
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

                            <!-- Photos Collage Grid -->
                            <?php 
                            $photos = $trip_photos_data[$item['trip_id']] ?? [];
                            $photo_count = count($photos);
                            if ($photo_count > 0): 
                            ?>
                                <div class="collage-grid collage-<?php echo min(4, $photo_count); ?> mb-3 rounded-4 overflow-hidden shadow-sm" style="height: 200px; display: grid; gap: 4px;">
                                    <?php if ($photo_count == 1): ?>
                                        <div class="collage-item w-100 h-100" onclick="openTripGallery(<?php echo $item['trip_id']; ?>, 0, '<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>')">
                                            <img src="<?php echo UPLOADS_URL . '/' . htmlspecialchars($photos[0]); ?>" class="w-100 h-100 object-fit-cover">
                                        </div>
                                    <?php elseif ($photo_count == 2): ?>
                                        <div class="collage-item h-100" onclick="openTripGallery(<?php echo $item['trip_id']; ?>, 0, '<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>')">
                                            <img src="<?php echo UPLOADS_URL . '/' . htmlspecialchars($photos[0]); ?>" class="w-100 h-100 object-fit-cover">
                                        </div>
                                        <div class="collage-item h-100" onclick="openTripGallery(<?php echo $item['trip_id']; ?>, 1, '<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>')">
                                            <img src="<?php echo UPLOADS_URL . '/' . htmlspecialchars($photos[1]); ?>" class="w-100 h-100 object-fit-cover">
                                        </div>
                                    <?php elseif ($photo_count == 3): ?>
                                        <div class="collage-item h-100" onclick="openTripGallery(<?php echo $item['trip_id']; ?>, 0, '<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>')" style="grid-column: span 2; grid-row: span 2;">
                                            <img src="<?php echo UPLOADS_URL . '/' . htmlspecialchars($photos[0]); ?>" class="w-100 h-100 object-fit-cover">
                                        </div>
                                        <div class="collage-item h-100" onclick="openTripGallery(<?php echo $item['trip_id']; ?>, 1, '<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>')">
                                            <img src="<?php echo UPLOADS_URL . '/' . htmlspecialchars($photos[1]); ?>" class="w-100 h-100 object-fit-cover">
                                        </div>
                                        <div class="collage-item h-100" onclick="openTripGallery(<?php echo $item['trip_id']; ?>, 2, '<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>')">
                                            <img src="<?php echo UPLOADS_URL . '/' . htmlspecialchars($photos[2]); ?>" class="w-100 h-100 object-fit-cover">
                                        </div>
                                    <?php else: // 4 or more ?>
                                        <div class="collage-item h-100" onclick="openTripGallery(<?php echo $item['trip_id']; ?>, 0, '<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>')" style="grid-column: span 2; grid-row: span 2;">
                                            <img src="<?php echo UPLOADS_URL . '/' . htmlspecialchars($photos[0]); ?>" class="w-100 h-100 object-fit-cover">
                                        </div>
                                        <div class="collage-item h-100" onclick="openTripGallery(<?php echo $item['trip_id']; ?>, 1, '<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>')">
                                            <img src="<?php echo UPLOADS_URL . '/' . htmlspecialchars($photos[1]); ?>" class="w-100 h-100 object-fit-cover">
                                        </div>
                                        <div class="collage-item h-100" onclick="openTripGallery(<?php echo $item['trip_id']; ?>, 2, '<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>')">
                                            <img src="<?php echo UPLOADS_URL . '/' . htmlspecialchars($photos[2]); ?>" class="w-100 h-100 object-fit-cover">
                                        </div>
                                        <div class="collage-item h-100 position-relative" onclick="openTripGallery(<?php echo $item['trip_id']; ?>, 3, '<?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?>')">
                                            <img src="<?php echo UPLOADS_URL . '/' . htmlspecialchars($photos[3]); ?>" class="w-100 h-100 object-fit-cover">
                                            <?php if ($photo_count > 4): ?>
                                                <div class="collage-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-black bg-opacity-50 text-white fw-bold">
                                                    +<?php echo ($photo_count - 4); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
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
                                            <div class="p-2 border rounded-3 bg-light bg-opacity-50 d-flex align-items-center justify-content-between cursor-pointer hover-bg-light" onclick="focusMap(<?php echo $c['latitude']; ?>, <?php echo $c['longitude']; ?>, true)">
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
                                                            <?php echo htmlspecialchars($c['full_name'] ?: $c['username']); ?> - <?php echo htmlspecialchars($c['feeling']); ?>
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
                                            <div class="trip-card-photo" style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden; border: 1px solid rgba(0,0,0,0.05); flex-shrink: 0; background: #eee;">
                                                <img src="<?php echo UPLOADS_URL . '/' . htmlspecialchars($p); ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.src='https://cdn-icons-png.flaticon.com/512/4140/4140044.png'">
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
                    <ul class="list-group list-group-flush small">
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
            <form action="index.php?url=location/save" method="POST" enctype="multipart/form-data">
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
            <form action="index.php?url=location/update" method="POST" enctype="multipart/form-data">
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
            <form action="index.php?url=trip/create" method="POST">
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
    
    savedLocations.forEach(function(loc) {
        var customIcon = L.divIcon({
            className: 'photo-marker-wrapper',
            html: createCustomMarkerHtml(loc, false),
            iconSize: [48, 48],
            iconAnchor: [24, 48],
            popupAnchor: [0, -48]
        });

        var marker = L.marker([loc.latitude, loc.longitude], { icon: customIcon }).addTo(map);
        
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

    friendLocations.forEach(function(loc) {
        var friendIcon = L.divIcon({
            className: 'photo-marker-wrapper',
            html: createCustomMarkerHtml(loc, true),
            iconSize: [48, 48],
            iconAnchor: [24, 48],
            popupAnchor: [0, -48]
        });

        var friendMarker = L.marker([loc.latitude, loc.longitude], { icon: friendIcon }).addTo(map);
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

    const journeyPoints = savedLocations
        .filter(loc => loc.latitude && loc.longitude)
        .map(loc => [Number(loc.latitude), Number(loc.longitude)])
        .reverse();

    let routeLine = null;
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
            offset = (offset - 1) % 22;
            const el = routeLine.getElement();
            if (el) {
                el.style.strokeDashoffset = offset + 'px';
            }
            requestAnimationFrame(animateMarchingAnts);
        }
        setTimeout(() => {
            requestAnimationFrame(animateMarchingAnts);
        }, 500);
    }

    journeyPoints.forEach((point, index) => {
        L.circleMarker(point, {
            radius: 26 + Math.min(index * 3, 18),
            stroke: false,
            fillColor: '#f43f5e',
            fillOpacity: 0.12
        }).addTo(map);
    });

    function setMapTheme(theme) {
        map.removeLayer(activeMapLayer);
        activeMapLayer = mapLayers[theme].addTo(map);
        document.getElementById('darkMapBtn').classList.toggle('active', theme === 'dark');
        document.getElementById('lightMapBtn').classList.toggle('active', theme === 'light');
        
        // Toggle Global UI Theme
        if (theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('uiTheme', 'dark');
        } else {
            document.documentElement.removeAttribute('data-theme');
            localStorage.setItem('uiTheme', 'light');
        }
    }
    
    // Khôi phục theme UI khi tải trang
    if (localStorage.getItem('uiTheme') === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    }

    function fitJourneyRoute() {
        if (routeLine) {
            map.fitBounds(routeLine.getBounds(), { padding: [60, 60] });
        } else if (journeyPoints.length === 1) {
            map.flyTo(journeyPoints[0], 15);
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

    // Options chính: yêu cầu độ chính xác cao nhất tuyệt đối
    const geoOptions = {
        enableHighAccuracy: true,
        maximumAge: 0,       // KHÔNG BAO GIỜ dùng vị trí cache
        timeout: 25000       // Chờ tối đa 25s để lấy GPS xịn nhất
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

        navigator.geolocation.getCurrentPosition(
            updateCurrentPosition,
            onLocationError,
            geoOptions
        );
    }

    function startLiveLocationTracking() {
        if (!navigator.geolocation) {
            updateLocationStatus('<i class="bi bi-exclamation-triangle-fill me-2"></i> <small>Trình duyệt không hỗ trợ định vị</small>', 'warning');
            return;
        }
        if (locationWatchId !== null) return;

        document.getElementById('liveLocationHud').style.display = 'block';
        document.getElementById('liveLocationHudText').textContent = 'Đang lấy GPS chính xác...';

        // Lấy vị trí bằng getCurrentPosition trước để phản hồi nhanh
        requestAccurateLocation();

        // watchPosition để liên tục cập nhật theo thời gian thực
        locationWatchId = navigator.geolocation.watchPosition(
            updateCurrentPosition,
            onLocationError,
            geoOptions
        );

        // Retry nếu chưa lấy được GPS xịn sau 20 giây
        _accuracyRetryTimer = setInterval(() => {
            if (!userManuallySetLocation && bestAccuracy > 100) {
                console.log('Đang thử nâng cao độ chính xác GPS...');
                requestAccurateLocation();
            }
        }, 20000);
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
        document.getElementById('followLocationBtn')?.classList.add('active');
        requestAccurateLocation();
    }

    function toggleFollowLocation() {
        followLiveLocation = !followLiveLocation;
        const btn = document.getElementById('followLocationBtn');
        btn?.classList.toggle('active', followLiveLocation);

        if (followLiveLocation && currentLocationMarker) {
            const pos = currentLocationMarker.getLatLng();
            map.flyTo(pos, 17, { duration: 1 });
            currentLocationMarker.openPopup();
        }
    }

    startLiveLocationTracking();
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
        
        if (openPopup) {
            savedLocations.concat(friendLocations).forEach(loc => {
                if(loc.latitude == lat && loc.longitude == lng) {
                    setTimeout(() => markers[loc.id].openPopup(), 1500);
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
        setTimeout(() => marker.openPopup(), 1500);
    }

    // Album Functionality (Supports Photos & Videos)
    function openAlbum(id, title) {
        document.getElementById('albumTitle').innerText = title;
        const itemsContainer = document.getElementById('albumItems');
        const thumbsContainer = document.getElementById('albumThumbs');
        itemsContainer.innerHTML = '<div class="text-white py-5"><div class="spinner-border text-primary"></div></div>';
        thumbsContainer.innerHTML = '';
        
        // Cập nhật link quản lý (chỉ hiện nếu là của mình)
        const manageLink = document.getElementById('manageAlbumLink');
        <?php if(isset($is_friend_view) && $is_friend_view): ?>
            manageLink.style.display = 'none';
        <?php else: ?>
            manageLink.style.display = 'inline-block';
            manageLink.href = `index.php?url=location/manageAlbum&id=${id}`;
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
                                <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                    <video controls class="d-block w-100 rounded-4" style="max-height: 70vh; background: #000;">
                                        <source src="${uploadsUrl}/${item.image_path}" type="video/${ext === 'mov' ? 'mp4' : ext}">
                                        Trình duyệt của bạn không hỗ trợ xem video.
                                    </video>
                                </div>
                            `;
                        } else {
                            return `
                                <div class="carousel-item ${index === 0 ? 'active' : ''}">
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
                }
            });
    }

    function openTripGallery(tripId, startPhotoIndex, title = "Album Chuyến đi") {
        document.getElementById('albumTitle').innerText = title;
        const itemsContainer = document.getElementById('albumItems');
        const thumbsContainer = document.getElementById('albumThumbs');
        itemsContainer.innerHTML = '<div class="text-white py-5"><div class="spinner-border text-primary"></div></div>';
        thumbsContainer.innerHTML = '';
        
        // Ẩn nút quản lý đối với album chuyến đi
        const manageLink = document.getElementById('manageAlbumLink');
        if (manageLink) manageLink.style.display = 'none';

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
                                <div class="carousel-item ${index === startPhotoIndex ? 'active' : ''}">
                                    <video controls class="d-block w-100 rounded-4" style="max-height: 70vh; background: #000;">
                                        <source src="${uploadsUrl}/${item.image_path}" type="video/${ext === 'mov' ? 'mp4' : ext}">
                                        Trình duyệt của bạn không hỗ trợ xem video.
                                    </video>
                                </div>
                            `;
                        } else {
                            return `
                                <div class="carousel-item ${index === startPhotoIndex ? 'active' : ''}">
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
                // Add marker to map
                var photoIcon = L.divIcon({
                    className: 'photo-marker',
                    html: `
                        <div class="marker-container" style="animation: slideUp 0.5s ease-out;">
                            <img src="${data.image_url}" class="marker-image">
                            <div class="marker-arrow"></div>
                        </div>
                    `,
                    iconSize: [50, 50],
                    iconAnchor: [25, 50],
                    popupAnchor: [0, -50]
                });
                
                var marker = L.marker([locketLat, locketLng], { icon: photoIcon }).addTo(map);
                marker.bindPopup(`
                    <div class="p-2 text-center" style="width: 240px">
                        <img src="${data.image_url}" class="img-fluid rounded-3 mb-2 shadow-sm" style="max-height: 200px; object-fit: cover;">
                        <h6 class="fw-bold mb-1">${data.place_name}</h6>
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                            <small class="text-muted"><i class="bi bi-clock"></i> Vừa xong</small>
                        </div>
                        <p class="small text-secondary mb-0">${caption}</p>
                    </div>
                `);

                // Fly to new marker
                map.flyTo([locketLat, locketLng], 16);
                
                closeLocketCamera();
                // Optionally reload to update sidebar or dynamically prepend HTML
                setTimeout(() => window.location.reload(), 1500);
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

    // AI Chat Widget
    const chatWidget = document.getElementById('aiChatWidget');
    const chatHistory = document.getElementById('chatHistory');
    const chatInput = document.getElementById('chatInput');

    function toggleChatWidget() {
        chatWidget.classList.toggle('open');
        if (chatWidget.classList.contains('open')) {
            chatInput.focus();
        }
    }

    function closeChatWidget() {
        chatWidget.classList.remove('open');
    }

    function setChatPrompt(text) {
        chatInput.value = text;
        chatInput.focus();
    }

    function appendChatMessage(role, message) {
        const wrapper = document.createElement('div');
        wrapper.className = 'chat-message ' + role;

        const avatar = document.createElement('div');
        avatar.className = 'avatar';
        avatar.textContent = role === 'user' ? 'T' : 'AI';

        const bubble = document.createElement('div');
        bubble.className = 'bubble';
        bubble.textContent = message;

        wrapper.appendChild(avatar);
        wrapper.appendChild(bubble);
        chatHistory.appendChild(wrapper);
        chatHistory.scrollTop = chatHistory.scrollHeight;
    }

    function getAiCoords() {
        if (typeof currentLocationMarker !== 'undefined' && currentLocationMarker) {
            const p = currentLocationMarker.getLatLng();
            return { latitude: p.lat, longitude: p.lng };
        }
        const latEl = document.getElementById('lat');
        const lngEl = document.getElementById('lng');
        if (latEl?.value && lngEl?.value) {
            return { latitude: latEl.value, longitude: lngEl.value };
        }
        return { latitude: '', longitude: '' };
    }

    function sendChatMessage() {
        const question = chatInput.value.trim();
        if (!question) return;

        appendChatMessage('user', question);
        chatInput.value = '';

        const loading = document.createElement('div');
        loading.className = 'chat-message assistant';
        loading.innerHTML = '<div class="avatar">AI</div><div class="bubble">Đang phân tích và tư vấn...</div>';
        chatHistory.appendChild(loading);
        chatHistory.scrollTop = chatHistory.scrollHeight;

        const coords = getAiCoords();
        fetch('index.php?url=ai/ask', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                question: question,
                latitude: coords.latitude,
                longitude: coords.longitude
            })
        })
        .then(res => res.json())
        .then(data => {
            loading.remove();
            if (data.success) {
                appendChatMessage('assistant', data.message);
            } else {
                appendChatMessage('assistant', 'Có lỗi: ' + data.message);
            }
        })
        .catch(() => {
            loading.remove();
            appendChatMessage('assistant', 'Không thể kết nối đến AI. Vui lòng thử lại');
        });
    }

    // Mobile Navigation Logic
    const sidebar = document.querySelector('.sidebar');
    const socialSidebar = document.querySelector('.social-sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function closeAllSidebars() {
        sidebar.classList.remove('mobile-open');
        socialSidebar.classList.remove('mobile-open');
        overlay.classList.remove('show');
        
        // Reset nav active state to Map
        document.querySelectorAll('.bottom-nav-item').forEach(el => el.classList.remove('active'));
        const mapTab = document.querySelector('.bottom-nav-item:first-child');
        if (mapTab) mapTab.classList.add('active');
        
        // Ensure map layout is correct after resizing
        setTimeout(() => { map.invalidateSize(); }, 300);
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
            socialSidebar.classList.remove('mobile-open');
            sidebar.classList.add('mobile-open');
            overlay.classList.add('show');
        } else if (tab === 'social') {
            sidebar.classList.remove('mobile-open');
            socialSidebar.classList.add('mobile-open');
            overlay.classList.add('show');
        }
    }

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
            <div class="ai-msg bot">Chào bạn! Tôi có thể giúp gì cho chuyến đi của bạn? (Gợi ý địa điểm, lịch trình...)</div>
        </div>
        <div class="ai-chat-input">
            <input type="text" id="aiChatInput" placeholder="Hỏi AI..." onkeypress="if(event.key === 'Enter') sendAIMessage()">
            <button onclick="sendAIMessage()"><i class="bi bi-send-fill"></i></button>
        </div>
    </div>
    
    <script>
    function toggleAIChat() {
        document.getElementById('aiChatWindow').classList.toggle('active');
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
        
        // Call backend or mock response
        setTimeout(() => {
            fetch('index.php?url=chat/ask', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: text })
            })
            .then(res => res.json())
            .then(data => {
                botMsg.innerHTML = data.reply || 'Xin lỗi, tôi chưa hiểu rõ ý bạn.';
                body.scrollTop = body.scrollHeight;
            })
            .catch(() => {
                botMsg.innerHTML = 'Tuyệt vời! Gợi ý là bạn nên khám phá thêm ẩm thực địa phương nhé.'; // Fallback
                body.scrollTop = body.scrollHeight;
            });
        }, 1000);
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
                const targetSrc = target.tagName.toLowerCase() === 'video' ? (target.querySelector('source')?.src || target.src) : target.src;
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

    // Initialize lightbox on load
    document.addEventListener('DOMContentLoaded', initLightbox);

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
