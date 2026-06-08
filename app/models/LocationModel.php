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

    // Lấy tất cả địa điểm của một user
    public function getAllByUserId($user_id) {
        $query = "SELECT l.*, u.avatar as user_avatar, u.username, u.full_name,
                         (SELECT COUNT(*) FROM likes WHERE location_id = l.id) as like_count,
                         (SELECT COUNT(*) FROM likes WHERE location_id = l.id AND user_id = :uid) as is_liked,
                         (SELECT k.reaction_type FROM likes k WHERE k.location_id = l.id AND k.user_id = :uid LIMIT 1) as reaction_type
                  FROM " . $this->table_name . " l
                  JOIN users u ON l.user_id = u.id
                  WHERE l.user_id = :user_id 
                  ORDER BY l.visit_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":uid", $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy tất cả địa điểm thuộc một chuyến đi (Bao gồm của tất cả thành viên)
    public function getAllByTripId($trip_id, $current_user_id) {
        $query = "SELECT l.*, u.full_name, u.username, u.avatar as user_avatar,
                         (SELECT COUNT(*) FROM likes WHERE location_id = l.id) as like_count,
                         (SELECT COUNT(*) FROM likes WHERE location_id = l.id AND user_id = :uid) as is_liked,
                         (SELECT k.reaction_type FROM likes k WHERE k.location_id = l.id AND k.user_id = :uid LIMIT 1) as reaction_type
                  FROM " . $this->table_name . " l
                  JOIN users u ON l.user_id = u.id
                  WHERE l.trip_id = :trip_id 
                  ORDER BY l.visit_date DESC, l.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":trip_id", $trip_id);
        $stmt->bindParam(":uid", $current_user_id);
        $stmt->execute();
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

        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);
        
        if ($stmt->execute()) {
            return $images; // Trả về danh sách ảnh để Controller xóa file
        }
        return false;
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
        // Kiểm tra quyền sở hữu thông qua location_id
        $query = "DELETE li FROM location_images li
                  JOIN locations l ON li.location_id = l.id
                  WHERE li.id = :image_id AND l.user_id = :user_id";
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
