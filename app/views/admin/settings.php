<?php require_once '../app/views/admin/_layout_top.php'; ?>

    <div class="admin-topbar">
        <div>
            <h4>⚙️ Cài đặt hệ thống</h4>
            <small style="color:var(--admin-muted);">Cấu hình toàn bộ website</small>
        </div>
    </div>

    <div class="admin-content">
        <?php if(isset($_GET['success'])): ?>
        <div style="background:rgba(16,185,129,0.15); border:1px solid rgba(16,185,129,0.3); border-radius:12px; padding:12px 18px; margin-bottom:20px; color:#10b981; font-weight:600; font-size:14px;">
            ✅ Đã lưu cài đặt thành công!
        </div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
        <div style="background:rgba(244,63,94,0.15); border:1px solid rgba(244,63,94,0.3); border-radius:12px; padding:12px 18px; margin-bottom:20px; color:#f43f5e; font-weight:600; font-size:14px;">
            ❌ Bạn không có quyền thay đổi cài đặt.
        </div>
        <?php endif; ?>

        <?php if(($_SESSION['admin_role']??'') !== 'admin'): ?>
        <div style="background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); border-radius:12px; padding:12px 18px; margin-bottom:20px; color:#f59e0b; font-weight:600; font-size:14px;">
            ⚠️ Chỉ Admin mới có thể thay đổi cài đặt hệ thống. Moderator chỉ có quyền xem.
        </div>
        <?php endif; ?>

        <form action="index.php?url=admin/settings" method="POST">
        <div class="row g-4">

            <!-- ĐĂNG KÝ & NGƯỜI DÙNG -->
            <div class="col-lg-6">
                <div class="chart-card">
                    <h6>👥 ĐĂNG KÝ & NGƯỜI DÙNG</h6>
                    <div style="margin-bottom:20px;">
                        <label style="font-size:12px; font-weight:700; color:var(--admin-muted); display:block; margin-bottom:8px;">CHO PHÉP ĐĂNG KÝ TÀI KHOẢN MỚI</label>
                        <div style="display:flex; gap:10px;">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:14px;">
                                <input type="radio" name="allow_register" value="1" <?= ($settings['allow_register']??'1')==='1'?'checked':'' ?>> ✅ Bật
                            </label>
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:14px;">
                                <input type="radio" name="allow_register" value="0" <?= ($settings['allow_register']??'1')==='0'?'checked':'' ?>> 🔒 Tắt
                            </label>
                        </div>
                        <small style="color:var(--admin-muted); font-size:12px;">Khi tắt, trang đăng ký sẽ hiển thị thông báo đóng cửa.</small>
                    </div>
                    <div style="margin-bottom:20px;">
                        <label style="font-size:12px; font-weight:700; color:var(--admin-muted); display:block; margin-bottom:8px;">BẬT / TẮT CHIA SẺ HÀNH TRÌNH</label>
                        <div style="display:flex; gap:10px;">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:14px;">
                                <input type="radio" name="allow_sharing" value="1" <?= ($settings['allow_sharing']??'1')==='1'?'checked':'' ?>> ✅ Bật
                            </label>
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:14px;">
                                <input type="radio" name="allow_sharing" value="0" <?= ($settings['allow_sharing']??'1')==='0'?'checked':'' ?>> 🔒 Tắt
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- UPLOAD & ALBUM -->
            <div class="col-lg-6">
                <div class="chart-card">
                    <h6>🖼️ UPLOAD & ALBUM</h6>
                    <div style="margin-bottom:20px;">
                        <label style="font-size:12px; font-weight:700; color:var(--admin-muted); display:block; margin-bottom:8px;">GIỚI HẠN DUNG LƯỢNG ẢNH (MB)</label>
                        <input type="number" name="max_upload_size" class="admin-input" style="width:100%;"
                            value="<?= htmlspecialchars($settings['max_upload_size']??'10') ?>" min="1" max="100">
                        <small style="color:var(--admin-muted); font-size:12px;">Mỗi file ảnh tối đa bao nhiêu MB.</small>
                    </div>
                    <div>
                        <label style="font-size:12px; font-weight:700; color:var(--admin-muted); display:block; margin-bottom:8px;">SỐ ẢNH TỐI ĐA MỖI ALBUM</label>
                        <input type="number" name="max_images_per_album" class="admin-input" style="width:100%;"
                            value="<?= htmlspecialchars($settings['max_images_per_album']??'50') ?>" min="1" max="500">
                        <small style="color:var(--admin-muted); font-size:12px;">Giới hạn số ảnh trong mỗi album hành trình.</small>
                    </div>
                </div>
            </div>

            <!-- BẢN ĐỒ -->
            <div class="col-lg-6">
                <div class="chart-card">
                    <h6>🗺️ CẤU HÌNH BẢN ĐỒ</h6>
                    <div>
                        <label style="font-size:12px; font-weight:700; color:var(--admin-muted); display:block; margin-bottom:8px;">MỨC ZOOM MẶC ĐỊNH (1–20)</label>
                        <input type="range" name="map_default_zoom" min="1" max="20"
                            value="<?= htmlspecialchars($settings['map_default_zoom']??'13') ?>"
                            oninput="document.getElementById('zoomVal').textContent=this.value"
                            style="width:100%; accent-color:#6366f1;">
                        <div style="text-align:center; margin-top:8px; font-size:22px; font-weight:700; color:var(--admin-primary);">
                            <span id="zoomVal"><?= $settings['map_default_zoom']??'13' ?></span>
                        </div>
                        <small style="color:var(--admin-muted); font-size:12px;">13 = tầm nhìn thành phố. 16 = chi tiết đường phố.</small>
                    </div>
                </div>
            </div>

            <!-- REALTIME -->
            <div class="col-lg-6">
                <div class="chart-card">
                    <h6>⚡ CẤU HÌNH REALTIME</h6>
                    <div>
                        <label style="font-size:12px; font-weight:700; color:var(--admin-muted); display:block; margin-bottom:8px;">KHOẢNG CÁCH POLLING (giây)</label>
                        <input type="number" name="realtime_interval" class="admin-input" style="width:100%;"
                            value="<?= htmlspecialchars($settings['realtime_interval']??'10') ?>" min="5" max="300">
                        <small style="color:var(--admin-muted); font-size:12px;">Tần suất tự động kiểm tra bài mới từ bạn bè (5–300 giây).</small>
                    </div>
                </div>
            </div>

            <!-- THÔNG TIN HỆ THỐNG -->
            <div class="col-12">
                <div class="chart-card">
                    <h6>💻 THÔNG TIN HỆ THỐNG</h6>
                    <div class="row g-3">
                        <?php $sysInfo = [
                            ['label'=>'PHP Version','value'=>phpversion(),'icon'=>'🐘'],
                            ['label'=>'Server Software','value'=>$_SERVER['SERVER_SOFTWARE']??'N/A','icon'=>'🖥️'],
                            ['label'=>'Document Root','value'=>$_SERVER['DOCUMENT_ROOT']??'N/A','icon'=>'📁'],
                            ['label'=>'Max Upload Size','value'=>ini_get('upload_max_filesize'),'icon'=>'📤'],
                            ['label'=>'Post Max Size','value'=>ini_get('post_max_size'),'icon'=>'📬'],
                            ['label'=>'Memory Limit','value'=>ini_get('memory_limit'),'icon'=>'🧠'],
                        ]; ?>
                        <?php foreach($sysInfo as $info): ?>
                        <div class="col-md-4 col-6">
                            <div style="background:rgba(255,255,255,0.03); border:1px solid var(--admin-border); border-radius:12px; padding:14px;">
                                <div style="font-size:20px; margin-bottom:6px;"><?= $info['icon'] ?></div>
                                <div style="font-size:11px; color:var(--admin-muted); font-weight:700; margin-bottom:4px;"><?= $info['label'] ?></div>
                                <div style="font-size:13px; font-weight:600; word-break:break-all;"><?= htmlspecialchars($info['value']) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

        </div><!-- end row -->

        <!-- SAVE BUTTON -->
        <?php if(($_SESSION['admin_role']??'') === 'admin'): ?>
        <div style="margin-top:24px; display:flex; gap:12px;">
            <button type="submit" class="btn-admin btn-admin-primary" style="padding:14px 32px; font-size:15px;">
                <i class="bi bi-save-fill"></i> Lưu tất cả cài đặt
            </button>
            <a href="index.php?url=admin/settings" class="btn-admin" style="background:var(--admin-border); color:var(--admin-text); padding:14px 24px; font-size:15px;">
                Hủy bỏ
            </a>
        </div>
        <?php endif; ?>
        </form>
    </div>

<?php require_once '../app/views/admin/_layout_bottom.php'; ?>
