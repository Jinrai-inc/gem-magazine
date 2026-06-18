<?php
/**
 * GEM-MAGAZINE ─ ピラーページ用 追加ショートコード／構造化データ
 *
 * 含まれるもの：
 *   ・トップ「おすすめコンテンツ」4カードのリンク自動配線
 *   ・gem_pillar_faq() で各ピラーページのFAQを一元管理
 *   ・[gem_faq key="slug"] でアコーディオン表示
 *   ・ピラー3ページに FAQPage 構造化データを自動出力（独立動作）
 *
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;

/* ---- トップ「おすすめコンテンツ」カードのリンク先を設定 ---- */
add_filter('gem_content_cards', function ($cards) {
    $by_icon = array(
        'loupe'   => home_url('/gem-appraisal-basics/'),
        'receipt' => home_url('/appraisal-value/'),
        'book'    => home_url('/gem-basics/'),
        'flag'    => home_url('/line-course/'),
    );
    foreach ($cards as &$c) {
        if (isset($by_icon[$c['icon']])) $c['url'] = $by_icon[$c['icon']];
    }
    unset($c);
    return $cards;
});

/* ---- ピラーページごとのFAQ（slugで対応付け） ---- */
function gem_pillar_faq() {
    $data = array(
        'gem-appraisal-basics' => array(
            array('q' => '宝石鑑定と宝石鑑別はどう違いますか？',
                  'a' => '鑑別は「石の正体（種類・天然か否か・処理の有無）」を特定する作業、鑑定（グレーディング）は「品質や価値を格付けする」作業です。一般に、まず鑑別で正体を確かめ、その上で鑑定で品質を評価します。'),
            array('q' => '鑑定士はどんな仕事をしていますか？',
                  'a' => '宝石鑑定士は、ルーペや屈折計などの機材を使って宝石の種類・真贋・処理の有無を見極め、必要に応じて品質を評価します。宝石店や鑑別機関で、買取・販売・鑑別書発行などの判断を支える専門職です。'),
            array('q' => '鑑別書・鑑定書はどこで発行されますか？',
                  'a' => '第三者の宝石鑑別機関で発行されるのが一般的です。鑑別書は石の種類や天然・処理の有無を、鑑定書（グレーディングレポート）は主にダイヤモンドの4Cなど品質を記載します。発行機関によって基準や信頼性が異なる点に注意が必要です。'),
            array('q' => '自分の宝石を鑑定してもらうにはどうすればいい？',
                  'a' => 'お手持ちの宝石の鑑別・鑑定をご希望の場合は、専門の宝石鑑別機関や、鑑別を扱う宝石店にご相談ください。当メディアは情報提供を目的としており、個別の鑑定・査定は承っておりません。'),
        ),
        'appraisal-value' => array(
            array('q' => '鑑定書のどこを見れば価値がわかりますか？',
                  'a' => 'ダイヤモンドなら、カラット（重さ）・カラー（色）・クラリティ（透明度）・カット（仕上がり）の4Cがそろって良いほど高く評価されます。色石では、色の美しさ・透明度・天然か否か・処理の有無が価値を大きく左右します。'),
            array('q' => 'ダイヤモンドの4Cとは何ですか？',
                  'a' => '4Cとは、Carat（重さ）・Color（色）・Clarity（透明度）・Cut（カットの仕上がり）の4つの評価基準です。ダイヤモンドの品質と価格を判断する世界共通のものさしで、4つのバランスで価値が決まります。'),
            array('q' => '色石（ルビーやサファイアなど）の価値は何で決まりますか？',
                  'a' => '色石は、まず「色の美しさ」が最重要で、加えて透明度・カット・大きさ・希少性で決まります。さらに、加熱などの処理がされているか、無処理の天然石かによっても価値が大きく変わります。'),
            array('q' => '同じ種類の宝石でも価格が違うのはなぜですか？',
                  'a' => '同じ種類でも、色みや透明度、カットの良し悪し、処理の有無、産地などの条件で品質が変わるためです。鑑別書・鑑定書でこれらの条件を確認することが、適正な価値を見極める近道になります。'),
        ),
        'gem-basics' => array(
            array('q' => '宝石と鉱物は何が違いますか？',
                  'a' => '宝石は、鉱物などのうち「美しさ・希少性・耐久性」を備え、装飾品として価値を持つものを指します。たとえばダイヤモンドやルビーは鉱物ですが、その中でも宝石として扱われる条件を満たしたものです。'),
            array('q' => '代表的な宝石にはどんな種類がありますか？',
                  'a' => 'ダイヤモンド、ルビー、サファイア、エメラルドが代表的で、四大宝石と呼ばれます。ルビーとサファイアは同じコランダムという鉱物で、赤いものがルビー、それ以外の色がサファイアです。'),
            array('q' => '宝石の価値を決める要素は何ですか？',
                  'a' => '宝石の価値は、色・透明度・カット・大きさ（重さ）に加え、天然か否か・処理の有無・希少性で決まります。ダイヤモンドでは4Cが基準となり、色石では特に色の美しさが重視されます。'),
            array('q' => '宝石の知識はどこから学べばいいですか？',
                  'a' => 'まずは「宝石の種類」と「価値の決まり方」を知ることから始めるのがおすすめです。次に鑑別書・鑑定書の読み方へ進むと理解がつながります。当メディアの無料LINE講座では、この入り口をやさしく学べます。'),
        ),
    );
    return apply_filters('gem_pillar_faq', $data);
}

/* ---- [gem_faq key="slug"] ：指定キーのFAQをアコーディオン表示 ---- */
function gem_faq_shortcode($atts) {
    $atts = shortcode_atts(array('key' => ''), $atts, 'gem_faq');
    $all  = gem_pillar_faq();
    if (empty($atts['key']) || !isset($all[$atts['key']])) return '';
    ob_start();
    echo '<div class="gem-acc-group">';
    foreach ($all[$atts['key']] as $qa) {
        echo '<details class="gem-acc"><summary>' . esc_html($qa['q']) . '</summary>';
        echo '<div class="gem-acc-body"><p>' . esc_html($qa['a']) . '</p></div></details>';
    }
    echo '</div>';
    return ob_get_clean();
}
add_shortcode('gem_faq', 'gem_faq_shortcode');

/* ---- ピラーページに FAQPage 構造化データを自動出力 ---- */
function gem_pillar_jsonld() {
    if (is_admin() || is_feed() || !is_page()) return;
    $slug = get_post_field('post_name', get_queried_object_id());
    $pf   = gem_pillar_faq();
    if (!isset($pf[$slug])) return;

    $items = array();
    foreach ($pf[$slug] as $qa) {
        $items[] = array('@type' => 'Question', 'name' => $qa['q'],
            'acceptedAnswer' => array('@type' => 'Answer', 'text' => $qa['a']));
    }
    if (empty($items)) return;

    $graph = array('@context' => 'https://schema.org',
        '@graph' => array(array('@type' => 'FAQPage', 'mainEntity' => $items, 'inLanguage' => 'ja')));
    echo "\n" . '<script type="application/ld+json">'
        . wp_json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        . '</script>' . "\n";
}
add_action('wp_head', 'gem_pillar_jsonld', 21);
