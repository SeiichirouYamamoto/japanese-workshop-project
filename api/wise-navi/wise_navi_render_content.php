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
        if (!isset($input['wise_navi_waypoint_index'])) {
            respond_error('Value not found: wise_navi_waypoint_index', 400);
        }
        if (!isset($input['wise_navi_script_index'])) {
            respond_error('Value not found: wise_navi_script_index', 400);
        }
        if (!isset($input['int_selected_language'])) {
            respond_error('Value not found: int_selected_language', 400);
        }

        $unique_code = escape_html((string)$input['unique_code']);
        $wise_navi_waypoint_index = intval($input['wise_navi_waypoint_index']);
        $wise_navi_script_index = intval($input['wise_navi_script_index']);
        $int_selected_language = intval($input['int_selected_language']);

        if ($unique_code === '') {
            respond_error('Invalid value: unique_code', 400);
        }
        if ($wise_navi_waypoint_index < 0) {
            respond_error('Invalid value: wise_navi_waypoint_index', 400);
        }
        if ($wise_navi_script_index < 0) {
            respond_error('Invalid value: wise_navi_script_index', 400);
        }
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
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

        $t_registered_sentence_id = intval($arr_registered_sentences[INDEX_FIRST]['id']);

        $arr_wise_navigation_waypoints = get_arr_wise_navigation_waypoints(
            $t_wise_navigation_id,
            $int_selected_language
        );

        if (!is_array($arr_wise_navigation_waypoints) || count($arr_wise_navigation_waypoints) === 0) {
            respond_error('No waypoints found', 404);
        }

        if (!isset($arr_wise_navigation_waypoints[$wise_navi_waypoint_index])) {
            respond_error('Invalid value: wise_navi_waypoint_index', 400);
        }

        $waypoint_id = intval($arr_wise_navigation_waypoints[$wise_navi_waypoint_index]['id']);

        $arr_wise_navigation_scripts = get_arr_wise_navigation_scripts(
            $waypoint_id,
            $int_selected_language
        );

        if (!is_array($arr_wise_navigation_scripts) || count($arr_wise_navigation_scripts) === 0) {
            respond_success([]);
        }

        if (!isset($arr_wise_navigation_scripts[$wise_navi_script_index])) {
            respond_success([]);
        }

        $script_row = $arr_wise_navigation_scripts[$wise_navi_script_index];
        $script_id = intval($script_row['id']);
        $script_type_id = intval($script_row['script_type_id']);
        $script_message = (string)($script_row['script_message'] ?? '');
        $script_message_template = (string)($script_row['script_message_template'] ?? '');
        $item_japanese = '';

        $result = [
            'message' => $script_message,
            'script_type_id' => $script_type_id,
            'item_title' => '',
            'item_unique_code' => '',
            'item_japanese' => ''
        ];

        switch ($script_type_id) {

            case $int_masta_script_type_id_layer_for_goal:
            case $int_masta_script_type_id_layer_for_content:
            case $int_masta_script_type_id_message_explanation_for_particle:
            case $int_masta_script_type_id_message_explanation_for_grammar:
            case $int_masta_script_type_id_message_explanation_for_inflection:
            case $int_masta_script_type_id_message_explanation_for_sentence:
            case $int_masta_script_type_id_reset_goal:

                $arr_wise_navigation_items = get_arr_wise_navigation_items(
                    $script_id,
                    $int_selected_language
                );

                if (is_array($arr_wise_navigation_items) && count($arr_wise_navigation_items) > 0) {

                    $arr_layer_unique_codes = [];
                    $t_layer_unique_codes_for_highlight = [];

                    foreach ($arr_wise_navigation_items as $it) {
                        $layer_id = intval($it['layer_id']);
                        if ($layer_id <= 0) continue;

                        $layer_uc = fetch_unique_code_from_layer_id($layer_id, $int_selected_language);
                        if ($layer_uc === null || $layer_uc === '') continue;

                        $arr_layer_unique_codes[] = $layer_uc;

                        if (isset($it['is_new']) && intval($it['is_new']) === 1) {
                            $t_layer_unique_codes_for_highlight[] = $layer_uc;
                        }
                    }

                    if (count($arr_layer_unique_codes) > 0) {
                        $sentence_result = get_data_wise_map_sentence_from_waypoints(
                            $arr_layer_unique_codes,
                            $t_layer_unique_codes_for_highlight,
                            $int_selected_language
                        );

                        if (is_array($sentence_result)) {
                            $result['item_title'] = (string)($sentence_result['item_title'] ?? '');
                            $result['item_unique_code'] = (string)($sentence_result['item_unique_code'] ?? '');
                            $item_japanese = (string)($sentence_result['item_japanese'] ?? '');
                            $result['item_japanese'] = $item_japanese;
                        }
                    }

                    if (
                        $script_type_id === $int_masta_script_type_id_message_explanation_for_particle ||
                        $script_type_id === $int_masta_script_type_id_message_explanation_for_grammar ||
                        $script_type_id === $int_masta_script_type_id_message_explanation_for_inflection ||
                        $script_type_id === $int_masta_script_type_id_message_explanation_for_sentence
                    ) {
                        $result['message'] = generate_wise_navigation_message_from_script_type(
                            $script_type_id,
                            $script_message,
                            $script_message_template,
                            $script_id,
                            $item_japanese,
                            $int_selected_language,
                            $arr_wise_navigation_scripts
                        );
                    }
                }
                break;

            default:
                $result['message'] = generate_wise_navigation_message_from_script_type(
                    $script_type_id,
                    $script_message,
                    $script_message_template,
                    $script_id,
                    $item_japanese,
                    $int_selected_language,
                    $arr_wise_navigation_scripts
                );
                break;
        }

        respond_success($result);

    } catch (Throwable $e) {
        respond_exception($e, 'wise_navigation_script_detail_unhandled');
    }

