<?php

// CSRF対策
// function s_start(){
// add_action('init', 's_start');
// 	 session_start();
// 	 session_regenerate_id(true);
// }


add_action( 'wp_enqueue_scripts', 'add_files', 9999 );
function add_files() {
	
	$address_html2canvas_file = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
	$address_html2pdf_file = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.2/html2pdf.bundle.min.js';
	$address_jspdf_file = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
	$address_jszip_file = 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js';
	$address_pdfjs_file = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
	$address_pdfjs_worker = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
	
	wp_enqueue_script(
		'html2canvas',
		$address_html2canvas_file,
		[],
		false,
		true
	);
	wp_enqueue_script(
		'jspdf',
		$address_jspdf_file,
		[],
		false,
		true
	);
	
	wp_enqueue_script(
		'html2pdf',
		$address_html2pdf_file,
		['html2canvas', 'jspdf'],
		false,
		true
	);

	wp_enqueue_script(
		'jszip',
		$address_jszip_file,
		array(),
		false,
		true
	);

	wp_enqueue_script(
		'pdfjs',
		$address_pdfjs_file,
		[],
		false,
		true
	);

	wp_add_inline_script(
		'pdfjs',
		'pdfjsLib.GlobalWorkerOptions.workerSrc = "' . $address_pdfjs_worker . '";',
		'after'
	);

	
	// ーーーーーーーーーーーーーーーーーーーーCSS
	$dir_rel = '/css';

    $deps = [];

    $first_layers = ['css-jws-common'];

    $last_layers = [];

    jws_enqueue_css_dir_with_ordered_layers($dir_rel, $deps, $first_layers, $last_layers);
	
	// ーーーーーーーーーーーーーーーーーーーーJS
	$deps_core = jws_enqueue_js_dir_with_ordered_layers(
		'/js/core',
		[],
		[
			'js-jws-constants',
			'js-jws-variables',
			'js-jws-masta-ids',
			'js-jws-urls',
		],
		[
			'js-jws-rules',
			'js-jws-ui',
			'js-jws-flags',
		]
	);
	$deps = $deps_core;


	$deps_wise_utils = jws_enqueue_js_dir_with_ordered_layers(
		'/js/utils',
		$deps,
		[],
		[]
	);
	$deps = array_merge($deps, $deps_wise_utils);

	
	$deps_wise_core = jws_enqueue_js_dir_with_ordered_layers(
		'/js/wise-core',
		$deps,
		[
			'js-jws-wise-core-logic',
			'js-jws-wise-core-element-logic',
			'js-jws-wise-core-sentence-logic'
		],
		['js-jws-wise-core-bootstrap']
	);
	$deps = array_merge($deps, $deps_wise_core);

	
	$deps_wise_panel = jws_enqueue_js_dir_with_ordered_layers(
		'/js/wise-panel',
		$deps,
		[],
		[]
	);
	$deps = array_merge($deps, $deps_wise_panel);


	$deps_wise_panel_whiteboard_ui = jws_enqueue_js_dir_with_ordered_layers(
		'/js/wise-panel-whiteboard-ui',
		$deps,
		['js-jws-wise-panel-whiteboard-ui-logic'],
		[]
	);
	$deps = array_merge($deps, $deps_wise_panel_whiteboard_ui);

	
	$deps_wise_modules = jws_enqueue_js_dir_with_ordered_layers(
		'/js/wise-modules',
		$deps,
		[],
		[]
	);
	$deps = array_merge($deps, $deps_wise_modules);

	
	$deps_dashboard = jws_enqueue_js_dir_with_ordered_layers(
		'/js/dashboard',
		$deps,
		['js-jws-dashboard-core'],
		[]
	);
	$deps = array_merge($deps, $deps_dashboard);


	$deps_entry = jws_enqueue_js_dir_with_ordered_layers(
		'/js/entry',
		$deps,
		[],
		[]
	);
	$deps = array_merge($deps, $deps_entry);


	wp_enqueue_script(
		'js-jws-bootstrap',
		get_stylesheet_directory_uri() . '/js/js-jws-bootstrap.js',
		$deps,
		filemtime(get_stylesheet_directory() . '/js/js-jws-bootstrap.js'),
		true
	);


	// ーーーーーーーーーーーーーーーーーーーーurl

	$url_home_main = get_home_url(
		get_main_site_id(),
		'/'
	);

	$url_home_current = get_home_url(
		get_current_blog_id(),
		'/'
	);
	wp_localize_script(
		'js-jws-urls',
		'JWS_URLS',
		[
			'mainHomeUrl' => trailingslashit($url_home_main), // main
			'currentHomeUrl' => trailingslashit($url_home_current), // current site
			'networkHomeUrl' => trailingslashit(network_home_url('/')),
			'themeUrl' => trailingslashit(get_stylesheet_directory_uri()),
		]
	);

}

function jws_enqueue_js_dir_with_ordered_layers($dir_rel, $deps, $first_layers, $last_layers) {

    $dir_rel = '/' . ltrim($dir_rel, '/');

    $dir_abs = get_stylesheet_directory() . $dir_rel;
    $dir_uri = get_stylesheet_directory_uri() . $dir_rel;

    $files = glob($dir_abs . '/*.js');
    if ($files === false) {
        return [];
    }

    natsort($files);

    $first_set = array_fill_keys($first_layers, true);
    $last_set = array_fill_keys($last_layers, true);

    $mid_files = [];
    foreach ($files as $abs_path) {

        $base_name = basename($abs_path);
        $handle = pathinfo($base_name, PATHINFO_FILENAME);

        if (isset($first_set[$handle]) || isset($last_set[$handle])) {
            continue;
        }

        $mid_files[] = [
            'handle' => $handle,
            'abs' => $abs_path,
            'uri' => $dir_uri . '/' . $base_name,
        ];
    }

    $enqueued_first = [];
    $enqueued_mid = [];
    $enqueued_last = [];

    // 1) First layers: order guaranteed (dependency chain)
    $prev = $deps;
    foreach ($first_layers as $handle) {

        $abs = $dir_abs . '/' . $handle . '.js';
        $uri = $dir_uri . '/' . $handle . '.js';

        if (!file_exists($abs)) {
            continue;
        }

        wp_enqueue_script(
            $handle,
            $uri,
            $prev,
            filemtime($abs),
            true
        );

        $enqueued_first[] = $handle;
        $prev = [$handle];
    }

    $deps_for_mid = !empty($enqueued_first)
        ? [$enqueued_first[count($enqueued_first) - 1]]
        : $deps;

    // 2) Middle: depends on last first-layer (order not enforced among middles)
    foreach ($mid_files as $info) {

        wp_enqueue_script(
            $info['handle'],
            $info['uri'],
            $deps_for_mid,
            filemtime($info['abs']),
            true
        );

        $enqueued_mid[] = $info['handle'];
    }

    $deps_for_last = $deps_for_mid;
    if (!empty($enqueued_mid)) {
        $deps_for_last = array_merge($deps_for_mid, $enqueued_mid);
    }

    // 3) Last layers: order guaranteed (dependency chain)
    $prev = $deps_for_last;
    foreach ($last_layers as $handle) {

        $abs = $dir_abs . '/' . $handle . '.js';
        $uri = $dir_uri . '/' . $handle . '.js';

        if (!file_exists($abs)) {
            continue;
        }

        wp_enqueue_script(
            $handle,
            $uri,
            $prev,
            filemtime($abs),
            true
        );

        $enqueued_last[] = $handle;
        $prev = [$handle];
    }

    return array_merge($enqueued_first, $enqueued_mid, $enqueued_last);
}

function jws_enqueue_css_dir_with_ordered_layers($dir_rel, $deps, $first_layers, $last_layers) {

    $dir_rel = '/' . ltrim($dir_rel, '/');

    $dir_abs = get_stylesheet_directory() . $dir_rel;
    $dir_uri = get_stylesheet_directory_uri() . $dir_rel;

    $files = glob($dir_abs . '/*.css');
    if ($files === false) {
        return [];
    }

    natsort($files);

    $first_set = array_fill_keys($first_layers, true);
    $last_set = array_fill_keys($last_layers, true);

    $mid_files = [];
    foreach ($files as $abs_path) {

        $base_name = basename($abs_path);
        $handle = pathinfo($base_name, PATHINFO_FILENAME);

        if (isset($first_set[$handle]) || isset($last_set[$handle])) {
            continue;
        }

        $mid_files[] = [
            'handle' => $handle,
            'abs' => $abs_path,
            'uri' => $dir_uri . '/' . $base_name,
        ];
    }

    $enqueued_first = [];
    $enqueued_mid = [];
    $enqueued_last = [];

    // 1) First layers: order guaranteed (dependency chain)
    $prev = $deps;
    foreach ($first_layers as $handle) {

        $abs = $dir_abs . '/' . $handle . '.css';
        $uri = $dir_uri . '/' . $handle . '.css';

        if (!file_exists($abs)) {
            continue;
        }

        wp_enqueue_style(
            $handle,
            $uri,
            $prev,
            filemtime($abs)
        );

        $enqueued_first[] = $handle;
        $prev = [$handle];
    }

    $deps_for_mid = !empty($enqueued_first)
        ? [$enqueued_first[count($enqueued_first) - 1]]
        : $deps;

    // 2) Middle: depends on last first-layer (order not enforced among middles)
    foreach ($mid_files as $info) {

        wp_enqueue_style(
            $info['handle'],
            $info['uri'],
            $deps_for_mid,
            filemtime($info['abs'])
        );

        $enqueued_mid[] = $info['handle'];
    }

    $deps_for_last = $deps_for_mid;
    if (!empty($enqueued_mid)) {
        $deps_for_last = array_merge($deps_for_mid, $enqueued_mid);
    }

    // 3) Last layers: order guaranteed (dependency chain)
    $prev = $deps_for_last;
    foreach ($last_layers as $handle) {

        $abs = $dir_abs . '/' . $handle . '.css';
        $uri = $dir_uri . '/' . $handle . '.css';

        if (!file_exists($abs)) {
            continue;
        }

        wp_enqueue_style(
            $handle,
            $uri,
            $prev,
            filemtime($abs)
        );

        $enqueued_last[] = $handle;
        $prev = [$handle];
    }

    return array_merge($enqueued_first, $enqueued_mid, $enqueued_last);
}


add_action('wp_head', 'add_noindex_tag');
function add_noindex_tag() {
	if (!is_front_page()) {
		echo '<meta name="robots" content="noindex">';
	}
}


// recaptchaを非表示
add_action('wp_head', 'hidden_recaptcha');
function hidden_recaptcha() {
	if (!do_display_site_contents()) {
		echo '<style>
			.grecaptcha-badge {
				display: none !important;
			}
		</style>';
	}
}


add_action('wp_head', 'remove_existing_viewport_tag', 1);
function remove_existing_viewport_tag() {
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'wp_resource_hints', 2);
	remove_action('wp_head', '_wp_render_title_tag', 1);
}


add_action('wp_head', 'add_custom_viewport_tag'); 
function add_custom_viewport_tag() {
	
	global
		$arr_fullscreen_pages;
	
	if(is_page($arr_fullscreen_pages)){
		echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">';
	}
}


add_action('init', 'jws_maybe_start_session', 1);
function jws_maybe_start_session() {

    // CLI は除外
    if (PHP_SAPI === 'cli') {
        return;
    }

    // cron は除外
    if (function_exists('wp_doing_cron') && wp_doing_cron()) {
        return;
    }

    // REST API は原則除外（必要なら外す）
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return;
    }

    if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
        session_start();
    }
}


// ==================================================
// Redirect: requires login
// ==================================================
add_action('template_redirect', 'jws_redirect_require_login_for_special_pages', 1);
function jws_redirect_require_login_for_special_pages() {

    if (!is_page(['request-to-vip', 'apply'])) {
        return;
    }

    if (is_user_logged_in()) {
        return;
    }

    $request_uri = (string)($_SERVER['REQUEST_URI'] ?? '/');

	$url_home_current = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		'/'
	);

	$base_path = (string) parse_url(
		$url_home_current,
		PHP_URL_PATH
	); // 例: "/cht/"

    $base_path = '/' . trim($base_path, '/') . '/';
    if ($base_path === '//') {
        $base_path = '/';
    }

    if ($base_path !== '/' && strpos($request_uri, $base_path) === 0) {
        // 先頭の "/cht/" を取り除く（先頭 "/" は残す）
        $request_uri = substr($request_uri, strlen($base_path) - 1);
        if ($request_uri === '' || $request_uri[0] !== '/') {
            $request_uri = '/' . $request_uri;
        }
    }

    $redirect_to = $request_uri;

    global $path_login;
    $path = $path_login ?? '/account/login/';

	$login_base_url = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path, '/'))
	);

    $url_login = add_query_arg(
        'redirect_to',
        rawurlencode($redirect_to),
        $login_base_url
    );

    wp_safe_redirect($url_login);
    exit;
}