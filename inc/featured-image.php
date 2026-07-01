<?php
/**
 * GEM-MAGAZINE ─ アイキャッチ自動生成
 *
 * 記事にアイキャッチ画像が未設定の場合、テーマの背景画像に記事タイトルを
 * オーバーレイした「疑似アイキャッチ」を出力する。
 *
 * 背景画像の解決順（先勝ち）：
 *   1. 外観 > カスタマイズ > GEM-MAGAZINE設定 > 「アイキャッチ背景画像」
 *   2. テーマ同梱の assets/images/featured-bg.jpg（あれば）
 *   3. 無ければ既存の宝石グラデ（.gem-fallback）にフォールバック
 *
 * alt テキストはすべて記事タイトルを自動設定（画像側の alt が空でも代入）。
 *
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;

/* ---------------------------------------------------------
 * カスタマイザー：背景画像の設定
 * ------------------------------------------------------- */
add_action('customize_register', function ($wp_customize) {
    $wp_customize->add_setting('gem_featured_fallback_bg', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ));
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'gem_featured_fallback_bg', array(
        'label'       => __('アイキャッチ背景画像（自動生成用）', 'gem-magazine'),
        'description' => __('アイキャッチ未設定の記事で、タイトルをオーバーレイする背景画像。推奨サイズ 1600×900。', 'gem-magazine'),
        'section'     => 'gem_options',
        'settings'    => 'gem_featured_fallback_bg',
    )));
}, 20);

/**
 * フォールバック背景画像のURLを返す（無ければ空文字）
 */
function gem_featured_bg_url() {
    $url = trim((string) get_theme_mod('gem_featured_fallback_bg', ''));
    if ($url !== '') return esc_url($url);

    // テーマ同梱ファイル（jpg / png / webp の順で自動検出）
    foreach (array('featured-bg.jpg', 'featured-bg.png', 'featured-bg.webp') as $file) {
        $path = get_template_directory()      . '/assets/images/' . $file;
        $uri  = get_template_directory_uri()  . '/assets/images/' . $file;
        if (file_exists($path)) return esc_url($uri);
    }
    return '';
}

/**
 * 記事のアイキャッチを出力（未設定時はタイトルをオーバーレイした疑似アイキャッチ）
 *
 * @param string $size       WP 画像サイズ（'medium_large' / 'large' 等）
 * @param string $wrap_class ラッパー div のクラス（既定 "post-thumb"）
 * @param string $inner_html post-thumb 内に追加で挿入するHTML（例：カテゴリピル）
 */
function gem_post_thumb($size = 'medium_large', $wrap_class = 'post-thumb', $inner_html = '') {
    $title = get_the_title();
    $alt   = esc_attr($title);

    echo '<div class="' . esc_attr($wrap_class) . '">';

    if (has_post_thumbnail()) {
        the_post_thumbnail($size, array('alt' => $alt, 'loading' => 'lazy'));
    } else {
        $bg = gem_featured_bg_url();
        $i  = intval(get_the_ID());
        $gem_class = 'gem-' . ((abs($i) % 4) + 1);

        if ($bg) {
            printf(
                '<div class="gem-thumb-fallback" role="img" aria-label="%1$s" style="background-image:url(%2$s);">'
                    . '<span class="gem-thumb-scrim" aria-hidden="true"></span>'
                    . '<span class="gem-thumb-title">%3$s</span>'
                . '</div>',
                $alt, esc_url($bg), esc_html($title)
            );
        } else {
            printf(
                '<div class="gem-thumb-fallback gem-thumb-fallback--gradient" role="img" aria-label="%1$s">'
                    . '<span class="gem-fallback %2$s" aria-hidden="true"></span>'
                    . '<span class="gem-thumb-scrim" aria-hidden="true"></span>'
                    . '<span class="gem-thumb-title">%3$s</span>'
                . '</div>',
                $alt, esc_attr($gem_class), esc_html($title)
            );
        }
    }

    if ($inner_html !== '') echo $inner_html;
    echo '</div>';
}

/**
 * WP画像の alt が空だった場合、親投稿のタイトルを自動で入れる
 */
add_filter('wp_get_attachment_image_attributes', function ($attr, $attachment, $size) {
    if (isset($attr['alt']) && trim((string) $attr['alt']) !== '') return $attr;

    $post_id = 0;
    if (is_object($attachment) && !empty($attachment->post_parent)) {
        $post_id = $attachment->post_parent;
    }
    if (!$post_id && in_the_loop()) {
        $post_id = get_the_ID();
    }
    if ($post_id) {
        $title = get_the_title($post_id);
        if ($title !== '') $attr['alt'] = esc_attr($title);
    }
    return $attr;
}, 10, 3);
