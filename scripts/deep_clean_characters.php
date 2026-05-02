<?php
$dir = __DIR__ . '/../resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$replacements = [
    'ðŸ“Š' => '📊',
    'ðŸ¥' => '🏠',
    'âŒ›' => '⌛',
    'ðŸ‘¤' => '👤',
    'ðŸš›' => '🚚',
    'ðŸ“œ' => '📜',
    'ðŸšª' => '🚪',
    'ðŸ’Š' => '💊',
    'âš ï¸ ' => '⚠️',
    'âœ…' => '✅',
    'â Œ' => '❌',
    'â€”' => '—',
    'Â°' => '°'
];

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getRealPath());
        $newContent = strtr($content, $replacements);
        if ($content !== $newContent) {
            file_put_contents($file->getRealPath(), $newContent);
            echo "Cleaned: " . $file->getFilename() . "\n";
        }
    }
}
?>
