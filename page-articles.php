<?php
/**
 * 記事一覧テンプレート（スラッグ "articles" のページに自動割当）
 *
 * WordPress の paged / page クエリ変数は固定ページのマルチページ機能や
 * canonical redirect と衝突するため、独自のクエリ変数 `?pg=N` を用いる。
 * これにより WP のパージネーション関連ロジックが一切介入しない。
 *
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;
get_header();

// 独自クエリ変数 ?pg=N（WPの paged/page とは無関係）
$paged = isset($_GET['pg']) ? max(1, (int) $_GET['pg']) : 1;

$q = new WP_Query(array(
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 9,
    'paged'               => $paged,
    'ignore_sticky_posts' => true,
));

$total_pages = (int) $q->max_num_pages;
$base_url    = get_permalink();
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

    <?php if ($total_pages > 1) : ?>
      <div class="pagination">
        <?php
        $win = 2; // 現在ページの前後何個を出すか
        $range_start = max(1, $paged - $win);
        $range_end   = min($total_pages, $paged + $win);

        $link = function ($p, $label = null, $class = '') use ($base_url) {
            $url = ($p <= 1) ? $base_url : add_query_arg('pg', $p, $base_url);
            $lbl = $label !== null ? $label : $p;
            echo '<a class="page-numbers ' . esc_attr($class) . '" href="' . esc_url($url) . '">' . esc_html($lbl) . '</a>';
        };
        $current = function ($p) {
            echo '<span class="page-numbers current">' . esc_html($p) . '</span>';
        };
        $dots = function () {
            echo '<span class="page-numbers dots">…</span>';
        };

        if ($paged > 1) $link($paged - 1, '‹', 'prev');

        if ($range_start > 1) {
            $link(1);
            if ($range_start > 2) $dots();
        }
        for ($p = $range_start; $p <= $range_end; $p++) {
            if ($p === $paged) $current($p); else $link($p);
        }
        if ($range_end < $total_pages) {
            if ($range_end < $total_pages - 1) $dots();
            $link($total_pages);
        }

        if ($paged < $total_pages) $link($paged + 1, '›', 'next');
        ?>
      </div>
    <?php endif; ?>
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
