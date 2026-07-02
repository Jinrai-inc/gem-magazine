<?php
/**
 * GEM-MAGAZINE ─ 記事の実閲覧数カウンタ
 *
 * 単一投稿の表示時に post meta `gem_view_count` をインクリメント。
 * 同一ブラウザからの多重カウントを避けるため 12 時間の cookie で抑止する。
 * ボット排除は簡易的に is_user_logged_in() が管理者以上ならスキップし、
 * User-Agent に bot / crawler / spider を含む場合もスキップする。
 *
 * ランキング取得: gem_ranking_query($num) を呼ぶと WP_Query が返る。
 *
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;

const GEM_VIEW_META    = 'gem_view_count';
const GEM_VIEW_COOKIE  = 'gem_v';
const GEM_VIEW_TTL     = 12 * HOUR_IN_SECONDS;

/**
 * 現在の投稿の閲覧数を+1する。 template_redirect にフック。
 */
add_action('template_redirect', function () {
    if (!is_singular('post')) return;
    if (is_preview()) return;
    if (current_user_can('edit_posts')) return; // 編集者以上はカウントしない

    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : '';
    if ($ua && preg_match('/bot|crawler|spider|slurp|preview|archive\.org/i', $ua)) return;

    $post_id = get_queried_object_id();
    if (!$post_id) return;

    $cookie_key = GEM_VIEW_COOKIE . '_' . $post_id;
    if (!empty($_COOKIE[$cookie_key])) return;

    $count = (int) get_post_meta($post_id, GEM_VIEW_META, true);
    update_post_meta($post_id, GEM_VIEW_META, $count + 1);

    // 12h の cookie で二重カウントを抑止
    setcookie($cookie_key, '1', time() + GEM_VIEW_TTL, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);
});

/**
 * 現在の投稿の閲覧数を返す
 */
function gem_get_view_count($post_id = 0) {
    if (!$post_id) $post_id = get_the_ID();
    return (int) get_post_meta($post_id, GEM_VIEW_META, true);
}

/**
 * ランキング（閲覧数上位）を返す WP_Query
 * @param int  $num
 * @param bool $only_recent 直近90日に限定するか（初期表示の偏りを緩和）
 */
function gem_ranking_query($num = 6, $only_recent = false) {
    $args = array(
        'posts_per_page'      => (int) $num,
        'ignore_sticky_posts' => true,
        'meta_key'            => GEM_VIEW_META,
        'orderby'             => 'meta_value_num',
        'order'               => 'DESC',
        'no_found_rows'       => true,
    );
    if ($only_recent) {
        $args['date_query'] = array(array('after' => '-90 days'));
    }
    return new WP_Query($args);
}
