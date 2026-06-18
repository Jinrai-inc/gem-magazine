<?php
/**
 * GEM-MAGAZINE ─ 固定ページ自動セットアップ
 *
 * テーマ有効化時に、指示書（INSTRUCTIONS.md）に定義された
 * 7枚の固定ページを `pages/` 配下の HTML を本文として自動作成します。
 * 既に同じスラッグのページが存在する場合は何もしません（冪等）。
 *
 * 管理画面の「外観 > GEM固定ページの導入」からいつでも再実行できます。
 *
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;

/**
 * 自動作成対象。スラッグ => [タイトル, HTMLファイル名]
 */
function gem_default_pages() {
    return array(
        'about-us'        => array('運営者情報',                      'about-us.html'),
        'line-course'     => array('LINE無料講座について',            'line-course.html'),
        'faq'             => array('よくある質問',                    'faq.html'),
        'contact'         => array('お問い合わせ',                    'contact.html'),
        'privacy-policy'  => array('プライバシーポリシー',            'privacy-policy.html'),
        'disclaimer'      => array('免責事項',                        'disclaimer.html'),
        'ad-disclosure'   => array('アフィリエイト広告に関する表記', 'ad-disclosure.html'),
    );
}

/**
 * 固定ページを一括で導入する。
 * @return array 作成・スキップしたスラッグの一覧
 */
function gem_install_default_pages() {
    $result = array('created' => array(), 'skipped' => array(), 'missing' => array());
    $dir = trailingslashit(get_template_directory()) . 'pages/';

    foreach (gem_default_pages() as $slug => $info) {
        list($title, $file) = $info;

        if (get_page_by_path($slug, OBJECT, 'page')) {
            $result['skipped'][] = $slug;
            continue;
        }

        $path = $dir . $file;
        if (!file_exists($path)) {
            $result['missing'][] = $slug;
            continue;
        }

        $content = file_get_contents($path);
        if ($content === false) {
            $result['missing'][] = $slug;
            continue;
        }

        $page_id = wp_insert_post(array(
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_content' => $content,
            'comment_status' => 'closed',
            'ping_status'  => 'closed',
        ), true);

        if (is_wp_error($page_id)) {
            $result['missing'][] = $slug;
        } else {
            $result['created'][] = $slug;
        }
    }

    return $result;
}

/* テーマ有効化時に1度だけ自動実行 */
function gem_install_default_pages_on_activation() {
    if (get_option('gem_default_pages_installed')) return;
    gem_install_default_pages();
    update_option('gem_default_pages_installed', 1);
}
add_action('after_switch_theme', 'gem_install_default_pages_on_activation');

/* ----- 管理画面メニュー：手動で再実行できるツール ----- */
function gem_register_pages_admin_menu() {
    add_theme_page(
        __('GEM固定ページの導入', 'gem-magazine'),
        __('GEM固定ページの導入', 'gem-magazine'),
        'manage_options',
        'gem-install-pages',
        'gem_render_pages_admin'
    );
}
add_action('admin_menu', 'gem_register_pages_admin_menu');

function gem_render_pages_admin() {
    if (!current_user_can('manage_options')) return;

    $report = null;
    if (isset($_POST['gem_install_pages']) && check_admin_referer('gem_install_pages')) {
        $report = gem_install_default_pages();
    }

    echo '<div class="wrap"><h1>' . esc_html__('GEM固定ページの導入', 'gem-magazine') . '</h1>';
    echo '<p>' . esc_html__('テーマに同梱された7枚の固定ページ（運営者情報・LINE無料講座・よくある質問・お問い合わせ・プライバシーポリシー・免責事項・アフィリエイト広告に関する表記）を作成します。既に同じスラッグで存在する場合はスキップされます。', 'gem-magazine') . '</p>';

    if ($report) {
        echo '<div class="notice notice-success"><p>';
        printf(
            esc_html__('作成: %1$d 件 ／ スキップ: %2$d 件 ／ 失敗: %3$d 件', 'gem-magazine'),
            count($report['created']), count($report['skipped']), count($report['missing'])
        );
        echo '</p></div>';
        if (!empty($report['created']))  echo '<p><strong>Created:</strong> '  . esc_html(implode(', ', $report['created'])) . '</p>';
        if (!empty($report['skipped']))  echo '<p><strong>Skipped:</strong> '  . esc_html(implode(', ', $report['skipped'])) . '</p>';
        if (!empty($report['missing']))  echo '<p><strong>Failed:</strong> '   . esc_html(implode(', ', $report['missing'])) . '</p>';
    }

    echo '<form method="post">';
    wp_nonce_field('gem_install_pages');
    echo '<p><button type="submit" name="gem_install_pages" class="button button-primary">' . esc_html__('固定ページを作成する', 'gem-magazine') . '</button></p>';
    echo '</form>';

    echo '<h2>' . esc_html__('置き換えが必要な値', 'gem-magazine') . '</h2>';
    echo '<p>' . esc_html__('本文中の【　】はプレースホルダーです。サイト名・運営者名・サイトURL・開設年月・お問い合わせ用メール・規約の制定日／改定日を実際の値に置き換えてください。', 'gem-magazine') . '</p>';
    echo '</div>';
}
