<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hành Trình Của Bạn - Travel Memory Map</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet Control Geocoder -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    
    <script>
        if (location.protocol !== 'https:' && location.hostname !== 'localhost') {
            location.replace(`https:${location.href.substring(location.protocol.length)}`);
        }
    </script>
    
    <style>
        body { height: 100vh; overflow: hidden; background: #0b1220; }
        .dashboard-container { height: 100vh; display: flex; }
        
        .sidebar { 
            width: 430px; 
            height: 100vh; 
            overflow-y: auto; 
            background: rgba(248, 250, 252, 0.94); 
            backdrop-filter: blur(18px);
            z-index: 1000;
            box-shadow: 10px 0 30px rgba(0,0,0,0.05);
            padding: 25px;
            transition: all 0.3s ease;
        }

        .social-sidebar {
            width: 300px;
            background: rgba(248, 250, 252, 0.94);
            backdrop-filter: blur(18px);
            border-left: 1px solid rgba(148, 163, 184, 0.22);
            height: 100vh;
            overflow-y: auto;
            padding: 25px;
        }

        #map { flex: 1; height: 100vh; z-index: 1; }

        .user-profile {
            padding: 20px;
            background: linear-gradient(135deg, var(--primary), #818cf8);
            border-radius: 24px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
        }

        .journey-stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 24px;
        }

        .stat-card {
            padding: 14px;
            border-radius: 8px;
            background: rgba(255,255,255,0.74);
            border: 1px solid rgba(148,163,184,0.22);
            box-shadow: 0 12px 30px rgba(15,23,42,0.06);
        }

        .stat-card .stat-value {
            font-size: 1.28rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1;
        }

        .stat-card small {
            color: #64748b;
            font-weight: 600;
        }

        .profile-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 12px;
        }

        .profile-badges span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,0.18);
            font-size: 11px;
            font-weight: 700;
        }

        .avatar-placeholder {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            color: white;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .avatar-placeholder:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 22px rgba(15, 23, 42, 0.1);
        }
        .avatar-placeholder img.avatar-logo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .memory-list {
            position: relative;
        }

        .journey-timeline {
            position: relative;
            padding-left: 22px;
        }

        .journey-timeline::before {
            content: "";
            position: absolute;
            left: 7px;
            top: 8px;
            bottom: 18px;
            width: 2px;
            background: linear-gradient(180deg, #22d3ee, #6366f1, #f43f5e);
            opacity: 0.55;
        }

        .memory-item {
            position: relative;
            border-radius: 8px;
            border: 1px solid rgba(226, 232, 240, 0.9);
            padding: 14px;
            margin-bottom: 16px;
            transition: all 0.3s ease;
            cursor: pointer;
            background: white;
            animation: timelineRise .55s ease both;
        }

        .memory-item::before {
            content: "";
            position: absolute;
            left: -21px;
            top: 26px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #22d3ee;
            border: 3px solid #f8fafc;
            box-shadow: 0 0 0 4px rgba(34,211,238,0.12);
        }

        .memory-item:hover {
            border-color: var(--primary);
            box-shadow: 0 10px 20px rgba(99, 102, 241, 0.05);
            transform: translateY(-3px);
        }

        .memory-img-wrapper {
            position: relative;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .memory-img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .memory-item:hover .memory-img {
            transform: scale(1.05);
        }

        .album-badge {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(5px);
            color: white;
            padding: 4px 10px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
        }

        .memory-meta-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
            margin: 12px 0;
        }

        .memory-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            min-width: 0;
            padding: 7px 9px;
            border-radius: 999px;
            background: #f8fafc;
            color: #334155;
            font-size: 11px;
            font-weight: 700;
        }

        @keyframes timelineRise {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .map-mode-pill {
            position: fixed;
            left: 455px;
            top: 24px;
            z-index: 900;
            display: flex;
            gap: 8px;
            padding: 8px;
            border-radius: 999px;
            background: rgba(15, 23, 42, 0.72);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.12);
            color: white;
        }

        .map-mode-pill button {
            border: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            color: white;
            background: rgba(255,255,255,0.12);
        }

        .map-mode-pill button.active {
            background: linear-gradient(135deg, #22d3ee, #6366f1);
        }

        .live-location-marker { background: none; border: none; }
        .live-location-dot {
            width: 18px;
            height: 18px;
            background: #ef4444;
            border: 3px solid #fff;
            border-radius: 50%;
            box-shadow: 0 4px 14px rgba(239, 68, 68, 0.55);
            position: relative;
        }
        .live-location-pulse {
            position: absolute;
            inset: -10px;
            border-radius: 50%;
            border: 2px solid rgba(34, 211, 238, 0.75);
            animation: liveLocationPulse 2s ease-out infinite;
        }
        @keyframes liveLocationPulse {
            0% { transform: scale(0.6); opacity: 1; }
            100% { transform: scale(2.2); opacity: 0; }
        }

        .live-location-hud {
            position: fixed;
            left: 455px;
            bottom: 28px;
            z-index: 900;
            max-width: min(340px, calc(100vw - 480px));
            padding: 10px 14px;
            border-radius: 14px;
            background: rgba(15, 23, 42, 0.88);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255,255,255,0.14);
            color: #e2e8f0;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 12px 36px rgba(0,0,0,0.28);
            line-height: 1.4;
        }
        .live-location-hud .accuracy-good { color: #4ade80; }
        .live-location-hud .accuracy-mid { color: #fbbf24; }
        .live-location-hud .accuracy-low { color: #f87171; }

        @media (max-width: 768px) {
            .live-location-hud { left: 16px; bottom: 100px; max-width: calc(100vw - 32px); }
        }

        .heat-dot {
            border-radius: 50%;
            background: radial-gradient(circle, rgba(244,63,94,.46) 0%, rgba(244,63,94,.18) 48%, rgba(244,63,94,0) 70%);
            pointer-events: none;
        }

        .friend-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            border-radius: 16px;
            background: #f8fafc;
            margin-bottom: 10px;
        }

        /* Photo Marker Styles */
        .photo-marker {
            background: none;
            border: none;
        }

        .marker-container {
            width: 50px;
            height: 50px;
            position: relative;
            background: white;
            border: 3px solid white;
            border-radius: 50%;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
            z-index: 500;
        }

        .marker-container:hover {
            transform: scale(1.2);
            z-index: 1000;
            border-color: var(--primary);
        }

        .marker-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .marker-image-empty {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            font-size: 20px;
        }

        .marker-arrow {
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-top: 8px solid white;
        }

        .marker-container:hover .marker-arrow {
            border-top-color: var(--primary);
        }

        /* Locket Camera UI Styles */
        .camera-fab {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 65px;
            height: 65px;
            background: linear-gradient(135deg, #f43f5e, #fb923c);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            box-shadow: 0 10px 25px rgba(244, 63, 94, 0.4);
            cursor: pointer;
            z-index: 2000;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 4px solid white;
        }

        .camera-fab:hover {
            transform: scale(1.1) rotate(15deg);
        }

        .locket-camera-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: #000;
            z-index: 3000;
            display: none;
            flex-direction: column;
            animation: slideUp 0.3s ease-out;
            overflow-y: auto;
        }

        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }

        .camera-header {
            position: absolute;
            top: 20px;
            left: 0;
            width: 100%;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 3010;
        }

        .camera-close {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            cursor: pointer;
            border: none;
        }

        /* Viewfinder: explicitly calc height = 100vh - controls height */
        .camera-viewfinder {
            position: relative;
            width: 100%;
            flex: 1 1 auto;
            min-height: 240px;
            overflow: hidden;
            background: #111;
        }

        #cameraVideo {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            background: #000;
        }

        #photoPreviewCanvas {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 3005;
        }

        #cameraError {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: white;
            z-index: 3006;
            padding: 20px;
        }

        .camera-controls {
            height: 160px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #000;
            position: relative;
            padding: 0 20px;
        }

        .capture-btn-container {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .capture-btn-outer {
            width: 85px;
            height: 85px;
            border-radius: 50%;
            border: 4px solid white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }

        .capture-btn-inner {
            width: 70px;
            height: 70px;
            background: white;
            border-radius: 50%;
            transition: all 0.2s;
        }

        .capture-btn-outer:active .capture-btn-inner {
            transform: scale(0.9);
            background: #ccc;
        }

        .post-controls {
            display: none;
            width: 100%;
            flex-direction: column;
            gap: 15px;
            padding: 20px;
            background: #000;
        }

        .caption-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 20px;
            padding: 15px 20px;
            color: white;
            font-size: 16px;
            backdrop-filter: blur(10px);
        }

        /* AI Chat Widget */
        .chat-toggle-btn {
            position: fixed;
            right: 24px;
            bottom: 110px;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            border: none;
            background: linear-gradient(135deg, #22d3ee, #7c3aed);
            color: white;
            box-shadow: 0 18px 44px rgba(124, 58, 237, 0.34);
            z-index: 2300;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .chat-toggle-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 24px 48px rgba(34, 211, 238, 0.35);
        }

        .chat-widget {
            position: fixed;
            right: 24px;
            bottom: 18px;
            width: 360px;
            max-width: calc(100vw - 32px);
            height: min(720px, calc(100vh - 36px));
            max-height: calc(100vh - 36px);
            z-index: 2200;
            transform: translateX(120%);
            transition: transform 0.35s ease;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 28px 90px rgba(15, 23, 42, 0.38);
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(255,255,255,0.5);
            display: flex;
            flex-direction: column;
            backdrop-filter: blur(24px);
        }
        .chat-widget.open {
            transform: translateX(0);
        }
        .chat-widget-header {
            padding: 18px 18px 14px;
            background:
                linear-gradient(135deg, rgba(79,70,229,0.96), rgba(14,165,233,0.92)),
                url('https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=900&q=70');
            background-size: cover;
            background-position: center;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .chat-widget-header h5 {
            margin: 0;
            font-size: 1rem;
            line-height: 1.2;
            font-weight: 700;
        }
        .chat-widget-header .chat-close {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: rgba(255,255,255,0.18);
            border: none;
            color: white;
            display: grid;
            place-items: center;
            cursor: pointer;
        }
        .chat-widget-body {
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            flex-direction: column;
            padding: 14px 14px 0;
            background: #f8fbff;
            overflow: hidden;
        }
        .chat-history {
            flex: 1 1 auto;
            min-height: 160px;
            overflow-y: auto;
            overscroll-behavior: contain;
            padding-right: 6px;
            scroll-behavior: smooth;
        }
        .chat-message {
            display: flex;
            gap: 12px;
            margin-bottom: 14px;
        }
        .chat-message.assistant {
            justify-content: flex-start;
        }
        .chat-message.user {
            justify-content: flex-end;
        }
        .chat-message .avatar {
            width: 38px;
            min-width: 38px;
            height: 38px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-weight: 700;
            color: white;
            background: linear-gradient(135deg, #22d3ee, #7c3aed);
        }
        .chat-message.user .avatar {
            background: #4f46e5;
        }
        .chat-message .bubble {
            max-width: 75%;
            padding: 12px 14px;
            border-radius: 18px;
            line-height: 1.5;
            white-space: pre-wrap;
            font-size: 0.92rem;
        }
        .chat-message.assistant .bubble {
            background: #ffffff;
            color: #1f2937;
            box-shadow: 0 10px 26px rgba(15,23,42,0.08);
        }
        .chat-message.user .bubble {
            background: linear-gradient(135deg, #4f46e5, #0ea5e9);
            color: white;
        }
        .chat-quick-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            padding: 14px 0 0;
            flex: 0 0 auto;
        }
        .chat-quick-actions button {
            border: none;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 0.82rem;
            font-weight: 700;
            color: #1f2937;
            background: #edf2ff;
            transition: transform 0.2s ease, background 0.2s ease;
            cursor: pointer;
        }
        .chat-quick-actions button:hover {
            transform: translateY(-1px);
            background: #dbeafe;
        }
        .chat-input-area {
            flex: 0 0 auto;
            padding: 14px;
            border-top: 1px solid rgba(15,23,42,0.08);
            background: #ffffff;
        }
        .chat-input-area textarea {
            width: 100%;
            min-height: 58px;
            max-height: 112px;
            border-radius: 18px;
            border: 1px solid #dbeafe;
            padding: 12px 14px;
            resize: vertical;
            font-family: 'Outfit', sans-serif;
            font-size: 0.95rem;
            color: #0f172a;
            background: #f8fbff;
        }
        .chat-input-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 12px;
        }
        .chat-input-actions .text-muted {
            font-size: 0.8rem;
            color: #64748b;
        }
        .chat-input-actions .btn-send {
            border: none;
            border-radius: 14px;
            padding: 10px 16px;
            background: linear-gradient(135deg, #4338ca, #0ea5e9);
            color: white;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .chat-input-actions .btn-send:hover {
            transform: translateY(-1px);
        }
        .chat-widget-footer {
            padding: 0 14px 14px;
        }
        .chat-widget-footer small {
            color: #64748b;
            display: block;
        }

        /* Mobile Navigation & Layout */
        .bottom-nav {
            display: none;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 65px;
            background: rgba(248, 250, 252, 0.94);
            backdrop-filter: blur(18px);
            border-top: 1px solid rgba(148, 163, 184, 0.22);
            z-index: 1050;
            justify-content: space-around;
            align-items: center;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.05);
        }

        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #64748b;
            text-decoration: none;
            font-size: 11px;
            font-weight: 600;
            width: 60px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .bottom-nav-item i {
            font-size: 22px;
            margin-bottom: 2px;
        }

        .bottom-nav-item.active {
            color: #4f46e5;
        }

        .bottom-nav-item.center-cam {
            position: relative;
            top: -15px;
            width: 55px;
            height: 55px;
            background: linear-gradient(135deg, #f43f5e, #fb923c);
            border-radius: 50%;
            color: white;
            box-shadow: 0 10px 20px rgba(244, 63, 94, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bottom-nav-item.center-cam i {
            margin: 0;
            font-size: 24px;
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.4);
            backdrop-filter: blur(4px);
            z-index: 990;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        @media(max-width: 768px) {
            /* Dashboard container fills full screen on mobile */
            .dashboard-container {
                position: relative;
                width: 100vw;
                height: 100vh;
                overflow: hidden;
            }

            /* Map takes full screen minus bottom nav */
            #map {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100vw !important;
                height: calc(100vh - 65px) !important;
                z-index: 1 !important;
            }

            /* Sidebars slide in as drawers */
            .sidebar {
                position: fixed !important;
                left: 0;
                top: 0;
                width: 88% !important;
                max-width: 400px;
                height: 100vh !important;
                overflow-y: auto;
                transform: translateX(-100%);
                z-index: 1001;
                padding-bottom: 80px;
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 6px 0 30px rgba(0,0,0,0.2);
            }

            .social-sidebar {
                position: fixed !important;
                right: 0;
                top: 0;
                width: 88% !important;
                max-width: 400px;
                height: 100vh !important;
                overflow-y: auto;
                transform: translateX(100%);
                z-index: 1001;
                padding-bottom: 80px;
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: -6px 0 30px rgba(0,0,0,0.2);
            }

            .sidebar.mobile-open {
                transform: translateX(0) !important;
            }

            .social-sidebar.mobile-open {
                transform: translateX(0) !important;
            }

            /* Show bottom nav on mobile */
            .bottom-nav {
                display: flex;
                z-index: 1002;
            }

            /* Adjust Leaflet zoom controls above bottom nav */
            .leaflet-bottom.leaflet-right {
                bottom: 75px;
            }

            /* Hide desktop camera FAB on mobile (use bottom nav instead) */
            .camera-fab {
                display: none !important;
            }

            /* Map mode pill position on mobile */
            .map-mode-pill {
                left: 16px;
                top: 16px;
            }

            /* Live location HUD on mobile */
            .live-location-hud {
                left: 16px;
                bottom: 80px;
                max-width: calc(100vw - 32px);
            }

            /* AI Chat Widget for mobile */
            .chat-toggle-btn {
                right: 18px;
                bottom: 80px;
                z-index: 1003;
            }

            .chat-widget {
                width: calc(100vw - 16px);
                right: 8px;
                bottom: 75px;
                height: min(75vh, calc(100vh - 90px));
                max-height: calc(100vh - 90px);
                border-radius: 20px;
                z-index: 1003;
            }

            .chat-history { min-height: 120px; }
            .chat-quick-actions {
                grid-template-columns: repeat(2, 1fr);
                overflow-x: visible;
                padding-bottom: 4px;
            }
            .chat-input-actions small { display: none; }
        }

        .album-lightbox .modal-dialog {
            max-width: min(1180px, calc(100vw - 24px));
        }

        .album-lightbox .modal-content {
            min-height: min(820px, calc(100vh - 24px));
            background: rgba(7, 17, 31, 0.96) !important;
            border: 1px solid rgba(255,255,255,0.12) !important;
            box-shadow: 0 30px 100px rgba(0,0,0,0.45);
        }

        .album-stage {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 260px;
            gap: 18px;
            align-items: start;
        }

        .album-thumbs {
            max-height: 70vh;
            overflow-y: auto;
            columns: 2 110px;
            column-gap: 10px;
        }

        .album-thumb {
            width: 100%;
            break-inside: avoid;
            margin-bottom: 10px;
            border: 0;
            padding: 0;
            background: transparent;
            border-radius: 8px;
            overflow: hidden;
            opacity: .74;
            cursor: pointer;
        }

        .album-thumb.active,
        .album-thumb:hover {
            opacity: 1;
            outline: 2px solid #22d3ee;
        }

        .album-thumb img,
        .album-thumb video {
            width: 100%;
            height: auto;
            display: block;
        }

        .album-toolbar {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .album-toolbar button {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: 0;
            background: rgba(255,255,255,0.14);
            color: white;
        }

        @media (max-width: 992px) {
            .album-stage { grid-template-columns: 1fr; }
            .album-thumbs { max-height: 180px; columns: 4 92px; }
        }

        .caption-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .post-actions {
            display: flex;
            gap: 10px;
        }

        .btn-locket-post {
            flex: 1;
            background: linear-gradient(135deg, #f43f5e, #fb923c);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-locket-cancel {
            width: 60px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
    </style>
</head>
<body>
<?php
    $journey_count = count($locations ?? []);
    $photo_count = 0;
    $place_names = [];
    $top_place = 'Chưa có dữ liệu';
    $total_distance = 0;
    $previous_point = null;
    $mood_counts = [];

    foreach (($locations ?? []) as $journey) {
        if (!empty($journey['image'])) {
            $photo_count++;
        }

        $place = trim($journey['place_name'] ?? '');
        if ($place !== '') {
            $place_names[] = $place;
        }

        $mood = trim($journey['feeling'] ?? 'Khac');
        $mood_counts[$mood] = ($mood_counts[$mood] ?? 0) + 1;

        if ($previous_point && isset($journey['latitude'], $journey['longitude'])) {
            $earth_radius = 6371;
            $lat1 = deg2rad((float)$previous_point['latitude']);
            $lon1 = deg2rad((float)$previous_point['longitude']);
            $lat2 = deg2rad((float)$journey['latitude']);
            $lon2 = deg2rad((float)$journey['longitude']);
            $dlat = $lat2 - $lat1;
            $dlon = $lon2 - $lon1;
            $a = sin($dlat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dlon / 2) ** 2;
            $total_distance += 2 * $earth_radius * asin(min(1, sqrt($a)));
        }
        $previous_point = $journey;
    }

    if (!empty($place_names)) {
        $place_frequency = array_count_values($place_names);
        arsort($place_frequency);
        $top_place = array_key_first($place_frequency);
    }

    arsort($mood_counts);
    $dominant_mood = array_key_first($mood_counts) ?: 'Dang cho ky uc moi';
    $explorer_level = max(1, min(9, (int)ceil($journey_count / 5)));
?>

<style>
        :root {
            --primary: #1a73e8;
            --bg-color: #f3f4f6;
            --surface: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
            --safe-area-bottom: 20px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Inter', 'Outfit', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            background-color: #e2e8f0;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            overflow-x: hidden;
            overflow-y: auto;
        }

        /* App Container - Responsive full width */
        .app-container {
            width: 100%;
            background-color: var(--surface);
            min-height: 100vh;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        /* --- 1. Map Area --- */
        .map-section {
            position: relative;
            height: 45vh;
            min-height: 300px;
            width: 100%;
        }

        #map {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        /* Profile Badge */
        .profile-badge {
            position: absolute;
            top: 20px;
            left: 20px;
            background: rgba(37, 99, 235, 0.9);
            backdrop-filter: blur(8px);
            padding: 6px 16px 6px 6px;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
            cursor: pointer;
        }
        .profile-badge img {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }
        .profile-info {
            display: flex;
            flex-direction: column;
        }
        .profile-name { font-weight: 700; font-size: 14px; line-height: 1.2; }
        .profile-level { font-size: 11px; opacity: 0.9; }

        /* Map UI Elements */
        .chat-map-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 42px;
            height: 42px;
            background: white;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-size: 20px;
            color: var(--text-main);
            z-index: 1000;
            cursor: pointer;
        }

        .map-mode-pill {
            position: absolute;
            right: 20px;
            top: 70px;
            z-index: 900;
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding: 8px;
            border-radius: 20px;
            background: rgba(15, 23, 42, 0.72);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.12);
        }
        .map-mode-pill button {
            border: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            color: white;
            background: rgba(255,255,255,0.12);
        }
        .map-mode-pill button.active {
            background: linear-gradient(135deg, #22d3ee, #6366f1);
        }

        .live-location-hud {
            position: absolute;
            left: 20px;
            bottom: 30px;
            z-index: 900;
            padding: 8px 16px;
            border-radius: 16px;
            background: rgba(15, 23, 42, 0.92);
            color: #e2e8f0;
            font-size: 13px;
            font-weight: 600;
        }

        /* --- 2. Main Content Area --- */
        .main-content {
            flex: 1;
            background: white;
            border-radius: 24px 24px 0 0;
            margin-top: -24px;
            position: relative;
            z-index: 10;
            padding: 0 20px 100px 20px; 
            box-shadow: 0 -10px 20px rgba(0,0,0,0.05);
        }

        /* Tabs */
        .tabs {
            display: flex;
            justify-content: space-between;
            padding: 20px 10px;
            border-bottom: 1px solid var(--border-color);
        }
        .tab-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            color: var(--text-muted);
            font-size: 13px;
            font-weight: 500;
            position: relative;
            cursor: pointer;
            flex: 1;
        }
        .tab-item.active { color: var(--primary); }
        .tab-item i { font-size: 20px; }
        .tab-item.active::after {
            content: '';
            position: absolute;
            bottom: -21px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px 3px 0 0;
        }

        .tab-content-section { display: none; margin-top: 20px; }
        .tab-content-section.active { display: block; animation: fadeIn 0.3s ease; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        /* Old Timeline Styles (Adapted) */
        .journey-timeline { position: relative; padding-left: 20px; margin-top: 20px;}
        .journey-timeline::before {
            content: ''; position: absolute; left: 5px; top: 0; bottom: 0; width: 2px;
            background: linear-gradient(180deg, #22d3ee, #6366f1, #f43f5e); opacity: 0.5;
        }
        .memory-item {
            position: relative; border-radius: 16px; border: 1px solid var(--border-color);
            padding: 16px; margin-bottom: 20px; background: white; cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        }
        .memory-item::before {
            content: ''; position: absolute; left: -21px; top: 20px; width: 12px; height: 12px;
            border-radius: 50%; background: #22d3ee; border: 3px solid white; box-shadow: 0 0 0 2px rgba(34,211,238,0.2);
        }
        .memory-img-wrapper { border-radius: 12px; overflow: hidden; margin-bottom: 12px; position: relative; }
        .memory-img { width: 100%; height: 180px; object-fit: cover; }
        
        .memory-meta-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 8px; margin: 12px 0; }
        .memory-chip { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px; background: #f8fafc; font-size: 11px; font-weight: 600; }

        /* AI Section Small */
        .ai-section {
            background: #f8fafc; border-radius: 16px; margin-bottom: 24px; overflow: hidden; border: 1px solid var(--border-color);
        }
        .ai-header { background: var(--primary); color: white; padding: 12px 16px; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 8px; }
        .ai-body { padding: 16px; }
        .user-chat { background: white; border: 1px solid var(--border-color); padding: 10px 16px; border-radius: 20px; font-size: 13px; display: flex; align-items: center; gap: 10px; margin-bottom: 10px;}
        .ai-avatar { width: 28px; height: 28px; background: #3b82f6; color: white; border-radius: 50%; display: flex; justify-content: center; align-items: center; font-weight: bold; font-size: 12px; }
        .ai-response { background: white; border: 1px solid var(--border-color); padding: 16px; border-radius: 16px; font-size: 13px; line-height: 1.5; }
        .ai-btn { background: var(--primary); color: white; border: none; padding: 8px 20px; border-radius: 20px; font-weight: 600; font-size: 13px; margin-top: 10px; cursor: pointer; }

        /* Friends Section */
        .friend-card { display: flex; align-items: center; gap: 12px; padding: 12px; border-radius: 16px; background: #f8fafc; margin-bottom: 10px; border: 1px solid var(--border-color);}

        /* --- 3. Bottom Navigation --- */
        .bottom-nav {
            position: fixed; bottom: 0; left: 0; width: 100%;
            background: white; border-top: 1px solid var(--border-color);
            display: flex; justify-content: space-around; align-items: center;
            padding: 12px 20px calc(12px + var(--safe-area-bottom)); z-index: 2000;
            box-shadow: 0 -4px 20px rgba(0,0,0,0.05);
        }
        .nav-item {
            display: flex; flex-direction: column; align-items: center; gap: 4px;
            color: var(--text-muted); font-size: 11px; font-weight: 500; text-decoration: none; cursor: pointer;
        }
        .nav-item.active { color: var(--text-main); }
        .nav-item i { font-size: 22px; }
        
        .nav-item-camera {
            width: 56px; height: 56px; background: #1e293b; border-radius: 50%;
            display: flex; justify-content: center; align-items: center; color: white;
            font-size: 24px; transform: translateY(-15px); box-shadow: 0 8px 16px rgba(30, 41, 59, 0.3);
            cursor: pointer;
        }

        /* Marker Styles - preserve old logic */
        .photo-marker { background: none; border: none; }
        .marker-container { width: 50px; height: 50px; position: relative; background: white; border: 3px solid white; border-radius: 50%; box-shadow: 0 5px 15px rgba(0,0,0,0.2); transition: all 0.3s ease; z-index: 500; }
        .marker-container:hover { transform: scale(1.2); z-index: 1000; border-color: var(--primary); }
        .marker-image { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .marker-arrow { position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); border-left: 8px solid transparent; border-right: 8px solid transparent; border-top: 8px solid white; }
        .marker-container:hover .marker-arrow { border-top-color: var(--primary); }
        .heat-dot { border-radius: 50%; background: radial-gradient(circle, rgba(244,63,94,.46) 0%, rgba(244,63,94,.18) 48%, rgba(244,63,94,0) 70%); pointer-events: none; }
        
        .avatar-placeholder { display: flex; align-items: center; justify-content: center; background: #ddd; color: #555; }
        .album-badge { position: absolute; bottom: 10px; right: 10px; background: rgba(0,0,0,0.6); color: white; padding: 4px 10px; border-radius: 10px; font-size: 11px; font-weight: 600; backdrop-filter: blur(5px);}
        
        /* Modals and Overlays (Keep existing logic, hide on default if needed) */
        .locket-camera-overlay { position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: #000; z-index: 3000; display: none; flex-direction: column; overflow-y: auto; }
        .camera-header { position: absolute; top: 16px; left: 0; width: 100%; padding: 0 20px; display: flex; justify-content: space-between; align-items: center; z-index: 3010; }
        .camera-close { width: 44px; height: 44px; background: rgba(255,255,255,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; cursor: pointer; border: 1px solid rgba(255,255,255,0.2); }
        .camera-viewfinder { position: relative; width: 100%; flex: 1 1 auto; min-height: 240px; overflow: hidden; background: #111; }
        #cameraVideo, #photoPreviewCanvas { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }
        .camera-controls { height: 180px; display: flex; align-items: center; justify-content: center; background: #000; position: relative; padding: 0 20px; }
        .capture-btn-outer { width: 96px; height: 96px; border-radius: 50%; border: 5px solid rgba(255,255,255,0.9); display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 8px 32px rgba(0,0,0,0.3); }
        .capture-btn-inner { width: 80px; height: 80px; background: white; border-radius: 50%; }
        .post-controls { display: none; width: 100%; flex-direction: column; gap: 12px; padding: 20px; background: #000; position: absolute; bottom: 0; left: 0; z-index: 3020; }
        .caption-input { width: 100%; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2); border-radius: 20px; padding: 15px 20px; color: white; font-size: 16px; }
    </style>
</head>
<body>

<div class="app-container">
    
    <!-- 1. Map Section -->
    <div class="map-section">
        <div id="map"></div>
        
        <!-- Profile Badge -->
        <div class="profile-badge" onclick="openAvatarUploader()">
            <div class="avatar-placeholder" style="width: 36px; height: 36px; border-radius: 50%; overflow: hidden; border: 2px solid white; display: flex; align-items: center; justify-content: center; background: #ddd; color: #555;">
                <?php if (!empty($_SESSION['avatar'])): ?>
                    <img src="<?php echo htmlspecialchars($_SESSION['avatar']); ?>" alt="Avatar" style="width:100%; height:100%; object-fit: cover;">
                <?php else: ?>
                    <img src="https://cdn-icons-png.flaticon.com/512/4140/4140044.png" alt="AI icon" style="width:100%; height:100%; object-fit: cover;">
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <span class="profile-name"><?php echo $_SESSION['full_name']; ?></span>
                <span class="profile-level">Explorer Lv.<?php echo $explorer_level; ?></span>
            </div>
        </div>

        <form id="avatarUploadForm" action="index.php?url=location/uploadAvatar" method="POST" enctype="multipart/form-data" style="display:none;">
            <input type="file" id="avatarInput" name="avatar_file" accept="image/*" onchange="submitAvatarForm()">
        </form>

        <!-- Chat Button (Floating over map) -->
        <div class="chat-map-btn" onclick="toggleChatWidget()">
            <i class="bi bi-chat-text"></i>
        </div>

        <div class="map-mode-pill">
            <button type="button" class="active" id="darkMapBtn" onclick="setMapTheme('dark')" title="Bản đồ tối"><i class="bi bi-moon-stars-fill"></i></button>
            <button type="button" id="lightMapBtn" onclick="setMapTheme('light')" title="Bản đồ sáng"><i class="bi bi-brightness-high-fill"></i></button>
            <button type="button" class="active" id="followLocationBtn" onclick="toggleFollowLocation()" title="Theo dõi vị trí thực tế"><i class="bi bi-crosshair"></i></button>
            <button type="button" onclick="refreshMyLocation()" title="Định vị lại (GPS chính xác)"><i class="bi bi-arrow-clockwise"></i></button>
        </div>

        <div id="liveLocationHud" class="live-location-hud" style="display:none;">
            <i class="bi bi-geo-alt-fill text-danger"></i>
            <span id="liveLocationHudText">Đang định vị...</span>
        </div>
    </div>

    <!-- 2. Main Content -->
    <div class="main-content">
        <!-- Tabs -->
        <div class="tabs">
            <div class="tab-item active" onclick="switchTab('timeline')">
                <i class="bi bi-signpost-2" style="color: #3b82f6;"></i>
                Lịch trình
            </div>
            <div class="tab-item" onclick="switchTab('friends')">
                <i class="bi bi-people-fill" style="color: #8b5cf6;"></i>
                Bạn Bè
            </div>
            <div class="tab-item" onclick="switchTab('album')">
                <i class="bi bi-journal-album" style="color: #f59e0b;"></i>
                Album
            </div>
        </div>

        <!-- TAB 1: Timeline -->
        <div id="tab-timeline" class="tab-content-section active">
            <!-- AI Inline Chat Box -->
            <div class="ai-section">
                <div class="ai-header">
                    <i class="bi bi-robot"></i> Travel Memory AI
                </div>
                <div class="ai-body">
                    <div class="user-chat">
                        <div class="ai-avatar">Ai</div>
                        <span>Bạn muốn AI giúp gì cho chuyến đi hôm nay?</span>
                    </div>
                    <div class="ai-response">
                        Hãy chat ở cửa sổ nổi phía dưới để được gợi ý địa điểm, phân tích ảnh hoặc tạo nhật ký tự động nhé!
                        <button class="ai-btn" onclick="toggleChatWidget()">Mở hộp thoại AI</button>
                    </div>
                </div>
            </div>

            <!-- New from Friends -->
            <?php if(!isset($is_friend_view) && !empty($friend_locations)): ?>
                <div class="mb-4">
                    <h6 class="small fw-bold text-muted mb-3 d-flex align-items-center">
                        <i class="bi bi-people-fill me-2 text-primary"></i> MỚI TỪ BẠN BÈ
                    </h6>
                    <?php 
                    $top_friends = array_slice($friend_locations, 0, 3);
                    foreach($top_friends as $floc): 
                    ?>
                        <div class="memory-item" style="background: #f0f7ff;" onclick="focusMemory(<?php echo (int)$floc['id']; ?>)">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="avatar-placeholder" style="width: 25px; height: 25px; font-size: 10px; border-radius: 8px;">
                                    <?php echo strtoupper(substr($floc['username'], 0, 1)); ?>
                                </div>
                                <span class="small fw-bold text-primary"><?php echo $floc['full_name']; ?></span>
                                <small class="text-muted ms-auto" style="font-size: 10px;"><?php echo date('d/m/Y', strtotime($floc['created_at'])); ?></small>
                            </div>
                            <div class="memory-img-wrapper" style="height: 100px;">
                                <?php if($floc['image']): 
                                    $fext = strtolower(pathinfo($floc['image'], PATHINFO_EXTENSION));
                                    if (in_array($fext, ['mp4', 'webm', 'ogg', 'mov'])):
                                ?>
                                    <div class="d-flex align-items-center justify-content-center bg-dark w-100 h-100">
                                        <i class="bi bi-play-circle text-white fs-2 position-absolute" style="z-index: 2;"></i>
                                        <video class="memory-img" style="height: 100px; opacity: 0.5;">
                                            <source src="../uploads/<?php echo $floc['image']; ?>" type="video/mp4">
                                        </video>
                                    </div>
                                <?php else: ?>
                                    <img src="../uploads/<?php echo $floc['image']; ?>" class="memory-img" style="height: 100px;">
                                <?php endif; ?>
                                <?php else: ?>
                                    <div class="memory-img d-flex align-items-center justify-content-center bg-white" style="height: 100px;">
                                        <i class="bi bi-geo-alt text-muted fs-4"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h6 class="fw-bold mb-1 small"><?php echo $floc['place_name']; ?></h6>
                            <p class="small text-muted mb-0 text-truncate" style="font-size: 11px;"><?php echo $floc['description']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="small fw-bold text-muted mb-0"><i class="bi bi-signpost-split-fill me-2 text-primary"></i> TIMELINE</h6>
                <button class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addMemoryModal">
                    <i class="bi bi-plus-lg"></i> Thêm mới
                </button>
            </div>

            <div class="journey-timeline">
                <?php if(empty($locations)): ?>
                    <div class="text-center py-5 opacity-50">
                        <i class="bi bi-geo-alt display-4"></i>
                        <p class="mt-2">Chưa có kỷ niệm nào.</p>
                    </div>
                <?php endif; ?>

                <?php foreach($locations as $index => $loc): ?>
                    <div class="memory-item" onclick="focusMap(<?php echo $loc['latitude']; ?>, <?php echo $loc['longitude']; ?>, true)">
                        <div class="memory-img-wrapper" onclick="event.stopPropagation(); openAlbum(<?php echo $loc['id']; ?>, '<?php echo $loc['place_name']; ?>')">
                            <?php if($loc['image']): 
                                $ext = strtolower(pathinfo($loc['image'], PATHINFO_EXTENSION));
                                $video_exts = ['mp4', 'webm', 'ogg', 'mov'];
                                if (in_array($ext, $video_exts)):
                            ?>
                                <div class="memory-img-wrapper" style="height: 140px; background: #000; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-play-circle-fill text-white fs-1 position-absolute" style="z-index: 2; opacity: 0.8;"></i>
                                    <video class="memory-img" style="opacity: 0.6;">
                                        <source src="../uploads/<?php echo $loc['image']; ?>" type="video/mp4">
                                    </video>
                                </div>
                                <div class="album-badge">
                                    <i class="bi bi-film me-1"></i> Video
                                </div>
                            <?php else: ?>
                                <img src="../uploads/<?php echo $loc['image']; ?>" class="memory-img">
                                <div class="album-badge">
                                    <i class="bi bi-images me-1"></i> Album
                                </div>
                            <?php endif; ?>
                            <?php else: ?>
                                <div class="memory-img d-flex align-items-center justify-content-center bg-light">
                                    <i class="bi bi-camera text-muted fs-2"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bold mb-1"><?php echo $loc['place_name']; ?></h6>
                            <?php if(!isset($is_friend_view)): ?>
                            <div class="d-flex gap-2">
                                <a href="javascript:void(0)" class="text-primary opacity-50" onclick='event.stopPropagation(); openEditModal(<?php echo json_encode($loc); ?>)'><i class="bi bi-pencil-square"></i></a>
                                <a href="index.php?url=location/delete&id=<?php echo $loc['id']; ?>" class="text-danger opacity-50" onclick="event.stopPropagation(); return confirm('Xóa kỷ niệm này?')"><i class="bi bi-trash"></i></a>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="memory-meta-grid">
                            <span class="memory-chip"><i class="bi bi-calendar3 text-primary"></i> <?php echo date('d/m/Y', strtotime($loc['visit_date'])); ?></span>
                            <span class="memory-chip"><i class="bi bi-emoji-smile text-warning"></i> <?php echo htmlspecialchars($loc['feeling']); ?></span>
                        </div>
                        <p class="small text-muted mb-0 text-truncate"><?php echo $loc['description']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- TAB 2: Bạn Bè -->
        <div id="tab-friends" class="tab-content-section">
            <h5 class="fw-bold mb-4">Bạn Bè & Kết nối</h5>
            <?php if(isset($_GET['friend_success'])): ?>
                <div class="alert alert-success border-0 rounded-4 py-2 small mb-3">Kết bạn thành công!</div>
            <?php endif; ?>
            <?php if(isset($_GET['friend_error'])): ?>
                <div class="alert alert-danger border-0 rounded-4 py-2 small mb-3">Lỗi kết bạn!</div>
            <?php endif; ?>
            
            <div class="mb-4">
                <label class="form-label small fw-bold text-muted">MÃ KẾT BẠN CỦA BẠN</label>
                <div class="p-3 border rounded-4 text-center bg-light">
                    <div id="qrcode" class="mb-2 d-flex justify-content-center"></div>
                    <small class="text-primary fw-bold">Quét mã bằng điện thoại</small>
                </div>
            </div>

            <div class="mb-4">
                <button class="btn btn-light w-100 rounded-pill py-2 small fw-bold mb-2" onclick="copyInviteLink()">
                    <i class="bi bi-link-45deg me-1"></i> Sao chép link mời
                </button>
                <form action="index.php?url=friend/add" method="POST">
                    <div class="input-group">
                        <input type="text" name="invite_link" class="form-control form-control-sm rounded-start-pill" placeholder="Dán link bạn bè vào đây...">
                        <button class="btn btn-primary btn-sm rounded-end-pill px-3" type="submit">Thêm</button>
                    </div>
                </form>
            </div>

            <h6 class="small fw-bold text-muted mb-3">DANH SÁCH BẠN BÈ (<?php echo count($friends); ?>)</h6>
            <div class="friend-list">
                <?php if(empty($friends)): ?>
                    <div class="text-center py-4 opacity-50 small">Chưa có bạn bè nào.<br>Hãy mời bạn bè tham gia!</div>
                <?php else: ?>
                    <?php foreach($friends as $friend): ?>
                        <div class="friend-card">
                            <div class="avatar-placeholder" style="width: 35px; height: 35px; border-radius: 50%; font-size: 14px; display:flex; align-items:center; justify-content:center; background:#ccc;">
                                <?php echo strtoupper(substr($friend['username'], 0, 1)); ?>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-bold small text-truncate"><?php echo $friend['full_name']; ?></div>
                                <div class="text-muted" style="font-size: 11px;">@<?php echo $friend['username']; ?></div>
                            </div>
                            <a href="index.php?url=location/friend_map&id=<?php echo $friend['id']; ?>" class="btn btn-primary btn-sm rounded-circle p-1" style="width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-geo-alt-fill" style="font-size: 12px;"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- TAB 3: Album -->
        <div id="tab-album" class="tab-content-section">
            <h5 class="fw-bold mb-4">Tất cả ảnh & Video</h5>
            <div class="row g-2">
                <?php 
                $has_images = false;
                foreach($locations as $loc) {
                    if(!empty($loc['image'])) {
                        $has_images = true;
                        $ext = strtolower(pathinfo($loc['image'], PATHINFO_EXTENSION));
                        $is_video = in_array($ext, ['mp4', 'webm', 'ogg', 'mov']);
                        echo '<div class="col-6 col-md-4">';
                        echo '<div style="position:relative; width:100%; padding-bottom:100%; border-radius:12px; overflow:hidden;" onclick="openAlbum('.$loc['id'].', \''.htmlspecialchars($loc['place_name']).'\')">';
                        if($is_video) {
                            echo '<video style="position:absolute; width:100%; height:100%; object-fit:cover;"><source src="../uploads/'.$loc['image'].'" type="video/mp4"></video>';
                            echo '<i class="bi bi-play-circle-fill text-white fs-2" style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%);"></i>';
                        } else {
                            echo '<img src="../uploads/'.$loc['image'].'" style="position:absolute; width:100%; height:100%; object-fit:cover;">';
                        }
                        echo '</div></div>';
                    }
                }
                if(!$has_images) {
                    echo '<div class="col-12 text-center text-muted py-4"><i class="bi bi-images display-4 opacity-25"></i><br>Chưa có ảnh nào</div>';
                }
                ?>
            </div>
        </div>

    </div>

    <!-- 3. Bottom Navigation -->
    <div class="bottom-nav">
        <div class="nav-item active" onclick="switchTab('timeline');">
            <i class="bi bi-house-door-fill"></i>
            Home
        </div>
        <div class="nav-item" onclick="window.scrollTo(0,0);">
            <i class="bi bi-geo-alt-fill"></i>
            Map
        </div>
        <div class="nav-item-camera" onclick="openLocketCamera()">
            <i class="bi bi-camera-fill"></i>
        </div>
        <div class="nav-item" onclick="switchTab('album');">
            <i class="bi bi-images"></i>
            Album
        </div>
        <div class="nav-item" onclick="switchTab('friends');">
            <i class="bi bi-people-fill"></i>
            Friends
        </div>
    </div>
</div>

<!-- Modal Thêm Kỷ Niệm (Album Support) -->
<div class="modal fade" id="addMemoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0 p-4">
                <h5 class="modal-title fw-bold">Lưu Giữ Kỷ Niệm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=location/save" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <input type="hidden" name="latitude" id="lat">
                    <input type="hidden" name="longitude" id="lng">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">TÊN ĐỊA ĐIỂM</label>
                        <input type="text" name="place_name" class="form-control form-control-premium" placeholder="Ví dụ: Bãi biển Mỹ Khê..." required>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">NGÀY GHÉ THĂM</label>
                            <input type="date" name="visit_date" class="form-control form-control-premium" required value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">CẢM XÚC</label>
                            <select name="feeling" class="form-select form-control-premium">
                                <option value="Hạnh phúc">😊 Hạnh phúc</option>
                                <option value="Tuyệt vời">🤩 Tuyệt vời</option>
                                <option value="Bình yên">🧘 Bình yên</option>
                                <option value="Thú vị">🎈 Thú vị</option>
                                <option value="Nhớ nhung">🥺 Nhớ nhung</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">MÔ TẢ NGẮN</label>
                        <textarea name="description" class="form-control form-control-premium" rows="3" placeholder="Ghi lại đôi dòng cảm xúc của bạn..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">QUYỀN RIÊNG TƯ (CHẾ ĐỘ HIỂN THỊ)</label>
                        <select name="privacy" id="add_privacy" class="form-select form-control-premium" onchange="toggleSpecificFriends('add')">
                            <option value="public">🌐 Công khai (Ai cũng có thể xem trên bảng tin)</option>
                            <option value="friends" selected>👥 Bạn bè (Chỉ những người đã kết bạn mới thấy)</option>
                            <option value="specific_friends">👥 Bạn bè cụ thể (Chọn người được xem)</option>
                            <option value="private">🔒 Chỉ mình tôi (Ẩn hoàn toàn khỏi bảng tin)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="addSpecificFriendsContainer" style="display: none; max-height: 150px; overflow-y: auto; background: rgba(255,255,255,0.05); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                        <label class="form-label small fw-bold text-primary mb-2">CHỌN BẠN BÈ ĐƯỢC XEM</label>
                        <?php if(!empty($friends)): ?>
                            <?php foreach($friends as $f): ?>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="visible_friends[]" value="<?php echo $f['id']; ?>" id="add_friend_<?php echo $f['id']; ?>">
                                    <label class="form-check-label text-white-50 small" for="add_friend_<?php echo $f['id']; ?>">
                                        <?php echo htmlspecialchars($f['full_name']); ?> (@<?php echo htmlspecialchars($f['username']); ?>)
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-muted small">Chưa có bạn bè nào để chọn.</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">ALBUM ẢNH/VIDEO (CHỌN NHIỀU)</label>
                        <input type="file" name="images[]" class="form-control form-control-premium" accept="image/*,video/*" multiple>
                        <small class="text-muted">Hỗ trợ ảnh và video (mp4, webm). Ảnh/Video đầu tiên sẽ là đại diện.</small>
                    </div>

                    <div class="alert alert-primary border-0 rounded-4 d-flex align-items-center py-2" id="location-status">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        <small>Đang lấy vị trí hiện tại...</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-premium rounded-pill px-4 shadow"><i class="bi bi-send-fill me-1"></i> Đăng Kỷ Niệm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Album (Lightbox) -->
<div class="modal fade album-lightbox" id="albumModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 bg-dark">
            <div class="modal-header border-0 pb-0 p-4 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="modal-title fw-bold text-white" id="albumTitle">Album Ảnh</h5>
                    <a href="#" id="manageAlbumLink" class="btn btn-sm btn-white-glass rounded-pill px-3">
                        <i class="bi bi-gear-fill me-1"></i> Quản lý
                    </a>
                </div>
                <div class="album-toolbar ms-auto me-3">
                    <button type="button" onclick="toggleAlbumSlideshow()" title="Auto slideshow"><i class="bi bi-play-fill" id="albumPlayIcon"></i></button>
                    <button type="button" onclick="openFullscreenAlbum()" title="Fullscreen"><i class="bi bi-arrows-fullscreen"></i></button>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="album-stage">
                <div id="albumCarousel" class="carousel slide carousel-fade" data-bs-ride="false">
                    <div class="carousel-inner rounded-4" id="albumItems">
                        <!-- Images will be injected here -->
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#albumCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon"></span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#albumCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon"></span>
                    </button>
                </div>
                <div class="album-thumbs text-start" id="albumThumbs"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chỉnh Sửa Kỷ Niệm -->
<div class="modal fade" id="editMemoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Chỉnh Sửa Kỷ Niệm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?url=location/update" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="edit_id">
                    
                    <div class="mb-3">
                        <label class="form-label small fw-bold">TÊN ĐỊA ĐIỂM</label>
                        <input type="text" name="place_name" id="edit_place_name" class="form-control form-control-premium" required>
                    </div>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold">NGÀY GHÉ THĂM</label>
                            <input type="date" name="visit_date" id="edit_visit_date" class="form-control form-control-premium" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold">CẢM XÚC</label>
                            <select name="feeling" id="edit_feeling" class="form-select form-control-premium">
                                <option value="Hạnh phúc">😊 Hạnh phúc</option>
                                <option value="Tuyệt vời">🤩 Tuyệt vời</option>
                                <option value="Bình yên">🧘 Bình yên</option>
                                <option value="Thú vị">🎈 Thú vị</option>
                                <option value="Nhớ nhung">🥺 Nhớ nhung</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">MÔ TẢ NGẮN</label>
                        <textarea name="description" id="edit_description" class="form-control form-control-premium" rows="3"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">QUYỀN RIÊNG TƯ (CHẾ ĐỘ HIỂN THỊ)</label>
                        <select name="privacy" id="edit_privacy" class="form-select form-control-premium" onchange="toggleSpecificFriends('edit')">
                            <option value="public">🌐 Công khai (Ai cũng có thể xem trên bảng tin)</option>
                            <option value="friends">👥 Bạn bè (Chỉ những người đã kết bạn mới thấy)</option>
                            <option value="specific_friends">👥 Bạn bè cụ thể (Chọn người được xem)</option>
                            <option value="private">🔒 Chỉ mình tôi (Ẩn hoàn toàn khỏi bảng tin)</option>
                        </select>
                    </div>

                    <div class="mb-3" id="editSpecificFriendsContainer" style="display: none; max-height: 150px; overflow-y: auto; background: rgba(255,255,255,0.05); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                        <label class="form-label small fw-bold text-primary mb-2">CHỌN BẠN BÈ ĐƯỢC XEM</label>
                        <?php if(!empty($friends)): ?>
                            <?php foreach($friends as $f): ?>
                                <div class="form-check mb-1">
                                    <input class="form-check-input" type="checkbox" name="visible_friends[]" value="<?php echo $f['id']; ?>" id="edit_friend_<?php echo $f['id']; ?>">
                                    <label class="form-check-label text-white-50 small" for="edit_friend_<?php echo $f['id']; ?>">
                                        <?php echo htmlspecialchars($f['full_name']); ?> (@<?php echo htmlspecialchars($f['username']); ?>)
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-muted small">Chưa có bạn bè nào để chọn.</div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">THÊM ẢNH/VIDEO VÀO ALBUM (CHỌN NHIỀU)</label>
                        <input type="file" name="new_images[]" class="form-control form-control-premium" accept="image/*,video/*" multiple>
                        <small class="text-muted">Các tệp này sẽ được thêm vào album hiện tại của bạn.</small>
                        <div id="edit_current_image" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-premium rounded-pill px-4"><i class="bi bi-send-fill me-1"></i> Cập Nhật & Đăng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bottom Navigation Bar (Mobile) -->
<nav class="bottom-nav">
    <div class="bottom-nav-item active" onclick="switchMobileTab('map', this)">
        <i class="bi bi-map-fill"></i>
        <span>Bản đồ</span>
    </div>
    <div class="bottom-nav-item" onclick="switchMobileTab('profile', this)">
        <i class="bi bi-person-circle"></i>
        <span>Cá nhân</span>
    </div>
    <div class="bottom-nav-item center-cam" onclick="openLocketCamera()">
        <i class="bi bi-camera-fill"></i>
    </div>
    <div class="bottom-nav-item" onclick="switchMobileTab('social', this)">
        <i class="bi bi-people-fill"></i>
        <span>Bạn bè</span>
    </div>
</nav>

<!-- JS Dependencies -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<!-- Leaflet Control Geocoder -->
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>

<script>
    // Force HTTPS for better GPS accuracy
    if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
        location.replace(`https:${location.href.substring(location.protocol.length)}`);
    }
    // Initialize Map
    var map = L.map('map', {
        zoomControl: false
    }).setView([10.762622, 106.660172], 13);

    L.control.zoom({ position: 'bottomright' }).addTo(map);

    // Add Geocoder (Search Bar)
    var geocoder = L.Control.geocoder({
        defaultMarkGeocode: false,
        placeholder: "Tìm kiếm địa điểm..."
    })
    .on('markgeocode', function(e) {
        var bbox = e.geocode.bbox;
        var poly = L.polygon([
            bbox.getSouthEast(),
            bbox.getNorthEast(),
            bbox.getNorthWest(),
            bbox.getSouthWest()
        ]);
        map.fitBounds(poly.getBounds());
        
        // Cập nhật marker vị trí hiện tại
        updateLocationFromMapClick(e.geocode.center);
    })
    .addTo(map);

    // Xử lý sự kiện click trên bản đồ để chọn vị trí
    map.on('click', function(e) {
        updateLocationFromMapClick(e.latlng);
    });

    function updateLocationFromMapClick(latlng) {
        userManuallySetLocation = true;
        followLiveLocation = false;
        
        const btn = document.getElementById('followLocationBtn');
        if (btn) btn.classList.remove('active');
        
        const latEl = document.getElementById('lat');
        const lngEl = document.getElementById('lng');
        if (latEl) latEl.value = latlng.lat;
        if (lngEl) lngEl.value = latlng.lng;
        
        locketLat = latlng.lat;
        locketLng = latlng.lng;

        const timeStr = new Date().toLocaleTimeString('vi-VN');
        const dragPopupHtml = `<strong>Vị trí đã chọn</strong><br>
            <small>${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}</small><br>
            <small>${timeStr}</small>`;
        
        if (!currentLocationMarker) {
            currentLocationMarker = L.marker([latlng.lat, latlng.lng], { 
                icon: liveLocationIcon, 
                zIndexOffset: 2000,
                draggable: true 
            }).addTo(map).bindPopup(dragPopupHtml).openPopup();
            
            // Re-bind dragend event
            currentLocationMarker.on('dragend', function(e) {
                updateLocationFromMapClick(e.target.getLatLng());
            });

            currentLocationCircle = L.circle([latlng.lat, latlng.lng], {
                radius: 10,
                color: '#22d3ee',
                fillColor: '#22d3ee',
                fillOpacity: 0.12,
                weight: 1,
                interactive: false
            }).addTo(map);
        } else {
            currentLocationMarker.setLatLng(latlng);
            currentLocationMarker.setPopupContent(dragPopupHtml).openPopup();
            if (currentLocationCircle) {
                currentLocationCircle.setLatLng(latlng);
                currentLocationCircle.setRadius(10);
            }
        }
        
        updateLocationStatus(
            '<i class="bi bi-pin-map-fill me-2"></i> <small>Đã ghim vị trí thủ công</small>',
            'success'
        );
        
        const hudText = document.getElementById('liveLocationHudText');
        if (hudText) {
            hudText.innerHTML = `<span class="accuracy-good">Ghim thủ công</span><br>${latlng.lat.toFixed(6)}, ${latlng.lng.toFixed(6)}`;
        }
    }

    const mapLayers = {
        dark: L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; OpenStreetMap &copy; CARTO',
            maxZoom: 20
        }),
        light: L.tileLayer('https://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
            attribution: '&copy; Google Maps',
            maxZoom: 20
        })
    };
    let activeMapLayer = mapLayers.dark.addTo(map);

    // Markers Data
    var savedLocations = <?php echo json_encode($locations); ?>;
    var friendLocations = <?php echo json_encode((!isset($is_friend_view) && isset($friend_locations)) ? $friend_locations : []); ?>;
    var markers = [];
    
    savedLocations.forEach(function(loc) {
        // Tạo Icon bằng ảnh nổi bật
        var photoIcon = L.divIcon({
            className: 'photo-marker',
            html: `
                <div class="marker-container">
                    ${loc.image ? 
                        `<img src="../uploads/${loc.image}" class="marker-image">` : 
                        `<div class="marker-image-empty"><i class="bi bi-geo-alt-fill"></i></div>`
                    }
                    <div class="marker-arrow"></div>
                </div>
            `,
            iconSize: [50, 50],
            iconAnchor: [25, 50],
            popupAnchor: [0, -50]
        });

        var marker = L.marker([loc.latitude, loc.longitude], { icon: photoIcon }).addTo(map);
        
        var popupContent = `
            <div class="p-2" style="width: 240px">
                ${loc.image ? `<img src="../uploads/${loc.image}" class="img-fluid rounded-3 mb-2 shadow-sm" onclick="openAlbum(${loc.id}, decodeURIComponent('${encodeURIComponent(loc.place_name || 'Album')}'))" style="cursor:pointer">` : ''}
                <h6 class="fw-bold mb-1">${loc.place_name}</h6>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <small class="text-muted"><i class="bi bi-calendar-event"></i> ${loc.visit_date}</small>
                    <span class="badge bg-primary-subtle text-primary rounded-pill">${loc.feeling}</span>
                </div>
                <p class="small text-secondary mb-0">${loc.description || 'Không có mô tả'}</p>
                <button class="btn btn-primary btn-sm w-100 mt-2 rounded-pill" onclick="openAlbum(${loc.id}, decodeURIComponent('${encodeURIComponent(loc.place_name || 'Album')}'))">Xem Album</button>
            </div>
        `;
        
        marker.bindPopup(popupContent, {
            className: 'premium-popup',
            maxWidth: 300
        });
        
        markers[loc.id] = marker;
    });

    friendLocations.forEach(function(loc) {
        var friendIcon = L.divIcon({
            className: 'photo-marker',
            html: `
                <div class="marker-container" style="border-color:#bfdbfe;">
                    ${loc.image ? 
                        `<img src="../uploads/${loc.image}" class="marker-image">` : 
                        `<div class="marker-image-empty"><i class="bi bi-people-fill"></i></div>`
                    }
                    <div class="marker-arrow"></div>
                </div>
            `,
            iconSize: [50, 50],
            iconAnchor: [25, 50],
            popupAnchor: [0, -50]
        });

        var friendMarker = L.marker([loc.latitude, loc.longitude], { icon: friendIcon }).addTo(map);
        var friendPopup = `
            <div class="p-2" style="width: 250px">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <div class="avatar-placeholder" style="width:28px;height:28px;font-size:12px;border-radius:50%;background:#e0f2fe;color:#0d6efd;">
                        ${(loc.username || '?').charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <div class="small fw-bold text-primary">${loc.full_name || 'Bạn bè'}</div>
                        <div class="text-muted" style="font-size:11px;">@${loc.username || ''}</div>
                    </div>
                </div>
                ${loc.image ? `<img src="../uploads/${loc.image}" class="img-fluid rounded-3 mb-2 shadow-sm" onclick="openAlbum(${loc.id}, decodeURIComponent('${encodeURIComponent(loc.place_name || 'Album')}'))" style="cursor:pointer;max-height:150px;object-fit:cover;width:100%;">` : ''}
                <h6 class="fw-bold mb-1">${loc.place_name}</h6>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <small class="text-muted"><i class="bi bi-calendar-event"></i> ${loc.visit_date}</small>
                    <span class="badge bg-primary-subtle text-primary rounded-pill">${loc.feeling || ''}</span>
                </div>
                <p class="small text-secondary mb-0">${loc.description || 'Không có mô tả'}</p>
                <button class="btn btn-primary btn-sm w-100 mt-2 rounded-pill" onclick="openAlbum(${loc.id}, decodeURIComponent('${encodeURIComponent(loc.place_name || 'Album')}'))">
                    <i class="bi bi-images me-1"></i> Xem album
                </button>
            </div>
        `;

        friendMarker.bindPopup(friendPopup, {
            className: 'premium-popup',
            maxWidth: 310
        });

        markers[loc.id] = friendMarker;
    });

    const journeyPoints = savedLocations
        .filter(loc => loc.latitude && loc.longitude)
        .map(loc => [Number(loc.latitude), Number(loc.longitude)]);

    let routeLine = null;
    if (journeyPoints.length > 1) {
        routeLine = L.polyline(journeyPoints, {
            color: '#22d3ee',
            weight: 4,
            opacity: 0.78,
            dashArray: '10 12'
        }).addTo(map);
    }

    journeyPoints.forEach((point, index) => {
        L.circleMarker(point, {
            radius: 26 + Math.min(index * 3, 18),
            stroke: false,
            fillColor: '#f43f5e',
            fillOpacity: 0.12
        }).addTo(map);
    });

    function setMapTheme(theme) {
        map.removeLayer(activeMapLayer);
        activeMapLayer = mapLayers[theme].addTo(map);
        document.getElementById('darkMapBtn').classList.toggle('active', theme === 'dark');
        document.getElementById('lightMapBtn').classList.toggle('active', theme === 'light');
    }

    function fitJourneyRoute() {
        if (routeLine) {
            map.fitBounds(routeLine.getBounds(), { padding: [60, 60] });
        } else if (journeyPoints.length === 1) {
            map.flyTo(journeyPoints[0], 15);
        }
    }

    // Theo dõi vị trí thực tế — GPS ưu tiên, bộ lọc độ chính xác thông minh
    let currentLocationMarker = null;
    let currentLocationCircle = null;
    let locationWatchId = null;
    let followLiveLocation = true;
    let bestAccuracy = Infinity;
    let locationFixCount = 0;
    let userManuallySetLocation = false;
    let _lastRawLat = null, _lastRawLng = null;
    let _accuracyRetryTimer = null;

    // Options chính: yêu cầu độ chính xác cao nhất
    const geoOptions = {
        enableHighAccuracy: true,
        maximumAge: 0,       // Không dùng cache cũ
        timeout: 15000       // Giảm xuống 15s để retry nhanh hơn
    };

    // Options fallback: dùng khi thiết bị không có GPS tốt
    const geoFallbackOptions = {
        enableHighAccuracy: false,
        maximumAge: 10000,
        timeout: 10000
    };

    const liveLocationIcon = L.divIcon({
        className: 'live-location-marker',
        html: '<div class="live-location-dot"><div class="live-location-pulse"></div></div>',
        iconSize: [18, 18],
        iconAnchor: [9, 9]
    });

    /** Sửa trường hợp lat/lng bị đảo (hay gặp khi lưu sai) */
    function fixCoordinates(lat, lng) {
        lat = parseFloat(lat);
        lng = parseFloat(lng);
        if (!isFinite(lat) || !isFinite(lng)) return null;
        const latLooksLikeLng = lat > 50 && lat < 180;
        const lngLooksLikeLat = lng > -90 && lng < 50;
        if (latLooksLikeLng && lngLooksLikeLat) {
            return { lat: lng, lng: lat };
        }
        return { lat, lng };
    }

    function zoomForAccuracy(accuracy) {
        if (accuracy <= 20) return 18;
        if (accuracy <= 50) return 17;
        if (accuracy <= 120) return 16;
        return 15;
    }

    function updateLocationHud(lat, lng, accuracy) {
        const hud = document.getElementById('liveLocationHud');
        const hudText = document.getElementById('liveLocationHudText');
        if (!hud || !hudText) return;
        hud.style.display = 'block';
        const accClass = accuracy <= 40 ? 'accuracy-good' : (accuracy <= 150 ? 'accuracy-mid' : 'accuracy-low');
        const accLabel = accuracy <= 40 ? 'GPS tốt' : (accuracy <= 150 ? 'GPS trung bình' : 'Độ chính xác thấp — bấm định vị lại');
        hudText.innerHTML = `<span class="${accClass}">${accLabel}</span><br>${lat.toFixed(6)}, ${lng.toFixed(6)} · ±${Math.round(accuracy)}m`;
    }

    function updateLocationStatus(message, type = 'success') {
        const statusEl = document.getElementById('location-status');
        if (!statusEl) return;
        const alertClass = type === 'success' ? 'alert-success' : (type === 'warning' ? 'alert-warning' : 'alert-danger');
        statusEl.className = `alert ${alertClass} border-0 rounded-4 d-flex align-items-center py-2 animate-fade-in`;
        statusEl.innerHTML = message;
    }

    let lastFlyLatLng = null;

    function updateCurrentPosition(position) {
        if (userManuallySetLocation) return;

        const fixed = fixCoordinates(position.coords.latitude, position.coords.longitude);
        if (!fixed) return;

        const lat = fixed.lat;
        const lng = fixed.lng;
        const accuracy = position.coords.accuracy || 999;
        const improved = accuracy < bestAccuracy;
        bestAccuracy = Math.min(bestAccuracy, accuracy);
        locationFixCount++;

        const latEl = document.getElementById('lat');
        const lngEl = document.getElementById('lng');
        if (latEl) latEl.value = lat;
        if (lngEl) lngEl.value = lng;

        const updatedAt = new Date().toLocaleTimeString('vi-VN');
        updateLocationStatus(
            `<i class="bi bi-geo-alt-fill me-2"></i> <small>GPS · ${updatedAt} · ${lat.toFixed(5)}, ${lng.toFixed(5)} · ±${Math.round(accuracy)}m</small>`,
            accuracy > 200 ? 'warning' : 'success'
        );
        updateLocationHud(lat, lng, accuracy);

        const popupHtml = `<strong>Vị trí của bạn</strong><br>
            <small>${lat.toFixed(6)}, ${lng.toFixed(6)}</small><br>
            <small>±${Math.round(accuracy)}m · ${updatedAt}</small><br>
            <span class="badge bg-warning mt-1 text-dark">Kéo thả marker để sửa vị trí</span>`;

        if (!currentLocationMarker) {
            currentLocationMarker = L.marker([lat, lng], { 
                icon: liveLocationIcon, 
                zIndexOffset: 2000,
                draggable: true 
            }).addTo(map).bindPopup(popupHtml);

            currentLocationMarker.on('dragend', function(e) {
                const newPos = e.target.getLatLng();
                userManuallySetLocation = true;
                followLiveLocation = false;
                
                const btn = document.getElementById('followLocationBtn');
                if (btn) btn.classList.remove('active');
                
                const latEl = document.getElementById('lat');
                const lngEl = document.getElementById('lng');
                if (latEl) latEl.value = newPos.lat;
                if (lngEl) lngEl.value = newPos.lng;
                
                locketLat = newPos.lat;
                locketLng = newPos.lng;

                const timeStr = new Date().toLocaleTimeString('vi-VN');
                const dragPopupHtml = `<strong>Vị trí đã ghim tay</strong><br>
                    <small>${newPos.lat.toFixed(6)}, ${newPos.lng.toFixed(6)}</small><br>
                    <small>${timeStr}</small>`;
                
                currentLocationMarker.setPopupContent(dragPopupHtml).openPopup();
                if (currentLocationCircle) {
                    currentLocationCircle.setLatLng(newPos);
                    currentLocationCircle.setRadius(8);
                }
                
                updateLocationStatus(
                    '<i class="bi bi-pin-map-fill me-2"></i> <small>Đã ghim vị trí thủ công</small>',
                    'success'
                );
                
                const hudText = document.getElementById('liveLocationHudText');
                if (hudText) {
                    hudText.innerHTML = `<span class="accuracy-good">Ghim thủ công</span><br>${newPos.lat.toFixed(6)}, ${newPos.lng.toFixed(6)}`;
                }
                
                const camStatus = document.getElementById('camLocationStatus');
                if (camStatus) {
                    camStatus.innerHTML = `<i class="bi bi-pin-map-fill text-success me-1"></i> Đã ghim tay`;
                }
            });

            currentLocationCircle = L.circle([lat, lng], {
                radius: Math.max(accuracy, 8),
                color: '#22d3ee',
                fillColor: '#22d3ee',
                fillOpacity: 0.12,
                weight: 1,
                interactive: false
            }).addTo(map);
        } else {
            currentLocationMarker.setLatLng([lat, lng]);
            currentLocationMarker.setPopupContent(popupHtml);
            currentLocationCircle.setLatLng([lat, lng]);
            currentLocationCircle.setRadius(Math.max(accuracy, 8));
        }

        if (followLiveLocation) {
            const here = L.latLng(lat, lng);
            const movedM = lastFlyLatLng ? lastFlyLatLng.distanceTo(here) : 999;
            if (locationFixCount <= 3 || improved || movedM > 25) {
                const zoom = zoomForAccuracy(accuracy);
                map.flyTo([lat, lng], zoom, { duration: locationFixCount <= 2 ? 1.2 : 0.6 });
                lastFlyLatLng = here;
            }
        }

        const cameraUi = document.getElementById('locketCameraUI');
        if (cameraUi && cameraUi.style.display === 'flex') {
            locketLat = lat;
            locketLng = lng;
            const camStatus = document.getElementById('camLocationStatus');
            if (camStatus) {
                camStatus.innerHTML = `<i class="bi bi-geo-alt-fill text-success me-1"></i> GPS · ±${Math.round(accuracy)}m`;
            }
        }
    }

    function onLocationError(error) {
        // Nếu timeout và chưa có fix nào, thử lại với fallback options
        if (error.code === 3 && locationFixCount === 0) {
            console.warn('GPS timeout, thử lại với chế độ fallback...');
            navigator.geolocation.getCurrentPosition(
                updateCurrentPosition,
                (fallbackErr) => {
                    const messages = {
                        1: 'Cho phép truy cập vị trí (biểu tượng ổ khóa trên thanh địa chỉ).',
                        2: 'Không xác định được GPS. Bật Location Services trên thiết bị.',
                        3: 'Không lấy được GPS — hãy ra ngoài trời và bấm nút định vị lại.'
                    };
                    updateLocationStatus(
                        `<i class="bi bi-exclamation-triangle-fill me-2"></i> <small>${messages[fallbackErr.code] || 'Lỗi định vị'}</small>`,
                        'warning'
                    );
                    const hudText = document.getElementById('liveLocationHudText');
                    if (hudText) hudText.textContent = messages[fallbackErr.code] || 'Lỗi định vị';
                },
                geoFallbackOptions
            );
            return;
        }

        const messages = {
            1: 'Cho phép truy cập vị trí (biểu tượng ổ khóa trên thanh địa chỉ).',
            2: 'Không xác định được GPS. Bật Location Services trên thiết bị.',
            3: 'GPS quá lâu — ra ngoài trời hoặc bấm nút định vị lại.'
        };
        updateLocationStatus(
            `<i class="bi bi-exclamation-triangle-fill me-2"></i> <small>${messages[error.code] || 'Lỗi định vị'}</small>`,
            'warning'
        );
        const hudText = document.getElementById('liveLocationHudText');
        if (hudText) hudText.textContent = messages[error.code] || 'Lỗi định vị';
    }

    function requestAccurateLocation() {
        if (!navigator.geolocation) return;
        navigator.geolocation.getCurrentPosition(
            updateCurrentPosition,
            onLocationError,
            geoOptions
        );
    }

    function startLiveLocationTracking() {
        if (!navigator.geolocation) {
            updateLocationStatus('<i class="bi bi-exclamation-triangle-fill me-2"></i> <small>Trình duyệt không hỗ trợ định vị</small>', 'warning');
            return;
        }
        if (locationWatchId !== null) return;

        document.getElementById('liveLocationHud').style.display = 'block';
        document.getElementById('liveLocationHudText').textContent = 'Đang lấy GPS chính xác...';

        // Lấy ngay 1 lần bằng getCurrentPosition (nhanh hơn watchPosition lần đầu)
        requestAccurateLocation();

        // watchPosition để liên tục cập nhật
        locationWatchId = navigator.geolocation.watchPosition(
            updateCurrentPosition,
            onLocationError,
            geoOptions
        );

        // Cứ 30 giây request thêm 1 lần để đảm bảo vị trí không bị stuck
        _accuracyRetryTimer = setInterval(() => {
            if (!userManuallySetLocation && bestAccuracy > 80) {
                console.log('Đang thử nâng cao độ chính xác GPS...');
                requestAccurateLocation();
            }
        }, 30000);
    }

    function stopLiveLocationTracking() {
        if (locationWatchId !== null) {
            navigator.geolocation.clearWatch(locationWatchId);
            locationWatchId = null;
        }
        if (_accuracyRetryTimer !== null) {
            clearInterval(_accuracyRetryTimer);
            _accuracyRetryTimer = null;
        }
    }

    function refreshMyLocation() {
        bestAccuracy = Infinity;
        locationFixCount = 0;
        lastFlyLatLng = null;
        userManuallySetLocation = false;
        document.getElementById('liveLocationHudText').textContent = 'Đang định vị lại...';
        followLiveLocation = true;
        document.getElementById('followLocationBtn')?.classList.add('active');
        requestAccurateLocation();
    }

    function toggleFollowLocation() {
        followLiveLocation = !followLiveLocation;
        const btn = document.getElementById('followLocationBtn');
        btn?.classList.toggle('active', followLiveLocation);

        if (followLiveLocation && currentLocationMarker) {
            const pos = currentLocationMarker.getLatLng();
            map.flyTo(pos, 17, { duration: 1 });
            currentLocationMarker.openPopup();
        }
    }

    startLiveLocationTracking();
    window.addEventListener('beforeunload', stopLiveLocationTracking);

    // Invalidate map size on load and resize to prevent gray tiles
    setTimeout(() => { map.invalidateSize(true); }, 300);
    setTimeout(() => { map.invalidateSize(true); }, 800);

    // Debounced resize handler to fix map on DevTools toggle
    let _resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(_resizeTimer);
        _resizeTimer = setTimeout(() => {
            map.invalidateSize(true);
        }, 200);
    });

    // Also invalidate when visibility changes (e.g. switching tabs)
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            setTimeout(() => map.invalidateSize(true), 200);
        }
    });

    function focusMap(lat, lng, openPopup = false) {
        map.flyTo([lat, lng], 16, {
            duration: 1.5
        });
        
        if (openPopup) {
            savedLocations.concat(friendLocations).forEach(loc => {
                if(loc.latitude == lat && loc.longitude == lng) {
                    setTimeout(() => markers[loc.id].openPopup(), 1500);
                }
            });
        }
    }

    function focusMemory(locationId) {
        const marker = markers[locationId];
        const loc = savedLocations.concat(friendLocations).find(item => Number(item.id) === Number(locationId));
        if (!marker || !loc) return;

        map.flyTo([loc.latitude, loc.longitude], 16, {
            duration: 1.5
        });
        setTimeout(() => marker.openPopup(), 1500);
    }

    // Album Functionality (Supports Photos & Videos)
    function openAlbum(id, title) {
        document.getElementById('albumTitle').innerText = title;
        const itemsContainer = document.getElementById('albumItems');
        const thumbsContainer = document.getElementById('albumThumbs');
        itemsContainer.innerHTML = '<div class="text-white py-5"><div class="spinner-border text-primary"></div></div>';
        thumbsContainer.innerHTML = '';
        
        // Cập nhật link quản lý (chỉ hiện nếu là của mình)
        const manageLink = document.getElementById('manageAlbumLink');
        <?php if(isset($is_friend_view) && $is_friend_view): ?>
            manageLink.style.display = 'none';
        <?php else: ?>
            manageLink.style.display = 'inline-block';
            manageLink.href = `index.php?url=location/manageAlbum&id=${id}`;
        <?php endif; ?>

        var albumModal = new bootstrap.Modal(document.getElementById('albumModal'));
        albumModal.show();
        
        const videoExtensions = ['mp4', 'webm', 'ogg', 'mov'];

        fetch(`index.php?url=location/getAlbum&id=${id}`)
            .then(res => res.json())
            .then(data => {
                if (data.length > 0) {
                    itemsContainer.innerHTML = data.map((item, index) => {
                        const ext = item.image_path.split('.').pop().toLowerCase();
                        const isVideo = videoExtensions.includes(ext);
                        
                        if (isVideo) {
                            return `
                                <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                    <video controls class="d-block w-100 rounded-4" style="max-height: 70vh; background: #000;">
                                        <source src="../uploads/${item.image_path}" type="video/${ext === 'mov' ? 'mp4' : ext}">
                                        Trình duyệt của bạn không hỗ trợ xem video.
                                    </video>
                                </div>
                            `;
                        } else {
                            return `
                                <div class="carousel-item ${index === 0 ? 'active' : ''}">
                                    <img src="../uploads/${item.image_path}" class="d-block w-100 rounded-4" style="max-height: 70vh; object-fit: contain;">
                                </div>
                            `;
                        }
                    }).join('');
                    thumbsContainer.innerHTML = data.map((item, index) => {
                        const ext = item.image_path.split('.').pop().toLowerCase();
                        const isVideo = videoExtensions.includes(ext);
                        const media = isVideo
                            ? `<video muted><source src="../uploads/${item.image_path}" type="video/${ext === 'mov' ? 'mp4' : ext}"></video>`
                            : `<img src="../uploads/${item.image_path}" alt="Album item ${index + 1}">`;
                        return `<button class="album-thumb ${index === 0 ? 'active' : ''}" type="button" data-bs-target="#albumCarousel" data-bs-slide-to="${index}" onclick="setActiveAlbumThumb(${index})">${media}</button>`;
                    }).join('');
                } else {
                    itemsContainer.innerHTML = '<div class="text-white py-5">Chưa có ảnh hoặc video trong album này.</div>';
                    thumbsContainer.innerHTML = '';
                }
            });
    }

    let albumSlideshow = null;
    function setActiveAlbumThumb(index) {
        document.querySelectorAll('.album-thumb').forEach((thumb, thumbIndex) => {
            thumb.classList.toggle('active', thumbIndex === index);
        });
    }

    document.getElementById('albumCarousel').addEventListener('slid.bs.carousel', function (event) {
        setActiveAlbumThumb(event.to);
    });

    function toggleAlbumSlideshow() {
        const carouselElement = document.getElementById('albumCarousel');
        const icon = document.getElementById('albumPlayIcon');
        if (albumSlideshow) {
            albumSlideshow.pause();
            albumSlideshow = null;
            icon.className = 'bi bi-play-fill';
            return;
        }
        albumSlideshow = bootstrap.Carousel.getOrCreateInstance(carouselElement, {
            interval: 2600,
            ride: false,
            wrap: true,
            touch: true
        });
        albumSlideshow.cycle();
        icon.className = 'bi bi-pause-fill';
    }

    function openFullscreenAlbum() {
        const modalContent = document.querySelector('#albumModal .modal-content');
        if (modalContent && modalContent.requestFullscreen) {
            modalContent.requestFullscreen();
        }
    }

    // Social Functionality
    const inviteLink = window.location.origin + window.location.pathname + "?url=friend/add&token=<?php echo $_SESSION['user_id'] * 12345; ?>";
    
    new QRCode(document.getElementById("qrcode"), {
        text: inviteLink,
        width: 120,
        height: 120,
        colorDark : "#6366f1",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });

    function copyInviteLink() {
        navigator.clipboard.writeText(inviteLink).then(() => {
            alert("Đã sao chép link mời kết bạn!");
        });
    }

    function openAvatarUploader() {
        const input = document.getElementById('avatarInput');
        if (input) {
            input.click();
        }
    }

    function submitAvatarForm() {
        const form = document.getElementById('avatarUploadForm');
        if (form) {
            form.submit();
        }
    }

    function openEditModal(loc) {
        document.getElementById('edit_id').value = loc.id;
        document.getElementById('edit_place_name').value = loc.place_name;
        document.getElementById('edit_visit_date').value = loc.visit_date;
        document.getElementById('edit_feeling').value = loc.feeling;
        document.getElementById('edit_description').value = loc.description;
        document.getElementById('edit_privacy').value = loc.privacy || 'public';
        
        const currentImageDiv = document.getElementById('edit_current_image');
        if (loc.image) {
            currentImageDiv.innerHTML = `<img src="../uploads/${loc.image}" class="img-fluid rounded-3" style="max-height: 100px;">`;
        } else {
            currentImageDiv.innerHTML = '';
        }

        var editModal = new bootstrap.Modal(document.getElementById('editMemoryModal'));
        editModal.show();
    }

    // --- Locket Camera Logic ---
    let cameraStream = null;
    let currentFacingMode = "environment"; // user or environment
    const videoElem = document.getElementById('cameraVideo');
    const canvasElem = document.getElementById('photoPreviewCanvas');
    const ctx = canvasElem.getContext('2d');
    let capturedPhotoBase64 = null;
    let locketLat = null;
    let locketLng = null;

    async function openLocketCamera() {
        document.getElementById('locketCameraUI').style.display = 'flex';
        resetCameraUI();
        await startCamera(currentFacingMode);

        // Vị trí được cập nhật liên tục qua watchPosition (updateCurrentPosition)
        if (currentLocationMarker) {
            const pos = currentLocationMarker.getLatLng();
            locketLat = pos.lat;
            locketLng = pos.lng;
            document.getElementById('camLocationStatus').innerHTML = '<i class="bi bi-geo-alt-fill text-success me-1"></i> GPS live';
        } else {
            document.getElementById('camLocationStatus').innerHTML = '<i class="bi bi-geo-alt-fill text-warning me-1"></i> Đang tìm vị trí...';
        }
    }

    function closeLocketCamera() {
        document.getElementById('locketCameraUI').style.display = 'none';
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
            videoElem.srcObject = null;
        }
    }

    async function startCamera(facingMode) {
        const errorDiv = document.getElementById('cameraError');
        const errorMsg = document.getElementById('cameraErrorMsg');
        errorDiv.style.display = 'none';
        videoElem.style.display = 'block';

        // Dừng stream cũ nếu có
        if (cameraStream) {
            cameraStream.getTracks().forEach(track => track.stop());
            cameraStream = null;
        }

        // Kiểm tra trình duyệt có hỗ trợ không
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            errorMsg.textContent = 'Trình duyệt không hỗ trợ Camera API. Hãy dùng Chrome/Firefox mới nhất.';
            errorDiv.style.display = 'block';
            videoElem.style.display = 'none';
            return;
        }

        // Kiểm tra HTTPS (camera chỉ hoạt động trên HTTPS hoặc localhost)
        if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
            errorMsg.innerHTML = 'Camera yêu cầu kết nối <strong>HTTPS</strong> hoặc chạy trên <strong>localhost</strong>.<br>Hãy truy cập qua <code>http://localhost/...</code>';
            errorDiv.style.display = 'block';
            videoElem.style.display = 'none';
            return;
        }

        try {
            // Thử constraints đơn giản nhất trước
            const constraints = {
                video: {
                    facingMode: { ideal: facingMode },
                    width: { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            };

            cameraStream = await navigator.mediaDevices.getUserMedia(constraints);
            videoElem.srcObject = cameraStream;
            videoElem.muted = true; // Đảm bảo muted để autoplay hoạt động

            // Bắt buộc gọi play() thủ công
            const playPromise = videoElem.play();
            if (playPromise !== undefined) {
                playPromise
                    .then(() => {
                        console.log('Camera đang phát video thành công!');
                        errorDiv.style.display = 'none';
                    })
                    .catch(playErr => {
                        console.error('Lỗi play():', playErr);
                        // Thử lại không muted
                        videoElem.muted = false;
                        videoElem.play().catch(e => console.error(e));
                    });
            }
        } catch (err) {
            console.error('Camera error:', err);
            videoElem.style.display = 'none';

            // Phân tích lỗi cụ thể
            if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
                errorMsg.innerHTML = 'Bạn đã từ chối quyền camera.<br>Vào <strong>Settings > Site Permissions > Camera</strong> để cấp quyền lại.';
            } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
                // Thử lại với facingMode đơn giản hơn
                try {
                    cameraStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                    videoElem.srcObject = cameraStream;
                    videoElem.muted = true;
                    await videoElem.play();
                    console.log('Camera khởi động với fallback constraints!');
                    return;
                } catch (fallbackErr) {
                    errorMsg.innerHTML = 'Không tìm thấy camera trên thiết bị này.';
                }
            } else if (err.name === 'NotReadableError') {
                errorMsg.innerHTML = 'Camera đang được ứng dụng khác sử dụng. Đóng ứng dụng đó và thử lại.';
            } else if (err.name === 'OverconstrainedError') {
                // Thử lại với constraints tối thiểu
                try {
                    cameraStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
                    videoElem.srcObject = cameraStream;
                    videoElem.muted = true;
                    await videoElem.play();
                    return;
                } catch (e2) {
                    errorMsg.innerHTML = 'Camera không hỗ trợ cấu hình được yêu cầu.';
                }
            } else {
                errorMsg.innerHTML = 'Lỗi camera: ' + err.message;
            }
            errorDiv.style.display = 'block';
        }
    }

    function switchCamera() {
        currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
        startCamera(currentFacingMode);
    }

    function capturePhoto() {
        canvasElem.width = videoElem.videoWidth;
        canvasElem.height = videoElem.videoHeight;
        
        // Nếu dùng camera trước, lật ảnh lại cho đỡ ngược
        if (currentFacingMode === "user") {
            ctx.translate(canvasElem.width, 0);
            ctx.scale(-1, 1);
        }
        
        ctx.drawImage(videoElem, 0, 0, canvasElem.width, canvasElem.height);
        capturedPhotoBase64 = canvasElem.toDataURL('image/jpeg', 0.8);
        
        // Hide video, show canvas
        videoElem.style.display = 'none';
        canvasElem.style.display = 'block';
        
        // Swap controls
        document.getElementById('cameraControls').style.display = 'none';
        document.getElementById('postControls').style.display = 'flex';
    }

    function retakePhoto() {
        resetCameraUI();
    }

    function resetCameraUI() {
        videoElem.style.display = 'block';
        canvasElem.style.display = 'none';
        document.getElementById('cameraControls').style.display = 'flex';
        document.getElementById('postControls').style.display = 'none';
        document.getElementById('locketCaption').value = '';
        capturedPhotoBase64 = null;
    }

    function postLocketPhoto(albumName = '') {
        if (!capturedPhotoBase64) return;
        if (!locketLat || !locketLng) {
            alert("Đang lấy vị trí, vui lòng đợi...");
            return;
        }

        const caption = document.getElementById('locketCaption').value || 'Một khoảnh khắc tuyệt vời';
        const privacy = document.getElementById('locketPrivacy').value || 'friends';
        const postBtn = document.querySelector('.btn-locket-post');
        postBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang đăng...';
        postBtn.disabled = true;

        fetch('index.php?url=location/saveLocket', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                image: capturedPhotoBase64,
                caption: caption,
                lat: locketLat,
                lng: locketLng,
                privacy: privacy,
                album_name: albumName // Nếu rỗng, tự động lấy ngày
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Add marker to map
                var photoIcon = L.divIcon({
                    className: 'photo-marker',
                    html: `
                        <div class="marker-container" style="animation: slideUp 0.5s ease-out;">
                            <img src="${data.image_url}" class="marker-image">
                            <div class="marker-arrow"></div>
                        </div>
                    `,
                    iconSize: [50, 50],
                    iconAnchor: [25, 50],
                    popupAnchor: [0, -50]
                });
                
                var marker = L.marker([locketLat, locketLng], { icon: photoIcon }).addTo(map);
                marker.bindPopup(`
                    <div class="p-2 text-center" style="width: 240px">
                        <img src="${data.image_url}" class="img-fluid rounded-3 mb-2 shadow-sm" style="max-height: 200px; object-fit: cover;">
                        <h6 class="fw-bold mb-1">${data.place_name}</h6>
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                            <small class="text-muted"><i class="bi bi-clock"></i> Vừa xong</small>
                        </div>
                        <p class="small text-secondary mb-0">${caption}</p>
                    </div>
                `);

                // Fly to new marker
                map.flyTo([locketLat, locketLng], 16);
                
                closeLocketCamera();
                // Optionally reload to update sidebar or dynamically prepend HTML
                setTimeout(() => window.location.reload(), 1500);
            } else {
                alert("Lỗi: " + data.message);
                postBtn.innerHTML = '<i class="bi bi-send-fill"></i> Đăng ngay lên bản đồ';
                postBtn.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            postBtn.innerHTML = '<i class="bi bi-send-fill"></i> Đăng ngay lên bản đồ';
            postBtn.disabled = false;
        });
    }

    function saveToAlbumPrompt() {
        const albumName = prompt("Nhập tên album (ví dụ: Hành trình Hà Giang):", "Hành trình - " + new Date().toLocaleDateString('vi-VN'));
        if (albumName) {
            postLocketPhoto(albumName);
        }
    }

    // Auto Refresh markers for Real-time update (Polling every 10s)
    setInterval(() => {
        fetch('index.php?url=location/getUpdates')
            .then(res => res.json())
            .then(data => {
                if(data.has_updates) {
                    console.log("New updates found, reloading...");
                    // For simplicity, we just reload if there's a new post. 
                    // Better approach: fetch new locations and append markers dynamically
                    // window.location.reload(); 
                }
            })
            .catch(e => console.log(e));
    }, 10000);

    // AI Chat Widget
    const chatWidget = document.getElementById('aiChatWidget');
    const chatHistory = document.getElementById('chatHistory');
    const chatInput = document.getElementById('chatInput');

    function toggleChatWidget() {
        chatWidget.classList.toggle('open');
        if (chatWidget.classList.contains('open')) {
            chatInput.focus();
        }
    }

    function closeChatWidget() {
        chatWidget.classList.remove('open');
    }

    function setChatPrompt(text) {
        chatInput.value = text;
        chatInput.focus();
    }

    function appendChatMessage(role, message) {
        const wrapper = document.createElement('div');
        wrapper.className = 'chat-message ' + role;

        const avatar = document.createElement('div');
        avatar.className = 'avatar';
        avatar.textContent = role === 'user' ? 'T' : 'AI';

        const bubble = document.createElement('div');
        bubble.className = 'bubble';
        bubble.textContent = message;

        wrapper.appendChild(avatar);
        wrapper.appendChild(bubble);
        chatHistory.appendChild(wrapper);
        chatHistory.scrollTop = chatHistory.scrollHeight;
    }

    function getAiCoords() {
        if (typeof currentLocationMarker !== 'undefined' && currentLocationMarker) {
            const p = currentLocationMarker.getLatLng();
            return { latitude: p.lat, longitude: p.lng };
        }
        const latEl = document.getElementById('lat');
        const lngEl = document.getElementById('lng');
        if (latEl?.value && lngEl?.value) {
            return { latitude: latEl.value, longitude: lngEl.value };
        }
        return { latitude: '', longitude: '' };
    }

    function sendChatMessage() {
        const question = chatInput.value.trim();
        if (!question) return;

        appendChatMessage('user', question);
        chatInput.value = '';

        const loading = document.createElement('div');
        loading.className = 'chat-message assistant';
        loading.innerHTML = '<div class="avatar">AI</div><div class="bubble">Đang phân tích và tư vấn...</div>';
        chatHistory.appendChild(loading);
        chatHistory.scrollTop = chatHistory.scrollHeight;

        const coords = getAiCoords();
        fetch('index.php?url=ai/ask', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                question: question,
                latitude: coords.latitude,
                longitude: coords.longitude
            })
        })
        .then(res => res.json())
        .then(data => {
            loading.remove();
            if (data.success) {
                appendChatMessage('assistant', data.message);
            } else {
                appendChatMessage('assistant', 'Có lỗi: ' + data.message);
            }
        })
        .catch(() => {
            loading.remove();
            appendChatMessage('assistant', 'Không thể kết nối đến AI. Vui lòng thử lại');
        });
    }

    // Mobile Navigation Logic
    const sidebar = document.querySelector('.sidebar');
    const socialSidebar = document.querySelector('.social-sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function closeAllSidebars() {
        sidebar.classList.remove('mobile-open');
        socialSidebar.classList.remove('mobile-open');
        overlay.classList.remove('show');
        
        // Reset nav active state to Map
        document.querySelectorAll('.bottom-nav-item').forEach(el => el.classList.remove('active'));
        const mapTab = document.querySelector('.bottom-nav-item:first-child');
        if (mapTab) mapTab.classList.add('active');
        
        // Ensure map layout is correct after resizing
        setTimeout(() => { map.invalidateSize(); }, 300);
    }

    if (overlay) {
        overlay.addEventListener('click', closeAllSidebars);
    }

    function switchMobileTab(tab, element) {
        document.querySelectorAll('.bottom-nav-item').forEach(el => el.classList.remove('active'));
        if (element) {
            element.classList.add('active');
        }

        if (tab === 'map') {
            closeAllSidebars();
        } else if (tab === 'profile') {
            socialSidebar.classList.remove('mobile-open');
            sidebar.classList.add('mobile-open');
            overlay.classList.add('show');
        } else if (tab === 'social') {
            sidebar.classList.remove('mobile-open');
            socialSidebar.classList.add('mobile-open');
            overlay.classList.add('show');
        }
    }

</script>

<style>
    /* Popup styling */
    .leaflet-popup-content-wrapper {
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        padding: 5px;
    }
    .leaflet-popup-tip {
        box-shadow: none;
    }
    .hover-opacity-100:hover {
        opacity: 1 !important;
    }
    .btn-white-glass {
        background: rgba(255,255,255,0.2);
        color: white;
        backdrop-filter: blur(5px);
        border: none;
    }
    .btn-white-glass:hover {
        background: rgba(255,255,255,0.3);
        color: white;
    }
</style>


<script>
    function switchTab(tabId) {
        document.querySelectorAll('.tab-item').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.bottom-nav .nav-item').forEach(el => el.classList.remove('active'));
        
        event.currentTarget.classList.add('active');
        
        document.querySelectorAll('.tab-content-section').forEach(el => el.classList.remove('active'));
        
        const target = document.getElementById('tab-' + tabId);
        if(target) {
            target.classList.add('active');
        }
        
        if (typeof map !== 'undefined') {
            setTimeout(() => { map.invalidateSize(); }, 300);
        }
    }
</script>

</body>
</html>
