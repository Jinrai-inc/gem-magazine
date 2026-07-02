<?php
/**
 * GEM-MAGAZINE ─ アーカイブ／記事一覧のページ送り安定化
 *
 * 目的:
 *   1) /articles/（固定ページ）に ?paged=N が付いた際、WordPress の
 *      redirect_canonical が「固定ページ本文のマルチページ分割」形式
 *      /articles/page/N/ へ強制リダイレクトしてしまう挙動を無効化。
 *   2) カテゴリ・タグ・アーカイブ等の一覧が、Reading 設定の
 *      「投稿の表示数」が極端に少ないと 1 ページあたり 1〜2 件しか
 *      出ないため、テーマ側で最低件数（既定 9）を保証する。
 *      サイト運用側で変えたい場合は gem_min_archive_per_page フィルタで上書き可。
 *
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;

/**
 * /articles/ 上での canonical redirect を無効化。
 * ?paged=N が付いた時に /page/N/ へ変換される問題を防ぐ。
 * 他ページ（投稿・カテゴリ等）では通常通り canonical redirect を効かせる。
 */
add_filter('redirect_canonical', function ($redirect_url) {
    $obj = get_queried_object();
    if ($obj instanceof WP_Post && $obj->post_type === 'page' && $obj->post_name === 'articles') {
        return false;
    }
    return $redirect_url;
});

/**
 * アーカイブ／カテゴリ／タグ／検索の 1 ページあたり件数を最低 9 に。
 * gem_min_archive_per_page フィルタで自由に上書き可能。
 */
add_action('pre_get_posts', function ($q) {
    if (is_admin() || !$q->is_main_query()) return;
    if (!($q->is_archive() || $q->is_home() || $q->is_search())) return;

    $min = (int) apply_filters('gem_min_archive_per_page', 9);
    $current = (int) $q->get('posts_per_page');
    if ($current < 1 || $current < $min) {
        $q->set('posts_per_page', $min);
    }
});
