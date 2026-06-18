<?php
/**
 * フロントページ（トップ）
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;
get_header();

$line_url   = gem_line_url();
$cta        = gem_mod('gem_cta_primary', 'LINE無料講座を受ける');
$hero_img   = get_header_image();
$kicker     = gem_mod('gem_hero_kicker', '初心者からでも安心して学べる');
$accent     = gem_mod('gem_hero_accent', '宝石鑑定');
$title_rest = gem_mod('gem_hero_title', 'の知識を、やさしく学ぶ。');
$sub        = gem_mod('gem_hero_sub', '鑑定額の見方や鑑定のしくみ、宝石の実践的な知識まで、初心者の方にもわかりやすく解説します。');
$gift       = gem_mod('gem_hero_gift', '査定額の見方・基礎知識・選び方を配信');
?>

<!-- ===== Hero / FV ===== -->
<section class="hero">
  <?php if ($hero_img) : ?>
    <div class="hero-bg" style="background-image:url('<?php echo esc_url($hero_img); ?>');"></div>
  <?php else : ?>
    <div class="hero-bg" style="background:radial-gradient(ellipse at 18% 30%, #f3d6e0 0%, transparent 45%), radial-gradient(ellipse at 82% 40%, #d9c6e8 0%, transparent 45%), linear-gradient(135deg,#F3ECE6 0%,#EADFE0 100%);"></div>
  <?php endif; ?>
  <div class="hero-overlay"></div>

  <div class="hero-inner">
    <div class="hero-card">
      <div class="hero-card-inner">
        <div class="ornament">
          <span class="rule-l"></span>
          <?php echo gem_icon('flourish', 22, 'currentColor', 10); ?>
          <span class="rule-r"></span>
        </div>
        <div class="kicker"><?php echo esc_html($kicker); ?></div>
        <h1 class="hero-title"><span class="accent"><?php echo esc_html($accent); ?></span><?php echo esc_html($title_rest); ?></h1>
        <p class="hero-sub"><?php echo nl2br(esc_html($sub)); ?></p>

        <a class="btn-line" href="<?php echo esc_url($line_url); ?>">
          <span class="btn-line-mark">LINE</span>
          <span><?php echo esc_html($cta); ?></span>
          <span class="btn-arrow"><?php echo gem_icon('chevron', 16, '#fff'); ?></span>
        </a>

        <a class="btn-secondary" href="#content">
          <span><?php esc_html_e('無料で学べる内容を見る', 'gem-magazine'); ?></span>
          <?php echo gem_icon('chevron', 16, '#C2A35A'); ?>
        </a>

        <?php if ($gift) : ?>
          <div class="gift-line"><?php echo gem_icon('gift', 17, '#D2778A'); ?><span><?php echo esc_html($gift); ?></span></div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- ===== おすすめコンテンツ ===== -->
<section class="section" id="content">
  <div class="reveal">
    <div class="section-head">
      <?php echo gem_icon('book-open', 26, '#D87E92'); ?>
      <h2 class="section-title"><?php esc_html_e('はじめての方におすすめのコンテンツ', 'gem-magazine'); ?></h2>
    </div>
    <div class="cards-grid">
      <?php foreach (gem_content_cards() as $c) : ?>
        <a class="content-card" href="<?php echo esc_url($c['url']); ?>">
          <span class="icon-circle"><?php echo gem_icon($c['icon'], 34); ?></span>
          <h3><?php echo esc_html($c['title']); ?></h3>
          <p><?php echo esc_html($c['desc']); ?></p>
          <span class="more-link"><?php esc_html_e('詳しく見る', 'gem-magazine'); ?> <?php echo gem_icon('chevron', 13); ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===== 新着記事 ===== -->
<section class="section" id="posts">
  <div class="reveal">
    <div class="section-head section-head--between">
      <div class="left">
        <?php echo gem_icon('gem', 22, '#D87E92'); ?>
        <h2 class="section-title"><?php esc_html_e('新着記事', 'gem-magazine'); ?></h2>
      </div>
      <a class="head-link" href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/')); ?>">
        <?php esc_html_e('記事一覧へ', 'gem-magazine'); ?> <?php echo gem_icon('chevron', 13); ?>
      </a>
    </div>
    <div class="posts-grid">
      <?php
      $q = new WP_Query(array('posts_per_page' => 4, 'ignore_sticky_posts' => true));
      if ($q->have_posts()) :
          $i = 0;
          while ($q->have_posts()) : $q->the_post();
              gem_post_card($i);
              $i++;
          endwhile;
          wp_reset_postdata();
      else : ?>
        <p style="color:var(--muted);"><?php esc_html_e('記事がまだありません。投稿を追加すると、ここに最新4件が表示されます。', 'gem-magazine'); ?></p>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- ===== LINE無料講座 ===== -->
<?php get_template_part('template-parts/section-course'); ?>

<!-- ===== 選ばれています ===== -->
<section class="section" style="padding-bottom:12px;">
  <div class="reveal">
    <div class="trust-head">
      <?php echo gem_icon('leaf', 20, 'currentColor', 26); ?>
      <h2 class="section-title"><?php esc_html_e('多くの方に選ばれています', 'gem-magazine'); ?></h2>
      <span class="leaf-r"><?php echo gem_icon('leaf', 20, 'currentColor', 26); ?></span>
    </div>
    <div class="trust-grid">
      <?php foreach (gem_trust() as $t) : ?>
        <div class="trust-item">
          <span class="icon"><?php echo gem_icon($t['icon'], 30); ?></span>
          <h3><?php echo esc_html($t['title']); ?></h3>
          <p><?php echo esc_html($t['desc']); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php get_footer();
