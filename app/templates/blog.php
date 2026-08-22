<?php /** @var array $site @var array $posts */ ?>
<section class="page-hero">
  <div class="container">
    <h1 class="reveal" data-reveal><?= e($site['blog']['page_title'] ?? 'Blog') ?></h1>
    <p class="reveal" data-reveal data-reveal-delay="1"><?= e($site['blog']['page_intro'] ?? '') ?></p>
  </div>
</section>
<section class="section">
  <div class="container">
    <?php if (empty($posts)): ?>
    <p class="prose">Henüz yayınlanmış bir yazı yok.</p>
    <?php else: ?>
    <div class="card-grid blog-grid">
      <?php foreach ($posts as $i => $post): ?>
      <a class="card blog-card reveal" href="/blog/<?= e($post['slug']) ?>" data-reveal data-reveal-delay="<?= $i % 3 ?>">
        <?php if (!empty($post['image'])): ?>
        <div class="blog-image"><img src="<?= e($post['image']) ?>" alt="<?= e($post['title']) ?>" loading="lazy"></div>
        <?php else: ?>
        <div class="blog-image project-image-placeholder" aria-hidden="true"></div>
        <?php endif; ?>
        <div class="blog-body">
          <span class="blog-date"><?= e(formatTurkishDate($post['date'])) ?></span>
          <h3><?= e($post['title']) ?></h3>
          <p><?= e($post['excerpt']) ?></p>
          <span class="project-more">Devamını Oku →</span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
