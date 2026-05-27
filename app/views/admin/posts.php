<?php require_once '../app/views/admin/_layout_top.php'; ?>

    <div class="admin-topbar">
        <div>
            <h4>🖼️ Quản lý bài đăng & Ảnh</h4>
            <small style="color:var(--admin-muted);">Tổng: <strong><?= number_format($total) ?></strong> bài đăng</small>
        </div>
    </div>

    <div class="admin-content">
        <!-- SEARCH BAR -->
        <form method="GET" action="">
            <input type="hidden" name="url" value="admin/posts">
            <div class="search-filter-bar">
                <input type="text" name="search" class="admin-input" placeholder="🔍 Tìm theo tên địa điểm, mô tả..." value="<?= htmlspecialchars($_GET['search']??'') ?>">
                <button type="submit" class="btn-admin btn-admin-primary">Lọc</button>
                <a href="index.php?url=admin/posts" class="btn-admin" style="background:var(--admin-border); color:var(--admin-text);">Xóa lọc</a>
            </div>
        </form>

        <div class="admin-table">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Địa điểm</th>
                        <th>Người đăng</th>
                        <th>Cảm xúc</th>
                        <th>Ảnh</th>
                        <th>❤️</th>
                        <th>Trạng thái</th>
                        <th>Ngày đăng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($posts)): ?>
                    <tr><td colspan="10" style="text-align:center; color:var(--admin-muted); padding:40px;">Không có bài đăng nào.</td></tr>
                <?php else: ?>
                <?php foreach($posts as $p): ?>
                <tr id="post-row-<?= $p['id'] ?>">
                    <td style="color:var(--admin-muted); font-size:12px;">#<?= $p['id'] ?></td>
                    <td>
                        <?php if($p['image']): ?>
                            <img src="../uploads/<?= htmlspecialchars($p['image']) ?>" 
                                 style="width:44px; height:44px; object-fit:cover; border-radius:10px; border:2px solid var(--admin-border);"
                                 onerror="this.style.display='none'">
                        <?php else: ?>
                            <div style="width:44px; height:44px; border-radius:10px; background:var(--admin-border); display:flex; align-items:center; justify-content:center; font-size:18px;">📍</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="font-weight:600; font-size:13px; max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($p['place_name']) ?></div>
                        <div style="font-size:11px; color:var(--admin-muted); max-width:180px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?= htmlspecialchars($p['description']??'') ?></div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar-mini"><?= strtoupper(substr($p['username'],0,1)) ?></div>
                            <div>
                                <div style="font-weight:600; font-size:12px;"><?= htmlspecialchars($p['full_name']) ?></div>
                                <div style="font-size:11px; color:var(--admin-muted);">@<?= $p['username'] ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;"><?= htmlspecialchars($p['feeling']??'') ?></td>
                    <td style="font-weight:700; color:var(--admin-primary);"><?= $p['image_count'] ?></td>
                    <td style="font-weight:700; color:#f43f5e;"><?= $p['like_count'] ?></td>
                    <td>
                        <?php if($p['is_hidden']??0): ?>
                            <span class="badge-role badge-hidden">👁️ Ẩn</span>
                        <?php else: ?>
                            <span class="badge-role badge-active">✅ Hiện</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-size:12px; color:var(--admin-muted); white-space:nowrap;"><?= date('H:i d/m/Y', strtotime($p['created_at'])) ?></td>
                    <td>
                        <div class="d-flex gap-1">
                            <button class="btn-admin btn-admin-warning btn-admin-sm" onclick="doToggleHide(<?= $p['id'] ?>, <?= $p['is_hidden']??0 ?>)" title="<?= ($p['is_hidden']??0)?'Hiện bài':'Ẩn bài' ?>">
                                <i class="bi bi-eye<?= ($p['is_hidden']??0)?'':'-slash' ?>"></i>
                            </button>
                            <button class="btn-admin btn-admin-danger btn-admin-sm" onclick="doDeletePost(<?= $p['id'] ?>, '<?= htmlspecialchars(addslashes($p['place_name'])) ?>')" title="Xóa bài đăng">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
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
                <?php if($page > 1): ?>
                    <a href="?url=admin/posts&page=<?= $page-1 ?>&search=<?= urlencode($_GET['search']??'') ?>">← Trước</a>
                <?php endif; ?>
                <?php for($p2 = max(1,$page-2); $p2 <= min($totalPages,$page+2); $p2++): ?>
                    <<?= $p2==$page?'span class="active-page"':'a href="?url=admin/posts&page='.$p2.'&search='.urlencode($_GET['search']??'').'"' ?>><?= $p2 ?></<?= $p2==$page?'span':'a' ?>>
                <?php endfor; ?>
                <?php if($page < $totalPages): ?>
                    <a href="?url=admin/posts&page=<?= $page+1 ?>&search=<?= urlencode($_GET['search']??'') ?>">Tiếp →</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

<script>
function doToggleHide(id, isHidden) {
    const action = isHidden ? 'Hiện' : 'Ẩn';
    showConfirm(`${action} bài đăng`, `${action} bài đăng #${id}?`, () => {
        adminAjax(`index.php?url=admin/toggleHidePost&id=${id}`, `Đã ${action.toLowerCase()} bài đăng`, 'Thất bại', () => location.reload());
    }, '👁️');
}
function doDeletePost(id, name) {
    showConfirm('Xóa bài đăng', `Xóa vĩnh viễn bài "${name}" và toàn bộ ảnh?`, () => {
        adminAjax(`index.php?url=admin/deletePost&id=${id}`, 'Đã xóa bài đăng', 'Thất bại', () => {
            document.getElementById(`post-row-${id}`)?.remove();
        });
    }, '🗑️');
}
</script>

<?php require_once '../app/views/admin/_layout_bottom.php'; ?>
