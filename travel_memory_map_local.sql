-- ============================================================
-- Travel Memory Map - Database Schema cho XAMPP Local
-- Import file này vào phpMyAdmin để tạo database
-- ============================================================

CREATE DATABASE IF NOT EXISTS `travel_memory_map`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `travel_memory_map`;

-- ============================================================
-- Bảng users
-- ============================================================
CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `full_name`  VARCHAR(100) NOT NULL,
    `username`   VARCHAR(50)  NOT NULL UNIQUE,
    `email`      VARCHAR(100) NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL,
    `avatar`     VARCHAR(255) DEFAULT NULL,
    `role`       ENUM('user','moderator','admin') DEFAULT 'user',
    `xp`         INT DEFAULT 0,
    `is_locked`  TINYINT(1) DEFAULT 0,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng locations
-- ============================================================
CREATE TABLE IF NOT EXISTS `locations` (
    `id`              INT AUTO_INCREMENT PRIMARY KEY,
    `user_id`         INT NOT NULL,
    `place_name`      VARCHAR(255) NOT NULL,
    `latitude`        DECIMAL(10,8) NOT NULL,
    `longitude`       DECIMAL(11,8) NOT NULL,
    `description`     TEXT DEFAULT NULL,
    `feeling`         VARCHAR(100) DEFAULT NULL,
    `image`           VARCHAR(255) DEFAULT NULL,
    `visit_date`      DATE DEFAULT NULL,
    `trip_id`         INT DEFAULT NULL,
    `privacy`         ENUM('public','friends','specific_friends','private') DEFAULT 'public',
    `visible_friends` TEXT DEFAULT NULL,
    `is_hidden`       TINYINT(1) DEFAULT 0,
    `created_at`      DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng location_images (album ảnh)
-- ============================================================
CREATE TABLE IF NOT EXISTS `location_images` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `location_id` INT NOT NULL,
    `image_path`  VARCHAR(255) NOT NULL,
    `is_featured` TINYINT(1) DEFAULT 0,
    `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`location_id`) REFERENCES `locations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng friendships
-- ============================================================
CREATE TABLE IF NOT EXISTS `friendships` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `user_id`    INT NOT NULL,
    `friend_id`  INT NOT NULL,
    `status`     ENUM('pending','accepted','rejected') DEFAULT 'pending',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`)   REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`friend_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng trips (Chuyến đi)
-- ============================================================
CREATE TABLE IF NOT EXISTS `trips` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `user_id`     INT NOT NULL,
    `title`       VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `start_date`  DATE DEFAULT NULL,
    `end_date`    DATE DEFAULT NULL,
    `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng trip_members (Hành trình chung)
-- ============================================================
CREATE TABLE IF NOT EXISTS `trip_members` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `trip_id`    INT NOT NULL,
    `user_id`    INT NOT NULL,
    `role`       ENUM('member','admin') DEFAULT 'member',
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_member` (`trip_id`, `user_id`),
    FOREIGN KEY (`trip_id`) REFERENCES `trips`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng likes
-- ============================================================
CREATE TABLE IF NOT EXISTS `likes` (
    `id`            INT AUTO_INCREMENT PRIMARY KEY,
    `user_id`       INT NOT NULL,
    `location_id`   INT NOT NULL,
    `reaction_type` VARCHAR(20) DEFAULT 'heart',
    `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `unique_like` (`user_id`, `location_id`),
    FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`location_id`) REFERENCES `locations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng comments (Bình luận địa điểm)
-- ============================================================
CREATE TABLE IF NOT EXISTS `comments` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `location_id` INT NOT NULL,
    `user_id`     INT NOT NULL,
    `content`     TEXT NOT NULL,
    `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`location_id`) REFERENCES `locations`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`)     REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng private_messages (Tin nhắn riêng tư)
-- ============================================================
CREATE TABLE IF NOT EXISTS `private_messages` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `sender_id`   INT NOT NULL,
    `receiver_id` INT NOT NULL,
    `message`     TEXT NOT NULL,
    `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
    KEY `idx_private_pair` (`sender_id`,`receiver_id`,`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng notifications
-- ============================================================
CREATE TABLE IF NOT EXISTS `notifications` (
    `id`           INT AUTO_INCREMENT PRIMARY KEY,
    `user_id`      INT NOT NULL,
    `actor_id`     INT NOT NULL,
    `type`         VARCHAR(50) NOT NULL,
    `reference_id` INT DEFAULT NULL,
    `message`      TEXT DEFAULT NULL,
    `is_read`      TINYINT(1) DEFAULT 0,
    `created_at`   DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`)  REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`actor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng image_messages (bình luận ảnh)
-- ============================================================
CREATE TABLE IF NOT EXISTS `image_messages` (
    `id`         INT AUTO_INCREMENT PRIMARY KEY,
    `image_id`   INT NOT NULL,
    `sender_id`  INT NOT NULL,
    `message`    TEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`image_id`)  REFERENCES `location_images`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng login_logs
-- ============================================================
CREATE TABLE IF NOT EXISTS `login_logs` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `user_id`     INT NOT NULL,
    `login_time`  DATETIME DEFAULT CURRENT_TIMESTAMP,
    `logout_time` DATETIME DEFAULT NULL,
    `ip_address`  VARCHAR(45) DEFAULT NULL,
    `user_agent`  TEXT DEFAULT NULL,
    `status`      ENUM('success','failed') DEFAULT 'success',
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng admin_activity_log
-- ============================================================
CREATE TABLE IF NOT EXISTS `admin_activity_log` (
    `id`          INT AUTO_INCREMENT PRIMARY KEY,
    `admin_id`    INT NOT NULL,
    `action`      VARCHAR(100) NOT NULL,
    `target_type` VARCHAR(50) DEFAULT NULL,
    `target_id`   INT DEFAULT NULL,
    `detail`      TEXT DEFAULT NULL,
    `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`admin_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Bảng system_settings
-- ============================================================
CREATE TABLE IF NOT EXISTS `system_settings` (
    `id`    INT AUTO_INCREMENT PRIMARY KEY,
    `key`   VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mặc định cho system_settings
INSERT INTO `system_settings` (`key`, `value`) VALUES
('allow_register', '1'),
('site_name', 'Travel Memory Map'),
('maintenance_mode', '0')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);

-- ============================================================
-- Tài khoản Admin mặc định
-- username: admin | password: Admin@123
-- (bcrypt hash của "Admin@123")
-- ============================================================
INSERT INTO `users` (`full_name`, `username`, `email`, `password`, `role`) VALUES
('Administrator', 'admin', 'admin@travelmap.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin')
ON DUPLICATE KEY UPDATE `id` = `id`;

-- Ghi chú: password hash trên là của chuỗi "password"
-- Sau khi import, đổi mật khẩu admin ngay tại trang quản trị!
