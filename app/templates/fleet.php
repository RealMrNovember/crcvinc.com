<?php /** @var array $site @var array $page */ ?>
<section class="page-hero">
  <div class="container">
    <h1 class="reveal" data-reveal><?= e($page['title'] ?? 'Makine Parkı') ?></h1>
    <p class="reveal" data-reveal data-reveal-delay="1"><?= e($page['body'] ?? '') ?></p>
  </div>
</section>
<section class="section">
  <div class="container">
    <?= render('partials/fleet-grid', ['fleet' => $site['fleet']]) ?>
    <div class="section-cta reveal" data-reveal>
      <a class="btn btn-primary" href="/iletisim">Bu Araçlar İçin Teklif Al</a>
    </div>
  </div>
</section>
