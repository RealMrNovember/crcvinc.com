<?php
declare(strict_types=1);

define('APP_DIR', __DIR__);
define('DATA_DIR', dirname(__DIR__) . '/data');
define('TEMPLATE_DIR', APP_DIR . '/templates');

require APP_DIR . '/content.php';

/** HTML çıktısı için güvenli kaçış. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** Düz metni paragraf etiketlerine çevirir (admin "body" alanları için). */
function paragraphs(?string $text): string
{
    if ($text === null || trim($text) === '') {
        return '';
    }
    $blocks = preg_split('/\R{2,}/u', trim($text)) ?: [];
    $html = '';
    foreach ($blocks as $block) {
        $html .= '<p>' . nl2br(e(trim($block))) . '</p>';
    }
    return $html;
}

/** Şablonu verilen değişkenlerle çalıştırıp çıktıyı döndürür. */
function render(string $template, array $vars = []): string
{
    $file = TEMPLATE_DIR . '/' . $template . '.php';
    if (!is_file($file)) {
        throw new RuntimeException("Şablon bulunamadı: {$template}");
    }
    extract($vars, EXTR_SKIP);
    ob_start();
    require $file;
    return (string) ob_get_clean();
}

/** Sayfayı ana yerleşim (layout) içinde basar. */
function renderPage(string $template, array $vars = []): void
{
    $site = siteContent();
    $vars['site'] = $site;
    $vars['settings'] = $site['settings'];
    $content = render($template, $vars);
    echo render('layout', $vars + ['content' => $content]);
}
