<?php
/**
 * 検索フォーム
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;
?>
<form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
  <label class="screen-reader-text" for="gem-s"><?php esc_html_e('検索', 'gem-magazine'); ?></label>
  <input type="search" id="gem-s" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php esc_attr_e('宝石名・記事・鑑定キーワードで検索', 'gem-magazine'); ?>">
  <button type="submit"><?php esc_html_e('検索', 'gem-magazine'); ?></button>
</form>
