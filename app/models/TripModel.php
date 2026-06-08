<?php
/**
 * Trip Model
 * Quản lý các chuyến đi (nhóm các địa điểm)
 */

class TripModel {
    private $conn;
    private $table_name = "trips";

    public $id;
    public $user_id;
    public $title;
    public $description;
    public $start_date;
    public $end_date;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Tạo chuyến đi mới
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                (user_id, title, description, start_date, end_date, created_at) 
                VALUES (:user_id, :title, :description, :start_date, :end_date, NOW())";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return true;
        }
        return false;
    }

    // Lấy danh sách chuyến đi của user (kể cả chuyến đi được mời)
    public function getByUser($user_id) {
        $query = "SELECT t.* 
                  FROM " . $this->table_name . " t
                  LEFT JOIN trip_members tm ON t.id = tm.trip_id
                  WHERE t.user_id = :user_id OR tm.user_id = :user_id
                  GROUP BY t.id
                  ORDER BY t.start_date DESC, t.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Xóa chuyến đi
    public function delete($id, $user_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);
        return $stmt->execute();
    }

    // Sửa chuyến đi
    public function update() {
        $query = "UPDATE " . $this->table_name . " 
                  SET title = :title, description = :description, start_date = :start_date, end_date = :end_date 
                  WHERE id = :id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":description", $this->description);
        $stmt->bindParam(":start_date", $this->start_date);
        $stmt->bindParam(":end_date", $this->end_date);
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":user_id", $this->user_id);

        return $stmt->execute();
    }
}
?>
