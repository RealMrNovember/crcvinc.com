<?php /** @var array $site */
$areas = $site['service_areas']['areas'] ?? [];
?>
<section class="page-hero">
  <div class="container">
    <h1 class="reveal" data-reveal><?= e($site['service_areas']['page_title'] ?? 'Hizmet Bölgelerimiz') ?></h1>
    <p class="reveal" data-reveal data-reveal-delay="1"><?= e($site['service_areas']['page_intro'] ?? '') ?></p>
  </div>
</section>
<section class="section">
  <div class="container">
    <div class="card-grid">
      <?php foreach ($areas as $i => $area): ?>
      <article class="card area-card reveal" data-reveal data-reveal-delay="<?= $i % 4 ?>">
        <h3><?= e($area['name'] ?? '') ?></h3>
        <p><?= e($area['desc'] ?? '') ?></p>
      </article>
      <?php endforeach; ?>
    </div>
    <div class="section-cta reveal" data-reveal>
      <a class="btn btn-primary" href="/iletisim">Bölgenizdeki İhtiyacınız İçin Teklif Alın</a>
    </div>
  </div>
</section>
