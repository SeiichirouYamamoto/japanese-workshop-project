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

        if (!isset($input['waypoint_index'])) {
            respond_error('Value not found: waypoint_index', 400);
        }

        $unique_code = escape_html((string)$input['unique_code']);
        $int_selected_language = intval($input['int_selected_language']);
        $waypoint_index = intval($input['waypoint_index']);

        if ($unique_code === '') {
            respond_error('Invalid value: unique_code', 400);
        }

        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        if ($waypoint_index < 0) {
            respond_error('Invalid value: waypoint_index', 400);
        }

        $t_wise_navigation_id = fetch_wise_navigation_id_from_unique_code(
            $unique_code,
            $int_selected_language
        );

        if ($t_wise_navigation_id === null || $t_wise_navigation_id <= 0) {
            respond_error('Target not found: wise_navigation', 404);
        }

        $arr_wise_navigation_waypoints = get_arr_wise_navigation_waypoints(
            $t_wise_navigation_id,
            $int_selected_language
        );

        if (!is_array($arr_wise_navigation_waypoints) || count($arr_wise_navigation_waypoints) === 0) {
            respond_error('No waypoints found', 404);
        }

        if (!isset($arr_wise_navigation_waypoints[$waypoint_index])) {
            respond_error('Invalid value: waypoint_index', 400);
        }

        $waypoint_id = intval($arr_wise_navigation_waypoints[$waypoint_index]['id'] ?? 0);
        if ($waypoint_id <= 0) {
            respond_error('Invalid waypoint data', 500);
        }

        $arr_wise_navigation_scripts = get_arr_wise_navigation_scripts(
            $waypoint_id,
            $int_selected_language
        );

        $scripts_length = is_array($arr_wise_navigation_scripts) ? count($arr_wise_navigation_scripts) : 0;

        $result = [];
        $extra = [
            'scripts_length' => $scripts_length
        ];

        respond_success($result, $extra);

    } catch (Throwable $e) {
        respond_exception($e, 'wise_navigation_scripts_length_unhandled');
    }

