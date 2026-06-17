<!-- Floating Chat Widget -->
<style>
    .floating-chat-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #0ea5e9);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4);
        cursor: pointer;
        z-index: 9999;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .floating-chat-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6);
    }
    .chat-widget-panel {
        position: fixed;
        bottom: 90px;
        right: 20px;
        width: 350px;
        height: 500px;
        max-height: 80vh;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        display: none;
        flex-direction: column;
        z-index: 9998;
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .chat-widget-header {
        background: linear-gradient(135deg, #6366f1, #0ea5e9);
        color: white;
        padding: 15px;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .chat-widget-header .back-btn {
        cursor: pointer;
        display: none;
        margin-right: 10px;
    }
    .chat-widget-body {
        flex: 1;
        overflow-y: auto;
        background: #f8fafc;
        position: relative;
    }
    .friend-list-item {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background 0.2s;
    }
    .friend-list-item:hover {
        background: #f1f5f9;
    }
    .friend-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #cbd5e1;
        margin-right: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        overflow: hidden;
    }
    .friend-avatar img { width: 100%; height: 100%; object-fit: cover; }
    
    .chat-messages-container {
        display: none;
        flex-direction: column;
        height: 100%;
    }
    .chat-messages-list {
        flex: 1;
        overflow-y: auto;
        padding: 15px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .chat-input-area {
        padding: 10px;
        background: #fff;
        border-top: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .chat-input-area input[type="text"] {
        flex: 1;
        border: 1px solid #cbd5e1;
        border-radius: 20px;
        padding: 8px 15px;
        outline: none;
    }
    .chat-bubble {
        max-width: 80%;
        padding: 10px 14px;
        border-radius: 18px;
        font-size: 14px;
        position: relative;
        word-wrap: break-word;
    }
    .chat-message-row { display: flex; margin-bottom: 5px; flex-direction: column;}
    .chat-message-row.mine { align-items: flex-end; }
    .chat-message-row.theirs { align-items: flex-start; }
    .chat-message-row.mine .chat-bubble {
        background: #6366f1;
        color: white;
        border-bottom-right-radius: 4px;
    }
    .chat-message-row.theirs .chat-bubble {
        background: #e2e8f0;
        color: #1e293b;
        border-bottom-left-radius: 4px;
    }
    .chat-reaction {
        position: absolute;
        bottom: -10px;
        right: 0px;
        background: white;
        border-radius: 50%;
        padding: 2px 4px;
        font-size: 12px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        cursor: pointer;
    }
    .chat-message-row.theirs .chat-reaction { left: 0px; right: auto; }
    
    .chat-attachment {
        max-width: 150px;
        border-radius: 12px;
        margin-top: 5px;
        cursor: pointer;
    }
    .reaction-picker {
        position: absolute;
        background: white;
        border-radius: 20px;
        padding: 5px;
        display: flex;
        gap: 5px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        z-index: 100;
        display: none;
    }
    .reaction-picker span {
        cursor: pointer;
        font-size: 18px;
        transition: transform 0.2s;
    }
    .reaction-picker span:hover { transform: scale(1.3); }
    .replied-image {
        max-width: 100px;
        border-radius: 8px;
        opacity: 0.8;
        margin-bottom: 5px;
    }
</style>

<div class="floating-chat-btn" onclick="toggleChatWidget()">
    <i class="bi bi-chat-dots-fill"></i>
</div>

<div class="chat-widget-panel" id="chatWidgetPanel">
    <div class="chat-widget-header">
        <div class="d-flex align-items-center">
            <span class="back-btn" id="chatBackBtn" onclick="showFriendList()"><i class="bi bi-arrow-left"></i></span>
            <span id="chatTitle">Tin nhắn</span>
        </div>
        <i class="bi bi-x-lg" style="cursor: pointer;" onclick="toggleChatWidget()"></i>
    </div>
    
    <div class="chat-widget-body">
        <!-- Danh sách bạn bè -->
        <div id="chatFriendList" style="display: block;">
            <div class="text-center p-4 text-muted small" id="friendListLoading">Đang tải...</div>
        </div>
        
        <!-- Khung chat -->
        <div class="chat-messages-container" id="chatMessagesContainer">
            <div class="chat-messages-list" id="chatMessagesList">
            </div>
            
            <div class="reaction-picker" id="reactionPicker">
                <span onclick="sendReaction('❤️')">❤️</span>
                <span onclick="sendReaction('😂')">😂</span>
                <span onclick="sendReaction('😮')">😮</span>
                <span onclick="sendReaction('😢')">😢</span>
                <span onclick="sendReaction('👍')">👍</span>
            </div>

            <form class="chat-input-area" id="chatForm" onsubmit="sendWidgetMessage(event)">
                <input type="hidden" id="chatActiveFriendId" value="0">
                <label style="cursor: pointer; color: #64748b; margin: 0; padding: 5px;">
                    <i class="bi bi-image"></i>
                    <input type="file" id="chatAttachmentInput" accept="image/*" style="display:none;" onchange="previewAttachment(this)">
                </label>
                <input type="text" id="chatInputText" placeholder="Nhắn tin..." autocomplete="off">
                <button type="submit" class="btn btn-primary rounded-circle" style="width: 36px; height: 36px; padding: 0; display:flex; align-items:center; justify-content:center;"><i class="bi bi-send-fill" style="font-size: 14px;"></i></button>
            </form>
            <div id="chatAttachmentPreview" style="display:none; padding: 5px 10px; background: #f1f5f9; font-size:12px; color:#475569;">
                <i class="bi bi-paperclip"></i> Đã chọn ảnh <span style="cursor:pointer; color:red; margin-left:10px;" onclick="clearAttachment()">[Xóa]</span>
            </div>
        </div>
    </div>
</div>

<script>
    let chatPollInterval = null;
    let selectedMessageIdForReaction = null;

    function toggleChatWidget() {
        const panel = document.getElementById('chatWidgetPanel');
        if (panel.style.display === 'none' || panel.style.display === '') {
            panel.style.display = 'flex';
            showFriendList();
        } else {
            panel.style.display = 'none';
            if (chatPollInterval) clearInterval(chatPollInterval);
        }
    }

    function showFriendList() {
        document.getElementById('chatFriendList').style.display = 'block';
        document.getElementById('chatMessagesContainer').style.display = 'none';
        document.getElementById('chatBackBtn').style.display = 'none';
        document.getElementById('chatTitle').innerText = 'Tin nhắn';
        if (chatPollInterval) clearInterval(chatPollInterval);

        fetch('index.php?url=feed/getFriendsList')
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById('chatFriendList');
                if (!data.success || data.friends.length === 0) {
                    list.innerHTML = '<div class="text-center p-4 text-muted small">Bạn chưa có bạn bè nào.</div>';
                    return;
                }
                list.innerHTML = data.friends.map(f => {
                    const avatar = f.avatar ? `<img src="<?= UPLOADS_URL ?>/avatars/${f.avatar}">` : f.username.substring(0,1).toUpperCase();
                    return `
                        <div class="friend-list-item" onclick="openWidgetChat(${f.id}, '${escapeHtml(f.full_name)}')">
                            <div class="friend-avatar">${avatar}</div>
                            <div style="flex:1">
                                <div class="fw-bold" style="font-size:14px; color:#1e293b;">${escapeHtml(f.full_name)}</div>
                                <div class="text-muted" style="font-size:12px;">@${f.username}</div>
                            </div>
                        </div>
                    `;
                }).join('');
            });
    }

    function openWidgetChat(friendId, friendName) {
        document.getElementById('chatFriendList').style.display = 'none';
        document.getElementById('chatMessagesContainer').style.display = 'flex';
        document.getElementById('chatBackBtn').style.display = 'block';
        document.getElementById('chatTitle').innerText = friendName;
        document.getElementById('chatActiveFriendId').value = friendId;
        
        loadWidgetMessages();
        if (chatPollInterval) clearInterval(chatPollInterval);
        chatPollInterval = setInterval(loadWidgetMessages, 3000);
    }

    function loadWidgetMessages() {
        const friendId = document.getElementById('chatActiveFriendId').value;
        if (!friendId || friendId == 0) return;

        fetch(`index.php?url=feed/getPrivateMessages&friend_id=${friendId}`)
            .then(res => res.json())
            .then(data => {
                const list = document.getElementById('chatMessagesList');
                if (!data.success) return;
                
                let isScrolledToBottom = list.scrollHeight - list.clientHeight <= list.scrollTop + 50;

                list.innerHTML = data.messages.map(m => {
                    const mine = Number(m.sender_id) !== Number(friendId);
                    const attachmentHtml = m.attachment ? `<img src="<?= UPLOADS_URL ?>/${m.attachment}" class="chat-attachment" onclick="window.open('<?= UPLOADS_URL ?>/${m.attachment}', '_blank')">` : '';
                    const repliedHtml = m.replied_image_path ? `<img src="<?= UPLOADS_URL ?>/${m.replied_image_path}" class="replied-image"><br>` : '';
                    const reactionHtml = m.reaction ? `<div class="chat-reaction">${m.reaction}</div>` : '';
                    
                    return `
                        <div class="chat-message-row ${mine ? 'mine' : 'theirs'}">
                            <div class="chat-bubble" ondblclick="showReactionPicker(event, ${m.id})">
                                ${repliedHtml}
                                ${attachmentHtml}
                                ${m.message ? `<div>${escapeHtml(m.message)}</div>` : ''}
                                ${reactionHtml}
                            </div>
                        </div>
                    `;
                }).join('');

                if (isScrolledToBottom) {
                    list.scrollTop = list.scrollHeight;
                }
            });
    }

    function previewAttachment(input) {
        if (input.files && input.files[0]) {
            document.getElementById('chatAttachmentPreview').style.display = 'block';
        }
    }
    
    function clearAttachment() {
        document.getElementById('chatAttachmentInput').value = '';
        document.getElementById('chatAttachmentPreview').style.display = 'none';
    }

    function sendWidgetMessage(e) {
        e.preventDefault();
        const friendId = document.getElementById('chatActiveFriendId').value;
        const input = document.getElementById('chatInputText');
        const fileInput = document.getElementById('chatAttachmentInput');
        
        const text = input.value.trim();
        const file = fileInput.files[0];
        
        if (!text && !file) return;

        const formData = new FormData();
        formData.append('receiver_id', friendId);
        formData.append('message', text);
        if (file) formData.append('attachment', file);

        input.value = '';
        clearAttachment();

        fetch('index.php?url=feed/sendPrivateMessage', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    loadWidgetMessages();
                    setTimeout(() => {
                        const list = document.getElementById('chatMessagesList');
                        list.scrollTop = list.scrollHeight;
                    }, 100);
                } else {
                    alert(data.message);
                }
            });
    }

    function showReactionPicker(e, messageId) {
        selectedMessageIdForReaction = messageId;
        const picker = document.getElementById('reactionPicker');
        picker.style.display = 'flex';
        
        const rect = e.target.getBoundingClientRect();
        const panelRect = document.getElementById('chatWidgetPanel').getBoundingClientRect();
        
        picker.style.top = (rect.top - panelRect.top - 30) + 'px';
        picker.style.left = (rect.left - panelRect.left + 10) + 'px';

        // Hide if click outside
        const hidePicker = function(evt) {
            if (!picker.contains(evt.target)) {
                picker.style.display = 'none';
                document.removeEventListener('click', hidePicker);
            }
        };
        setTimeout(() => document.addEventListener('click', hidePicker), 50);
    }

    function sendReaction(icon) {
        if (!selectedMessageIdForReaction) return;
        
        const formData = new FormData();
        formData.append('message_id', selectedMessageIdForReaction);
        formData.append('reaction', icon);

        fetch('index.php?url=feed/reactToMessage', { method: 'POST', body: formData })
            .then(() => {
                document.getElementById('reactionPicker').style.display = 'none';
                loadWidgetMessages();
            });
    }
    
    function sendImageToChat(friendId, imageId) {
        const formData = new FormData();
        formData.append('receiver_id', friendId);
        formData.append('message', 'Gửi một ảnh từ bảng tin');
        formData.append('reply_to_image_id', imageId);

        fetch('index.php?url=feed/sendPrivateMessage', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert("Đã gửi ảnh qua tin nhắn!");
                    toggleChatWidget();
                    openWidgetChat(friendId, "Bạn bè");
                }
            });
    }
</script>
