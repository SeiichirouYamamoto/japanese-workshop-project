<?php

    header('Content-Type: application/json; charset=utf-8');

    require_once __DIR__ . '/../_bootstrap.php';

    try {

        $user_level = get_user_level();
	
		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		$user_id = jws_require_single_session();

		if (!is_teacher_level($user_level)) {
            respond_error('Forbidden', 403);
        }

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);

        if (!is_array($input)) {
            respond_error('Invalid JSON', 400);
        }

        if (!isset($input['target_ids']) || !is_array($input['target_ids']) || count($input['target_ids']) === 0) {
            respond_error('Value not found or invalid: target_ids', 400);
        }

        if (!isset($input['int_selected_language'])) {
            respond_error('Value not found: int_selected_language', 400);
        }

        if (!isset($input['map_type'])) {
            respond_error('Value not found: map_type', 400);
        }

        $target_ids = array_values(array_unique(array_filter(array_map('intval', $input['target_ids']), function ($v) {
            return $v > 0;
        })));

        if (count($target_ids) === 0) {
            respond_error('Value not found or invalid: target_ids', 400);
        }

        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        $map_type = escape_html((string)($input['map_type'] ?? ''));

        // 未定（必要になったら受け取り＆バリデーションを追加）
        $user_id = null;
        $target_id = null;
        $is_direct = false;

        $result = get_data_wise_map_view(
            $int_selected_language,
            $user_id,
            $map_type,
            $target_id,
            $target_ids,
            $is_direct
        );

        respond_success($result);

    } catch (Throwable $e) {
        respond_exception($e, 'wise_map_view_unhandled');
    }

