# Backup the original report
$desktop_path = "C:\Users\lamho\OneDrive\Desktop\BC_TravelMemoryMap_Final.docx"
$backup_path = "C:\Users\lamho\OneDrive\Desktop\BC_TravelMemoryMap_Final_Backup.docx"
if (-not (Test-Path $backup_path)) {
    Copy-Item $desktop_path $backup_path
    Write-Host "Backup created at $backup_path"
}

# Open Word
$word = New-Object -ComObject Word.Application
$word.Visible = $false
$doc = $word.Documents.Open($desktop_path)

# ----------------------------------------------------
# 1. Edit Section 2.2: Add "Dữ liệu cần lưu trữ"
# ----------------------------------------------------
Write-Host "Editing Section 2.2..."
$range22 = $doc.Content
$find22 = $range22.Find
$find22.Text = "2.2. Phân tích vai trò người dùng và luồng xử lý"
$found22 = $find22.Execute()

if ($found22) {
    # Move selection to end of the heading paragraph
    $para = $range22.Paragraphs.Item(1)
    
    # We want to insert text after the user roles table. The table is right under 2.2.
    # Let's search for "2.3. Thiết kế cơ sở dữ liệu" to insert right before it.
    $range23 = $doc.Content
    $find23 = $range23.Find
    $find23.Text = "2.3. Thiết kế cơ sở dữ liệu"
    $found23 = $find23.Execute()
    
    if ($found23) {
        # Insert text before 2.3
        $insertRange = $range23.Duplicate
        $insertRange.Collapse(1) # Collapse to start of "2.3. Thiết kế cơ sở dữ liệu"
        
        $text22 = @"
* Phân tích các thực thể dữ liệu cần lưu trữ trong hệ thống:
Để hiện thực hóa các yêu cầu nghiệp vụ và luồng xử lý của hệ thống Travel Memory Map, cơ sở dữ liệu cần lưu trữ và quản lý các nhóm thông tin chính sau đây:
1. Thông tin người dùng (users): Lưu trữ định danh tài khoản bao gồm họ tên, tên đăng nhập, email, mật khẩu (đã mã hóa an toàn), ảnh đại diện (avatar), vai trò hệ thống (role: admin, moderator, user) để phân quyền, điểm tích lũy kinh nghiệm (xp) cho tính năng Gamification và trạng thái khóa tài khoản (is_locked).
2. Thông tin địa điểm du lịch (locations): Lưu trữ chi tiết từng điểm check-in của người dùng gồm tên địa điểm (place_name), tọa độ địa lý chính xác (latitude, longitude) để hiển thị lên bản đồ số, ghi chú mô tả chuyến đi, ngày ghé thăm (visit_date), cảm xúc của chuyến đi (feeling), chế độ hiển thị riêng tư (privacy: public, friends, private) và liên kết với chuyến đi chung (trip_id).
3. Album ảnh chi tiết (location_images): Nhằm hỗ trợ người dùng đăng tải nhiều ảnh cho một địa điểm, bảng này lưu trữ đường dẫn ảnh (image_path) và cờ đánh dấu ảnh nổi bật (is_featured), liên kết khóa ngoại với địa điểm thông qua location_id.
4. Mối quan hệ bạn bè (friendships): Quản lý liên kết xã hội giữa các tài khoản bao gồm mã người gửi (user_id), người nhận (friend_id) và trạng thái lời mời (status: pending, accepted, rejected).
5. Chuyến đi chung (trips & trip_members): Lưu trữ thông tin chuyến đi tập thể (trips) bao gồm tiêu đề, mô tả, ngày bắt đầu, ngày kết thúc và danh sách các thành viên tham gia chuyến đi (trip_members) kèm vai trò của họ (member, admin).
6. Tương tác cộng đồng (likes & comments): Lưu trữ lượt thích/thả tim (likes) và các nội dung bình luận (comments) của bạn bè dưới mỗi địa điểm check-in.
7. Tin nhắn riêng tư (private_messages): Lưu trữ lịch sử trò chuyện trực tuyến giữa các cặp người dùng (sender_id, receiver_id) kèm thời gian gửi.
8. Thông báo (notifications): Hệ thống gửi thông báo tự động khi có tương tác mới (like, comment, lời mời kết bạn).
9. Nhật ký an ninh (login_logs & admin_activity_log): Ghi nhận lịch sử đăng nhập (thời gian, địa chỉ IP, thiết bị đăng nhập) của người dùng và nhật ký các thao tác nhạy cảm của Admin.
10. Cài đặt cấu hình (system_settings): Lưu trữ các tham số cấu hình động của trang web như bật/tắt đăng ký thành viên, tên trang web và trạng thái bảo trì.

Từ các yêu cầu dữ liệu cần lưu trữ thực tế nêu trên, chúng tôi đề xuất thiết kế cấu trúc cơ sở dữ liệu chi tiết gồm 13 bảng tương ứng như trình bày ở Mục 2.3 dưới đây.

"@
        $insertRange.InsertBefore($text22)
        Write-Host "Successfully added Section 2.2 detailed storage analysis."
    }
}

# ----------------------------------------------------
# 2. Edit Section 3.2: Add implementation details (Front/Back/MVC)
# ----------------------------------------------------
Write-Host "Editing Section 3.2..."
$range32 = $doc.Content
$find32 = $range32.Find
$find32.Text = "3.2. Quy trình cài đặt chi tiết"
$found32 = $find32.Execute()

if ($found32) {
    # Find section 3.3 to insert right before it
    $range33 = $doc.Content
    $find33 = $range33.Find
    $find33.Text = "3.3. Kiểm thử chức năng theo vai trò"
    $found33 = $find33.Execute()
    
    if ($found33) {
        $insertRange32 = $range33.Duplicate
        $insertRange32.Collapse(1) # Collapse to start of 3.3
        
        $text32 = @"
* Phân tích giải pháp công nghệ và phân tách kiến trúc MVC trong cài đặt hệ thống:
Hệ thống Travel Memory Map được xây dựng và triển khai dựa trên các công nghệ giao diện và kiến trúc mã nguồn phân lớp như sau:

1. Giải pháp thiết kế giao diện phía Client (Frontend UI):
- HTML5 & CSS3: Xây dựng cấu trúc giao diện phẳng hiện đại, tùy biến kiểu dáng (Vanilla CSS) kết hợp hiệu ứng chiều sâu, mờ kính (Glassmorphism) và các chuyển động vi mô (micro-animations) mượt mà khi di chuột qua các thẻ địa điểm.
- Bootstrap 5: Sử dụng hệ thống lưới (Grid System) để thiết kế giao diện đáp ứng (Responsive Web Design), đảm bảo hiển thị tối ưu trên cả thiết bị di động và máy tính. Sử dụng các thành phần giao diện dựng sẵn như Modal (hộp thoại ghim ảnh), Alert, Card và Form Control để chuẩn hóa trải nghiệm.
- Leaflet JS API: Thư viện bản đồ số động mã nguồn mở được nhúng vào trang chính. Leaflet JS chịu trách nhiệm vẽ bản đồ, lắng nghe sự kiện click của người dùng để lấy tọa độ, đặt các điểm đánh dấu (Markers) với biểu tượng tùy biến và vẽ đường nối lộ trình (Polyline) kết nối hành trình du lịch.
- Vanilla JavaScript (Fetch API): Xử lý bất đồng bộ (Ajax) cho các chức năng tương tác thời gian thực như đăng tải ảnh kỷ niệm (không cần tải lại trang), gửi nhanh bình luận, cập nhật cảm xúc và gửi tin nhắn trong phòng chat riêng tư.

2. Cài đặt các thành phần kiến trúc mã nguồn (MVC):
Hệ thống tuân thủ chặt chẽ mô hình kiến trúc MVC để tách biệt luồng xử lý:
- Các Bộ điều khiển (Controllers):
  + LocationController.php: Controller trung tâm điều phối bản đồ chính, tiếp nhận tọa độ ghim, gọi model lưu địa điểm, điều phối upload nhiều ảnh cùng lúc, xử lý lấy danh sách điểm đi trả về dạng JSON cho Leaflet vẽ lên bản đồ.
  + AuthController.php: Xử lý quy trình xác thực bao gồm Đăng ký, Đăng nhập, Đăng xuất, ghi nhận log phiên làm việc (Session) và log đăng nhập an toàn.
  + AiController.php: Xử lý luồng tương tác với Trợ lý AI du lịch, tiếp nhận câu hỏi của người dùng và gọi API bên ngoài để trả lời.
  + TripController.php & FriendController.php: Điều phối hoạt động lập chuyến đi nhóm, quản lý thành viên chuyến đi và xử lý các trạng thái kết bạn.
  + AdminController.php: Quản lý dashboard quản trị viên, bật/tắt cài đặt hệ thống, thực hiện khóa/mở khóa tài khoản người dùng vi phạm.
- Các Mô hình dữ liệu (Models):
  + LocationModel.php: Thực hiện các câu lệnh SQL tương tác trực tiếp với bảng `locations` và `location_images` (truy vấn lấy danh sách điểm đi của cá nhân/bạn bè, lưu trữ ảnh kỷ niệm).
  + UserModel.php: Thực thi kiểm tra xác thực người dùng, so khớp mật khẩu mã hóa BCRYPT qua hàm `password_verify()`, cộng điểm tích lũy kinh nghiệm (XP) cho người dùng khi check-in điểm mới.
  + TripModel.php & AdminModel.php: Thực hiện các truy vấn dữ liệu đặc thù cho chuyến đi và thống kê hệ thống dành cho Admin.
- Các Giao diện hiển thị (Views):
  + app/views/home.php: Trang chủ Landing Page giới thiệu tính năng hệ thống.
  + app/views/auth/login.php & register.php: Giao diện đăng nhập và đăng ký tài khoản sạch sẽ, trực quan.
  + app/views/location/dashboard.php: Giao diện bản đồ tương tác chính, tích hợp CSS/JS Leaflet, thanh trượt Sidebar chứa danh sách địa điểm, Album ảnh kỷ niệm và khung trò chuyện bạn bè.
  + app/views/admin/dashboard.php: Giao diện quản lý bảng điều khiển dành riêng cho quản trị viên.

"@
        $insertRange32.InsertBefore($text32)
        Write-Host "Successfully added Section 3.2 detailed implementation explanation."
    }
}

# ----------------------------------------------------
# 3. Edit Section 3.4: Insert screenshots
# ----------------------------------------------------
Write-Host "Editing Section 3.4 (Screenshots)..."
$range34 = $doc.Content
$find34 = $range34.Find
$find34.Text = "3.4. Giao diện chương trình"
$found34 = $find34.Execute()

if ($found34) {
    # Find section 3.5 to insert screenshots right before it
    $range35 = $doc.Content
    $find35 = $range35.Find
    $find35.Text = "3.5. Đánh giá kết quả và hướng phát triển"
    $found35 = $find35.Execute()
    
    if ($found35) {
        $insertRange34 = $range35.Duplicate
        $insertRange34.Collapse(1) # Collapse to start of 3.5
        
        $conv_id = "b62215a2-c911-4a74-b42b-aa5a7f329653"
        $brain_dir = "C:\Users\lamho\.gemini\antigravity\brain\$conv_id"
        
        $screenshots = @(
            @{
                Path = "$brain_dir\media__1781061788005.png"
                Caption = "Hình 3.2. Giao diện Đăng nhập hệ thống (Auth Login)"
            },
            @{
                Path = "$brain_dir\media__1781079354989.png"
                Caption = "Hình 3.3. Giao diện Bản đồ tương tác chính (Dashboard Map)"
            },
            @{
                Path = "$brain_dir\media__1781061859685.png"
                Caption = "Hình 3.4. Giao diện Album kỷ niệm tại địa điểm và danh sách ảnh"
            },
            @{
                Path = "$brain_dir\media__1781077244500.png"
                Caption = "Hình 3.5. Giao diện Quản lý Chuyến đi chung và lộ trình nhóm (Shared Trips)"
            },
            @{
                Path = "$brain_dir\media__1781071852596.png"
                Caption = "Hình 3.6. Giao diện Bảng tin (News Feed) tương tác bình luận và kết bạn"
            },
            @{
                Path = "$brain_dir\media__1781074231807.png"
                Caption = "Hình 3.7. Giao diện trò chuyện tương tác với Trợ lý AI du lịch thông minh"
            }
        )
        
        foreach ($shot in $screenshots) {
            if (Test-Path $shot.Path) {
                Write-Host "Inserting $($shot.Caption)..."
                # Insert a paragraph break before inserting picture
                $p_break = $insertRange34.Paragraphs.Add()
                $p_break_range = $p_break.Range
                $p_break_range.Collapse(1)
                
                # Insert Picture
                $shape = $p_break_range.InlineShapes.AddPicture($shot.Path, $false, $true)
                
                # Resize to fit nicely (e.g. width = 450pt, keep aspect ratio)
                $ratio = $shape.Height / $shape.Width
                $shape.Width = 450
                $shape.Height = 450 * $ratio
                
                # Add Caption paragraph
                $p_cap = $insertRange34.Paragraphs.Add()
                $p_cap.Range.Text = "`n" + $shot.Caption + "`n"
                $p_cap.Range.Font.Italic = $true
                $p_cap.Range.Font.Size = 10
                $p_cap.Alignment = 1 # Center alignment
            } else {
                Write-Warning "File not found: $($shot.Path)"
            }
        }
    }
}

# ----------------------------------------------------
# 4. Update Table of Contents
# ----------------------------------------------------
Write-Host "Updating Table of Contents..."
try {
    if ($doc.TablesOfContents.Count -ge 1) {
        $doc.TablesOfContents.Item(1).Update()
        Write-Host "Table of Contents updated successfully."
    } else {
        Write-Host "No Table of Contents found in document to update."
    }
} catch {
    Write-Warning "Could not update Table of Contents automatically: $_"
}

# Save and close
$doc.Save()
$doc.Close()
$word.Quit()
Write-Host "Done editing report document!"
