<?php
/** @var array $services */
require_once TEMPLATE_DIR . '/partials/icons.php';
?>
<div class="card-grid">
  <?php foreach ($services as $i => $service): ?>
  <a class="card service-card reveal" href="/hizmetler/<?= e($service['slug'] ?? '') ?>" data-reveal data-reveal-delay="<?= $i % 4 ?>">
    <div class="card-icon"><?= serviceIcon($service['icon'] ?? 'crane') ?></div>
    <h3><?= e($service['title']) ?></h3>
    <p><?= e($service['desc']) ?></p>
    <span class="project-more">Detayları Gör →</span>
  </a>
  <?php endforeach; ?>
</div>
