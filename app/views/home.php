<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Memory Map - Lưu giữ từng hành trình</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background: #07111f; color: #f8fafc; }
        .nav-glass {
            background: rgba(7, 17, 31, 0.58);
            backdrop-filter: blur(18px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }
        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(90deg, rgba(7,17,31,0.88) 0%, rgba(7,17,31,0.58) 46%, rgba(7,17,31,0.18) 100%),
                url('https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=2200&q=85');
            background-size: cover;
            background-position: center;
        }
        .hero-section::after {
            content: "";
            position: absolute;
            inset: auto 0 0;
            height: 22vh;
            background: linear-gradient(0deg, #07111f, rgba(7,17,31,0));
        }
        .hero-content { position: relative; z-index: 2; padding-top: 88px; padding-bottom: 80px; }
        .hero-title {
            max-width: 780px;
            font-size: clamp(3rem, 8vw, 6.8rem);
            font-weight: 800;
            line-height: 0.96;
        }
        .hero-copy { max-width: 620px; color: rgba(248,250,252,0.82); font-size: 1.14rem; }
        .hero-actions .btn { border-radius: 16px; padding: 14px 22px; font-weight: 700; }
        .floating-memory {
            position: absolute;
            right: clamp(22px, 8vw, 110px);
            bottom: 8vh;
            width: min(360px, 84vw);
            z-index: 3;
            background: rgba(255,255,255,0.14);
            border: 1px solid rgba(255,255,255,0.22);
            backdrop-filter: blur(22px);
            border-radius: 22px;
            padding: 16px;
            box-shadow: 0 28px 80px rgba(0,0,0,0.28);
        }
        .floating-memory img { width: 100%; height: 170px; object-fit: cover; border-radius: 16px; }
        .section-dark { background: #07111f; }
        .section-soft { background: #f6f8fb; color: #0f172a; }
        .feature-card {
            height: 100%;
            padding: 26px;
            border-radius: 8px;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.12);
            transition: transform .28s ease, border-color .28s ease;
        }
        .feature-card:hover {
            transform: translateY(-8px) scale(1.02);
            border-color: rgba(34,211,238,0.55);
            box-shadow: 0 20px 50px rgba(34,211,238,0.15);
        }
        .feature-card .feature-icon { transition: transform 0.35s var(--ease-spring, ease); }
        .feature-card:hover .feature-icon { transform: scale(1.1) rotate(-4deg); }
        .floating-memory { animation: float 5s ease-in-out infinite; }
        .hero-badge-pulse {
            animation: pulseGlow 2.5s ease infinite;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .timeline-row { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .timeline-row:hover {
            transform: translateX(8px);
            box-shadow: 0 22px 56px rgba(15,23,42,0.12);
        }
        .metric { transition: transform 0.3s ease, box-shadow 0.3s ease; }
        .metric:hover {
            transform: translateY(-4px);
            box-shadow: 0 22px 50px rgba(99,102,241,0.12);
        }
        .nav-glass a:not(.btn) { transition: color 0.2s ease, opacity 0.2s ease; }
        .nav-glass a:not(.btn):hover { opacity: 0.85; color: #22d3ee !important; }
        .feature-icon {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            color: #07111f;
            background: #7dd3fc;
            margin-bottom: 18px;
        }
        .timeline-preview {
            position: relative;
            display: grid;
            gap: 18px;
            padding-left: 22px;
        }
        .timeline-preview::before {
            content: "";
            position: absolute;
            left: 5px;
            top: 12px;
            bottom: 12px;
            width: 2px;
            background: linear-gradient(#22d3ee, #f43f5e);
        }
        .timeline-row {
            position: relative;
            padding: 18px;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 18px 48px rgba(15,23,42,0.08);
        }
        .timeline-row::before {
            content: "";
            position: absolute;
            left: -23px;
            top: 24px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #22d3ee;
            border: 3px solid #f6f8fb;
        }
        .metric {
            padding: 22px;
            border-radius: 8px;
            background: #ffffff;
            box-shadow: 0 16px 44px rgba(15,23,42,0.07);
        }
        @media (max-width: 991px) {
            .floating-memory { position: relative; right: auto; bottom: auto; margin-top: 36px; }
            .hero-section { align-items: flex-start; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top nav-glass py-3">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="index.php">
                <i class="bi bi-geo-alt-fill me-2 text-info"></i>
                <span>Travel Memory Map</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <div class="ms-auto d-flex align-items-center gap-3 mt-3 mt-lg-0">
                    <a href="#features" class="text-white text-decoration-none fw-semibold">Tính năng</a>
                    <a href="index.php?url=auth/login" class="text-white text-decoration-none fw-semibold">Đăng nhập</a>
                    <a href="index.php?url=auth/register" class="btn btn-premium">Bắt đầu hành trình</a>
                </div>
            </div>
        </div>
    </nav>

    <header class="hero-section">
        <div class="hero-orb hero-orb-1"></div>
        <div class="hero-orb hero-orb-2"></div>
        <div class="hero-orb hero-orb-3"></div>
        <div class="container hero-content">
            <div class="row align-items-end">
                <div class="col-lg-8">
                    <span class="badge rounded-pill text-bg-light px-3 py-2 mb-4 hero-badge-pulse reveal revealed">Travel journal + map + AI companion</span>
                    <h1 class="hero-title mb-4 reveal reveal-delay-1 revealed">Lưu giữ từng <span class="text-gradient">hành trình</span>, từng khoảnh khắc.</h1>
                    <p class="hero-copy mb-5 reveal reveal-delay-2 revealed">Đánh dấu nơi bạn đã đi qua, gom ảnh thành album, xem lại cảm xúc theo thời gian và biến mỗi chuyến đi thành một câu chuyện đáng nhớ.</p>
                    <div class="hero-actions d-flex flex-column flex-sm-row gap-3 reveal reveal-delay-3 revealed">
                        <a href="index.php?url=auth/register" class="btn btn-premium btn-lg"><i class="bi bi-compass-fill me-2"></i>Bắt đầu hành trình</a>
                        <a href="index.php?url=auth/login" class="btn btn-outline-light btn-lg"><i class="bi bi-map-fill me-2"></i>Khám phá bản đồ</a>
                    </div>
                </div>
                <div class="floating-memory reveal reveal-delay-2 revealed">
                    <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=900&q=80" alt="Travel memory preview">
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <div class="fw-bold">Hành trình biển xanh</div>
                            <small class="text-white-50">21/05/2026 · Bình yên · 12 ảnh</small>
                        </div>
                        <i class="bi bi-play-circle-fill fs-2 text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="features" class="section-dark py-5">
        <div class="container py-5">
            <div class="row g-4 mb-4 align-items-end reveal">
                <div class="col-lg-7">
                    <span class="text-info fw-bold small">SẢN PHẨM CHO KÝ ỨC DU LỊCH</span>
                    <h2 class="display-6 fw-bold mt-2">Không chỉ là dashboard, đây là <span class="text-gradient">bản đồ cảm xúc</span> của bạn.</h2>
                </div>
                <div class="col-lg-5 text-lg-end text-white-50">Timeline, album, friend system, AI caption và profile du lịch được gom trong một trải nghiệm gọn gàng.</div>
            </div>
            <div class="row g-4 stagger-children">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon"><i class="bi bi-map"></i></div>
                        <h5 class="fw-bold">Bản đồ sống động</h5>
                        <p class="text-white-50 mb-0">Marker ảnh, đường di chuyển, dark map và heatmap giúp hành trình có chiều sâu hơn.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon" style="background:#fda4af"><i class="bi bi-images"></i></div>
                        <h5 class="fw-bold">Album như Pinterest</h5>
                        <p class="text-white-50 mb-0">Masonry grid, lightbox fullscreen, slideshow và hiệu ứng fade cho ảnh và video.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon" style="background:#c4b5fd"><i class="bi bi-stars"></i></div>
                        <h5 class="fw-bold">Travel Memory AI</h5>
                        <p class="text-white-50 mb-0">Viết caption, tạo nhật ký, gợi ý cảm xúc, story ngắn và địa điểm gần đó.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-soft py-5">
        <div class="container py-5">
            <div class="row g-5 align-items-center">
                <div class="col-lg-5 reveal">
                    <span class="text-primary fw-bold small">TIMELINE HÀNH TRÌNH</span>
                    <h2 class="fw-bold display-6 mt-2">Mỗi mốc thời gian đều có ngày, nơi đến, mood, thời tiết và số ảnh.</h2>
                    <p class="text-muted">Thiết kế mới giúp người dùng đọc lại hành trình như một nhật ký, không phải một danh sách CRUD.</p>
                </div>
                <div class="col-lg-7 reveal reveal-delay-1">
                    <div class="timeline-preview stagger-children">
                        <div class="timeline-row">
                            <div class="small text-muted">21/05/2026</div>
                            <h5 class="fw-bold mb-2">Hải Dương</h5>
                            <div class="d-flex flex-wrap gap-2 small">
                                <span class="badge text-bg-light">Hạnh phúc</span>
                                <span class="badge text-bg-light">Trời đẹp</span>
                                <span class="badge text-bg-light">12 ảnh</span>
                            </div>
                        </div>
                        <div class="timeline-row">
                            <div class="small text-muted">14/04/2026</div>
                            <h5 class="fw-bold mb-2">Cat Ba</h5>
                            <div class="d-flex flex-wrap gap-2 small">
                                <span class="badge text-bg-light">Tự do</span>
                                <span class="badge text-bg-light">Gió biển</span>
                                <span class="badge text-bg-light">28 ảnh</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-4 mt-4 stagger-children">
                <div class="col-6 col-lg-3"><div class="metric"><div class="h3 fw-bold mb-0" data-count="18">0</div><small class="text-muted">địa điểm</small></div></div>
                <div class="col-6 col-lg-3"><div class="metric"><div class="h3 fw-bold mb-0" data-count="520">0</div><small class="text-muted">ảnh đã lưu</small></div></div>
                <div class="col-6 col-lg-3"><div class="metric"><div class="h3 fw-bold mb-0" data-count="6">0</div><small class="text-muted">tỉnh thành</small></div></div>
                <div class="col-6 col-lg-3"><div class="metric"><div class="h3 fw-bold mb-0" data-count="1240">0</div><small class="text-muted">km ký ức</small></div></div>
            </div>
        </div>
    </section>

    <footer class="section-dark py-4 border-top border-white border-opacity-10">
        <div class="container d-flex flex-column flex-md-row justify-content-between gap-2 text-white-50">
            <span>&copy; 2026 Travel Memory Map.</span>
            <span>Built for journeys, photos, friends and feelings.</span>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Navbar thu gọn khi cuộn
        const nav = document.querySelector('.nav-glass');
        window.addEventListener('scroll', () => {
            nav?.classList.toggle('scrolled', window.scrollY > 40);
        }, { passive: true });

        // Scroll reveal
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('revealed');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('.reveal, .stagger-children').forEach(el => revealObserver.observe(el));

        // Đếm số động cho metrics
        function animateCounter(el, target, duration = 1400) {
            const start = performance.now();
            const update = (now) => {
                const progress = Math.min((now - start) / duration, 1);
                const eased = 1 - Math.pow(1 - progress, 3);
                el.textContent = Math.floor(eased * target).toLocaleString('vi-VN');
                if (progress < 1) requestAnimationFrame(update);
                else el.textContent = target.toLocaleString('vi-VN');
            };
            requestAnimationFrame(update);
        }

        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                const target = parseInt(el.dataset.count, 10);
                if (!isNaN(target)) {
                    el.classList.add('metric-counting');
                    animateCounter(el, target);
                }
                counterObserver.unobserve(el);
            });
        }, { threshold: 0.5 });

        document.querySelectorAll('[data-count]').forEach(el => counterObserver.observe(el));
    </script>
</body>
</html>
