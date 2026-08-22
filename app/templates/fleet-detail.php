<?php /** @var array $item */ ?>
<section class="page-hero">
  <div class="container">
    <a class="back-link reveal" data-reveal href="/makine-parki">← Tüm Makine Parkı</a>
    <h1 class="reveal" data-reveal data-reveal-delay="1"><?= e($item['title']) ?></h1>
    <p class="reveal" data-reveal data-reveal-delay="2"><?= e($item['capacity']) ?></p>
  </div>
</section>
<section class="section">
  <div class="container project-detail-grid">
    <div class="reveal" data-reveal>
      <?php if (!empty($item['image'])): ?>
      <div class="project-detail-image"><img src="<?= e($item['image']) ?>" alt="<?= e($item['title']) ?>" loading="lazy"></div>
      <?php else: ?>
      <div class="project-detail-image project-image-placeholder" aria-hidden="true"></div>
      <?php endif; ?>
      <div class="prose">
        <?= paragraphs($item['content'] !== '' ? $item['content'] : $item['desc']) ?>
      </div>
    </div>
    <aside class="project-detail-side reveal" data-reveal data-reveal-delay="1">
      <div class="project-detail-stat">
        <span class="project-stat"><?= e($item['capacity']) ?></span>
        <span>Kapasite Aralığı</span>
      </div>
      <a class="btn btn-primary project-detail-cta" href="/iletisim">Bu Araç İçin Teklif Al</a>
    </aside>
  </div>
</section>
