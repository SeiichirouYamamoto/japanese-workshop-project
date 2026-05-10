<?php

/******************************************************
 *  PAGE
 *  
 ******************************************************/
function build_html_login_page($wp_error = null, $int_selected_language) {

    global 
		$path_dashboard,
		$path_forgot_password;

    $arr_login_messages = [
        'page_title' => [
            'ログイン',
            '登入',
            'Log In',
        ],
        'already_logged_in' => [
            'すでにログインしています。',
            '已登入。',
            'You are already logged in.',
        ],
        'go_mypage' => [
            'Dashboardへ',
            '前往 Dashboard',
            'Go to Dashboard',
        ],
        'login_failed' => [
            'ログインに失敗しました。',
            '登入失敗。',
            'Login failed.',
        ],
        'forgot_password_question' => [
            'パスワードをお忘れですか？',
            '忘記密碼了嗎？',
            'Forgot your password?',
        ],
        'forgot_password_link' => [
            'パスワード再設定ページへ',
            '前往重設密碼頁面',
            'Go to password reset page',
        ],
        'label_login' => [
            'メールアドレスまたはユーザー名',
            '電子郵件或使用者名稱',
            'Email or Username',
        ],
        'label_password' => [
            'パスワード',
            '密碼',
            'Password',
        ],
        'remember_me' => [
            'ログイン状態を維持する',
            '保持登入狀態',
            'Keep me logged in',
        ],
        'login_button' => [
            'ログイン',
            '登入',
            'Log In',
        ],
    ];

    $str_html = '';

    $str_html .= '<section class="accountSection loginSection">';
    $str_html .= '<div class="container">';
	
    if (is_user_logged_in()) {
		
		$url_dashboard = get_home_url(
			get_data_blog_id_from_selected_language($int_selected_language ?? null),
			trailingslashit(ltrim($path_dashboard, '/'))
		);
		
        $str_html .= '<div class="loginAlready">';
        $str_html .= '<p>' . esc_html($arr_login_messages['already_logged_in'][$int_selected_language]) . '</p>';
        $str_html .= '<p><a href="' . esc_url($url_dashboard) . '">' . esc_html($arr_login_messages['go_mypage'][$int_selected_language]) . '</a></p>';
        $str_html .= '</div>';
	
	} else {
			
		$url_forgot_password = get_home_url(
			get_data_blog_id_from_selected_language($int_selected_language ?? null),
			trailingslashit(ltrim($path_forgot_password, '/'))
		);

        $str_html .= '<div class="loginContent">';
        $str_html .= '<h2 class="loginTitle">' . esc_html($arr_login_messages['page_title'][$int_selected_language]) . '</h2>';

        if ($wp_error instanceof WP_Error) {

			// デバッグ 必要であれば内部ログだけ残す
            // $raw_error_message = $wp_error->get_error_message();
            // if (! empty($raw_error_message)) {
            //     error_log('jws_login_error: ' . $raw_error_message);
            // }

            $str_html .= '<div class="errorMessage">';
            $str_html .= '<p class="errorItem">' . esc_html($arr_login_messages['login_failed'][$int_selected_language]) . '</p>';
            $str_html .= '<p class="errorHelp">'
                . esc_html($arr_login_messages['forgot_password_question'][$int_selected_language])
                . ' <a href="' . esc_url($url_forgot_password) . '">'
                . esc_html($arr_login_messages['forgot_password_link'][$int_selected_language])
                . '</a></p>';
            $str_html .= '</div>';
        }

        $str_html .= '<form method="post" class="loginForm">';

		$redirect_to = '';
		if (!empty($_POST['redirect_to'])) {
			$redirect_to = esc_url_raw(wp_unslash($_POST['redirect_to']));
		}
		elseif (!empty($_GET['redirect_to'])) {
			$redirect_to = esc_url_raw(wp_unslash($_GET['redirect_to']));
		}

		$str_html .= '<input type="hidden" name="redirect_to" value="' . esc_attr($redirect_to) . '">';

        ob_start();
        wp_nonce_field('custom_login_action', 'custom_login_nonce');
        $str_html .= ob_get_clean();

        $str_html .= '<div class="formGroup">';
        $str_html .= '<label>' . esc_html($arr_login_messages['label_login'][$int_selected_language]) . '</label>';
        $str_html .= '<input
                        type="text"
                        name="user_login"
                        autocomplete="username"
						required
                    >';
        $str_html .= '</div>';

        $str_html .= '<div class="formGroup">';
        $str_html .= '<label>' . esc_html($arr_login_messages['label_password'][$int_selected_language]) . '</label>';
        $str_html .= '<input
                        type="password"
                        name="user_password"
                        autocomplete="current-password"
						required
                    >';
        $str_html .= '</div>';

        $str_html .= '<div class="formGroup checkBox">';
        $str_html .= '<label><input type="checkbox" name="remember_me" value="1"> '
            . esc_html($arr_login_messages['remember_me'][$int_selected_language])
            . '</label>';
        $str_html .= '</div>';

        $str_html .= '<div class="formGroup">';
        $str_html .= '<button type="submit" class="loginBtn">'
            . esc_html($arr_login_messages['login_button'][$int_selected_language])
            . '</button>';
        $str_html .= '</div>';

        $str_html .= '</form>';

		$str_html .= build_html_create_account_link(
			'loginCreateAccountLink',
			$int_selected_language
		);

		$str_html .= build_html_forgot_password_link(
			'loginForgotPasswordLink',
			$int_selected_language
		);

        $str_html .= '</div>';
    }

    $str_html .= '</div>';
    $str_html .= '</section>';

    return $str_html;
}


function build_html_register_account_page($int_selected_language) {

    global 
		$arr_tree_visible_pages,
		$jws_register_error,
		$jws_register_user_login,
		$jws_register_user_email,
		$jws_register_user_nickname;

    $arr_register_page_messages = [
        'page_title' => [
            'アカウント作成',
            '建立帳號',
            'Create Account',
        ],
        'already_logged_in' => [
            'すでにログイン済みです。',
            '已登入。',
            'You are already logged in.',
        ],
        'go_mypage' => [
            'Dashboardへ',
            '前往 Dashboard',
            'Go to Dashboard',
        ],
        'label_username' => [
            'ユーザー名',
            '使用者名稱',
            'Username',
        ],
        'label_email' => [
            'メールアドレス',
            '電子郵件',
            'Email Address',
        ],
        'label_nickname' => [
            'ニックネーム（画面に表示する名前）',
            '暱稱（顯示名稱）',
            'Nickname (display name)',
        ],
        'label_password' => [
            'パスワード',
            '密碼',
            'Password',
        ],
        'label_password_confirm' => [
            'パスワード（確認用）',
            '密碼（確認用）',
            'Password (confirmation)',
        ],
        'button_create' => [
            'アカウントを作成する',
            '建立帳號',
            'Create Account',
        ],
    ];

    $str_html = '';

    $str_html .= '<section class="accountSection registerSection">';
    $str_html .= '<div class="container">';

    if (is_user_logged_in()) {

        $url_dashboard = get_home_url(
			get_data_blog_id_from_selected_language($int_selected_language ?? null),
			trailingslashit(ltrim($path_dashboard, '/'))
		);

        $str_html .= '<div class="registerAlready">';
        $str_html .= '<p>' . $arr_register_page_messages['already_logged_in'][$int_selected_language] . '</p>';
        $str_html .= '<p><a href="' . esc_url($url_dashboard) . '">'
            . $arr_register_page_messages['go_mypage'][$int_selected_language] . '</a></p>';
        $str_html .= '</div>';

    } else {

        $str_html .= '<div class="registerContent">';
        $str_html .= '<h2 class="registerTitle">'
            . $arr_register_page_messages['page_title'][$int_selected_language] . '</h2>';

        if ($jws_register_error instanceof WP_Error) {

            $messages = $jws_register_error->get_error_messages();

            if (! empty($messages)) {
                $str_html .= '<div class="errorMessage">';
                foreach ($messages as $message) {
                    $str_html .= '<p class="errorItem">' . esc_html($message) . '</p>';
                }
                $str_html .= '</div>';
            }
        }

        $str_html .= '<form method="post" class="registerForm">';

        ob_start();
        wp_nonce_field('custom_register_action', 'custom_register_nonce');
        $str_html .= ob_get_clean();

        $str_html .= '<div class="formGroup">';
        $str_html .= '<label>'
            . $arr_register_page_messages['label_username'][$int_selected_language] . '</label>';
        $str_html .= '<input
						type="text"
						name="user_login"
						value="' . esc_attr($jws_register_user_login) . '"
						autocomplete="username"
						required
					>';

        $str_html .= '</div>';

        $str_html .= '<div class="formGroup">';
        $str_html .= '<label>'
            . $arr_register_page_messages['label_email'][$int_selected_language] . '</label>';
        $str_html .= '<input
						type="email"
						name="user_email"
						value="' . esc_attr($jws_register_user_email) . '"
						autocomplete="email"
						required
					>';

        $str_html .= '</div>';

        $str_html .= '<div class="formGroup">';
        $str_html .= '<label>'
            . $arr_register_page_messages['label_nickname'][$int_selected_language] . '</label>';
        $str_html .= '<input
						type="text"
						name="user_nickname"
						value="' . esc_attr($jws_register_user_nickname) . '"
						autocomplete="nickname"
						required
					>';

        $str_html .= '</div>';

        $str_html .= '<div class="formGroup">';
        $str_html .= '<label>'
            . $arr_register_page_messages['label_password'][$int_selected_language] . '</label>';
        $str_html .= '<input
							type="password"
							name="user_password"
							autocomplete="new-password"
							required
					>';
        $str_html .= '</div>';

        $str_html .= '<div class="formGroup">';
        $str_html .= '<label>'
            . $arr_register_page_messages['label_password_confirm'][$int_selected_language] . '</label>';
        $str_html .= '<input
						type="password"
						name="user_password_confirm"
						autocomplete="new-password"
						required
					>
					';
        $str_html .= '</div>';

        $str_html .= '<div class="formGroup">';
        $str_html .= '<button type="submit" class="registerBtn">'
            . $arr_register_page_messages['button_create'][$int_selected_language] . '</button>';
        $str_html .= '</div>';

        $str_html .= '</form>';
        $str_html .= '</div>';
    }

    $str_html .= '</div>';
    $str_html .= '</section>';

    return $str_html;
}


function build_html_logout_page($int_selected_language) {

    global
		$path_login,
		$arr_tree_visible_pages;

    $arr_logout_messages = [
        'page_title' => [
            'ログアウト',
            '登出',
            'Log out',
        ],
        'confirm_logout' => [
            'ログアウトしますか？',
            '要登出嗎？',
            'Do you want to log out?',
        ],
        'logout_button' => [
            'ログアウト',
            '登出',
            'Log out',
        ],
        'not_logged_in' => [
            'ログインしていません。',
            '尚未登入。',
            'You are not logged in.',
        ],
        'go_login' => [
            'ログインページへ',
            '前往登入頁面',
            'Go to login page',
        ],
    ];

    $str_html = '';

    $str_html .= '<section class="accountSection logoutSection">';
    $str_html .= '<div class="container">';

    if (is_user_logged_in()) {

        $str_html .= '<div class="logoutContent">';
        $str_html .= '<h2 class="logoutTitle">' . $arr_logout_messages['page_title'][$int_selected_language] . '</h2>';
        $str_html .= '<p>' . $arr_logout_messages['confirm_logout'][$int_selected_language] . '</p>';
        $str_html .= '<form method="post" class="logoutForm">';

        ob_start();
        wp_nonce_field('custom_logout_action', 'custom_logout_nonce');
        $str_html .= ob_get_clean();

        $str_html .= '<div class="formGroup">';
        $str_html .= '<button type="submit" class="logoutBtn">'
            . $arr_logout_messages['logout_button'][$int_selected_language]
            . '</button>';
        $str_html .= '</div>';

        $str_html .= '</form>';
        $str_html .= '</div>';

    } else {

        $url_login = get_home_url(
			get_data_blog_id_from_selected_language($int_selected_language ?? null),
			trailingslashit(ltrim($path_login, '/'))
		);

        $str_html .= '<div class="logoutContent">';
        $str_html .= '<h2 class="logoutTitle">' . $arr_logout_messages['page_title'][$int_selected_language] . '</h2>';
        $str_html .= '<p>' . $arr_logout_messages['not_logged_in'][$int_selected_language] . '</p>';
        $str_html .= '<p><a href="' . esc_url($url_login) . '">'
            . $arr_logout_messages['go_login'][$int_selected_language]
            . '</a></p>';
        $str_html .= '</div>';
    }

    $str_html .= '</div>';
    $str_html .= '</section>';

    return $str_html;
}


function build_html_logout_complete_page($int_selected_language) {

    $arr_logout_complete_messages = [
        'page_title' => [
            'ログアウト完了',
            '登出完成',
            'Logout complete',
        ],
        'message' => [
            'ログアウトしました。',
            '已登出。',
            'You have been logged out.',
        ],
        'go_home' => [
            'ホームへ戻る',
            '回到首頁',
            'Back to Home',
        ],
    ];

    $str_html = '';

    $url_home_current = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		'/'
	);

    $str_html .= '<section class="accountSection logoutCompleteSection">';
    $str_html .= '<div class="container">';

    $str_html .= '<div class="logoutCompleteContent">';
    $str_html .= '<h2 class="logoutCompleteTitle">'
        . esc_html($arr_logout_complete_messages['page_title'][$int_selected_language])
        . '</h2>';

    $str_html .= '<p class="logoutCompleteMessage">'
        . esc_html($arr_logout_complete_messages['message'][$int_selected_language])
        . '</p>';

    $str_html .= '<div class="formGroup">';
    $str_html .= '<a href="' . esc_url($url_home_current) . '" class="logoutCompleteBtn">'
        . esc_html($arr_logout_complete_messages['go_home'][$int_selected_language])
        . '</a>';
    $str_html .= '</div>';

    $str_html .= '</div>';

    $str_html .= '</div>';
    $str_html .= '</section>';

    return $str_html;
}


function build_html_profile_page($int_selected_language) {

    global 
		$path_login,
		$path_change_email,
		$jws_profile_error,
		$jws_profile_notice,
		$jws_profile_nickname;

    $arr_profile_messages = [
        'page_title' => [
            'プロフィール編集',
            '編輯個人資料',
            'Edit Profile',
        ],
        'need_login' => [
            'このページを利用するにはログインが必要です。',
            '此頁面需要登入後才能使用。',
            'You need to log in to use this page.',
        ],
        'go_login' => [
            'ログインページへ',
            '前往登入頁面',
            'Go to login page',
        ],
        'notice_updated' => [
            'プロフィールを更新しました。',
            '已更新個人資料。',
            'Your profile has been updated.',
        ],
        'label_username' => [
            'ユーザー名',
            '使用者名稱',
            'Username',
        ],
        'label_email' => [
            'メールアドレス',
            '電子郵件',
            'Email Address',
        ],
        'label_nickname' => [
            'ニックネーム（画面に表示する名前）',
            '暱稱（顯示名稱）',
            'Nickname (display name)',
        ],
        'button_update' => [
            'プロフィールを更新する',
            '更新個人資料',
            'Update profile',
        ],
        'button_change_email' => [
            'メールアドレスを変更する',
            '變更電子郵件',
            'Change email address',
        ],
    ];

    $str_html = '';

    $str_html .= '<section class="accountSection profileSection">';
    $str_html .= '<div class="container">';

    if (! is_user_logged_in()) {

		$url_login = get_home_url(
			get_data_blog_id_from_selected_language($int_selected_language ?? null),
			trailingslashit(ltrim($path_login, '/'))
		);

        $str_html .= '<div class="profileContent">';
        $str_html .= '<p>' . $arr_profile_messages['need_login'][$int_selected_language] . '</p>';
		$str_html .= '<p><a href="' . esc_url($url_login) . '">' .$arr_profile_messages['go_login'][$int_selected_language] . '</a></p>';
        $str_html .= '</div>';

    } else {

        $current_user = wp_get_current_user();

        $default_name = $current_user->display_name !== ''
            ? $current_user->display_name
            : ($current_user->nickname !== '' ? $current_user->nickname : $current_user->user_login);

        $input_name = $jws_profile_nickname !== '' ? $jws_profile_nickname : $default_name;

        $str_html .= '<div class="profileContent">';

        $str_html .= '<h2 class="profileTitle">' . $arr_profile_messages['page_title'][$int_selected_language] . '</h2>';

        if (! empty($jws_profile_notice)) {
            $str_html .= '<div class="profileNotice">';
            $str_html .= '<p>' . $arr_profile_messages['notice_updated'][$int_selected_language] . '</p>';
            $str_html .= '</div>';
        }

        if ($jws_profile_error instanceof WP_Error) {

            $messages = $jws_profile_error->get_error_messages();

            if (! empty($messages)) {
                $str_html .= '<div class="errorMessage">';
                foreach ($messages as $message) {
                    $str_html .= '<p class="errorItem">' . esc_html($message) . '</p>';
                }
                $str_html .= '</div>';
            }
        }

		$url_change_email = get_home_url(
			get_data_blog_id_from_selected_language($int_selected_language ?? null),
			trailingslashit(ltrim($path_change_email, '/'))
		);

        $str_html .= '<div class="profileInfo">';
		$str_html .= '<p>' . $arr_profile_messages['label_username'][$int_selected_language] . ': ' . esc_html($current_user->user_login) . '</p>';

		$str_html .= '<p class="profileEmailRow">';
		$str_html .=     $arr_profile_messages['label_email'][$int_selected_language] . ': '
			. '<span class="profileEmailAddress">' . esc_html($current_user->user_email) . '</span>'
			. ' <a href="' . esc_url($url_change_email) . '" class="changeEmailLink">'
			. $arr_profile_messages['button_change_email'][$int_selected_language]
			. '</a>';
		$str_html .= '</p>';

		$str_html .= '</div>';


        $str_html .= '<form method="post" class="profileForm">';

        ob_start();
        wp_nonce_field('custom_profile_action', 'custom_profile_nonce');
        $str_html .= ob_get_clean();

        $str_html .= '<div class="formGroup">';
        $str_html .= '<label>' . $arr_profile_messages['label_nickname'][$int_selected_language] . '</label>';
        $str_html .= '<input
						type="text"
						name="user_nickname"
						value="' . esc_attr($input_name) . '"
						autocomplete="nickname"
						required
					>';

        $str_html .= '</div>';

        $str_html .= '<div class="formGroup">';
        $str_html .= '<button type="submit" class="profileBtn">' . $arr_profile_messages['button_update'][$int_selected_language] . '</button>';
        $str_html .= '</div>';

        $str_html .= '</form>';
        $str_html .= '</div>';
    }

    $str_html .= '</div>';
    $str_html .= '</section>';

    return $str_html;
}


function build_html_change_email_page($int_selected_language) {

    global 
		$path_login,
		$jws_change_email_error,
		$jws_change_email_notice,
		$jws_change_email_new_email;

    $arr_change_email_messages = [
        'page_title' => [
            'メールアドレス変更',
            '變更電子郵件',
            'Change Email Address',
        ],
        'need_login' => [
            'このページを利用するにはログインが必要です。',
            '此頁面需要登入後才能使用。',
            'You need to log in to use this page.',
        ],
        'go_login' => [
            'ログインページへ',
            '前往登入頁面',
            'Go to login page',
        ],
        'notice_updated' => [
            'メールアドレスを更新しました。',
            '已更新電子郵件。',
            'Your email address has been updated.',
        ],
        'label_current_email' => [
            '現在のメールアドレス',
            '目前的電子郵件',
            'Current email address',
        ],
        'label_new_email' => [
            '新しいメールアドレス',
            '新的電子郵件',
            'New email address',
        ],
        'label_current_password' => [
            '現在のパスワード',
            '目前的密碼',
            'Current password',
        ],
        'button_update' => [
            'メールアドレスを更新する',
            '更新電子郵件',
            'Update email address',
        ],
    ];

    $str_html = '';

    $str_html .= '<section class="accountSection changeEmailSection">';
    $str_html .= '<div class="container">';

    if (! is_user_logged_in()) {

        $url_login = get_home_url(
			get_data_blog_id_from_selected_language($int_selected_language ?? null),
			trailingslashit(ltrim($path_login, '/'))
		);

        $str_html .= '<div class="changeEmailContent">';
        $str_html .= '<p>' . $arr_change_email_messages['need_login'][$int_selected_language] . '</p>';
        $str_html .= '<p><a href="' . esc_url($url_login) . '">' . $arr_change_email_messages['go_login'][$int_selected_language] . '</a></p>';
        $str_html .= '</div>';

    } else {

        $current_user = wp_get_current_user();

        $input_new_email = $jws_change_email_new_email !== '' ? $jws_change_email_new_email : $current_user->user_email;

        $display_email = (! empty($jws_change_email_notice) && is_email($jws_change_email_new_email))
            ? $jws_change_email_new_email
            : $current_user->user_email;

        $str_html .= '<div class="changeEmailContent">';

        $str_html .= '<h2 class="changeEmailTitle">' . $arr_change_email_messages['page_title'][$int_selected_language] . '</h2>';

        if (! empty($jws_change_email_notice)) {
            $str_html .= '<div class="changeEmailNotice">';
            $str_html .= '<p>' . $arr_change_email_messages['notice_updated'][$int_selected_language] . '</p>';
            $str_html .= '</div>';
        }

        if ($jws_change_email_error instanceof WP_Error) {

            $messages = $jws_change_email_error->get_error_messages();

            if (! empty($messages)) {
                $str_html .= '<div class="errorMessage">';
                foreach ($messages as $message) {
                    $str_html .= '<p class="errorItem">' . esc_html($message) . '</p>';
                }
                $str_html .= '</div>';
            }
        }

        $str_html .= '<div class="changeEmailInfo">';
        $str_html .= '<p>' . $arr_change_email_messages['label_current_email'][$int_selected_language] . ': ' . esc_html($display_email) . '</p>';
        $str_html .= '</div>';

        $str_html .= '<form method="post" class="changeEmailForm">';

        ob_start();
        wp_nonce_field('custom_change_email_action', 'custom_change_email_nonce');
        $str_html .= ob_get_clean();

        $str_html .= '<div class="formGroup">';
        $str_html .= '<label>' . $arr_change_email_messages['label_new_email'][$int_selected_language] . '</label>';
        $str_html .= '<input
						type="email"
						name="user_new_email"
						value="' . esc_attr($input_new_email) . '"
						autocomplete="email"
						required
					>';

        $str_html .= '</div>';

        $str_html .= '<div class="formGroup">';
        $str_html .= '<label>' . $arr_change_email_messages['label_current_password'][$int_selected_language] . '</label>';
        $str_html .= '<input
						type="password"
						name="user_current_password"
						autocomplete="current-password"
						required
					>';
        $str_html .= '</div>';

        $str_html .= '<div class="formGroup">';
        $str_html .= '<button type="submit" class="changeEmailBtn">' . $arr_change_email_messages['button_update'][$int_selected_language] . '</button>';
        $str_html .= '</div>';

        $str_html .= '</form>';
        $str_html .= '</div>';
    }

    $str_html .= '</div>';
    $str_html .= '</section>';

    return $str_html;
}


function build_html_delete_account_page($int_selected_language) {

    global
		$path_login,
		$path_cancel_subscription,
		$jws_delete_account_error,
		$jws_delete_account_checked;

    $arr_delete_messages = [
        'page_title' => [
            'アカウント削除',
            '刪除帳號',
            'Delete Account',
        ],
        'need_login' => [
            'このページを利用するにはログインが必要です。',
            '此頁面需要登入後才能使用。',
            'You need to log in to use this page.',
        ],
        'go_login' => [
            'ログインページへ',
            '前往登入頁面',
            'Go to login page',
        ],
        'auto_renew_active' => [
            '現在、有料会員の自動更新が有効になっています。アカウントを削除する前に、自動更新を停止してください。',
            '目前仍為付費會員自動續訂狀態。請先停止自動續訂後，再申請刪除帳號。',
            'Your paid membership auto-renewal is currently active. Please stop auto-renewal before deleting your account.',
        ],
        'go_cancel_auto_renew' => [
            '自動更新停止ページへ',
            '前往停止自動續訂頁面',
            'Go to auto-renewal cancellation page',
        ],
        'confirm_description' => [
            'アカウントを削除する前に、以下の内容をご確認ください。',
            '在刪除帳號之前，請先確認以下內容。',
            'Before deleting your account, please confirm the following:',
        ],
        'checkbox_1' => [
            '有料会員の自動更新を停止済みであることを確認しました。',
            '我已確認已停止付費會員自動續訂。',
            'I confirm that auto-renewal for my paid membership has been stopped.',
        ],
        'checkbox_2' => [
            '退会後は有料コンテンツを利用できなくなることを理解しました。',
            '我了解退會後將無法再使用任何付費內容。',
            'I understand that I will no longer have access to paid content after deletion.',
        ],
        'checkbox_3' => [
            '退会後の個人情報の取り扱い（一定期間経過後に削除など）に同意します。',
            '我同意退會後的個人資料處理方式（例如經過一定期間後刪除等）。',
            'I agree to the handling of my personal data after deletion (e.g., deletion after a certain period).',
        ],
        'checkbox_4' => [
            'アカウントの削除（退会）を希望します。',
            '我希望刪除帳號（退會）。',
            'I wish to delete my account (cancel membership).',
        ],
        'button_delete' => [
            'アカウントを削除する',
            '刪除帳號',
            'Delete account',
        ],
    ];

    $str_html = '';

    $str_html .= '<section class="accountSection deleteAccountSection">';
    $str_html .= '<div class="container">';

    if (! is_user_logged_in()) {

        $url_login = get_home_url(
			get_data_blog_id_from_selected_language($int_selected_language ?? null),
			trailingslashit(ltrim($path_login, '/'))
		);

        $str_html .= '<div class="deleteAccountContent">';
        $str_html .= '<p>' . $arr_delete_messages['need_login'][$int_selected_language] . '</p>';
        $str_html .= '<p><a href="' . esc_url($url_login) . '">' . $arr_delete_messages['go_login'][$int_selected_language] . '</a></p>';
        $str_html .= '</div>';

    } else {

        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;

        $str_html .= '<div class="deleteAccountContent">';
        $str_html .= '<h2 class="deleteAccountTitle">' . $arr_delete_messages['page_title'][$int_selected_language] . '</h2>';

        if ($jws_delete_account_error instanceof WP_Error) {
            $messages = $jws_delete_account_error->get_error_messages();
            if (! empty($messages)) {
                $str_html .= '<div class="errorMessage">';
                foreach ($messages as $message) {
                    $str_html .= '<p class="errorItem">' . esc_html($message) . '</p>';
                }
                $str_html .= '</div>';
            }
        }

        if (jws_is_user_auto_renew_active($user_id)) {

			$url_cancel_auto_renew = get_home_url(
				get_data_blog_id_from_selected_language($int_selected_language ?? null),
				trailingslashit(ltrim($path_cancel_subscription, '/'))
			);

            $str_html .= '<div class="autoRenewActiveInfo">';
            $str_html .= '<p>' . $arr_delete_messages['auto_renew_active'][$int_selected_language] . '</p>';
            $str_html .= '<p><a href="' . esc_url($url_cancel_auto_renew) . '" class="cancelAutoRenewBtn">' . $arr_delete_messages['go_cancel_auto_renew'][$int_selected_language] . '</a></p>';
            $str_html .= '</div>';

        } else {

            $checked_1 = ! empty($jws_delete_account_checked['confirm_stop_renewal']);
            $checked_2 = ! empty($jws_delete_account_checked['confirm_lose_access']);
            $checked_3 = ! empty($jws_delete_account_checked['confirm_data_handling']);
            $checked_4 = ! empty($jws_delete_account_checked['confirm_delete']);

            $str_html .= '<p>' . $arr_delete_messages['confirm_description'][$int_selected_language] . '</p>';

            $str_html .= '<form method="post" class="deleteAccountForm">';

            ob_start();
            wp_nonce_field('custom_delete_account_action', 'custom_delete_account_nonce');
            $str_html .= ob_get_clean();

            $str_html .= '<div class="formGroup checkBox">';
            $str_html .= '<label>';
            $str_html .= '<input type="checkbox" name="confirm_stop_renewal" value="1"' . ($checked_1 ? ' checked' : '') . '>';
            $str_html .= $arr_delete_messages['checkbox_1'][$int_selected_language];
            $str_html .= '</label>';
            $str_html .= '</div>';

            $str_html .= '<div class="formGroup checkBox">';
            $str_html .= '<label>';
            $str_html .= '<input type="checkbox" name="confirm_lose_access" value="1"' . ($checked_2 ? ' checked' : '') . '>';
            $str_html .= $arr_delete_messages['checkbox_2'][$int_selected_language];
            $str_html .= '</label>';
            $str_html .= '</div>';

            $str_html .= '<div class="formGroup checkBox">';
            $str_html .= '<label>';
            $str_html .= '<input type="checkbox" name="confirm_data_handling" value="1"' . ($checked_3 ? ' checked' : '') . '>';
            $str_html .= $arr_delete_messages['checkbox_3'][$int_selected_language];
            $str_html .= '</label>';
            $str_html .= '</div>';

            $str_html .= '<div class="formGroup checkBox">';
            $str_html .= '<label>';
            $str_html .= '<input type="checkbox" name="confirm_delete" value="1"' . ($checked_4 ? ' checked' : '') . '>';
            $str_html .= $arr_delete_messages['checkbox_4'][$int_selected_language];
            $str_html .= '</label>';
            $str_html .= '</div>';

            $str_html .= '<div class="formGroup">';
            $str_html .= '<button type="submit" class="deleteAccountBtn">' . $arr_delete_messages['button_delete'][$int_selected_language] . '</button>';
            $str_html .= '</div>';

            $str_html .= '</form>';
        }

        $str_html .= '</div>';
    }

    $str_html .= '</div>';
    $str_html .= '</section>';

    return $str_html;
}


function build_html_delete_account_complete_page($int_selected_language) {

    $arr_complete_messages = [
        'page_title' => [
			'アカウント削除完了',
			'帳號刪除完成',
			'Account Deleted',
		],
        'message_main' => [
            'ご利用ありがとうございました。アカウントの削除が完了しました。',
            '感謝您的使用，帳號刪除程序已完成。',
            'Thank you for using our service. Your account has been deleted.',
        ],
        'message_detail' => [
            'トップページより、引き続き無料コンテンツをご利用いただけます。',
            '您仍然可以從首頁繼續瀏覽免費內容。',
            'You can still access free content from the top page.',
        ],
        'button_home' => [
            'ホームへ戻る',
            '返回首頁',
            'Back to home',
        ],
    ];

    $url_home_current = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		'/'
	);

    $str_html = '';

    $str_html .= '<section class="accountSection deleteAccountCompleteSection">';
    $str_html .= '<div class="container">';
    $str_html .= '<div class="deleteAccountCompleteContent">';

    $str_html .= '<h2 class="deleteAccountCompleteTitle">' . $arr_complete_messages['page_title'][$int_selected_language] . '</h2>';
    $str_html .= '<p class="deleteAccountCompleteMessageMain">' . $arr_complete_messages['message_main'][$int_selected_language] . '</p>';
    $str_html .= '<p class="deleteAccountCompleteMessageDetail">' . $arr_complete_messages['message_detail'][$int_selected_language] . '</p>';

    $str_html .= '<p class="deleteAccountCompleteHomeLink">';
    $str_html .= '<a href="' . esc_url($url_home_current) . '" class="homeBtn">' . $arr_complete_messages['button_home'][$int_selected_language] . '</a>';
    $str_html .= '</p>';

    $str_html .= '</div>';
    $str_html .= '</div>';
    $str_html .= '</section>';

    return $str_html;
}


function build_html_change_password_page($int_selected_language) {

    global
		$path_login,
		$jws_change_password_error,
		$jws_change_password_notice;

    $arr_change_password_messages = [
        'page_title' => [
            'パスワード変更',
            '變更密碼',
            'Change Password',
        ],
        'need_login' => [
            'このページを利用するにはログインが必要です。',
            '此頁面需要登入後才能使用。',
            'You need to log in to use this page.',
        ],
        'go_login' => [
            'ログインページへ',
            '前往登入頁面',
            'Go to login page',
        ],
        'label_current_password' => [
            '現在のパスワード',
            '目前的密碼',
            'Current password',
        ],
        'label_new_password' => [
            '新しいパスワード',
            '新的密碼',
            'New password',
        ],
        'label_new_password_confirm' => [
            '新しいパスワード（確認用）',
            '新的密碼（確認用）',
            'New password (confirmation)',
        ],
        'button_update' => [
            'パスワードを変更する',
            '變更密碼',
            'Change password',
        ],
        'notice_updated' => [
            'パスワードを変更しました。',
            '已變更密碼。',
            'Your password has been updated.',
        ],
    ];

    $str_html = '';

    $str_html .= '<section class="accountSection changePasswordSection">';
    $str_html .= '<div class="container">';

    if (! is_user_logged_in()) {

        $url_login = get_home_url(
			get_data_blog_id_from_selected_language($int_selected_language ?? null),
			trailingslashit(ltrim($path_login, '/'))
		);

        $str_html .= '<div class="changePasswordContent">';
        $str_html .= '<p>' . $arr_change_password_messages['need_login'][$int_selected_language] . '</p>';
        $str_html .= '<p><a href="' . esc_url($url_login) . '">'
            . $arr_change_password_messages['go_login'][$int_selected_language] . '</a></p>';
        $str_html .= '</div>';

    } else {

        $str_html .= '<div class="changePasswordContent">';
        $str_html .= '<h2 class="changePasswordTitle">'
            . $arr_change_password_messages['page_title'][$int_selected_language] . '</h2>';

        if (! empty($jws_change_password_notice)) {
            $str_html .= '<div class="changePasswordNotice">';
            $str_html .= '<p>' . $arr_change_password_messages['notice_updated'][$int_selected_language] . '</p>';
            $str_html .= '</div>';
        }

        if ($jws_change_password_error instanceof WP_Error) {
            $messages = $jws_change_password_error->get_error_messages();
            if (! empty($messages)) {
                $str_html .= '<div class="errorMessage">';
                foreach ($messages as $message) {
                    $str_html .= '<p class="errorItem">' . esc_html($message) . '</p>';
                }
                $str_html .= '</div>';
            }
        }

        $str_html .= '<form method="post" class="changePasswordForm">';

        ob_start();
        wp_nonce_field('custom_change_password_action', 'custom_change_password_nonce');
        $str_html .= ob_get_clean();

        $str_html .= '<div class="formGroup">';
        $str_html .= '<label>' . $arr_change_password_messages['label_current_password'][$int_selected_language] . '</label>';
        $str_html .= '<input
						type="password"
						name="user_current_password"
						autocomplete="current-password"
						required
					>';
        $str_html .= '</div>';

        $str_html .= '<div class="formGroup">';
        $str_html .= '<label>' . $arr_change_password_messages['label_new_password'][$int_selected_language] . '</label>';
        $str_html .= '<input
						type="password"
						name="user_new_password"
						autocomplete="new-password"
						required
					>';
        $str_html .= '</div>';

        $str_html .= '<div class="formGroup">';
        $str_html .= '<label>' . $arr_change_password_messages['label_new_password_confirm'][$int_selected_language] . '</label>';
        $str_html .= '<input
						type="password"
						name="user_new_password_confirm"
						autocomplete="new-password"
						required
					>';
        $str_html .= '</div>';

        $str_html .= '<div class="formGroup">';
        $str_html .= '<button type="submit" class="changePasswordBtn">'
            . $arr_change_password_messages['button_update'][$int_selected_language] . '</button>';
        $str_html .= '</div>';

        $str_html .= '</form>';
        $str_html .= '</div>';
    }

    $str_html .= '</div>';
    $str_html .= '</section>';

    return $str_html;
}


function build_html_lost_password_page($int_selected_language) {

    global
		$jws_lost_password_error,
		$jws_lost_password_notice,
		$jws_lost_password_login;

    $arr_lost_password_messages = [
        'page_title' => [
            'パスワードをお忘れですか？',
            '忘記密碼？',
            'Forgot your password?',
        ],
        'description' => [
            '登録したユーザー名またはメールアドレスを入力してください。パスワード再設定用のリンクをメールでお送りします。',
            '請輸入已註冊的使用者名稱或電子郵件，我們會寄送密碼重設連結給您。',
            'Please enter your username or registered email address. We will send you a password reset link.',
        ],
        'label_login_or_email' => [
            'ユーザー名またはメールアドレス',
            '使用者名稱或電子郵件',
            'Username or email address',
        ],
        'button_send' => [
            '再設定用リンクを送信する',
            '寄送重設連結',
            'Send reset link',
        ],
        'notice_email_sent' => [
            'パスワード再設定用のリンクをメールで送信しました（登録がある場合）。',
            '已寄出密碼重設連結（若該帳號已註冊）。',
            'If an account exists for the provided information, a reset link has been sent.',
        ],
    ];

    $str_html = '';

    $str_html .= '<section class="accountSection lostPasswordSection">';
    $str_html .= '<div class="container">';
    $str_html .= '<div class="lostPasswordContent">';

    $str_html .= '<h2 class="lostPasswordTitle">' . $arr_lost_password_messages['page_title'][$int_selected_language] . '</h2>';
    $str_html .= '<p class="lostPasswordDescription">' . $arr_lost_password_messages['description'][$int_selected_language] . '</p>';

    if (! empty($jws_lost_password_notice)) {
        $str_html .= '<div class="lostPasswordNotice">';
        $str_html .= '<p>' . $arr_lost_password_messages['notice_email_sent'][$int_selected_language] . '</p>';
        $str_html .= '</div>';
    }

    if ($jws_lost_password_error instanceof WP_Error) {
        $messages = $jws_lost_password_error->get_error_messages();
        if (! empty($messages)) {
            $str_html .= '<div class="errorMessage">';
            foreach ($messages as $message) {
                $str_html .= '<p class="errorItem">' . esc_html($message) . '</p>';
            }
            $str_html .= '</div>';
        }
    }

    $str_html .= '<form method="post" class="lostPasswordForm">';

    ob_start();
    wp_nonce_field('custom_lost_password_action', 'custom_lost_password_nonce');
    $str_html .= ob_get_clean();

    $str_html .= '<div class="formGroup">';
    $str_html .= '<label>' . $arr_lost_password_messages['label_login_or_email'][$int_selected_language] . '</label>';
    $str_html .= '<input
					type="text"
					name="user_login_or_email"
					value="' . esc_attr($jws_lost_password_login) . '"
					autocomplete="username"
					required
				>';

    $str_html .= '</div>';

    $str_html .= '<div class="formGroup">';
    $str_html .= '<button type="submit" class="lostPasswordBtn">'
        . $arr_lost_password_messages['button_send'][$int_selected_language] . '</button>';
    $str_html .= '</div>';

    $str_html .= '</form>';

    $str_html .= '</div>';
    $str_html .= '</div>';
    $str_html .= '</section>';

    return $str_html;
}


function build_html_reset_password_page($int_selected_language) {

    global
        $path_login,
        $jws_reset_password_error,
        $jws_reset_password_notice;

    $arr_reset_password_messages = [
        'page_title' => [
            'パスワード再設定',
            '重設密碼',
            'Reset Password',
        ],
        'description' => [
            '新しいパスワードを入力してください。',
            '請輸入新的密碼。',
            'Please enter your new password.',
        ],
        'invalid_link' => [
            'パスワード再設定用のリンクが無効か、すでに使用されています。もう一度最初からやり直してください。',
            '密碼重設連結無效或已被使用，請重新申請。',
            'The password reset link is invalid or has already been used. Please request a new one.',
        ],
        'label_new_password' => [
            '新しいパスワード',
            '新的密碼',
            'New password',
        ],
        'label_new_password_confirm' => [
            '新しいパスワード（確認用）',
            '新的密碼（確認用）',
            'New password (confirmation)',
        ],
        'button_reset' => [
            'パスワードを再設定する',
            '重設密碼',
            'Reset password',
        ],
        'notice_password_updated' => [
            'パスワードを再設定しました。ログインページから新しいパスワードでログインしてください。',
            '已重設密碼，請使用新密碼重新登入。',
            'Your password has been reset. Please log in with your new password.',
        ],
        'go_login' => [
            'ログインページへ',
            '前往登入頁面',
            'Go to login page',
        ],
    ];

    $is_success = isset($_GET['success']) && sanitize_text_field($_GET['success']) === '1';

    $reset_key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';
    $reset_login = isset($_GET['login']) ? sanitize_text_field($_GET['login']) : '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['reset_key'])) {
            $reset_key = sanitize_text_field($_POST['reset_key']);
        }
        if (isset($_POST['reset_login'])) {
            $reset_login = sanitize_text_field($_POST['reset_login']);
        }
    }

    $str_html = '';

    $str_html .= '<section class="accountSection resetPasswordSection">';
    $str_html .= '<div class="container">';
    $str_html .= '<div class="resetPasswordContent">';

    $str_html .= '<h2 class="resetPasswordTitle">' . $arr_reset_password_messages['page_title'][$int_selected_language] . '</h2>';

    if ($is_success) {

        $url_login = get_home_url(
            get_data_blog_id_from_selected_language($int_selected_language ?? null),
            trailingslashit(ltrim($path_login, '/'))
        );

        $str_html .= '<div class="resetPasswordNotice">';
        $str_html .= '<p>' . $arr_reset_password_messages['notice_password_updated'][$int_selected_language] . '</p>';
        $str_html .= '<p><a href="' . esc_url($url_login) . '">'
            . $arr_reset_password_messages['go_login'][$int_selected_language] . '</a></p>';
        $str_html .= '</div>';

    } else {

        $is_invalid = false;

        if ($reset_key === '' || $reset_login === '') {
            $is_invalid = true;
        } else {
            $user = check_password_reset_key($reset_key, $reset_login);
            if (is_wp_error($user) || ! $user) {
                $is_invalid = true;
            }
        }

        if ($is_invalid) {

            $str_html .= '<div class="resetPasswordInvalid">';
            $str_html .= '<p>' . $arr_reset_password_messages['invalid_link'][$int_selected_language] . '</p>';
            $str_html .= '</div>';

        } else {

            if ($jws_reset_password_error instanceof WP_Error) {
                $messages = $jws_reset_password_error->get_error_messages();
                if (! empty($messages)) {
                    $str_html .= '<div class="errorMessage">';
                    foreach ($messages as $message) {
                        $str_html .= '<p class="errorItem">' . esc_html($message) . '</p>';
                    }
                    $str_html .= '</div>';
                }
            } else {
                $str_html .= '<p class="resetPasswordDescription">'
                    . $arr_reset_password_messages['description'][$int_selected_language] . '</p>';
            }

            $str_html .= '<form method="post" class="resetPasswordForm">';

            ob_start();
            wp_nonce_field('custom_reset_password_action', 'custom_reset_password_nonce');
            $str_html .= ob_get_clean();

            $str_html .= '<input type="hidden" name="reset_key" value="' . esc_attr($reset_key) . '">';
            $str_html .= '<input type="hidden" name="reset_login" value="' . esc_attr($reset_login) . '">';

            $str_html .= '<div class="formGroup">';
            $str_html .= '<label>' . $arr_reset_password_messages['label_new_password'][$int_selected_language] . '</label>';
            $str_html .= '<input
                            type="password"
                            name="user_new_password"
                            autocomplete="new-password"
                            required
                        >';
            $str_html .= '</div>';

            $str_html .= '<div class="formGroup">';
            $str_html .= '<label>' . $arr_reset_password_messages['label_new_password_confirm'][$int_selected_language] . '</label>';
            $str_html .= '<input
                            type="password"
                            name="user_new_password_confirm"
                            autocomplete="new-password"
                            required
                        >';
            $str_html .= '</div>';

            $str_html .= '<div class="formGroup">';
            $str_html .= '<button type="submit" class="resetPasswordBtn">'
                . $arr_reset_password_messages['button_reset'][$int_selected_language] . '</button>';
            $str_html .= '</div>';

            $str_html .= '</form>';
        }
    }

    $str_html .= '</div>';
    $str_html .= '</div>';
    $str_html .= '</section>';

    return $str_html;
}


/******************************************************
 *  add actions
 *  
 ******************************************************/
add_action('template_redirect', 'jws_redirect_grammar_point_access', 1);
function jws_redirect_grammar_point_access()
{

	$int_selected_language = jws_get_language_index();

    $url_home_current = get_home_url(
        get_data_blog_id_from_selected_language($int_selected_language ?? null),
        '/'
    );

    if (is_page('dashboard') && !is_user_logged_in()) {
        wp_safe_redirect($url_home_current);
        exit;
    }

    if (empty($_GET['grammar_unique_code'])) {
        return;
    }

    $code = sanitize_text_field(wp_unslash($_GET['grammar_unique_code']));

    if (is_page('grammar-point-guest') && is_user_logged_in()) {

        global $path_grammar_point;
        $path_grammar_point = $path_grammar_point ?? '/dashboard/study/grammar-point/';

        $url_grammar_point = get_home_url(
            get_data_blog_id_from_selected_language($int_selected_language ?? null),
            trailingslashit(ltrim($path_grammar_point, '/'))
        );

        $url_redirect = add_query_arg(
            'grammar_unique_code',
            rawurlencode($code),
            $url_grammar_point
        );

        wp_safe_redirect($url_redirect);
        exit;
    }

    if (is_page('grammar-point') && !is_user_logged_in()) {

        global $path_grammar_point_guest;
        $path_grammar_point_guest = $path_grammar_point_guest ?? '/grammar-point-guest/';

        $url_grammar_point_guest = get_home_url(
            get_data_blog_id_from_selected_language($int_selected_language ?? null),
            trailingslashit(ltrim($path_grammar_point_guest, '/'))
        );

        $url_redirect = add_query_arg(
            'grammar_unique_code',
            rawurlencode($code),
            $url_grammar_point_guest
        );

        wp_safe_redirect($url_redirect);
        exit;
    }
}


add_action( 'wp_login_failed', function( $username, $error ){
    $ref = wp_get_referer();
    if ( $ref ) {
        $path = wp_parse_url( $ref, PHP_URL_PATH );
        if ( $path && ! preg_match( '#/login/?$#', untrailingslashit( $path ) ) ) {
            wp_safe_redirect( add_query_arg( 'login', 'failed', $ref ) );
            exit;
        }
    }
}, 10, 2 );


add_action('wp_logout', 'custom_redirect_after_logout');
function custom_redirect_after_logout() {

	$int_selected_language = jws_get_language_index();

	 global
		$path_logout_complete,
		$jws_skip_logout_redirect;

    if (!empty($jws_skip_logout_redirect)) {
        unset($jws_skip_logout_redirect);
        return;
    }

	$url_logout_complete = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_logout_complete, '/'))
	);
    wp_safe_redirect($url_logout_complete);
	exit();
}

$jws_login_error = null;

add_action('init', 'jws_handle_login_request');
function jws_handle_login_request() {

    if (is_admin()) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (
        !isset($_POST['custom_login_nonce']) ||
        !wp_verify_nonce($_POST['custom_login_nonce'], 'custom_login_action')
    ) {
        return;
    }

	$int_selected_language = jws_get_language_index();
	
    global $jws_login_error;

    $creds = [];
    $creds['user_login'] = $_POST['user_login'] ?? '';
    $creds['user_password'] = $_POST['user_password'] ?? '';
    $creds['remember'] = !empty($_POST['remember_me']);

    if (headers_sent($file, $line)) {
        error_log('jws_handle_login_request: ' . $file . ':' . $line);
    }

    $user = wp_signon($creds, false);

    if (is_wp_error($user)) {
        $jws_login_error = $user;
        return;
    }

    // -----------------------------
    // 追加: 単一セッション発行
    // -----------------------------
    list($ok, $token, $expires_ts) = jws_issue_single_session_token((int)$user->ID, $creds['remember']);

    if (!$ok) {
        // teacher account 前提: membership レコードが無いのは想定外
        wp_logout();
        jws_clear_single_session_cookie();

        $jws_login_error = new WP_Error('membership_missing', 'Membership record not found.');
        return;
    }

	wise_redirect_by_post_redirect_to_if_valid($int_selected_language);

    $url = generate_redirect_url_after_login($int_selected_language);

	if ($url !== null) {
		wp_safe_redirect($url);
		exit;
	}

	$url_home_current = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		'/'
	);
	wp_safe_redirect($url_home_current);
	exit;

}

function wise_redirect_by_post_redirect_to_if_valid($int_selected_language): void
{
    $redirect_to = '';
    if (!empty($_POST['redirect_to'])) {
        $redirect_to = esc_url_raw(wp_unslash($_POST['redirect_to']));
    }

    if ($redirect_to === '') {
        return;
    }

    $blog_id = get_data_blog_id_from_selected_language($int_selected_language ?? null);
    $url_home_current = get_home_url($blog_id, '/');

    // 相対パス "/xxx" を許可（current blog の home に正規化）
    if (strpos($redirect_to, '/') === 0 && strpos($redirect_to, '//') !== 0) {
        $redirect_to = get_home_url($blog_id, $redirect_to);
    }

    // current blog 配下のみ許可（オープンリダイレクト対策）
    if (strpos($redirect_to, $url_home_current) !== 0) {
        return;
    }

    $validated = wp_validate_redirect($redirect_to, '');
    if ($validated === '') {
        return;
    }

    wp_safe_redirect($validated);
    exit;
}


$jws_register_error = null;
$jws_register_user_login = '';
$jws_register_user_email = '';
$jws_register_user_nickname = '';

add_action('init', 'jws_handle_register_request');
function jws_handle_register_request() {

    if (is_admin()) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (! isset($_POST['custom_register_nonce']) || ! wp_verify_nonce($_POST['custom_register_nonce'], 'custom_register_action')) {
        return;
    }

    $int_selected_language = jws_get_language_index();

    global $jws_register_error, $jws_register_user_login, $jws_register_user_email, $jws_register_user_nickname;

    // 🔤 ユーザー名文字数制限
    $min_username_length = 5;
    $max_username_length = 20;

    $arr_register_messages = [
        'username_required' => [
            'ユーザー名を入力してください。',
            '請輸入使用者名稱。',
            'Please enter a username.',
        ],
        'username_too_short' => [
            'ユーザー名は%d文字以上で入力してください。',
            '使用者名稱請至少輸入%d個字元。',
            'The username must be at least %d characters long.',
        ],
        'username_too_long' => [
            'ユーザー名は%d文字以内で入力してください。',
            '使用者名稱請勿超過%d個字元。',
            'The username must not exceed %d characters.',
        ],
        'username_invalid_chars' => [
            'ユーザー名には英数字、ピリオド、ハイフン、アンダーバーのみ使用できます。',
            '使用者名稱僅能使用英數字、點、連字號與底線。',
            'The username may only contain letters, numbers, dots, hyphens, and underscores.',
        ],
        'username_restricted' => [
            'このユーザー名は使用できません。（ブランドまたは公式関連の語が含まれています）',
            '此使用者名稱無法使用。（包含品牌或官方相關字詞）',
            'This username cannot be used (contains brand or official-related term).',
        ],
        'email_required' => [
            'メールアドレスを入力してください。',
            '請輸入電子郵件。',
            'Please enter your email address.',
        ],
        'email_invalid' => [
            'メールアドレスの形式が正しくありません。',
            '電子郵件格式不正確。',
            'The email address format is invalid.',
        ],
        'password_required' => [
            'パスワードと確認用パスワードを入力してください。',
            '請輸入密碼與確認密碼。',
            'Please enter the password and its confirmation.',
        ],
        'password_mismatch' => [
            'パスワードが一致していません。',
            '兩次輸入的密碼不一致。',
            'The passwords do not match.',
        ],
        'password_too_short' => [
            'パスワードは%d文字以上で入力してください。',
            '密碼請至少輸入%d個字元。',
            'The password must be at least %d characters long.',
        ],
        'password_too_long' => [
            'パスワードは%d文字以内で入力してください。',
            '密碼請勿超過%d個字元。',
            'The password must not exceed %d characters.',
        ],
        'nickname_required' => [
            'ニックネームを入力してください。',
            '請輸入暱稱。',
            'Please enter a nickname.',
        ],
        'username_exists' => [
            'このユーザー名はすでに使用されています。',
            '此使用者名稱已被使用。',
            'This username is already taken。',
        ],
        'email_exists' => [
            'このメールアドレスはすでに登録されています。',
            '此電子郵件地址已經註冊。',
            'This email address is already registered。',
        ],
        'email_reserved' => [
            'このメールアドレスは使用できません。',
            '此電子郵件無法使用。',
            'This email address cannot be used.',
        ],
    ];

    $user_login = isset($_POST['user_login']) ? sanitize_user($_POST['user_login']) : '';
    $user_email = isset($_POST['user_email']) ? sanitize_email($_POST['user_email']) : '';
    $user_password = isset($_POST['user_password']) ? $_POST['user_password'] : '';
    $user_password_confirm = isset($_POST['user_password_confirm']) ? $_POST['user_password_confirm'] : '';
    $user_nickname = isset($_POST['user_nickname']) ? sanitize_text_field($_POST['user_nickname']) : '';

    $jws_register_user_login = $user_login;
    $jws_register_user_email = $user_email;
    $jws_register_user_nickname = $user_nickname;

    $errors = new WP_Error();

    // 🚫 ブランド関連禁止ワード（部分一致）
    $restricted_keywords = [
        'japaneseworkshop',
        'japanese-workshop',
        'japanese workshop',
        'jws',
        'wise',
        'w.i.s.e',
        'w-i-s-e',
        'official',
        'admin',
        '運営',
        '管理者',
    ];

    // 🔍 ユーザー名バリデーション
    if ($user_login === '') {
        $errors->add('register_error', $arr_register_messages['username_required'][$int_selected_language]);
    } else {
        $username_length = mb_strlen($user_login);

        if ($username_length < $min_username_length) {
            $errors->add(
                'register_error',
                sprintf($arr_register_messages['username_too_short'][$int_selected_language], $min_username_length)
            );
        } elseif ($username_length > $max_username_length) {
            $errors->add(
                'register_error',
                sprintf($arr_register_messages['username_too_long'][$int_selected_language], $max_username_length)
            );
        }

        if (! preg_match('/^[A-Za-z0-9._-]+$/', $user_login)) {
            $errors->add('register_error', $arr_register_messages['username_invalid_chars'][$int_selected_language]);
        }

        // 🛡 ブランド名等が含まれているか（管理者は除外）
        if (! current_user_can('manage_options')) {
            $lower_username = mb_strtolower($user_login);
            foreach ($restricted_keywords as $keyword) {
                if (strpos($lower_username, mb_strtolower($keyword)) !== false) {
                    $errors->add('register_error', $arr_register_messages['username_restricted'][$int_selected_language]);
                    break;
                }
            }
        }
    }

    // 📧 メールチェック
    if ($user_email === '') {
        $errors->add('register_error', $arr_register_messages['email_required'][$int_selected_language]);
    } elseif (! is_email($user_email)) {
        $errors->add('register_error', $arr_register_messages['email_invalid'][$int_selected_language]);
    } elseif (jws_is_reserved_email($user_email)) {
        $errors->add('register_error', $arr_register_messages['email_reserved'][$int_selected_language]);
    }

    // 🔒 パスワードチェック
    if ($user_password === '' || $user_password_confirm === '') {
        $errors->add('register_error', $arr_register_messages['password_required'][$int_selected_language]);
    } elseif ($user_password !== $user_password_confirm) {
        $errors->add('register_error', $arr_register_messages['password_mismatch'][$int_selected_language]);
    } else {
        $password_min_length = 8;
        $password_max_length = 72;
        $password_length = mb_strlen($user_password);

        if ($password_length < $password_min_length) {
            $errors->add(
                'register_error',
                sprintf($arr_register_messages['password_too_short'][$int_selected_language], $password_min_length)
            );
        } elseif ($password_length > $password_max_length) {
            $errors->add(
                'register_error',
                sprintf($arr_register_messages['password_too_long'][$int_selected_language], $password_max_length)
            );
        }
    }

    // 🧍 ニックネームチェック
    if ($user_nickname === '') {
        $errors->add('register_error', $arr_register_messages['nickname_required'][$int_selected_language]);
    }

    // 🔁 重複確認
    if ($user_login !== '' && username_exists($user_login)) {
        $errors->add('register_error', $arr_register_messages['username_exists'][$int_selected_language]);
    }

    if ($user_email !== '' && email_exists($user_email)) {
        $errors->add('register_error', $arr_register_messages['email_exists'][$int_selected_language]);
    }

    // ❌ エラーがある場合は処理停止
    if ($errors->has_errors()) {
        $jws_register_error = $errors;
        return;
    }

    // 🆔 ユーザー作成
    $user_id = wp_create_user($user_login, $user_password, $user_email);

    if (is_wp_error($user_id)) {
        $jws_register_error = $user_id;
        return;
    }

	execute_insert_free_membership_record((int)$user_id);

    // 👤 ニックネーム更新
    if ($user_nickname !== '') {
        update_user_meta($user_id, 'nickname', $user_nickname);
        wp_update_user([
            'ID'           => $user_id,
            'display_name' => $user_nickname,
        ]);
    }

    // 🚪 自動ログイン
    $creds = [
        'user_login'    => $user_login,
        'user_password' => $user_password,
        'remember'      => true,
    ];

    $user = wp_signon($creds, false);

    if (is_wp_error($user)) {
        $jws_register_error = $user;
        return;
    }

	// -----------------------------
    // 追加: 単一セッション発行
    // -----------------------------
    list($ok, $token, $expires_ts) = jws_issue_single_session_token((int)$user->ID, $creds['remember']);

    if (!$ok) {
        // teacher account 前提: membership レコードが無いのは想定外
        wp_logout();
        jws_clear_single_session_cookie();

        $jws_register_error = new WP_Error('membership_missing', 'Membership record not found.');
        return;
    }

	wise_redirect_by_post_redirect_to_if_valid($int_selected_language);

    $url = generate_redirect_url_after_login($int_selected_language);

	if ($url !== null) {
		wp_safe_redirect($url);
		exit;
	}

	$url_home_current = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		'/'
	);
	wp_safe_redirect($url_home_current);
	exit;

}

function execute_insert_free_membership_record(int $user_id): bool
{
    global
        $t_user_membership,
        $int_Free_Member;

    $arr_insertSQL = [
        ['user_id', '?', $user_id, 'PDO::PARAM_INT'],
        ['level', '?', $int_Free_Member, 'PDO::PARAM_INT'],
    ];

    list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data(
        $t_user_membership,
        $arr_insertSQL
    );

    handle_database_error_and_respond($pdo_has_error, $insert_has_error, $e);

    return true;
}


add_action('init', 'jws_handle_logout_request');
function jws_handle_logout_request() {

    if (is_admin()) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (!isset($_POST['custom_logout_nonce']) || !wp_verify_nonce($_POST['custom_logout_nonce'], 'custom_logout_action')) {
        return;
    }

    if (!is_user_logged_in()) {
        return;
    }

    // 追加：ログアウト時にセッションを完全クリア
    jws_destroy_session();

    // WordPressログアウト
    wp_logout();

    // logout完了ページへ

	$int_selected_language = jws_get_language_index();

	global $path_logout_complete;

	$url_logout_complete = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_logout_complete, '/'))
	);
    wp_safe_redirect($url_logout_complete);
    exit;
}

function jws_destroy_session() {

    if (session_status() === PHP_SESSION_NONE) {
        return;
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}


$jws_profile_error = null;
$jws_profile_notice = '';
$jws_profile_nickname = '';

add_action('init', 'jws_handle_profile_update_request');
function jws_handle_profile_update_request() {

    if (is_admin()) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (! isset($_POST['custom_profile_nonce']) || ! wp_verify_nonce($_POST['custom_profile_nonce'], 'custom_profile_action')) {
        return;
    }

    if (! is_user_logged_in()) {
        return;
    }

    $int_selected_language = jws_get_language_index();

    global $jws_profile_error, $jws_profile_notice, $jws_profile_nickname;

    $arr_profile_messages = [
        'nickname_required' => [
            'ニックネームを入力してください。',
            '請輸入暱稱。',
            'Please enter a nickname.',
        ],
        'profile_updated' => [
            'プロフィールを更新しました。',
            '已更新個人資料。',
            'Your profile has been updated.',
        ],
    ];

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    $user_nickname = isset($_POST['user_nickname']) ? sanitize_text_field($_POST['user_nickname']) : '';

    $jws_profile_nickname = $user_nickname;

    $errors = new WP_Error();

    if ($user_nickname === '') {
        $errors->add('profile_error', $arr_profile_messages['nickname_required'][$int_selected_language]);
    }

    if ($errors->has_errors()) {
        $jws_profile_error = $errors;
        return;
    }

    update_user_meta($user_id, 'nickname', $user_nickname);
    wp_update_user([
        'ID' => $user_id,
        'display_name' => $user_nickname,
    ]);

    $jws_profile_notice = $arr_profile_messages['profile_updated'][$int_selected_language];
}



$jws_change_email_error = null;
$jws_change_email_notice = '';
$jws_change_email_new_email = '';

add_action('init', 'jws_handle_change_email_request');
function jws_handle_change_email_request() {

    // 管理エリアでは制限しない → 管理者は自由に設定可能
    if (is_admin()) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (! isset($_POST['custom_change_email_nonce']) || ! wp_verify_nonce($_POST['custom_change_email_nonce'], 'custom_change_email_action')) {
        return;
    }

    if (! is_user_logged_in()) {
        return;
    }

    $int_selected_language = jws_get_language_index();

    global $jws_change_email_error, $jws_change_email_notice, $jws_change_email_new_email;

    $arr_change_email_messages = [
		'email_required' => [
			'新しいメールアドレスを入力してください。',
			'請輸入新的電子郵件。',
			'Please enter a new email address.'
		],
		'email_invalid' => [
			'メールアドレスの形式が正しくありません。',
			'電子郵件格式不正確。',
			'The email address format is invalid.'
		],
		'email_exists' => [
			'このメールアドレスはすでに登録されています。',
			'此電子郵件已被註冊。',
			'This email address is already registered.'
		],
		'password_required' => [
			'メールアドレスを変更するには現在のパスワードを入力してください。',
			'變更電子郵件時請輸入目前的密碼。',
			'Please enter your current password to change your email address.'
		],
		'password_incorrect' => [
			'現在のパスワードが正しくありません。',
			'目前的密碼不正確。',
			'The current password is incorrect.'
		],
		'email_not_changed' => [
			'現在のメールアドレスと同じです。',
			'與目前的電子郵件相同。',
			'The new email address is the same as the current one.'
		],
		'email_reserved' => [
			'このメールアドレスは使用できません。',
			'此電子郵件無法使用。',
			'This email address cannot be used.'
		],
		'email_updated' => [
			'メールアドレスを更新しました。',
			'已更新電子郵件。',
			'Your email address has been updated.'
		],
	];


    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    $new_email = isset($_POST['user_new_email']) ? sanitize_email($_POST['user_new_email']) : '';
    $current_password = isset($_POST['user_current_password']) ? $_POST['user_current_password'] : '';

    $jws_change_email_new_email = $new_email;

    $errors = new WP_Error();

    // ===== メール形式・予約メールチェック ======
    if ($new_email === '') {
        $errors->add('change_email_error', $arr_change_email_messages['email_required'][$int_selected_language]);
    } elseif (! is_email($new_email)) {
        $errors->add('change_email_error', $arr_change_email_messages['email_invalid'][$int_selected_language]);
    }
    // ★管理画面では通すため is_admin() 側で除外済み
    elseif (jws_is_reserved_email($new_email)) {
        $errors->add('change_email_error', $arr_change_email_messages['email_reserved'][$int_selected_language]);
    }
    elseif ($new_email === $current_user->user_email) {
        $errors->add('change_email_error', $arr_change_email_messages['email_not_changed'][$int_selected_language]);
    }
    elseif (email_exists($new_email)) {
        $errors->add('change_email_error', $arr_change_email_messages['email_exists'][$int_selected_language]);
    }

    // ===== パスワードチェック ======
    if ($current_password === '') {
        $errors->add('change_email_error', $arr_change_email_messages['password_required'][$int_selected_language]);
    } elseif (! wp_check_password($current_password, $current_user->user_pass, $user_id)) {
        $errors->add('change_email_error', $arr_change_email_messages['password_incorrect'][$int_selected_language]);
    }

    if ($errors->has_errors()) {
        $jws_change_email_error = $errors;
        return;
    }

    // 更新処理
    $update_result = wp_update_user([
        'ID' => $user_id,
        'user_email' => $new_email,
    ]);

    if (is_wp_error($update_result)) {
        $jws_change_email_error = $update_result;
        return;
    }

    clean_user_cache($user_id);
    wp_set_current_user($user_id);

    $jws_change_email_notice = $arr_change_email_messages['email_updated'][$int_selected_language];
}


$jws_delete_account_error = null;
$jws_delete_account_notice = '';
$jws_delete_account_checked = [];

add_action('init', 'jws_handle_delete_account_request');
function jws_handle_delete_account_request() {

    if (is_admin()) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (! isset($_POST['custom_delete_account_nonce']) || ! wp_verify_nonce($_POST['custom_delete_account_nonce'], 'custom_delete_account_action')) {
        return;
    }

    if (! is_user_logged_in()) {
        return;
    }

	$int_selected_language = jws_get_language_index();
	
    global
		$path_delete_account_complete,
		$jws_delete_account_error,
		$jws_delete_account_notice,
		$jws_delete_account_checked;

    $arr_delete_messages = [
        'must_stop_auto_renew' => [
            '有料会員の自動更新が有効のため、アカウント削除の前に自動更新を停止してください。',
            '目前仍為付費會員自動續訂狀態，請先停止自動續訂後再申請刪除帳號。',
            'You must stop the paid membership auto-renewal before deleting your account.',
        ],
        'checkbox_1_required' => [
            '「有料会員の自動更新を停止済みであること」のチェックが必要です。',
            '請勾選「已停止付費會員自動續訂」。',
            'You must confirm that auto-renewal has been stopped.',
        ],
        'checkbox_2_required' => [
            '「退会後は有料コンテンツを利用できなくなること」のチェックが必要です。',
            '請勾選「了解退會後無法再使用付費內容」。',
            'You must confirm that you will lose access to paid content after deletion.',
        ],
        'checkbox_3_required' => [
            '「退会後の個人情報の取り扱いに同意」のチェックが必要です。',
            '請勾選「同意退會後之個人資料處理方式」。',
            'You must agree to the handling of your personal data after deletion.',
        ],
        'checkbox_4_required' => [
            '「退会を希望します」のチェックが必要です。',
            '請勾選「我希望退會並刪除帳號」。',
            'You must confirm that you wish to delete your account.',
        ],
        'deleted' => [
            'アカウントを削除しました。',
            '已刪除帳號。',
            'Your account has been deleted.',
        ],
    ];

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    if (jws_is_user_auto_renew_active($user_id)) {
        $errors = new WP_Error();
        $errors->add('delete_account_error', $arr_delete_messages['must_stop_auto_renew'][$int_selected_language]);
        $jws_delete_account_error = $errors;
        return;
    }

    $checked_1 = ! empty($_POST['confirm_stop_renewal']);
    $checked_2 = ! empty($_POST['confirm_lose_access']);
    $checked_3 = ! empty($_POST['confirm_data_handling']);
    $checked_4 = ! empty($_POST['confirm_delete']);

    $jws_delete_account_checked = [
        'confirm_stop_renewal' => $checked_1,
        'confirm_lose_access' => $checked_2,
        'confirm_data_handling' => $checked_3,
        'confirm_delete' => $checked_4,
    ];

    $errors = new WP_Error();

    if (! $checked_1) {
        $errors->add('delete_account_error', $arr_delete_messages['checkbox_1_required'][$int_selected_language]);
    }
    if (! $checked_2) {
        $errors->add('delete_account_error', $arr_delete_messages['checkbox_2_required'][$int_selected_language]);
    }
    if (! $checked_3) {
        $errors->add('delete_account_error', $arr_delete_messages['checkbox_3_required'][$int_selected_language]);
    }
    if (! $checked_4) {
        $errors->add('delete_account_error', $arr_delete_messages['checkbox_4_required'][$int_selected_language]);
    }

    if ($errors->has_errors()) {
        $jws_delete_account_error = $errors;
        return;
    }

    update_user_meta($user_id, 'jws_account_status', 'deleted');
    update_user_meta($user_id, 'jws_account_deleted_at', current_time('mysql'));

    remove_action('wp_logout', 'custom_redirect_after_logout');

	wp_logout();

	$url_delete_account_complete = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_delete_account_complete, '/'))
	);
	wp_safe_redirect($url_delete_account_complete);
	exit;

    exit;
}


$jws_change_password_error = null;
$jws_change_password_notice = '';

add_action('init', 'jws_handle_change_password_request');
function jws_handle_change_password_request() {

    if (is_admin()) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (! isset($_POST['custom_change_password_nonce']) || ! wp_verify_nonce($_POST['custom_change_password_nonce'], 'custom_change_password_action')) {
        return;
    }

    if (! is_user_logged_in()) {
        return;
    }

	$int_selected_language = jws_get_language_index();
	
    global $jws_change_password_error, $jws_change_password_notice;

    $arr_change_password_messages = [
        'current_password_required' => [
            '現在のパスワードを入力してください。',
            '請輸入目前的密碼。',
            'Please enter your current password.',
        ],
        'current_password_incorrect' => [
            '現在のパスワードが正しくありません。',
            '目前的密碼不正確。',
            'The current password is incorrect.',
        ],
        'new_password_required' => [
            '新しいパスワードと確認用パスワードを入力してください。',
            '請輸入新的密碼與確認密碼。',
            'Please enter the new password and its confirmation.',
        ],
        'new_password_mismatch' => [
            '新しいパスワードが一致していません。',
            '兩次輸入的新密碼不一致。',
            'The new passwords do not match.',
        ],
        'new_password_too_short' => [
            'パスワードは%d文字以上にしてください。',
            '密碼請設定為 %d 字元以上。',
            'The password must be at least %d characters.',
        ],
        'new_password_too_long' => [
            'パスワードは%d文字以内にしてください。',
            '密碼請勿超過 %d 個字元。',
            'The password must not exceed %d characters.',
        ],
        'password_updated' => [
            'パスワードを変更しました。',
            '已變更密碼。',
            'Your password has been updated.',
        ],
    ];

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    $current_password = isset($_POST['user_current_password']) ? $_POST['user_current_password'] : '';
    $new_password = isset($_POST['user_new_password']) ? $_POST['user_new_password'] : '';
    $new_password_confirm = isset($_POST['user_new_password_confirm']) ? $_POST['user_new_password_confirm'] : '';

    $errors = new WP_Error();

    if ($current_password === '') {
        $errors->add('change_password_error', $arr_change_password_messages['current_password_required'][$int_selected_language]);
    } elseif (! wp_check_password($current_password, $current_user->user_pass, $user_id)) {
        $errors->add('change_password_error', $arr_change_password_messages['current_password_incorrect'][$int_selected_language]);
    }

    if ($new_password === '' || $new_password_confirm === '') {
        $errors->add('change_password_error', $arr_change_password_messages['new_password_required'][$int_selected_language]);
    } elseif ($new_password !== $new_password_confirm) {
        $errors->add('change_password_error', $arr_change_password_messages['new_password_mismatch'][$int_selected_language]);
    } else {
        $min_password_length = 8;
        $max_password_length = 72;

        if (strlen($new_password) < $min_password_length) {
            $errors->add(
                'change_password_error',
                sprintf($arr_change_password_messages['new_password_too_short'][$int_selected_language], $min_password_length)
            );
        } elseif (strlen($new_password) > $max_password_length) {
            $errors->add(
                'change_password_error',
                sprintf($arr_change_password_messages['new_password_too_long'][$int_selected_language], $max_password_length)
            );
        }
    }

    if ($errors->has_errors()) {
        $jws_change_password_error = $errors;
        return;
    }

    wp_set_password($new_password, $user_id);

    wp_set_auth_cookie($user_id, true);
    wp_set_current_user($user_id);

    $jws_change_password_notice = $arr_change_password_messages['password_updated'][$int_selected_language];
}


$jws_lost_password_error = null;
$jws_lost_password_notice = '';
$jws_lost_password_login = '';

add_action('init', 'jws_handle_lost_password_request');
function jws_handle_lost_password_request() {

    if (is_admin()) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (! isset($_POST['custom_lost_password_nonce']) || ! wp_verify_nonce($_POST['custom_lost_password_nonce'], 'custom_lost_password_action')) {
        return;
    }

	$int_selected_language = jws_get_language_index();
	
    global
		$int_used_language_jpn,
		$int_used_language_cht,
		$int_used_language_eng,
		$path_reset_password,
		$jws_lost_password_error,
		$jws_lost_password_notice,
		$jws_lost_password_login;

    $arr_lost_password_messages = [
        'login_or_email_required' => [
            'ユーザー名またはメールアドレスを入力してください。',
            '請輸入使用者名稱或電子郵件。',
            'Please enter your username or email address.',
        ],
        'email_sent' => [
            'パスワード再設定用のリンクをメールで送信しました（登録がある場合）。',
            '已寄出密碼重設連結（若該帳號已註冊）。',
            'If an account exists for the provided information, a reset link has been sent.',
        ],
        'email_failed' => [
            'メールの送信に失敗しました。時間をおいて再度お試しください。',
            '郵件寄送失敗，請稍後再試。',
            'Failed to send the email. Please try again later.',
        ],
        'invalid_request' => [
            'リクエストが正しくありません。',
            '請求不正確。',
            'Invalid request.',
        ],
    ];

    $login_or_email = isset($_POST['user_login_or_email']) ? sanitize_text_field($_POST['user_login_or_email']) : '';
    $jws_lost_password_login = $login_or_email;

    $errors = new WP_Error();

    if ($login_or_email === '') {
        $errors->add('lost_password_error', $arr_lost_password_messages['login_or_email_required'][$int_selected_language]);
    }

    if ($errors->has_errors()) {
        $jws_lost_password_error = $errors;
        return;
    }

    if (is_email($login_or_email)) {
        $user = get_user_by('email', $login_or_email);
    } else {
        $user = get_user_by('login', $login_or_email);
    }

    if (! $user) {
        $jws_lost_password_notice = $arr_lost_password_messages['email_sent'][$int_selected_language];
        return;
    }

    $reset_key = get_password_reset_key($user);

    if (is_wp_error($reset_key)) {
        $errors->add('lost_password_error', $arr_lost_password_messages['invalid_request'][$int_selected_language]);
        $jws_lost_password_error = $errors;
        return;
    }

	$url_reset_password = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_reset_password, '/'))
	);

	$reset_url = add_query_arg(
		[
			'login' => rawurlencode($user->user_login),
			'key' => $reset_key,
		],
		$url_reset_password
	);

    if ($int_selected_language === $int_used_language_cht) {
        $subject = '【Japanese Workshop】密碼重設連結';
        $message = "這是 Japanese Workshop 的密碼重設通知。\n\n"
            . "請點擊以下連結重設您的密碼：\n"
            . $reset_url . "\n\n"
            . "如果您沒有要求重設密碼，請忽略此郵件。";
    } elseif ($int_selected_language === $int_used_language_eng) {
        $subject = '[Japanese Workshop] Password Reset Link';
        $message = "You requested a password reset for your Japanese Workshop account.\n\n"
            . "Please click the link below to reset your password:\n"
            . $reset_url . "\n\n"
            . "If you did not request this, please ignore this email.";
    } else {
        $subject = '【Japanese Workshop】パスワード再設定のご案内';
        $message = "Japanese Workshop のパスワード再設定リクエストを受け付けました。\n\n"
            . "以下のリンクをクリックして、パスワードを再設定してください。\n"
            . $reset_url . "\n\n"
            . "※このメールに心当たりがない場合は、破棄してください。";
    }

    $mail_sent = wp_mail($user->user_email, $subject, $message);

    if (! $mail_sent) {
        $errors->add('lost_password_error', $arr_lost_password_messages['email_failed'][$int_selected_language]);
        $jws_lost_password_error = $errors;
        return;
    }

    $jws_lost_password_notice = $arr_lost_password_messages['email_sent'][$int_selected_language];
}



$jws_reset_password_error = null;
$jws_reset_password_notice = '';

add_action('init', 'jws_handle_reset_password_request');
function jws_handle_reset_password_request() {

    if (is_admin()) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

	$int_selected_language = jws_get_language_index();
	
    global
        $path_reset_password,
        $jws_reset_password_error,
        $jws_reset_password_notice;

    $jws_reset_password_error = null;
    $jws_reset_password_notice = '';

    if (! isset($_POST['custom_reset_password_nonce']) ||
        ! wp_verify_nonce($_POST['custom_reset_password_nonce'], 'custom_reset_password_action')) {

        $jws_reset_password_error = new WP_Error('reset_password_error', 'セキュリティチェックに失敗しました。もう一度お試しください。');
        return;
    }

    $arr_reset_password_messages = [
        'invalid_link' => [
            'パスワード再設定用のリンクが無効か、期限切れです。もう一度最初からやり直してください。',
            '密碼重設連結無效或已過期，請重新申請。',
            'The password reset link is invalid or has expired. Please request a new one.',
        ],
        'password_required' => [
            '新しいパスワードと確認用パスワードを入力してください。',
            '請輸入新的密碼與確認密碼。',
            'Please enter the new password and its confirmation.',
        ],
        'password_mismatch' => [
            '新しいパスワードが一致していません。',
            '兩次輸入的新密碼不一致。',
            'The new passwords do not match.',
        ],
        'password_too_short' => [
            'パスワードは8文字以上にしてください。',
            '密碼請設定為 8 字元以上。',
            'The password must be at least 8 characters.',
        ],
    ];

    $reset_key = isset($_POST['reset_key']) ? sanitize_text_field($_POST['reset_key']) : '';
    $reset_login = isset($_POST['reset_login']) ? sanitize_text_field($_POST['reset_login']) : '';

    $new_password = isset($_POST['user_new_password']) ? (string) $_POST['user_new_password'] : '';
    $new_password_confirm = isset($_POST['user_new_password_confirm']) ? (string) $_POST['user_new_password_confirm'] : '';

    $errors = new WP_Error();

    if ($reset_key === '' || $reset_login === '') {
        $errors->add('reset_password_error', $arr_reset_password_messages['invalid_link'][$int_selected_language]);
        $jws_reset_password_error = $errors;
        return;
    }

    $user = check_password_reset_key($reset_key, $reset_login);

    if (is_wp_error($user) || ! $user) {
        $errors->add('reset_password_error', $arr_reset_password_messages['invalid_link'][$int_selected_language]);
        $jws_reset_password_error = $errors;
        return;
    }

    if ($new_password === '' || $new_password_confirm === '') {
        $errors->add('reset_password_error', $arr_reset_password_messages['password_required'][$int_selected_language]);
    } elseif ($new_password !== $new_password_confirm) {
        $errors->add('reset_password_error', $arr_reset_password_messages['password_mismatch'][$int_selected_language]);
    } elseif (strlen($new_password) < 8) {
        $errors->add('reset_password_error', $arr_reset_password_messages['password_too_short'][$int_selected_language]);
    }

    if ($errors->has_errors()) {
        $jws_reset_password_error = $errors;
        return;
    }

    reset_password($user, $new_password);

    $url_reset_password = get_home_url(
        get_data_blog_id_from_selected_language($int_selected_language ?? null),
        trailingslashit(ltrim($path_reset_password, '/'))
    );

    $redirect_url = add_query_arg(
        [
            'success' => '1',
        ],
        $url_reset_password
    );

    wp_safe_redirect($redirect_url);
    exit;
}



/******************************************************
 *  LogIn tools
 *  
 ******************************************************/
function jws_is_reserved_email($email) {

    if (! is_email($email)) {
        return false;
    }

    list($local_part, $domain) = explode('@', $email, 2);

    $local_part = strtolower($local_part);
    $domain = strtolower($domain);

    // 予約ドメイン（サイトで利用する独自ドメイン）
    $reserved_domains = [
        'japaneseworkshop.online',
        'spballoon.com',
    ];

    // よくあるシステム用ローカル部
    $reserved_local_parts = [
        'info',
        'admin',
        'support',
        'no-reply',
        'noreply',
        'webmaster',
        'contact',
    ];

    // ドメインが完全一致であれば全て禁止
    if (in_array($domain, $reserved_domains, true)) {
        return true;
    }

    // @gmail.com でも localが admin など…は禁止
    if (in_array($local_part, $reserved_local_parts, true)) {
        return true;
    }

    return false;
}
