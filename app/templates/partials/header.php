<?php /** @var array $settings @var array $site */ ?>
<div class="topbar">
  <div class="container topbar-inner">
    <span class="topbar-badge">7/24 Hizmet</span>
    <a class="topbar-phone" href="tel:<?= e($settings['phone']) ?>">
      <svg viewBox="0 0 24 24" width="14" height="14" fill="currentColor" aria-hidden="true"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.2.2 2.4.6 3.6.1.3 0 .7-.2 1l-2.3 2.2z"/></svg>
      <?= e($settings['phone_display']) ?>
    </a>
  </div>
</div>
<header class="site-header" data-header>
  <div class="container header-inner">
    <a class="brand" href="/">
      <span class="brand-mark" aria-hidden="true">CRC</span>
      <span class="brand-text"><?= e($settings['site_name']) ?></span>
    </a>
    <nav class="site-nav" data-nav aria-label="Ana menü">
      <?php foreach ($site['menu'] as $item): ?>
      <a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a>
      <?php endforeach; ?>
      <a class="nav-cta" href="/iletisim">Teklif Al</a>
    </nav>
    <button class="nav-toggle" data-nav-toggle aria-label="Menüyü aç/kapat" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>
