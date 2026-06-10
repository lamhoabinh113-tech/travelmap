<?php
/**
 * Location Controller
 * Xử lý các yêu cầu liên quan đến bản đồ và địa điểm
 */

require_once '../app/models/LocationModel.php';

class LocationController {
    private $db;
    private $locationModel;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/login");
            exit();
        }
        file_put_contents(__DIR__ . '/dashboard_debug.txt', "Dashboard Access: user_id=" . $_SESSION['user_id'] . ", full_name=" . $_SESSION['full_name'] . "\n", FILE_APPEND);
        $database = new Database();
        $this->db = $database->getConnection();
        $this->locationModel = new LocationModel($this->db);
    }

    // Hiển thị Dashboard
    public function index() {
        $this->dashboard();
    }

    public function dashboard() {
        $user_id = $_SESSION['user_id'];
        $current_trip = null;
        if (isset($_GET['trip_id']) && is_numeric($_GET['trip_id'])) {
            $trip_id = $_GET['trip_id'];
            $locations = $this->locationModel->getAllByTripId($trip_id, $user_id);
            
            require_once '../app/models/TripModel.php';
            $tm = new TripModel($this->db);
            $tripsList = $tm->getByUser($user_id);
            foreach($tripsList as $t) {
                if ($t['id'] == $trip_id) { $current_trip = $t; break; }
            }
        } else {
            $locations = $this->locationModel->getTimelineLocations($user_id);
        }
        
        // Lấy danh sách bạn bè
        $query = "SELECT u.id, u.full_name, u.username FROM users u 
                  JOIN friendships f ON (u.id = f.friend_id OR u.id = f.user_id) 
                  WHERE (f.user_id = :uid OR f.friend_id = :uid) AND u.id != :uid AND f.status = 'accepted'";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':uid' => $user_id]);
        $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Lấy hành trình của bạn bè để hiển thị ở sidebar
        $friend_locations = $this->locationModel->getFriendLocations($user_id);

        require_once '../app/models/TripModel.php';
        $tripModel = new TripModel($this->db);
        $trips = $tripModel->getByUser($user_id);

        $trip_members_data = [];
        $trip_photos_data = [];

        if (!empty($trips)) {
            $trip_ids = array_column($trips, 'id');
            $placeholders = implode(',', array_fill(0, count($trip_ids), '?'));
            
            // Lấy thành viên của chuyến đi (bao gồm chủ chuyến đi và các thành viên được mời)
            $q_mem = "
                SELECT tm.trip_id, u.id as user_id, u.username, u.full_name, u.avatar 
                FROM trip_members tm
                JOIN users u ON tm.user_id = u.id
                WHERE tm.trip_id IN ($placeholders)
                UNION
                SELECT t.id as trip_id, u.id as user_id, u.username, u.full_name, u.avatar 
                FROM trips t
                JOIN users u ON t.user_id = u.id
                WHERE t.id IN ($placeholders)
            ";
            $s_mem = $this->db->prepare($q_mem);
            $params = array_merge($trip_ids, $trip_ids);
            $s_mem->execute($params);
            $members_list = $s_mem->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($members_list as $m) {
                $trip_members_data[$m['trip_id']][$m['user_id']] = $m;
            }

            // Lấy tất cả ảnh của từng chuyến đi (có lọc quyền riêng tư)
            foreach ($trips as &$t) {
                $q_photos = "
                    SELECT DISTINCT image_path, place_name, feeling, location_id FROM (
                        SELECT image as image_path, place_name, feeling, id as location_id, visit_date, id FROM locations 
                        WHERE trip_id = :trip_id AND image IS NOT NULL AND image != ''
                          AND (
                              user_id = :uid
                              OR privacy = 'public'
                              OR (
                                  privacy = 'friends' 
                                  AND EXISTS (
                                      SELECT 1 FROM friendships f 
                                      WHERE ((f.user_id = :uid AND f.friend_id = locations.user_id) OR (f.user_id = locations.user_id AND f.friend_id = :uid))
                                        AND f.status = 'accepted'
                                  )
                              )
                              OR (
                                  privacy = 'specific_friends'
                                  AND (
                                      visible_friends LIKE CONCAT('%\"', :uid, '\"%')
                                      OR visible_friends LIKE CONCAT('%', :uid, '%')
                                  )
                              )
                          )
                        UNION
                        SELECT li.image_path, l.place_name, l.feeling, l.id as location_id, l.visit_date, li.id FROM location_images li 
                        JOIN locations l ON li.location_id = l.id 
                        WHERE l.trip_id = :trip_id AND li.image_path IS NOT NULL AND li.image_path != ''
                          AND (
                              l.user_id = :uid
                              OR l.privacy = 'public'
                              OR (
                                  l.privacy = 'friends' 
                                  AND EXISTS (
                                      SELECT 1 FROM friendships f 
                                      WHERE ((f.user_id = :uid AND f.friend_id = l.user_id) OR (f.user_id = l.user_id AND f.friend_id = :uid))
                                        AND f.status = 'accepted'
                                  )
                              )
                              OR (
                                  l.privacy = 'specific_friends'
                                  AND (
                                      l.visible_friends LIKE CONCAT('%\"', :uid, '\"%')
                                      OR l.visible_friends LIKE CONCAT('%', :uid, '%')
                                  )
                              )
                          )
                    ) AS combined_images
                    ORDER BY visit_date DESC, id DESC
                ";
                $s_photos = $this->db->prepare($q_photos);
                $s_photos->execute([
                    ':trip_id' => $t['id'],
                    ':uid' => $user_id
                ]);
                $t['photos'] = $s_photos->fetchAll(PDO::FETCH_ASSOC);
                $trip_photos_data[$t['id']] = $t['photos'];
            }
            unset($t);
        }

        // Lấy thông tin XP và Danh hiệu
        $q_user = "SELECT xp FROM users WHERE id = :uid";
        $s_user = $this->db->prepare($q_user);
        $s_user->execute([':uid' => $user_id]);
        $user_data = $s_user->fetch(PDO::FETCH_ASSOC);
        $user_xp = $user_data['xp'] ?? 0;
        
        $badge_name = "Explorer Lv.1";
        if ($user_xp >= 1000) $badge_name = "👑 Thánh Check-in";
        elseif ($user_xp >= 500) $badge_name = "🗺️ Kẻ lang thang";
        elseif ($user_xp >= 100) $badge_name = "🎒 Tân binh xê dịch";

        require_once '../app/views/location/dashboard.php';
    }

    // Lấy JSON album cho frontend
    public function getAlbum() {
        if (isset($_GET['id'])) {
            $album = $this->locationModel->getAlbum($_GET['id']);
            echo json_encode($album);
            exit();
        }
    }

    // Lấy JSON tất cả ảnh trong chuyến đi (AJAX)
    public function getTripPhotos() {
        if (isset($_GET['trip_id'])) {
            $trip_id = intval($_GET['trip_id']);
            $user_id = $_SESSION['user_id'];

            $q = "
                SELECT DISTINCT image_path, visit_date, id FROM (
                    SELECT image as image_path, visit_date, id FROM locations 
                    WHERE trip_id = :trip_id AND image IS NOT NULL AND image != ''
                      AND (
                          user_id = :uid
                          OR privacy = 'public'
                          OR (
                              privacy = 'friends' 
                              AND EXISTS (
                                  SELECT 1 FROM friendships f 
                                  WHERE ((f.user_id = :uid AND f.friend_id = locations.user_id) OR (f.user_id = locations.user_id AND f.friend_id = :uid))
                                    AND f.status = 'accepted'
                              )
                          )
                          OR (
                              privacy = 'specific_friends'
                              AND (
                                  visible_friends LIKE CONCAT('%\"', :uid, '\"%')
                                  OR visible_friends LIKE CONCAT('%', :uid, '%')
                              )
                          )
                      )
                    UNION
                    SELECT li.image_path, l.visit_date, li.id FROM location_images li 
                    JOIN locations l ON li.location_id = l.id 
                    WHERE l.trip_id = :trip_id AND li.image_path IS NOT NULL AND li.image_path != ''
                      AND (
                          l.user_id = :uid
                          OR l.privacy = 'public'
                          OR (
                              l.privacy = 'friends' 
                              AND EXISTS (
                                  SELECT 1 FROM friendships f 
                                  WHERE ((f.user_id = :uid AND f.friend_id = l.user_id) OR (f.user_id = l.user_id AND f.friend_id = :uid))
                                    AND f.status = 'accepted'
                              )
                          )
                          OR (
                              l.privacy = 'specific_friends'
                              AND (
                                  l.visible_friends LIKE CONCAT('%\"', :uid, '\"%')
                                  OR l.visible_friends LIKE CONCAT('%', :uid, '%')
                              )
                          )
                      )
                ) AS combined_images
                ORDER BY visit_date DESC, id DESC
            ";
            
            $stmt = $this->db->prepare($q);
            $stmt->execute([
                ':trip_id' => $trip_id,
                ':uid' => $user_id
            ]);
            $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($photos);
            exit();
        }
    }

    // Hiển thị thống kê
    public function stats() {
        $user_id = $_SESSION['user_id'];
        if (isset($_GET['trip_id']) && is_numeric($_GET['trip_id'])) {
            $trip_id = $_GET['trip_id'];
            $locations = $this->locationModel->getAllByTripId($trip_id, $user_id);
        } else {
            $locations = $this->locationModel->getAllByUserId($user_id);
        }
        require_once '../app/models/TripModel.php';
        $tripModel = new TripModel($this->db);
        $trips = $tripModel->getByUser($user_id);
        
        require_once '../app/views/location/stats.php';
    }

    // Upload ảnh đại diện người dùng
    public function uploadAvatar() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_FILES['avatar_file']) || $_FILES['avatar_file']['error'] !== UPLOAD_ERR_OK) {
                header('Location: index.php?url=location/dashboard&avatar_error=' . urlencode('Vui lòng chọn ảnh đại diện.'));
                exit();
            }

            $file = $_FILES['avatar_file'];
            $validTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($fileInfo, $file['tmp_name']);
            finfo_close($fileInfo);

            if (!in_array($mimeType, $validTypes)) {
                header('Location: index.php?url=location/dashboard&avatar_error=' . urlencode('Chỉ chấp nhận ảnh JPG, PNG, WEBP hoặc GIF.'));
                exit();
            }

            $target_dir = '../uploads/avatars/';
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $safeName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . strtolower($extension);
            $targetPath = $target_dir . $safeName;

            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                header('Location: index.php?url=location/dashboard&avatar_error=' . urlencode('Không thể lưu ảnh. Vui lòng thử lại.'));
                exit();
            }

            // Cập nhật đường dẫn avatar vào cơ sở dữ liệu để lưu trữ vĩnh viễn
            try {
                $stmt = $this->db->prepare("UPDATE users SET avatar = :avatar WHERE id = :id");
                $stmt->execute([
                    ':avatar' => $safeName,
                    ':id'     => $_SESSION['user_id']
                ]);
            } catch (Exception $e) {
                // Bỏ qua hoặc log lỗi nếu có
            }

            // Lưu đường dẫn avatar vào session để hiển thị ngay
            $_SESSION['avatar'] = UPLOADS_URL . '/avatars/' . $safeName;
            
            session_write_close();
            header('Location: index.php?url=location/dashboard&avatar_success=1');
            exit();
        }
        header('Location: index.php?url=location/dashboard');
        exit();
    }

    // Thêm tin nhắn cho 1 ảnh trong album (AJAX)
    public function postImageMessage() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid method']);
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $image_id = isset($_POST['image_id']) ? intval($_POST['image_id']) : 0;
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';

        if ($image_id <= 0 || $message === '') {
            echo json_encode(['success' => false, 'message' => 'Thiếu dữ liệu']);
            exit();
        }

        // Kiểm tra quyền: ảnh thuộc location công khai / bạn bè / hoặc ảnh của mình / bạn bè được phép
        $q = "SELECT li.id as img_id, l.user_id as owner_id, l.privacy, l.visible_friends
              FROM location_images li
              JOIN locations l ON li.location_id = l.id
              WHERE li.id = :img_id";
        $stmt = $this->db->prepare($q);
        $stmt->execute([':img_id' => $image_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            echo json_encode(['success' => false, 'message' => 'Ảnh không tồn tại']);
            exit();
        }

        $owner_id = $row['owner_id'];
        $privacy = $row['privacy'] ?? 'public';

        $allowed = false;
        if ($owner_id == $user_id) $allowed = true;
        elseif ($privacy === 'public') $allowed = true;
        elseif ($privacy === 'friends') {
            // kiểm tra có phải bạn bè
            $qf = "SELECT 1 FROM friendships f WHERE ((f.user_id = :u AND f.friend_id = :o) OR (f.user_id = :o AND f.friend_id = :u)) AND f.status = 'accepted'";
            $s = $this->db->prepare($qf);
            $s->execute([':u' => $user_id, ':o' => $owner_id]);
            if ($s->fetch()) $allowed = true;
        } elseif ($privacy === 'specific_friends') {
            if ($row['visible_friends']) {
                $vf = $row['visible_friends'];
                if (strpos($vf, (string)$user_id) !== false) $allowed = true;
            }
        }

        if (!$allowed) {
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền bình luận ảnh này']);
            exit();
        }

        // Lưu tin nhắn
        $ins = "INSERT INTO image_messages (image_id, sender_id, message, created_at) VALUES (:img, :sid, :msg, NOW())";
        $si = $this->db->prepare($ins);
        $ok = $si->execute([':img' => $image_id, ':sid' => $user_id, ':msg' => $message]);

        if ($ok) {
            // Optionally: tạo notification cho chủ ảnh nếu khác người gửi và bảng notifications tồn tại
            if ($owner_id != $user_id) {
                try {
                    $nq = "INSERT INTO notifications (user_id, actor_id, type, reference_id, message, created_at, is_read) VALUES (:uid, :actor, 'image_message', :ref, :msg, NOW(), 0)";
                    $ns = $this->db->prepare($nq);
                    $ns->execute([':uid' => $owner_id, ':actor' => $user_id, ':ref' => $image_id, ':msg' => $message]);
                } catch (Exception $e) {
                    // Ignore if notifications table doesn't exist
                }
            }

            echo json_encode(['success' => true, 'message' => 'Đã gửi tin nhắn']);
            exit();
        }

        echo json_encode(['success' => false, 'message' => 'Lỗi khi lưu tin nhắn']);
        exit();
    }

    // Lấy tin nhắn cho 1 ảnh (AJAX)
    public function getImageMessages() {
        if (!isset($_GET['image_id'])) {
            echo json_encode(['success' => false, 'message' => 'Thiếu image_id']);
            exit();
        }
        $image_id = intval($_GET['image_id']);

        $q = "SELECT im.id, im.message, im.created_at, im.sender_id, u.full_name, u.username, u.avatar as user_avatar
              FROM image_messages im
              JOIN users u ON im.sender_id = u.id
              WHERE im.image_id = :img
              ORDER BY im.created_at DESC
              LIMIT 20";
        $s = $this->db->prepare($q);
        $s->execute([':img' => $image_id]);
        $rows = $s->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'messages' => $rows]);
        exit();
    }

    // Lưu địa điểm
    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->locationModel->user_id = $_SESSION['user_id'];
            $this->locationModel->place_name = $_POST['place_name'];
            $this->locationModel->latitude = $_POST['latitude'];
            $this->locationModel->longitude = $_POST['longitude'];
            $this->locationModel->description = $_POST['description'];
            $this->locationModel->feeling = $_POST['feeling'];
            $this->locationModel->visit_date = $_POST['visit_date'];
            $this->locationModel->privacy = isset($_POST['privacy']) ? $_POST['privacy'] : 'public';
            $this->locationModel->trip_id = !empty($_POST['trip_id']) ? intval($_POST['trip_id']) : null;
            
            $visible_friends = null;
            if ($this->locationModel->privacy === 'specific_friends' && isset($_POST['visible_friends'])) {
                $visible_friends = json_encode(array_map('intval', $_POST['visible_friends']));
            }
            $this->locationModel->visible_friends = $visible_friends;

            // Xử lý upload nhiều ảnh
            $target_dir = "../uploads/";
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $featured_image = "";
            $uploaded_images = [];

            if (isset($_FILES['images'])) {
                $total_files = count($_FILES['images']['name']);
                for ($i = 0; $i < $total_files; $i++) {
                    if ($_FILES['images']['error'][$i] == 0) {
                        $ext = pathinfo($_FILES["images"]["name"][$i], PATHINFO_EXTENSION);
                        if (empty($ext)) {
                            $ext = 'jpg';
                        }
                        $new_name = time() . "_" . $i . "_" . md5(uniqid(rand(), true)) . "." . $ext;
                        if (move_uploaded_file($_FILES["images"]["tmp_name"][$i], $target_dir . $new_name)) {
                            $uploaded_images[] = $new_name;
                            if ($featured_image == "") {
                                $featured_image = $new_name;
                            }
                        }
                    }
                }
            }

            $this->locationModel->image = $featured_image;

            $database = new Database();
            $conn = $database->getConnection();
            $conn->beginTransaction();

            try {
                $query = "INSERT INTO locations (user_id, trip_id, place_name, latitude, longitude, description, feeling, image, visit_date, privacy, visible_friends, created_at) 
                          VALUES (:user_id, :trip_id, :place_name, :latitude, :longitude, :description, :feeling, :image, :visit_date, :privacy, :visible_friends, NOW())";
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    ':user_id' => $this->locationModel->user_id,
                    ':trip_id' => $this->locationModel->trip_id,
                    ':place_name' => $this->locationModel->place_name,
                    ':latitude' => $this->locationModel->latitude,
                    ':longitude' => $this->locationModel->longitude,
                    ':description' => $this->locationModel->description,
                    ':feeling' => $this->locationModel->feeling,
                    ':image' => $this->locationModel->image,
                    ':visit_date' => $this->locationModel->visit_date,
                    ':privacy' => $this->locationModel->privacy,
                    ':visible_friends' => $this->locationModel->visible_friends
                ]);

                $location_id = $conn->lastInsertId();

                // Cập nhật XP (+20 XP)
                $xp_query = "UPDATE users SET xp = xp + 20 WHERE id = :user_id";
                $xp_stmt = $conn->prepare($xp_query);
                $xp_stmt->execute([':user_id' => $this->locationModel->user_id]);

                // Lưu vào bảng album
                foreach ($uploaded_images as $idx => $img) {
                    $is_feat = ($img == $featured_image) ? 1 : 0;
                    $q_img = "INSERT INTO location_images (location_id, image_path, is_featured) VALUES (:loc_id, :path, :feat)";
                    $s_img = $conn->prepare($q_img);
                    $s_img->execute([':loc_id' => $location_id, ':path' => $img, ':feat' => $is_feat]);
                }

                $conn->commit();
                $is_ajax = isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
                if ($is_ajax) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Lưu kỷ niệm thành công!',
                        'new_id' => $location_id,
                        'latitude' => $this->locationModel->latitude,
                        'longitude' => $this->locationModel->longitude
                    ]);
                    exit();
                }
                header("Location: index.php?url=location/dashboard&success=1&new_id=" . $location_id . "&lat=" . $this->locationModel->latitude . "&lng=" . $this->locationModel->longitude);
            } catch (Exception $e) {
                $conn->rollBack();
                $is_ajax = isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
                if ($is_ajax) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Lỗi: ' . $e->getMessage()
                    ]);
                    exit();
                }
                header("Location: index.php?url=location/dashboard&error=1");
            }
            exit();
        }
    }

    // Cập nhật địa điểm
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $location_id = $_POST['id'];
            $user_id = $_SESSION['user_id'];
            
            $this->locationModel->id = $location_id;
            $this->locationModel->user_id = $user_id;
            $this->locationModel->place_name = $_POST['place_name'];
            $this->locationModel->description = $_POST['description'];
            $this->locationModel->feeling = $_POST['feeling'];
            $this->locationModel->visit_date = $_POST['visit_date'];
            $this->locationModel->privacy = isset($_POST['privacy']) ? $_POST['privacy'] : 'public';
            $this->locationModel->trip_id = !empty($_POST['trip_id']) ? intval($_POST['trip_id']) : null;
            
            $visible_friends = null;
            if ($this->locationModel->privacy === 'specific_friends' && isset($_POST['visible_friends'])) {
                $visible_friends = json_encode(array_map('intval', $_POST['visible_friends']));
            }
            $this->locationModel->visible_friends = $visible_friends;
            $this->locationModel->image = ""; // Không thay đổi ảnh đại diện qua biến này

            // Xử lý upload thêm ảnh/video mới vào album
            $target_dir = "../uploads/";
            $uploaded_images = [];

            if (isset($_FILES['new_images'])) {
                $total_files = count($_FILES['new_images']['name']);
                for ($i = 0; $i < $total_files; $i++) {
                    if ($_FILES['new_images']['error'][$i] == 0) {
                        $ext = pathinfo($_FILES["new_images"]["name"][$i], PATHINFO_EXTENSION);
                        if (empty($ext)) {
                            $ext = 'jpg';
                        }
                        $new_name = time() . "_add_" . $i . "_" . md5(uniqid(rand(), true)) . "." . $ext;
                        if (move_uploaded_file($_FILES["new_images"]["tmp_name"][$i], $target_dir . $new_name)) {
                            $uploaded_images[] = $new_name;
                        }
                    }
                }
            }

            // Khi người dùng cập nhật album/ảnh, ta nâng thời gian cập nhật để bài đăng được đẩy lên bảng tin
            $this->locationModel->touchCreatedAt = true;

            $this->db->beginTransaction();

            try {
                // 1. Cập nhật thông tin cơ bản và trạng thái quyền riêng tư
                $this->locationModel->update();

                // 2. Thêm ảnh/video mới vào bảng location_images
                foreach ($uploaded_images as $img) {
                    $q_img = "INSERT INTO location_images (location_id, image_path) VALUES (:loc_id, :path)";
                    $s_img = $this->db->prepare($q_img);
                    $s_img->execute([':loc_id' => $location_id, ':path' => $img]);
                }

                $this->db->commit();
                $is_ajax = isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
                if ($is_ajax) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Cập nhật kỷ niệm thành công!',
                        'location_id' => $location_id
                    ]);
                    exit();
                }
                header("Location: index.php?url=location/dashboard&update_success=1");
            } catch (Exception $e) {
                $this->db->rollBack();
                $is_ajax = isset($_POST['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
                if ($is_ajax) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Lỗi: ' . $e->getMessage()
                    ]);
                    exit();
                }
                header("Location: index.php?url=location/dashboard&error=update_failed");
            }
            exit();
        }
    }

    // Xóa địa điểm
    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            // Xóa địa điểm và lấy danh sách ảnh album
            $images = $this->locationModel->delete($id, $_SESSION['user_id']);
            
            if ($images) {
                foreach ($images as $img) {
                    $path = "../uploads/" . $img['image_path'];
                    if (file_exists($path)) {
                        unlink($path);
                    }
                }
            }
        }
        $is_ajax = isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
        if ($is_ajax) {
            echo json_encode(['success' => true, 'message' => 'Xóa kỷ niệm thành công!']);
            exit();
        }
        header("Location: index.php?url=location/dashboard");
        exit();
    }

    // Lưu ảnh tức thì (Locket Feature)
    public function saveLocket() {
        header('Content-Type: application/json');
        
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || empty($data['image'])) {
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $base64_image = $data['image'];
        $caption = isset($data['caption']) ? $data['caption'] : '';
        $lat = isset($data['lat']) ? $data['lat'] : 0;
        $lng = isset($data['lng']) ? $data['lng'] : 0;
        $privacy = isset($data['privacy']) ? $data['privacy'] : 'friends';
        $visible_friends = null;
        if ($privacy === 'specific_friends' && isset($data['visible_friends'])) {
            $visible_friends = json_encode(array_map('intval', $data['visible_friends']));
        }
        $album_name = isset($data['album_name']) && trim($data['album_name']) !== '' ? trim($data['album_name']) : "Nhật ký - " . date('d/m/Y');
        $trip_id = isset($data['trip_id']) && is_numeric($data['trip_id']) ? intval($data['trip_id']) : null;

        // Xử lý base64 image
        if (preg_match('/^data:image\/(\w+);base64,/', $base64_image, $type)) {
            $data_img = substr($base64_image, strpos($base64_image, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif
            if (!in_array($type, [ 'jpg', 'jpeg', 'gif', 'png' ])) {
                echo json_encode(['success' => false, 'message' => 'Định dạng ảnh không hợp lệ']);
                exit();
            }
            $data_img = base64_decode($data_img);
            if ($data_img === false) {
                echo json_encode(['success' => false, 'message' => 'Lỗi giải mã ảnh']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi base64']);
            exit();
        }

        $file_name = time() . "_locket_" . $user_id . ".jpg"; // Force save as jpg
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        if (!file_put_contents($target_dir . $file_name, $data_img)) {
            echo json_encode(['success' => false, 'message' => 'Không thể lưu file ảnh']);
            exit();
        }

        $this->db->beginTransaction();
        try {
            // Xem có location nào tạo hôm nay với tên này không, nếu muốn gom nhóm
            // Nhưng hiện tại mỗi cái đăng mới ta tạo 1 marker cho Locket-style.
            $query = "INSERT INTO locations (user_id, place_name, latitude, longitude, description, feeling, image, visit_date, privacy, visible_friends, trip_id, created_at) 
                      VALUES (:uid, :pname, :lat, :lng, :desc, 'Hạnh phúc', :img, CURDATE(), :privacy, :visible_friends, :trip_id, NOW())";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':uid' => $user_id,
                ':pname' => $album_name,
                ':lat' => $lat,
                ':lng' => $lng,
                ':desc' => $caption,
                ':img' => $file_name,
                ':privacy' => $privacy,
                ':visible_friends' => $visible_friends,
                ':trip_id' => $trip_id
            ]);
            $location_id = $this->db->lastInsertId();

            // Lưu vào location_images
            $q_img = "INSERT INTO location_images (location_id, image_path, is_featured) VALUES (:loc_id, :path, 1)";
            $s_img = $this->db->prepare($q_img);
            $s_img->execute([':loc_id' => $location_id, ':path' => $file_name]);

            // Cập nhật XP (+20 XP)
            $xp_query = "UPDATE users SET xp = xp + 20 WHERE id = :user_id";
            $xp_stmt = $this->db->prepare($xp_query);
            $xp_stmt->execute([':user_id' => $user_id]);

            $this->db->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Đã đăng lên bản đồ',
                'image_url' => '../uploads/' . $file_name,
                'place_name' => $album_name,
                'location_id' => $location_id
            ]);
        } catch (Exception $e) {
            $this->db->rollBack();
            echo json_encode(['success' => false, 'message' => 'Lỗi database']);
        }
        exit();
    }

    // Lấy thông báo cập nhật mới (Polling realtime)
    public function getUpdates() {
        header('Content-Type: application/json');
        $user_id = $_SESSION['user_id'];
        
        // Kiểm tra xem trong 15s qua có bài post mới nào từ user hoặc bạn bè không
        $query = "
            SELECT COUNT(l.id) as new_posts 
            FROM locations l
            LEFT JOIN friendships f ON (
                (f.user_id = :uid AND f.friend_id = l.user_id) OR 
                (f.friend_id = :uid AND f.user_id = l.user_id)
            )
            WHERE (l.user_id = :uid OR (f.status = 'accepted' AND l.user_id != :uid))
            AND l.created_at >= NOW() - INTERVAL 15 SECOND
        ";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([':uid' => $user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['has_updates' => $row['new_posts'] > 0]);
        exit();
    }

    // Quản lý Album
    public function manageAlbum() {
        if (isset($_GET['id'])) {
            $location_id = $_GET['id'];
            $user_id = $_SESSION['user_id'];
            
            // Lấy thông tin địa điểm
            $location = $this->locationModel->getById($location_id, $user_id);
            if (!$location) {
                header("Location: index.php?url=location/dashboard");
                exit();
            }

            $album = $this->locationModel->getAlbum($location_id);

            $query = "SELECT u.id, u.full_name, u.username FROM users u 
                      JOIN friendships f ON (u.id = f.friend_id OR u.id = f.user_id) 
                      WHERE (f.user_id = :uid OR f.friend_id = :uid) AND u.id != :uid AND f.status = 'accepted'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':uid' => $user_id]);
            $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);

            require_once '../app/views/location/manage_album.php';
        }
    }

    // Xóa một ảnh trong album
    public function deleteAlbumImage() {
        if (isset($_GET['id']) && isset($_GET['location_id'])) {
            $image_id = $_GET['id'];
            $location_id = $_GET['location_id'];
            $user_id = $_SESSION['user_id'];

            // Lấy tên file để xóa
            $q = "SELECT image_path FROM location_images WHERE id = :id";
            $s = $this->db->prepare($q);
            $s->execute([':id' => $image_id]);
            $file = $s->fetchColumn();

            if ($this->locationModel->deleteImage($image_id, $user_id)) {
                if ($file && file_exists("../uploads/" . $file)) {
                    unlink("../uploads/" . $file);
                }

                // Cập nhật ảnh đại diện của locations nếu ảnh bị xóa trùng với ảnh đại diện chính
                $q_loc = "SELECT image FROM locations WHERE id = :loc_id";
                $s_loc = $this->db->prepare($q_loc);
                $s_loc->execute([':loc_id' => $location_id]);
                $curr_main = $s_loc->fetchColumn();

                if ($curr_main == $file) {
                    // Tìm ảnh tiếp theo trong album làm đại diện mới, nếu không còn ảnh nào thì để trống
                    $q_next = "SELECT image_path FROM location_images WHERE location_id = :loc_id LIMIT 1";
                    $s_next = $this->db->prepare($q_next);
                    $s_next->execute([':loc_id' => $location_id]);
                    $next_img = $s_next->fetchColumn() ?: "";

                    $q_up = "UPDATE locations SET image = :img WHERE id = :loc_id";
                    $s_up = $this->db->prepare($q_up);
                    $s_up->execute([':img' => $next_img, ':loc_id' => $location_id]);
                }
                
                header("Location: index.php?url=location/manageAlbum&id=" . $location_id . "&success=deleted");
            } else {
                header("Location: index.php?url=location/manageAlbum&id=" . $location_id . "&error=delete_failed");
            }
            exit();
        }
    }

    // Xóa một ảnh trong album (trả về JSON cho AJAX)
    public function deleteAlbumImageJson() {
        header('Content-Type: application/json; charset=utf-8');
        if (isset($_GET['id'])) {
            $image_id = intval($_GET['id']);
            $user_id = $_SESSION['user_id'];

            // Lấy location_id và tên file
            $q_loc = "SELECT location_id, image_path FROM location_images WHERE id = :id";
            $s_loc = $this->db->prepare($q_loc);
            $s_loc->execute([':id' => $image_id]);
            $row = $s_loc->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy ảnh']);
                exit();
            }

            $location_id = $row['location_id'];
            $file = $row['image_path'];

            if ($this->locationModel->deleteImage($image_id, $user_id)) {
                if ($file && file_exists("../uploads/" . $file)) {
                    unlink("../uploads/" . $file);
                }

                // Cập nhật ảnh đại diện của locations nếu ảnh bị xóa trùng với ảnh đại diện chính
                $q_main = "SELECT image FROM locations WHERE id = :loc_id";
                $s_main = $this->db->prepare($q_main);
                $s_main->execute([':loc_id' => $location_id]);
                $curr_main = $s_main->fetchColumn();

                if ($curr_main == $file) {
                    // Tìm ảnh tiếp theo trong album làm đại diện mới
                    $q_next = "SELECT image_path FROM location_images WHERE location_id = :loc_id LIMIT 1";
                    $s_next = $this->db->prepare($q_next);
                    $s_next->execute([':loc_id' => $location_id]);
                    $next_img = $s_next->fetchColumn() ?: "";

                    $q_up = "UPDATE locations SET image = :img WHERE id = :loc_id";
                    $s_up = $this->db->prepare($q_up);
                    $s_up->execute([':img' => $next_img, ':loc_id' => $location_id]);
                }

                echo json_encode(['success' => true, 'message' => 'Đã xóa tệp tin']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không có quyền xóa ảnh này']);
            }
            exit();
        }
        echo json_encode(['success' => false, 'message' => 'Thiếu tham số']);
        exit();
    }

    // Đặt làm ảnh đại diện
    public function setFeatured() {
        if (isset($_GET['id']) && isset($_GET['location_id'])) {
            $image_id = $_GET['id'];
            $location_id = $_GET['location_id'];
            $user_id = $_SESSION['user_id'];

            if ($this->locationModel->setFeaturedImage($image_id, $location_id, $user_id)) {
                header("Location: index.php?url=location/manageAlbum&id=" . $location_id . "&success=featured");
            } else {
                header("Location: index.php?url=location/manageAlbum&id=" . $location_id . "&error=featured_failed");
            }
            exit();
        }
    }

    // Xem bản đồ của bạn bè
    public function friend_map() {
        if (isset($_GET['id'])) {
            $friend_id = $_GET['id'];
            $user_id = $_SESSION['user_id'];

            // Kiểm tra xem có phải là bạn bè không
            $query = "SELECT * FROM friendships WHERE ((user_id = :u1 AND friend_id = :f1) OR (user_id = :f2 AND friend_id = :u2)) AND status = 'accepted'";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':u1' => $user_id, ':f1' => $friend_id, ':f2' => $friend_id, ':u2' => $user_id]);
            
            if ($stmt->rowCount() > 0) {
                // Lấy thông tin bạn bè
                $q_friend = "SELECT full_name, username FROM users WHERE id = :id";
                $s_friend = $this->db->prepare($q_friend);
                $s_friend->execute([':id' => $friend_id]);
                $friend_info = $s_friend->fetch(PDO::FETCH_ASSOC);

                // Lấy danh sách địa điểm của bạn bè
                $locations = $this->locationModel->getAllByUserId($friend_id, $user_id);
                $is_friend_view = true;
                
                // Vẫn cần lấy danh sách bạn bè để hiển thị sidebar
                $q_friends = "SELECT u.id, u.full_name, u.username FROM users u 
                          JOIN friendships f ON (u.id = f.friend_id OR u.id = f.user_id) 
                          WHERE (f.user_id = :uid OR f.friend_id = :uid) AND u.id != :uid AND f.status = 'accepted'";
                $s_friends = $this->db->prepare($q_friends);
                $s_friends->execute([':uid' => $user_id]);
                $friends = $s_friends->fetchAll(PDO::FETCH_ASSOC);

                require_once '../app/views/location/dashboard.php';
            } else {
                header("Location: index.php?url=location/dashboard&error=not_friend");
            }
            exit();
        }
    }
    // Toggle Like cho kỷ niệm (AJAX)
    public function toggleLike() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $location_id = $_POST['location_id'] ?? 0;
            $user_id = $_SESSION['user_id'];

            if ($location_id == 0) {
                echo json_encode(['success' => false, 'message' => 'Lỗi dữ liệu']);
                exit();
            }

            $reaction_type = $_POST['reaction_type'] ?? 'heart';

            // Kiểm tra xem đã like chưa
            $q_check = "SELECT id, reaction_type FROM likes WHERE location_id = :lid AND user_id = :uid";
            $s_check = $this->db->prepare($q_check);
            $s_check->execute([':lid' => $location_id, ':uid' => $user_id]);
            $existing = $s_check->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                if ($existing['reaction_type'] === $reaction_type) {
                    // Cùng loại -> Unlike
                    $q_del = "DELETE FROM likes WHERE id = :id";
                    $s_del = $this->db->prepare($q_del);
                    $s_del->execute([':id' => $existing['id']]);
                    echo json_encode(['success' => true, 'action' => 'unliked', 'type' => null]);
                } else {
                    // Đổi loại
                    $q_upd = "UPDATE likes SET reaction_type = :rtype WHERE id = :id";
                    $s_upd = $this->db->prepare($q_upd);
                    $s_upd->execute([':rtype' => $reaction_type, ':id' => $existing['id']]);
                    echo json_encode(['success' => true, 'action' => 'updated', 'type' => $reaction_type]);
                }
            } else {
                // Chưa like -> Like
                $q_ins = "INSERT INTO likes (location_id, user_id, reaction_type) VALUES (:lid, :uid, :rtype)";
                $s_ins = $this->db->prepare($q_ins);
                $s_ins->execute([':lid' => $location_id, ':uid' => $user_id, ':rtype' => $reaction_type]);
                echo json_encode(['success' => true, 'action' => 'liked', 'type' => $reaction_type]);
            }
            exit();
        }
    }
}
?>
