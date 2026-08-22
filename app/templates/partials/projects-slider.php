<?php /** @var array $projects */ ?>
<div class="slider" data-slider>
  <div class="slider-track">
    <?php foreach ($projects as $project): ?>
    <article class="slide project-card">
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
      </div>
    </article>
    <?php endforeach; ?>
  </div>
  <div class="slider-nav">
    <button type="button" data-slider-prev aria-label="Önceki proje">&#8592;</button>
    <button type="button" data-slider-next aria-label="Sonraki proje">&#8594;</button>
  </div>
</div>
