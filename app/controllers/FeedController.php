<?php
/**
 * Feed Controller
 * Xử lý bảng tin hành trình từ bạn bè
 */

class FeedController {
    private $db;

    public function __construct() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?url=auth/login");
            exit();
        }
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index() {
        $user_id = $_SESSION['user_id'];

        // Lấy danh sách location_id của bạn bè + chính mình
        // JOIN với location_images để lấy đầy đủ danh sách ảnh/video
        $query = "SELECT 
                    l.*,
                    u.full_name,
                    u.username,
                    (SELECT COUNT(*) FROM likes WHERE location_id = l.id) as like_count,
                    (SELECT COUNT(*) FROM likes WHERE location_id = l.id AND user_id = :uid_like) as is_liked,
                    (SELECT COUNT(*) FROM location_images WHERE location_id = l.id) as album_count,
                    CASE WHEN l.user_id = :uid_mine THEN 1 ELSE 0 END as is_mine
                  FROM locations l
                  JOIN users u ON l.user_id = u.id
                  WHERE (l.is_hidden = 0 OR l.is_hidden IS NULL)
                    AND (
                        -- 1. Bài viết của chính mình
                        l.user_id = :uid_self
                        -- 2. Bài viết ở chế độ công khai (Public)
                        OR l.privacy = 'public'
                        -- 3. Bài viết ở chế độ bạn bè (Friends)
                        OR (
                            l.privacy = 'friends'
                            AND l.user_id IN (
                                SELECT CASE 
                                    WHEN f.user_id = :uid_friend THEN f.friend_id
                                    ELSE f.user_id
                                END
                                FROM friendships f
                                WHERE (f.user_id = :uid_f1 OR f.friend_id = :uid_f2)
                                  AND f.status = 'accepted'
                            )
                        )
                        -- 4. Bài viết ở chế độ bạn bè cụ thể (Specific Friends)
                        OR (
                            l.privacy = 'specific_friends'
                            AND (
                                (l.visible_friends IS NOT NULL AND JSON_CONTAINS(l.visible_friends, :uid_string))
                                OR l.visible_friends LIKE :uid_like_str
                            )
                        )
                    )
                  ORDER BY l.created_at DESC
                  LIMIT 50";

        $stmt = $this->db->prepare($query);
        $stmt->execute([
            ':uid_like'   => $user_id,
            ':uid_mine'   => $user_id,
            ':uid_self'   => $user_id,
            ':uid_friend' => $user_id,
            ':uid_f1'     => $user_id,
            ':uid_f2'     => $user_id,
            ':uid_string' => (string)$user_id,
            ':uid_like_str'=> '%' . $user_id . '%',
        ]);
        $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Với mỗi location, lấy tất cả ảnh trong album
        $feed_items = [];
        foreach ($locations as $loc) {
            $imgStmt = $this->db->prepare(
                "SELECT id, image_path FROM location_images WHERE location_id = :id ORDER BY created_at ASC"
            );
            $imgStmt->execute([':id' => $loc['id']]);
            $loc['album_images'] = $imgStmt->fetchAll(PDO::FETCH_ASSOC);

            // Nếu không có ảnh trong album_images, dùng image đại diện
            if (empty($loc['album_images']) && $loc['image']) {
                $loc['album_images'] = [$loc['image']];
            }

            $feed_items[] = $loc;
        }

        require_once '../app/views/location/feed.php';
    }


    // Thả tim/Bỏ thả tim (AJAX Support)
    public function toggleLike() {
        if (isset($_GET['id'])) {
            $location_id = $_GET['id'];
            $user_id = $_SESSION['user_id'];

            // Kiểm tra xem đã like chưa
            $check = "SELECT id FROM likes WHERE user_id = :uid AND location_id = :lid";
            $stmt = $this->db->prepare($check);
            $stmt->execute([':uid' => $user_id, ':lid' => $location_id]);

            $is_liked = false;
            if ($stmt->rowCount() > 0) {
                $query = "DELETE FROM likes WHERE user_id = :uid AND location_id = :lid";
            } else {
                $query = "INSERT INTO likes (user_id, location_id) VALUES (:uid, :lid)";
                $is_liked = true;
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute([':uid' => $user_id, ':lid' => $location_id]);
            
            // Lấy lại số lượng like mới
            $count_query = "SELECT COUNT(*) FROM likes WHERE location_id = :lid";
            $stmt_count = $this->db->prepare($count_query);
            $stmt_count->execute([':lid' => $location_id]);
            $new_count = $stmt_count->fetchColumn();

            echo json_encode([
                'success' => true,
                'is_liked' => $is_liked,
                'like_count' => $new_count
            ]);
            exit();
        }
    }

    private function ensurePrivateMessagesTable() {
        $query = "CREATE TABLE IF NOT EXISTS private_messages (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sender_id INT NOT NULL,
            receiver_id INT NOT NULL,
            message TEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_private_pair (sender_id, receiver_id, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->db->exec($query);
    }

    private function areFriends($user_id, $friend_id) {
        if ($user_id == $friend_id) {
            return false;
        }

        $query = "SELECT 1 FROM friendships
                  WHERE ((user_id = :u AND friend_id = :f) OR (user_id = :f AND friend_id = :u))
                    AND status = 'accepted'
                  LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':u' => $user_id, ':f' => $friend_id]);
        return (bool)$stmt->fetchColumn();
    }

    public function sendPrivateMessage() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit();
        }

        $sender_id = $_SESSION['user_id'];
        $receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
        $message = isset($_POST['message']) ? trim($_POST['message']) : '';

        if ($receiver_id <= 0 || $message === '') {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập tin nhắn']);
            exit();
        }

        $message_length = function_exists('mb_strlen') ? mb_strlen($message, 'UTF-8') : strlen($message);
        if ($message_length > 500) {
            echo json_encode(['success' => false, 'message' => 'Tin nhắn tối đa 500 ký tự']);
            exit();
        }

        if (!$this->areFriends($sender_id, $receiver_id)) {
            echo json_encode(['success' => false, 'message' => 'Bạn chỉ có thể nhắn riêng cho bạn bè']);
            exit();
        }

        $this->ensurePrivateMessagesTable();

        $query = "INSERT INTO private_messages (sender_id, receiver_id, message, created_at)
                  VALUES (:sender_id, :receiver_id, :message, NOW())";
        $stmt = $this->db->prepare($query);
        $ok = $stmt->execute([
            ':sender_id' => $sender_id,
            ':receiver_id' => $receiver_id,
            ':message' => $message
        ]);

        echo json_encode([
            'success' => $ok,
            'message' => $ok ? 'Đã gửi tin nhắn riêng' : 'Không thể gửi tin nhắn'
        ]);
        exit();
    }

    public function getPrivateMessages() {
        header('Content-Type: application/json; charset=utf-8');

        $user_id = $_SESSION['user_id'];
        $friend_id = isset($_GET['friend_id']) ? intval($_GET['friend_id']) : 0;

        if ($friend_id <= 0 || !$this->areFriends($user_id, $friend_id)) {
            echo json_encode(['success' => false, 'message' => 'Không thể tải tin nhắn']);
            exit();
        }

        $this->ensurePrivateMessagesTable();

        $query = "SELECT pm.id, pm.sender_id, pm.receiver_id, pm.message, pm.created_at,
                         u.full_name, u.username, u.avatar
                  FROM private_messages pm
                  JOIN users u ON pm.sender_id = u.id
                  WHERE (pm.sender_id = :user_id AND pm.receiver_id = :friend_id)
                     OR (pm.sender_id = :friend_id AND pm.receiver_id = :user_id)
                  ORDER BY pm.created_at DESC
                  LIMIT 8";
        $stmt = $this->db->prepare($query);
        $stmt->execute([':user_id' => $user_id, ':friend_id' => $friend_id]);
        $messages = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));

        echo json_encode(['success' => true, 'messages' => $messages]);
        exit();
    }
}
