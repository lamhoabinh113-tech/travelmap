<?php
// Tính toán thống kê
$total_trips = count($trips);
$total_locations = count($locations);
$total_photos = 0;
$feelings_count = [];

foreach ($locations as $loc) {
    if ($loc['image']) $total_photos++;
    if ($loc['feeling']) {
        $f = $loc['feeling'];
        $feelings_count[$f] = ($feelings_count[$f] ?? 0) + 1;
    }
}
arsort($feelings_count);
$top_feeling = !empty($feelings_count) ? array_key_first($feelings_count) : 'Chưa có';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Thống Kê Hành Trình - Travel Memory Map</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard_mobile.css?v=3.3">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        if (localStorage.getItem('uiTheme') === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
    <style>
        .stats-card {
            background: var(--surface);
            border: 1px solid var(--border);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-radius: 20px;
            box-shadow: var(--neu-shadow-flat);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--neu-shadow-hover);
        }
    </style>
</head>
<body class="py-4">
    
    <div class="container py-4" style="max-width: 768px;">
        <div class="d-flex align-items-center mb-4">
            <a href="index.php?url=location/dashboard" class="btn btn-premium-outline rounded-circle p-0 d-flex align-items-center justify-content-center me-3" style="width: 44px; height: 44px;"><i class="bi bi-arrow-left"></i></a>
            <h3 class="fw-bold mb-0">Thống Kê Của Bạn</h3>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6">
                <div class="stats-card p-3 text-center">
                    <i class="bi bi-briefcase-fill text-primary fs-3 mb-2"></i>
                    <h2 class="fw-bold mb-0 text-strong"><?php echo $total_trips; ?></h2>
                    <small class="text-muted fw-semibold">Chuyến đi</small>
                </div>
            </div>
            <div class="col-6">
                <div class="stats-card p-3 text-center">
                    <i class="bi bi-geo-alt-fill text-danger fs-3 mb-2"></i>
                    <h2 class="fw-bold mb-0 text-strong"><?php echo $total_locations; ?></h2>
                    <small class="text-muted fw-semibold">Điểm đến</small>
                </div>
            </div>
            <div class="col-6">
                <div class="stats-card p-3 text-center">
                    <i class="bi bi-images text-success fs-3 mb-2"></i>
                    <h2 class="fw-bold mb-0 text-strong"><?php echo $total_photos; ?></h2>
                    <small class="text-muted fw-semibold">Kỷ niệm ảnh</small>
                </div>
            </div>
            <div class="col-6">
                <div class="stats-card p-3 text-center">
                    <i class="bi bi-emoji-smile-fill text-warning fs-3 mb-2"></i>
                    <h5 class="fw-bold mb-0 mt-1 text-truncate text-strong"><?php echo $top_feeling; ?></h5>
                    <small class="text-muted fw-semibold">Cảm xúc chính</small>
                </div>
            </div>
        </div>

        <div class="stats-card p-4 mb-4">
            <h5 class="fw-bold mb-4 text-strong"><i class="bi bi-pie-chart-fill me-2 text-primary"></i>Biểu Đồ Cảm Xúc</h5>
            <canvas id="moodChart" height="220"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('moodChart').getContext('2d');
        const feelingsData = <?php echo json_encode($feelings_count); ?>;
        const isDark = localStorage.getItem('uiTheme') === 'dark';
        const labelColor = isDark ? '#94a3b8' : '#64748b';
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(feelingsData),
                datasets: [{
                    data: Object.values(feelingsData),
                    backgroundColor: [
                        '#fbbf24', '#f87171', '#60a5fa', '#34d399', '#a78bfa'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: labelColor,
                            font: {
                                family: 'Outfit',
                                size: 13,
                                weight: '500'
                            },
                            padding: 15
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
