$files = @('vencidos','sedes','solicitud_municipio','reportes','registro_paciente','proveedores','historial','aprobacion_pedidos','asignacion_personal','admin_usuarios','inventario_central','registro_entrega')
$base = 'c:\wamp64\www\YUDI_CONSTANZA\farmacia\esefjl\views\'
foreach ($f in $files) {
    $path = $base + $f + '.php'
    $c = Get-Content $path -Raw
    if ($c -notmatch 'animations\.js') {
        $tag = '    <script src="../assets/js/animations.js" defer></script>' + "`r`n" + '</body>'
        $c = $c -replace '</body>', $tag
        Set-Content $path $c -Encoding UTF8 -NoNewline
        Write-Host "Updated: $f"
    } else {
        Write-Host "Skip (already has animations): $f"
    }
}
