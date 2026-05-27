<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Memory AI</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            min-height: 100vh;
            background:
                linear-gradient(135deg, rgba(15,23,42,.88), rgba(49,46,129,.62)),
                url('https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=2200&q=80');
            background-size: cover;
            background-position: center;
        }
        .ai-container { max-width: 980px; height: calc(100vh - 108px); margin: 72px auto 36px; padding: 0 16px; }
        .chat-box {
            overflow: hidden;
            border-radius: 24px;
            background: rgba(255,255,255,0.82);
            border: 1px solid rgba(255,255,255,0.45);
            box-shadow: 0 30px 90px rgba(0,0,0,0.28);
            backdrop-filter: blur(24px);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .chat-header {
            padding: 28px;
            color: white;
            background: linear-gradient(135deg, rgba(79,70,229,.96), rgba(14,165,233,.92));
        }
        .chat-header h1 { font-size: clamp(1.8rem, 4vw, 3rem); font-weight: 800; margin: 0; }
        .status-pill { display: inline-flex; gap: 8px; align-items: center; padding: 7px 12px; border-radius: 999px; background: rgba(255,255,255,.16); }
        .chat-body {
            flex: 1 1 auto;
            min-height: 220px;
            overflow-y: auto;
            overscroll-behavior: contain;
            scroll-behavior: smooth;
            padding: 24px;
            background: rgba(248,251,255,.72);
        }
        .message { display: flex; gap: 14px; margin-bottom: 20px; }
        .message.user { justify-content: flex-end; }
        .avatar { width: 42px; height: 42px; min-width: 42px; border-radius: 50%; display: grid; place-items: center; color: white; font-weight: 800; background: linear-gradient(135deg, #22d3ee, #7c3aed); }
        .message.user .avatar { background: #0f172a; }
        .bubble { max-width: 76%; padding: 15px 17px; border-radius: 18px; background: white; color: #0f172a; line-height: 1.6; white-space: pre-wrap; box-shadow: 0 12px 28px rgba(15,23,42,.08); }
        .message.user .bubble { background: linear-gradient(135deg, #4f46e5, #0ea5e9); color: white; }
        .quick-actions { display: grid; grid-template-columns: repeat(auto-fit,minmax(170px,1fr)); gap: 10px; }
        .quick-actions button { border-radius: 8px; border: 0; background: #eef6ff; color: #0f172a; padding: 12px; font-weight: 800; transition: transform .2s ease, background .2s ease; }
        .quick-actions button:hover { transform: translateY(-2px); background: #dbeafe; }
        .chat-input-area { flex: 0 0 auto; padding: 18px 24px 22px; background: rgba(255,255,255,.92); display: grid; gap: 14px; }
        .chat-input-area textarea { resize: vertical; min-height: 74px; max-height: 150px; border-radius: 16px; border: 1px solid rgba(99,102,241,.22); padding: 16px; font-size: 1rem; background: rgba(248,250,252,.9); }
        .chat-input-actions { display: flex; flex-wrap: wrap; gap: 12px; justify-content: space-between; align-items: center; }
        @media (max-width: 768px) {
            .ai-container { height: calc(100vh - 24px); margin: 12px auto; }
            .chat-header { padding: 20px; }
            .chat-body { min-height: 180px; padding: 18px; }
            .quick-actions { display: flex; overflow-x: auto; padding-bottom: 4px; }
            .quick-actions button { min-width: 150px; }
            .chat-input-area { padding: 14px; }
            .chat-input-actions small { display: none; }
        }
    </style>
</head>
<body>

<div class="ai-container">
    <div class="chat-box">
        <div class="chat-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <div class="status-pill mb-3"><i class="bi bi-stars"></i> AI Companion</div>
                <h1><i class="bi bi-globe-asia-australia me-2"></i>Travel Memory AI</h1>
                <p class="mb-0 opacity-75">Cố vấn du lịch: lịch trình tour, thời tiết, địa điểm đẹp, món ngon, check-in, caption và nhật ký.</p>
            </div>
            <a href="index.php?url=location/dashboard" class="btn btn-light rounded-pill fw-bold px-4"><i class="bi bi-map me-2"></i>Về bản đồ</a>
        </div>

        <div class="chat-body" id="chatBody">
            <div class="message assistant">
                <div class="avatar">AI</div>
                <div class="bubble">Hãy kể mình nơi bạn muốn đi và vibe chuyến đi. Mình có thể lập lịch trình tour, dự báo thời tiết, gợi ý địa điểm đẹp, món ngon, check-in — và viết caption/nhật ký khi bạn cần.</div>
            </div>
        </div>

        <div class="chat-input-area">
            <div class="quick-actions">
                <button type="button" onclick="setQuestion('Lịch trình 3 ngày 2 đêm Đà Nẵng, ưu tiên biển và ẩm thực')">Lịch trình</button>
                <button type="button" onclick="setQuestion('Dự báo thời tiết Hà Nội 5 ngày tới')">Thời tiết</button>
                <button type="button" onclick="setQuestion('Địa điểm nào đẹp ở Hải Dương?')">Địa điểm đẹp</button>
                <button type="button" onclick="setQuestion('Ăn gì ngon ở Đà Lạt? Gợi ý đặc sản')">Món ngon</button>
                <button type="button" onclick="setQuestion('Viết caption chill cho chuyến đi biển')">Caption</button>
            </div>

            <textarea id="assistantQuestion" placeholder="Nhập yêu cầu AI..." rows="3"></textarea>

            <div class="chat-input-actions">
                <small class="text-muted">Có thể dùng vị trí hiện tại để gợi ý địa điểm gần bạn.</small>
                <button type="button" class="btn-ai-send" onclick="sendQuestion()"><i class="bi bi-send-fill me-2"></i>Hỏi AI</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentCoords = { latitude: null, longitude: null };

    function setQuestion(text) {
        document.getElementById('assistantQuestion').value = text;
        document.getElementById('assistantQuestion').focus();
    }

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    function linkify(text) {
        const escaped = escapeHtml(text);
        return escaped.replace(/(https?:\/\/[^\s]+)/g, '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>');
    }

    function appendMessage(role, content) {
        const body = document.getElementById('chatBody');
        const message = document.createElement('div');
        message.className = 'message ' + (role === 'user' ? 'user' : 'assistant');
        message.innerHTML = `<div class="avatar">${role === 'user' ? 'T' : 'AI'}</div><div class="bubble">${linkify(content)}</div>`;
        body.appendChild(message);
        body.scrollTop = body.scrollHeight;
    }

    function sendQuestion() {
        const textarea = document.getElementById('assistantQuestion');
        const question = textarea.value.trim();
        if (!question) return;

        appendMessage('user', question);
        textarea.value = '';

        const loader = document.createElement('div');
        loader.className = 'message assistant';
        loader.innerHTML = '<div class="avatar">AI</div><div class="bubble">Đang phân tích và tư vấn...</div>';
        document.getElementById('chatBody').appendChild(loader);
        document.getElementById('chatBody').scrollTop = document.getElementById('chatBody').scrollHeight;

        fetch('index.php?url=ai/ask', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                question: question,
                latitude: currentCoords.latitude || '',
                longitude: currentCoords.longitude || ''
            })
        })
        .then(res => res.json())
        .then(data => {
            loader.remove();
            appendMessage('assistant', data.success ? data.message : 'Có lỗi: ' + data.message);
        })
        .catch(() => {
            loader.remove();
            appendMessage('assistant', 'Không thể kết nối đến dịch vụ AI. Vui lòng thử lại sau.');
        });
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(pos => {
            currentCoords.latitude = pos.coords.latitude;
            currentCoords.longitude = pos.coords.longitude;
        }, () => {}, { enableHighAccuracy: true, maximumAge: 0, timeout: 20000 });
        navigator.geolocation.watchPosition(pos => {
            currentCoords.latitude = pos.coords.latitude;
            currentCoords.longitude = pos.coords.longitude;
        }, () => {}, { enableHighAccuracy: true, maximumAge: 0 });
    }
</script>

</body>
</html>
