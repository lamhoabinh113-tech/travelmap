# Set console output encoding
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

$desktop_path = "C:\Users\lamho\OneDrive\Desktop\BC_TravelMemoryMap_Final.docx"

# Open Word
$word = New-Object -ComObject Word.Application
$word.Visible = $false
$doc = $word.Documents.Open($desktop_path)

Write-Host "Searching for Table of Contents..."
$updated = $false

# Method 1: TablesOfContents collection
if ($doc.TablesOfContents.Count -ge 1) {
    for ($i = 1; $i -le $doc.TablesOfContents.Count; $i++) {
        $doc.TablesOfContents.Item($i).Update()
        Write-Host "Updated TOC item $i using TablesOfContents collection."
        $updated = $true
    }
}

# Method 2: Fields collection (wdFieldTOC = 8)
for ($i = 1; $i -le $doc.Fields.Count; $i++) {
    $field = $doc.Fields.Item($i)
    if ($field.Type -eq 8) {
        $field.Update()
        Write-Host "Updated TOC field $i using Fields collection."
        $updated = $true
    }
}

if (-not $updated) {
    Write-Warning "Could not find any Table of Contents (TOC) fields to update. Making sure all fields are updated..."
    $doc.Fields.Update()
}

# Save and close
$doc.Save()
$doc.Close()
$word.Quit()
Write-Host "Done updating Table of Contents!"
