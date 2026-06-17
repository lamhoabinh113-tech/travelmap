# Set console output encoding
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

# Backup the original report
$desktop_path = "C:\Users\lamho\OneDrive\Desktop\BC_TravelMemoryMap_Final.docx"
$backup_path = "C:\Users\lamho\OneDrive\Desktop\BC_TravelMemoryMap_Final_Backup.docx"
if (-not (Test-Path $backup_path)) {
    Copy-Item $desktop_path $backup_path
    Write-Host "Backup created at $backup_path"
}

# Read text content
$text22 = Get-Content -Path "text22.txt" -Encoding UTF8 -Raw
$text32 = Get-Content -Path "text32.txt" -Encoding UTF8 -Raw

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
    # Find section 2.3
    $range23 = $doc.Content
    $find23 = $range23.Find
    $find23.Text = "2.3. Thiết kế cơ sở dữ liệu"
    $found23 = $find23.Execute()
    
    if ($found23) {
        $insertRange = $range23.Duplicate
        $insertRange.Collapse(1) # Collapse to start of 2.3
        $insertRange.InsertBefore("`n" + $text22 + "`n")
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
    # Find section 3.3
    $range33 = $doc.Content
    $find33 = $range33.Find
    $find33.Text = "3.3. Kiểm thử chức năng theo vai trò"
    $found33 = $find33.Execute()
    
    if ($found33) {
        $insertRange32 = $range33.Duplicate
        $insertRange32.Collapse(1) # Collapse to start of 3.3
        $insertRange32.InsertBefore("`n" + $text32 + "`n")
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
    # Find section 3.5
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
                # Add a paragraph for picture
                $p_break = $doc.Paragraphs.Add($insertRange34)
                $p_break_range = $p_break.Range
                $p_break_range.Collapse(1)
                
                # Add picture
                $shape = $p_break_range.InlineShapes.AddPicture($shot.Path, $false, $true)
                $ratio = $shape.Height / $shape.Width
                $shape.Width = 450
                $shape.Height = [int](450 * $ratio)
                
                # Add caption paragraph
                $p_cap = $doc.Paragraphs.Add($insertRange34)
                $p_cap.Range.Text = "`n" + $shot.Caption + "`n"
                $p_cap.Range.Font.Italic = $true
                $p_cap.Range.Font.Size = 10
                $p_cap.Alignment = 1 # Center
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
