<?php
$dir = __DIR__ . '/..';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$replacements = [
    'ó' => 'ó',
    'á' => 'á',
    'é' => 'é',
    'í' => 'í',
    'ú' => 'ú',
    'ñ' => 'ñ',
    'Ñ' => 'Ñ',
    'í' => 'í', // A veces se rompe así
    '⚠️' => '⚠️',
    '✅' => '✅',
    '❌' => '❌',
    '°' => '°'
];

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getRealPath());
        $newContent = strtr($content, $replacements);
        if ($content !== $newContent) {
            file_put_contents($file->getRealPath(), $newContent);
            echo "Fixed: " . $file->getFilename() . "\n";
        }
    }
}
?>
