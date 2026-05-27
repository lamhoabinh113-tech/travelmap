<?php require_once '../app/views/admin/_layout_top.php'; ?>

    <div class="admin-topbar">
        <div>
            <h4>🔑 Lịch sử đăng nhập</h4>
            <small style="color:var(--admin-muted);">Tổng: <strong><?= number_format($total) ?></strong> bản ghi</small>
        </div>
    </div>

    <div class="admin-content">
        <!-- FILTER BAR -->
        <form method="GET" action="">
            <input type="hidden" name="url" value="admin/loginLogs">
            <div class="search-filter-bar">
                <input type="text" name="user" class="admin-input" placeholder="🔍 Tìm theo username, tên..." value="<?= htmlspecialchars($_GET['user']??'') ?>">
                <input type="date" name="from" class="admin-input" value="<?= htmlspecialchars($_GET['from']??'') ?>" style="max-width:160px;">
                <input type="date" name="to" class="admin-input" value="<?= htmlspecialchars($_GET['to']??'') ?>" style="max-width:160px;">
                <button type="submit" class="btn-admin btn-admin-primary">Lọc</button>
                <a href="index.php?url=admin/loginLogs" class="btn-admin" style="background:var(--admin-border); color:var(--admin-text);">Xóa lọc</a>
            </div>
        </form>

        <div class="admin-table">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tài khoản</th>
                        <th>Thời gian đăng nhập</th>
                        <th>Thời gian đăng xuất</th>
                        <th>IP</th>
                        <th>Trình duyệt / Thiết bị</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($logs)): ?>
                    <tr><td colspan="7" style="text-align:center; color:var(--admin-muted); padding:40px;">Không có bản ghi nào.</td></tr>
                <?php else: ?>
                <?php foreach($logs as $log): ?>
                <?php
                    $ua = $log['user_agent'] ?? '';
                    // Simple browser/device detection
                    $browser = 'Unknown';
                    if (str_contains($ua, 'Chrome')) $browser = '🌐 Chrome';
                    elseif (str_contains($ua, 'Firefox')) $browser = '🦊 Firefox';
                    elseif (str_contains($ua, 'Safari')) $browser = '🧭 Safari';
                    elseif (str_contains($ua, 'Edge')) $browser = '🌊 Edge';
                    $device = 'Desktop';
                    if (str_contains($ua, 'Mobile') || str_contains($ua, 'Android') || str_contains($ua, 'iPhone')) $device = '📱 Mobile';
                ?>
                <tr>
                    <td style="color:var(--admin-muted); font-size:12px;"><?= $log['id'] ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar-mini"><?= strtoupper(substr($log['username']??'?',0,1)) ?></div>
                            <div>
                                <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($log['full_name']??'') ?></div>
                                <div style="font-size:11px; color:var(--admin-muted);">@<?= htmlspecialchars($log['username']??'') ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px; white-space:nowrap;">
                        <?= $log['login_time'] ? date('H:i:s d/m/Y', strtotime($log['login_time'])) : '–' ?>
                    </td>
                    <td style="font-size:13px; white-space:nowrap; color:var(--admin-muted);">
                        <?= $log['logout_time'] ? date('H:i:s d/m/Y', strtotime($log['logout_time'])) : '–' ?>
                    </td>
                    <td>
                        <code style="background:rgba(255,255,255,0.05); padding:3px 8px; border-radius:6px; font-size:12px;">
                            <?= htmlspecialchars($log['ip_address'] ?? 'N/A') ?>
                        </code>
                    </td>
                    <td style="font-size:12px;">
                        <div><?= $browser ?></div>
                        <div style="color:var(--admin-muted);"><?= $device ?></div>
                    </td>
                    <td>
                        <?php $s = $log['status'] ?? 'success'; ?>
                        <span class="badge-role <?= $s==='success'?'badge-active':'badge-locked' ?>">
                            <?= $s==='success' ? '✅ Thành công' : '❌ Thất bại' ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <?php if($totalPages > 1): ?>
        <div class="d-flex justify-content-center mt-4">
            <div class="admin-pagination">
                <?php if($page > 1): ?><a href="?url=admin/loginLogs&page=<?= $page-1 ?>&user=<?= urlencode($_GET['user']??'') ?>&from=<?= $_GET['from']??'' ?>&to=<?= $_GET['to']??'' ?>">← Trước</a><?php endif; ?>
                <?php for($p2=max(1,$page-2); $p2<=min($totalPages,$page+2); $p2++): ?>
                    <<?= $p2==$page?'span class="active-page"':'a href="?url=admin/loginLogs&page='.$p2.'"' ?>><?= $p2 ?></<?= $p2==$page?'span':'a' ?>>
                <?php endfor; ?>
                <?php if($page < $totalPages): ?><a href="?url=admin/loginLogs&page=<?= $page+1 ?>">Tiếp →</a><?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

<?php require_once '../app/views/admin/_layout_bottom.php'; ?>
