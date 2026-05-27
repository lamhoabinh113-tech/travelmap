<?php require_once '../app/views/admin/_layout_top.php'; ?>

    <div class="admin-topbar">
        <div>
            <h4>📋 Nhật ký hoạt động hệ thống</h4>
            <small style="color:var(--admin-muted);">Tổng: <strong><?= number_format($total) ?></strong> bản ghi</small>
        </div>
    </div>

    <div class="admin-content">
        <div class="admin-table">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Thời gian</th>
                        <th>Admin</th>
                        <th>Hành động</th>
                        <th>Đối tượng</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($logs)): ?>
                    <tr><td colspan="6" style="text-align:center; color:var(--admin-muted); padding:40px;">Chưa có nhật ký nào.</td></tr>
                <?php else: ?>
                <?php foreach($logs as $log): ?>
                <tr>
                    <td style="color:var(--admin-muted); font-size:12px;"><?= $log['id'] ?></td>
                    <td style="font-size:12px; white-space:nowrap; color:var(--admin-muted);">
                        <?= date('H:i:s', strtotime($log['created_at'])) ?><br>
                        <span style="font-size:11px;"><?= date('d/m/Y', strtotime($log['created_at'])) ?></span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar-mini" style="background:linear-gradient(135deg,#f43f5e,#6366f1);">
                                <?= strtoupper(substr($log['username']??'?', 0, 1)) ?>
                            </div>
                            <div>
                                <div style="font-size:13px; font-weight:600;"><?= htmlspecialchars($log['full_name']??'') ?></div>
                                <div style="font-size:11px; color:var(--admin-muted);">@<?= $log['username']??'' ?></div>
                            </div>
                        </div>
                    </td>
                    <td><span class="log-action"><?= htmlspecialchars($log['action']) ?></span></td>
                    <td style="font-size:12px; color:var(--admin-muted);">
                        <?php if($log['target_type']): ?>
                            <span style="background:rgba(255,255,255,0.05); padding:2px 8px; border-radius:6px;">
                                <?= htmlspecialchars($log['target_type']) ?>
                                <?= $log['target_id'] ? ' #'.$log['target_id'] : '' ?>
                            </span>
                        <?php else: ?>–<?php endif; ?>
                    </td>
                    <td style="font-size:12px; color:var(--admin-muted); max-width:250px; word-break:break-word;">
                        <?= htmlspecialchars($log['detail']??'') ?>
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
                <?php if($page > 1): ?><a href="?url=admin/activityLog&page=<?= $page-1 ?>">← Trước</a><?php endif; ?>
                <?php for($p2=max(1,$page-2); $p2<=min($totalPages,$page+2); $p2++): ?>
                    <<?= $p2==$page?'span class="active-page"':'a href="?url=admin/activityLog&page='.$p2.'"' ?>><?= $p2 ?></<?= $p2==$page?'span':'a' ?>>
                <?php endfor; ?>
                <?php if($page < $totalPages): ?><a href="?url=admin/activityLog&page=<?= $page+1 ?>">Tiếp →</a><?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

<?php require_once '../app/views/admin/_layout_bottom.php'; ?>
