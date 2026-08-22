<?php /** @var array $site */ ?>
<nav class="mobile-menu" data-mobile-menu aria-label="Mobil menü">
  <?php foreach ($site['menu'] as $item): ?>
  <a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a>
  <?php endforeach; ?>
  <a class="nav-cta" href="/iletisim">Teklif Al</a>
</nav>
