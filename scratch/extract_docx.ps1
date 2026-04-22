$docxPath = "C:\Users\Maria\Documents\ADSO\2026\MARIAMORA\ACTIVIDAD DE APRENDIZAJE 3 MANEJO DE RESIDUOS EN EL SENA.docx"
$tempPath = "C:\wamp64\www\YUDI_CONSTANZA\farmacia\esefjl\scratch\temp_doc.docx"
Copy-Item $docxPath $tempPath -Force

Add-Type -AssemblyName System.IO.Compression.FileSystem
$zip = [System.IO.Compression.ZipFile]::OpenRead($tempPath)
$entry = $zip.Entries | Where-Object { $_.FullName -eq "word/document.xml" }
$stream = $entry.Open()
$reader = New-Object System.IO.StreamReader($stream)
$content = $reader.ReadToEnd()
$reader.Close()
$stream.Close()
$zip.Dispose()
$text = $content -replace '<[^>]+>', ' '
$text | Out-File -FilePath "C:\wamp64\www\YUDI_CONSTANZA\farmacia\esefjl\scratch\extracted_text.txt" -Encoding utf8
Remove-Item $tempPath
