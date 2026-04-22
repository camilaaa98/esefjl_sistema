$files = Get-ChildItem -Path "c:\wamp64\www\YUDI_CONSTANZA\farmacia\esefjl\docs" -Filter "*.html" -Recurse

$replacements = @{
    "&iacute;±" = "ñ"
    "&iacute;³n" = "ón"
    "&iacute;³" = "ó"
    "&iacute;ºn" = "ún"
    "&iacute;­" = "í"
    "&iacute;" = "í"
    "&aacute;" = "á"
    "&eacute;" = "é"
    "&oacute;" = "ó"
    "&uacute;" = "ú"
    "&ntilde;" = "ñ"
    "â€""" = "-"
    """&iacute; rea" = """Área"
    "&iacute; rea" = "Área"
    "âš ï¸" = "⚠️"
    "âœ…" = "✅"
    "ðŸ›‘" = "🛑"
    "â€œ" = """"
    "â€" = """"
    "R&eacute;gimen" = "Régimen"
    "Est&aacute;ndar" = "Estándar"
    "&Aacute;" = "Á"
    "&Eacute;" = "É"
    "&Iacute;" = "Í"
    "&Oacute;" = "Ó"
    "&Uacute;" = "Ú"
    "&Ntilde;" = "Ñ"
    "¡ndares" = "ándares"
}

foreach ($file in $files) {
    if ($file.Extension -eq ".html") {
        $content = Get-Content $file.FullName -Raw -Encoding UTF8
        
        foreach ($key in $replacements.Keys) {
            $content = $content.Replace($key, $replacements[$key])
        }
        
        Set-Content -Path $file.FullName -Value $content -Encoding UTF8
        Write-Host "Fixed encoding in $($file.Name)"
    }
}
