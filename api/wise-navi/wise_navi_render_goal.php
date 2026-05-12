<?php

    header('Content-Type: application/json; charset=utf-8');

    require_once __DIR__ . '/../_bootstrap.php';

    try {

		$user_level = get_user_level();
	
		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		$user_id = jws_require_single_session();

		if (!is_admin_level($user_level)) {
			respond_error('Forbidden', 403);
		}

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);

        if (!is_array($input)) {
            respond_error('Invalid JSON', 400);
        }

        if (!isset($input['unique_code'])) {
            respond_error('Value not found: unique_code', 400);
        }

        if (!isset($input['int_selected_language'])) {
            respond_error('Value not found: int_selected_language', 400);
        }

        if (!isset($input['map_type'])) {
            respond_error('Value not found: map_type', 400);
        }

        $unique_code = escape_html((string)$input['unique_code']);
        $int_selected_language = intval($input['int_selected_language']);
        $map_type = escape_html((string)$input['map_type']);

        if ($unique_code === '') {
            respond_error('Invalid value: unique_code', 400);
        }

        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        if ($map_type === '') {
            respond_error('Invalid value: map_type', 400);
        }

        $t_wise_navigation_id = fetch_wise_navigation_id_from_unique_code(
            $unique_code,
            $int_selected_language
        );

        if (!is_numeric($t_wise_navigation_id) || intval($t_wise_navigation_id) <= 0) {
            respond_error('Invalid navigation unique_code', 404);
        }
        $t_wise_navigation_id = intval($t_wise_navigation_id);

        $arr_registered_sentences = fetch_arr_registered_sentences_from_wise_navigation_id(
            $t_wise_navigation_id,
            $int_selected_language
        );

        if (!is_array($arr_registered_sentences) || count($arr_registered_sentences) === 0) {
            respond_error('Value not found: array', 404);
        }

        $t_registered_sentence_id = intval($arr_registered_sentences[INDEX_FIRST]['id'] ?? 0);
        if ($t_registered_sentence_id <= 0) {
            respond_error('Invalid registered sentence data', 500);
        }

        // 未定（ログイン対応等をする場合は後で user_id を変更）
        $user_id = null;
        $target_ids = [];
        $is_direct = false;

        $result = get_data_wise_map_view(
            $int_selected_language,
            $user_id,
            $map_type,
            $t_registered_sentence_id,
            $target_ids,
            $is_direct
        );

        $arr_wise_navigation_waypoints = get_arr_wise_navigation_waypoints(
            $t_wise_navigation_id,
            $int_selected_language
        );

        $wise_navi_sequence_length = is_array($arr_wise_navigation_waypoints) ? count($arr_wise_navigation_waypoints) : 0;

        $extra = [
            'length' => $wise_navi_sequence_length
        ];

        respond_success($result, $extra);

    } catch (Throwable $e) {
        respond_exception($e, 'wise_map_view_and_sequence_length_unhandled');
    }

