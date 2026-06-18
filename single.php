<?php
/**
 * 単一記事
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;
get_header();

while (have_posts()) : the_post();
    $cat = get_the_category();
?>
<?php if (has_post_thumbnail()) : ?>
  <div class="article-hero"><?php the_post_thumbnail('large'); ?></div>
<?php endif; ?>

<article <?php post_class('article-wrap'); ?>>
  <?php if (!empty($cat)) : ?>
    <a class="entry-cat" href="<?php echo esc_url(get_category_link($cat[0]->term_id)); ?>"><?php echo esc_html($cat[0]->name); ?></a>
  <?php endif; ?>
  <h1 class="entry-title"><?php the_title(); ?></h1>
  <div class="entry-meta">
    <span><?php echo gem_icon('clock', 13); ?> <?php echo esc_html(get_the_date('Y.m.d')); ?></span>
    <?php if (get_the_author()) : ?><span><?php the_author(); ?></span><?php endif; ?>
  </div>

  <div class="entry-content">
    <?php
    the_content();
    wp_link_pages(array('before' => '<div class="page-links">', 'after' => '</div>'));
    ?>
  </div>

  <?php get_template_part('template-parts/inline-cta'); ?>

  <?php if (has_tag()) : ?>
    <div style="margin-top:24px;display:flex;flex-wrap:wrap;gap:8px;">
      <?php
      foreach (get_the_tags() as $tag) {
          printf('<a href="%s" style="font-size:12px;font-weight:700;color:var(--ink-2);border:1px solid var(--border);border-radius:999px;padding:6px 12px;">#%s</a>',
              esc_url(get_tag_link($tag->term_id)), esc_html($tag->name));
      }
      ?>
    </div>
  <?php endif; ?>
</article>

<?php
    if (comments_open() || get_comments_number()) {
        echo '<div class="article-wrap" style="padding-top:0;">';
        comments_template();
        echo '</div>';
    }
endwhile;

get_template_part('template-parts/section-course');
get_footer();
