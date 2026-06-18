<?php
/**
 * 記事下のLINE誘導CTA（single用）
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;
$line_url = gem_line_url();
$cta = gem_mod('gem_cta_primary', 'LINE無料講座を受ける');
?>
<div class="inline-cta">
  <h3><?php esc_html_e('宝石の知識を、無料でもっと深く。', 'gem-magazine'); ?></h3>
  <p><?php esc_html_e('鑑定の基礎から実践まで、LINEでやさしくお届けします。', 'gem-magazine'); ?></p>
  <a class="btn-line btn-line--sm" href="<?php echo esc_url($line_url); ?>" style="margin:0 auto;">
    <span class="btn-line-mark">LINE</span>
    <?php echo esc_html($cta); ?>
    <span class="btn-arrow"><?php echo gem_icon('chevron', 14, '#fff'); ?></span>
  </a>
</div>
