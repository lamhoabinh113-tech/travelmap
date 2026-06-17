# Initialize Word COM object
$word = New-Object -ComObject Word.Application
$word.Visible = $false

# Open document
$doc_path = "C:\Users\lamho\OneDrive\Desktop\BC_TravelMemoryMap_Final.docx"
$doc = $word.Documents.Open($doc_path)

# Loop through paragraphs and print headings
Write-Host "--- Headings in Document ---"
for ($i = 1; $i -le $doc.Paragraphs.Count; $i++) {
    $p = $doc.Paragraphs.Item($i)
    $text = $p.Range.Text.Trim()
    if ($text -ne "") {
        # Check if style is heading
        $style = $p.Style.NameLocal
        if ($style -like "*Heading*" -or $style -like "*Tiêu đề*" -or $text -like "*2.2*" -or $text -like "*3.2*" -or $text -like "*Kết quả*") {
            Write-Host "[$i] ($style): $text"
        }
    }
}

# Close document and Word
$doc.Close()
$word.Quit()
