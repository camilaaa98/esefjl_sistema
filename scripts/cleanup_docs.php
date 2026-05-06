<?php
$files = [
    'docs/manual_usuario.html',
    'docs/manual_tecnico.html',
    'docs/articulo_cientifico.html',
    'docs/manual_usuario_en.html',
    'docs/manual_tecnico_en.html',
    'docs/articulo_cientifico_en.html'
];

foreach ($files as $f) {
    if (!file_exists($f)) continue;
    $content = file_get_contents($f);
    
    // Fix mojibake and broken characters in HTML docs
    $replacements = [
        'í rea' => 'Área',
        'í‰' => 'É',
        'í í‰í í“íšÑ' => 'ÁÉÍÓÚÑ',
        'níºmero' => 'número',
        'comuníquese' => 'comuníquese',
        'í\x81' => 'Á',
        'Â¿' => '¿',
        'Â¡' => '¡',
        'Ã³' => 'ó',
        'Ã¡' => 'á',
        'Ã©' => 'é',
        'Ã' => 'í',
        'Ãº' => 'ú',
        'Ã±' => 'ñ',
        'Ã‘' => 'Ñ',
        'â€“' => '—',
        'â€”' => '—',
        'â€œ' => '“',
        'â€\x9D' => '”',
        'â€\x98' => '‘',
        'â€\x99' => '’',
        'â€¢' => '•'
    ];
    
    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    
    // Specific cleanup for manual_usuario.html
    $content = str_replace('níºmero', 'número', $content);
    $content = str_replace('í rea', 'Área', $content);
    
    file_put_contents($f, $content);
    echo "Cleaned Docs: $f\n";
}
echo "Done.";
