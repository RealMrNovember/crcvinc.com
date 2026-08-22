<?php /** @var array $fleet */ ?>
<div class="card-grid fleet-grid">
  <?php foreach ($fleet as $i => $item): ?>
  <article class="card fleet-card reveal" data-reveal data-reveal-delay="<?= $i % 3 ?>">
    <?php if (!empty($item['image'])): ?>
    <div class="fleet-image"><img src="<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>" loading="lazy"></div>
    <?php endif; ?>
    <h3><?= e($item['title']) ?></h3>
    <span class="fleet-capacity"><?= e($item['capacity']) ?></span>
    <p><?= e($item['desc']) ?></p>
  </article>
  <?php endforeach; ?>
</div>
