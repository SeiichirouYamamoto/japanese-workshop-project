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

        if (!isset($input['send_japanese_id'])) { respond_error('Value not found: send_japanese_id', 400); }
        if (!isset($input['send_japanese_element_id'])) { respond_error('Value not found: send_japanese_element_id', 400); }
        if (!isset($input['send_sub_classification_id'])) { respond_error('Value not found: send_sub_classification_id', 400); }
        if (!isset($input['send_form_id'])) { respond_error('Value not found: send_form_id', 400); }
        if (!isset($input['send_voice_id'])) { respond_error('Value not found: send_voice_id', 400); }
        if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }

        $arr_contents = [];

        $t_masta_japanese_root_id = intval($input['send_japanese_id'] ?? $int_id_default);
        $t_japanese_element_id = intval($input['send_japanese_element_id'] ?? $int_id_default);
        $t_masta_japanese_sub_classification_id = intval($input['send_sub_classification_id'] ?? $int_id_default);
        $t_masta_form_root_id = intval($input['send_form_id'] ?? $int_id_default);
        $int_voice_id = intval($input['send_voice_id'] ?? $int_id_default);
        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);

        if ($t_masta_japanese_root_id === $int_id_default) {
            respond_success($arr_contents);
        }

        $arr_search_condition = [
            [$t_japanese_labels, 'japanese_element_id', '=', $t_japanese_element_id, 'PDO::PARAM_INT', '']
        ];

        $arr_japanese_labels = get_arr_japanese_from_labels($arr_search_condition, $int_selected_language);

        foreach ($arr_japanese_labels as $loop_japanese_labels) {

            $arr_contents_add = [];
            $id = $loop_japanese_labels['id'] ?? 0;
            $int_masta_japanese_label_id = $loop_japanese_labels[$str_column_masta_japanese_label_id] ?? 0;

            $arr_contents_add = [
                'id' => $id
            ];

            $str_japanese = $loop_japanese_labels[$str_column_label_japanese] ?? '';
            $str_kana = $loop_japanese_labels[$str_column_label_kana] ?? '';

            $arr_inflected_label = [
                $str_snake_to_camel_japanese => $str_japanese,
                $str_snake_to_camel_kana => $str_kana
            ];

            $arr_inflected_label = apply_word_inflection(
                $arr_inflected_label,
                $t_masta_japanese_root_id,
                $t_masta_japanese_sub_classification_id,
                $t_masta_form_root_id,
                $int_voice_id,
                $int_masta_japanese_label_id,
                false,
                $int_selected_language
            );

            $arr_inflected_label = array_merge($arr_contents_add, $arr_inflected_label);

            $arr_contents[] = $arr_inflected_label;
        }

        respond_success($arr_contents);

    } catch (Throwable $e) {
        respond_exception($e, 'change_the_form_by_label_unhandled');
    }

