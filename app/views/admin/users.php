<?php require_once '../app/views/admin/_layout_top.php'; ?>

    <!-- TOPBAR -->
    <div class="admin-topbar">
        <div>
            <h4>👥 Quản lý tài khoản</h4>
            <small style="color:var(--admin-muted);">Tổng: <strong><?= number_format($total) ?></strong> tài khoản</small>
        </div>
        <div class="topbar-actions">
            <button class="btn-admin btn-admin-primary" onclick="openAddUserModal()">
                <i class="bi bi-person-plus-fill"></i> Thêm tài khoản
            </button>
        </div>
    </div>

    <div class="admin-content">
        <?php if(isset($_GET['success'])): ?>
        <div style="background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); border-radius:12px; padding:12px 18px; margin-bottom:18px; color:#10b981; font-weight:600; font-size:14px;">
            ✅ Cập nhật thành công!
        </div>
        <?php endif; ?>

        <!-- SEARCH / FILTER BAR -->
        <form method="GET" action="">
            <input type="hidden" name="url" value="admin/users">
            <div class="search-filter-bar">
                <input type="text" name="search" class="admin-input" placeholder="🔍 Tìm theo tên, email, username..." value="<?= htmlspecialchars($_GET['search']??'') ?>">
                <select name="role" class="admin-select">
                    <option value="">Tất cả vai trò</option>
                    <option value="user" <?= ($_GET['role']??'')==='user'?'selected':'' ?>>👤 User</option>
                    <option value="moderator" <?= ($_GET['role']??'')==='moderator'?'selected':'' ?>>🛡️ Moderator</option>
                    <option value="admin" <?= ($_GET['role']??'')==='admin'?'selected':'' ?>>👑 Admin</option>
                </select>
                <select name="status" class="admin-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active" <?= ($_GET['status']??'')==='active'?'selected':'' ?>>✅ Hoạt động</option>
                    <option value="locked" <?= ($_GET['status']??'')==='locked'?'selected':'' ?>>🔒 Bị khóa</option>
                </select>
                <button type="submit" class="btn-admin btn-admin-primary">Lọc</button>
                <a href="index.php?url=admin/users" class="btn-admin" style="background:var(--admin-border); color:var(--admin-text);">Xóa lọc</a>
            </div>
        </form>

        <!-- TABLE -->
        <div class="admin-table">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người dùng</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Bài đăng</th>
                        <th>Đăng nhập cuối</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                <?php if(empty($users)): ?>
                    <tr><td colspan="9" style="text-align:center; color:var(--admin-muted); padding:40px;">Không tìm thấy tài khoản nào.</td></tr>
                <?php else: ?>
                <?php foreach($users as $u): ?>
                <tr id="user-row-<?= $u['id'] ?>">
                    <td style="color:var(--admin-muted); font-size:12px;">#<?= $u['id'] ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="user-avatar-mini"><?= strtoupper(substr($u['username'],0,1)) ?></div>
                            <div>
                                <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($u['full_name']) ?></div>
                                <div style="font-size:11px; color:var(--admin-muted);">@<?= htmlspecialchars($u['username']) ?></div>
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px; color:var(--admin-muted);"><?= htmlspecialchars($u['email']) ?></td>
                    <td><span class="badge-role badge-<?= $u['role']??'user' ?>"><?= strtoupper($u['role']??'USER') ?></span></td>
                    <td>
                        <?php if($u['is_locked']): ?>
                            <span class="badge-role badge-locked">🔒 Bị khóa</span>
                        <?php else: ?>
                            <span class="badge-role badge-active">✅ Hoạt động</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight:700;"><?= $u['post_count'] ?></td>
                    <td style="font-size:12px; color:var(--admin-muted);">
                        <?= $u['last_login'] ? date('H:i d/m/Y', strtotime($u['last_login'])) : 'Chưa đăng nhập' ?>
                    </td>
                    <td style="font-size:12px; color:var(--admin-muted);"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            <button class="btn-admin btn-admin-primary btn-admin-sm" onclick="openEditModal(<?= htmlspecialchars(json_encode($u)) ?>)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn-admin btn-admin-warning btn-admin-sm" onclick="doToggleLock(<?= $u['id'] ?>, '<?= $u['is_locked']?'Mở khóa':'Khóa' ?>')" title="<?= $u['is_locked']?'Mở khóa':'Khóa tài khoản' ?>">
                                <i class="bi bi-<?= $u['is_locked']?'unlock':'lock' ?>"></i>
                            </button>
                            <button class="btn-admin btn-admin-success btn-admin-sm" onclick="openResetPassModal(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>')" title="Reset mật khẩu">
                                <i class="bi bi-key"></i>
                            </button>
                            <?php if($u['id'] != $_SESSION['user_id']): ?>
                            <button class="btn-admin btn-admin-danger btn-admin-sm" onclick="doDeleteUser(<?= $u['id'] ?>, '<?= htmlspecialchars($u['full_name']) ?>')" title="Xóa tài khoản">
                                <i class="bi bi-trash"></i>
                            </button>
                            <?php endif; ?>
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
                    <a href="?url=admin/users&page=<?= $page-1 ?>&search=<?= urlencode($_GET['search']??'') ?>&role=<?= $_GET['role']??'' ?>&status=<?= $_GET['status']??'' ?>">← Trước</a>
                <?php endif; ?>
                <?php for($p = max(1,$page-2); $p <= min($totalPages,$page+2); $p++): ?>
                    <<?= $p==$page?'span class="active-page"':'a href="?url=admin/users&page='.$p.'&search='.urlencode($_GET['search']??'').'&role='.($_GET['role']??'').'&status='.($_GET['status']??'').'"' ?>><?= $p ?></<?= $p==$page?'span':'a' ?>>
                <?php endfor; ?>
                <?php if($page < $totalPages): ?>
                    <a href="?url=admin/users&page=<?= $page+1 ?>&search=<?= urlencode($_GET['search']??'') ?>&role=<?= $_GET['role']??'' ?>&status=<?= $_GET['status']??'' ?>">Tiếp →</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

<!-- EDIT USER MODAL -->
<div class="admin-modal-backdrop" id="editUserModal">
    <div class="admin-modal">
        <h5>✏️ Chỉnh sửa tài khoản</h5>
        <form action="index.php?url=admin/editUser" method="POST">
            <input type="hidden" name="id" id="edit_uid">
            <label>HỌ TÊN</label>
            <input type="text" name="full_name" id="edit_full_name" class="admin-input" required>
            <label>EMAIL</label>
            <input type="email" name="email" id="edit_email" class="admin-input" required>
            <label>VAI TRÒ</label>
            <select name="role" id="edit_role" class="admin-select">
                <option value="user">👤 User</option>
                <option value="moderator">🛡️ Moderator</option>
                <option value="admin">👑 Admin</option>
            </select>
            <label>TRẠNG THÁI</label>
            <select name="is_locked" id="edit_is_locked" class="admin-select">
                <option value="0">✅ Hoạt động</option>
                <option value="1">🔒 Khóa tài khoản</option>
            </select>
            <div class="d-flex gap-3 mt-4">
                <button type="submit" class="btn-admin btn-admin-primary flex-fill">Lưu thay đổi</button>
                <button type="button" class="btn-admin flex-fill" style="background:var(--admin-border);" onclick="closeModal('editUserModal')">Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- RESET PASSWORD MODAL -->
<div class="admin-modal-backdrop" id="resetPassModal">
    <div class="admin-modal">
        <h5>🔑 Reset mật khẩu</h5>
        <p style="color:var(--admin-muted); font-size:13px;">Đặt mật khẩu mới cho: <strong id="reset_uname"></strong></p>
        <label>MẬT KHẨU MỚI</label>
        <input type="text" id="reset_new_pass" class="admin-input" placeholder="Tối thiểu 6 ký tự...">
        <div class="d-flex gap-3 mt-4">
            <button class="btn-admin btn-admin-success flex-fill" onclick="doResetPass()">Đặt mật khẩu</button>
            <button type="button" class="btn-admin flex-fill" style="background:var(--admin-border);" onclick="closeModal('resetPassModal')">Hủy</button>
        </div>
    </div>
</div>

<script>
function openEditModal(u) {
    document.getElementById('edit_uid').value = u.id;
    document.getElementById('edit_full_name').value = u.full_name;
    document.getElementById('edit_email').value = u.email;
    document.getElementById('edit_role').value = u.role || 'user';
    document.getElementById('edit_is_locked').value = u.is_locked || '0';
    document.getElementById('editUserModal').classList.add('show');
}

let resetUserId = null;
function openResetPassModal(id, username) {
    resetUserId = id;
    document.getElementById('reset_uname').textContent = username;
    document.getElementById('reset_new_pass').value = '';
    document.getElementById('resetPassModal').classList.add('show');
}

function doResetPass() {
    const pass = document.getElementById('reset_new_pass').value;
    if (pass.length < 6) { showToast('Lỗi', 'Mật khẩu phải có ít nhất 6 ký tự', '❌'); return; }
    fetch('index.php?url=admin/resetPassword', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${resetUserId}&new_password=${encodeURIComponent(pass)}`
    }).then(r=>r.json()).then(d => {
        if(d.success) { showToast('Thành công','Đã reset mật khẩu','✅'); closeModal('resetPassModal'); }
        else showToast('Lỗi', d.msg||'Thất bại','❌');
    });
}

function doToggleLock(id, action) {
    showConfirm(`${action} tài khoản`, `Bạn có muốn ${action.toLowerCase()} tài khoản #${id}?`, () => {
        adminAjax(`index.php?url=admin/toggleLock&id=${id}`, `Đã ${action.toLowerCase()} tài khoản`, 'Thất bại', () => location.reload());
    }, '🔒');
}

function doDeleteUser(id, name) {
    showConfirm('Xóa tài khoản', `Xóa vĩnh viễn tài khoản "${name}"? Toàn bộ dữ liệu sẽ bị xóa.`, () => {
        adminAjax(`index.php?url=admin/deleteUser&id=${id}`, 'Đã xóa tài khoản', 'Thất bại', () => {
            document.getElementById(`user-row-${id}`)?.remove();
        });
    }, '🗑️');
}

function closeModal(id) { document.getElementById(id).classList.remove('show'); }
function openAddUserModal() { showToast('Gợi ý','Dùng nút Đăng ký để tạo tài khoản mới','ℹ️'); }
// Close modal clicking backdrop
document.querySelectorAll('.admin-modal-backdrop').forEach(el => {
    el.addEventListener('click', e => { if(e.target === el) el.classList.remove('show'); });
});
</script>

<?php require_once '../app/views/admin/_layout_bottom.php'; ?>
