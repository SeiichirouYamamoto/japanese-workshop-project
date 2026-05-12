<?php

function build_html_landing_page(int $int_selected_language): string
{
	$html = '';

	// === グローバルパス（variables.php） ===
	global
		$path_login,
		$path_what_japanese_workshop,
		$path_create_account;
		// $path_students_can_do,
		// $path_teachers_can_do;

	$url_login = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_login, '/'))
	);

	// --- can_do（後で復元するため残しておく） ---
	// $url_students_can_do = get_home_url(
	//     get_data_blog_id_from_selected_language($int_selected_language ?? null),
	//     trailingslashit(ltrim($path_students_can_do, '/'))
	// );
	// $url_teachers_can_do = get_home_url(
	//     get_data_blog_id_from_selected_language($int_selected_language ?? null),
	//     trailingslashit(ltrim($path_teachers_can_do, '/'))
	// );

	$url_what_japanese_workshop = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_what_japanese_workshop, '/'))
	);

	$url_create_account = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_create_account, '/'))
	);

	// === 文言定義（0:JP / 1:ZH-TW / 2:EN） ===
	$texts = [
		'title_students' => [
			'学生アカウントをお持ちの方',
			'已持有學生帳號者',
			'For Students',
		],
		'title_teachers' => [
			'教師アカウントをお持ちの方',
			'已持有教師帳號者',
			'For Teachers',
		],
		'title_create_account' => [
			'新規アカウント作成',
			'建立新帳號',
			'Create an account',
		],
		'title_about' => [
			'Japanese Workshopとは?',
			'關於 Japanese Workshop',
			'What is Japanese Workshop?',
		],

		'desc_students' => [
			'学生の方はこちらからログインページを開きます。',
			'學生請從這裡開啟登入頁面。',
			'Open the login page for students.',
		],
		'desc_teachers' => [
			'教師の方はこちらからログインページを開きます。',
			'教師請從這裡開啟登入頁面。',
			'Open the login page for teachers.',
		],
		'desc_create_account' => [
			'はじめての方はこちらからアカウント作成ページを開きます。',
			'第一次使用者請從這裡開啟註冊頁面。',
			'Open the account creation page.',
		],
		'desc_about' => [
			'概要・理念・使い方など、Japanese Workshopのご案内ページを開きます。',
			'開啟 Japanese Workshop 的介紹頁（概要、理念、使用方式）。',
			'Open an overview page (concept, features, how to use).',
		],

		// ボタンラベル
		'login' => [
			'ログイン',
			'登入',
			'Login',
		],
		'show' => [
			'開く',
			'查看',
			'Show',
		],

		// --- can_do（後で復元するため残しておく） ---
		// 'students_can_do' => [
		//     '学生アカウントとは?',
		//     '學生帳號是什麼?',
		//     'What is a student account?',
		// ],
		// 'teachers_can_do' => [
		//     '教師アカウントとは?',
		//     '教師帳號是什麼?',
		//     'What is a teacher account?',
		// ],
	];

	// === LP Section ===
	$html .= '<section class="jwsSection">';
	$html .= '<div class="jwsContainer">';
	$html .= '<div class="jwsCardContainer">';

	// --- Card : Students ---
	$html .= '<div class="jwsCard">';
	$html .= '<h3 class="jwsCardTitle">' . esc_html($texts['title_students'][$int_selected_language]) . '</h3>';
	// $html .= '<p class="jwsCardDesc">' . esc_html($texts['desc_students'][$int_selected_language]) . '</p>';
	$html .= '<p><a href="' . esc_url($url_login) . '" class="jwsAction">' . esc_html($texts['login'][$int_selected_language]) . '</a></p>';

	// --- can_do（後で復元するため残しておく） ---
	// $html .= '<p class="jwsCardSubLink"><a href="' . esc_url($url_students_can_do) . '">' . esc_html($texts['students_can_do'][$int_selected_language]) . '</a></p>';

	$html .= '</div>';

	// --- Card : Teachers ---
	$html .= '<div class="jwsCard">';
	$html .= '<h3 class="jwsCardTitle">' . esc_html($texts['title_teachers'][$int_selected_language]) . '</h3>';
	// $html .= '<p class="jwsCardDesc">' . esc_html($texts['desc_teachers'][$int_selected_language]) . '</p>';
	$html .= '<p><a href="' . esc_url($url_login) . '" class="jwsAction">' . esc_html($texts['login'][$int_selected_language]) . '</a></p>';

	// --- can_do（後で復元するため残しておく） ---
	// $html .= '<p class="jwsCardSubLink"><a href="' . esc_url($url_teachers_can_do) . '">' . esc_html($texts['teachers_can_do'][$int_selected_language]) . '</a></p>';

	$html .= '</div>';

	// --- Card : Create Account ---
	$html .= '<div class="jwsCard">';
	$html .= '<h3 class="jwsCardTitle">' . esc_html($texts['title_create_account'][$int_selected_language]) . '</h3>';
	// $html .= '<p class="jwsCardDesc">' . esc_html($texts['desc_create_account'][$int_selected_language]) . '</p>';
	$html .= '<p><a href="' . esc_url($url_create_account) . '" class="jwsAction">' . esc_html($texts['show'][$int_selected_language]) . '</a></p>';
	$html .= '</div>';

	// --- Card : About ---
	$html .= '<div class="jwsCard">';
	$html .= '<h3 class="jwsCardTitle">' . esc_html($texts['title_about'][$int_selected_language]) . '</h3>';
	// $html .= '<p class="jwsCardDesc">' . esc_html($texts['desc_about'][$int_selected_language]) . '</p>';
	$html .= '<p><a href="' . esc_url($url_what_japanese_workshop) . '" class="jwsAction">' . esc_html($texts['show'][$int_selected_language]) . '</a></p>';
	$html .= '</div>';

	$html .= '</div>'; // jwsCardContainer
	$html .= '</div>'; // jwsContainer
	$html .= '</section>';

	return $html;
}
