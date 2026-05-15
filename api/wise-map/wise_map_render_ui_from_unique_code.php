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

        if (!isset($input['sentence_unique_code'])) {
            respond_error('Value not found: sentence_unique_code', 400);
        }

        if (!isset($input['int_selected_language'])) {
            respond_error('Value not found: int_selected_language', 400);
        }

        if (!isset($input['map_type'])) {
            respond_error('Value not found: map_type', 400);
        }

        $sentence_unique_code = escape_html((string)$input['sentence_unique_code']);
        if ($sentence_unique_code === '') {
            respond_error('Invalid value: sentence_unique_code', 400);
        }

        $int_selected_language = intval($input['int_selected_language']);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        // map_type は build_wise_map_view 側に委ねる
        $map_type = escape_html((string)$input['map_type']);

        // 未定（将来 user_id を使う場合に備えて変数は保持）
        $user_id = null;

        $target_id = fetch_registered_sentence_id_from_unique_code(
            $sentence_unique_code,
            $int_selected_language
        );

        if ($target_id === null) {
            respond_error('Target not found', 404);
        }

        $target_ids = [];
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
        respond_exception($e, 'wise_map_view_direct_unhandled');
    }

