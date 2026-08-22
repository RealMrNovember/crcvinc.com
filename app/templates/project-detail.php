<?php /** @var array $project @var array $settings */ ?>
<section class="page-hero">
  <div class="container">
    <a class="back-link reveal" data-reveal href="/projeler">← Tüm Projeler</a>
    <h1 class="reveal" data-reveal data-reveal-delay="1"><?= e($project['title']) ?></h1>
    <p class="reveal" data-reveal data-reveal-delay="2"><?= e($project['client']) ?></p>
  </div>
</section>
<section class="section">
  <div class="container project-detail-grid">
    <div class="reveal" data-reveal>
      <?php if (!empty($project['image'])): ?>
      <div class="project-detail-image"><img src="<?= e($project['image']) ?>" alt="<?= e($project['title']) ?>" loading="lazy"></div>
      <?php else: ?>
      <div class="project-detail-image project-image-placeholder" aria-hidden="true"></div>
      <?php endif; ?>
      <div class="prose">
        <?= paragraphs($project['content'] !== '' ? $project['content'] : $project['desc']) ?>
      </div>
    </div>
    <aside class="project-detail-side reveal" data-reveal data-reveal-delay="1">
      <?php if (!empty($project['stat'])): ?>
      <div class="project-detail-stat">
        <span class="project-stat"><?= e($project['stat']) ?></span>
        <span>Proje Kapasitesi</span>
      </div>
      <?php endif; ?>
      <a class="btn btn-primary project-detail-cta" href="/iletisim">Benzer Bir Proje mi Planlıyorsunuz?</a>
    </aside>
  </div>
</section>
