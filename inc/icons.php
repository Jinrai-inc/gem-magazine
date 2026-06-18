<?php
/**
 * インラインSVGアイコン集
 * 使い方: echo gem_icon('loupe');  ／  gem_icon('chevron', 14, '#fff')
 */
if (!defined('ABSPATH')) exit;

function gem_icon_map() {
    return array(
        'logo' => '<svg width="%1$d" height="%1$d" viewBox="0 0 32 32" fill="none"><path d="M9 4h14l6 8-13 16L3 12z" stroke="%2$s" stroke-width="1.6" stroke-linejoin="round"/><path d="M3 12h26M11 4l-2 8 7 16 7-16-2-8M9 12l7 7 7-7" stroke="%2$s" stroke-width="1.1" stroke-linejoin="round" opacity="0.75"/></svg>',
        'search' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.8" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>',
        'menu' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.8" stroke-linecap="round"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>',
        'chevron' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 6 15 12 9 18"/></svg>',
        'clock' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><polyline points="12 7 12 12 15 14"/></svg>',
        'check' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
        'gift' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>',
        'book-open' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M2 4h7a3 3 0 0 1 3 3v13a2.5 2.5 0 0 0-2.5-2.5H2z"/><path d="M22 4h-7a3 3 0 0 0-3 3v13a2.5 2.5 0 0 1 2.5-2.5H22z"/></svg>',
        'loupe' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="10.5" cy="10.5" r="6.5"/><line x1="21" y1="21" x2="15.5" y2="15.5"/><path d="M8 10.5l2.5-3 2.5 3-2.5 3z"/></svg>',
        'receipt' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 3h14v18l-2.5-1.5L14 21l-2-1.5L10 21l-2.5-1.5L5 21z"/><path d="M10 8l2 2.4L14 8M12 10.4V14M9.5 11.4h5"/></svg>',
        'book' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M3 5h7a2.5 2.5 0 0 1 2 1 2.5 2.5 0 0 1 2-1h7v13h-7a2.5 2.5 0 0 0-2 1 2.5 2.5 0 0 0-2-1H3z"/><line x1="12" y1="6" x2="12" y2="19"/></svg>',
        'flag' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 21V4M5 4h11l-2 3 2 3H5"/></svg>',
        'phone' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="2" width="12" height="20" rx="2.5"/><circle cx="12" cy="9" r="3"/><path d="M10.6 9l1 1 1.8-2"/><line x1="10" y1="18" x2="14" y2="18"/></svg>',
        'learn' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 6h7a2 2 0 0 1 2 2v11a2 2 0 0 0-2-1.5H4zM20 6h-7a2 2 0 0 0-2 2"/></svg>',
        'gem' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12l3 5-9 13L3 8z"/><path d="M3 8h18M8 3l-2 5 6 13 6-13-2-5"/></svg>',
        'shield' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8 3v6c0 5-3.5 8.5-8 11-4.5-2.5-8-6-8-11V5z"/><polyline points="8.5 12 11 14.5 15.5 9.5"/></svg>',
        'yen' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M9 8l3 3.5L15 8M12 11.5V17M9.5 12.5h5M9.5 15h5"/></svg>',
        'chat' => '<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="%2$s" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h12a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H9l-4 3v-3a2 2 0 0 1-1-2V7a2 2 0 0 1 0-2z"/><path d="M20 9h0a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2v3l-4-3"/></svg>',
        'leaf' => '<svg width="%1$d" height="%3$d" viewBox="0 0 20 26" fill="none" stroke="%2$s" stroke-width="1.4"><path d="M14 2C9 5 6 9 6 14c0 5 3 8 3 8M16 4c-4 3-6 7-6 12 0 6 4 8 4 8"/></svg>',
        'flourish' => '<svg width="%1$d" height="%3$d" viewBox="0 0 32 12" fill="none" stroke="%2$s" stroke-width="1.2"><path d="M2 6c6-6 10-6 14 0 4 6 8 6 14 0"/><circle cx="16" cy="6" r="1.4" fill="%2$s" stroke="none"/></svg>',
    );
}

/**
 * @param string $name  アイコン名
 * @param int    $size  サイズ(px)
 * @param string $color stroke色（currentColor可）
 * @param int    $h     一部アイコンの高さ（leaf/flourish用）
 */
function gem_icon($name, $size = 24, $color = 'currentColor', $h = null) {
    $map = gem_icon_map();
    if (!isset($map[$name])) return '';
    if ($h === null) $h = $size;
    return sprintf($map[$name], (int) $size, esc_attr($color), (int) $h);
}
