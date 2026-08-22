<?php
/** @var array $services */
require_once TEMPLATE_DIR . '/partials/icons.php';
?>
<div class="card-grid">
  <?php foreach ($services as $i => $service): ?>
  <article class="card reveal" data-reveal data-reveal-delay="<?= $i % 4 ?>">
    <div class="card-icon"><?= serviceIcon($service['icon'] ?? 'crane') ?></div>
    <h3><?= e($service['title']) ?></h3>
    <p><?= e($service['desc']) ?></p>
  </article>
  <?php endforeach; ?>
</div>
