<?php /** @var array $settings @var array $site */ ?>
<footer class="site-footer">
  <div class="container footer-grid">
    <div>
      <a class="brand" href="/"><span class="brand-mark" aria-hidden="true">CRC</span> <span class="brand-text"><?= e($settings['site_name']) ?></span></a>
      <p class="footer-about"><?= e($settings['footer_text']) ?></p>
    </div>
    <div>
      <h4>Menü</h4>
      <ul class="footer-links">
        <?php foreach ($site['menu'] as $item): ?>
        <li><a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div>
      <h4>İletişim</h4>
      <ul class="footer-links">
        <li><a href="tel:<?= e($settings['phone']) ?>"><?= e($settings['phone_display']) ?></a></li>
        <li><a href="mailto:<?= e($settings['email']) ?>"><?= e($settings['email']) ?></a></li>
        <li><?= e($settings['address']) ?></li>
      </ul>
      <?php if (!empty($settings['instagram']) || !empty($settings['linkedin'])): ?>
      <div class="footer-social">
        <?php if (!empty($settings['instagram'])): ?><a href="<?= e($settings['instagram']) ?>" target="_blank" rel="noopener">Instagram</a><?php endif; ?>
        <?php if (!empty($settings['linkedin'])): ?><a href="<?= e($settings['linkedin']) ?>" target="_blank" rel="noopener">LinkedIn</a><?php endif; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
  <div class="container footer-bottom">
    <span>© <?= date('Y') ?> <?= e($settings['legal_name'] ?? $settings['site_name']) ?></span>
  </div>
</footer>
