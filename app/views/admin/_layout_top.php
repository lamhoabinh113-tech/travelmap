<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – Travel Memory Map</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <style>
        :root {
            --admin-bg: #0f1117;
            --admin-sidebar: #16181f;
            --admin-card: #1e2029;
            --admin-border: #2d2f3a;
            --admin-primary: #6366f1;
            --admin-accent: #f43f5e;
            --admin-success: #10b981;
            --admin-warning: #f59e0b;
            --admin-text: #e2e8f0;
            --admin-muted: #64748b;
        }
        * { box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: var(--admin-bg); color: var(--admin-text); margin: 0; overflow-x: hidden; }

        /* SIDEBAR */
        .admin-sidebar {
            width: 260px; height: 100vh; position: fixed; top: 0; left: 0;
            background: var(--admin-sidebar); border-right: 1px solid var(--admin-border);
            display: flex; flex-direction: column; z-index: 1000; transition: transform 0.3s;
        }
        .sidebar-logo {
            padding: 24px 20px; border-bottom: 1px solid var(--admin-border);
            display: flex; align-items: center; gap: 12px;
        }
        .sidebar-logo .logo-icon {
            width: 40px; height: 40px; background: linear-gradient(135deg, var(--admin-primary), #818cf8);
            border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .sidebar-logo span { font-weight: 700; font-size: 16px; }
        .sidebar-logo small { color: var(--admin-accent); font-size: 11px; font-weight: 600; display: block; }
        .sidebar-nav { flex: 1; padding: 16px 12px; overflow-y: auto; }
        .nav-section-label {
            font-size: 10px; font-weight: 700; letter-spacing: 1.5px;
            color: var(--admin-muted); padding: 8px 12px; margin-top: 8px;
        }
        .sidebar-nav a {
            display: flex; align-items: center; gap: 12px; padding: 11px 14px;
            border-radius: 12px; color: var(--admin-muted); text-decoration: none;
            font-weight: 500; font-size: 14px; transition: all 0.2s; margin-bottom: 2px;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(99,102,241,0.15); color: var(--admin-primary);
        }
        .sidebar-nav a .badge-count {
            margin-left: auto; background: var(--admin-accent); color: white;
            font-size: 10px; padding: 2px 7px; border-radius: 20px; font-weight: 700;
        }
        .sidebar-footer {
            padding: 16px; border-top: 1px solid var(--admin-border);
        }
        .sidebar-user {
            display: flex; align-items: center; gap: 10px;
            background: rgba(255,255,255,0.04); padding: 10px 12px; border-radius: 12px;
        }
        .sidebar-user .avatar {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, var(--admin-primary), #818cf8);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 15px;
        }

        /* MAIN CONTENT */
        .admin-main { margin-left: 260px; min-height: 100vh; }
        .admin-topbar {
            background: var(--admin-sidebar); border-bottom: 1px solid var(--admin-border);
            padding: 16px 28px; display: flex; align-items: center;
            justify-content: space-between; position: sticky; top: 0; z-index: 900;
        }
        .admin-topbar h4 { margin: 0; font-weight: 700; font-size: 18px; }
        .topbar-actions { display: flex; gap: 10px; align-items: center; }
        .admin-content { padding: 28px; }

        /* STAT CARDS */
        .stat-card {
            background: var(--admin-card); border: 1px solid var(--admin-border);
            border-radius: 20px; padding: 22px; transition: all 0.3s;
        }
        .stat-card:hover { border-color: var(--admin-primary); transform: translateY(-3px); }
        .stat-icon {
            width: 48px; height: 48px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; margin-bottom: 16px;
        }
        .stat-value { font-size: 28px; font-weight: 700; margin-bottom: 4px; }
        .stat-label { color: var(--admin-muted); font-size: 13px; font-weight: 500; }
        .stat-change { font-size: 12px; margin-top: 6px; }

        /* CHART CARD */
        .chart-card {
            background: var(--admin-card); border: 1px solid var(--admin-border);
            border-radius: 20px; padding: 24px;
        }
        .chart-card h6 { font-weight: 700; margin-bottom: 20px; font-size: 14px; color: var(--admin-muted); letter-spacing: 0.5px; }

        /* DATA TABLE */
        .admin-table {
            background: var(--admin-card); border: 1px solid var(--admin-border); border-radius: 20px; overflow: hidden;
        }
        .admin-table .table-header {
            padding: 18px 24px; display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid var(--admin-border);
        }
        .admin-table .table-header h6 { margin: 0; font-weight: 700; font-size: 15px; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th {
            background: rgba(255,255,255,0.02); padding: 12px 20px;
            font-size: 11px; font-weight: 700; color: var(--admin-muted);
            letter-spacing: 1px; text-transform: uppercase; border-bottom: 1px solid var(--admin-border);
            text-align: left; white-space: nowrap;
        }
        .data-table td {
            padding: 14px 20px; border-bottom: 1px solid rgba(45,47,58,0.5);
            font-size: 14px; vertical-align: middle;
        }
        .data-table tr:last-child td { border-bottom: none; }
        .data-table tr:hover td { background: rgba(255,255,255,0.02); }

        /* BADGES & PILLS */
        .badge-role { padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 700; }
        .badge-admin { background: rgba(244,63,94,0.15); color: #f43f5e; }
        .badge-moderator { background: rgba(245,158,11,0.15); color: #f59e0b; }
        .badge-user { background: rgba(99,102,241,0.15); color: #6366f1; }
        .badge-locked { background: rgba(239,68,68,0.15); color: #ef4444; }
        .badge-active { background: rgba(16,185,129,0.15); color: #10b981; }
        .badge-hidden { background: rgba(100,116,139,0.15); color: #94a3b8; }

        /* STATUS DOT */
        .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
        .status-online { background: var(--admin-success); box-shadow: 0 0 6px var(--admin-success); }
        .status-offline { background: var(--admin-muted); }

        /* FORM CONTROLS */
        .admin-input {
            background: rgba(255,255,255,0.05); border: 1px solid var(--admin-border);
            border-radius: 12px; padding: 10px 16px; color: var(--admin-text);
            font-family: 'Outfit', sans-serif; font-size: 14px; outline: none;
            transition: border-color 0.2s;
        }
        .admin-input:focus { border-color: var(--admin-primary); background: rgba(99,102,241,0.05); }
        .admin-input::placeholder { color: var(--admin-muted); }
        .admin-select { background: rgba(255,255,255,0.05); border: 1px solid var(--admin-border); border-radius: 12px; padding: 10px 14px; color: var(--admin-text); font-family: 'Outfit',sans-serif; font-size:14px; cursor: pointer; }
        .admin-select option { background: var(--admin-card); }

        /* BUTTONS */
        .btn-admin { border-radius: 10px; font-weight: 600; font-size: 13px; padding: 8px 16px; border: none; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-admin-primary { background: var(--admin-primary); color: white; }
        .btn-admin-primary:hover { background: #4f46e5; }
        .btn-admin-danger { background: rgba(244,63,94,0.15); color: #f43f5e; }
        .btn-admin-danger:hover { background: var(--admin-accent); color: white; }
        .btn-admin-warning { background: rgba(245,158,11,0.15); color: #f59e0b; }
        .btn-admin-warning:hover { background: #f59e0b; color: #000; }
        .btn-admin-success { background: rgba(16,185,129,0.15); color: #10b981; }
        .btn-admin-success:hover { background: var(--admin-success); color: white; }
        .btn-admin-sm { padding: 5px 10px; font-size: 12px; border-radius: 8px; }

        /* PAGINATION */
        .admin-pagination { display: flex; gap: 6px; align-items: center; }
        .admin-pagination a, .admin-pagination span {
            padding: 7px 13px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 600;
            background: var(--admin-card); border: 1px solid var(--admin-border); color: var(--admin-text);
        }
        .admin-pagination a:hover { border-color: var(--admin-primary); color: var(--admin-primary); }
        .admin-pagination .active-page { background: var(--admin-primary); border-color: var(--admin-primary); color: white; }

        /* MODAL */
        .admin-modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.7); z-index: 2000; align-items: center; justify-content: center; }
        .admin-modal-backdrop.show { display: flex; }
        .admin-modal { background: var(--admin-card); border: 1px solid var(--admin-border); border-radius: 24px; padding: 32px; width: 100%; max-width: 500px; animation: modalIn 0.25s ease-out; }
        @keyframes modalIn { from { opacity:0; transform: scale(0.9); } to { opacity:1; transform: scale(1); } }
        .admin-modal h5 { font-weight: 700; margin-bottom: 20px; }
        .admin-modal label { font-size: 12px; font-weight: 700; color: var(--admin-muted); letter-spacing: 0.5px; display: block; margin-bottom: 6px; margin-top: 14px; }
        .admin-modal .admin-input, .admin-modal .admin-select { width: 100%; }

        /* CONFIRM DIALOG */
        .confirm-dialog { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.75); z-index: 3000; align-items: center; justify-content: center; }
        .confirm-dialog.show { display: flex; }
        .confirm-box { background: var(--admin-card); border: 1px solid var(--admin-border); border-radius: 20px; padding: 28px; width: 380px; text-align: center; }
        .confirm-icon { font-size: 48px; margin-bottom: 12px; }
        .confirm-box h6 { font-weight: 700; margin-bottom: 8px; }
        .confirm-box p { color: var(--admin-muted); font-size: 14px; margin-bottom: 20px; }

        /* BREADCRUMB */
        .admin-breadcrumb { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--admin-muted); margin-bottom: 20px; }
        .admin-breadcrumb a { color: var(--admin-primary); text-decoration: none; }

        /* SEARCH BAR */
        .search-filter-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px; }
        .search-filter-bar .admin-input { flex: 1; min-width: 200px; }

        /* LOG BADGE */
        .log-action { background: rgba(99,102,241,0.1); color: var(--admin-primary); padding: 3px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; }

        /* PROGRESS BAR */
        .admin-progress { height: 6px; background: var(--admin-border); border-radius: 3px; overflow: hidden; }
        .admin-progress-bar { height: 100%; border-radius: 3px; background: linear-gradient(90deg, var(--admin-primary), #818cf8); }

        /* AVATAR */
        .user-avatar-mini { width: 32px; height: 32px; border-radius: 10px; background: linear-gradient(135deg, #6366f1, #818cf8); display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; color: white; }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.open { transform: translateX(0); }
            .admin-main { margin-left: 0; }
        }

        /* SCROLLBAR */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--admin-bg); }
        ::-webkit-scrollbar-thumb { background: var(--admin-border); border-radius: 3px; }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<nav class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">🗺️</div>
        <div>
            <span>TravelMap</span>
            <small>ADMIN PANEL</small>
        </div>
    </div>
    <div class="sidebar-nav">
        <div class="nav-section-label">TỔNG QUAN</div>
        <a href="index.php?url=admin/dashboard" class="<?= (strpos($_SERVER['QUERY_STRING']??'', 'admin/dashboard') !== false || ($_GET['url']??'') === 'admin') ? 'active' : '' ?>">
            <i class="bi bi-grid-fill"></i> Dashboard
        </a>

        <div class="nav-section-label">QUẢN LÝ</div>
        <a href="index.php?url=admin/users" class="<?= strpos($_GET['url']??'','admin/users')!==false ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i> Tài khoản
        </a>
        <a href="index.php?url=admin/posts" class="<?= strpos($_GET['url']??'','admin/posts')!==false ? 'active' : '' ?>">
            <i class="bi bi-images"></i> Bài đăng & Ảnh
        </a>
        <a href="index.php?url=admin/interactions" class="<?= strpos($_GET['url']??'','admin/interactions')!==false ? 'active' : '' ?>">
            <i class="bi bi-heart-fill"></i> Tương tác
        </a>
        <a href="index.php?url=admin/loginLogs" class="<?= strpos($_GET['url']??'','admin/loginLogs')!==false ? 'active' : '' ?>">
            <i class="bi bi-shield-lock-fill"></i> Lịch sử đăng nhập
        </a>

        <div class="nav-section-label">HỆ THỐNG</div>
        <a href="index.php?url=admin/activityLog" class="<?= strpos($_GET['url']??'','admin/activityLog')!==false ? 'active' : '' ?>">
            <i class="bi bi-journal-text"></i> Nhật ký hoạt động
        </a>
        <a href="index.php?url=admin/settings" class="<?= strpos($_GET['url']??'','admin/settings')!==false ? 'active' : '' ?>">
            <i class="bi bi-gear-fill"></i> Cài đặt hệ thống
        </a>

        <div class="nav-section-label">ĐIỀU HƯỚNG</div>
        <a href="index.php?url=location/dashboard" target="_blank">
            <i class="bi bi-map"></i> Về bản đồ
        </a>
        <a href="index.php?url=auth/logout">
            <i class="bi bi-box-arrow-right"></i> Đăng xuất
        </a>
    </div>
    <div class="sidebar-footer">
        <div class="sidebar-user">
            <div class="avatar"><?= strtoupper(substr($_SESSION['username']??'A',0,1)) ?></div>
            <div>
                <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($_SESSION['full_name']??'Admin') ?></div>
                <div style="font-size:11px; color:var(--admin-accent); font-weight:700;"><?= strtoupper($_SESSION['admin_role']??'ADMIN') ?></div>
            </div>
        </div>
    </div>
</nav>

<!-- MOBILE TOGGLE -->
<button class="btn-admin btn-admin-primary" id="sidebarToggle" 
    style="display:none; position:fixed; bottom:20px; left:20px; z-index:1500; border-radius:50%; width:50px; height:50px; justify-content:center;">
    <i class="bi bi-list fs-5"></i>
</button>

<!-- MAIN CONTENT STARTS HERE -->
<main class="admin-main">
