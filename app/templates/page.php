<?php /** @var array $page */ ?>
<section class="page-hero">
  <div class="container">
    <h1 class="reveal" data-reveal><?= e($page['title'] ?? '') ?></h1>
  </div>
</section>
<section class="section">
  <div class="container prose reveal" data-reveal>
    <?= paragraphs($page['body'] ?? '') ?>
  </div>
</section>
