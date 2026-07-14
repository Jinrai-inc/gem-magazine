<?php
/**
 * 記事一覧 / ブログ / 検索結果 / アーカイブ 共通
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;
get_header();
?>

<div class="archive-head">
  <?php if (function_exists('gem_breadcrumbs')) gem_breadcrumbs(); ?>
  <h1 class="archive-title">
    <?php
    if (is_home() && !is_front_page()) {
        single_post_title();
        if (!single_post_title('', false)) esc_html_e('記事一覧', 'gem-magazine');
    } elseif (is_search()) {
        printf(esc_html__('「%s」の検索結果', 'gem-magazine'), '<span style="color:var(--rose);">' . esc_html(get_search_query()) . '</span>');
    } elseif (is_category() || is_tag() || is_tax()) {
        single_term_title();
    } elseif (is_archive()) {
        the_archive_title();
    } else {
        esc_html_e('記事一覧', 'gem-magazine');
    }
    ?>
  </h1>
  <?php
  // カテゴリ・タグ・タクソノミーの説明文（SEO/GEOの文脈補強）
  if (is_category() || is_tag() || is_tax()) {
      $term_desc = term_description();
      if ($term_desc) {
          echo '<div class="archive-lead">' . wp_kses_post($term_desc) . '</div>';
      }
  }
  ?>
</div>

<section class="section" style="padding-top:32px;">
  <?php if (have_posts()) : ?>
    <div class="posts-grid">
      <?php $i = 0; while (have_posts()) : the_post(); gem_post_card($i); $i++; endwhile; ?>
    </div>

    <div class="pagination">
      <?php
      /**
       * ページ番号は ?paged=N 形式で統一。パーマリンク設定に依存せず、
       * "/category/xxx/page/2/" が別記事に誤ルーティングされる問題を防ぐ。
       */
      global $wp_query;
      $base = strtok(add_query_arg(array()), '?'); // 現在URL（クエリ除去）
      echo paginate_links(array(
          'base'      => trailingslashit($base) . '%_%',
          'format'    => '?paged=%#%',
          'current'   => max(1, (int) get_query_var('paged')),
          'total'     => isset($wp_query->max_num_pages) ? (int) $wp_query->max_num_pages : 1,
          'mid_size'  => 1,
          'prev_text' => '‹',
          'next_text' => '›',
      ));
      ?>
    </div>
  <?php else : ?>
    <p style="text-align:center;color:var(--muted);padding:40px 0;">
      <?php esc_html_e('該当する記事が見つかりませんでした。', 'gem-magazine'); ?>
    </p>
  <?php endif; ?>
</section>

<?php
get_template_part('template-parts/section-course');
get_footer();
