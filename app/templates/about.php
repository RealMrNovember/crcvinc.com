<?php /** @var array $site @var array $page */
$founder = $site['founder'] ?? [];
?>
<section class="page-hero">
  <div class="container">
    <h1 class="reveal" data-reveal><?= e($page['title'] ?? '') ?></h1>
  </div>
</section>
<section class="section">
  <div class="container about-grid">
    <div class="prose reveal" data-reveal>
      <?= paragraphs($page['body'] ?? '') ?>
    </div>
    <?php if (!empty($founder['name'])): ?>
    <aside class="founder-card reveal" data-reveal data-reveal-delay="1">
      <span class="founder-avatar" aria-hidden="true"><?= e(firstChar($founder['name'])) ?></span>
      <span class="founder-name"><?= e($founder['name']) ?></span>
      <span class="founder-title"><?= e($founder['title'] ?? '') ?></span>
    </aside>
    <?php endif; ?>
  </div>
</section>
