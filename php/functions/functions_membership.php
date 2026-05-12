<?php

function build_html_about_membership_page($int_selected_language) {

    global $arr_membership_levels;

    if (!isset($arr_membership_levels) || !is_array($arr_membership_levels)) {
        return '';
    }

    $arr_theme_class_by_label = [
        'Free' => 'theme-free',
        'Basic' => 'theme-basic',
        'Plus' => 'theme-plus',
        'Premium' => 'theme-premium'
    ];

    $arr_plan_order = build_arr_membership_plan_order($arr_membership_levels);

	// ーーーーーーーーーーーーーーーーーーーーデバッグ 後で削除ーーーーーーーーーーーーーーーーーーーー
    $arr_theme_class_by_label = [
        'Free' => 'theme-free',
        'Premium' => 'theme-premium'
    ];

	global $int_Free_Member, $int_Premium_Student;
	$allowed_plans = [
		$int_Free_Member,
		$int_Premium_Student,
	];

	$arr_plan_order = array_values(
		array_intersect($arr_plan_order, $allowed_plans)
	);
	// ーーーーーーーーーーーーーーーーーーーーデバッグ 後で削除ーーーーーーーーーーーーーーーーーーーー

    $html = '';
    $html .= '<div class="membershipWrap">';
    $html .= '<div class="membershipGrid">';

    foreach ($arr_plan_order as $level_key) {

        if (!isset($arr_membership_levels[$level_key])) {
            continue;
        }

        $plan = $arr_membership_levels[$level_key];

        $label = $plan['label'] ?? 'Plan';
        $theme_class = $arr_theme_class_by_label[$label] ?? 'theme-free';

        $title = pick_lang_value($plan['title'] ?? '', $int_selected_language);
        $target_level = pick_lang_value($plan['target_level'] ?? '', $int_selected_language);

        $monthly = pick_lang_value($plan['fee']['monthly'] ?? '', $int_selected_language);
        $yearly = pick_lang_value($plan['fee']['yearly'] ?? '', $int_selected_language);

        $button_label = pick_lang_value($plan['button_label'] ?? 'Start', $int_selected_language);

		$path = $plan['path'] ?? null;

		$url = $path
			? get_home_url(
				get_data_blog_id_from_selected_language($int_selected_language ?? null),
				trailingslashit(ltrim($path, '/'))
			)
			: '#';


        $html .= '<section class="planCard ' . escape_html($theme_class) . '">';

        // planHead：常に equal height 対象（ただし行単位）
        $html .= '<div class="planHead equalHeightTarget" data-eq-key="planHead">';
        $html .= '<div class="planLabel">' . escape_html($label) . '</div>';
        $html .= '<div class="planSub">';
        $html .= escape_html($title);
        if ($target_level !== '') {
            $html .= '<span>（' . escape_html($target_level) . '）</span>';
        }
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div class="planBody">';

        foreach ($plan['description'] ?? [] as $desc) {

            if (!is_array($desc)) {
                continue;
            }

            $category_key = (string)($desc['category_key'] ?? '');
            $category_label = pick_lang_value($desc['category_label'] ?? '', $int_selected_language);
            $items = $desc['items'] ?? [];

            $section_class = 'planSection';
            $section_attr = '';

            if (is_equal_height_section_key($category_key)) {
                $section_class .= ' equalHeightSection equalHeightTarget';
                $section_attr .= ' data-eq-key="' . escape_html($category_key) . '"';
            }

            $html .= '<div class="' . escape_html($section_class) . '"' . $section_attr . '>';
            $html .= '<p class="planSectionTitle">' . escape_html($category_label) . '</p>';
            $html .= '<ul class="planList">';

            foreach ($items as $row) {

                $text = pick_lang_value($row, $int_selected_language);

                $is_sub = false;
                if (is_string($text) && strpos($text, '---') === 0) {
                    $is_sub = true;
                    $text = ltrim(substr($text, 3));
                }

                $html .= '<li class="planItem' . ($is_sub ? ' isSub' : '') . '">';
                $html .= escape_html($text);
                $html .= '</li>';
            }

            $html .= '</ul>';
            $html .= '</div>';
        }

        $html .= '</div>';

        $html .= '<div class="planFoot equalHeightTarget" data-eq-key="planFoot">';
        $html .= '<div class="planFee">';
        $html .= '<span class="feeMonth">月 ' . escape_html($monthly) . '</span>';
		// ーーーーーーーーーーーーーーーーーーーーデバッグ 後で復元ーーーーーーーーーーーーーーーーーーーー
        // $html .= '<span class="feeYear">/ 年 ' . escape_html($yearly) . '</span>';
		// ーーーーーーーーーーーーーーーーーーーーデバッグ 後で復元ーーーーーーーーーーーーーーーーーーーー
        $html .= '</div>';
        $html .= '<a class="planButton" href="' . esc_url($url) . '">';
        $html .= escape_html($button_label);
        $html .= '</a>';
        $html .= '</div>';

        $html .= '</section>';
    }

    $html .= '</div>';
    $html .= '</div>';

    return $html;
}

function is_equal_height_section_key($category_key) {

    return in_array(
        (string)$category_key,
        ['study_style', 'learning_materials'],
        true
    );
}

function pick_lang_value($value, $int_selected_language) {

    if (is_array($value)) {

        if (isset($value[$int_selected_language]) && $value[$int_selected_language] !== '') {
            return $value[$int_selected_language];
        }

        $first = reset($value);
        return $first === false ? '' : $first;
    }

    return (string)$value;
}


function build_arr_membership_plan_order($arr_membership_levels) {

    global 
		$int_Free_Member,
		$int_Basic_Student,
		$int_Plus_Student,
		$int_Premium_Student;

    $arr_order = [];

    foreach ([
        $int_Free_Member ?? null,
        $int_Basic_Student ?? null,
        $int_Plus_Student ?? null,
        $int_Premium_Student ?? null
    ] as $key) {

        if ($key !== null && isset($arr_membership_levels[$key])) {
            $arr_order[] = $key;
        }
    }

    return !empty($arr_order) ? $arr_order : array_keys($arr_membership_levels);
}


/******************************************************
 *  apply_page
 *  
 ******************************************************/
// ==============================
// グローバル（エラー＆入力保持）
// ==============================
$jws_membership_apply_error = null;
$jws_membership_apply_message_value = '';


// ==============================
// POST処理（init）
// ==============================
add_action('init', 'jws_handle_user_membership_apply_request');
function jws_handle_user_membership_apply_request(){

    if(is_admin()){
        return;
    }

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        return;
    }

    if(!isset($_POST['user_membership_apply_action']) || $_POST['user_membership_apply_action'] !== 'apply'){
        return;
    }

    if(!isset($_POST['user_membership_apply_nonce']) || !wp_verify_nonce($_POST['user_membership_apply_nonce'], 'user_membership_apply_action')){
        return;
    }

    if(!is_user_logged_in()){
        return;
    }

    global
        $jws_membership_apply_error,
        $jws_membership_apply_message_value,
        $int_Basic_Student,
        $int_Plus_Student,
        $int_Premium_Student,
        $t_user_membership_apply,
        $path_membership_status;

    $int_selected_language = jws_get_language_index();

    $arr_messages = [

        'agree_required' => [
            '規約への同意が必要です。',
            '需要同意相關條款。',
            'You must agree to the terms.',
        ],

        'plan_invalid' => [
            '申請プランが正しくありません。',
            '申請方案不正確。',
            'Invalid plan selected.',
        ],

        'message_too_long' => [
            'メッセージは%d文字以内で入力してください。',
            '留言請勿超過%d個字元。',
            'Message must be %d characters or fewer.',
        ],

        'already_pending' => [
            'すでに申請中です。申請状況をご確認ください。',
            '您已提交申請，請查看申請狀態。',
            'You already have a pending application. Please check the status.',
        ],

        'apply_failed' => [
            '申請の登録に失敗しました。時間を置いて再度お試しください。',
            '申請提交失敗，請稍後再試。',
            'Failed to submit your application. Please try again later.',
        ],
    ];

    $current_user = wp_get_current_user();
    $current_user_id = (int) ($current_user->ID ?? 0);

    if($current_user_id < 1){
        return;
    }

    $errors = new WP_Error();

    // 入力取得
    $agree_terms = isset($_POST['agree_terms']) ? (int) $_POST['agree_terms'] : 0;
    $apply_level = isset($_POST['apply_level']) ? (int) $_POST['apply_level'] : 0;

    $apply_message = isset($_POST['apply_message']) ? sanitize_textarea_field($_POST['apply_message']) : '';
    $jws_membership_apply_message_value = $apply_message;

    // pending確認（既存関数）
    $arr_pending_apply =
        fetch_arr_user_membership_apply_latest_by_user_id_and_status(
            $current_user_id,
            'pending'
        );

    if(!empty($arr_pending_apply)){
        $errors->add('apply_error', $arr_messages['already_pending'][$int_selected_language]);
    }

    // 同意必須
    if($agree_terms !== 1){
        $errors->add('apply_error', $arr_messages['agree_required'][$int_selected_language]);
    }

    // white_list（将来拡張しやすい）
    $arr_apply_level_white_list = [
        (int) $int_Basic_Student,
        (int) $int_Plus_Student,
        (int) $int_Premium_Student,
    ];

    if(!in_array((int) $apply_level, $arr_apply_level_white_list, true)){
        $errors->add('apply_error', $arr_messages['plan_invalid'][$int_selected_language]);
    }

    // メッセージ（任意）
    $max_message_length = 1000;
    if($apply_message !== '' && mb_strlen($apply_message) > $max_message_length){
        $errors->add(
            'apply_error',
            sprintf($arr_messages['message_too_long'][$int_selected_language], $max_message_length)
        );
    }

    if($errors->has_errors()){
        $jws_membership_apply_error = $errors;
        return;
    }

    // ─────────────────────────
    // INSERT（execute_insert_data 直実行）
    // ─────────────────────────

    // unique_code（既存仕様があるなら置換してください）
    try{
        $generated = bin2hex(random_bytes(6)); // 12 chars
    }catch(Throwable $e){
        $generated = uniqid('', true);
    }

    $status = 'pending';
	$agreed_at = current_time('mysql');

	$message_value = ($apply_message === '') ? null : $apply_message;
	$message_param = ($apply_message === '') ? 'PDO::PARAM_NULL' : 'PDO::PARAM_STR';

	$arr_insertSQL = [
		['user_id', '?', $current_user_id, 'PDO::PARAM_INT'],
		['apply_level', '?', (int) $apply_level, 'PDO::PARAM_INT'],
		['apply_status', '?', $status, 'PDO::PARAM_STR'],
		['agreed_at', '?', $agreed_at, 'PDO::PARAM_STR'],
		['apply_message', '?', $message_value, $message_param],
	];

	list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data($t_user_membership_apply, $arr_insertSQL);


    if($pdo_has_error || $insert_has_error){
        $jws_membership_apply_error = new WP_Error('apply_error', $arr_messages['apply_failed'][$int_selected_language]);
        return;
    }

    // ─────────────────────────
    // 成功：statusへ
    // ─────────────────────────
    $url_status = get_home_url(
        get_data_blog_id_from_selected_language($int_selected_language ?? null),
        trailingslashit(ltrim($path_membership_status, '/'))
    );

    wp_safe_redirect($url_status);
    exit;
}

// ==============================
// 表示（applyページ）
// ==============================
function build_html_user_membership_apply_page($int_selected_language){

    global
        $path_membership_status,
        $int_Basic_Student,
        $int_Plus_Student,
        $int_Premium_Student,
        $arr_membership_prices,
        $jws_membership_apply_error,
        $jws_membership_apply_message_value;

    $int_selected_language = (int) ($int_selected_language ?? 0);

    // ─────────────────────────
    // 表示メッセージ（多言語）
    // ─────────────────────────
    $arr_membership_apply_messages = [

        'page_title' => [
            '有料会員申請',
            '付費會員申請',
            'Paid Membership Application',
        ],

        'pending_message' => [
            '現在、承認審査中です。',
            '目前正在審核中。',
            'Your application is currently under review.',
        ],

        'go_status' => [
            '申請状況を見る',
            '查看申請狀態',
            'View application status',
        ],

        'form_description' => [
            '以下をご確認のうえ、同意して申請してください。',
            '請確認以下內容並同意後提出申請。',
            'Please review the following and agree before applying.',
        ],

        'terms_fee_title' => [
            '料金',
            '費用',
            'Fee',
        ],

        'terms_method' => [
            '授業方法：Google Meet',
            '上課方式：Google Meet',
            'Lesson method: Google Meet',
        ],

        'terms_contact' => [
            '連絡方法：登録メールアドレス',
            '聯絡方式：註冊的電子郵件',
            'Contact: Registered email address',
        ],

        'agree_terms' => [
            '料金および確認事項に同意します',
            '我同意費用及相關條款',
            'I agree to the fee and terms',
        ],

        'label_plan' => [
            '申請プラン',
            '申請方案',
            'Plan',
        ],

        'label_message' => [
            'メッセージ（任意）',
            '留言（選填）',
            'Message (optional)',
        ],

        'submit_button' => [
            '申請する',
            '送出申請',
            'Apply',
        ],

        'label_monthly' => [
            '月額',
            '月費',
            'Monthly',
        ],

        'label_yearly' => [
            '年額',
            '年費',
            'Yearly',
        ],

        'lesson_fee_regular' => [
            'レッスン料金（通常）',
            '課程費用（一般）',
            'Lesson fee (standard)',
        ],

        'lesson_fee_benefit' => [
            '特典',
            '優惠',
            'Benefit',
        ],
    ];

    // ─────────────────────────
    // ログインチェック
    // ─────────────────────────
    if(!is_user_logged_in()){
        return '';
    }

    $current_user = wp_get_current_user();
    $current_user_id = (int) ($current_user->ID ?? 0);

    if($current_user_id < 1){
        return '';
    }

    // ─────────────────────────
    // pending 判定
    // ─────────────────────────
    $arr_pending_apply =
        fetch_arr_user_membership_apply_latest_by_user_id_and_status(
            $current_user_id,
            'pending'
        );

    // ─────────────────────────
    // 申請中の場合
    // ─────────────────────────
    if(!empty($arr_pending_apply)){

        $url_status = get_home_url(
            get_data_blog_id_from_selected_language($int_selected_language ?? null),
            trailingslashit(ltrim($path_membership_status, '/'))
        );

        $html  = '';
        $html .= '<div class="membershipApply">';
        $html .= '<h2>' . esc_html($arr_membership_apply_messages['page_title'][$int_selected_language]) . '</h2>';
        $html .= '<p>' . esc_html($arr_membership_apply_messages['pending_message'][$int_selected_language]) . '</p>';
        $html .= '<p>';
        $html .= '<a class="submitButton" href="' . esc_url($url_status) . '">';
        $html .= esc_html($arr_membership_apply_messages['go_status'][$int_selected_language]);
        $html .= '</a>';
        $html .= '</p>';
        $html .= '</div>';

        return $html;
    }

    // ─────────────────────────
    // white_list（表示にも使う：将来拡張しやすい）
    // ─────────────────────────
    $arr_apply_level_white_list = [
        (int) $int_Basic_Student,
        (int) $int_Plus_Student,
        (int) $int_Premium_Student,
    ];
	
	// ーーーーーーーーーーーーーーーーーーーーデバッグ 後で削除ーーーーーーーーーーーーーーーーーーーー
    $arr_apply_level_white_list = [
        (int) $int_Premium_Student,
    ];
	// ーーーーーーーーーーーーーーーーーーーーデバッグ 後で削除ーーーーーーーーーーーーーーーーーーーー

    $arr_apply_level_labels = [
        (int) $int_Basic_Student => 'Basic',
        (int) $int_Plus_Student => 'Plus',
        (int) $int_Premium_Student => 'Premium',
    ];

    // ─────────────────────────
    // 申請フォーム表示
    // ─────────────────────────
    $html  = '';
    $html .= '<div class="membershipApply">';
    $html .= '<h2>' . esc_html($arr_membership_apply_messages['page_title'][$int_selected_language]) . '</h2>';

    // エラー表示
    if(is_wp_error($jws_membership_apply_error) && $jws_membership_apply_error->has_errors()){
        $html .= '<div class="membershipApplyError">';
        foreach($jws_membership_apply_error->get_error_messages() as $msg){
            $html .= '<p>' . esc_html($msg) . '</p>';
        }
        $html .= '</div>';
    }

    $html .= '<form method="post">';
    $html .= '<input type="hidden" name="user_membership_apply_action" value="apply">';

    ob_start();
    wp_nonce_field('user_membership_apply_action', 'user_membership_apply_nonce');
    $html .= ob_get_clean();

    // ─────────────────────────
    // プラン選択
    // ─────────────────────────
    $html .= '<div class="membershipApplyField">';
    $html .= '<label>' . esc_html($arr_membership_apply_messages['label_plan'][$int_selected_language]) . '</label>';
    $html .= '<select name="apply_level" id="apply_level" required>';

    foreach($arr_apply_level_white_list as $level){
        $label = $arr_apply_level_labels[$level] ?? ('Plan ' . (string) $level);
        $html .= '<option value="' . esc_attr((string) $level) . '">' . esc_html($label) . '</option>';
    }

    $html .= '</select>';
    $html .= '</div>';

    // ─────────────────────────
    // 料金コンテナ（プラン別料金は hidden 切替）
    // ─────────────────────────
    $html .= '<div class="membershipApplyFees membershipApplySection" id="membershipApplyFees">';
    $html .= '<h3>' . esc_html($arr_membership_apply_messages['terms_fee_title'][$int_selected_language]) . '</h3>';

    foreach($arr_apply_level_white_list as $level){

        $label = $arr_apply_level_labels[$level] ?? ('Plan ' . (string) $level);

        $monthly = $arr_membership_prices[$level]['monthly'][$int_selected_language] ?? '';
        $yearly = $arr_membership_prices[$level]['yearly'][$int_selected_language] ?? '';

        $html .= '<div class="membershipApplyFeeItem hidden" data-membership-level="' . esc_attr((string) $level) . '">';
        $html .= '<h4>' . esc_html($label) . '</h4>';
        $html .= '<ul>';

        if($monthly !== ''){
            $html .= '<li>' . esc_html($arr_membership_apply_messages['label_monthly'][$int_selected_language] . '：' . $monthly) . '</li>';
        }

        if($yearly !== ''){
            $html .= '<li>' . esc_html($arr_membership_apply_messages['label_yearly'][$int_selected_language] . '：' . $yearly) . '</li>';
        }

        // lesson_fee（Premium のみ）
        if(!empty($arr_membership_prices[$level]['lesson_fee']['regular'][$int_selected_language])){
            $lesson_regular =
                $arr_membership_prices[$level]['lesson_fee']['regular'][$int_selected_language];

            $html .= '<li>' . esc_html($arr_membership_apply_messages['lesson_fee_regular'][$int_selected_language] . '：' . $lesson_regular) . '</li>';
        }

        if(!empty($arr_membership_prices[$level]['lesson_fee']['benefit'][$int_selected_language])){
            $lesson_benefit =
                $arr_membership_prices[$level]['lesson_fee']['benefit'][$int_selected_language];

            $html .= '<li>' . esc_html($arr_membership_apply_messages['lesson_fee_benefit'][$int_selected_language] . '：' . $lesson_benefit) . '</li>';
        }

        $html .= '</ul>';
        $html .= '</div>';
    }

    $html .= '</div>';

    // ─────────────────────────
    // 必須確認事項コンテナ
    // ─────────────────────────
    $html .= '<div class="membershipApplyTerms membershipApplySection" id="membershipApplyTerms">';
    $html .= '<p>' . esc_html($arr_membership_apply_messages['form_description'][$int_selected_language]) . '</p>';
    $html .= '<ul>';
    $html .= '<li>' . esc_html($arr_membership_apply_messages['terms_method'][$int_selected_language]) . '</li>';
    $html .= '<li>' . esc_html($arr_membership_apply_messages['terms_contact'][$int_selected_language]) . '</li>';
    $html .= '</ul>';
    $html .= '</div>';

    // ─────────────────────────
    // 同意チェック（料金＋確認事項に同意）
    // ─────────────────────────
    $html .= '<div class="membershipApplyAgree">';
    $html .= '<label>';
    $html .= '<input type="checkbox" name="agree_terms" value="1" required> ';
    $html .= esc_html($arr_membership_apply_messages['agree_terms'][$int_selected_language]);
    $html .= '</label>';
    $html .= '</div>';

    // ─────────────────────────
    // メッセージ（入力保持）
    // ─────────────────────────
    $html .= '<div class="membershipApplyField">';
    $html .= '<label>' . esc_html($arr_membership_apply_messages['label_message'][$int_selected_language]) . '</label>';
    $html .= '<textarea name="apply_message" rows="6">' . esc_textarea((string) $jws_membership_apply_message_value) . '</textarea>';
    $html .= '</div>';

    // ─────────────────────────
    // 送信
    // ─────────────────────────
    $html .= '<button type="submit" class="submitButton">';
    $html .= esc_html($arr_membership_apply_messages['submit_button'][$int_selected_language]);
    $html .= '</button>';

    $html .= '</form>';
    $html .= '</div>';

    return $html;
}

/******************************************************
 *  status_page
 *
 *  - t_user_membership_apply から取得
 *  - 表示：現在のプラン / 申請中のプラン / 状況 / キャンセル
 *  - POST：pending を cancelled に更新（execute_update_data）
 *
 ******************************************************/

/* ==============================
 * グローバル（エラー＆メッセージ）
 * ============================== */
$jws_membership_status_error = null;
$jws_membership_status_message = null;


/* ==============================
 * POST処理（init）: 申請キャンセル
 * ============================== */
add_action('init', 'jws_handle_user_membership_status_request');
function jws_handle_user_membership_status_request(){

    if(is_admin()){
        return;
    }

    if($_SERVER['REQUEST_METHOD'] !== 'POST'){
        return;
    }

    if(!isset($_POST['user_membership_status_action']) || $_POST['user_membership_status_action'] !== 'cancel_apply'){
        return;
    }

    if(!isset($_POST['user_membership_status_nonce']) || !wp_verify_nonce($_POST['user_membership_status_nonce'], 'user_membership_status_action')){
        return;
    }

    if(!is_user_logged_in()){
        return;
    }

    global
        $jws_membership_status_error,
        $jws_membership_status_message,
        $t_user_membership_apply,
        $path_membership_status;

    $int_selected_language = jws_get_language_index();

    $arr_messages = [

        'no_pending' => [
            'キャンセル可能な申請が見つかりませんでした。',
            '找不到可取消的申請。',
            'No cancellable application was found.',
        ],

        'cancel_failed' => [
            'キャンセル処理に失敗しました。時間を置いて再度お試しください。',
            '取消失敗，請稍後再試。',
            'Failed to cancel. Please try again later.',
        ],

        'cancel_success' => [
            '申請をキャンセルしました。',
            '已取消申請。',
            'Your application has been cancelled.',
        ],
    ];

    $current_user = wp_get_current_user();
    $current_user_id = (int) ($current_user->ID ?? 0);

    if($current_user_id < 1){
        return;
    }

    // pending 取得（共通fetch）
    $arr_pending_apply = fetch_arr_user_membership_apply_latest_by_user_id_and_status($current_user_id, 'pending');

    if(empty($arr_pending_apply)){
        $jws_membership_status_error = new WP_Error('status_error', $arr_messages['no_pending'][$int_selected_language]);
        return;
    }

    // ─────────────────────────
    // UPDATE（execute_update_data）
    // pending は 1ユーザー1件設計なので user_id + status で更新します
    // ─────────────────────────
    $update_table = $t_user_membership_apply;

    $arr_updateSQL = [
        ['apply_status', ':update_apply_status', 'cancelled', 'PDO::PARAM_STR'],
    ];

    $arr_whereSQL = [
        ['user_id', ':where_user_id', $current_user_id, 'PDO::PARAM_INT', ' And '],
        ['apply_status', ':where_apply_status', 'pending', 'PDO::PARAM_STR', ''],
    ];

    list($pdo_has_error, $update_has_error, $e) = execute_update_data($update_table, $arr_updateSQL, $arr_whereSQL);

    if($pdo_has_error || $update_has_error){
        $jws_membership_status_error = new WP_Error('status_error', $arr_messages['cancel_failed'][$int_selected_language]);
        return;
    }

    $jws_membership_status_message = $arr_messages['cancel_success'][$int_selected_language];

    $url_status = get_home_url(
        get_data_blog_id_from_selected_language($int_selected_language ?? null),
        trailingslashit(ltrim($path_membership_status, '/'))
    );

    wp_safe_redirect($url_status);
    exit;
}


/* ==============================
 * 表示（statusページ）
 * ============================== */
function build_html_user_membership_status_page($int_selected_language){

    global
        $int_Basic_Student,
        $int_Plus_Student,
        $int_Premium_Student,
        $jws_membership_status_error,
        $jws_membership_status_message;

    $int_selected_language = (int) ($int_selected_language ?? 0);

    $arr_messages = [

        'page_title' => [
            '申請状況',
            '申請狀態',
            'Application Status',
        ],

        'current_plan' => [
            '現在のプラン',
            '目前方案',
            'Current plan',
        ],

        'pending_plan' => [
            '申請中のプラン',
            '申請中的方案',
            'Pending plan',
        ],

        'latest_record' => [
            '直近の申請',
            '最近一次申請',
            'Latest application',
        ],

        'status' => [
            '状況',
            '狀態',
            'Status',
        ],

        'label_created_at' => [
            '申請日時',
            '申請時間',
            'Submitted at',
        ],

        'label_message' => [
            'メッセージ',
            '留言',
            'Message',
        ],

        'no_record' => [
            '申請情報がありません。',
            '沒有申請資訊。',
            'No application record found.',
        ],

        'cancel_button' => [
            '申請をキャンセルする',
            '取消申請',
            'Cancel application',
        ],

        'cancel_confirm' => [
            'この申請をキャンセルしますか？',
            '確定要取消這個申請嗎？',
            'Are you sure you want to cancel this application?',
        ],

        'free_label' => [
            'Free',
            'Free',
            'Free',
        ],
    ];

    if(!is_user_logged_in()){
        return '';
    }

    $current_user = wp_get_current_user();
    $current_user_id = (int) ($current_user->ID ?? 0);

    if($current_user_id < 1){
        return '';
    }

    // ─────────────────────────
    // 取得（共通fetch）
    // ─────────────────────────
    $arr_pending_apply = fetch_arr_user_membership_apply_latest_by_user_id_and_status($current_user_id, 'pending');
    $arr_latest_apply = fetch_arr_user_membership_apply_latest_by_user_id($current_user_id);

    // レベルラベル（必要なら多言語化してください）
    $arr_apply_level_labels = [
        (int) $int_Basic_Student => 'Basic',
        (int) $int_Plus_Student => 'Plus',
        (int) $int_Premium_Student => 'Premium',
    ];

    // ステータス表示ラベル
    $arr_status_label_map = [
        'pending' => [
            '承認待ち',
            '審核中',
            'Pending',
        ],
        'approved' => [
            '承認済み',
            '已核准',
            'Approved',
        ],
        'rejected' => [
            '却下',
            '已拒絕',
            'Rejected',
        ],
        'cancelled' => [
            'キャンセル済み',
            '已取消',
            'Cancelled',
        ],
    ];

    // ─────────────────────────
    // 現在のプラン（暫定）
    // - 本来は確定テーブル（t_user_membership 等）から取得が理想
    // - 現時点では「直近が approved の場合」を現在プラン扱い
    // ─────────────────────────
    $current_plan_level = 0;

    if(!empty($arr_latest_apply) && ($arr_latest_apply['apply_status'] ?? '') === 'approved'){
        $current_plan_level = (int) ($arr_latest_apply['apply_level'] ?? 0);
    }

    $current_plan_label =
        $arr_apply_level_labels[$current_plan_level]
        ?? $arr_messages['free_label'][$int_selected_language];

    // ─────────────────────────
    // HTML
    // ─────────────────────────
    $html = '';
    $html .= '<div class="membershipStatus">';
    $html .= '<h2>' . esc_html($arr_messages['page_title'][$int_selected_language]) . '</h2>';

    // エラー
    if(is_wp_error($jws_membership_status_error) && $jws_membership_status_error->has_errors()){
        $html .= '<div class="membershipStatusError">';
        foreach($jws_membership_status_error->get_error_messages() as $msg){
            $html .= '<p>' . esc_html($msg) . '</p>';
        }
        $html .= '</div>';
    }

    // 成功
    if(is_string($jws_membership_status_message) && $jws_membership_status_message !== ''){
        $html .= '<div class="membershipStatusMessage">';
        $html .= '<p>' . esc_html($jws_membership_status_message) . '</p>';
        $html .= '</div>';
    }

    // データが何も無い
    if(empty($arr_latest_apply) && empty($arr_pending_apply)){
        $html .= '<p>' . esc_html($arr_messages['no_record'][$int_selected_language]) . '</p>';
        $html .= '</div>';
        return $html;
    }

    // ─────────────────────────
    // 現在のプラン
    // ─────────────────────────
    $html .= '<div class="membershipStatusCard membershipStatusSection">';
    $html .= '<h3>' . esc_html($arr_messages['current_plan'][$int_selected_language]) . '</h3>';
    $html .= '<p class="membershipStatusPlan">' . esc_html($current_plan_label) . '</p>';
    $html .= '</div>';

    // ─────────────────────────
    // 申請中（pending がある場合）
    // ─────────────────────────
    if(!empty($arr_pending_apply)){

        $pending_level = (int) ($arr_pending_apply['apply_level'] ?? 0);
        $pending_label = $arr_apply_level_labels[$pending_level] ?? ('Plan ' . (string) $pending_level);

        $pending_status = (string) ($arr_pending_apply['apply_status'] ?? 'pending');
        $pending_status_label = $arr_status_label_map[$pending_status][$int_selected_language] ?? $pending_status;

        $pending_created_at = (string) ($arr_pending_apply['created_at'] ?? '');
        $pending_message = (string) ($arr_pending_apply['apply_message'] ?? '');

        $html .= '<div class="membershipStatusCard membershipStatusSection">';
        $html .= '<h3>' . esc_html($arr_messages['pending_plan'][$int_selected_language]) . '</h3>';

        $html .= '<p class="membershipStatusPlan">' . esc_html($pending_label) . '</p>';

        $html .= '<div class="membershipStatusRow">';
        $html .= '<span class="membershipStatusLabel">' . esc_html($arr_messages['status'][$int_selected_language]) . '</span>';
        $html .= '<span class="membershipStatusValue membershipStatusBadge membershipStatusBadge--' . esc_attr($pending_status) . '">';
        $html .= esc_html($pending_status_label);
        $html .= '</span>';
        $html .= '</div>';

        if($pending_created_at !== ''){
            $html .= '<div class="membershipStatusRow">';
            $html .= '<span class="membershipStatusLabel">' . esc_html($arr_messages['label_created_at'][$int_selected_language]) . '</span>';
            $html .= '<span class="membershipStatusValue">' . esc_html($pending_created_at) . '</span>';
            $html .= '</div>';
        }

        if($pending_message !== ''){
            $html .= '<div class="membershipStatusRow">';
            $html .= '<span class="membershipStatusLabel">' . esc_html($arr_messages['label_message'][$int_selected_language]) . '</span>';
            $html .= '<span class="membershipStatusValue">' . nl2br(esc_html($pending_message)) . '</span>';
            $html .= '</div>';
        }

        // キャンセルボタン
        $html .= '<form method="post" class="membershipStatusCancelForm" onsubmit="return confirm(' . json_encode($arr_messages['cancel_confirm'][$int_selected_language]) . ');">';
        $html .= '<input type="hidden" name="user_membership_status_action" value="cancel_apply">';

        ob_start();
        wp_nonce_field('user_membership_status_action', 'user_membership_status_nonce');
        $html .= ob_get_clean();

        $html .= '<button type="submit" class="submitButton submitButton--danger">';
        $html .= esc_html($arr_messages['cancel_button'][$int_selected_language]);
        $html .= '</button>';

        $html .= '</form>';

        $html .= '</div>';

    }else{

        // ─────────────────────────
        // pending が無い場合：直近の申請（参考表示）
        // ─────────────────────────
        $latest_level = (int) ($arr_latest_apply['apply_level'] ?? 0);
        $latest_label = $arr_apply_level_labels[$latest_level] ?? ('Plan ' . (string) $latest_level);

        $latest_status = (string) ($arr_latest_apply['apply_status'] ?? '');
        $latest_status_label = $arr_status_label_map[$latest_status][$int_selected_language] ?? $latest_status;

        $latest_created_at = (string) ($arr_latest_apply['created_at'] ?? '');
        $latest_message = (string) ($arr_latest_apply['apply_message'] ?? '');

        $html .= '<div class="membershipStatusCard membershipStatusSection">';
        $html .= '<h3>' . esc_html($arr_messages['latest_record'][$int_selected_language]) . '</h3>';

        $html .= '<p class="membershipStatusPlan">' . esc_html($latest_label) . '</p>';

        if($latest_status !== ''){
            $html .= '<div class="membershipStatusRow">';
            $html .= '<span class="membershipStatusLabel">' . esc_html($arr_messages['status'][$int_selected_language]) . '</span>';
            $html .= '<span class="membershipStatusValue membershipStatusBadge membershipStatusBadge--' . esc_attr($latest_status) . '">';
            $html .= esc_html($latest_status_label);
            $html .= '</span>';
            $html .= '</div>';
        }

        if($latest_created_at !== ''){
            $html .= '<div class="membershipStatusRow">';
            $html .= '<span class="membershipStatusLabel">' . esc_html($arr_messages['label_created_at'][$int_selected_language]) . '</span>';
            $html .= '<span class="membershipStatusValue">' . esc_html($latest_created_at) . '</span>';
            $html .= '</div>';
        }

        if($latest_message !== ''){
            $html .= '<div class="membershipStatusRow">';
            $html .= '<span class="membershipStatusLabel">' . esc_html($arr_messages['label_message'][$int_selected_language]) . '</span>';
            $html .= '<span class="membershipStatusValue">' . nl2br(esc_html($latest_message)) . '</span>';
            $html .= '</div>';
        }

        $html .= '</div>';
    }

    $html .= '</div>';

    return $html;
}
