<?php /** @var array $settings */
$preloaderDuration = max(0, min(3000, (int) ($settings['preloader_duration'] ?? 400)));
$preloaderText = $settings['preloader_text'] ?? 'CRC Vinç';
?>
<div id="preloader" class="preloader" role="status" aria-label="Sayfa yükleniyor" data-min-duration="<?= $preloaderDuration ?>">
  <svg class="preloader-crane" viewBox="0 0 220 200" width="132" height="120" aria-hidden="true" focusable="false">
    <rect x="58" y="184" width="74" height="8" rx="2" fill="var(--accent)"/>
    <rect x="91" y="50" width="8" height="136" fill="var(--accent)"/>
    <rect x="52" y="50" width="42" height="7" fill="var(--accent)"/>
    <rect x="52" y="58" width="17" height="17" fill="#2a313d"/>
    <rect x="95" y="50" width="98" height="7" fill="var(--accent)"/>
    <g class="preloader-hookgroup">
      <line x1="175" y1="57" x2="175" y2="91" stroke="#aab2bf" stroke-width="2.5"/>
      <g transform="translate(175,91)">
        <path d="M-9,0 Q-9,-9 0,-9 Q9,-9 9,0 Q9,7 0,15 Q-9,7 -9,0 Z" fill="var(--accent-strong)"/>
        <rect class="preloader-load" x="-17" y="14" width="34" height="24" rx="3" fill="var(--accent-strong)" stroke="#12161d" stroke-width="1.5"/>
      </g>
    </g>
  </svg>
  <span class="preloader-text"><?= e($preloaderText) ?></span>
</div>
<noscript><style>.preloader { display: none; }</style></noscript>
