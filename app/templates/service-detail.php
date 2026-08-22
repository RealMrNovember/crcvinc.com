<?php /** @var array $service */
require_once TEMPLATE_DIR . '/partials/icons.php';
?>
<section class="page-hero">
  <div class="container">
    <a class="back-link reveal" data-reveal href="/hizmetler">← Tüm Hizmetler</a>
    <h1 class="reveal" data-reveal data-reveal-delay="1"><?= e($service['title']) ?></h1>
    <p class="reveal" data-reveal data-reveal-delay="2"><?= e($service['desc']) ?></p>
  </div>
</section>
<section class="section">
  <div class="container project-detail-grid">
    <div class="prose reveal" data-reveal>
      <?= paragraphs($service['content'] !== '' ? $service['content'] : $service['desc']) ?>
    </div>
    <aside class="project-detail-side reveal" data-reveal data-reveal-delay="1">
      <div class="project-detail-stat project-detail-icon">
        <?= serviceIcon($service['icon'] ?? 'crane') ?>
      </div>
      <a class="btn btn-primary project-detail-cta" href="/iletisim">Bu Hizmet İçin Teklif Al</a>
    </aside>
  </div>
</section>
