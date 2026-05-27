<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Album - <?php echo $location['place_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body { background: #f8fafc; }
        .manage-container { max-width: 1000px; margin: 50px auto; padding: 20px; }
        .album-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 20px; }
        .album-item { 
            background: white; 
            border-radius: 20px; 
            overflow: hidden; 
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
            position: relative;
        }
        .album-item:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .media-preview { width: 100%; height: 200px; object-fit: cover; background: #000; }
        .item-actions { padding: 15px; display: flex; gap: 10px; justify-content: center; }
        .btn-action { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; border: none; transition: all 0.2s; }
        .btn-delete { background: #fee2e2; color: #ef4444; }
        .btn-delete:hover { background: #ef4444; color: white; }
        .btn-featured { background: #f0f7ff; color: #3b82f6; }
        .btn-featured.active { background: #3b82f6; color: white; }
        .featured-badge { 
            position: absolute; 
            top: 10px; 
            left: 10px; 
            background: #3b82f6; 
            color: white; 
            padding: 4px 12px; 
            border-radius: 10px; 
            font-size: 11px; 
            font-weight: 600;
            z-index: 10;
        }
    </style>
</head>
<body>

<div class="manage-container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="index.php?url=location/dashboard" class="text-decoration-none small fw-bold text-primary d-block mb-1">
                <i class="bi bi-arrow-left"></i> Quay lại Dashboard
            </a>
            <h3 class="fw-bold mb-0">Quản lý Album: <?php echo $location['place_name']; ?></h3>
        </div>
        <form action="index.php?url=location/update" method="POST" enctype="multipart/form-data" id="addMoreForm" class="d-flex flex-column align-items-end gap-2">
            <input type="hidden" name="id" value="<?php echo $location['id']; ?>">
            <input type="hidden" name="place_name" value="<?php echo $location['place_name']; ?>">
            <input type="hidden" name="visit_date" value="<?php echo $location['visit_date']; ?>">
            <input type="hidden" name="feeling" value="<?php echo $location['feeling']; ?>">
            <input type="hidden" name="description" value="<?php echo $location['description']; ?>">

            <div class="d-flex gap-2 align-items-center">
                <div class="me-2">
                    <label for="albumPrivacy" class="form-label small fw-bold mb-1">Quyền riêng tư</label>
                    <select name="privacy" id="albumPrivacy" class="form-select form-select-sm" onchange="toggleSpecificFriends('album')">
                        <option value="public" <?php echo ($location['privacy'] === 'public') ? 'selected' : ''; ?>>🌐 Công khai</option>
                        <option value="friends" <?php echo ($location['privacy'] === 'friends') ? 'selected' : ''; ?>>👥 Bạn bè</option>
                        <option value="specific_friends" <?php echo ($location['privacy'] === 'specific_friends') ? 'selected' : ''; ?>>👥 Bạn bè cụ thể</option>
                        <option value="private" <?php echo ($location['privacy'] === 'private') ? 'selected' : ''; ?>>🔒 Chỉ mình tôi</option>
                    </select>
                </div>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" onclick="document.getElementById('addMoreInput').click()">
                    <i class="bi bi-plus-lg me-2"></i> Thêm ảnh/video
                </button>
            </div>

            <div class="mb-3 w-100" id="albumSpecificFriendsContainer" style="display: none; max-height: 150px; overflow-y: auto; background: #f8fafc; padding: 12px; border-radius: 16px; border: 1px solid #e2e8f0;">
                <label class="form-label small fw-bold mb-2">Chọn bạn bè cụ thể</label>
                <?php $selectedFriends = json_decode($location['visible_friends'] ?? '[]', true) ?: []; ?>
                <?php if(!empty($friends)): ?>
                    <div class="row g-2">
                        <?php foreach($friends as $f): ?>
                            <div class="col-6">
                                <label class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="visible_friends[]" value="<?php echo $f['id']; ?>" id="album_friend_<?php echo $f['id']; ?>" <?php echo in_array($f['id'], $selectedFriends) ? 'checked' : ''; ?>>
                                    <span class="form-check-label small"><?php echo htmlspecialchars($f['full_name']); ?></span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-muted small">Bạn chưa có bạn bè nào để chọn.</div>
                <?php endif; ?>
            </div>

            <input type="file" name="new_images[]" id="addMoreInput" multiple onchange="this.form.submit()" style="display:none;">
        </form>
    </div>

    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success border-0 rounded-4 mb-4">
            <i class="bi bi-check-circle-fill me-2"></i> 
            <?php 
                if($_GET['success'] == 'deleted') echo "Đã xóa tệp khỏi album.";
                else if($_GET['success'] == 'featured') echo "Đã cập nhật ảnh đại diện mới.";
            ?>
        </div>
    <?php endif; ?>

    <div class="album-grid">
        <?php foreach($album as $item): 
            $ext = strtolower(pathinfo($item['image_path'], PATHINFO_EXTENSION));
            $isVideo = in_array($ext, ['mp4', 'webm', 'ogg', 'mov']);
        ?>
            <div class="album-item animate-fade-in">
                <?php if($item['is_featured']): ?>
                    <div class="featured-badge">Ảnh đại diện</div>
                <?php endif; ?>

                <?php if($isVideo): ?>
                    <video class="media-preview">
                        <source src="../uploads/<?php echo $item['image_path']; ?>" type="video/mp4">
                    </video>
                    <div class="position-absolute top-50 start-50 translate-middle text-white opacity-75">
                        <i class="bi bi-play-circle-fill display-6"></i>
                    </div>
                <?php else: ?>
                    <img src="../uploads/<?php echo $item['image_path']; ?>" class="media-preview">
                <?php endif; ?>

                <div class="item-actions">
                    <a href="index.php?url=location/setFeatured&id=<?php echo $item['id']; ?>&location_id=<?php echo $location['id']; ?>" 
                       class="btn-action btn-featured <?php echo $item['is_featured'] ? 'active' : ''; ?>" 
                       title="Đặt làm ảnh đại diện">
                        <i class="bi bi-star-fill"></i>
                    </a>
                    <a href="index.php?url=location/deleteAlbumImage&id=<?php echo $item['id']; ?>&location_id=<?php echo $location['id']; ?>" 
                       class="btn-action btn-delete" 
                       onclick="return confirm('Bạn có chắc muốn xóa tệp này?')"
                       title="Xóa khỏi album">
                        <i class="bi bi-trash-fill"></i>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
    function toggleSpecificFriends(mode) {
        const privacySelect = document.getElementById('albumPrivacy');
        const container = document.getElementById('albumSpecificFriendsContainer');
        if (privacySelect && container) {
            if (privacySelect.value === 'specific_friends') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        toggleSpecificFriends('album');
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
