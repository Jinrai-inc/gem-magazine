<?php
/**
 * GEM-MAGAZINE ─ SEO / GEO 構造強化
 *
 * - 投稿に BlogPosting + BreadcrumbList の JSON-LD を自動出力
 * - 画面上のパンくず gem_breadcrumbs()
 * - 著者情報ボックス gem_author_box()（bio があれば表示・Person は BlogPosting 側で付与）
 * - 同カテゴリの関連記事 gem_related_posts()
 * - /articles/?pg=2 以降を noindex,follow（薄い重複ページ対策）
 * - meta description / OGP / Twitter Card のフォールバック
 *   （AIOSEO / Yoast / Rank Math / SEO SIMPLE PACK 等が入っている場合は出力しない）
 *
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;

/* ============================================================
 * 1. 投稿の構造化データ（BlogPosting + BreadcrumbList）
 * ========================================================== */
function gem_post_jsonld() {
    if (!is_singular('post')) return;

    $post  = get_queried_object();
    if (!$post instanceof WP_Post) return;

    $home  = home_url('/');
    $url   = get_permalink($post);
    $title = get_the_title($post);

    $img = get_the_post_thumbnail_url($post, 'large');
    if (!$img && function_exists('gem_featured_bg_url')) {
        $img = gem_featured_bg_url();
    }

    $author_id   = (int) $post->post_author;
    $author_name = get_the_author_meta('display_name', $author_id);

    $blog = array(
        '@type'            => 'BlogPosting',
        '@id'              => $url . '#article',
        'mainEntityOfPage' => array('@type' => 'WebPage', '@id' => $url),
        'headline'         => $title,
        'datePublished'    => get_the_date('c', $post),
        'dateModified'     => get_the_modified_date('c', $post),
        'author'           => array(
            '@type' => 'Person',
            'name'  => $author_name ?: get_bloginfo('name'),
            'url'   => get_author_posts_url($author_id),
        ),
        'publisher'        => array('@id' => $home . '#org'),
        'isPartOf'         => array('@id' => $home . '#website'),
        'inLanguage'       => 'ja',
    );
    if ($img) $blog['image'] = array($img);

    $excerpt = has_excerpt($post)
        ? get_the_excerpt($post)
        : wp_trim_words(wp_strip_all_tags(strip_shortcodes($post->post_content)), 60, '…');
    if ($excerpt) $blog['description'] = $excerpt;

    // パンくず（ホーム > カテゴリ > 記事）
    $items = array(array('@type' => 'ListItem', 'position' => 1, 'name' => 'ホーム', 'item' => $home));
    $pos = 2;
    $cats = get_the_category($post->ID);
    if ($cats) {
        $c = $cats[0];
        $items[] = array('@type' => 'ListItem', 'position' => $pos++, 'name' => $c->name, 'item' => get_category_link($c->term_id));
    }
    $items[] = array('@type' => 'ListItem', 'position' => $pos, 'name' => $title, 'item' => $url);

    $graph = array('@context' => 'https://schema.org', '@graph' => array(
        $blog,
        array('@type' => 'BreadcrumbList', 'itemListElement' => $items),
    ));

    echo "\n" . '<script type="application/ld+json">'
        . wp_json_encode($graph, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        . '</script>' . "\n";
}
add_action('wp_head', 'gem_post_jsonld', 21);

/* ============================================================
 * 2. 画面上のパンくず
 * ========================================================== */
function gem_breadcrumbs() {
    if (is_front_page()) return;

    $home  = home_url('/');
    $items = array(array('name' => 'ホーム', 'url' => $home));

    if (is_singular('post')) {
        $cats = get_the_category();
        if ($cats) {
            $items[] = array('name' => $cats[0]->name, 'url' => get_category_link($cats[0]->term_id));
        }
        $items[] = array('name' => get_the_title(), 'url' => '');
    } elseif (is_page()) {
        foreach (array_reverse(get_post_ancestors(get_the_ID())) as $aid) {
            $items[] = array('name' => get_the_title($aid), 'url' => get_permalink($aid));
        }
        $items[] = array('name' => get_the_title(), 'url' => '');
    } elseif (is_category() || is_tag() || is_tax()) {
        $items[] = array('name' => single_term_title('', false), 'url' => '');
    } elseif (is_search()) {
        $items[] = array('name' => '検索結果', 'url' => '');
    } elseif (is_archive()) {
        $items[] = array('name' => wp_strip_all_tags(get_the_archive_title()), 'url' => '');
    } else {
        $items[] = array('name' => '記事一覧', 'url' => '');
    }

    $last = count($items) - 1;
    echo '<nav class="gem-crumbs" aria-label="' . esc_attr__('パンくずリスト', 'gem-magazine') . '">';
    foreach ($items as $idx => $it) {
        if ($idx > 0) echo '<span aria-hidden="true">›</span>';
        if (!empty($it['url']) && $idx !== $last) {
            echo '<a href="' . esc_url($it['url']) . '">' . esc_html($it['name']) . '</a>';
        } else {
            echo '<span>' . esc_html($it['name']) . '</span>';
        }
    }
    echo '</nav>';
}

/* ============================================================
 * 3. 著者情報ボックス（bio があるときのみ）
 * ========================================================== */
function gem_author_box() {
    $bio = get_the_author_meta('description');
    if (!$bio) return;

    $name = get_the_author_meta('display_name');
    $url  = get_author_posts_url((int) get_the_author_meta('ID'));
    ?>
    <div class="gem-author-box">
      <div class="gem-author-avatar"><?php echo get_avatar(get_the_author_meta('ID'), 72, '', $name); ?></div>
      <div class="gem-author-body">
        <p class="gem-author-role"><?php esc_html_e('この記事を書いた人', 'gem-magazine'); ?></p>
        <p class="gem-author-name"><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($name); ?></a></p>
        <p class="gem-author-bio"><?php echo esc_html($bio); ?></p>
      </div>
    </div>
    <?php
}

/* ============================================================
 * 4. 同カテゴリの関連記事
 * ========================================================== */
function gem_related_posts($num = 3) {
    if (!is_singular('post')) return;
    $cats = get_the_category();
    if (!$cats) return;

    $cat_ids = wp_list_pluck($cats, 'term_id');
    $q = new WP_Query(array(
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => (int) $num,
        'post__not_in'        => array(get_the_ID()),
        'category__in'        => $cat_ids,
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
        'orderby'             => 'date',
        'order'               => 'DESC',
    ));
    if (!$q->have_posts()) { wp_reset_postdata(); return; }
    ?>
    <section class="section gem-related" aria-label="<?php esc_attr_e('関連記事', 'gem-magazine'); ?>">
      <div class="section-head section-head--between">
        <div class="left">
          <?php echo gem_icon('gem', 20, '#D87E92'); ?>
          <h2 class="section-title" style="font-size:22px;"><?php esc_html_e('関連記事', 'gem-magazine'); ?></h2>
        </div>
      </div>
      <div class="posts-grid">
        <?php $i = 0; while ($q->have_posts()) : $q->the_post(); gem_post_card($i); $i++; endwhile; ?>
      </div>
    </section>
    <?php
    wp_reset_postdata();
}

/* ============================================================
 * 5. /articles/?pg=2 以降を noindex,follow
 * ========================================================== */
add_filter('wp_robots', function ($robots) {
    if (isset($_GET['pg']) && (int) $_GET['pg'] >= 2) {
        $robots['noindex'] = true;
        $robots['follow']  = true;
    }
    return $robots;
});

/* ============================================================
 * 6. meta description / OGP / Twitter Card フォールバック
 *    SEOプラグインがある場合は二重出力を避けるため出さない。
 * ========================================================== */
function gem_has_seo_plugin() {
    return defined('WPSEO_VERSION')            // Yoast
        || defined('AIOSEO_VERSION')           // All in One SEO
        || function_exists('aioseo')
        || defined('RANK_MATH_VERSION')        // Rank Math
        || class_exists('RankMath')
        || defined('SSP_VERSION')              // SEO SIMPLE PACK
        || class_exists('SEO_SIMPLE_PACK')
        || function_exists('ssp_get_option');
}

function gem_meta_description() {
    if (is_singular()) {
        $post = get_queried_object();
        if ($post instanceof WP_Post) {
            if (has_excerpt($post)) return get_the_excerpt($post);
            return wp_trim_words(wp_strip_all_tags(strip_shortcodes($post->post_content)), 60, '…');
        }
    }
    if (is_category() || is_tag() || is_tax()) {
        $d = term_description();
        if ($d) return wp_trim_words(wp_strip_all_tags($d), 60, '…');
        return sprintf(__('「%s」に関する記事一覧です。', 'gem-magazine'), single_term_title('', false));
    }
    if (is_front_page() || is_home()) {
        $t = gem_mod('gem_brand_tagline', get_bloginfo('description'));
        return $t ?: get_bloginfo('description');
    }
    return get_bloginfo('description');
}

function gem_og_image() {
    if (is_singular() && has_post_thumbnail()) {
        $src = get_the_post_thumbnail_url(get_queried_object_id(), 'large');
        if ($src) return $src;
    }
    if (function_exists('gem_featured_bg_url')) {
        $bg = gem_featured_bg_url();
        if ($bg) return $bg;
    }
    $logo_id = get_theme_mod('custom_logo');
    if ($logo_id) {
        $src = wp_get_attachment_image_src($logo_id, 'full');
        if ($src) return $src[0];
    }
    return '';
}

add_action('wp_head', function () {
    if (gem_has_seo_plugin()) return;
    if (!apply_filters('gem_enable_meta_fallback', true)) return;

    $desc = trim((string) gem_meta_description());
    $title = wp_get_document_title();
    $url   = is_singular() ? get_permalink() : home_url(add_query_arg(array(), $GLOBALS['wp']->request));
    $type  = is_singular('post') ? 'article' : 'website';
    $img   = gem_og_image();

    if ($desc) {
        echo '<meta name="description" content="' . esc_attr($desc) . '">' . "\n";
    }
    echo '<meta property="og:type" content="' . esc_attr($type) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:locale" content="ja_JP">' . "\n";
    if ($desc) echo '<meta property="og:description" content="' . esc_attr($desc) . '">' . "\n";
    if ($img)  echo '<meta property="og:image" content="' . esc_url($img) . '">' . "\n";

    if (is_singular('post')) {
        echo '<meta property="article:published_time" content="' . esc_attr(get_the_date('c')) . '">' . "\n";
        echo '<meta property="article:modified_time" content="' . esc_attr(get_the_modified_date('c')) . '">' . "\n";
    }

    echo '<meta name="twitter:card" content="' . ($img ? 'summary_large_image' : 'summary') . '">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    if ($desc) echo '<meta name="twitter:description" content="' . esc_attr($desc) . '">' . "\n";
    if ($img)  echo '<meta name="twitter:image" content="' . esc_url($img) . '">' . "\n";
}, 5);
