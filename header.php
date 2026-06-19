<?php
/**
 * ヘッダー
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header" id="top">
  <div class="header-inner">
    <a class="brand" href="<?php echo esc_url(home_url('/')); ?>">
      <?php if (has_custom_logo()) : ?>
        <span class="brand-mark"><?php the_custom_logo(); ?></span>
      <?php else : ?>
        <span class="brand-mark"><?php echo gem_icon('logo', 30, '#C2A35A'); ?></span>
      <?php endif; ?>
      <span class="brand-names">
        <span class="brand-name"><?php bloginfo('name'); ?></span>
        <?php $sub = gem_mod('gem_brand_sub', 'GEMOLOGY MEDIA'); if ($sub) : ?>
          <span class="brand-sub"><?php echo esc_html($sub); ?></span>
        <?php endif; ?>
      </span>
      <?php $tagline = gem_mod('gem_brand_tagline', get_bloginfo('description')); if ($tagline) : ?>
        <span class="brand-tagline"><?php echo esc_html($tagline); ?></span>
      <?php endif; ?>
    </a>

    <nav class="nav-desktop" aria-label="<?php esc_attr_e('グローバルナビ', 'gem-magazine'); ?>">
      <?php
      if (has_nav_menu('primary')) {
          wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'depth' => 0, 'fallback_cb' => false));
      } else {
          gem_default_menu();
      }
      ?>
    </nav>

    <div class="header-actions">
      <button class="icon-btn" data-search-toggle aria-label="<?php esc_attr_e('検索', 'gem-magazine'); ?>">
        <?php echo gem_icon('search', 20); ?>
      </button>
      <button class="icon-btn burger" data-menu-toggle aria-label="<?php esc_attr_e('メニュー', 'gem-magazine'); ?>">
        <?php echo gem_icon('menu', 22); ?>
      </button>
    </div>
  </div>

  <div class="search-bar" data-search-bar>
    <?php get_search_form(); ?>
  </div>

  <div class="mobile-menu" data-mobile-menu>
    <?php
    if (has_nav_menu('primary')) {
        wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'depth' => 0, 'fallback_cb' => false));
    } else {
        gem_default_menu();
    }
    ?>
  </div>
</header>
