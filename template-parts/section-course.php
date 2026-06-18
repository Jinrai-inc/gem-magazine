<?php
/**
 * LINE無料講座セクション（トップ用）
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;
$line_url = gem_line_url();
$cta = gem_mod('gem_cta_primary', 'LINE無料講座を受ける');
?>
<section class="course" id="course">
  <div class="course-wrap reveal">
    <div class="course-grid">
      <div class="course-intro">
        <div class="course-head">
          <span class="badge-free"><?php esc_html_e('無料', 'gem-magazine'); ?></span>
          <h2 class="section-title"><?php esc_html_e('LINE無料講座のご案内', 'gem-magazine'); ?></h2>
        </div>
        <p class="course-lead"><?php esc_html_e('宝石鑑定の基礎から実践的な知識まで、LINEでわかりやすくお届けします。', 'gem-magazine'); ?></p>
        <div class="benefits">
          <?php foreach (gem_benefits() as $b) : ?>
            <div class="benefit"><span class="check"><?php echo gem_icon('check', 13, '#fff'); ?></span><?php echo esc_html($b); ?></div>
          <?php endforeach; ?>
        </div>
        <a class="btn-line btn-line--sm" href="<?php echo esc_url($line_url); ?>">
          <span class="btn-line-mark">LINE</span>
          <?php echo esc_html($cta); ?>
          <span class="btn-arrow"><?php echo gem_icon('chevron', 14, '#fff'); ?></span>
        </a>
      </div>

      <div class="course-steps">
        <div class="steps-title"><?php esc_html_e('＼ たった3ステップで宝石の知識が身につく！ ／', 'gem-magazine'); ?></div>
        <div class="steps">
          <?php foreach (gem_steps() as $s) : ?>
            <div class="step">
              <span class="step-no"><small>STEP</small><b><?php echo esc_html($s['no']); ?></b></span>
              <div class="step-icon"><?php echo gem_icon($s['icon'], 30); ?></div>
              <h3><?php echo esc_html($s['title']); ?></h3>
              <p><?php echo esc_html($s['desc']); ?></p>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>
