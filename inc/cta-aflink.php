<?php
/**
 * GEM-MAGAZINE ─ 記事CTAのAFリンク自動化
 *
 * [gem_cta] などが出力する `.btn-line` の href="#" を、記事のカテゴリに
 * 対応するアフィリエイトリンクへ本文フィルターで置換する。
 *
 * リンクを変更したい場合は下記 `$map` / `$default` を修正するだけ。
 * 個別に別リンクを指定したいボタンは href を "#" 以外にすれば置換されない。
 *
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;

/** 記事(投稿)のカテゴリに対応するAFリンクを返す */
function gem_af_link_for_post($post_id = 0) {
    // カテゴリ slug => AFリンク
    $map = array(
        'appraisal'  => 'https://housegematender.net/lp/2afl/8hhp', // 宝石鑑別・鑑定
        'gemology'   => 'https://housegematender.net/lp/2afp/8hhq', // 宝石学・資格・スクール
        'business'   => 'https://housegematender.net/lp/2afq/8hhr', // 宝石ビジネス・副業・開業
        'estate'     => 'https://housegematender.net/lp/2afr/8hhs', // 古物・遺品・相続
        'craft'      => 'https://housegematender.net/lp/2afs/8hht', // ジュエリー制作・彫金・CAD
        'gem-guide'  => 'https://housegematender.net/lp/2aft/8hhu', // 宝石図鑑
        'birthstone' => 'https://housegematender.net/lp/2afu/8hhv', // 誕生石
    );
    // どのカテゴリにも該当しない場合の既定
    $default = 'https://housegematender.net/lp/2af8/8hho';

    /**
     * カテゴリ→AFリンクマップの外部フィルター
     * add_filter('gem_af_link_map', function($map){ ... });
     */
    $map     = apply_filters('gem_af_link_map', $map);
    $default = apply_filters('gem_af_link_default', $default);

    if (!$post_id) { $post_id = get_the_ID(); }
    $cats = get_the_category($post_id);
    if ($cats) {
        foreach ($cats as $c) {
            if (isset($map[$c->slug])) { return $map[$c->slug]; }
        }
    }
    return $default;
}

/** 記事本文中の .btn-line ボタンの href="#" をカテゴリ別AFリンクへ置換 */
add_filter('the_content', function ($content) {
    if (is_singular('post') && in_the_loop() && is_main_query()) {
        $url = esc_url(gem_af_link_for_post());

        // パターン1: class(btn-line) → href="#" の順
        $content = preg_replace_callback(
            '/<a\b([^>]*\bclass="[^"]*btn-line[^"]*"[^>]*?)href="#"/i',
            function ($m) use ($url) { return '<a' . $m[1] . 'href="' . $url . '"'; },
            $content
        );
        // パターン2: href="#" → class(btn-line) の順（保険）
        $content = preg_replace_callback(
            '/<a\b([^>]*?)href="#"([^>]*\bclass="[^"]*btn-line[^"]*"[^>]*)>/i',
            function ($m) use ($url) { return '<a' . $m[1] . 'href="' . $url . '"' . $m[2] . '>'; },
            $content
        );
    }
    return $content;
}, 20);
