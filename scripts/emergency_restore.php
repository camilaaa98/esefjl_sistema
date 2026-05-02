<?php
$dir = __DIR__ . '/../resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$replacements = [
    '<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/tailwind.css">' => '<script src="https://cdn.tailwindcss.com"></script><script>tailwind.config={theme:{extend:{colors:{"elite-gold":"#d4af37","elite-dark":"#111111"}}}}</script>',
    'ðŸ“Š' => '📊',
    'ðŸ¥' => '🏠',
    'âŒ›' => '⌛',
    'ðŸ‘¤' => '👤',
    'ðŸš›' => '🚚',
    'ðŸ“œ' => '📜',
    'ðŸšª' => '🚪',
    'ðŸš' => '🚪',
    'âš ï¸ ' => '⚠️',
    'âœ…' => '✅',
    'â Œ' => '❌',
    'â€”' => '—',
    'Â°' => '°',
    'Ã³' => 'ó',
    'Ã¡' => 'á',
    'Ã©' => 'é',
    'Ã­' => 'í',
    'Ãº' => 'ú',
    'Ã±' => 'ñ'
];

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getRealPath());
        $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
        if ($content !== $newContent) {
            file_put_contents($file->getRealPath(), $newContent);
            echo "Restored & Fixed: " . $file->getFilename() . "\n";
        }
    }
}
?>
