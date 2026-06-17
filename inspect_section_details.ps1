# Initialize Word COM object
$word = New-Object -ComObject Word.Application
$word.Visible = $false

# Open document
$doc_path = "C:\Users\lamho\OneDrive\Desktop\BC_TravelMemoryMap_Final.docx"
$doc = $word.Documents.Open($doc_path)

# Print paragraph text
Write-Host "--- Text of Section 2.2 ---"
for ($i = 337; $i -lt 358; $i++) {
    if ($i -le $doc.Paragraphs.Count) {
        $p = $doc.Paragraphs.Item($i)
        Write-Host "[$i] $($p.Style.NameLocal): $($p.Range.Text.Trim())"
    }
}

Write-Host "`n--- Text of Section 3.2 ---"
for ($i = 520; $i -lt 528; $i++) {
    if ($i -le $doc.Paragraphs.Count) {
        $p = $doc.Paragraphs.Item($i)
        Write-Host "[$i] $($p.Style.NameLocal): $($p.Range.Text.Trim())"
    }
}

# Close document and Word
$doc.Close()
$word.Quit()
