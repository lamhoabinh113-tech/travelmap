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
            background: linear-gradient(180deg, #eef2ff 0%, #f8fafc 35%, #f8fafc 100%);
            min-height: 100vh;
        }
        .feed-container { max-width: 700px; margin: 0 auto; padding-top: 80px; padding-bottom: 50px; }
        
        .navbar-premium {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid #e2e8f0;
            padding: 15px 0;
            transition: box-shadow 0.3s ease, background 0.3s ease;
        }
        .navbar-premium.scrolled {
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
            background: rgba(255, 255, 255, 0.95);
        }

        .feed-page-title {
            background: linear-gradient(135deg, #4f46e5, #0ea5e9);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .feed-card {
            background: white;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            margin-bottom: 25px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.55s cubic-bezier(0.4, 0, 0.2, 1),
                        transform 0.55s cubic-bezier(0.4, 0, 0.2, 1),
                        box-shadow 0.35s ease;
        }
        .feed-card.visible {
            opacity: 1;
            transform: translateY(0);
        }

        .feed-header {
            padding: 15px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .feed-user-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary);
            color: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
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

        .feed-carousel .carousel-indicators {
            margin-bottom: 0.5rem;
        }

        .feed-content { padding: 20px; }
        
        .btn-reaction {
            border: none;
            background: #f1f5f9;
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-reaction.liked {
            background: #fee2e2;
            color: #ef4444;
        }

        .btn-reaction:active {
            transform: scale(0.96);
        }

        .empty-feed-icon {
            animation: float 3s ease-in-out infinite;
        }

        .private-chat-card {
            margin-top: 16px;
            padding: 16px;
            border-radius: 18px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
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
        }

        .private-message {
            display: flex;
        }

        .private-message.mine {
            justify-content: flex-end;
        }

        .private-message .bubble {
            max-width: 78%;
            padding: 9px 12px;
            border-radius: 14px;
            background: #ffffff;
            color: #334155;
            font-size: 13px;
            box-shadow: 0 8px 18px rgba(15,23,42,0.06);
        }

        .private-message.mine .bubble {
            background: #0d6efd;
            color: white;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-premium fixed-top">
    <div class="container d-flex justify-content-between align-items-center">
        <a class="navbar-brand fw-bold text-primary" href="index.php?url=location/dashboard">
            <i class="bi bi-geo-alt-fill me-2"></i> TravelMap
        </a>
        <div class="d-flex gap-3">
            <a href="index.php?url=location/dashboard" class="btn btn-light rounded-pill px-4 fw-bold">
                <i class="bi bi-map me-2"></i> Bản đồ của tôi
            </a>
            <div class="feed-user-avatar">
                <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
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
                                <source src="../uploads/<?php echo $media['image_path']; ?>" type="video/mp4">
                            </video>
                        </div>
                    <?php else: ?>
                        <div class="feed-media-wrap">
                            <img src="../uploads/<?php echo $media['image_path']; ?>" class="feed-image" alt="<?php echo htmlspecialchars($item['place_name']); ?>" onclick="window.location.href='index.php?url=location/friend_map&id=<?php echo $item['user_id']; ?>'">
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
                                            <source src="../uploads/<?php echo $media['image_path']; ?>" type="video/mp4">
                                        </video>
                                    <?php else: ?>
                                        <img src="../uploads/<?php echo $media['image_path']; ?>" alt="<?php echo htmlspecialchars($item['place_name']); ?>" draggable="false">
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
                    <div class="d-flex gap-2">
                        <button class="btn-reaction <?php echo $item['is_liked'] ? 'liked' : ''; ?>" onclick="toggleLike(this, <?php echo $item['id']; ?>)">
                            <i class="bi <?php echo $item['is_liked'] ? 'bi-heart-fill' : 'bi-heart'; ?> me-2"></i>
                            <span class="like-count"><?php echo $item['like_count']; ?></span> Yêu thích
                        </button>
                        <button class="btn-reaction" onclick="window.location.href='index.php?url=location/friend_map&id=<?php echo $item['user_id']; ?>'">
                            <i class="bi bi-geo-alt me-2"></i> Xem trên bản đồ
                        </button>
                    </div>
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
        // Tránh click liên tục
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
                    // Thêm 1 toast nhỏ thông báo có bài mới
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
</script>
</body>
</html>
