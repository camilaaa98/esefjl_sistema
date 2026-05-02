<?php
$dir = __DIR__ . '/../resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getRealPath());
        if (strpos($content, 'lg:ml-[260px]') !== false) {
            $newContent = str_replace('lg:ml-[260px]', '', $content);
            file_put_contents($file->getRealPath(), $newContent);
            echo "Removed margin from: " . $file->getFilename() . "\n";
        }
    }
}
?>
