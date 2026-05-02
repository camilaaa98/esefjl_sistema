<?php
$dir = __DIR__ . '/../resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getRealPath());
        // Buscar class="content-area ..." y agregar lg:ml-[260px] si no lo tiene
        if (preg_match('/class="[^"]*content-area[^"]*"/', $content, $matches)) {
            if (strpos($matches[0], 'lg:ml-[260px]') === false) {
                $newClass = str_replace('content-area', 'content-area lg:ml-[260px]', $matches[0]);
                $newContent = str_replace($matches[0], $newClass, $content);
                file_put_contents($file->getRealPath(), $newContent);
                echo "Fixed (Regex): " . $file->getFilename() . "\n";
            }
        }
    }
}
?>
