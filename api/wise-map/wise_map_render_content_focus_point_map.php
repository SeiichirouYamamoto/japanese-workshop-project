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

        if (!isset($input['goal_unique_code'])) { respond_error('Value not found: goal_unique_code', 400); }
        if (!isset($input['added_unique_codes']) || !is_array($input['added_unique_codes'])) { respond_error('Value not found: added_unique_codes', 400); }
        if (!isset($input['waypoints']) || !is_array($input['waypoints'])) { respond_error('Value not found: waypoints', 400); }
        if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }

        $t_registered_sentence_unique_code = escape_html((string)($input['goal_unique_code'] ?? ''));

        $t_layer_unique_codes_for_highlight = array_map('escape_html', $input['added_unique_codes'] ?? []);
        $arr_layer_unique_codes = array_map('escape_html', $input['waypoints'] ?? []);

        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        $result = get_data_wise_map_sentence_from_waypoints(
            $arr_layer_unique_codes,
            $t_layer_unique_codes_for_highlight,
            $int_selected_language
        );

        respond_success($result);

    } catch (Throwable $e) {
        respond_exception($e, 'wise_map_sentence_from_waypoints_unhandled');
    }

