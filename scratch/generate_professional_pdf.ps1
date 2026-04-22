$word = New-Object -ComObject Word.Application
$word.Visible = $false
try {
    $doc = $word.Documents.Add()
    $selection = $word.Selection
    
    # Page Setup (1 inch margins = 72 points)
    $doc.PageSetup.TopMargin = 72
    $doc.PageSetup.BottomMargin = 72
    $doc.PageSetup.LeftMargin = 72
    $doc.PageSetup.RightMargin = 72
    
    # Read text using UTF8 to ensure accents are correct
    $textLines = Get-Content 'c:\wamp64\www\YUDI_CONSTANZA\farmacia\esefjl\scratch\humanized_final.txt' -Encoding utf8
    
    foreach ($line in $textLines) {
        if ($line.Trim() -eq '') {
            $selection.TypeParagraph()
            continue
        }
        
        # Reset formatting for the paragraph
        $selection.Font.Name = 'Times New Roman'
        $selection.Font.Size = 12
        $selection.ParagraphFormat.LineSpacingRule = 2 # Double space
        $selection.ParagraphFormat.Alignment = 0 # Left align (Standard APA Paragraph)
        $selection.ParagraphFormat.FirstLineIndent = 36 # 0.5 inch indent
        
        # Detection of Headers (All CAPS lines like INTRODUCTION)
        if ($line.Trim() -ceq $line.Trim().ToUpper() -and $line.Trim().Length -gt 3) {
            $selection.Font.Bold = $true
            $selection.ParagraphFormat.Alignment = 1 # Centered
            $selection.ParagraphFormat.FirstLineIndent = 0
            $selection.TypeText($line.Trim())
        }
        # Detection of Subtitles (1., 1.1, etc.)
        elseif ($line.Trim() -match '^[0-9](\.[0-9])*') {
            $selection.Font.Bold = $true
            $selection.ParagraphFormat.FirstLineIndent = 0
            $selection.TypeText($line.Trim())
        }
        else {
            $selection.Font.Bold = $false
            $selection.TypeText($line)
        }
        $selection.TypeParagraph()
    }
    
    $pdfPath = 'c:\wamp64\www\YUDI_CONSTANZA\farmacia\esefjl\img\investigacion\COMPORTAMIENTO_ECONOMICO_PROFESIONAL.pdf'
    if (Test-Path $pdfPath) { Remove-Item $pdfPath -Force }
    $doc.SaveAs([ref]$pdfPath, [ref]17) # 17 = wdFormatPDF
    $doc.Close($false)
    Write-Host "SUCCESS: Professional PDF generated at $pdfPath"
}
catch {
    Write-Error "ERROR during PDF generation: $($_.Exception.Message)"
}
finally {
    $word.Quit()
    [System.Runtime.Interopservices.Marshal]::ReleaseComObject($word) | Out-Null
    [GC]::Collect()
    [GC]::WaitForPendingFinalizers()
}
