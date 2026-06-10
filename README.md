# 🗺️ Travel Memory Map — Lưu Giữ Ký ức Hành Trình

**Travel Memory Map** là một ứng dụng web hiện đại giúp người dùng lưu giữ hành trình, đánh dấu các địa điểm đã đi qua, lưu giữ khoảnh khắc bằng hình ảnh/video, chia sẻ và kết nối với bạn bè cùng một Trợ lý AI du lịch thông minh.

Dự án được xây dựng trên mô hình **MVC** tùy biến bằng **PHP thuần**, kết hợp bản đồ tương tác **Leaflet.js** và các hiệu ứng giao diện hiện đại.

---

## ✨ Các Tính Năng Nổi Bật

### 1. Bản Đồ Ký Ức Tương Tác (Interactive Map)
* Tích hợp bản đồ Leaflet.js hỗ trợ nhiều giao diện (Sáng, Tối, Vệ tinh).
* Định vị GPS chính xác thời gian thực để ghim địa điểm nhanh chóng.
* Đánh dấu địa điểm du lịch bằng marker hình ảnh đại diện tùy chỉnh sống động.
* Tìm kiếm địa điểm thông minh (Geocoding) toàn cầu.
* Bộ lọc bản đồ trực quan theo chuyến đi hoặc theo trạng thái cảm xúc.

### 2. Album Ảnh Locket & Dòng Thời Gian (Timeline Feed)
* Lưu trữ kỷ niệm với tiêu đề, ngày ghé thăm, cảm xúc cá nhân và hình ảnh/video.
* Bảng tin chia sẻ khoảnh khắc (Locket Feed) cập nhật các hoạt động mới nhất từ bạn bè.
* Hệ thống tương tác bài viết phong phú: thả cảm xúc biểu tượng cảm xúc động (Like, Tim, Haha, Wow, Sad) kèm hiệu ứng pháo hoa cảm xúc (Emoji Burst).
* Trình xem ảnh/video trực quan (Lightbox) mượt mà.

### 3. Hệ Thống Bạn Bè & Xã Hội
* Gửi/nhận lời mời kết bạn, quản lý danh sách tri kỷ.
* Nhắn tin trò chuyện riêng tư trực tiếp trên bảng tin.
* Chế độ quyền riêng tư bài đăng: Công khai (mọi người), Bạn bè (chỉ bạn bè), hoặc Cá nhân (chỉ mình tôi).

### 4. Hệ Thống Cấp Độ & Thành Tựu (Gamification)
* Tích lũy kinh nghiệm (XP) từ các hoạt động ghim điểm, đăng ảnh locket, kết bạn.
* Bảng cấp độ cá nhân (Leveling): Tự động thăng cấp từ *Tân binh* đến *Thánh Check-in*.
* Hệ thống Huy chương & Thành tựu đa dạng: *Nhà thám hiểm*, *Thánh Locket*, *Cú Đêm*, *Leo Núi*, *Tri Kỷ* tự động mở khóa theo hành vi thực tế.

### 5. Trợ Lý AI Du Lịch Thông Minh (AI Companion)
* Tích hợp **Google Gemini API** (hoặc OpenAI) để tư vấn lịch trình thông minh.
* Gợi ý lộ trình phượt chi tiết giữa các địa danh (phương tiện, thời gian, điểm dừng chân).
* Thiết kế lịch trình chi tiết (ngày, buổi Sáng - Trưa - Tối) theo gu của người dùng.
* Tra cứu thời tiết thời gian thực cho điểm đến.
* Viết caption mạng xã hội siêu chill, gợi ý góc chụp ảnh sống ảo và đặc sản vùng miền.
* Cơ chế tự động fallback thông minh sang cơ sở dữ liệu offline nếu chưa cấu hình API Key.

### 6. Trang Quản Trị (Admin Dashboard)
* Thống kê tổng quan số lượng người dùng, địa điểm ghim, hình ảnh tải lên.
* Quản lý tài khoản người dùng và phân quyền (Admin / User).
* Theo dõi lịch sử hoạt động hệ thống (Activity Logs) và lịch sử đăng nhập thiết bị (Login Logs).

---

## 🛠️ Công Nghệ Sử Dụng

* **Frontend:** HTML5, CSS3 (Vanilla CSS neumorphism & glassmorphism), JavaScript (ES6+), Leaflet.js, Bootstrap 5, Bootstrap Icons.
* **Backend:** PHP thuần (Custom MVC Architecture), Sessions, PDO Database Wrapper.
* **Database:** MySQL / MariaDB.
* **APIs & Services:** Google Gemini API, OpenStreetMap (Nominatim & Overpass POI), Open-Meteo Weather API.

---

## 🚀 Hướng Dẫn Cài Đặt & Chạy Local (XAMPP)

### Bước 1: Sao chép mã nguồn
```bash
git clone https://github.com/lamhoabinh113-tech/travelmap.git
cd travelmap
```

### Bước 2: Thiết lập Cơ sở dữ liệu
1. Mở **XAMPP Control Panel** và khởi động dịch vụ **Apache** và **MySQL**.
2. Truy cập vào trang quản trị cơ sở dữ liệu: `http://localhost/phpmyadmin`.
3. Tạo một cơ sở dữ liệu mới tên là: `travel_memory_map`.
4. Import file cơ sở dữ liệu mẫu `travel_memory_map_local.sql` (nằm ở thư mục gốc của dự án) vào cơ sở dữ liệu vừa tạo.

### Bước 3: Cấu hình kết nối Database
1. Truy cập thư mục `config/`.
2. Tạo file `config/db_config.php` (nếu chưa có) và thiết lập thông tin kết nối MySQL của bạn:
```php
<?php
// config/db_config.php - KHÔNG push file này lên GitHub
define('DB_HOST', 'localhost');
define('DB_USER', 'root');      // Mặc định của XAMPP
define('DB_PASS', '');          // Mặc định trống
define('DB_NAME', 'travel_memory_map');
```

### Bước 4: Cấu hình Trợ lý AI (Google Gemini Key)
Để kích hoạt trí thông minh nhân tạo đầy đủ cho Trợ lý AI:
1. Lấy mã API miễn phí từ Google tại: [Google AI Studio](https://aistudio.google.com/app/apikey).
2. Tại thư mục `config/`, tạo file `ai_private.php` (file này đã được cấu hình ẩn khỏi Git để bảo mật):
```php
<?php
// config/ai_private.php - KHÔNG push file này lên GitHub
putenv('GEMINI_API_KEY=MÃ_API_KEY_CỦA_BẠN_Ở_ĐÂY');
```

### Bước 5: Trải nghiệm ứng dụng
1. Di chuyển thư mục dự án `travelmap` vào thư mục gốc của Apache (`C:/xampp/htdocs/`).
2. Mở trình duyệt và truy cập: `http://localhost/travelmap/public/index.php`.

---

## 📂 Cấu Trúc Thư Mục Dự Án

```text
travelmap/
├── app/
│   ├── controllers/      # Điều hướng logic nghiệp vụ (AuthController, AiController,...)
│   ├── models/           # Truy vấn cơ sở dữ liệu (LocationModel, TripModel,...)
│   ├── views/            # Giao diện người dùng (home, dashboard, admin,...)
│   └── data/             # Cơ sở dữ liệu du lịch offline (travel_knowledge_vn.php)
├── config/
│   ├── database.php      # Lớp kết nối PDO dùng chung
│   ├── ai.php            # Nạp cấu hình AI
│   └── ai_private.php    # (Bảo mật) Chứa khóa API cục bộ
├── database/             # Chứa tài liệu / script bổ trợ database
├── public/
│   ├── css/              # Bộ CSS thiết kế giao diện (style.css, dashboard_mobile.css,...)
│   ├── uploads/          # Lưu trữ hình ảnh kỷ niệm & ảnh đại diện
│   └── index.php         # Front Controller điều phối mọi request (Routing)
├── travel_memory_map_local.sql # File xuất database mẫu
└── README.md             # File hướng dẫn này
```

---

## 🛡️ Bản Quyền & Giấy Phép
Dự án được phát triển phi thương mại nhằm mục đích học tập và lưu trữ kỷ niệm du lịch cá nhân. Làm với ❤️ tại Việt Nam. 🇻🇳
