import os
import sys
import subprocess

# Check and install pywin32 if not present
try:
    import win32com.client
except ImportError:
    print("Installing pywin32...")
    subprocess.run([sys.executable, "-m", "pip", "install", "pywin32"], check=True)
    import win32com.client

def edit_document():
    # Paths
    desktop_dir = os.path.join(os.environ['USERPROFILE'], 'OneDrive', 'Desktop')
    if not os.path.exists(desktop_dir):
        desktop_dir = os.path.join(os.environ['USERPROFILE'], 'Desktop')
        
    doc_path = os.path.join(desktop_dir, 'BC_TravelMemoryMap_Final.docx')
    backup_path = os.path.join(desktop_dir, 'BC_TravelMemoryMap_Final_Backup.docx')
    
    if not os.path.exists(doc_path):
        print(f"Error: Document not found at {doc_path}")
        return
        
    if not os.path.exists(backup_path):
        import shutil
        shutil.copy(doc_path, backup_path)
        print(f"Created backup at {backup_path}")
        
    # Open Word via COM
    word = win32com.client.Dispatch("Word.Application")
    word.Visible = False
    
    try:
        doc = word.Documents.Open(doc_path)
        
        # 1. Section 2.2 Edit
        print("Editing Section 2.2...")
        range23 = doc.Content
        find23 = range23.Find
        find23.Text = "2.3. Thiết kế cơ sở dữ liệu"
        if find23.Execute():
            insertRange = range23.Duplicate
            insertRange.Collapse(1) # Collapse to start of 2.3
            
            text22 = (
                "\n* Phân tích các thực thể dữ liệu cần lưu trữ trong hệ thống:\n"
                "Để hiện thực hóa các yêu cầu nghiệp vụ và luồng xử lý của hệ thống Travel Memory Map, cơ sở dữ liệu cần lưu trữ và quản lý các nhóm thông tin chính sau đây:\n"
                "1. Thông tin người dùng (users): Lưu trữ định danh tài khoản bao gồm họ tên, tên đăng nhập, email, mật khẩu (đã mã hóa an toàn), ảnh đại diện (avatar), vai trò hệ thống (role: admin, moderator, user) để phân quyền, điểm tích lũy kinh nghiệm (xp) cho tính năng Gamification và trạng thái khóa tài khoản (is_locked).\n"
                "2. Thông tin địa điểm du lịch (locations): Lưu trữ chi tiết từng điểm check-in của người dùng gồm tên địa điểm (place_name), tọa độ địa lý chính xác (latitude, longitude) để hiển thị lên bản đồ số, ghi chú mô tả chuyến đi, ngày ghé thăm (visit_date), cảm xúc của chuyến đi (feeling), chế độ hiển thị riêng tư (privacy: public, friends, private) và liên kết với chuyến đi chung (trip_id).\n"
                "3. Album ảnh chi tiết (location_images): Nhằm hỗ trợ người dùng đăng tải nhiều ảnh cho một địa điểm, bảng này lưu trữ đường dẫn ảnh (image_path) và cờ đánh dấu ảnh nổi bật (is_featured), liên kết khóa ngoại với địa điểm thông qua location_id.\n"
                "4. Mối quan hệ bạn bè (friendships): Quản lý liên kết xã hội giữa các tài khoản bao gồm mã người gửi (user_id), người nhận (friend_id) và trạng thái lời mời (status: pending, accepted, rejected).\n"
                "5. Chuyến đi chung (trips và trip_members): Lưu trữ thông tin chuyến đi tập thể (trips) bao gồm tiêu đề, mô tả, ngày bắt đầu, ngày kết thúc và danh sách các thành viên tham gia chuyến đi (trip_members) kèm vai trò của họ (member, admin).\n"
                "6. Tương tác cộng đồng (likes và comments): Lưu trữ lượt thích/thả tim (likes) và các nội dung bình luận (comments) của bạn bè dưới mỗi địa điểm check-in.\n"
                "7. Tin nhắn riêng tư (private_messages): Lưu trữ lịch sử trò chuyện trực tuyến giữa các cặp người dùng (sender_id, receiver_id) kèm thời gian gửi.\n"
                "8. Thông báo (notifications): Hệ thống gửi thông báo tự động khi có tương tác mới (like, comment, lời mời kết bạn).\n"
                "9. Nhật ký an ninh (login_logs và admin_activity_log): Ghi nhận lịch sử đăng nhập (thời gian, địa chỉ IP, thiết bị đăng nhập) của người dùng và nhật ký các thao tác nhạy cảm của Admin.\n"
                "10. Cài đặt cấu hình (system_settings): Lưu trữ các tham số cấu hình động của trang web như bật/tắt đăng ký thành viên, tên trang web và trạng thái bảo trì.\n\n"
                "Từ các yêu cầu dữ liệu cần lưu trữ thực tế nêu trên, chúng tôi đề xuất thiết kế cấu trúc cơ sở dữ liệu chi tiết gồm 13 bảng tương ứng như trình bày ở Mục 2.3 dưới đây.\n\n"
            )
            insertRange.InsertBefore(text22)
            print("Section 2.2 updated.")
            
        # 2. Section 3.2 Edit
        print("Editing Section 3.2...")
        range33 = doc.Content
        find33 = range33.Find
        find33.Text = "3.3. Kiểm thử chức năng theo vai trò"
        if find33.Execute():
            insertRange32 = range33.Duplicate
            insertRange32.Collapse(1) # Collapse to start of 3.3
            
            text32 = (
                "\n* Phân tích giải pháp công nghệ và phân tách kiến trúc MVC trong cài đặt hệ thống:\n"
                "Hệ thống Travel Memory Map được xây dựng và triển khai dựa trên các công nghệ giao diện và kiến trúc mã nguồn phân lớp như sau:\n\n"
                "1. Giải pháp thiết kế giao diện phía Client (Frontend UI):\n"
                "- HTML5 và CSS3: Xây dựng cấu trúc giao diện phẳng hiện đại, tùy biến kiểu dáng (Vanilla CSS) kết hợp hiệu ứng chiều sâu, mờ kính (Glassmorphism) và các chuyển động vi mô (micro-animations) mượt mà khi di chuột qua các thẻ địa điểm.\n"
                "- Bootstrap 5: Sử dụng hệ thống lưới (Grid System) để thiết kế giao diện đáp ứng (Responsive Web Design), đảm bảo hiển thị tối ưu trên cả thiết bị di động và máy tính. Sử dụng các thành phần giao diện dựng sẵn như Modal (hộp thoại ghim ảnh), Alert, Card và Form Control để chuẩn hóa trải nghiệm.\n"
                "- Leaflet JS API: Thư viện bản đồ số động mã nguồn mở được nhúng vào trang chính. Leaflet JS chịu trách nhiệm vẽ bản đồ, lắng nghe sự kiện click của người dùng để lấy tọa độ, đặt các điểm đánh dấu (Markers) với biểu tượng tùy biến và vẽ đường nối lộ trình (Polyline) kết nối hành trình du lịch.\n"
                "- Vanilla JavaScript (Fetch API): Xử lý bất đồng bộ (Ajax) cho các chức năng tương tác thời gian thực như đăng tải ảnh kỷ niệm (không cần tải lại trang), gửi nhanh bình luận, cập nhật cảm xúc và gửi tin nhắn trong phòng chat riêng tư.\n\n"
                "2. Cài đặt các thành phần kiến trúc mã nguồn (MVC):\n"
                "Hệ thống tuân thủ chặt chẽ mô hình kiến trúc MVC để tách biệt luồng xử lý:\n"
                "- Các Bộ điều khiển (Controllers):\n"
                "  + LocationController.php: Controller trung tâm điều phối bản đồ chính, tiếp nhận tọa độ ghim, gọi model lưu địa điểm, điều phối upload nhiều ảnh cùng lúc, xử lý lấy danh sách điểm đi trả về dạng JSON cho Leaflet vẽ lên bản đồ.\n"
                "  + AuthController.php: Xử lý quy trình xác thực bao gồm Đăng ký, Đăng nhập, Đăng xuất, ghi nhận log phiên làm việc (Session) và log đăng nhập an toàn.\n"
                "  + AiController.php: Xử lý luồng tương tác với Trợ lý AI du lịch, tiếp nhận câu hỏi của người dùng và gọi API bên ngoài để trả lời.\n"
                "  + TripController.php và FriendController.php: Điều phối hoạt động lập chuyến đi nhóm, quản lý thành viên chuyến đi và xử lý các trạng thái kết bạn.\n"
                "  + AdminController.php: Quản lý dashboard quản trị viên, bật/tắt cài đặt hệ thống, thực hiện khóa/mở khóa tài khoản người dùng vi phạm.\n"
                "- Các Mô hình dữ liệu (Models):\n"
                "  + LocationModel.php: Thực hiện các câu lệnh SQL tương tác trực tiếp với bảng `locations` và `location_images` (truy vấn lấy danh sách điểm đi của cá nhân/bạn bè, lưu trữ ảnh kỷ niệm).\n"
                "  + UserModel.php: Thực thi kiểm tra xác thực người dùng, so khớp mật khẩu mã hóa BCRYPT qua hàm `password_verify()`, cộng điểm tích lũy kinh nghiệm (XP) cho người dùng khi check-in điểm mới.\n"
                "  + TripModel.php và AdminModel.php: Thực hiện các truy vấn dữ liệu đặc thù cho chuyến đi và thống kê hệ thống dành cho Admin.\n"
                "- Các Giao diện hiển thị (Views):\n"
                "  + app/views/home.php: Trang chủ Landing Page giới thiệu tính năng hệ thống.\n"
                "  + app/views/auth/login.php và register.php: Giao diện đăng nhập và đăng ký tài khoản sạch sẽ, trực quan.\n"
                "  + app/views/location/dashboard.php: Giao diện bản đồ tương tác chính, tích hợp CSS/JS Leaflet, thanh trượt Sidebar chứa danh sách địa điểm, Album ảnh kỷ niệm và khung trò chuyện bạn bè.\n"
                "  + app/views/admin/dashboard.php: Giao diện quản lý bảng điều khiển dành riêng cho quản trị viên.\n\n"
            )
            insertRange32.InsertBefore(text32)
            print("Section 3.2 updated.")
            
        # 3. Section 3.4 Screenshots Insert
        print("Editing Section 3.4 (Screenshots)...")
        range35 = doc.Content
        find35 = range35.Find
        find35.Text = "3.5. Đánh giá kết quả và hướng phát triển"
        if find35.Execute():
            insertRange34 = range35.Duplicate
            insertRange34.Collapse(1) # Collapse to start of 3.5
            
            conv_id = "b62215a2-c911-4a74-b42b-aa5a7f329653"
            brain_dir = os.path.join(os.environ['USERPROFILE'], '.gemini', 'antigravity', 'brain', conv_id)
            
            screenshots = [
                {"path": "media__1781061788005.png", "caption": "Hình 3.2. Giao diện Đăng nhập hệ thống (Auth Login)"},
                {"path": "media__1781079354989.png", "caption": "Hình 3.3. Giao diện Bản đồ tương tác chính (Dashboard Map)"},
                {"path": "media__1781061859685.png", "caption": "Hình 3.4. Giao diện Album kỷ niệm tại địa điểm và danh sách ảnh"},
                {"path": "media__1781077244500.png", "caption": "Hình 3.5. Giao diện Quản lý Chuyến đi chung và lộ trình nhóm (Shared Trips)"},
                {"path": "media__1781071852596.png", "caption": "Hình 3.6. Giao diện Bảng tin (News Feed) tương tác bình luận và kết bạn"},
                {"path": "media__1781074231807.png", "caption": "Hình 3.7. Giao diện trò chuyện tương tác với Trợ lý AI du lịch thông minh"}
            ]
            
            for shot in screenshots:
                full_path = os.path.join(brain_dir, shot["path"])
                if os.path.exists(full_path):
                    print(f"Inserting {shot['caption']}...")
                    
                    # Insert a paragraph break
                    p_break = doc.Paragraphs.Add(insertRange34)
                    p_break_range = p_break.Range
                    p_break_range.Collapse(1)
                    
                    # Add inline picture
                    shape = p_break_range.InlineShapes.AddPicture(full_path, False, True)
                    
                    # Resize nicely
                    ratio = shape.Height / shape.Width
                    shape.Width = 450
                    shape.Height = int(450 * ratio)
                    
                    # Add caption
                    p_cap = doc.Paragraphs.Add(insertRange34)
                    p_cap.Range.Text = f"\n{shot['caption']}\n"
                    p_cap.Range.Font.Italic = True
                    p_cap.Range.Font.Size = 10
                    p_cap.Alignment = 1 # Center
                else:
                    print(f"Warning: Screenshot path not found {full_path}")
            
        # 4. Update Table of Contents
        print("Updating Table of Contents...")
        if doc.TablesOfContents.Count >= 1:
            doc.TablesOfContents.Item(1).Update()
            print("Table of Contents updated.")
            
        doc.Save()
        print("Report document modified and saved successfully!")
        
    finally:
        doc.Close()
        word.Quit()

if __name__ == "__main__":
    edit_document()
