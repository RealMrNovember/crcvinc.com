<?php /** @var array $site @var array $page */ ?>
<section class="page-hero">
  <div class="container">
    <h1 class="reveal" data-reveal><?= e($page['title'] ?? 'Projelerimiz') ?></h1>
    <p class="reveal" data-reveal data-reveal-delay="1"><?= e($page['body'] ?? '') ?></p>
  </div>
</section>
<section class="section">
  <div class="container">
    <div class="card-grid projects-grid">
      <?php foreach ($site['projects'] as $i => $project): ?>
      <a class="card project-card reveal" href="/projeler/<?= e($project['slug'] ?? '') ?>" data-reveal data-reveal-delay="<?= $i % 3 ?>">
        <?php if (!empty($project['image'])): ?>
        <div class="project-image"><img src="<?= e($project['image']) ?>" alt="<?= e($project['title']) ?>" loading="lazy"></div>
        <?php else: ?>
        <div class="project-image project-image-placeholder" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="project-body">
          <span class="project-stat"><?= e($project['stat']) ?></span>
          <h3><?= e($project['title']) ?></h3>
          <p class="project-client"><?= e($project['client']) ?></p>
          <p><?= e($project['desc']) ?></p>
          <span class="project-more">Detayları Gör →</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
