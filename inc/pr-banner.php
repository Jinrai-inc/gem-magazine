<?php
/**
 * GEM-MAGAZINE ─ PR（広告）表記の自動表示
 *
 * ステマ規制（景品表示法）対応。
 * 投稿（記事）の本文先頭に「本ページはプロモーション（広告）が含まれています。」
 * を自動付与する。固定ページ・管理画面・抜粋・RSSは対象外。
 *
 * 対象を固定ページにも広げたい場合は `is_singular('post')` を
 * `is_singular(array('post','page'))` に変更する。
 *
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;

add_filter('the_content', function ($content) {
    if (is_singular('post') && in_the_loop() && is_main_query()) {
        $label  = '<p class="gem-pr-banner gem-pr-banner--auto">';
        $label .= esc_html__('本ページはプロモーション（広告）が含まれています。', 'gem-magazine');
        $label .= '</p>';
        return $label . $content;
    }
    return $content;
}, 5);
