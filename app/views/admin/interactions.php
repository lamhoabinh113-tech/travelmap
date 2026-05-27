<?php require_once '../app/views/admin/_layout_top.php'; ?>

    <div class="admin-topbar">
        <div>
            <h4>🤝 Quản lý tương tác</h4>
            <small style="color:var(--admin-muted);">Danh sách kết bạn & Lượt thích</small>
        </div>
    </div>

    <div class="admin-content">
        <?php
            // Đếm tổng lượt thích từ DB
            $total_likes_count = $db_likes_count ?? 0;
            try {
                global $db_connection;
                // Lấy từ biến $db được truyền xuống view từ controller (qua AdminModel)
                $total_likes_count = $likes_total ?? 0;
            } catch(Exception $e) {}

            // Đếm pending friendships
            $pending_count = 0;
            foreach($friendships as $f) {
                if(($f['status'] ?? '') === 'pending') $pending_count++;
            }
        ?>
        <!-- Stats mini -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(99,102,241,0.15);">🤝</div>
                    <div class="stat-value"><?= number_format($total) ?></div>
                    <div class="stat-label">Tổng quan hệ kết bạn</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(244,63,94,0.15);">❤️</div>
                    <div class="stat-value"><?= number_format($likes_total ?? 0) ?></div>
                    <div class="stat-label">Tổng lượt thích</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="stat-icon" style="background:rgba(245,158,11,0.15);">⏳</div>
                    <div class="stat-value"><?= $pending_count ?></div>
                    <div class="stat-label">Yêu cầu đang chờ</div>
                </div>
            </div>
        </div>


        <!-- FRIENDSHIPS TABLE -->
        <div class="admin-table mb-4">
            <div class="table-header">
                <h6>🤝 Danh sách kết bạn</h6>
                <span style="font-size:13px; color:var(--admin-muted);"><?= $total ?> quan hệ</span>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Người gửi</th>
                        <th>Người nhận</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($friendships)): ?>
                    <tr><td colspan="6" style="text-align:center; color:var(--admin-muted); padding:40px;">Chưa có quan hệ kết bạn nào.</td></tr>
                <?php else: ?>
                <?php foreach($friendships as $f): ?>
                <tr id="friend-row-<?= $f['id'] ?>">
                    <td style="color:var(--admin-muted); font-size:12px;">#<?= $f['id'] ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar-mini"><?= strtoupper(substr($f['user_uname'],0,1)) ?></div>
                            <div>
                                <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($f['user_name']) ?></div>
                                <div style="font-size:11px; color:var(--admin-muted);">@<?= $f['user_uname'] ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar-mini" style="background:linear-gradient(135deg,#f43f5e,#fb923c)"><?= strtoupper(substr($f['friend_uname'],0,1)) ?></div>
                            <div>
                                <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($f['friend_name']) ?></div>
                                <div style="font-size:11px; color:var(--admin-muted);">@<?= $f['friend_uname'] ?></div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <?php if($f['status']==='accepted'): ?>
                            <span class="badge-role badge-active">✅ Đã kết bạn</span>
                        <?php else: ?>
                            <span class="badge-role badge-warning" style="background:rgba(245,158,11,0.15);color:#f59e0b;">⏳ Đang chờ</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:12px; color:var(--admin-muted);"><?= date('d/m/Y H:i', strtotime($f['created_at'])) ?></td>
                    <td>
                        <button class="btn-admin btn-admin-danger btn-admin-sm" onclick="doDeleteFriendship(<?= $f['id'] ?>, '<?= htmlspecialchars($f['user_name']) ?>', '<?= htmlspecialchars($f['friend_name']) ?>')">
                            <i class="bi bi-person-x"></i> Hủy
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <?php if($totalPages > 1): ?>
        <div class="d-flex justify-content-center mt-3">
            <div class="admin-pagination">
                <?php if($page > 1): ?><a href="?url=admin/interactions&page=<?= $page-1 ?>">← Trước</a><?php endif; ?>
                <?php for($p2=max(1,$page-2); $p2<=min($totalPages,$page+2); $p2++): ?>
                    <<?= $p2==$page?'span class="active-page"':'a href="?url=admin/interactions&page='.$p2.'"' ?>><?= $p2 ?></<?= $p2==$page?'span':'a' ?>>
                <?php endfor; ?>
                <?php if($page < $totalPages): ?><a href="?url=admin/interactions&page=<?= $page+1 ?>">Tiếp →</a><?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

<script>
function doDeleteFriendship(id, u1, u2) {
    showConfirm('Hủy kết bạn', `Hủy quan hệ bạn bè giữa "${u1}" và "${u2}"?`, () => {
        adminAjax(`index.php?url=admin/deleteFriendship&id=${id}`, 'Đã hủy kết bạn', 'Thất bại', () => {
            document.getElementById(`friend-row-${id}`)?.remove();
        });
    }, '💔');
}
</script>

<?php require_once '../app/views/admin/_layout_bottom.php'; ?>
