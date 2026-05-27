</main><!-- end admin-main -->

<!-- CONFIRM DIALOG -->
<div class="confirm-dialog" id="confirmDialog">
    <div class="confirm-box">
        <div class="confirm-icon" id="confirmIcon">⚠️</div>
        <h6 id="confirmTitle">Xác nhận hành động</h6>
        <p id="confirmMessage">Bạn có chắc chắn muốn thực hiện hành động này?</p>
        <div class="d-flex gap-3 justify-content-center">
            <button class="btn-admin btn-admin-danger" id="confirmOk">Xác nhận</button>
            <button class="btn-admin" style="background:var(--admin-border); color:var(--admin-text);" onclick="closeConfirm()">Hủy</button>
        </div>
    </div>
</div>

<!-- TOAST NOTIFICATION -->
<div id="adminToast" style="display:none; position:fixed; bottom:24px; right:24px; z-index:4000; min-width:280px;">
    <div style="background:var(--admin-card); border:1px solid var(--admin-border); border-radius:16px; padding:16px 20px; display:flex; align-items:center; gap:12px; box-shadow:0 10px 30px rgba(0,0,0,0.4);">
        <span id="toastIcon" style="font-size:22px;">✅</span>
        <div>
            <div id="toastTitle" style="font-weight:700; font-size:14px;"></div>
            <div id="toastMsg" style="font-size:12px; color:var(--admin-muted);"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar toggle for mobile
    const sidebarToggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');
    if (window.innerWidth <= 992) { sidebarToggleBtn.style.display = 'flex'; }
    sidebarToggleBtn?.addEventListener('click', () => sidebar.classList.toggle('open'));

    // Toast
    function showToast(title, msg, icon='✅') {
        document.getElementById('toastTitle').textContent = title;
        document.getElementById('toastMsg').textContent = msg;
        document.getElementById('toastIcon').textContent = icon;
        const t = document.getElementById('adminToast');
        t.style.display = 'block';
        t.style.animation = 'none';
        setTimeout(() => t.style.animation = '', 10);
        setTimeout(() => t.style.display = 'none', 3500);
    }

    // Confirm dialog
    let confirmCallback = null;
    function showConfirm(title, msg, callback, icon='⚠️') {
        document.getElementById('confirmTitle').textContent = title;
        document.getElementById('confirmMessage').textContent = msg;
        document.getElementById('confirmIcon').textContent = icon;
        document.getElementById('confirmDialog').classList.add('show');
        confirmCallback = callback;
    }
    function closeConfirm() { document.getElementById('confirmDialog').classList.remove('show'); confirmCallback = null; }
    document.getElementById('confirmOk').addEventListener('click', () => {
        if (confirmCallback) confirmCallback();
        closeConfirm();
    });

    // Generic AJAX action
    function adminAjax(url, successMsg, errorMsg, onSuccess) {
        fetch(url)
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    showToast('Thành công', successMsg, '✅');
                    if (onSuccess) onSuccess(data);
                } else {
                    showToast('Lỗi', data.msg || errorMsg, '❌');
                }
            })
            .catch(() => showToast('Lỗi kết nối', errorMsg, '❌'));
    }
</script>
</body>
</html>
