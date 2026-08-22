<?php
/** Hizmet kartı ikonları — anahtar: site.json services[].icon */
function serviceIcon(string $name): string
{
    $icons = [
        'crane' => '<path d="M4 44h40M10 44V20l16-12v8h14v6H26v22M26 16l-16 4M38 22v10a4 4 0 1 0 4 4" stroke-linecap="round" stroke-linejoin="round"/>',
        'basket' => '<path d="M14 44h20M24 44V30M10 30h28l-4-10H14l-4 10zM24 20V8m0 0-5 5m5-5 5 5" stroke-linecap="round" stroke-linejoin="round"/>',
        'truck' => '<path d="M2 34h28V14H2v20zm28-12h10l6 8v4h-16M10 40a4 4 0 1 0 0-8 4 4 0 0 0 0 8zm26 0a4 4 0 1 0 0-8 4 4 0 0 0 0 8z" stroke-linecap="round" stroke-linejoin="round"/>',
        'gear' => '<path d="M24 30a6 6 0 1 0 0-12 6 6 0 0 0 0 12zm16-6 4-2-2-7-4.5.5a14 14 0 0 0-3-3L35 8l-7-2-2 4a14 14 0 0 0-4 0l-2-4-7 2 .5 4.5a14 14 0 0 0-3 3L6 15l-2 7 4 2a14 14 0 0 0 0 4l-4 2 2 7 4.5-.5a14 14 0 0 0 3 3L13 44l7 2 2-4a14 14 0 0 0 4 0l2 4 7-2-.5-4.5a14 14 0 0 0 3-3l4.5.5 2-7-4-2a14 14 0 0 0 0-4z" stroke-linejoin="round"/>',
    ];
    $path = $icons[$name] ?? $icons['crane'];
    return '<svg viewBox="0 0 48 48" width="44" height="44" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">' . $path . '</svg>';
}
