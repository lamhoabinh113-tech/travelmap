<?php
/**
 * Location Model
 * Quản lý các thao tác liên quan đến bảng locations
 */

class LocationModel {
    private $conn;
    private $table_name = "locations";

    public $id;
    public $user_id;
    public $place_name;
    public $latitude;
    public $longitude;
    public $description;
    public $feeling;
    public $image;
    public $visit_date;
    public $privacy = 'public'; // 'public', 'friends', 'private'
    public $visible_friends; // JSON list of user IDs allowed to view
    public $trip_id = null;
    public $touchCreatedAt = false;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lưu địa điểm mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                (user_id, trip_id, place_name, latitude, longitude, description, feeling, image, visit_date, privacy, visible_friends, created_at) 
                VALUES (:user_id, :trip_id, :place_name, :latitude, :longitude, :description, :feeling, :image, :visit_date, :privacy, :visible_friends, NOW())";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":trip_id", $this->trip_id);
        $stmt->bindParam(":place_name", $this->place_name);
        $stmt->bindParam(":latitude", $this->latitude);
        $stmt->bindParam(":longitude", $this->longitude);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":feeling", $this->feeling);
        $stmt->bindParam(":image", $this->image);
        $stmt->bindParam(":visit_date", $this->visit_date);
        $stmt->bindParam(":privacy", $this->privacy);
        $stmt->bindParam(":visible_friends", $this->visible_friends);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Lấy tất cả địa điểm của một user, có lọc quyền riêng tư đối với người xem khác
    public function getAllByUserId($user_id, $viewer_id = null) {
        if ($viewer_id === null) {
            $viewer_id = $user_id; // Mặc định tự xem thì thấy hết
        }
        
        if ($user_id == $viewer_id) {
            $query = "SELECT l.*, u.avatar as user_avatar, u.username, u.full_name,
                             (SELECT COUNT(*) FROM likes WHERE location_id = l.id) as like_count,
                             (SELECT COUNT(*) FROM likes WHERE location_id = l.id AND user_id = :uid1) as is_liked,
                             (SELECT k.reaction_type FROM likes k WHERE k.location_id = l.id AND k.user_id = :uid2 LIMIT 1) as reaction_type
                      FROM " . $this->table_name . " l
                      JOIN users u ON l.user_id = u.id
                      WHERE l.user_id = :user_id 
                      ORDER BY l.visit_date DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':user_id' => $user_id,
                ':uid1' => $viewer_id,
                ':uid2' => $viewer_id
            ]);
        } else {
            // Xem của người khác -> lọc quyền riêng tư
            $query = "SELECT l.*, u.avatar as user_avatar, u.username, u.full_name,
                             (SELECT COUNT(*) FROM likes WHERE location_id = l.id) as like_count,
                             (SELECT COUNT(*) FROM likes WHERE location_id = l.id AND user_id = :uid1) as is_liked,
                             (SELECT k.reaction_type FROM likes k WHERE k.location_id = l.id AND k.user_id = :uid2 LIMIT 1) as reaction_type
                      FROM " . $this->table_name . " l
                      JOIN users u ON l.user_id = u.id
                      WHERE l.user_id = :user_id
                        AND (
                            l.privacy = 'public'
                            OR (
                                l.privacy = 'friends' 
                                AND EXISTS (
                                    SELECT 1 FROM friendships f 
                                    WHERE ((f.user_id = :uid3 AND f.friend_id = l.user_id) OR (f.user_id = l.user_id AND f.friend_id = :uid4))
                                      AND f.status = 'accepted'
                                )
                            )
                            OR (
                                l.privacy = 'specific_friends'
                                AND (
                                    l.visible_friends LIKE CONCAT('%\"', :uid5, '\"%')
                                    OR l.visible_friends LIKE CONCAT('%', :uid6, '%')
                                )
                            )
                        )
                      ORDER BY l.visit_date DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([
                ':user_id' => $user_id,
                ':uid1' => $viewer_id,
                ':uid2' => $viewer_id,
                ':uid3' => $viewer_id,
                ':uid4' => $viewer_id,
                ':uid5' => $viewer_id,
                ':uid6' => $viewer_id
            ]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả địa điểm thuộc một chuyến đi (Bao gồm của tất cả thành viên, có lọc quyền riêng tư)
    public function getAllByTripId($trip_id, $current_user_id) {
        $query = "SELECT l.*, u.full_name, u.username, u.avatar as user_avatar,
                         (SELECT COUNT(*) FROM likes WHERE location_id = l.id) as like_count,
                         (SELECT COUNT(*) FROM likes WHERE location_id = l.id AND user_id = :uid1) as is_liked,
                         (SELECT k.reaction_type FROM likes k WHERE k.location_id = l.id AND k.user_id = :uid2 LIMIT 1) as reaction_type
                  FROM " . $this->table_name . " l
                  JOIN users u ON l.user_id = u.id
                  WHERE l.trip_id = :trip_id
                    AND (
                        l.user_id = :uid3
                        OR l.privacy = 'public'
                        OR (
                            l.privacy = 'friends' 
                            AND EXISTS (
                                SELECT 1 FROM friendships f 
                                WHERE ((f.user_id = :uid4 AND f.friend_id = l.user_id) OR (f.user_id = l.user_id AND f.friend_id = :uid5))
                                  AND f.status = 'accepted'
                            )
                        )
                        OR (
                            l.privacy = 'specific_friends'
                            AND (
                                l.visible_friends LIKE CONCAT('%\"', :uid6, '\"%')
                                OR l.visible_friends LIKE CONCAT('%', :uid7, '%')
                            )
                        )
                    )
                  ORDER BY l.visit_date DESC, l.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':trip_id' => $trip_id,
            ':uid1' => $current_user_id,
            ':uid2' => $current_user_id,
            ':uid3' => $current_user_id,
            ':uid4' => $current_user_id,
            ':uid5' => $current_user_id,
            ':uid6' => $current_user_id,
            ':uid7' => $current_user_id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy toàn bộ địa điểm hiển thị trên Dòng thời gian của user
    // (Bao gồm địa điểm của user và địa điểm của thành viên trong chuyến đi chung, có lọc quyền riêng tư)
    public function getTimelineLocations($user_id) {
        $query = "SELECT l.*, u.avatar as user_avatar, u.username, u.full_name,
                         (SELECT COUNT(*) FROM likes WHERE location_id = l.id) as like_count,
                         (SELECT COUNT(*) FROM likes WHERE location_id = l.id AND user_id = :uid1) as is_liked,
                         (SELECT k.reaction_type FROM likes k WHERE k.location_id = l.id AND k.user_id = :uid2 LIMIT 1) as reaction_type
                  FROM " . $this->table_name . " l
                  JOIN users u ON l.user_id = u.id
                  WHERE l.user_id = :uid3 
                     OR (
                         l.trip_id IN (
                             SELECT id FROM trips WHERE user_id = :uid4
                             UNION
                             SELECT trip_id FROM trip_members WHERE user_id = :uid5
                         )
                         AND (
                             l.privacy = 'public'
                             OR (
                                 l.privacy = 'friends' 
                                 AND EXISTS (
                                     SELECT 1 FROM friendships f 
                                     WHERE ((f.user_id = :uid6 AND f.friend_id = l.user_id) OR (f.user_id = l.user_id AND f.friend_id = :uid7))
                                       AND f.status = 'accepted'
                                 )
                             )
                             OR (
                                 l.privacy = 'specific_friends'
                                 AND (
                                     l.visible_friends LIKE CONCAT('%\"', :uid8, '\"%')
                                     OR l.visible_friends LIKE CONCAT('%', :uid9, '%')
                                 )
                             )
                         )
                     )
                  ORDER BY l.visit_date DESC, l.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            ':uid1' => $user_id,
            ':uid2' => $user_id,
            ':uid3' => $user_id,
            ':uid4' => $user_id,
            ':uid5' => $user_id,
            ':uid6' => $user_id,
            ':uid7' => $user_id,
            ':uid8' => $user_id,
            ':uid9' => $user_id
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy thông tin một địa điểm theo ID
    public function getById($id, $user_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật địa điểm
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                SET place_name = :place_name, 
                    description = :description, 
                    feeling = :feeling, 
                    visit_date = :visit_date,
                    trip_id = :trip_id,
                    privacy = :privacy,
                    visible_friends = :visible_friends";
        
        // Chỉ cập nhật ảnh nếu có ảnh mới
        if ($this->image != "") {
            $query .= ", image = :image";
        }

        if ($this->touchCreatedAt) {
            $query .= ", created_at = NOW()";
        }

        $query .= " WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":place_name", $this->place_name);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":feeling", $this->feeling);
        $stmt->bindParam(":visit_date", $this->visit_date);
        $stmt->bindParam(":trip_id", $this->trip_id);
        $stmt->bindParam(":privacy", $this->privacy);
        $stmt->bindParam(":visible_friends", $this->visible_friends);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);

        if ($this->image != "") {
            $stmt->bindParam(":image", $this->image);
        }

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Xóa địa điểm
    public function delete($id, $user_id) {
        // Lấy danh sách ảnh trong album để xóa file vật lý
        $query_images = "SELECT image_path FROM location_images WHERE location_id = :location_id";
        $stmt_images = $this->conn->prepare($query_images);
        $stmt_images->bindParam(":location_id", $id);
        $stmt_images->execute();
        $images = $stmt_images->fetchAll(PDO::FETCH_ASSOC);

        // Bắt đầu transaction để đảm bảo tính toàn vẹn dữ liệu
        $this->conn->beginTransaction();
        try {
            // 1. Xóa comments của địa điểm này
            $q_comm = "DELETE FROM comments WHERE location_id = :id";
            $s_comm = $this->conn->prepare($q_comm);
            $s_comm->execute([':id' => $id]);

            // 2. Xóa likes của địa điểm này
            $q_likes = "DELETE FROM likes WHERE location_id = :id";
            $s_likes = $this->conn->prepare($q_likes);
            $s_likes->execute([':id' => $id]);

            // 3. Xóa các bình luận ảnh (image_messages) liên quan đến các ảnh của địa điểm này
            $q_img_msgs = "DELETE FROM image_messages WHERE image_id IN (SELECT id FROM location_images WHERE location_id = :id)";
            $s_img_msgs = $this->conn->prepare($q_img_msgs);
            $s_img_msgs->execute([':id' => $id]);

            // 4. Xóa ảnh trong bảng location_images
            $q_imgs = "DELETE FROM location_images WHERE location_id = :id";
            $s_imgs = $this->conn->prepare($q_imgs);
            $s_imgs->execute([':id' => $id]);

            // 5. Xóa địa điểm trong bảng locations
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            $this->conn->commit();
            return $images; // Trả về danh sách ảnh để Controller xóa file
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

    // Thêm ảnh vào album
    public function addImage($location_id, $image_path, $is_featured = 0) {
        $query = "INSERT INTO location_images (location_id, image_path, is_featured) VALUES (:location_id, :image_path, :is_featured)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":location_id", $location_id);
        $stmt->bindParam(":image_path", $image_path);
        $stmt->bindParam(":is_featured", $is_featured);
        return $stmt->execute();
    }

    // Lấy album ảnh của một địa điểm
    public function getAlbum($location_id) {
        $query = "SELECT * FROM location_images WHERE location_id = :location_id ORDER BY created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":location_id", $location_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy hành trình mới nhất từ bạn bè
    public function getFriendLocations($user_id) {
        $query = "SELECT l.*, u.full_name, u.username, u.avatar as user_avatar,
                         (SELECT COUNT(*) FROM likes WHERE location_id = l.id) as like_count,
                         (SELECT COUNT(*) FROM likes WHERE location_id = l.id AND user_id = :uid) as is_liked,
                         (SELECT k.reaction_type FROM likes k WHERE k.location_id = l.id AND k.user_id = :uid LIMIT 1) as reaction_type
                  FROM locations l
                  JOIN users u ON l.user_id = u.id
                  JOIN friendships f ON (u.id = f.friend_id OR u.id = f.user_id)
                  WHERE ((f.user_id = :uid OR f.friend_id = :uid) AND u.id != :uid AND f.status = 'accepted')
                  ORDER BY l.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":uid", $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Xóa một ảnh trong album
    public function deleteImage($image_id, $user_id) {
        // Kiểm tra quyền sở hữu thông qua location_id dùng subquery để tương thích SQLite/MySQL MyISAM
        $query = "DELETE FROM location_images 
                  WHERE id = :image_id 
                    AND location_id IN (SELECT id FROM locations WHERE user_id = :user_id)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":image_id", $image_id);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    // Đặt ảnh làm ảnh đại diện (featured)
    public function setFeaturedImage($image_id, $location_id, $user_id) {
        try {
            $this->conn->beginTransaction();

            // 1. Lấy đường dẫn ảnh
            $q_path = "SELECT image_path FROM location_images WHERE id = :id";
            $s_path = $this->conn->prepare($q_path);
            $s_path->execute([':id' => $image_id]);
            $path = $s_path->fetchColumn();

            if (!$path) return false;

            // 2. Cập nhật bảng locations
            $q_loc = "UPDATE locations SET image = :path WHERE id = :loc_id AND user_id = :user_id";
            $s_loc = $this->conn->prepare($q_loc);
            $s_loc->execute([':path' => $path, ':loc_id' => $location_id, ':user_id' => $user_id]);

            // 3. Cập nhật bảng location_images (is_featured)
            $q_reset = "UPDATE location_images SET is_featured = 0 WHERE location_id = :loc_id";
            $s_reset = $this->conn->prepare($q_reset);
            $s_reset->execute([':loc_id' => $location_id]);

            $q_set = "UPDATE location_images SET is_featured = 1 WHERE id = :id";
            $s_set = $this->conn->prepare($q_set);
            $s_set->execute([':id' => $image_id]);

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }
}
?>
