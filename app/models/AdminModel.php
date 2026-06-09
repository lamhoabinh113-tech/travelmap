<?php
/**
 * Admin Model
 * Xử lý toàn bộ truy vấn dữ liệu cho trang quản trị
 */
class AdminModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // ===================== STATISTICS =====================

    public function getOverviewStats() {
        $stats = [];

        $stats['total_users']    = $this->conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $stats['total_locations'] = $this->conn->query("SELECT COUNT(*) FROM locations")->fetchColumn();
        $stats['total_images']   = $this->conn->query("SELECT COUNT(*) FROM location_images")->fetchColumn();
        $stats['total_friends']  = $this->conn->query("SELECT COUNT(*) FROM friendships WHERE status='accepted'")->fetchColumn();
        $stats['total_likes']    = $this->conn->query("SELECT COUNT(*) FROM likes")->fetchColumn();
        $stats['posts_today']    = $this->conn->query("SELECT COUNT(*) FROM locations WHERE DATE(created_at)=CURDATE()")->fetchColumn();
        $stats['new_users_week'] = $this->conn->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();
        $stats['logins_today']   = $this->conn->query("SELECT COUNT(*) FROM login_logs WHERE DATE(login_time)=CURDATE()")->fetchColumn();

        return $stats;
    }

    public function getPostsPerDay($days = 14) {
        $stmt = $this->conn->prepare("
            SELECT DATE(created_at) as day, COUNT(*) as count
            FROM locations
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL :days DAY)
            GROUP BY DATE(created_at)
            ORDER BY day ASC
        ");
        $stmt->execute([':days' => $days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLoginsPerDay($days = 14) {
        $stmt = $this->conn->prepare("
            SELECT DATE(login_time) as day, COUNT(*) as count
            FROM login_logs
            WHERE login_time >= DATE_SUB(NOW(), INTERVAL :days DAY)
            GROUP BY DATE(login_time)
            ORDER BY day ASC
        ");
        $stmt->execute([':days' => $days]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTopActiveUsers($limit = 10) {
        $stmt = $this->conn->prepare("
            SELECT u.id, u.full_name, u.username, u.role,
                   COUNT(l.id) as post_count
            FROM users u
            LEFT JOIN locations l ON u.id = l.user_id
            GROUP BY u.id
            ORDER BY post_count DESC
            LIMIT :lim
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFeelingDistribution() {
        $stmt = $this->conn->query("
            SELECT feeling, COUNT(*) as count
            FROM locations
            GROUP BY feeling
            ORDER BY count DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===================== USER MANAGEMENT =====================

    public function getAllUsers($search = '', $role = '', $status = '', $limit = 20, $offset = 0) {
        $where = [];
        $params = [];

        if ($search) {
            $where[] = "(u.full_name LIKE :search OR u.username LIKE :search OR u.email LIKE :search)";
            $params[':search'] = "%$search%";
        }
        if ($role) {
            $where[] = "u.role = :role";
            $params[':role'] = $role;
        }
        if ($status === 'locked') {
            $where[] = "u.is_locked = 1";
        } elseif ($status === 'active') {
            $where[] = "u.is_locked = 0";
        }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $stmt = $this->conn->prepare("
            SELECT u.*,
                   COUNT(DISTINCT l.id) as post_count,
                   COUNT(DISTINCT li.id) as like_count,
                   MAX(ll.login_time) as last_login
            FROM users u
            LEFT JOIN locations l ON u.id = l.user_id
            LEFT JOIN likes li ON u.id = li.user_id
            LEFT JOIN login_logs ll ON u.id = ll.user_id
            $whereClause
            GROUP BY u.id
            ORDER BY u.created_at DESC
            LIMIT :lim OFFSET :off
        ");
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countUsers($search = '', $role = '', $status = '') {
        $where = [];
        $params = [];
        if ($search) {
            $where[] = "(full_name LIKE :search OR username LIKE :search OR email LIKE :search)";
            $params[':search'] = "%$search%";
        }
        if ($role) { $where[] = "role = :role"; $params[':role'] = $role; }
        if ($status === 'locked')  { $where[] = "is_locked = 1"; }
        elseif ($status === 'active') { $where[] = "is_locked = 0"; }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users $whereClause");
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function getUserById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateUser($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        foreach (['full_name','email','role','is_locked'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        if (!$fields) return false;
        $stmt = $this->conn->prepare("UPDATE users SET " . implode(',', $fields) . " WHERE id = :id");
        return $stmt->execute($params);
    }

    public function resetPassword($id, $hashedPassword) {
        $stmt = $this->conn->prepare("UPDATE users SET password = :pass WHERE id = :id");
        return $stmt->execute([':pass' => $hashedPassword, ':id' => $id]);
    }

    public function deleteUser($id) {
        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public function toggleLock($id) {
        $stmt = $this->conn->prepare("UPDATE users SET is_locked = NOT is_locked WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ===================== POSTS / LOCATIONS =====================

    public function getAllPosts($search = '', $user_id = '', $limit = 20, $offset = 0) {
        $where = [];
        $params = [];
        if ($search) {
            $where[] = "(l.place_name LIKE :search OR l.description LIKE :search)";
            $params[':search'] = "%$search%";
        }
        if ($user_id) { $where[] = "l.user_id = :uid"; $params[':uid'] = $user_id; }

        $whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';
        $stmt = $this->conn->prepare("
            SELECT l.*, u.full_name, u.username,
                   COUNT(DISTINCT li.id) as like_count,
                   COUNT(DISTINCT img.id) as image_count,
                   l.is_hidden
            FROM locations l
            JOIN users u ON l.user_id = u.id
            LEFT JOIN likes li ON l.id = li.location_id
            LEFT JOIN location_images img ON l.id = img.location_id
            $whereClause
            GROUP BY l.id
            ORDER BY l.created_at DESC
            LIMIT :lim OFFSET :off
        ");
        foreach ($params as $k => $v) $stmt->bindValue($k, $v);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPosts($search = '', $user_id = '') {
        $where = []; $params = [];
        if ($search) { $where[] = "(place_name LIKE :s OR description LIKE :s)"; $params[':s'] = "%$search%"; }
        if ($user_id) { $where[] = "user_id = :uid"; $params[':uid'] = $user_id; }
        $wc = $where ? 'WHERE '.implode(' AND ', $where) : '';
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM locations $wc");
        foreach ($params as $k=>$v) $stmt->bindValue($k,$v);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function deletePost($id) {
        // Get images first
        $imgs = $this->conn->prepare("SELECT image_path FROM location_images WHERE location_id = :id");
        $imgs->execute([':id' => $id]);
        $images = $imgs->fetchAll(PDO::FETCH_COLUMN);

        $this->conn->beginTransaction();
        try {
            // Xóa comments
            $s1 = $this->conn->prepare("DELETE FROM comments WHERE location_id = :id");
            $s1->execute([':id' => $id]);

            // Xóa likes
            $s2 = $this->conn->prepare("DELETE FROM likes WHERE location_id = :id");
            $s2->execute([':id' => $id]);

            // Xóa image_messages
            $s3 = $this->conn->prepare("DELETE FROM image_messages WHERE image_id IN (SELECT id FROM location_images WHERE location_id = :id)");
            $s3->execute([':id' => $id]);

            // Xóa location_images
            $s4 = $this->conn->prepare("DELETE FROM location_images WHERE location_id = :id");
            $s4->execute([':id' => $id]);

            // Xóa locations
            $s5 = $this->conn->prepare("DELETE FROM locations WHERE id = :id");
            $s5->execute([':id' => $id]);

            $this->conn->commit();
            return $images;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    public function toggleHidePost($id) {
        $stmt = $this->conn->prepare("UPDATE locations SET is_hidden = NOT is_hidden WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ===================== LOGIN LOGS =====================

    public function getLoginLogs($user_filter = '', $date_from = '', $date_to = '', $limit = 30, $offset = 0) {
        $where = [];
        $params = [];
        if ($user_filter) {
            $where[] = "(u.username LIKE :uf OR u.full_name LIKE :uf)";
            $params[':uf'] = "%$user_filter%";
        }
        if ($date_from) { $where[] = "ll.login_time >= :df"; $params[':df'] = $date_from . ' 00:00:00'; }
        if ($date_to)   { $where[] = "ll.login_time <= :dt"; $params[':dt'] = $date_to . ' 23:59:59'; }

        $wc = $where ? 'WHERE '.implode(' AND ', $where) : '';
        $stmt = $this->conn->prepare("
            SELECT ll.*, u.full_name, u.username
            FROM login_logs ll
            JOIN users u ON ll.user_id = u.id
            $wc
            ORDER BY ll.login_time DESC
            LIMIT :lim OFFSET :off
        ");
        foreach ($params as $k=>$v) $stmt->bindValue($k,$v);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countLoginLogs($user_filter = '', $date_from = '', $date_to = '') {
        $where = []; $params = [];
        if ($user_filter) { $where[] = "(u.username LIKE :uf OR u.full_name LIKE :uf)"; $params[':uf'] = "%$user_filter%"; }
        if ($date_from) { $where[] = "ll.login_time >= :df"; $params[':df'] = $date_from . ' 00:00:00'; }
        if ($date_to)   { $where[] = "ll.login_time <= :dt"; $params[':dt'] = $date_to . ' 23:59:59'; }
        $wc = $where ? 'WHERE '.implode(' AND ', $where) : '';
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM login_logs ll JOIN users u ON ll.user_id = u.id $wc");
        foreach ($params as $k=>$v) $stmt->bindValue($k,$v);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // ===================== ACTIVITY LOG =====================

    public function addActivityLog($admin_id, $action, $target_type, $target_id, $detail = '') {
        $stmt = $this->conn->prepare("
            INSERT INTO admin_activity_log (admin_id, action, target_type, target_id, detail, created_at)
            VALUES (:admin_id, :action, :target_type, :target_id, :detail, NOW())
        ");
        return $stmt->execute([
            ':admin_id'    => $admin_id,
            ':action'      => $action,
            ':target_type' => $target_type,
            ':target_id'   => $target_id,
            ':detail'      => $detail
        ]);
    }

    public function getActivityLog($limit = 50, $offset = 0) {
        $stmt = $this->conn->prepare("
            SELECT al.*, u.full_name, u.username
            FROM admin_activity_log al
            LEFT JOIN users u ON al.admin_id = u.id
            ORDER BY al.created_at DESC
            LIMIT :lim OFFSET :off
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countActivityLog() {
        return $this->conn->query("SELECT COUNT(*) FROM admin_activity_log")->fetchColumn();
    }

    // ===================== FRIENDSHIPS =====================

    public function getAllFriendships($limit = 30, $offset = 0) {
        $stmt = $this->conn->prepare("
            SELECT f.*,
                   u1.full_name as user_name, u1.username as user_uname,
                   u2.full_name as friend_name, u2.username as friend_uname
            FROM friendships f
            JOIN users u1 ON f.user_id = u1.id
            JOIN users u2 ON f.friend_id = u2.id
            ORDER BY f.created_at DESC
            LIMIT :lim OFFSET :off
        ");
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteFriendship($id) {
        $stmt = $this->conn->prepare("DELETE FROM friendships WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ===================== SETTINGS =====================

    public function getSetting($key) {
        $stmt = $this->conn->prepare("SELECT value FROM system_settings WHERE `key` = :key");
        $stmt->execute([':key' => $key]);
        return $stmt->fetchColumn();
    }

    public function getAllSettings() {
        $stmt = $this->conn->query("SELECT * FROM system_settings ORDER BY `key` ASC");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $settings = [];
        foreach ($rows as $row) $settings[$row['key']] = $row['value'];
        return $settings;
    }

    public function setSetting($key, $value) {
        $stmt = $this->conn->prepare("
            INSERT INTO system_settings (`key`, value) VALUES (:key, :val)
            ON DUPLICATE KEY UPDATE value = :val
        ");
        return $stmt->execute([':key' => $key, ':val' => $value]);
    }
}
?>
