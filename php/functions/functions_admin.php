<?php

/******************************************************
 *  INVITE VIP
 *  
 ******************************************************/

// ====== POST Handler ======
function handle_post_vip_invites($int_selected_language) {

    if (empty($_POST['vip_invites_action'])) {
        return;
    }

    $action = escape_html($_POST['vip_invites_action']);

    if ($action === 'create') {

        $max_usage = isset($_POST['max_usage']) ? intval($_POST['max_usage']) : 0;
        if ($max_usage < 1) {
            $_SESSION['vip_invites_notice'] = 'Invalid max usage.';
            return;
        }

        if (empty($_POST['expires_at'])) {
            $_SESSION['vip_invites_notice'] = 'Expires at is required.';
            return;
        }

        $expires_at_raw = escape_html($_POST['expires_at']);
        $expires_at = normalize_datetime_local_to_mysql($expires_at_raw);
        if (empty($expires_at)) {
            $_SESSION['vip_invites_notice'] = 'Invalid expires at format.';
            return;
        }

        $result = create_user_vip_invite_token($max_usage, $expires_at, $int_selected_language);

        if (!empty($result['created_url'])) {
            $_SESSION['vip_invites_created_url'] = $result['created_url'];
        }
        if (!empty($result['notice'])) {
            $_SESSION['vip_invites_notice'] = $result['notice'];
        }

        return;
    }

    if ($action === 'revoke') {

        $invite_id = isset($_POST['invite_id']) ? intval($_POST['invite_id']) : 0;
        if ($invite_id < 1) {
            $_SESSION['vip_invites_notice'] = 'Invalid invite id.';
            return;
        }

        $notice = revoke_user_vip_invite_token($invite_id, $int_selected_language);
        $_SESSION['vip_invites_notice'] = $notice;

        return;
    }
}


// ====== HTML Page ======
function build_html_vip_invites_page($int_selected_language) {

    $user_level = get_user_level();
    if (!is_admin_level($user_level)) {
        return '';
    }

    $arr_invites = get_arr_user_vip_invite_tokens_for_admin($int_selected_language);

    $str_html = '';
    $str_html .= '<div class="vipInvitesPage">';

    $str_html .= '<h2 class="vipInvitesTitle">VIP Invites</h2>';

    $str_html .= build_html_vip_invites_notice_area();
    $str_html .= build_html_vip_invites_create_form();

    $str_html .= '<div class="vipInvitesListSection">';
    $str_html .= '<h3 class="vipInvitesSubTitle">Invites List</h3>';

    if (empty($arr_invites)) {
        $str_html .= '<p class="vipInvitesEmpty">No invites found.</p>';
    } else {
        $str_html .= build_html_vip_invites_table($arr_invites);
    }

    $str_html .= '</div>';
    $str_html .= '</div>';

    return $str_html;
}


function build_html_vip_invites_notice_area() {

    $str_html = '';
    $str_html .= '<div class="vipInvitesNoticeArea">';

    if (!empty($_SESSION['vip_invites_notice'])) {
        $notice = escape_html($_SESSION['vip_invites_notice']);
        unset($_SESSION['vip_invites_notice']);
        $str_html .= '<p class="vipInvitesNotice">' . $notice . '</p>';
    }

    if (!empty($_SESSION['vip_invites_created_url'])) {
        $created_url = escape_html($_SESSION['vip_invites_created_url']);
        unset($_SESSION['vip_invites_created_url']);

        $str_html .= '<div class="vipInvitesCreatedUrlBox">';
        $str_html .= '<p class="vipInvitesNotice">Invite URL (copy and share):</p>';
        $str_html .= '<input class="vipInvitesCreatedUrlInput" type="text" value="' . $created_url . '" readonly>';
        $str_html .= '</div>';
    }

    $str_html .= '</div>';

    return $str_html;
}


function build_html_vip_invites_create_form() {

    $str_html = '';
    $str_html .= '<div class="vipInvitesCreateSection">';
    $str_html .= '<h3 class="vipInvitesSubTitle">Create Invite</h3>';

    $str_html .= '<form method="post" class="vipInvitesCreateForm">';
    $str_html .= '<input type="hidden" name="vip_invites_action" value="create">';

    $str_html .= '<label class="vipInvitesLabel" for="vipInviteMaxUsage">Max Usage</label>';
    $str_html .= '<input class="vipInvitesInput" id="vipInviteMaxUsage" name="max_usage" type="number" min="1" value="1" required>';

    $str_html .= '<label class="vipInvitesLabel" for="vipInviteExpiresAt">Expires At</label>';
    $str_html .= '<input class="vipInvitesInput" id="vipInviteExpiresAt" name="expires_at" type="datetime-local" required>';

    $str_html .= '<button class="vipInvitesButton" type="submit">Create</button>';

    $str_html .= '<p class="vipInvitesHelp">A token URL will be shown once after creation.</p>';

    $str_html .= '</form>';
    $str_html .= '</div>';

    return $str_html;
}


function build_html_vip_invites_table($arr_invites) {

    $str_html = '';
    $str_html .= '<table class="vipInvitesTable">';
    $str_html .= '<thead>';
    $str_html .= '<tr>';
    $str_html .= '<th>ID</th>';
    $str_html .= '<th>Max</th>';
    $str_html .= '<th>Used</th>';
    $str_html .= '<th>Expires</th>';
    $str_html .= '<th>Revoked</th>';
    $str_html .= '<th>Created</th>';
    $str_html .= '<th>Action</th>';
    $str_html .= '</tr>';
    $str_html .= '</thead>';
    $str_html .= '<tbody>';

    foreach ($arr_invites as $row) {

        $id = intval($row['id'] ?? 0);
        $max_usage = intval($row['max_usage'] ?? 0);
        $used_count = intval($row['used_count'] ?? 0);
        $expires_at = escape_html($row['expires_at'] ?? '');
        $is_revoked = intval($row['is_revoked'] ?? 0);
        $created_at = escape_html($row['created_at'] ?? '');

        $is_expired = false;
        if (!empty($expires_at)) {
            $is_expired = strtotime($expires_at) < time();
        }

        $is_exhausted = $max_usage > 0 && $used_count >= $max_usage;

        $revoked_text = $is_revoked === 1 ? 'Yes' : 'No';
        $disabled = ($is_revoked === 1 || $is_expired || $is_exhausted) ? ' disabled' : '';

        $str_html .= '<tr>';
        $str_html .= '<td>' . $id . '</td>';
        $str_html .= '<td>' . $max_usage . '</td>';
        $str_html .= '<td>' . $used_count . '</td>';
        $str_html .= '<td>' . $expires_at . '</td>';
        $str_html .= '<td>' . $revoked_text . '</td>';
        $str_html .= '<td>' . $created_at . '</td>';

        $str_html .= '<td>';
        $str_html .= '<form method="post" class="vipInvitesRevokeForm">';
        $str_html .= '<input type="hidden" name="vip_invites_action" value="revoke">';
        $str_html .= '<input type="hidden" name="invite_id" value="' . $id . '">';
        $str_html .= '<button class="vipInvitesRevokeButton" type="submit"' . $disabled . '>Revoke</button>';
        $str_html .= '</form>';
        $str_html .= '</td>';

        $str_html .= '</tr>';
    }

    $str_html .= '</tbody>';
    $str_html .= '</table>';

    return $str_html;
}


// ====== SELECT (execute_select_and_fetch_all style) ======
function get_arr_user_vip_invite_tokens_for_admin($int_selected_language) {

    global
        $t_user_vip_invite_tokens;

    $arr_strSQL_select = [
        [$t_user_vip_invite_tokens, 'id'],
        [$t_user_vip_invite_tokens, 'max_usage'],
        [$t_user_vip_invite_tokens, 'used_count'],
        [$t_user_vip_invite_tokens, 'expires_at'],
        [$t_user_vip_invite_tokens, 'is_revoked'],
        [$t_user_vip_invite_tokens, 'user_id'],
        [$t_user_vip_invite_tokens, 'created_at']
    ];

    $strSQL_from = " FROM
                $t_user_vip_invite_tokens
                ";

    $arr_strSQL_where = [];

    $arr_strSQL_order = [
        [$t_user_vip_invite_tokens, 'id', 'DESC']
    ];

    $strSQL_option = ' LIMIT 200';

    list($pdo_has_error, $select_has_error, $e, $arr_rows) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );

    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    return $arr_rows ?? [];
}


// ====== INSERT (execute_insert_data) ======
function create_user_vip_invite_token($max_usage, $expires_at, $int_selected_language) {

    global
        $t_user_vip_invite_tokens;

    $user_id = 0;
    if (function_exists('get_current_user_id')) {
        $user_id = intval(get_current_user_id());
    }

    $token_plain = generate_secure_token();
    $token_hash = hash('sha256', $token_plain);

    $created_at = date('Y-m-d H:i:s');

    $arr_insertSQL = [
        ['token_hash', '?', $token_hash, 'PDO::PARAM_STR'],
        ['max_usage', '?', $max_usage, 'PDO::PARAM_INT'],
        ['used_count', '?', 0, 'PDO::PARAM_INT'],
        ['expires_at', '?', $expires_at, 'PDO::PARAM_STR'],
        ['is_revoked', '?', 0, 'PDO::PARAM_INT'],
        ['user_id', '?', $user_id, 'PDO::PARAM_INT'],
        ['created_at', '?', $created_at, 'PDO::PARAM_STR']
    ];

    list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data($t_user_vip_invite_tokens, $arr_insertSQL);

    if ($pdo_has_error === FLAG_TRUE || $insert_has_error === FLAG_TRUE) {
        return [
            'notice' => 'Failed to create invite.',
            'created_url' => ''
        ];
    }

    $created_url = build_vip_request_url($token_plain);

    return [
        'notice' => 'Invite created.',
        'created_url' => $created_url
    ];
}


// ====== UPDATE (execute_update_data) ======
function revoke_user_vip_invite_token($invite_id, $int_selected_language) {

    global
        $t_user_vip_invite_tokens;

    $update_table = $t_user_vip_invite_tokens;

    $arr_updateSQL = [
        ['is_revoked', ':update_is_revoked', 1, 'PDO::PARAM_INT']
    ];

    $arr_whereSQL = [
        ['id', ':where_id', $invite_id, 'PDO::PARAM_INT', '']
    ];

    list($pdo_has_error, $update_has_error, $e) = execute_update_data($update_table, $arr_updateSQL, $arr_whereSQL);

    if ($pdo_has_error === FLAG_TRUE || $update_has_error === FLAG_TRUE) {
        return 'Failed to revoke invite.';
    }

    return 'Invite revoked.';
}


// ====== Utilities ======
function generate_secure_token() {

    $bytes = random_bytes(32);
    $token = rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');

    return $token;
}


function normalize_datetime_local_to_mysql($datetime_local) {

    if (strpos($datetime_local, 'T') === false) {
        return '';
    }

    $normalized = str_replace('T', ' ', $datetime_local);

    if (strlen($normalized) === 16) {
        $normalized .= ':00';
    }

    $t = strtotime($normalized);
    if ($t === false) {
        return '';
    }

    return date('Y-m-d H:i:s', $t);
}

function build_vip_request_url($token_plain) {

    global $path_request_to_vip;

    $base_url = get_home_url(
        get_data_blog_id_from_selected_language($int_selected_language ?? null),
        trailingslashit(ltrim($path_request_to_vip, '/'))
    );

    return add_query_arg('token', $token_plain, $base_url);
}

/******************************************************
 *  Request to VIP
 *  
 ******************************************************/
function build_html_request_to_vip_page($token, $int_selected_language) {

    global
        $t_user_vip_invite_tokens,
        $t_user_vip_requests,
        $t_user_vip_invite_token_uses;

    // ==================================================
    // Message definitions
    // [0] 日本語 / [1] 中国語（繁体） / [2] 英語
    // ==================================================
    $arr_msg = [

        // ---- Common notices ----
        'login_required' => [
            'ログインが必要です。',
            '需要登入。',
            'Login is required.'
        ],
        'token_required' => [
            'トークンが必要です。',
            '需要邀請碼。',
            'Token is required.'
        ],
        'invalid_token_format' => [
            'トークンの形式が正しくありません。',
            '邀請碼格式不正確。',
            'Invalid token format.'
        ],
        'invalid_or_expired_invite' => [
            '無効、または期限切れの招待です。',
            '邀請無效或已過期。',
            'Invalid or expired invite.'
        ],
        'invite_disabled' => [
            'この招待は無効化されています。',
            '此邀請已被停用。',
            'This invite has been disabled.'
        ],
        'invite_expired' => [
            'この招待は期限切れです。',
            '此邀請已過期。',
            'This invite has expired.'
        ],
        'invite_max_usage' => [
            'この招待は使用上限に達しています。',
            '此邀請已達使用上限。',
            'This invite has reached its maximum usage.'
        ],

        // ---- Request flow ----
        'already_requested' => [
            'この招待ですでにVIP申請をしています。',
            '您已使用此邀請申請過 VIP。',
            'You already have a VIP request for this invite.'
        ],
        'request_success' => [
            'VIP申請を送信しました。承認をお待ちください。',
            'VIP 申請已送出，請等待管理員審核。',
            'VIP request submitted. Please wait for approval.'
        ],
        'request_failed' => [
            'VIP申請の送信に失敗しました。',
            'VIP 申請送出失敗。',
            'Failed to submit VIP request.'
        ],

        // ---- Page texts ----
        'page_title' => [
            'VIP申請',
            'VIP 申請',
            'Request To VIP'
        ],
        'help_request' => [
            'ボタンを押してVIP申請を送信してください。',
            '點擊按鈕送出 VIP 申請。',
            'Click to submit a VIP request.'
        ],

        // ---- Status ----
        'status_approved' => [
            'ステータス：承認済み',
            '狀態：已通過',
            'Status: Approved'
        ],
        'status_pending' => [
            'ステータス：審査中',
            '狀態：審核中',
            'Status: Pending'
        ],
        'status_rejected' => [
            'ステータス：却下',
            '狀態：已拒絕',
            'Status: Rejected'
        ],
        'help_approved' => [
            'すでにVIPです。',
            '您已是 VIP。',
            'You are already VIP.'
        ],
        'help_pending' => [
            '管理者の承認をお待ちください。',
            '請等待管理員審核。',
            'Please wait for admin approval.'
        ],
        'help_rejected' => [
            'この申請は却下されました。管理者に連絡するか、新しい招待を使用してください。',
            '此申請已被拒絕，請聯絡管理員或使用新的邀請。',
            'This request was rejected. Please contact admin or use a new invite.'
        ],

        // ---- Button ----
        'button_request' => [
            'VIPを申請する',
            '申請 VIP',
            'Request VIP'
        ],

		// ---- Button / Action ----
		'button_close_tab' => [
			'このタブを閉じる',
			'關閉此分頁',
			'Close this tab'
		],
		'help_close_tab' => [
			'この画面は不要になったら閉じてください。',
			'此畫面可直接關閉。',
			'You can close this tab.'
		],

    ];

    // shortcut
    $msg = function ($key) use ($arr_msg, $int_selected_language) {
        return $arr_msg[$key][$int_selected_language] ?? '';
    };

    // ==================================================
    // Login check
    // ==================================================
    $current_user_id = function_exists('get_current_user_id')
        ? intval(get_current_user_id())
        : 0;

    if ($current_user_id <= 0) {
        return '<p class="vipRequestNotice">' . escape_html($msg('login_required')) . '</p>';
    }

    // ==================================================
    // Token normalize
    // ==================================================
    $token = trim((string)$token);
    if ($token === '') {
        return '<p class="vipRequestNotice">' . escape_html($msg('token_required')) . '</p>';
    }

    if (!preg_match('/^[A-Za-z0-9\-_]+$/', $token)) {
        return '<p class="vipRequestNotice">' . escape_html($msg('invalid_token_format')) . '</p>';
    }

    $token_hash = hash('sha256', $token);

    // ==================================================
    // Fetch invite
    // ==================================================
    $invite = get_row_user_vip_invite_token_by_hash($token_hash, $int_selected_language);
    if (empty($invite)) {
        return '<p class="vipRequestNotice">' . escape_html($msg('invalid_or_expired_invite')) . '</p>';
    }

    $invite_id  = intval($invite['id'] ?? 0);
    $max_usage  = intval($invite['max_usage'] ?? 0);
    $used_count = intval($invite['used_count'] ?? 0);
    $expires_at = (string)($invite['expires_at'] ?? '');
    $is_revoked = intval($invite['is_revoked'] ?? 0);

    if ($is_revoked === 1) {
        return '<p class="vipRequestNotice">' . escape_html($msg('invite_disabled')) . '</p>';
    }

    if ($expires_at !== '' && strtotime($expires_at) < time()) {
        return '<p class="vipRequestNotice">' . escape_html($msg('invite_expired')) . '</p>';
    }

    if ($max_usage > 0 && $used_count >= $max_usage) {
        return '<p class="vipRequestNotice">' . escape_html($msg('invite_max_usage')) . '</p>';
    }

    // ==================================================
    // Existing request
    // ==================================================
    $existing_request = get_row_user_vip_request(
        $invite_id,
        $current_user_id,
        $int_selected_language
    );

    $status = '';
    if (!empty($existing_request)) {
        $status = (string)($existing_request['status'] ?? '');
    }

    $notice = '';

    // ==================================================
    // POST: create request
    // ==================================================
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $action = !empty($_POST['vip_request_action'])
            ? escape_html($_POST['vip_request_action'])
            : '';

        $post_token = !empty($_POST['token'])
            ? trim((string)escape_html($_POST['token']))
            : '';

        if ($action === 'request_vip' && hash_equals($token, $post_token)) {

            if (!empty($existing_request)) {
                $notice = $msg('already_requested');
            } else {

                $create_ok = create_user_vip_request(
                    $invite_id,
                    $current_user_id,
                    $int_selected_language
                );

                if ($create_ok === true) {
                    create_user_vip_invite_token_use_log(
                        $invite_id,
                        $current_user_id,
                        $int_selected_language
                    );

                    $notice = $msg('request_success');

                    $existing_request = get_row_user_vip_request(
                        $invite_id,
                        $current_user_id,
                        $int_selected_language
                    );
                    if (!empty($existing_request)) {
                        $status = (string)($existing_request['status'] ?? '');
                    }
                } else {
                    $notice = $msg('request_failed');
                }
            }
        }
    }

    // ==================================================
    // Build HTML
    // ==================================================
    $str_html  = '<div class="vipRequestPage">';
    $str_html .= '<h2 class="vipRequestTitle">' . escape_html($msg('page_title')) . '</h2>';

    if ($notice !== '') {
        $str_html .= '<p class="vipRequestNotice">' . escape_html($notice) . '</p>';
    }

    if ($status === 'approved') {
        $str_html .= '<p class="vipRequestStatus vipRequestStatusApproved">'
            . escape_html($msg('status_approved')) . '</p>';
        $str_html .= '<p class="vipRequestHelp">'
            . escape_html($msg('help_approved')) . '</p></div>';
        return $str_html;
    }

	if ($status === 'pending') {
		$str_html .= '<p class="vipRequestStatus vipRequestStatusPending">'
			. escape_html($msg('status_pending')) . '</p>';

		$str_html .= '<p class="vipRequestHelp">'
			. escape_html($msg('help_pending')) . '</p>';

		// ---- close tab button (handled by external JS) ----
		$str_html .= '<div class="vipRequestCloseArea">';
		// $str_html .= '<button type="button" class="vipRequestCloseButton" data-action="vip-close-tab">'
		// 	. escape_html($msg('button_close_tab')) . '</button>';
		$str_html .= '<p class="vipRequestCloseHelp">'
			. escape_html($msg('help_close_tab')) . '</p>';
		$str_html .= '</div>';

		$str_html .= '</div>';
		return $str_html;
	}


    if ($status === 'rejected') {
        $str_html .= '<p class="vipRequestStatus vipRequestStatusRejected">'
            . escape_html($msg('status_rejected')) . '</p>';
        $str_html .= '<p class="vipRequestHelp">'
            . escape_html($msg('help_rejected')) . '</p></div>';
        return $str_html;
    }

    // ---- not requested yet ----
    $str_html .= '<p class="vipRequestHelp">' . escape_html($msg('help_request')) . '</p>';
    $str_html .= '<form method="post" class="vipRequestForm">';
    $str_html .= '<input type="hidden" name="vip_request_action" value="request_vip">';
    $str_html .= '<input type="hidden" name="token" value="' . escape_html($token) . '">';
    $str_html .= '<button class="vipRequestButton" type="submit">'
        . escape_html($msg('button_request')) . '</button>';
    $str_html .= '</form></div>';

    return $str_html;
}


// ====== SELECT: invite token by hash ======
function get_row_user_vip_invite_token_by_hash($token_hash, $int_selected_language) {

    global
        $t_user_vip_invite_tokens;

    $arr_strSQL_select = [
        [$t_user_vip_invite_tokens, 'id'],
        [$t_user_vip_invite_tokens, 'max_usage'],
        [$t_user_vip_invite_tokens, 'used_count'],
        [$t_user_vip_invite_tokens, 'expires_at'],
        [$t_user_vip_invite_tokens, 'is_revoked']
    ];

    $strSQL_from = " FROM
                $t_user_vip_invite_tokens
                ";

    $arr_strSQL_where = [
        [
            [
                [$t_user_vip_invite_tokens, 'token_hash', '=', $token_hash, 'PDO::PARAM_STR', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [];
    $strSQL_option = ' LIMIT 1';

    list($pdo_has_error, $select_has_error, $e, $arr_rows) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );

    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    if (empty($arr_rows)) {
        return [];
    }

    return $arr_rows[0];
}


// ====== SELECT: existing request by (invite_id, user_id) ======
function get_row_user_vip_request($invite_id, $user_id, $int_selected_language) {

    global
        $t_user_vip_requests;

    $arr_strSQL_select = [
        [$t_user_vip_requests, 'id'],
        [$t_user_vip_requests, 'status'],
        [$t_user_vip_requests, 'requested_at'],
        [$t_user_vip_requests, 'approved_by_user_id'],
        [$t_user_vip_requests, 'approved_at']
    ];

    $strSQL_from = " FROM
                $t_user_vip_requests
                ";

    $arr_strSQL_where = [
        [
            [
                [$t_user_vip_requests, 'user_vip_invite_token_id', '=', $invite_id, 'PDO::PARAM_INT', 'And'],
                [$t_user_vip_requests, 'user_id', '=', $user_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [];
    $strSQL_option = ' LIMIT 1';

    list($pdo_has_error, $select_has_error, $e, $arr_rows) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );

    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    if (empty($arr_rows)) {
        return [];
    }

    return $arr_rows[0];
}


// ====== INSERT: create vip request ======
function create_user_vip_request($invite_id, $user_id, $int_selected_language) {

    global
        $t_user_vip_requests;

    $requested_at = date('Y-m-d H:i:s');

    $arr_insertSQL = [
        ['user_vip_invite_token_id', '?', $invite_id, 'PDO::PARAM_INT'],
        ['user_id', '?', $user_id, 'PDO::PARAM_INT'],
        ['status', '?', 'pending', 'PDO::PARAM_STR'],
        ['requested_at', '?', $requested_at, 'PDO::PARAM_STR'],
        ['approved_by_user_id', '?', null, 'PDO::PARAM_NULL'],
        ['approved_at', '?', null, 'PDO::PARAM_NULL']
    ];

    list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data($t_user_vip_requests, $arr_insertSQL);

    if ($pdo_has_error === FLAG_TRUE || $insert_has_error === FLAG_TRUE) {
        return false;
    }

    return true;
}


// ====== INSERT: token use log ======
function create_user_vip_invite_token_use_log($invite_id, $user_id, $int_selected_language) {

    global
        $t_user_vip_invite_token_uses;

    $ip_address = '';
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        $ip_address = (string)$_SERVER['REMOTE_ADDR'];
    }

    $user_agent = '';
    if (!empty($_SERVER['HTTP_USER_AGENT'])) {
        $user_agent = (string)$_SERVER['HTTP_USER_AGENT'];
    }

    $arr_insertSQL = [
        ['user_vip_invite_token_id', '?', $invite_id, 'PDO::PARAM_INT'],
        ['user_id', '?', $user_id, 'PDO::PARAM_INT'],
        ['used_at', '?', date('Y-m-d H:i:s'), 'PDO::PARAM_STR'],
        ['ip_address', '?', $ip_address, 'PDO::PARAM_STR'],
        ['user_agent', '?', mb_substr($user_agent, 0, 255, 'UTF-8'), 'PDO::PARAM_STR']
    ];

    // ログなので失敗しても致命ではない（戻り値は使わない）
    execute_insert_data($t_user_vip_invite_token_uses, $arr_insertSQL);

    return;
}



/******************************************************
 *  vip_requests
 *  
 ******************************************************/

// ====== Page Builder ======
function build_html_vip_requests_page($int_selected_language) {

    $user_level = get_user_level();
    if (!is_admin_level($user_level)) {
        return '';
    }

    $notice = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['vip_request_action'])) {
        $notice = handle_post_vip_requests_one_transaction($int_selected_language);
    }

    $arr_requests = get_arr_pending_vip_requests($int_selected_language);

    $str_html = '';
    $str_html .= '<div class="vipRequestsPage">';
    $str_html .= '<h2 class="vipRequestsTitle">VIP Requests</h2>';

    if ($notice !== '') {
        $str_html .= '<p class="vipRequestsNotice">' . escape_html($notice) . '</p>';
    }

    if (empty($arr_requests)) {
        $str_html .= '<p class="vipRequestsEmpty">No pending VIP requests.</p>';
        $str_html .= '</div>';
        return $str_html;
    }

    $str_html .= '<table class="vipRequestsTable">';
    $str_html .= '<thead>';
    $str_html .= '<tr>';
    $str_html .= '<th>Request ID</th>';
    $str_html .= '<th>User ID</th>';
    $str_html .= '<th>Invite ID</th>';
    $str_html .= '<th>Requested At</th>';
    $str_html .= '<th>Action</th>';
    $str_html .= '</tr>';
    $str_html .= '</thead>';

    $str_html .= '<tbody>';

    foreach ($arr_requests as $row) {

		$request_id   = intval($row['id'] ?? 0);
		$user_name    = escape_html($row['user_display_name'] ?? '');
		$user_id      = intval($row['user_id'] ?? 0);
		$invite_id    = intval($row['user_vip_invite_token_id'] ?? 0);
		$requested_at = escape_html($row['requested_at'] ?? '');

		$str_html .= '<tr>';

		// Request ID
		$str_html .= '<td>' . $request_id . '</td>';

		// User display name（ここを追加）
		$str_html .= '<td>' . $user_name . '</td>';

		// Invite ID
		$str_html .= '<td>' . $invite_id . '</td>';

		// Requested at
		$str_html .= '<td>' . $requested_at . '</td>';

		// Actions
		$str_html .= '<td class="vipRequestsActions">';

		$str_html .= '<form method="post" class="vipRequestsActionForm" style="display:inline;">';
		$str_html .= '<input type="hidden" name="vip_request_action" value="approve">';
		$str_html .= '<input type="hidden" name="request_id" value="' . $request_id . '">';
		$str_html .= '<button type="submit" class="vipRequestsApproveButton">Approve</button>';
		$str_html .= '</form>';

		$str_html .= '<form method="post" class="vipRequestsActionForm" style="display:inline;margin-left:8px;">';
		$str_html .= '<input type="hidden" name="vip_request_action" value="reject">';
		$str_html .= '<input type="hidden" name="request_id" value="' . $request_id . '">';
		$str_html .= '<button type="submit" class="vipRequestsRejectButton">Reject</button>';
		$str_html .= '</form>';

		$str_html .= '</td>';
		$str_html .= '</tr>';
	}


    $str_html .= '</tbody>';
    $str_html .= '</table>';

    $str_html .= '</div>';

    return $str_html;
}


// ====== One-Transaction POST Handler (Approve / Reject) ======
function handle_post_vip_requests_one_transaction($int_selected_language) {

    global
        $t_user_vip_requests,
        $t_user_vip_invite_tokens,
        $t_user_membership;

    $user_level = get_user_level();
    if (!is_admin_level($user_level)) {
        return 'Permission denied.';
    }

    $action = escape_html($_POST['vip_request_action'] ?? '');
    $request_id = intval($_POST['request_id'] ?? 0);

    if ($request_id <= 0) {
        return 'Invalid request id.';
    }

    $admin_user_id = 0;
    if (function_exists('get_current_user_id')) {
        $admin_user_id = intval(get_current_user_id());
    }

    $int_VIP_Student = 4;
    $now = date('Y-m-d H:i:s');

    $pdo = connect_to_database();
    if (empty($pdo)) {
        return 'Database connection failed.';
    }

    try {

        $pdo->beginTransaction();

        // 1) Lock request row
        $sql_lock_request = "
            SELECT
                id,
                user_id,
                user_vip_invite_token_id,
                status
            FROM
                $t_user_vip_requests
            WHERE
                id = ?
            FOR UPDATE
        ";
        $stmt_lock_request = $pdo->prepare($sql_lock_request);
        $stmt_lock_request->bindValue(1, $request_id, PDO::PARAM_INT);
        $stmt_lock_request->execute();
        $req = $stmt_lock_request->fetch(PDO::FETCH_ASSOC);

        if (empty($req)) {
            throw new Exception('Request not found.');
        }

        $current_status = (string)($req['status'] ?? '');
        if ($current_status !== 'pending') {
            throw new Exception('Request is not pending.');
        }

        $target_user_id = intval($req['user_id'] ?? 0);
        $invite_id = intval($req['user_vip_invite_token_id'] ?? 0);

        if ($target_user_id <= 0 || $invite_id <= 0) {
            throw new Exception('Invalid request data.');
        }

        if ($action === 'reject') {

            $sql_reject = "
                UPDATE $t_user_vip_requests
                SET
                    status = 'rejected',
                    approved_by_user_id = ?,
                    approved_at = ?
                WHERE
                    id = ?
            ";
            $stmt_reject = $pdo->prepare($sql_reject);
            $stmt_reject->bindValue(1, $admin_user_id, PDO::PARAM_INT);
            $stmt_reject->bindValue(2, $now, PDO::PARAM_STR);
            $stmt_reject->bindValue(3, $request_id, PDO::PARAM_INT);
            $stmt_reject->execute();

            $pdo->commit();
            $pdo = null;

            return 'Rejected.';
        }

        if ($action !== 'approve') {
            throw new Exception('Invalid action.');
        }

        // 2) Lock invite row and validate
        $sql_lock_invite = "
            SELECT
                id,
                max_usage,
                used_count,
                expires_at,
                is_revoked
            FROM
                $t_user_vip_invite_tokens
            WHERE
                id = ?
            FOR UPDATE
        ";
        $stmt_lock_invite = $pdo->prepare($sql_lock_invite);
        $stmt_lock_invite->bindValue(1, $invite_id, PDO::PARAM_INT);
        $stmt_lock_invite->execute();
        $inv = $stmt_lock_invite->fetch(PDO::FETCH_ASSOC);

        if (empty($inv)) {
            throw new Exception('Invite not found.');
        }

        if (intval($inv['is_revoked'] ?? 0) === 1) {
            throw new Exception('Invite is revoked.');
        }

        $expires_at = (string)($inv['expires_at'] ?? '');
        if (!empty($expires_at) && strtotime($expires_at) < time()) {
            throw new Exception('Invite is expired.');
        }

        $max_usage = intval($inv['max_usage'] ?? 0);
        $used_count = intval($inv['used_count'] ?? 0);
        if ($max_usage > 0 && $used_count >= $max_usage) {
            throw new Exception('Invite has reached maximum usage.');
        }

        // 3) Update request -> approved
        $sql_approve_request = "
            UPDATE $t_user_vip_requests
            SET
                status = 'approved',
                approved_by_user_id = ?,
                approved_at = ?
            WHERE
                id = ?
        ";
        $stmt_approve_request = $pdo->prepare($sql_approve_request);
        $stmt_approve_request->bindValue(1, $admin_user_id, PDO::PARAM_INT);
        $stmt_approve_request->bindValue(2, $now, PDO::PARAM_STR);
        $stmt_approve_request->bindValue(3, $request_id, PDO::PARAM_INT);
        $stmt_approve_request->execute();

        // 4) Update membership -> VIP (only if current level < VIP)
        // Safety: prevents overwriting admin/teacher levels.
        $sql_update_membership = "
            UPDATE $t_user_membership
            SET
                level = :vip_level,
                last_upgrade_date = :last_upgrade_date,
                updated_at = CURRENT_TIMESTAMP
            WHERE
                user_id = :user_id
                AND level < :vip_level
        ";
        $stmt_update_membership = $pdo->prepare($sql_update_membership);
        $stmt_update_membership->bindValue(':vip_level', $int_VIP_Student, PDO::PARAM_INT);
        $stmt_update_membership->bindValue(':last_upgrade_date', $now, PDO::PARAM_STR);
        $stmt_update_membership->bindValue(':user_id', $target_user_id, PDO::PARAM_INT);
        $stmt_update_membership->execute();

        // membershipが存在しないケースは異常なので止める
        // ただし「すでにVIP以上（例: 教師/管理者）」の場合は rowCount=0 になるので、そのときは止めない
        $sql_check_membership = "
            SELECT level
            FROM $t_user_membership
            WHERE user_id = ?
            FOR UPDATE
        ";
        $stmt_check_membership = $pdo->prepare($sql_check_membership);
        $stmt_check_membership->bindValue(1, $target_user_id, PDO::PARAM_INT);
        $stmt_check_membership->execute();
        $membership = $stmt_check_membership->fetch(PDO::FETCH_ASSOC);

        if (empty($membership)) {
            throw new Exception('Membership not found for user.');
        }

        // 5) used_count + 1
        $sql_inc_used = "
            UPDATE $t_user_vip_invite_tokens
            SET
                used_count = used_count + 1
            WHERE
                id = ?
        ";
        $stmt_inc_used = $pdo->prepare($sql_inc_used);
        $stmt_inc_used->bindValue(1, $invite_id, PDO::PARAM_INT);
        $stmt_inc_used->execute();

        $pdo->commit();
        $pdo = null;

        return 'Approved.';

    } catch (Exception $exception) {

        if (!empty($pdo) && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $pdo = null;

        return 'Failed: ' . $exception->getMessage();
    }
}


// ====== SELECT Pending Requests (execute_select_and_fetch_all style) ======
function get_arr_pending_vip_requests($int_selected_language) {

    global
		$t_user_vip_requests,
		$t_users;

    $arr_strSQL_select = [
		[$t_user_vip_requests, 'id'],
		[$t_user_vip_requests, 'user_id'],
		[$t_users, 'display_name AS user_display_name'],
		[$t_user_vip_requests, 'user_vip_invite_token_id'],
		[$t_user_vip_requests, 'requested_at']
	];


    $strSQL_from = "
					FROM $t_user_vip_requests
					INNER JOIN $t_users
						ON $t_user_vip_requests.user_id = $t_users.ID
				";


    $arr_strSQL_where = [
        [
            [
                [$t_user_vip_requests, 'status', '=', 'pending', 'PDO::PARAM_STR', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_user_vip_requests, 'requested_at', 'ASC']
    ];

    $strSQL_option = ' LIMIT 200';

    list($pdo_has_error, $select_has_error, $e, $arr_rows) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );

    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    return $arr_rows ?? [];
}





/******************************************************
 *  select_grammar
 *  
 ******************************************************/
/******************************************************
 *  PAGE
 *  
 ******************************************************/
function build_html_select_grammar_level_page($int_selected_language){

	global
		$arr_mastery_level,
		$int_mastery_level_select_all,
		$arr_str_button_caption_to_grammar,
		$path_select_grammar,
		$int_color_blue,
		$int_rgb_r_deep,
		$int_rgb_g_deep,
		$int_rgb_b_deep;


	$user_level = get_user_level();
	if(!is_admin_level($user_level)){
		return;
	}
	
	$str_html = '';

	$arr_grammar_level = $arr_mastery_level;
	unset($arr_grammar_level[$int_mastery_level_select_all]);

	$url_select_grammar = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_select_grammar, '/'))
	);

	foreach($arr_grammar_level as $key => $item){

		$str_heading = $item[$int_selected_language];
		$str_heading = '<h3>' . $str_heading . '</h3>';

		$button_id = '';
		$button_text = $arr_str_button_caption_to_grammar[$int_selected_language];

		$div_class = 'divChoices';
		$form_class = '';
		$input_class = 'inputChoices';
		$hidden_class = '';
		$button_color = $int_color_blue;
		$button_background_color = "rgb( $int_rgb_r_deep[$button_color], $int_rgb_g_deep[$button_color], $int_rgb_b_deep[$button_color]);";
		$new_tab = false;
		$send_method = 'GET';

		// 未定義id
		$arr_request_contents = [
			[
				'class' => $hidden_class,
				'name' => 'level',
				'value' => $key
			],
			[
				'class' => $hidden_class,
				'name' => 'subCategory',
				'value' => 0
			]
		];

		$add_choices = build_html_choice($button_id, $button_text, $arr_request_contents, $url_select_grammar, $div_class, $form_class, $input_class, $button_background_color, $new_tab, $send_method, $int_selected_language);

		$str_section = '<section class="sectionStandard">' . ($str_heading . $add_choices) . '</section>';
		$str_html .= $str_section;
	}

	return $str_html;
}


function build_html_select_grammar_page($int_selected_language){

	global
		$path_select_grammar_level,
		$int_mastery_level_jws_beginner;


	$user_level = get_user_level();
	if(!is_admin_level($user_level)){
		return;
	}

	$request_method = 'GET';
	validate_request_method($request_method, $int_selected_language);

	
	$level = $int_mastery_level_jws_beginner;
	if (isset($_GET['level'])) {
		$level = intval($_GET['level']);
	}
	validate_integer_or_redirect($level, $int_selected_language);

	$showSentences = FLAG_TRUE;
	if (isset($_GET['showSentences'])) {
		$showSentences = intval($_GET['showSentences']);
	}

	$showLayers = FLAG_FALSE;
	if (isset($_GET['showLayers'])) {
		$showLayers = intval($_GET['showLayers']);
	}

	// 未定義id
	$subCategory = 0;
	if (isset($_GET['subCategory'])) {
		$subCategory = intval($_GET['subCategory']);
	}

	$str_html = build_html_grammar_list_from_level($level, $showSentences, $showLayers, $subCategory, $int_selected_language);

	return $str_html;
}

/******************************************************
 *  HTML
 *  
 ******************************************************/
function build_html_grammar_list_from_level($level, $showSentences, $showLayers, $subCategory, $int_selected_language){

	global
		$t_masta_japanese_root,
		$str_snake_to_camel_japanese_id,
		$str_snake_to_camel_unique_code,
		$arr_columns_masta_japanese_root,
		$int_used_language_jpn,
		$str_column_root_kana,
		$t_masta_japanese_sub_category,
		$str_snake_to_camel_category_id,
		$int_masta_japanese_category_id_grammar;


	$arr_strSQL_select = [
		[$t_masta_japanese_root,'id as '.$str_snake_to_camel_japanese_id],
		[$t_masta_japanese_root,'unique_code as '.$str_snake_to_camel_unique_code],
		[$t_masta_japanese_root,'root_example'],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_used_language_jpn]],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]],
		[$t_masta_japanese_root,$str_column_root_kana],
		[$t_masta_japanese_root,'sub_category_id'],
		[$t_masta_japanese_sub_category,'category_id as '.$str_snake_to_camel_category_id]
	];

	$strSQL_from = " FROM
				$t_masta_japanese_root
				INNER JOIN $t_masta_japanese_sub_category
				ON
				$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
				";

	$arr_strSQL_where = [
		[
			[
				[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_grammar,'PDO::PARAM_INT','And'],
				[$t_masta_japanese_root,'jws_level','=',$level,'PDO::PARAM_INT','']
			],
			''
		]
	];
	
	// 未定義id
	if(intval($subCategory) !== 0){
		$arr_strSQL_where = [
		[
			[
				[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_grammar,'PDO::PARAM_INT','And'],
				[$t_masta_japanese_sub_category,'id','=',$subCategory,'PDO::PARAM_INT','And'],
				[$t_masta_japanese_root,'jws_level','=',$level,'PDO::PARAM_INT','']
			],
			''
		]
	];
	}


	$arr_strSQL_order = [
		[$t_masta_japanese_root,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	$arr_str_select_grammar_title = ['文法','文法'];
	$arr_target = ['t_masta_japanese_root_title',$arr_str_select_grammar_title[$int_selected_language],'t_masta_japanese_root_root'];

	return build_html_grammar_list($showSentences, $showLayers, $arr_masta_japanese_root, $arr_target, $int_selected_language);

}


function build_html_grammar_list($showSentences, $showLayers, $arr_masta_japanese_root, $arr_target, $int_selected_language){

	global
		$str_snake_to_camel_japanese_id,
		$str_snake_to_camel_unique_code,
		$str_avoid_null_proxy,
		$arr_columns_masta_japanese_root,
		$int_used_language_jpn,
		$str_snake_to_camel_category_id,
		$t_layers,
		$int_masta_japanese_category_id_grammar;


	$str_html = '';

	foreach($arr_masta_japanese_root as $index => $loop_masta_japanese_root){

		$str_html_add = '';

		$t_masta_japanese_root_id = $loop_masta_japanese_root[$str_snake_to_camel_japanese_id];
		$unique_code = $loop_masta_japanese_root[$str_snake_to_camel_unique_code];
		$t_masta_japanese_root_title = $loop_masta_japanese_root['root_example'];
		$int_sub_category_id = $loop_masta_japanese_root['sub_category_id'];

		if($t_masta_japanese_root_title === $str_avoid_null_proxy){
			$t_masta_japanese_root_title = $loop_masta_japanese_root[$arr_columns_masta_japanese_root[$int_selected_language]];
		}

		$t_masta_japanese_root_title = apply_text_for_output($t_masta_japanese_root_title);
		$t_masta_japanese_root_root = $loop_masta_japanese_root[$arr_columns_masta_japanese_root[$int_selected_language]];
		$t_masta_japanese_root_original = $loop_masta_japanese_root[$arr_columns_masta_japanese_root[$int_used_language_jpn]];
		$t_japanese_category_id = $loop_masta_japanese_root[$str_snake_to_camel_category_id];

		$user_level = get_user_level();
		if(is_admin_level($user_level)){

			$arr_registered_sentence = fetch_arr_registered_sentence_by_root_id($t_masta_japanese_root_id, $int_selected_language);

			$min_count_sentences = 5;

			if(
				!$showSentences &&
				count($arr_registered_sentence) >= $min_count_sentences &&
				$showLayers === FLAG_FALSE
			){
				continue;
			}

			$t_masta_japanese_root_title = $t_masta_japanese_root_title.' 例文:'.count($arr_registered_sentence);
			
			$count_registered_layer = COUNT_EMPTY;
			foreach($arr_registered_sentence as $loop_registered_sentence){
				
				$arr_strSQL_select = [
					[$t_layers,'id']
				];
	
				$strSQL_from = ' FROM ' .$t_layers;
				
				$arr_strSQL_where = [
					[
						[
							[$t_layers,'registered_sentence_id','=',$loop_registered_sentence['id'],'PDO::PARAM_INT','']
						],
						''
					]
				];
	
				$arr_strSQL_order = [];
	
				$strSQL_option = '';
	
				list($pdo_has_error, $select_has_error, $e, $arr_layers) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
				handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

				if(empty($arr_layers)){continue;}
				++$count_registered_layer;
			}

			if($count_registered_layer >= 1 && $showLayers === FLAG_TRUE){
				continue;
			}

			// if(count($arr_registered_sentence) == $count_registered_layer && $showLayers === FLAG_TRUE){
			// 	continue;
			// }
			
			$t_masta_japanese_root_title = $t_masta_japanese_root_title.' レイヤー:'.$count_registered_layer;

		}

		$str_contents_choices = build_html_contents_frame_button_to_grammar($unique_code, $int_selected_language);

		$str_contents_heading = '<h5>'.$arr_target[INDEX_SECOND].'</h5><p>'.${$arr_target[INDEX_THIRD]}.'</p>';
		$str_contents_frame = $str_contents_heading.$str_contents_choices;

		$class = 'frame frameDeepBlue';
		$caution_class = 'frameTitle cautionLightBlue';
		$str_contents_frame = build_html_div_frame($class, '', $str_contents_frame, $caution_class);

		$details_class = 'grammarListDetails animationSlideIn';

		if($t_japanese_category_id == $int_masta_japanese_category_id_grammar){
			$summary_class = 'grammarListDetailsSummarys heading tag colorPurpleBlack withBackgroundColor rounded headingSummary';
		}
		else{
			$summary_class = 'grammarListDetailsSummarys heading tag colorNothing withBackgroundColor rounded headingSummary';
		}

		$details_div_class = 'detailsDiv animationSlideIn';
		$str_html_add = build_html_details_contents($str_contents_frame, ${$arr_target[INDEX_FIRST]}, $details_class, $summary_class, $details_div_class);

		$str_html_add = '<div class="divBasicChoices">' . $str_html_add . '</div>';
		$str_html .= $str_html_add;
	}
	return $str_html;
}


function build_html_contents_frame_button_to_grammar($unique_code, $int_selected_language){

	global
		$path_grammar_view_for_teachers,
		$int_color_blue,
		$int_rgb_r_deep,
		$int_rgb_g_deep,
		$int_rgb_b_deep;

	$button_id = '';

	$url_grammar_view_for_teachers = get_home_url(
    get_data_blog_id_from_selected_language($int_selected_language ?? null),
    trailingslashit(ltrim($path_grammar_view_for_teachers, '/'))
);
	
	$arr_str_button_caption_to_grammar = [
		'要点チェック',
		'檢查要點'
	];
	$button_text = $arr_str_button_caption_to_grammar[$int_selected_language];

	$div_class = 'divChoices';
	$form_class = '';
	$input_class = 'inputChoices';
	$hidden_class = '';
	$button_color = $int_color_blue;
	$button_background_color = "rgb( $int_rgb_r_deep[$button_color], $int_rgb_g_deep[$button_color], $int_rgb_b_deep[$button_color]);";
	$new_tab = true;
	$send_method = 'GET';

	$arr_request_contents = [
		[
			'class' => $hidden_class,
			'name' => 'grammar_unique_code',
			'value' => $unique_code
		]
	];

	$str_contents_choices = build_html_choice($button_id, $button_text, $arr_request_contents, $url_grammar_view_for_teachers, $div_class, $form_class, $input_class, $button_background_color, $new_tab, $send_method, $int_selected_language);

	return $str_contents_choices;
}