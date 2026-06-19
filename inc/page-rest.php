<?php
/**
 * GEM-MAGAZINE ─ 固定ページ用 REST API
 *
 * `wp-json/gem/v1/pages` 名前空間で、同梱の固定ページ（gem_default_pages() に
 * 列挙されたスラッグ）を slug 直指定で操作できるエンドポイントを追加する。
 *
 * 認証：WordPress 標準のアプリケーションパスワード（Basic 認証）
 *   例： curl -u "USER:APP_PASS" https://example.com/wp-json/gem/v1/pages
 *
 * エンドポイント：
 *   GET    /gem/v1/pages                  … 対象ページ一覧（slug/id/title/link/modified/status）
 *   GET    /gem/v1/pages/<slug>           … 単一ページ（title/content/excerpt/status/modified）
 *   POST   /gem/v1/pages/<slug>           … title/content/status を更新（部分更新可）
 *   POST   /gem/v1/pages/<slug>/reset     … 本文を同梱HTML（pages/<file>.html）で上書き
 *
 * パーミッション：edit_pages を持つユーザー。
 *
 * @package GEM-MAGAZINE
 */
if (!defined('ABSPATH')) exit;

/**
 * 書込系の権限チェック。アプリケーションパスワードを使ったBasic認証で
 * `wp_authenticate_application_password` を通れば current_user は確立される。
 */
function gem_rest_edit_permission() {
    if (current_user_can('edit_pages')) return true;
    return new WP_Error(
        'rest_forbidden',
        __('固定ページを編集する権限がありません。', 'gem-magazine'),
        array('status' => rest_authorization_required_code())
    );
}

/**
 * 読み取り系は基本公開（公開済みページのみ）。下書き等を含めたい場合は
 * edit_pages を要求するロジックを追加できるが、ここでは「公開ステータスは
 * 全員、それ以外は edit_pages 必須」とする。
 */
function gem_rest_read_permission() {
    return true; // 一覧は公開可。単一は post_status を見て個別判定する
}

/**
 * 指定スラッグが GEM 既定ページに含まれるか検証。
 * @return array|WP_Error [title, file] か WP_Error
 */
function gem_rest_lookup_slug($slug) {
    $defaults = function_exists('gem_default_pages') ? gem_default_pages() : array();
    if (!isset($defaults[$slug])) {
        return new WP_Error(
            'gem_unknown_slug',
            sprintf(__('未登録のスラッグです: %s', 'gem-magazine'), $slug),
            array('status' => 404)
        );
    }
    return $defaults[$slug]; // array($title, $file)
}

/**
 * slug から WP_Post を取得（page 投稿タイプに限定）。
 */
function gem_rest_get_page($slug) {
    $post = get_page_by_path($slug, OBJECT, 'page');
    if (!$post) {
        return new WP_Error(
            'gem_page_not_found',
            sprintf(__('スラッグ %s のページが見つかりません。テーマ有効化／インストーラーで作成してください。', 'gem-magazine'), $slug),
            array('status' => 404)
        );
    }
    return $post;
}

/**
 * 出力整形（単一）
 */
function gem_rest_format_page(WP_Post $post) {
    return array(
        'slug'     => $post->post_name,
        'id'       => (int) $post->ID,
        'title'    => $post->post_title,
        'status'   => $post->post_status,
        'content'  => $post->post_content,
        'excerpt'  => $post->post_excerpt,
        'link'     => get_permalink($post),
        'modified' => mysql_to_rfc3339($post->post_modified_gmt),
        'edit_url' => get_edit_post_link($post->ID, ''),
    );
}

/* -------------------------------------------------------- */
/*  ルート登録                                              */
/* -------------------------------------------------------- */
add_action('rest_api_init', function () {

    /* 一覧 */
    register_rest_route('gem/v1', '/pages', array(
        'methods'             => WP_REST_Server::READABLE,
        'permission_callback' => 'gem_rest_read_permission',
        'callback'            => function () {
            $items = array();
            foreach (gem_default_pages() as $slug => $info) {
                $post = get_page_by_path($slug, OBJECT, 'page');
                $items[] = array(
                    'slug'      => $slug,
                    'title'     => $info[0],
                    'file'      => $info[1],
                    'exists'    => (bool) $post,
                    'id'        => $post ? (int) $post->ID : null,
                    'status'    => $post ? $post->post_status : null,
                    'link'      => $post ? get_permalink($post) : null,
                    'modified'  => $post ? mysql_to_rfc3339($post->post_modified_gmt) : null,
                );
            }
            return rest_ensure_response($items);
        },
    ));

    /* 単一取得・更新 */
    register_rest_route('gem/v1', '/pages/(?P<slug>[a-z0-9-]+)', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'permission_callback' => 'gem_rest_read_permission',
            'callback'            => function (WP_REST_Request $req) {
                $slug = (string) $req['slug'];
                $info = gem_rest_lookup_slug($slug);
                if (is_wp_error($info)) return $info;
                $post = gem_rest_get_page($slug);
                if (is_wp_error($post)) return $post;
                if ($post->post_status !== 'publish' && !current_user_can('edit_post', $post->ID)) {
                    return new WP_Error('rest_forbidden', __('閲覧権限がありません。', 'gem-magazine'),
                        array('status' => rest_authorization_required_code()));
                }
                return rest_ensure_response(gem_rest_format_page($post));
            },
        ),
        array(
            'methods'             => array(WP_REST_Server::CREATABLE, WP_REST_Server::EDITABLE),
            'permission_callback' => 'gem_rest_edit_permission',
            'args'                => array(
                'title'   => array('type' => 'string', 'required' => false),
                'content' => array('type' => 'string', 'required' => false),
                'excerpt' => array('type' => 'string', 'required' => false),
                'status'  => array('type' => 'string', 'required' => false,
                                   'enum' => array('publish', 'draft', 'private', 'pending')),
            ),
            'callback'            => function (WP_REST_Request $req) {
                $slug = (string) $req['slug'];
                $info = gem_rest_lookup_slug($slug);
                if (is_wp_error($info)) return $info;
                $post = gem_rest_get_page($slug);
                if (is_wp_error($post)) return $post;
                if (!current_user_can('edit_post', $post->ID)) {
                    return new WP_Error('rest_forbidden', __('このページを編集する権限がありません。', 'gem-magazine'),
                        array('status' => rest_authorization_required_code()));
                }

                $update = array('ID' => $post->ID);
                $params = $req->get_json_params();
                if (!is_array($params)) $params = $req->get_params();

                if (array_key_exists('title', $params))   $update['post_title']   = (string) $params['title'];
                if (array_key_exists('content', $params)) $update['post_content'] = (string) $params['content'];
                if (array_key_exists('excerpt', $params)) $update['post_excerpt'] = (string) $params['excerpt'];
                if (array_key_exists('status', $params))  $update['post_status']  = (string) $params['status'];

                if (count($update) === 1) {
                    return new WP_Error('gem_no_fields', __('更新するフィールドが指定されていません。', 'gem-magazine'),
                        array('status' => 400));
                }

                $result = wp_update_post(wp_slash($update), true);
                if (is_wp_error($result)) return $result;

                clean_post_cache($result);
                return rest_ensure_response(gem_rest_format_page(get_post($result)));
            },
        ),
    ));

    /* 同梱HTMLへ初期化 */
    register_rest_route('gem/v1', '/pages/(?P<slug>[a-z0-9-]+)/reset', array(
        'methods'             => WP_REST_Server::CREATABLE,
        'permission_callback' => 'gem_rest_edit_permission',
        'callback'            => function (WP_REST_Request $req) {
            $slug = (string) $req['slug'];
            $info = gem_rest_lookup_slug($slug);
            if (is_wp_error($info)) return $info;
            list($title, $file) = $info;

            $path = trailingslashit(get_template_directory()) . 'pages/' . $file;
            if (!file_exists($path)) {
                return new WP_Error('gem_bundled_missing',
                    sprintf(__('同梱HTMLが見つかりません: %s', 'gem-magazine'), $file),
                    array('status' => 500));
            }
            $content = file_get_contents($path);
            if ($content === false) {
                return new WP_Error('gem_bundled_unreadable',
                    sprintf(__('同梱HTMLを読み込めません: %s', 'gem-magazine'), $file),
                    array('status' => 500));
            }

            $post = get_page_by_path($slug, OBJECT, 'page');
            if (!$post) {
                $page_id = wp_insert_post(array(
                    'post_title'   => $title,
                    'post_name'    => $slug,
                    'post_type'    => 'page',
                    'post_status'  => 'publish',
                    'post_content' => $content,
                ), true);
                if (is_wp_error($page_id)) return $page_id;
                $post = get_post($page_id);
            } else {
                if (!current_user_can('edit_post', $post->ID)) {
                    return new WP_Error('rest_forbidden', __('このページを編集する権限がありません。', 'gem-magazine'),
                        array('status' => rest_authorization_required_code()));
                }
                $result = wp_update_post(wp_slash(array(
                    'ID'           => $post->ID,
                    'post_content' => $content,
                )), true);
                if (is_wp_error($result)) return $result;
                $post = get_post($result);
            }

            clean_post_cache($post->ID);
            return rest_ensure_response(gem_rest_format_page($post));
        },
    ));
});
