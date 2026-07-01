<?php
/**
 * GEM-MAGAZINE — functions.php
 */
if (!defined('ABSPATH')) exit;

define('GEM_VER', '1.0.0');

require_once get_template_directory() . '/inc/icons.php';
require_once get_template_directory() . '/inc/theme-data.php';
require_once get_template_directory() . '/inc/page-shortcodes.php';
require_once get_template_directory() . '/inc/pillar-pages.php';
require_once get_template_directory() . '/inc/pr-banner.php';
require_once get_template_directory() . '/inc/cta-aflink.php';
require_once get_template_directory() . '/inc/page-installer.php';
require_once get_template_directory() . '/inc/page-rest.php';

/* ---------------------------------------------------------
 * テーマサポート
 * ------------------------------------------------------- */
function gem_setup() {
    load_theme_textdomain('gem-magazine', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script'));
    add_theme_support('custom-logo', array('height' => 40, 'width' => 40, 'flex-width' => true, 'flex-height' => true));

    // FV（ファーストビュー）背景画像：外観 > ヘッダー画像 から設定
    add_theme_support('custom-header', array(
        'default-image' => '',
        'width'         => 1600,
        'height'        => 900,
        'flex-width'    => true,
        'flex-height'   => true,
        'header-text'   => false,
        'uploads'       => true,
    ));

    register_nav_menus(array(
        'primary'        => __('グローバルナビ', 'gem-magazine'),
        'footer_content' => __('フッター：コンテンツ', 'gem-magazine'),
        'footer_about'   => __('フッター：メディアについて', 'gem-magazine'),
    ));

    add_theme_support('custom-background', array('default-color' => 'FEFCFC'));
}
add_action('after_setup_theme', 'gem_setup');

/* ---------------------------------------------------------
 * スタイル・スクリプト
 * ------------------------------------------------------- */
function gem_assets() {
    wp_enqueue_style(
        'gem-fonts',
        'https://fonts.googleapis.com/css2?family=Shippori+Mincho:wght@500;600;700;800&family=Zen+Kaku+Gothic+New:wght@400;500;700;900&display=swap',
        array(),
        null
    );
    wp_enqueue_style('gem-style', get_stylesheet_uri(), array('gem-fonts'), GEM_VER);
    wp_enqueue_script('gem-theme', get_template_directory_uri() . '/assets/js/theme.js', array(), GEM_VER, true);

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'gem_assets');

/* ---------------------------------------------------------
 * カスタマイザー（LINE URL・FVテキスト）
 * ------------------------------------------------------- */
function gem_customize_register($wp_customize) {
    $wp_customize->add_section('gem_options', array(
        'title'    => __('GEM-MAGAZINE 設定', 'gem-magazine'),
        'priority' => 30,
    ));

    $fields = array(
        'gem_line_url'      => array('label' => 'LINE友だち追加URL', 'default' => '#', 'type' => 'url', 'sanitize' => 'esc_url_raw'),
        'gem_brand_sub'     => array('label' => 'ブランド英字（ロゴ下）', 'default' => 'GEMOLOGY MEDIA', 'type' => 'text', 'sanitize' => 'sanitize_text_field'),
        'gem_brand_tagline' => array('label' => 'ヘッダー説明文', 'default' => '宝石鑑定の知識をやさしく学べる｜宝石・ジュエリー専門メディア', 'type' => 'text', 'sanitize' => 'sanitize_text_field'),
        'gem_hero_kicker'   => array('label' => 'FV：小見出し', 'default' => '初心者からでも安心して学べる', 'type' => 'text', 'sanitize' => 'sanitize_text_field'),
        'gem_hero_accent'   => array('label' => 'FV：見出し（ピンク部分）', 'default' => '宝石鑑定', 'type' => 'text', 'sanitize' => 'sanitize_text_field'),
        'gem_hero_title'    => array('label' => 'FV：見出し（続き）', 'default' => 'の知識を、やさしく学ぶ。', 'type' => 'text', 'sanitize' => 'sanitize_text_field'),
        'gem_hero_sub'      => array('label' => 'FV：本文', 'default' => '鑑定額の見方や鑑定のしくみ、宝石の実践的な知識まで、初心者の方にもわかりやすく解説します。', 'type' => 'textarea', 'sanitize' => 'sanitize_textarea_field'),
        'gem_hero_gift'     => array('label' => 'FV：配信内容ライン', 'default' => '査定額の見方・基礎知識・選び方を配信', 'type' => 'text', 'sanitize' => 'sanitize_text_field'),
        'gem_cta_primary'   => array('label' => 'LINEボタンの文言', 'default' => 'LINE無料講座を受ける', 'type' => 'text', 'sanitize' => 'sanitize_text_field'),
    );

    foreach ($fields as $id => $f) {
        $wp_customize->add_setting($id, array('default' => $f['default'], 'sanitize_callback' => $f['sanitize'], 'transport' => 'refresh'));
        $wp_customize->add_control($id, array('label' => $f['label'], 'section' => 'gem_options', 'type' => $f['type']));
    }
}
add_action('customize_register', 'gem_customize_register');

function gem_mod($key, $fallback = '') {
    return get_theme_mod($key, $fallback);
}

/* ---------------------------------------------------------
 * デフォルトのグローバルナビ（メニュー未設定時）
 * ------------------------------------------------------- */
function gem_default_menu() {
    $items = array(
        home_url('/')                  => 'ホーム',
        home_url('/line-course/')      => 'LINE無料講座',
        home_url('/faq/')              => 'よくある質問',
        home_url('/about-us/')         => '運営者情報',
    );
    echo '<ul>';
    foreach ($items as $url => $label) {
        $cls = ($url === home_url('/') && is_front_page()) ? ' class="current-menu-item"' : '';
        printf('<li%s><a href="%s">%s</a></li>', $cls, esc_url($url), esc_html($label));
    }
    echo '</ul>';
}

/* ---------------------------------------------------------
 * 抜粋の調整
 * ------------------------------------------------------- */
function gem_excerpt_length($len) { return 48; }
add_filter('excerpt_length', 'gem_excerpt_length');
function gem_excerpt_more($more) { return '…'; }
add_filter('excerpt_more', 'gem_excerpt_more');

/* ---------------------------------------------------------
 * 記事カード（ループ内で使用）
 * $i = 0始まりのインデックス（フォールバック宝石グラデの色出し分け用）
 * ------------------------------------------------------- */
function gem_post_card($i = 0) {
    $cat = get_the_category();
    $cat_name = !empty($cat) ? $cat[0]->name : '';
    $gem = 'gem-' . (($i % 4) + 1);
    ?>
    <a class="post-card" href="<?php the_permalink(); ?>">
        <div class="post-thumb">
            <?php if (has_post_thumbnail()) : ?>
                <?php the_post_thumbnail('medium_large', array('alt' => esc_attr(get_the_title()))); ?>
            <?php else : ?>
                <span class="gem-fallback <?php echo esc_attr($gem); ?>"></span>
            <?php endif; ?>
            <?php if ($cat_name) : ?><span class="post-cat"><?php echo esc_html($cat_name); ?></span><?php endif; ?>
        </div>
        <h3 class="post-title"><?php the_title(); ?></h3>
        <div class="post-meta">
            <?php echo gem_icon('clock', 13); ?>
            <span><?php echo esc_html(get_the_date('Y.m.d')); ?></span>
        </div>
    </a>
    <?php
}
