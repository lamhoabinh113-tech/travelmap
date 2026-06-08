<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khám Phá Hành Trình - Travel Memory Map</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background: linear-gradient(180deg, #f1f5f9 0%, #e2e8f0 100%);
            min-height: 100vh;
        }
        .feed-container { max-width: 720px; margin: 0 auto; padding-top: 90px; padding-bottom: 50px; }
        
        .navbar-premium {
            background: rgba(255, 255, 255, 0.82);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            padding: 12px 0;
            transition: all 0.3s ease;
        }
        .navbar-premium.scrolled {
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            background: rgba(255, 255, 255, 0.94);
        }

        .feed-page-title {
            background: linear-gradient(135deg, #6366f1, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .feed-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(8px);
            border-radius: 24px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            margin-bottom: 30px;
            overflow: hidden;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04);
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.55s cubic-bezier(0.4, 0, 0.2, 1),
                        transform 0.55s cubic-bezier(0.4, 0, 0.2, 1),
                        box-shadow 0.35s ease;
        }
        .feed-card:hover {
            box-shadow: 0 20px 35px -10px rgba(15, 23, 42, 0.08);
        }
        .feed-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .feed-header {
            padding: 18px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .feed-user-avatar {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary), var(--cyan));
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: 0 4px 10px rgba(99, 102, 241, 0.2);
            overflow: hidden;
        }
        .feed-user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .feed-media-wrap {
            background: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 120px;
        }

        .feed-image {
            width: 100%;
            height: auto;
            max-height: min(72vh, 720px);
            object-fit: contain;
            object-position: center;
            display: block;
            cursor: pointer;
        }

        /* Khung cố định — tránh carousel nhảy khi đổi ảnh (không override display của Bootstrap) */
        .feed-carousel .carousel-inner {
            background: #0f172a;
        }

        .feed-carousel-stage {
            width: 100%;
            height: min(70vh, 520px);
            min-height: 360px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
        }

        .feed-carousel-stage img,
        .feed-carousel-stage video {
            max-width: 100%;
            max-height: 100%;
            width: auto;
            height: auto;
            object-fit: contain;
            object-position: center;
            display: block;
            user-select: none;
            -webkit-user-drag: none;
        }

        .feed-carousel .carousel-control-prev,
        .feed-carousel .carousel-control-next {
            width: 12%;
        }

        .feed-carousel .carousel-control-prev-icon,
        .feed-carousel .carousel-control-next-icon {
            background-color: rgba(15, 23, 42, 0.4);
            padding: 20px;
            border-radius: 50%;
            background-size: 50%;
        }

        .feed-carousel .carousel-indicators {
            margin-bottom: 0.5rem;
        }

        .feed-content { padding: 24px; }
        
        .btn-reaction {
            border: none;
            background: #f1f5f9;
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 13.5px;
            color: #475569;
            transition: all 0.2s;
        }

        .btn-reaction.liked {
            background: #fee2e2;
            color: #ef4444;
        }

        .btn-reaction:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .btn-reaction.liked:hover {
            background: #fecaca;
        }

        .btn-reaction:active {
            transform: scale(0.96);
        }

        .empty-feed-icon {
            animation: float 3s ease-in-out infinite;
        }

        .private-chat-card {
            margin-top: 20px;
            padding: 20px;
            border-radius: 20px;
            background: rgba(248, 250, 252, 0.85);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.02);
        }

        .private-chat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 12px;
        }

        .private-chat-messages {
            max-height: 180px;
            overflow-y: auto;
            display: grid;
            gap: 8px;
            margin-bottom: 12px;
            padding-right: 5px;
        }

        .private-message {
            display: flex;
        }

        .private-message.mine {
            justify-content: flex-end;
        }

        .private-message .bubble {
            max-width: 78%;
            padding: 9px 14px;
            border-radius: 16px;
            background: #ffffff;
            color: #334155;
            font-size: 13px;
            box-shadow: 0 4px 10px rgba(15,23,42,0.03);
            border: 1px solid rgba(226, 232, 240, 0.6);
        }

        .private-message.mine .bubble {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: white;
            border: none;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }

        /* Notifications Dropdown Custom Styling */
        .notification-dropdown {
            box-shadow: 0 15px 40px rgba(15, 23, 42, 0.15) !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            animation: fadeInScale 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
            transform-origin: top right;
        }
        .noti-item {
            display: flex;
            gap: 10px;
            padding: 10px;
            border-radius: 12px;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            color: inherit;
            align-items: center;
        }
        .noti-item:hover {
            background: #f1f5f9;
        }
        .noti-item.unread {
            background: #eff6ff;
            border-left: 3px solid var(--primary);
            padding-left: 7px;
        }
        .noti-item .avatar {
            width: 34px;
            height: 34px;
            background: linear-gradient(135deg, var(--primary), var(--cyan));
            color: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: bold;
            overflow: hidden;
            box-shadow: 0 3px 6px rgba(99, 102, 241, 0.1);
        }
        .noti-item .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .noti-item .info {
            flex: 1;
            font-size: 12px;
            line-height: 1.4;
        }
        .noti-item .time {
            font-size: 10px;
            color: var(--text-muted);
            margin-top: 2px;
        }
        
        /* Custom scrollbars */
        .comments-list::-webkit-scrollbar,
        .private-chat-messages::-webkit-scrollbar,
        #notiList::-webkit-scrollbar {
            width: 4px;
        }
        .comments-list::-webkit-scrollbar-thumb,
        .private-chat-messages::-webkit-scrollbar-thumb,
        #notiList::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }
        .comments-list::-webkit-scrollbar-track,
        .private-chat-messages::-webkit-scrollbar-track,
        #notiList::-webkit-scrollbar-track {
            background: transparent;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-premium fixed-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand fw-bold text-primary" href="index.php?url=location/dashboard">
            <i class="bi bi-geo-alt-fill me-2"></i> TravelMap
        </a>
        <div class="d-flex gap-3 align-items-center">
            <a href="index.php?url=location/dashboard" class="btn btn-light rounded-pill px-4 fw-bold">
                <i class="bi bi-map me-2"></i> Bản đồ của tôi
            </a>
            <!-- Quả chuông thông báo -->
            <div class="position-relative notification-bell-container" style="z-index: 1050;">
                <button class="btn btn-light rounded-circle p-2 position-relative" id="notiBellBtn" onclick="toggleNotiDropdown(event)">
                    <i class="bi bi-bell-fill fs-5 text-secondary"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" id="notiBadge" style="display:none; font-size:10px; padding: 3px 6px;">0</span>
                </button>
                <div class="notification-dropdown glass shadow-lg rounded-4 p-3 position-absolute end-0 mt-2" id="notiDropdown" style="display:none; width:320px; border:1px solid rgba(226, 232, 240, 0.8); background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <h6 class="fw-bold mb-0" style="font-size:14px;">Thông báo mới nhất</h6>
                        <button class="btn btn-link btn-sm text-decoration-none p-0" onclick="markAllRead()" style="font-size:12px; color: var(--primary);">Đọc tất cả</button>
                    </div>
                    <div id="notiList" style="max-height: 280px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px;">
                        <div class="text-center text-muted small py-3">Không có thông báo mới</div>
                    </div>
                </div>
            </div>
            <div class="feed-user-avatar">
                <?php if (!empty($_SESSION['avatar'])): ?>
                    <img src="<?php echo htmlspecialchars($_SESSION['avatar']); ?>" alt="Avatar">
                <?php else: ?>
                    <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="feed-container">
    <h4 class="fw-bold mb-4 animate-fade-in feed-page-title">Khám phá hành trình từ bạn bè</h4>

    <?php if(empty($feed_items)): ?>
        <div class="text-center py-5 bg-white rounded-4 border animate-fade-in">
            <i class="bi bi-people display-1 text-muted opacity-25 empty-feed-icon"></i>
            <h5 class="mt-3 text-muted">Chưa có cập nhật nào từ bạn bè</h5>
            <p class="small text-muted">Hãy kết bạn để cùng chia sẻ những hành trình thú vị!</p>
            <a href="index.php?url=location/dashboard" class="btn btn-primary rounded-pill mt-2">Đến trang kết bạn</a>
        </div>
    <?php endif; ?>

    <?php foreach($feed_items as $item): ?>
        <div class="feed-card">
            <div class="feed-header">
                <div class="feed-user-avatar">
                    <?php echo strtoupper(substr($item['username'], 0, 1)); ?>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold">
                        <?php echo $item['full_name']; ?>
                        <?php if($item['user_id'] == $_SESSION['user_id']): ?>
                            <span class="badge bg-primary-subtle text-primary rounded-pill fw-normal ms-1" style="font-size: 10px;">Của tôi</span>
                        <?php endif; ?>
                    </h6>
                    <small class="text-muted">
                        @<?php echo $item['username']; ?> • <?php echo date('d/m/Y', strtotime($item['created_at'])); ?>
                        • 
                        <?php 
                        $privacy = $item['privacy'] ?? 'public';
                        if ($privacy === 'public') {
                            echo '<span class="text-success small"><i class="bi bi-globe"></i> Công khai</span>';
                        } else if ($privacy === 'friends') {
                            echo '<span class="text-primary small"><i class="bi bi-people-fill"></i> Bạn bè</span>';
                        } else if ($privacy === 'specific_friends') {
                            echo '<span class="text-info small"><i class="bi bi-people-fill"></i> Bạn bè cụ thể</span>';
                        } else {
                            echo '<span class="text-secondary small"><i class="bi bi-lock-fill"></i> Riêng tư</span>';
                        }
                        ?>
                    </small>
                </div>
            </div>

            <?php 
            $album_images = $item['album_images'] ?? [];
            if(!empty($album_images)):
                if(count($album_images) === 1):
                    $media = $album_images[0];
                    $ext = strtolower(pathinfo($media['image_path'], PATHINFO_EXTENSION));
                    $video_exts = ['mp4', 'webm', 'ogg', 'mov'];
                    if (in_array($ext, $video_exts)):
            ?>
                        <div class="feed-media-wrap">
                            <video controls class="feed-image">
                                <source src="<?= UPLOADS_URL ?>/<?php echo $media['image_path']; ?>" type="video/mp4">
                            </video>
                        </div>
                    <?php else: ?>
                        <div class="feed-media-wrap">
                            <img src="<?= UPLOADS_URL ?>/<?php echo $media['image_path']; ?>" class="feed-image" alt="<?php echo htmlspecialchars($item['place_name']); ?>" onclick="window.location.href='index.php?url=location/friend_map&id=<?php echo $item['user_id']; ?>'">
                        </div>
                    <?php endif; ?>
                <?php else: 
                    // Nhiều ảnh/video: Render Bootstrap Carousel
                    $carousel_id = "carousel_feed_" . $item['id'];
                ?>
                    <div id="<?php echo $carousel_id; ?>" class="carousel slide feed-carousel" data-bs-ride="false" data-bs-interval="false" data-bs-touch="true" data-bs-wrap="true">
                        <div class="carousel-indicators">
                            <?php foreach($album_images as $index => $media): ?>
                                <button type="button" data-bs-target="#<?php echo $carousel_id; ?>" data-bs-slide-to="<?php echo $index; ?>" class="<?php echo $index === 0 ? 'active' : ''; ?>"></button>
                            <?php endforeach; ?>
                        </div>
                        <div class="carousel-inner">
                            <?php foreach($album_images as $index => $media): 
                                $ext = strtolower(pathinfo($media['image_path'], PATHINFO_EXTENSION));
                                $video_exts = ['mp4', 'webm', 'ogg', 'mov'];
                                $isVideo = in_array($ext, $video_exts);
                            ?>
                                <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <div class="feed-carousel-stage">
                                    <?php if ($isVideo): ?>
                                        <video controls playsinline>
                                            <source src="<?= UPLOADS_URL ?>/<?php echo $media['image_path']; ?>" type="video/mp4">
                                        </video>
                                    <?php else: ?>
                                        <img src="<?= UPLOADS_URL ?>/<?php echo $media['image_path']; ?>" alt="<?php echo htmlspecialchars($item['place_name']); ?>" draggable="false">
                                    <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#<?php echo $carousel_id; ?>" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Trước</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#<?php echo $carousel_id; ?>" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Sau</span>
                        </button>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="feed-content">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0"><?php echo $item['place_name']; ?></h5>
                    <span class="badge bg-light text-primary rounded-pill fw-normal">
                        <?php echo $item['feeling']; ?>
                    </span>
                </div>
                <p class="text-secondary mb-4"><?php echo $item['description']; ?></p>
                
                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <div class="d-flex gap-2 w-100 justify-content-between">
                        <button class="btn-reaction <?php echo $item['is_liked'] ? 'liked' : ''; ?> flex-grow-1 me-2" onclick="toggleLike(this, <?php echo $item['id']; ?>)">
                            <i class="bi <?php echo $item['is_liked'] ? 'bi-heart-fill' : 'bi-heart'; ?> me-2"></i>
                            <span class="like-count"><?php echo $item['like_count']; ?></span> Thích
                        </button>
                        <button class="btn-reaction flex-grow-1 me-2" onclick="toggleCommentSection(<?php echo $item['id']; ?>)">
                            <i class="bi bi-chat-text me-2"></i>
                            Bình luận (<span class="comment-count-<?php echo $item['id']; ?>"><?php echo count($item['comments'] ?? []); ?></span>)
                        </button>
                        <button class="btn-reaction flex-grow-1" onclick="window.location.href='index.php?url=location/friend_map&id=<?php echo $item['user_id']; ?>'">
                            <i class="bi bi-geo-alt me-2"></i> Bản đồ
                        </button>
                    </div>
                </div>

                <!-- Comments Panel -->
                <div class="comments-section-container mt-3 pt-3 border-top" id="comments_section_<?php echo $item['id']; ?>" style="display:none;">
                    <div class="comments-list mb-3" id="comments_list_<?php echo $item['id']; ?>" style="max-height: 220px; overflow-y: auto; display: flex; flex-direction: column; gap: 8px; padding-right: 5px;">
                        <?php if (empty($item['comments'])): ?>
                            <div class="text-center text-muted small py-3 no-comments-placeholder" style="font-size:12px;">Chưa có bình luận nào. Hãy gửi lời bình luận đầu tiên!</div>
                        <?php else: ?>
                            <?php foreach ($item['comments'] as $comm): ?>
                                <div class="comment-item d-flex gap-2">
                                    <div style="width: 32px; height: 32px; background: linear-gradient(135deg, var(--primary), var(--cyan)); color: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold; overflow:hidden; flex-shrink: 0;">
                                        <?php if (!empty($comm['avatar'])): ?>
                                            <img src="<?php echo UPLOADS_URL . '/avatars/' . $comm['avatar']; ?>" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                                        <?php else: ?>
                                            <?php echo strtoupper(substr($comm['username'], 0, 1)); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div style="flex:1; background:#f1f5f9; border-radius:14px; padding:8px 12px; font-size:12.5px;">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <strong style="font-size:11.5px; color:#1e293b;"><?php echo htmlspecialchars($comm['full_name']); ?></strong>
                                            <small class="text-muted" style="font-size:9.5px;"><?php echo date('H:i d/m/Y', strtotime($comm['created_at'])); ?></small>
                                        </div>
                                        <div style="color: #334155;"><?php echo htmlspecialchars($comm['content']); ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <form onsubmit="postComment(event, <?php echo $item['id']; ?>)">
                        <div class="input-group">
                            <input type="text" id="comment_input_<?php echo $item['id']; ?>" class="form-control rounded-start-pill border-end-0 px-3 py-2" placeholder="Viết bình luận..." required style="font-size: 13.5px; outline:none; background:#f1f5f9; border-color:#cbd5e1;" autocomplete="off">
                            <button class="btn btn-primary rounded-end-pill px-3 py-2" type="submit" style="box-shadow:none;"><i class="bi bi-send-fill"></i></button>
                        </div>
                    </form>
                </div>

                <?php if($item['user_id'] != $_SESSION['user_id']): ?>
                    <div class="private-chat-card" data-friend-id="<?php echo $item['user_id']; ?>" data-location-id="<?php echo $item['id']; ?>">
                        <div class="private-chat-header">
                            <div>
                                <div class="fw-bold">
                                    <i class="bi bi-messenger text-primary me-1"></i>
                                    Nhắn riêng cho <?php echo htmlspecialchars($item['full_name']); ?>
                                </div>
                                <small class="text-muted">Tin nhắn chỉ hiển thị giữa hai bạn.</small>
                            </div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill">Messenger</span>
                        </div>
                        <div class="private-chat-messages" id="private_messages_<?php echo $item['id']; ?>">
                            <div class="small text-muted">Đang tải cuộc trò chuyện...</div>
                        </div>
                        <div class="input-group input-group-sm">
                            <input type="text" id="private_input_<?php echo $item['id']; ?>" class="form-control" maxlength="500" placeholder="Nhập tin nhắn riêng...">
                            <button class="btn btn-primary" onclick="sendPrivateMessage(<?php echo $item['id']; ?>, <?php echo $item['user_id']; ?>)">Gửi</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const feedNav = document.querySelector('.navbar-premium');
    window.addEventListener('scroll', () => {
        feedNav?.classList.toggle('scrolled', window.scrollY > 20);
    }, { passive: true });

    const feedCardObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, i) => {
            if (entry.isIntersecting) {
                entry.target.style.transitionDelay = (i % 4) * 0.08 + 's';
                entry.target.classList.add('visible');
                feedCardObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });

    document.querySelectorAll('.feed-card').forEach(card => feedCardObserver.observe(card));

    // Carousel: tắt tự chạy, tránh nhảy slide; dừng video khi chuyển ảnh
    document.querySelectorAll('.feed-carousel').forEach(carouselEl => {
        const instance = bootstrap.Carousel.getOrCreateInstance(carouselEl, {
            interval: false,
            ride: false,
            wrap: true,
            touch: true
        });

        carouselEl.addEventListener('slide.bs.carousel', () => {
            carouselEl.querySelectorAll('video').forEach(v => {
                v.pause();
            });
        });
    });

    function toggleLike(btn, locationId) {
        btn.disabled = true;

        fetch(`index.php?url=feed/toggleLike&id=${locationId}`)
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const icon = btn.querySelector('i');
                    const countSpan = btn.querySelector('.like-count');
                    
                    if (data.is_liked) {
                        btn.classList.add('liked');
                        icon.classList.replace('bi-heart', 'bi-heart-fill');
                        icon.style.animation = 'none';
                        void icon.offsetWidth;
                        icon.style.animation = '';
                    } else {
                        btn.classList.remove('liked');
                        icon.classList.replace('bi-heart-fill', 'bi-heart');
                    }
                    
                    countSpan.innerText = data.like_count;
                }
            })
            .catch(err => console.error('Lỗi khi thả tim:', err))
            .finally(() => {
                btn.disabled = false;
            });
    }

    // Auto Refresh for Real-time feed update (Polling every 15s)
    setInterval(() => {
        fetch('index.php?url=location/getUpdates')
            .then(res => res.json())
            .then(data => {
                if(data.has_updates) {
                    console.log("New updates found, reloading feed...");
                    const toastHtml = `
                        <div class="position-fixed top-0 start-50 translate-middle-x mt-4" style="z-index: 1050;">
                            <button class="btn btn-primary rounded-pill shadow-lg px-4 fw-bold animate-fade-in" onclick="window.location.reload()">
                                <i class="bi bi-arrow-up"></i> Có cập nhật mới
                            </button>
                        </div>
                    `;
                    if(!document.getElementById('newPostToast')) {
                        const div = document.createElement('div');
                        div.id = 'newPostToast';
                        div.innerHTML = toastHtml;
                        document.body.appendChild(div);
                    }
                }
            })
            .catch(e => console.log(e));
    }, 15000);

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.private-chat-card').forEach(card => {
            loadPrivateMessages(card.dataset.locationId, card.dataset.friendId);
        });
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text || '';
        return div.innerHTML;
    }

    function loadPrivateMessages(locationId, friendId) {
        fetch(`index.php?url=feed/getPrivateMessages&friend_id=${friendId}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('private_messages_' + locationId);
                if (!container) return;

                if (!data.success) {
                    container.innerHTML = '<div class="small text-muted">Chưa thể tải tin nhắn.</div>';
                    return;
                }

                if (data.messages.length === 0) {
                    container.innerHTML = '<div class="small text-muted">Chưa có tin nhắn riêng. Hãy bắt đầu cuộc trò chuyện.</div>';
                    return;
                }

                container.innerHTML = data.messages.map(message => {
                    const mine = Number(message.sender_id) === Number(<?php echo $_SESSION['user_id']; ?>);
                    return `
                        <div class="private-message ${mine ? 'mine' : ''}">
                            <div class="bubble">${escapeHtml(message.message)}</div>
                        </div>
                    `;
                }).join('');
                container.scrollTop = container.scrollHeight;
            })
            .catch(() => {
                const container = document.getElementById('private_messages_' + locationId);
                if (container) container.innerHTML = '<div class="small text-muted">Lỗi khi tải tin nhắn.</div>';
            });
    }

    function sendPrivateMessage(locationId, friendId) {
        const input = document.getElementById('private_input_' + locationId);
        if (!input) return;

        const text = input.value.trim();
        if (!text) {
            alert('Vui lòng nhập tin nhắn');
            return;
        }

        const formData = new FormData();
        formData.append('receiver_id', friendId);
        formData.append('message', text);

        fetch('index.php?url=feed/sendPrivateMessage', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    alert(data.message || 'Không thể gửi tin nhắn');
                    return;
                }
                input.value = '';
                loadPrivateMessages(locationId, friendId);
            })
            .catch(() => alert('Lỗi khi gửi tin nhắn'));
    }

    // Biểu tượng chuông thông báo & Dropdown
    function toggleNotiDropdown(event) {
        event.stopPropagation();
        const dropdown = document.getElementById('notiDropdown');
        if (dropdown.style.display === 'none' || dropdown.style.display === '') {
            dropdown.style.display = 'block';
            loadNotifications();
            markAllRead();
        } else {
            dropdown.style.display = 'none';
        }
    }

    // Đóng dropdown khi bấm bên ngoài
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('notiDropdown');
        const bellBtn = document.getElementById('notiBellBtn');
        if (dropdown && dropdown.style.display === 'block') {
            if (!dropdown.contains(e.target) && !bellBtn.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        }
    });

    // Tải thông báo từ server
    function loadNotifications() {
        fetch('index.php?url=feed/getNotifications')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const badge = document.getElementById('notiBadge');
                    const list = document.getElementById('notiList');
                    
                    // Cập nhật Badge
                    if (data.unread_count > 0) {
                        badge.innerText = data.unread_count;
                        badge.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                    }

                    // Render danh sách
                    if (data.list.length === 0) {
                        list.innerHTML = '<div class="text-center text-muted small py-3">Không có thông báo mới</div>';
                    } else {
                        list.innerHTML = data.list.map(noti => {
                            let link = '#';
                            if (noti.type === 'like' || noti.type === 'comment') {
                                link = `index.php?url=location/friend_map&id=${noti.reference_id}`;
                            }
                            
                            const avatarMarkup = noti.avatar 
                                ? `<img src="${noti.avatar}" alt="Avatar">` 
                                : noti.username.substring(0, 1).toUpperCase();

                            return `
                                <a href="${link}" class="noti-item ${noti.is_read == 0 ? 'unread' : ''}">
                                    <div class="avatar">${avatarMarkup}</div>
                                    <div class="info">
                                        <strong>${escapeHtml(noti.full_name)}</strong> ${noti.message}
                                        <div class="time">${noti.created_at}</div>
                                    </div>
                                </a>
                            `;
                        }).join('');
                    }
                }
            })
            .catch(err => console.error('Lỗi tải thông báo:', err));
    }

    // Đánh dấu tất cả đã đọc
    function markAllRead() {
        fetch('index.php?url=feed/markNotificationsRead')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('notiBadge').style.display = 'none';
                }
            });
    }

    // Tải thông báo định kỳ (mỗi 20 giây)
    setInterval(loadNotifications, 20000);
    // Tải lần đầu
    loadNotifications();

    // Comments Section Toggle
    function toggleCommentSection(locationId) {
        const sec = document.getElementById('comments_section_' + locationId);
        if (sec.style.display === 'none') {
            sec.style.display = 'block';
            const list = document.getElementById('comments_list_' + locationId);
            list.scrollTop = list.scrollHeight;
        } else {
            sec.style.display = 'none';
        }
    }

    // Gửi bình luận qua AJAX
    function postComment(event, locationId) {
        event.preventDefault();
        const input = document.getElementById('comment_input_' + locationId);
        const content = input.value.trim();
        if (!content) return;

        const formData = new FormData();
        formData.append('location_id', locationId);
        formData.append('content', content);

        fetch('index.php?url=feed/addComment', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                input.value = '';
                const list = document.getElementById('comments_list_' + locationId);
                
                // Xóa placeholder "Chưa có bình luận" nếu có
                const ph = list.querySelector('.no-comments-placeholder');
                if (ph) ph.remove();

                const c = data.comment;
                const avatarMarkup = c.avatar 
                    ? `<img src="${c.avatar}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">` 
                    : c.username.substring(0, 1).toUpperCase();

                // Append comment HTML
                const div = document.createElement('div');
                div.className = 'comment-item d-flex gap-2';
                div.innerHTML = `
                    <div style="width: 32px; height: 32px; background: linear-gradient(135deg, var(--primary), var(--cyan)); color: white; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: bold; overflow:hidden; flex-shrink:0;">
                        ${avatarMarkup}
                    </div>
                    <div style="flex:1; background:#f1f5f9; border-radius:14px; padding:8px 12px; font-size:12.5px;">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <strong style="font-size:11.5px; color:#1e293b;">${escapeHtml(c.full_name)}</strong>
                            <small class="text-muted" style="font-size:9.5px;">${c.created_at}</small>
                        </div>
                        <div style="color: #334155;">${escapeHtml(c.content)}</div>
                    </div>
                `;
                list.appendChild(div);
                list.scrollTop = list.scrollHeight;

                // Tăng số lượng đếm bình luận
                const counter = document.querySelector('.comment-count-' + locationId);
                if (counter) {
                    counter.innerText = parseInt(counter.innerText) + 1;
                }
            } else {
                alert(data.message || 'Lỗi gửi bình luận');
            }
        })
        .catch(err => console.error('Lỗi bình luận:', err));
    }

    // Lightbox Implementation
    let lightboxMediaList = [];
    let lightboxCurrentIndex = 0;

    function initLightbox() {
        const mediaSelectors = '.memory-img, .media-preview, .feed-image, .album-cell img, .album-cell video, .feed-card img';
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
</script>
</body>
</html>
