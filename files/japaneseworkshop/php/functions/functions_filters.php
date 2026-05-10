<?php

// 投稿にページヘッダーを表示しない
add_filter( 'lightning_is_page_header', function(){
	$do_display_site_contents = do_display_site_contents();
	return $do_display_site_contents;
} );
add_filter( 'lightning_is_site_header', function(){
	$do_display_site_contents = do_display_site_contents();
	return $do_display_site_contents;
} );
add_filter( 'lightning_is_breadcrumb', function(){
	$do_display_site_contents = do_display_site_contents();
	return $do_display_site_contents;
} );
add_filter( 'lightning_is_site_footer', function(){
	$do_display_site_contents = do_display_site_contents();
	return $do_display_site_contents;
} );

//購読者がログイン時に管理バーを表示させない
function my_function_admin_bar($content) {
	return ( current_user_can("administrator") ) ? $content : false;
}
add_filter( 'show_admin_bar' , 'my_function_admin_bar');

// Multisite Language Switcherで「翻訳はみつかりませんでした」の文字を上書き
function my_msls_widget_alternative_content( $text ) {
	return '';
}
add_filter( 'msls_widget_alternative_content', 'my_msls_widget_alternative_content' );

// 送信者名を変更
add_filter( 'wp_mail_from_name', function( $email_from ) {
	$str_mysite_title = get_bloginfo('name');
	return $str_mysite_title;
});

// 送信者メールアドレスを変更
add_filter( 'wp_mail_from', function( $email_address ) {
	global $str_mysite_mail_address_info;
	return $str_mysite_mail_address_info;
});

// ディスプレイネームを変更
function set_display_name( $fields, $toggle ) {
	$fields['display_name'] = $fields['nickname'];
	return $fields;
}
add_filter( 'wpmem_register_data', 'set_display_name',10, 2 );
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
add_filter( 'rest_endpoints', function ( $endpoints ) {
    $remove = [
        '/wp/v2/posts',
        '/wp/v2/posts/(?P<id>[\d]+)',
        '/wp/v2/posts/(?P<parent>[\d]+)/revisions',
        '/wp/v2/posts/(?P<parent>[\d]+)/revisions/(?P<id>[\d]+)',

        '/wp/v2/pages',
        '/wp/v2/pages/(?P<id>[\d]+)',
        '/wp/v2/pages/(?P<parent>[\d]+)/revisions',
        '/wp/v2/pages/(?P<parent>[\d]+)/revisions/(?P<id>[\d]+)',

        '/wp/v2/media',
        '/wp/v2/media/(?P<id>[\d]+)',

        '/wp/v2/types',
        '/wp/v2/types/(?P<type>[\w-]+)',

        '/wp/v2/statuses',
        '/wp/v2/statuses/(?P<status>[\w-]+)',

        '/wp/v2/taxonomies',
        '/wp/v2/taxonomies/(?P<taxonomy>[\w-]+)',

        '/wp/v2/categories',
        '/wp/v2/categories/(?P<id>[\d]+)',

        '/wp/v2/tags',
        '/wp/v2/tags/(?P<id>[\d]+)',

        '/wp/v2/users',
        '/wp/v2/users/(?P<id>[\d]+)',
        '/wp/v2/users/me',

        '/wp/v2/comments',
        '/wp/v2/comments/(?P<id>[\d]+)',

        '/wp/v2/settings',
    ];

    foreach ( $remove as $route ) {
        if ( isset( $endpoints[$route] ) ) {
            unset( $endpoints[$route] );
        }
    }
    return $endpoints;
});

// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
function my_wpmem_login_redirect( $redirect_to, $user_id ) {
	
	// ホワイトリスト指定
    $keys = [ 'grammar_unique_code'];
    return jws_resolve_login_redirect(
        $redirect_to,
        isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '',
        $keys
    );
}
add_filter( 'wpmem_login_redirect', 'my_wpmem_login_redirect', 999, 2 );

function my_core_login_redirect( $redirect_to, $request, $user ) {
    // ホワイトリスト指定
    $keys = [ 'grammar_unique_code'];
    return jws_resolve_login_redirect(
        $redirect_to,
        isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : '',
        $keys
    );
}
add_filter( 'login_redirect', 'my_core_login_redirect', 999, 3 );

function jws_resolve_login_redirect($redirect_to, $current_url = '', $preserve_keys = null)
{
	
	$int_selected_language = jws_get_language_index();
	
    $url_home_current = get_home_url(
        get_data_blog_id_from_selected_language($int_selected_language ?? null),
        '/'
    );

    $ref = wp_get_referer();
    $ref_path = $ref ? wp_parse_url($ref, PHP_URL_PATH) : '';
    $to_path = $redirect_to ? wp_parse_url($redirect_to, PHP_URL_PATH) : '';
    $cur_path = $current_url ? wp_parse_url($current_url, PHP_URL_PATH) : '';

    $is_special_page = function ($path) {
        if (!$path) {
            return false;
        }
        $page = basename(untrailingslashit($path));
        return in_array($page, ['login', 'logout'], true);
    };

    $is_login_or_logout_ref = $is_special_page($ref_path);
    $is_login_or_logout_to = $is_special_page($to_path);
    $is_login_or_logout_cur = $is_special_page($cur_path);

    $blacklist = [
        'redirect_to', 'referredby', '_wpnonce', '_wp_http_referer',
        'pwd', 'pass1', 'pass2', 'password', 'log', 'username', 'rememberme',
        'jetpack-sso-show-default-form'
    ];

    $params = jws_collect_params([$redirect_to, $current_url, $ref], $preserve_keys, $blacklist, $int_selected_language);

    // login/logout 近辺から来た場合：レベルに応じた遷移を最優先
    if ($is_login_or_logout_ref || $is_login_or_logout_to || $is_login_or_logout_cur) {

        $url = generate_redirect_url_after_login($int_selected_language);

        if ($url !== null) {
            $url = jws_append_params_if_missing($url, $params);
            $url = wp_validate_redirect($url, $url_home_current);
            return $url;
        }

        $dest = $url_home_current;
        $dest = jws_append_params_if_missing($dest, $params);
        $dest = wp_validate_redirect($dest, $url_home_current);
        return esc_url($dest);
    }

    // 明示された redirect_to がある場合は優先（ただし後段でレベル上書き）
    if (!empty($_REQUEST['redirect_to'])) {
        $dest = esc_url_raw($_REQUEST['redirect_to']);
        $dest = jws_append_params_if_missing($dest, $params);
        $dest = wp_validate_redirect($dest, $url_home_current);

        $url = generate_redirect_url_after_login($int_selected_language);

        if ($url !== null) {
            $url = jws_append_params_if_missing($url, $params);
            $url = wp_validate_redirect($url, $url_home_current);
            return $url;
        }

        return $dest;
    }

    // それ以外：通常の $redirect_to を採用（ただし後段でレベル上書き）
    $dest = $redirect_to ? $redirect_to : $url_home_current;
    $dest = jws_append_params_if_missing($dest, $params);
    $dest = wp_validate_redirect($dest, $url_home_current);

    // ユーザーレベルに応じて最終遷移先を上書き
    $url = generate_redirect_url_after_login($int_selected_language);

    if ($url !== null) {
        $url = jws_append_params_if_missing($url, $params);
        $url = wp_validate_redirect($url, $url_home_current);
        return $url;
    }

    return $dest;
}

function jws_parse_query_to_array( $url ) {
    if ( ! $url ) {
        return [];
    }
    $query = wp_parse_url( $url, PHP_URL_QUERY );
    if ( ! $query ) {
        return [];
    }
    parse_str( $query, $args );
    return is_array( $args ) ? $args : [];
}

function jws_collect_params( $urls, $preserve_keys = null, $blacklist = [], $int_selected_language ) {
    $collected = [];
    $urls = array_values( array_filter( $urls ) );
	$url_home_current = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		'/'
	);

	$urls[] = add_query_arg(
		$_GET,
		$url_home_current
	);

    foreach ( $urls as $url ) {
        $args = jws_parse_query_to_array( $url );
        foreach ( $args as $k => $v ) {

            if ( in_array( $k, $blacklist, true ) ) {
                continue;
            }

            if ( is_array( $preserve_keys ) && ! in_array( $k, $preserve_keys, true ) ) {
                continue;
            }

            if ( is_array( $v ) ) {
                continue;
            }
            $v = sanitize_text_field( wp_unslash( (string) $v ) );
            if ( $v === '' ) {
                continue;
            }
            if ( ! array_key_exists( $k, $collected ) ) {
                $collected[ $k ] = $v;
            }
        }
    }
    return $collected;
}

function jws_append_params_if_missing( $url, $params ) {
    if ( ! $url || empty( $params ) ) {
        return $url;
    }
    $existing = jws_parse_query_to_array( $url );
    foreach ( $params as $k => $v ) {
        if ( ! array_key_exists( $k, $existing ) ) {
            $url = add_query_arg( $k, $v, $url );
        }
    }
    return $url;
}
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー



function wise_redirect_from_home_by_user_level()
{
    if (!is_front_page()) {
        return;
    }

    if (!is_user_logged_in()) {
        return;
    }

	$int_selected_language = jws_get_language_index();
	
    $url = generate_redirect_url_after_login($int_selected_language);

    if ($url === null) {
        return;
    }

    wp_safe_redirect($url);
    exit;
}
add_action('template_redirect', 'wise_redirect_from_home_by_user_level');
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
add_filter('authenticate', 'jws_block_deleted_users_login', 30, 3);

function jws_block_deleted_users_login($user, $username, $password) {

    if ($user instanceof WP_User) {

        $status = get_user_meta($user->ID, 'jws_account_status', true);

        if ($status === 'deleted') {

            $int_selected_language = jws_get_language_index();

            $arr_login_block_messages = [
                'deleted' => [
                    'このアカウントは退会手続きが完了しているため、ログインできません。',
                    '此帳號已完成退會手續，無法再登入。',
                    'This account has been deleted and cannot be used to log in.',
                ],
            ];

            return new WP_Error(
                'jws_account_deleted',
                $arr_login_block_messages['deleted'][$int_selected_language]
            );
        }
    }

    return $user;
}
