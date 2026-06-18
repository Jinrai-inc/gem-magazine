<?php
/**
 * 記事一覧 / ブログ / 検索結果 / アーカイブ 共通
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;
get_header();
?>

<div class="archive-head">
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
</div>

<section class="section" style="padding-top:32px;">
  <?php if (have_posts()) : ?>
    <div class="posts-grid">
      <?php $i = 0; while (have_posts()) : the_post(); gem_post_card($i); $i++; endwhile; ?>
    </div>

    <div class="pagination">
      <?php echo paginate_links(array('mid_size' => 1, 'prev_text' => '‹', 'next_text' => '›')); ?>
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
