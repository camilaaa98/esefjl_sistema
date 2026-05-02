<?php
$dir = __DIR__ . '/../resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$replacements = [
    '<script src="<?= BASE_URL ?>/public/js/tailwind.js"></script>' => '<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/tailwind.css">',
    '<script src="https://cdn.tailwindcss.com"></script>' => '<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/tailwind.css">'
];

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getRealPath());
        $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
        if ($content !== $newContent) {
            file_put_contents($file->getRealPath(), $newContent);
            echo "Migrated to CSS: " . $file->getFilename() . "\n";
        }
    }
}
?>
