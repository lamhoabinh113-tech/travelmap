<?php require_once '../app/views/admin/_layout_top.php'; ?>

    <!-- TOPBAR -->
    <div class="admin-topbar">
        <div>
            <h4>📊 Dashboard</h4>
            <small style="color:var(--admin-muted);">Tổng quan hệ thống – <?= date('d/m/Y H:i') ?></small>
        </div>
        <div class="topbar-actions">
            <button class="btn-admin btn-admin-primary" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise"></i> Làm mới
            </button>
        </div>
    </div>

    <div class="admin-content">

        <!-- STAT CARDS -->
        <div class="row g-3 mb-4">
            <?php
            $cards = [
                ['icon'=>'👥','label'=>'Tổng tài khoản','value'=>$stats['total_users'],'color'=>'rgba(99,102,241,0.15)','change'=>'+'. $stats['new_users_week'] .' tuần này','up'=>true],
                ['icon'=>'📍','label'=>'Bài đăng','value'=>$stats['total_locations'],'color'=>'rgba(244,63,94,0.15)','change'=>$stats['posts_today'].' hôm nay','up'=>true],
                ['icon'=>'🖼️','label'=>'Ảnh đã upload','value'=>$stats['total_images'],'color'=>'rgba(16,185,129,0.15)','change'=>'Tổng ảnh trong album','up'=>null],
                ['icon'=>'❤️','label'=>'Lượt thích','value'=>$stats['total_likes'],'color'=>'rgba(245,158,11,0.15)','change'=>'Tổng tương tác','up'=>null],
                ['icon'=>'🤝','label'=>'Kết bạn','value'=>$stats['total_friends'],'color'=>'rgba(139,92,246,0.15)','change'=>'Đã chấp nhận','up'=>null],
                ['icon'=>'🔑','label'=>'Đăng nhập hôm nay','value'=>$stats['logins_today'],'color'=>'rgba(20,184,166,0.15)','change'=>'Lần đăng nhập','up'=>null],
            ];
            foreach($cards as $c): ?>
            <div class="col-6 col-md-4 col-lg-2">
                <div class="stat-card h-100">
                    <div class="stat-icon" style="background:<?= $c['color'] ?>; font-size:22px;">
                        <?= $c['icon'] ?>
                    </div>
                    <div class="stat-value"><?= number_format($c['value']) ?></div>
                    <div class="stat-label"><?= $c['label'] ?></div>
                    <div class="stat-change" style="color:<?= $c['up']===true?'#10b981':($c['up']===false?'#f43f5e':'var(--admin-muted)') ?>">
                        <?= $c['up']===true?'▲ ':($c['up']===false?'▼ ':'') ?><?= $c['change'] ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- CHARTS ROW 1 -->
        <div class="row g-3 mb-4">
            <div class="col-lg-8">
                <div class="chart-card h-100">
                    <h6>📈 BÀI ĐĂNG MỚI THEO NGÀY (14 ngày)</h6>
                    <canvas id="postsChart" height="90"></canvas>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="chart-card h-100">
                    <h6>🎭 PHÂN BỐ CẢM XÚC</h6>
                    <canvas id="feelingsChart" height="180"></canvas>
                </div>
            </div>
        </div>

        <!-- CHARTS ROW 2 -->
        <div class="row g-3 mb-4">
            <div class="col-lg-6">
                <div class="chart-card h-100">
                    <h6>🔑 ĐĂNG NHẬP THEO NGÀY (14 ngày)</h6>
                    <canvas id="loginsChart" height="100"></canvas>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="chart-card h-100">
                    <h6>🏆 TOP NGƯỜI DÙNG HOẠT ĐỘNG</h6>
                    <canvas id="topUsersChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- BOTTOM ROW: Top users table + Recent logs -->
        <div class="row g-3">
            <div class="col-lg-7">
                <div class="admin-table">
                    <div class="table-header">
                        <h6>🏆 Top người dùng hoạt động</h6>
                        <a href="index.php?url=admin/users" class="btn-admin btn-admin-primary btn-admin-sm">Xem tất cả</a>
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Người dùng</th>
                                <th>Vai trò</th>
                                <th>Bài đăng</th>
                                <th>Tiến độ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $maxPosts = $top_users[0]['post_count'] ?? 1; ?>
                            <?php foreach($top_users as $i => $u): ?>
                            <tr>
                                <td><strong style="color:var(--admin-muted)"><?= $i+1 ?></strong></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="user-avatar-mini"><?= strtoupper(substr($u['username'],0,1)) ?></div>
                                        <div>
                                            <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($u['full_name']) ?></div>
                                            <div style="font-size:11px; color:var(--admin-muted);">@<?= $u['username'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><span class="badge-role badge-<?= $u['role']??'user' ?>"><?= strtoupper($u['role']??'USER') ?></span></td>
                                <td><strong><?= $u['post_count'] ?></strong></td>
                                <td style="width:100px;">
                                    <div class="admin-progress">
                                        <div class="admin-progress-bar" style="width:<?= $maxPosts>0?round($u['post_count']/$maxPosts*100):0 ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="admin-table">
                    <div class="table-header">
                        <h6>📋 Nhật ký gần đây</h6>
                        <a href="index.php?url=admin/activityLog" class="btn-admin btn-admin-primary btn-admin-sm">Xem tất cả</a>
                    </div>
                    <div style="padding: 4px 0;">
                        <?php if(empty($recent_logs)): ?>
                        <div style="padding:24px; text-align:center; color:var(--admin-muted); font-size:13px;">Chưa có nhật ký nào.</div>
                        <?php else: ?>
                        <?php foreach($recent_logs as $log): ?>
                        <div style="padding:12px 20px; border-bottom:1px solid var(--admin-border); display:flex; gap:12px; align-items:flex-start;">
                            <div class="user-avatar-mini" style="flex-shrink:0;"><?= strtoupper(substr($log['username']??'?',0,1)) ?></div>
                            <div>
                                <div style="font-size:13px; font-weight:600;"><?= htmlspecialchars($log['action']) ?></div>
                                <div style="font-size:11px; color:var(--admin-muted);">
                                    <?= htmlspecialchars($log['full_name']??'') ?> &bull;
                                    <?= date('H:i d/m', strtotime($log['created_at'])) ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- end admin-content -->

<script>
// ---- CHART DATA ----
const postsData = <?= json_encode($posts_chart) ?>;
const loginsData = <?= json_encode($logins_chart) ?>;
const feelingsData = <?= json_encode($feelings) ?>;
const topUsersData = <?= json_encode($top_users) ?>;

// Fill missing days
function fillDays(data, days = 14) {
    const result = {};
    for (let i = days - 1; i >= 0; i--) {
        const d = new Date(); d.setDate(d.getDate() - i);
        result[d.toISOString().slice(0, 10)] = 0;
    }
    data.forEach(r => { if (result[r.day] !== undefined) result[r.day] = parseInt(r.count); });
    return result;
}

const chartDefaults = {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748b', font: { size: 11 } } },
        y: { grid: { color: 'rgba(255,255,255,0.04)' }, ticks: { color: '#64748b', font: { size: 11 } }, beginAtZero: true }
    }
};

// Posts Chart
const pd = fillDays(postsData);
new Chart(document.getElementById('postsChart'), {
    type: 'bar',
    data: {
        labels: Object.keys(pd).map(d => d.slice(5)),
        datasets: [{
            data: Object.values(pd),
            backgroundColor: 'rgba(99,102,241,0.6)',
            borderColor: '#6366f1',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: { ...chartDefaults }
});

// Logins Chart
const ld = fillDays(loginsData);
new Chart(document.getElementById('loginsChart'), {
    type: 'line',
    data: {
        labels: Object.keys(ld).map(d => d.slice(5)),
        datasets: [{
            data: Object.values(ld),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16,185,129,0.1)',
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#10b981',
            pointRadius: 4,
        }]
    },
    options: { ...chartDefaults }
});

// Feelings Doughnut
const feelingColors = ['#6366f1','#f43f5e','#f59e0b','#10b981','#8b5cf6','#06b6d4'];
new Chart(document.getElementById('feelingsChart'), {
    type: 'doughnut',
    data: {
        labels: feelingsData.map(f => f.feeling),
        datasets: [{
            data: feelingsData.map(f => f.count),
            backgroundColor: feelingColors,
            borderColor: '#1e2029',
            borderWidth: 3,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: '#94a3b8', font: { size: 11 }, padding: 10, boxWidth: 12 }
            }
        },
        cutout: '65%'
    }
});

// Top Users Bar
new Chart(document.getElementById('topUsersChart'), {
    type: 'bar',
    data: {
        labels: topUsersData.map(u => u.username),
        datasets: [{
            data: topUsersData.map(u => u.post_count),
            backgroundColor: 'rgba(244,63,94,0.6)',
            borderColor: '#f43f5e',
            borderWidth: 1,
            borderRadius: 6,
        }]
    },
    options: {
        ...chartDefaults,
        indexAxis: 'y',
    }
});
</script>

<?php require_once '../app/views/admin/_layout_bottom.php'; ?>
