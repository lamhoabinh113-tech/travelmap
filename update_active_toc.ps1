# Set console output encoding
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8

$desktop_path = "C:\Users\lamho\OneDrive\Desktop\BC_TravelMemoryMap_Final.docx"
$word = $null
$doc = $null
$isOpenHere = $false

try {
    # Try to connect to an existing running Word instance
    $word = [System.Runtime.InteropServices.Marshal]::GetActiveObject("Word.Application")
    Write-Host "Connected to active running MS Word instance."
    
    # Try to find the document among open documents
    if ($word.Documents.Count -gt 0) {
        for ($i = 1; $i -le $word.Documents.Count; $i++) {
            $tempDoc = $word.Documents.Item($i)
            if ($tempDoc.FullName -like "*BC_TravelMemoryMap_Final.docx*") {
                $doc = $tempDoc
                $isOpenHere = $true
                Write-Host "Found the open document: $($doc.Name)"
                break
            }
        }
    }
} catch {
    # Word is not running
    Write-Host "Word is not running. Launching new instance..."
}

if ($doc -eq $null) {
    try {
        if ($word -eq $null) {
            $word = New-Object -ComObject Word.Application
        }
        $word.Visible = $false
        $doc = $word.Documents.Open($desktop_path)
        Write-Host "Opened document from file path."
    } catch {
        Write-Warning "Could not open document: $_"
        exit 1
    }
}

# Update Table of Contents
Write-Host "Updating Table of Contents..."
$updated = $false

# Method 1
try {
    if ($doc.TablesOfContents.Count -ge 1) {
        for ($i = 1; $i -le $doc.TablesOfContents.Count; $i++) {
            $doc.TablesOfContents.Item($i).Update()
            Write-Host "Updated TOC item $i."
            $updated = $true
        }
    }
} catch {
    Write-Warning "TOC update method 1 failed: $_"
}

# Method 2
try {
    for ($i = 1; $i -le $doc.Fields.Count; $i++) {
        $field = $doc.Fields.Item($i)
        if ($field.Type -eq 8) {
            $field.Update()
            Write-Host "Updated TOC field $i."
            $updated = $true
        }
    }
} catch {
    Write-Warning "TOC update method 2 failed: $_"
}

if (-not $updated) {
    Write-Warning "No specific TOC found. Updating all fields..."
    try {
        $doc.Fields.Update()
    } catch {}
}

# Save if we opened it, or tell user to save
if ($isOpenHere) {
    # Active document in user's Word instance
    $doc.Save()
    Write-Host "Saved the active document in MS Word."
} else {
    $doc.Save()
    $doc.Close()
    $word.Quit()
    Write-Host "Saved and closed the file."
}

Write-Host "Done!"
