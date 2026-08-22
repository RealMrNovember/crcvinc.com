<?php
/** @var array $site @var array $settings */
require_once TEMPLATE_DIR . '/partials/icons.php';
$videoId = youtubeId($settings['hero_video_id'] ?? '');
?>
<!-- HERO -->
<section class="hero">
  <?php if ($videoId !== ''): ?>
  <div class="hero-video" aria-hidden="true">
    <iframe
      src="https://www.youtube-nocookie.com/embed/<?= e($videoId) ?>?autoplay=1&mute=1&loop=1&playlist=<?= e($videoId) ?>&controls=0&showinfo=0&rel=0&modestbranding=1&playsinline=1&disablekb=1"
      title="Tanıtım videosu" frameborder="0" allow="autoplay; encrypted-media" tabindex="-1"></iframe>
  </div>
  <?php else: ?>
  <div class="hero-fallback" aria-hidden="true"></div>
  <?php endif; ?>
  <div class="hero-overlay" aria-hidden="true"></div>
  <div class="container hero-content">
    <p class="hero-kicker reveal" data-reveal><?= e($settings['hero_kicker']) ?></p>
    <h1 class="hero-title reveal" data-reveal data-reveal-delay="1"><?= e($settings['hero_title']) ?></h1>
    <p class="hero-subtitle reveal" data-reveal data-reveal-delay="2"><?= e($settings['hero_subtitle']) ?></p>
    <div class="hero-actions reveal" data-reveal data-reveal-delay="3">
      <a class="btn btn-primary" href="/iletisim"><?= e($settings['hero_cta_primary']) ?></a>
      <a class="btn btn-ghost" href="/makine-parki"><?= e($settings['hero_cta_secondary']) ?></a>
    </div>
  </div>
  <div class="hero-scroll" aria-hidden="true"><span></span></div>
</section>

<!-- SAYAÇLAR -->
<section class="counters">
  <div class="container counters-grid">
    <?php foreach ($site['counters'] as $i => $counter): ?>
    <div class="counter reveal" data-reveal data-reveal-delay="<?= $i ?>">
      <span class="counter-value" data-counter="<?= (int) $counter['value'] ?>">0</span><span class="counter-suffix"><?= e($counter['suffix']) ?></span>
      <span class="counter-label"><?= e($counter['label']) ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- HİZMETLER -->
<section class="section">
  <div class="container">
    <div class="section-head reveal" data-reveal>
      <p class="section-kicker"><?= e($site['home']['services_kicker']) ?></p>
      <h2><?= e($site['home']['services_title']) ?></h2>
    </div>
    <?= render('partials/services-grid', ['services' => $site['services']]) ?>
  </div>
</section>

<!-- MAKİNE PARKI -->
<section class="section section-dark">
  <div class="container">
    <div class="section-head reveal" data-reveal>
      <p class="section-kicker"><?= e($site['home']['fleet_kicker']) ?></p>
      <h2><?= e($site['home']['fleet_title']) ?></h2>
    </div>
    <?= render('partials/fleet-grid', ['fleet' => $site['fleet']]) ?>
    <div class="section-cta reveal" data-reveal>
      <a class="btn btn-primary" href="/makine-parki">Tüm Makine Parkı</a>
    </div>
  </div>
</section>

<!-- PROJELER -->
<section class="section">
  <div class="container">
    <div class="section-head reveal" data-reveal>
      <p class="section-kicker"><?= e($site['home']['projects_kicker']) ?></p>
      <h2><?= e($site['home']['projects_title']) ?></h2>
    </div>
  </div>
  <?= render('partials/projects-slider', ['projects' => $site['projects']]) ?>
</section>

<!-- REFERANSLAR -->
<?php if (!empty($site['clients'])): ?>
<section class="clients" aria-label="Referanslarımız">
  <div class="clients-track" data-marquee>
    <?php for ($pass = 0; $pass < 2; $pass++): ?>
      <?php foreach ($site['clients'] as $client): ?>
      <span class="client-logo"<?= $pass === 1 ? ' aria-hidden="true"' : '' ?>><?= e($client) ?></span>
      <?php endforeach; ?>
    <?php endfor; ?>
  </div>
</section>
<?php endif; ?>

<!-- İSG ŞERİDİ -->
<?php if (!empty($site['safety'])): ?>
<section class="safety">
  <div class="container safety-grid">
    <?php foreach ($site['safety'] as $i => $item): ?>
    <div class="safety-item reveal" data-reveal data-reveal-delay="<?= $i % 4 ?>"><strong><?= e($item['title']) ?></strong><span><?= e($item['desc']) ?></span></div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- CTA -->
<section class="cta-band">
  <div class="container cta-inner reveal" data-reveal>
    <h2><?= e($site['home']['cta_title']) ?></h2>
    <p><?= e($site['home']['cta_subtitle']) ?></p>
    <div class="hero-actions">
      <a class="btn btn-primary" href="/iletisim">Teklif Al</a>
      <a class="btn btn-ghost" href="tel:<?= e($settings['phone']) ?>">Hemen Ara: <?= e($settings['phone_display']) ?></a>
    </div>
  </div>
</section>
