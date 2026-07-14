<?php
/**
 * GEM-MAGAZINE ─ 表示速度の最適化（PageSpeed / Core Web Vitals 対策）
 *
 * - Google Fonts の preconnect と非同期読み込み（レンダリングブロック解消）
 * - 絵文字関連スクリプト/スタイルの停止（不要なJS/CSS/画像リクエスト削減）
 * - wp_head の不要出力の削減
 * - トップ／記事のLCP画像の優先取得（preload / fetchpriority）
 *
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;

/* -----------------------------------------------------------
 * 1. Google Fonts：preconnect で接続を先行確立
 * --------------------------------------------------------- */
add_filter('wp_resource_hints', function ($urls, $relation_type) {
    if ('preconnect' === $relation_type) {
        $urls[] = 'https://fonts.googleapis.com';
        $urls[] = array('href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous');
    }
    return $urls;
}, 10, 2);

/* -----------------------------------------------------------
 * 2. Google Fonts を非同期読み込み（CSSのレンダリングブロックを解消）
 *    media="print" → onload で all に戻す定番手法。JS無効時は noscript で担保。
 * --------------------------------------------------------- */
add_filter('style_loader_tag', function ($html, $handle, $href, $media) {
    if ('gem-fonts' !== $handle) return $html;
    $onload = "this.onload=null;this.media='all';";
    return sprintf(
        '<link rel="stylesheet" id="%1$s-css" href="%2$s" media="print" onload="%3$s" />' . "\n"
        . '<noscript><link rel="stylesheet" href="%2$s" /></noscript>' . "\n",
        esc_attr($handle),
        esc_url($href),
        esc_attr($onload)
    );
}, 10, 4);

/* -----------------------------------------------------------
 * 3. 絵文字用スクリプト/スタイルを停止
 *    （wp-emoji-release.min.js とインラインCSSのリクエストを削減）
 * --------------------------------------------------------- */
add_action('init', function () {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('tiny_mce_plugins', function ($plugins) {
        return is_array($plugins) ? array_diff($plugins, array('wpemoji')) : $plugins;
    });
    add_filter('emoji_svg_url', '__return_false');
});

/* -----------------------------------------------------------
 * 4. wp_head の不要出力を削減（軽微だが確実な削り）
 * --------------------------------------------------------- */
remove_action('wp_head', 'wp_generator');       // WordPress バージョンの露出
remove_action('wp_head', 'wlwmanifest_link');   // Windows Live Writer
remove_action('wp_head', 'rsd_link');           // Really Simple Discovery

/* -----------------------------------------------------------
 * 5. トップページ：ヒーロー背景（LCP候補）を preload
 *    背景画像はCSS由来で fetchpriority を付けられないため preload で優先取得。
 * --------------------------------------------------------- */
add_action('wp_head', function () {
    if (!is_front_page()) return;
    $img = get_header_image();
    if ($img) {
        printf(
            '<link rel="preload" as="image" href="%s" fetchpriority="high" />' . "\n",
            esc_url($img)
        );
    }
}, 2);
