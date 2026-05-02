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
        $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
        if ($content !== $newContent) {
            file_put_contents($file->getRealPath(), $newContent);
            echo "Cleaned (str_replace): " . $file->getFilename() . "\n";
        }
    }
}
?>
