<?php
$dir = __DIR__ . '/../resources/views';
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

$replacements = [
    'https://cdn.tailwindcss.com' => '<?= BASE_URL ?>/public/js/tailwind.js',
    'â€¢' => '•',
    'âš ï¸ ' => '⚠️',
    'âœ…' => '✅',
    'â Œ' => '❌',
    'â€”' => '—',
    'í¢Å“' => '✅',
    'í¢Â Å’' => '❌',
    'í¢â€ Â ' => '←',
    'í°Å¸â€œâ€¹' => '📋',
    'í°Å¸Å¡â€º' => '🚚',
    'í°Å¸â€˜Â¤' => '👤',
    'í°Å¸â€™°' => '💰',
    'í°Å¸â€œÂ²' => '📱',
    'í°Å¸â€œÅ ' => '📊',
    'í°Å¸Â â€ºí¯Â¸Â ' => '🏠',
    'í¢â€ â€¢' => '↓',
    'í¢Å¡â„¢í¯Â¸Â ' => '⚙️',
    'í°Å¸â€”Æ’í¯Â¸Â ' => '🗄️',
    'í°Å¸â€™Â»' => '💻',
    'í°Å¸Å½Â¨' => '🎨',
    'í°Å¸â€“Â¥í¯Â¸Â ' => '🖥️',
    'í°Å¸â€œÂ¶' => '📶',
    'í°Å¸â€ â€ž' => '🔄',
    'í°Å¸â€œÂ ' => '📍',
    'í¢â€ â€œ' => '↓',
    'í°Å¸â€ â€˜' => '🔑',
    'í°Å¸â€˜Â©í¢â‚¬Â í¢Å¡â€¢í¯Â¸Â ' => '👩‍⚕️',
    'í°Å¸â€™Å ' => '💊',
    'í¢â‚¬Â ' => ' ',
    'í¯Â¸Â ' => '',
    'íƒÂ' => 'í',
    'íƒÂ³' => 'ó',
    'íƒÂ¡' => 'á',
    'íƒÂ©' => 'é',
    'íƒÂº' => 'ú',
    'íƒÂ±' => 'ñ'
];

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getRealPath());
        $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
        if ($content !== $newContent) {
            file_put_contents($file->getRealPath(), $newContent);
            echo "Standardized: " . $file->getFilename() . "\n";
        }
    }
}
?>
