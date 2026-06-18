<?php
/**
 * フッター
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;
?>
<footer class="site-footer" id="course-footer">
  <div class="footer-grid">
    <div class="footer-brand">
      <div class="row">
        <?php echo gem_icon('logo', 26, '#E7D9AE'); ?>
        <span class="name"><?php bloginfo('name'); ?></span>
      </div>
      <p><?php echo esc_html(gem_mod('gem_brand_tagline', get_bloginfo('description'))); ?></p>
    </div>

    <div class="footer-col">
      <h4><?php esc_html_e('コンテンツ', 'gem-magazine'); ?></h4>
      <?php
      if (has_nav_menu('footer_content')) {
          wp_nav_menu(array('theme_location' => 'footer_content', 'container' => false, 'depth' => 1, 'fallback_cb' => false));
      } else { ?>
        <ul>
          <li><a href="<?php echo esc_url(home_url('/line-course/')); ?>"><?php esc_html_e('LINE無料講座について', 'gem-magazine'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/faq/')); ?>"><?php esc_html_e('よくある質問', 'gem-magazine'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('記事一覧', 'gem-magazine'); ?></a></li>
        </ul>
      <?php } ?>
    </div>

    <div class="footer-col">
      <h4><?php esc_html_e('メディアについて', 'gem-magazine'); ?></h4>
      <?php
      if (has_nav_menu('footer_about')) {
          wp_nav_menu(array('theme_location' => 'footer_about', 'container' => false, 'depth' => 1, 'fallback_cb' => false));
      } else { ?>
        <ul>
          <li><a href="<?php echo esc_url(home_url('/about-us/')); ?>"><?php esc_html_e('運営者情報', 'gem-magazine'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/ad-disclosure/')); ?>"><?php esc_html_e('アフィリエイト広告に関する表記', 'gem-magazine'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('お問い合わせ', 'gem-magazine'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>"><?php esc_html_e('プライバシーポリシー', 'gem-magazine'); ?></a></li>
          <li><a href="<?php echo esc_url(home_url('/disclaimer/')); ?>"><?php esc_html_e('免責事項', 'gem-magazine'); ?></a></li>
        </ul>
      <?php } ?>
    </div>
  </div>

  <div class="footer-bottom">
    &copy; <?php echo esc_html(date_i18n('Y')); ?> <?php bloginfo('name'); ?> &mdash; <?php echo esc_html(gem_mod('gem_brand_sub', 'GEMOLOGY MEDIA')); ?>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
