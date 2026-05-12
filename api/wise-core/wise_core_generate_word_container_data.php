<?php

    header('Content-Type: application/json; charset=utf-8');

    require_once __DIR__ . '/../_bootstrap.php';

    try {

        $user_level = get_user_level();
	
		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		// $user_id = jws_require_single_session();

		// if (!is_teacher_level($user_level)) {
        //     respond_error('Forbidden', 403);
        // }

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);

        if (!is_array($input)) {
            respond_error('Invalid JSON', 400);
        }

        if (!isset($input['send_japanese_id'])) { respond_error('Value not found: send_japanese_id', 400); }
        if (!isset($input['send_japanese_element_id'])) { respond_error('Value not found: send_japanese_element_id', 400); }
        if (!isset($input['send_sub_classification_id'])) { respond_error('Value not found: send_sub_classification_id', 400); }
        if (!isset($input['send_form_id'])) { respond_error('Value not found: send_form_id', 400); }
        if (!isset($input['send_label_id'])) { respond_error('Value not found: send_label_id', 400); }
        if (!isset($input['send_voice_id'])) { respond_error('Value not found: send_voice_id', 400); }
        if (!isset($input['str_japanese'])) { respond_error('Value not found: str_japanese', 400); }
        if (!isset($input['str_kana'])) { respond_error('Value not found: str_kana', 400); }
        if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }

        $arr_contents = [];

        $t_masta_japanese_root_id = intval($input['send_japanese_id'] ?? $int_id_default);
        $t_japanese_element_id = intval($input['send_japanese_element_id'] ?? $int_id_default);
        $t_masta_japanese_sub_classification_id = intval($input['send_sub_classification_id'] ?? $int_id_default);
        $t_masta_form_root_id = intval($input['send_form_id'] ?? $int_id_default);
        $int_label_id = intval($input['send_label_id'] ?? $int_id_default);
        $int_voice_id = intval($input['send_voice_id'] ?? $int_id_default);
        $str_japanese = escape_html($input['str_japanese'] ?? '');
        $str_kana = escape_html($input['str_kana'] ?? '');
        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);

        if ($t_masta_japanese_root_id === $int_id_default) {

            $t_masta_form_root_id = $int_DictionaryForm;
            $t_masta_japanese_sub_classification_id = $int_id_default;
            $int_label_id = $int_id_default;
            $int_voice_id = $int_id_default;

            $str_japanese_sub_classification = fetch_str_sub_classification_name_by_id(
                $t_masta_japanese_sub_classification_id,
                $int_selected_language
            );
            $str_japanese_form = fetch_str_form_name_by_form_root_id(
                $t_masta_form_root_id,
                $int_selected_language
            );
            $str_japanese_voice = get_str_voice_name_by_id(
                $int_voice_id,
                $int_selected_language
            );

            $arr_contents[] = [
                $str_snake_to_camel_japanese => $str_japanese,
                $str_snake_to_camel_kana => $str_kana,
                $str_snake_to_camel_japanese_element_id => $t_japanese_element_id,
                $str_snake_to_camel_sub_classification_id => $t_masta_japanese_sub_classification_id,
                $str_snake_to_camel_form_id => $int_DictionaryForm,
                $str_snake_to_camel_voice_id => $int_voice_id,
                $str_snake_to_camel_japanese_id => $t_masta_japanese_root_id,
                $str_snake_to_camel_label_id => $int_label_id,
                $str_snake_to_camel_sub_classification => $str_japanese_sub_classification,
                $str_snake_to_camel_form => $str_japanese_form,
                $str_snake_to_camel_voice => $str_japanese_voice
            ];

            respond_success($arr_contents);
        }

        if ($int_label_id === intval($INT_NONE)) {

            if ($t_japanese_element_id === $int_id_default) {
                $arr_where_base = [
                    [$t_japanese_elements, 'masta_japanese_root_id', '=', $t_masta_japanese_root_id, 'PDO::PARAM_INT', '']
                ];
            } else {
                $arr_where_base = [
                    [$t_japanese_elements, 'id', '=', $t_japanese_element_id, 'PDO::PARAM_INT', '']
                ];
            }

            $arr_strSQL_select = [
                [$t_japanese_elements, 'id'],
                [$t_japanese_elements, 'masta_japanese_sub_classification_id'],
                [$t_japanese_elements, 'masta_form_root_id'],
                [$t_japanese_elements, 'voice_id'],
                [$t_masta_japanese_sub_category, 'category_id']
            ];

            $strSQL_from = "
                FROM
                (
                    $t_masta_japanese_root
                    INNER JOIN $t_masta_japanese_sub_category
                        ON $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
                )
                INNER JOIN $t_japanese_elements
                    ON $t_masta_japanese_root.id = $t_japanese_elements.masta_japanese_root_id
            ";

            $arr_strSQL_where = [
                [
                    $arr_where_base,
                    ''
                ]
            ];

            $arr_strSQL_order = [
                [$t_japanese_elements, 'sort', 'ASC']
            ];

            list($pdo_has_error, $select_has_error, $e, $arr_japanese_elements) =
                execute_select_and_fetch_all(
                    $arr_strSQL_select,
                    $strSQL_from,
                    $arr_strSQL_where,
                    $arr_strSQL_order,
                    ''
                );

            handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

            if (empty($arr_japanese_elements)) {
                respond_success([]);
            }

            foreach ($arr_japanese_elements as $loop_japanese_elements) {

                $t_japanese_category_id = intval($loop_japanese_elements['category_id']);
                $t_japanese_element_id_target = intval($loop_japanese_elements['id']);
                $t_masta_japanese_sub_classification_id =
                    intval($loop_japanese_elements['masta_japanese_sub_classification_id']);

                if (
                    $t_japanese_category_id === $int_masta_japanese_category_id_word &&
                    $int_V1KU <= $t_masta_japanese_sub_classification_id &&
                    $t_masta_japanese_sub_classification_id <= $int_V3Z
                ) {
                    $t_masta_form_root_id = $t_masta_form_root_id;
                } else {
                    $t_masta_form_root_id = intval($loop_japanese_elements['masta_form_root_id']);
                }

                $int_voice_id = intval($loop_japanese_elements['voice_id']);

                $arr_indicator_labels = get_arr_indicator_label(
                    $t_japanese_element_id_target,
                    true,
                    $int_selected_language
                );

                $arr_contents[] = get_arr_inflected_label(
                    $arr_indicator_labels,
                    $t_masta_japanese_root_id,
                    $t_japanese_element_id_target,
                    $t_masta_japanese_sub_classification_id,
                    $t_masta_form_root_id,
                    $int_voice_id,
                    false,
                    $int_selected_language
                );
            }

        } else {

            $arr_indicator_labels = get_arr_indicator_label(
                $int_label_id,
                false,
                $int_selected_language
            );

            $arr_contents[] = get_arr_inflected_label(
                $arr_indicator_labels,
                $t_masta_japanese_root_id,
                $t_japanese_element_id,
                $t_masta_japanese_sub_classification_id,
                $t_masta_form_root_id,
                $int_voice_id,
                false,
                $int_selected_language
            );
        }

        respond_success($arr_contents);

    } catch (Throwable $e) {
        respond_exception($e, 'change_the_form_unhandled');
    }

