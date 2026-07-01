<?php
/**
 * GEM-MAGAZINE ─ 記事CTAのAFリンク自動化 v2
 *
 * 記事(単一投稿)ページの `.btn-line` の href="#" を、記事のカテゴリに応じて
 * 自動でAFリンクに設定する。
 *
 * v1 との違い：template_redirect + ob_start で「記事ページ全体」を対象に
 * するため、本文内CTAだけでなく、テーマが記事下部に自動追加するCTAも
 * まとめて置換される。
 *
 * リンク変更は $map / $default、あるいは
 * `gem_af_link_map` / `gem_af_link_default` フィルターで行える。
 *
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;

if (!function_exists('gem_af_link_for_post')) {
    /** 記事のカテゴリに対応するAFリンクを返す（該当なしは既定＝LPB） */
    function gem_af_link_for_post($post_id = 0) {
        $map = array(
            'appraisal'  => 'https://housegematender.net/lp/2afl/8hhp', // 宝石鑑別・鑑定
            'gemology'   => 'https://housegematender.net/lp/2afp/8hhq', // 宝石学・資格・スクール
            'business'   => 'https://housegematender.net/lp/2afq/8hhr', // 宝石ビジネス・副業・開業
            'estate'     => 'https://housegematender.net/lp/2afr/8hhs', // 古物・遺品・相続
            'craft'      => 'https://housegematender.net/lp/2afs/8hht', // ジュエリー制作・彫金・CAD
            'gem-guide'  => 'https://housegematender.net/lp/2aft/8hhu', // 宝石図鑑
            'birthstone' => 'https://housegematender.net/lp/2afu/8hhv', // 誕生石
        );
        $default = 'https://housegematender.net/lp/2af8/8hho'; // 既定（LPB=LP124）

        $map     = apply_filters('gem_af_link_map', $map);
        $default = apply_filters('gem_af_link_default', $default);

        if (!$post_id) { $post_id = get_queried_object_id(); }
        $cats = get_the_category($post_id);
        if ($cats) {
            foreach ($cats as $c) {
                if (isset($map[$c->slug])) { return $map[$c->slug]; }
            }
        }
        return $default;
    }
}

/** 単一記事ページ全体で .btn-line の href="#" をカテゴリ別AFリンクへ置換 */
add_action('template_redirect', function () {
    if (!is_singular('post')) { return; }
    ob_start(function ($html) {
        $url = esc_url(gem_af_link_for_post(get_queried_object_id()));
        // class(btn-line) → href="#" の順
        $html = preg_replace_callback(
            '/<a\b([^>]*\bclass="[^"]*btn-line[^"]*"[^>]*?)href="#"/i',
            function ($m) use ($url) { return '<a' . $m[1] . 'href="' . $url . '"'; },
            $html
        );
        // href="#" → class(btn-line) の順（保険）
        $html = preg_replace_callback(
            '/<a\b([^>]*?)href="#"([^>]*\bclass="[^"]*btn-line[^"]*"[^>]*)>/i',
            function ($m) use ($url) { return '<a' . $m[1] . 'href="' . $url . '"' . $m[2] . '>'; },
            $html
        );
        return $html;
    });
});
