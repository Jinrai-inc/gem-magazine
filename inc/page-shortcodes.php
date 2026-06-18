<?php
/**
 * GEM-MAGAZINE ─ 固定ページ用ショートコード ＋ 構造化データ
 *
 * 提供ショートコード：
 *   [gem_line_button]   … LINEボタン（text="…" で文言変更可）
 *   [gem_cta]           … CTAボックス（title/text/button）
 *   [gem_qa]            … QAページ用カテゴリ別アコーディオン
 *
 * LINE のURLは「外観 > カスタマイズ > GEM-MAGAZINE設定 > LINE友だち追加URL」を参照。
 *
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;

/* LINEボタン */
function gem_line_button_shortcode($atts) {
    $atts = shortcode_atts(array(
        'text' => gem_mod('gem_cta_primary', 'LINE無料講座を受ける'),
    ), $atts, 'gem_line_button');

    $url = gem_line_url();

    ob_start(); ?>
    <a class="btn-line btn-line--sm" href="<?php echo esc_url($url); ?>" style="margin:24px auto;max-width:440px;">
      <span class="btn-line-mark">LINE</span>
      <?php echo esc_html($atts['text']); ?>
      <span class="btn-arrow"><?php echo gem_icon('chevron', 14, '#fff'); ?></span>
    </a>
    <?php
    return ob_get_clean();
}
add_shortcode('gem_line_button', 'gem_line_button_shortcode');

/* CTAボックス（記事下CTAと同じ見た目） */
function gem_cta_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title'  => '宝石の知識を、無料でもっと深く。',
        'text'   => '鑑定の基礎から実践まで、LINEでやさしくお届けします。',
        'button' => gem_mod('gem_cta_primary', 'LINE無料講座を受ける'),
    ), $atts, 'gem_cta');

    $url = gem_line_url();

    ob_start(); ?>
    <div class="inline-cta">
      <h3><?php echo esc_html($atts['title']); ?></h3>
      <p><?php echo esc_html($atts['text']); ?></p>
      <a class="btn-line btn-line--sm" href="<?php echo esc_url($url); ?>" style="margin:0 auto;">
        <span class="btn-line-mark">LINE</span>
        <?php echo esc_html($atts['button']); ?>
        <span class="btn-arrow"><?php echo gem_icon('chevron', 14, '#fff'); ?></span>
      </a>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('gem_cta', 'gem_cta_shortcode');


/* ============================================================
 * ▼▼ 追加（2026）GEO QAデータ ＋ QAショートコード ＋ 構造化データ自動出力
 * ------------------------------------------------------------
 * ・gem_qa_data() … Q&Aの「唯一の置き場所」。ここを直せば
 *   画面表示（アコーディオン）と FAQPage 構造化データの両方が
 *   同時に更新され、ズレません（Googleの必須要件を満たす設計）。
 * ・[gem_qa] … QAページ本文に1つ置くだけでQ&A全体を表示。
 * ・gem_jsonld() … 全ページに Organization / WebSite を、
 *   各固定ページにパンくず・AboutPage・ContactPage・Course・
 *   FAQPage を自動で付与（手入力ほぼ不要・WP設定から自動取得）。
 * ============================================================ */

/* ---- Q&Aデータ（ここを編集すれば表示と構造化データに即反映） ---- */
function gem_qa_data() {
    $data = array(
        array('cat' => '宝石鑑別・宝石学の基礎', 'items' => array(
            array('q' => '宝石鑑別（かんべつ）とは何ですか？',
                  'a' => '宝石鑑別とは、その石が何という宝石なのか、天然か人工（合成・模造）か、どのような処理がされているかを科学的に判定することです。色・屈折率・比重・内包物などを専門の機材で調べ、種類と真偽を見極めます。価値の格付けを行う「鑑定」とは目的が異なります。'),
            array('q' => '宝石鑑別と宝石鑑定の違いは何ですか？',
                  'a' => '鑑別は「石の正体（種類・天然か否か・処理の有無）」を特定する作業、鑑定（グレーディング）は「品質や価値を格付けする」作業です。たとえばダイヤモンドの4C評価は鑑定にあたります。まず鑑別で正体を確かめ、その上で鑑定で品質を評価する、という関係です。'),
            array('q' => '鑑別書と鑑定書はどう違いますか？',
                  'a' => '鑑別書は宝石の種類・天然か否か・処理の有無を記したもの、鑑定書（グレーディングレポート）は主にダイヤモンドの4Cなど品質を格付けしたものです。一般に色石には鑑別書、ダイヤモンドには鑑定書が発行されます。'),
            array('q' => '天然石と人工石はどうやって見分けますか？',
                  'a' => '内包物（インクルージョン）や成長の痕跡、屈折率・比重といった光学的・物理的性質を手がかりに見分けます。天然石には自然にできた特有の内包物があり、合成石には製造由来の特徴が現れます。ルーペや屈折計などで複数の性質を総合して判断するのが基本です。'),
            array('q' => '宝石の価値は何で決まりますか？',
                  'a' => '宝石の価値は、種類・色・透明度・カット・大きさに加え、天然か否か・処理の有無・希少性で決まります。ダイヤモンドでは4C（カラット／カラー／クラリティ／カット）が基準となり、色石では特に「色の美しさ」と「天然・無処理かどうか」が価格を大きく左右します。'),
            array('q' => 'ルーペを使うと宝石の何がわかりますか？',
                  'a' => '10倍ルーペでは、内包物の有無や種類、傷、カットの仕上がり、表面の状態などを確認できます。内包物のパターンは天然・合成・模造を見分ける重要な手がかりになり、宝石鑑別の第一歩として欠かせない道具です。'),
            array('q' => '宝石鑑別は独学でも身につきますか？',
                  'a' => '基礎知識は書籍やネットでも学べますが、実際に石を見分ける力は、本物の石と機材に触れながら学ぶのが近道です。内包物の見方や機材の扱いは経験がものを言うため、体系立てた講座で手を動かして学ぶと、独学より早く確実に身につきやすくなります。'),
        )),
        array('cat' => '宝石を学ぶ方法・講座について', 'items' => array(
            array('q' => '宝石鑑別は自宅でも学べますか？',
                  'a' => 'はい、自宅でも学べます。基礎知識はオンラインで学習でき、近年は自宅で機材を使いながら本格的な鑑別を学べる講座も登場しています。当メディアでご紹介している講座も、自宅で実践的に学べる形式です。'),
            array('q' => '宝石の勉強は何から始めればいいですか？',
                  'a' => 'まずは「宝石の種類と、価値の決まり方」を知ることから始めるのがおすすめです。次に鑑別書・鑑定書の読み方、ルーペでの内包物の見方へ進むと理解がつながります。当メディアの無料LINE講座では、この入り口部分をやさしく学べます。'),
            array('q' => '「ハウスジェムアテンダー養成講座」とはどんな講座ですか？',
                  'a' => '現役宝石商の長谷川邦義先生が開発した、自宅で宝石鑑別を本格的に学べる養成講座です。鑑別の基礎から、宝石を見分ける技術、知識の活かし方までを体系的に学べます。まずは無料のLINE講座から内容に触れられます。詳細は公式の案内をご確認ください。'),
            array('q' => '無料のLINE講座と本講座は何が違いますか？',
                  'a' => 'LINE講座は、宝石の基礎や鑑別の考え方を無料で気軽に学べる入門編です。本講座は、より実践的な鑑別技術や知識の活かし方までを体系的に学ぶ内容です。まずは無料のLINE講座で雰囲気を確かめてから、ご自身のペースで検討いただけます。'),
            array('q' => '受講に専門の機材は必要ですか？',
                  'a' => '学び方によりますが、ルーペなど基本的な道具があるとより実践的に学べます。必要な機材は講座ごとに案内が異なるため、詳しくは公式の案内をご確認ください。無料のLINE講座は、特別な機材がなくても学び始められます。'),
            array('q' => 'まったくの初心者・知識ゼロでも大丈夫ですか？',
                  'a' => 'はい、知識ゼロからでも大丈夫です。当メディアおよびご紹介している講座は、専門用語をかみくだいて解説することを大切にしており、「宝石が好き」という気持ちさえあれば、基礎から順を追って学べる構成になっています。'),
        )),
        array('cat' => '資格・キャリア・仕事への活かし方', 'items' => array(
            array('q' => 'FGA・DGAとはどんな資格ですか？',
                  'a' => 'FGAは英国宝石学協会（Gem-A）が認定する宝石学の資格、DGAは同協会のダイヤモンド鑑別に関する資格です。いずれも世界的に権威があり、難関として知られています。宝石鑑別の専門性を示す国際的な資格のひとつです。'),
            array('q' => '宝石の知識は仕事にどう活かせますか？',
                  'a' => '宝石の査定・仕入れ・販売、ジュエリーデザイン、古物商や遺品整理、ブライダル関連など、宝石に触れる幅広い仕事に活かせます。正しく鑑別できる人は現場でも貴重なため、知識そのものが強みになります。'),
            array('q' => '古物商や遺品整理の仕事に宝石鑑別は役立ちますか？',
                  'a' => '非常に役立ちます。古物商や遺品整理では、宝石・ジュエリーの価値を正しく見極められるかどうかが、買取・査定の精度に直結します。鑑別の知識があれば、見落としや過小評価を防ぎ、取引の信頼性を高められます。'),
            array('q' => '宝石商になるにはどうすればいいですか？',
                  'a' => '必須の国家資格はありませんが、宝石を見分ける鑑別の知識と、売買のための古物商許可が実務上の基本になります。まずは鑑別を体系的に学び、信頼できる仕入れ先とのつながりを築くことが、宝石商への現実的な第一歩です。'),
            array('q' => '副業として宝石の知識は活かせますか？',
                  'a' => 'はい、活かせます。宝石の売買やジュエリーの個人販売など、知識を活かした副業に取り組む人もいます。販売では無在庫・卸値での取引が可能な道もありますが、取引には正しい鑑別知識と古物商許可などの準備が前提となります。'),
        )),
        array('cat' => 'このメディア・LINE講座について', 'items' => array(
            array('q' => 'このメディアはどんなサイトですか？',
                  'a' => '宝石・ジュエリーの基礎知識から、鑑別の学び方、知識の活かし方までを、初心者にもわかりやすくお届けする専門メディアです。正しい知識を持つ人が正しく宝石を楽しめる世界を目指し、根拠のある情報をやさしい言葉で発信しています。'),
            array('q' => 'LINEの無料講座は本当に無料ですか？',
                  'a' => 'はい、無料でご利用いただけます。友だち追加するだけで、宝石の基礎や鑑別の考え方を学べます。まずは無料で雰囲気を確かめてから、本講座を検討するかどうかをご自身のペースで判断いただけます。'),
            array('q' => '宝石の鑑定を直接お願いできますか？',
                  'a' => 'いいえ。当メディアは情報提供を目的としており、宝石そのものの売買・鑑定は直接お受けしていません。お手持ちの宝石の鑑別・鑑定をご希望の場合は、専門の鑑別機関にご相談ください。'),
            array('q' => '講座の申し込み方法を教えてください。',
                  'a' => 'まずは当サイトのLINE無料講座にご登録ください。無料講座の中で本講座の詳しい案内が届きます。お申し込み・ご契約は講座を提供する事業者との直接のお取引となりますので、最新の内容は公式の案内をご確認ください。'),
        )),
    );
    return apply_filters('gem_qa_data', $data);
}

/* ---- [gem_qa] ：QAページ本文に置くと、カテゴリ別アコーディオンを表示 ---- */
function gem_qa_shortcode() {
    $data = gem_qa_data();
    if (empty($data)) return '';
    ob_start();
    foreach ($data as $group) {
        echo '<h2 class="gem-qa-cat">' . esc_html($group['cat']) . '</h2>';
        echo '<div class="gem-acc-group">';
        foreach ($group['items'] as $qa) {
            echo '<details class="gem-acc">';
            echo '<summary>' . esc_html($qa['q']) . '</summary>';
            echo '<div class="gem-acc-body"><p>' . esc_html($qa['a']) . '</p></div>';
            echo '</details>';
        }
        echo '</div>';
    }
    return ob_get_clean();
}
add_shortcode('gem_qa', 'gem_qa_shortcode');

/* ---- FAQPage 構造化データを gem_qa_data() から組み立て ---- */
function gem_build_faqpage() {
    $items = array();
    foreach (gem_qa_data() as $group) {
        foreach ($group['items'] as $qa) {
            $items[] = array(
                '@type' => 'Question',
                'name'  => $qa['q'],
                'acceptedAnswer' => array('@type' => 'Answer', 'text' => $qa['a']),
            );
        }
    }
    if (empty($items)) return null;
    return array('@type' => 'FAQPage', 'mainEntity' => $items, 'inLanguage' => 'ja');
}

/* ---- 構造化データ（JSON-LD）を全ページに自動出力 ---- */
function gem_jsonld() {
    if (is_admin() || is_feed()) return;

    $home = home_url('/');
    $site_name = get_bloginfo('name');
    $blocks = array();

    /* Organization（運営者・発行元） */
    $org = array('@type' => 'Organization', '@id' => $home . '#org', 'name' => $site_name, 'url' => $home);
    $logo_id = get_theme_mod('custom_logo');
    if ($logo_id) {
        $src = wp_get_attachment_image_src($logo_id, 'full');
        if ($src) $org['logo'] = array('@type' => 'ImageObject', 'url' => $src[0]);
    }
    $sameas = array_values(array_filter(array(
        gem_mod('gem_sns_x', ''), gem_mod('gem_sns_instagram', ''), gem_mod('gem_sns_youtube', ''),
    )));
    if ($sameas) $org['sameAs'] = $sameas;
    $blocks[] = $org;

    /* WebSite（サイト＋サイト内検索） */
    $blocks[] = array(
        '@type' => 'WebSite', '@id' => $home . '#website',
        'name' => $site_name, 'url' => $home, 'inLanguage' => 'ja',
        'publisher' => array('@id' => $home . '#org'),
        'potentialAction' => array(
            '@type' => 'SearchAction',
            'target' => array('@type' => 'EntryPoint', 'urlTemplate' => $home . '?s={search_term_string}'),
            'query-input' => 'required name=search_term_string',
        ),
    );

    /* 固定ページごとの付与 */
    if (is_page()) {
        $pid   = get_queried_object_id();
        $title = get_the_title($pid);
        $url   = get_permalink($pid);
        $slug  = get_post_field('post_name', $pid);

        /* パンくず（ホーム > ページ） */
        $blocks[] = array(
            '@type' => 'BreadcrumbList',
            'itemListElement' => array(
                array('@type' => 'ListItem', 'position' => 1, 'name' => 'ホーム', 'item' => $home),
                array('@type' => 'ListItem', 'position' => 2, 'name' => $title, 'item' => $url),
            ),
        );

        if ($slug === 'about-us' || $slug === 'about') {
            $blocks[] = array('@type' => 'AboutPage', 'name' => $title, 'url' => $url,
                'isPartOf' => array('@id' => $home . '#website'), 'about' => array('@id' => $home . '#org'), 'inLanguage' => 'ja');
        } elseif ($slug === 'contact') {
            $blocks[] = array('@type' => 'ContactPage', 'name' => $title, 'url' => $url,
                'isPartOf' => array('@id' => $home . '#website'), 'inLanguage' => 'ja');
        } elseif ($slug === 'line-course') {
            $blocks[] = array(
                '@type' => 'Course',
                'name' => gem_mod('gem_course_name', '宝石鑑別 無料LINE講座'),
                'description' => '宝石鑑別の基礎をLINEで学べる無料講座のご紹介ページ。',
                'url' => $url, 'inLanguage' => 'ja', 'isAccessibleForFree' => true,
                'provider' => array('@type' => 'Organization', 'name' => gem_mod('gem_course_provider', $site_name)),
            );
        } elseif ($slug === 'faq') {
            $faq = gem_build_faqpage();
            if ($faq) $blocks[] = $faq;
        }
    }

    if (empty($blocks)) return;
    $graph = array('@context' => 'https://schema.org', '@graph' => $blocks);
    echo "\n" . '<script type="application/ld+json">'
        . wp_json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        . '</script>' . "\n";
}
add_action('wp_head', 'gem_jsonld', 20);
