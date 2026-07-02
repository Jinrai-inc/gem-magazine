<?php
/**
 * 記事一覧テンプレート（スラッグ "articles" のページに自動割当）
 *
 * WordPressの投稿ページ設定(page_for_posts)を触らずに、この固定ページに
 * アクセスした時だけ「投稿の3カラム一覧」を出す。
 *
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;
get_header();

$paged = max(1, get_query_var('paged') ? (int) get_query_var('paged') : (int) get_query_var('page'));
$q = new WP_Query(array(
    'post_type'      => 'post',
    'posts_per_page' => 9,
    'paged'          => $paged,
    'ignore_sticky_posts' => true,
));
?>

<div class="archive-head">
  <h1 class="archive-title"><?php the_title(); ?></h1>
  <?php
  while (have_posts()) : the_post();
      $content = get_the_content();
      if (trim(wp_strip_all_tags($content)) !== '') {
          echo '<div class="archive-lead">';
          the_content();
          echo '</div>';
      }
  endwhile;
  ?>
</div>

<section class="section" style="padding-top:32px;">
  <?php if ($q->have_posts()) : ?>
    <div class="posts-grid">
      <?php $i = 0; while ($q->have_posts()) : $q->the_post(); gem_post_card($i); $i++; endwhile; ?>
    </div>

    <div class="pagination">
      <?php
      echo paginate_links(array(
          'base'      => trailingslashit(get_permalink()) . '%_%',
          'format'    => 'page/%#%/',
          'current'   => $paged,
          'total'     => $q->max_num_pages,
          'mid_size'  => 1,
          'prev_text' => '‹',
          'next_text' => '›',
      ));
      ?>
    </div>
  <?php else : ?>
    <p style="text-align:center;color:var(--muted);padding:40px 0;">
      <?php esc_html_e('記事がまだありません。', 'gem-magazine'); ?>
    </p>
  <?php endif;
  wp_reset_postdata();
  ?>
</section>

<?php
get_template_part('template-parts/section-course');
get_footer();
