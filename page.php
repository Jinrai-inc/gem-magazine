<?php
/**
 * 固定ページ
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;
get_header();

while (have_posts()) : the_post();
?>
<?php if (has_post_thumbnail()) : ?>
  <div class="article-hero"><?php the_post_thumbnail('large'); ?></div>
<?php endif; ?>
<article <?php post_class('article-wrap'); ?>>
  <h1 class="entry-title"><?php the_title(); ?></h1>
  <div class="entry-content">
    <?php
    the_content();
    wp_link_pages(array('before' => '<div class="page-links">', 'after' => '</div>'));
    ?>
  </div>
</article>
<?php
endwhile;
get_footer();
