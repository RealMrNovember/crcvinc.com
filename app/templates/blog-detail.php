<?php /** @var array $post */ ?>
<section class="page-hero">
  <div class="container">
    <a class="back-link reveal" data-reveal href="/blog">← Tüm Yazılar</a>
    <h1 class="reveal" data-reveal data-reveal-delay="1"><?= e($post['title']) ?></h1>
    <p class="reveal" data-reveal data-reveal-delay="2"><?= e(formatTurkishDate($post['date'])) ?></p>
  </div>
</section>
<section class="section">
  <div class="container">
    <div class="blog-detail reveal" data-reveal>
      <?php if (!empty($post['image'])): ?>
      <div class="blog-detail-image"><img src="<?= e($post['image']) ?>" alt="<?= e($post['title']) ?>" loading="lazy"></div>
      <?php endif; ?>
      <div class="prose">
        <?= paragraphs($post['content']) ?>
      </div>
    </div>
  </div>
</section>
