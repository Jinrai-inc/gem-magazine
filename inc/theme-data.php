<?php
/**
 * トップページのセクション用データ（編集しやすいよう一箇所に集約）
 * フィルター gem_content_cards / gem_steps / gem_trust / gem_benefits で上書き可能。
 */
if (!defined('ABSPATH')) exit;

/** 「はじめての方におすすめのコンテンツ」4枚 */
function gem_content_cards() {
    return apply_filters('gem_content_cards', array(
        array('icon' => 'loupe',   'title' => 'はじめての宝石鑑定', 'desc' => '宝石鑑定とは？鑑定士の仕事や鑑定書の役割をやさしく解説。', 'url' => '#'),
        array('icon' => 'receipt', 'title' => '鑑定額の見方',       'desc' => '鑑定書のどこを見ればいい？査定額のポイントをわかりやすく。', 'url' => '#'),
        array('icon' => 'book',    'title' => '宝石の基礎講座',     'desc' => '宝石の種類や価値の決まり方など、基礎知識を丁寧に学べます。', 'url' => '#'),
        array('icon' => 'flag',    'title' => '講座受講の流れ',     'desc' => 'LINE無料講座の内容と、受講のステップを紹介します。', 'url' => '#'),
    ));
}

/** LINE講座「3ステップ」 */
function gem_steps() {
    return apply_filters('gem_steps', array(
        array('no' => '01', 'icon' => 'phone', 'title' => 'LINEで友だち追加',       'desc' => 'ボタンから友だち追加で講座がスタートします。'),
        array('no' => '02', 'icon' => 'learn', 'title' => '基礎からやさしく学ぶ',   'desc' => '鑑定額の見方や宝石の基礎をわかりやすく解説します。'),
        array('no' => '03', 'icon' => 'gem',   'title' => '知識を日常や取引に活かす', 'desc' => '学んだ知識を、宝石選びや査定時に活用できます。'),
    ));
}

/** LINE講座の特典チェックリスト */
function gem_benefits() {
    return apply_filters('gem_benefits', array(
        'スマホで完結・スキマ時間に学べる',
        '初心者でもわかるやさしい解説',
        '質問もできて安心のサポート付き',
    ));
}

/** 「多くの方に選ばれています」4項目 */
function gem_trust() {
    return apply_filters('gem_trust', array(
        array('icon' => 'shield', 'title' => '信頼できる情報',     'desc' => '宝石鑑定の専門知識をもとに、正確で信頼できる情報を提供します。'),
        array('icon' => 'gem',    'title' => '初心者にやさしい',   'desc' => '専門用語もかみくだいて解説。はじめての方でも安心して学べます。'),
        array('icon' => 'yen',    'title' => '実践的で役立つ',     'desc' => '査定や売買、資産価値の判断に役立つ知識が身につきます。'),
        array('icon' => 'chat',   'title' => 'LINEで気軽に質問OK', 'desc' => '講座内でわからないことは、LINEで気軽に質問できます。'),
    ));
}

/** LINE友だち追加URL（カスタマイザーで設定 / 既定は #） */
function gem_line_url() {
    return esc_url(get_theme_mod('gem_line_url', '#'));
}
