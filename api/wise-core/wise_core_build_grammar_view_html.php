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

        if (!isset($input['int_selected_language'])) {
            respond_error('Value not found: int_selected_language', 400);
        }
        if (!isset($input['grammar_unique_code'])) {
            respond_error('Value not found: grammar_unique_code', 400);
        }
        if (!isset($input['arr_targets_visible_from_urlParams']) || !is_array($input['arr_targets_visible_from_urlParams'])) {
            respond_error('Value not found: arr_targets_visible_from_urlParams', 400);
        }

        $int_selected_language = intval($input['int_selected_language']);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        $grammar_unique_code = trim((string)($input['grammar_unique_code'] ?? ''));
        if ($grammar_unique_code === '') {
            respond_error('Invalid value: grammar_unique_code', 400);
        }
        $grammar_unique_code = escape_html($grammar_unique_code);

		$room_unique_code = escape_html($_SESSION['wise']['room_unique_code'] ?? '');

        $arr_targets_visible = $allow_grammar_view_feature_capabilities_default[$user_level] ?? [];
        $allow_grammar_view_content_section_capabilities = $allow_grammar_view_content_section_capabilities_default[$user_level] ?? [];
        $arr_targets_visible['allow_grammar_view_content_section_capabilities'] = $allow_grammar_view_content_section_capabilities;

        $deny_for_role = [];

        if (is_admin_level($user_level)) {
            $deny_for_role = [];
        } elseif (is_teacher_level($user_level)) {
            $deny_for_role = ['recording_shorts', 'recording_video'];
        } else {
            $deny_for_role = ['listed_location_visible', 'user_input_data_visible', 'recording_shorts', 'recording_video'];
        }

        $effective_override_keys = array_values(array_diff($arr_allow_visible_override_keys, $deny_for_role));

        $input_flags = $input['arr_targets_visible_from_urlParams'] ?? [];

        $normalized_overrides = [];

        foreach ($effective_override_keys as $key) {
            if (array_key_exists($key, $input_flags)) {
                $v = $input_flags[$key];
                if ($v === FLAG_TRUE || $v === true || $v === 1 || $v === '1') {
                    $normalized_overrides[$key] = FLAG_TRUE;
                } else {
                    $normalized_overrides[$key] = $arr_targets_visible[$key] ?? FLAG_FALSE;
                }
            }
        }

        if (!empty($normalized_overrides)) {
            $arr_targets_visible = array_replace($arr_targets_visible, $normalized_overrides);
        }

        $str_grammar_view = build_html_grammar_view_page($grammar_unique_code, $room_unique_code, $user_level, $arr_targets_visible, $int_selected_language);

        respond_success(['html' => $str_grammar_view]);

    } catch (Throwable $e) {
        respond_exception($e, 'grammar_view_unhandled');
    }

