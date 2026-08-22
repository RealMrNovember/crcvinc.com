<?php
declare(strict_types=1);

/** site.json içeriğini okur (istek başına tek kez). */
function siteContent(): array
{
    static $content = null;
    if ($content === null) {
        $raw = @file_get_contents(DATA_DIR . '/site.json');
        if ($raw === false) {
            throw new RuntimeException('data/site.json okunamadı.');
        }
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('data/site.json geçersiz JSON içeriyor.');
        }
        $content = $decoded;
    }
    return $content;
}

/** site.json'u atomik olarak (önce geçici dosyaya) yazar. */
function saveSiteContent(array $content): bool
{
    $json = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        return false;
    }
    $target = DATA_DIR . '/site.json';
    $tmp = $target . '.tmp';
    if (file_put_contents($tmp, $json, LOCK_EX) === false) {
        return false;
    }
    return rename($tmp, $target);
}

/**
 * Her YouTube URL biçiminden (watch, youtu.be, shorts, embed) video ID çıkarır.
 * Çıplak 11 karakterlik ID de kabul edilir. Bulamazsa boş döner.
 */
function youtubeId(string $input): string
{
    $input = trim($input);
    if ($input === '') {
        return '';
    }
    if (preg_match('/^[A-Za-z0-9_-]{11}$/', $input)) {
        return $input;
    }
    $patterns = [
        '/[?&]v=([A-Za-z0-9_-]{11})/',
        '~youtu\.be/([A-Za-z0-9_-]{11})~',
        '~youtube\.com/(?:embed|shorts|live)/([A-Za-z0-9_-]{11})~',
    ];
    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $input, $m)) {
            return $m[1];
        }
    }
    return '';
}
