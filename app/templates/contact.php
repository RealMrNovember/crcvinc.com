<?php /** @var array $settings @var array $site @var array $errors @var bool $sent @var array $old */ ?>
<section class="page-hero">
  <div class="container">
    <h1 class="reveal" data-reveal><?= e($site['contact']['hero_title']) ?></h1>
    <p class="reveal" data-reveal data-reveal-delay="1"><?= e($site['contact']['hero_subtitle']) ?></p>
  </div>
</section>
<section class="section">
  <div class="container contact-grid">
    <div class="contact-info reveal" data-reveal>
      <h2><?= e($site['contact']['info_title']) ?></h2>
      <ul class="contact-list">
        <li><strong>Telefon</strong><a href="tel:<?= e($settings['phone']) ?>"><?= e($settings['phone_display']) ?></a></li>
        <li><strong>WhatsApp</strong><a href="https://wa.me/<?= e($settings['whatsapp']) ?>" target="_blank" rel="noopener">Mesaj gönderin</a></li>
        <li><strong>E-posta</strong><a href="mailto:<?= e($settings['email']) ?>"><?= e($settings['email']) ?></a></li>
        <li><strong>Adres</strong><span><?= e($settings['address']) ?></span></li>
      </ul>
      <?php if (!empty($settings['map_embed'])): ?>
      <div class="contact-map"><?= $settings['map_embed'] /* admin panelden gelen iframe */ ?></div>
      <?php endif; ?>
    </div>
    <div class="contact-form-wrap reveal" data-reveal data-reveal-delay="1">
      <h2><?= e($site['contact']['form_title']) ?></h2>
      <?php if ($sent): ?>
      <div class="form-success">Mesajınız alındı. En kısa sürede size döneceğiz.</div>
      <?php endif; ?>
      <?php if ($errors): ?>
      <div class="form-errors">
        <?php foreach ($errors as $error): ?><p><?= e($error) ?></p><?php endforeach; ?>
      </div>
      <?php endif; ?>
      <form method="post" action="/iletisim" class="contact-form">
        <div class="form-hp">
          <label>Bu alanı boş bırakın<input type="text" name="company_website" tabindex="-1" autocomplete="off"></label>
        </div>
        <label>Ad Soyad / Firma*
          <input type="text" name="name" value="<?= e($old['name']) ?>" required>
        </label>
        <div class="form-row">
          <label>Telefon
            <input type="tel" name="phone" value="<?= e($old['phone']) ?>">
          </label>
          <label>E-posta
            <input type="email" name="email" value="<?= e($old['email']) ?>">
          </label>
        </div>
        <label>Mesajınız* <span class="form-hint">(iş tanımı, tonaj, tarih, saha konumu)</span>
          <textarea name="message" rows="5" required><?= e($old['message']) ?></textarea>
        </label>
        <button type="submit" class="btn btn-primary">Gönder</button>
      </form>
    </div>
  </div>
</section>
