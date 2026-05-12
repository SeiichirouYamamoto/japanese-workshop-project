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

		if (!isset($input['int_layer_id'])) {
			respond_error('Value not found: int_layer_id', 400);
		}

		if (!isset($input['int_selected_language'])) {
			respond_error('Value not found: int_selected_language', 400);
		}

		$int_layer_id = (int)$input['int_layer_id'];
		$int_selected_language = (int)$input['int_selected_language'];

		if ($int_layer_id <= 0) {
			respond_error('invalid int_layer_id', 400);
		}

		if (!isset($arr_columns_masta_japanese_root[$int_selected_language])) {
			respond_error('invalid int_selected_language', 400);
		}

		$arr_strSQL_select = [
			[$t_layers, 'id as layerId'],
			[$t_layers, 'unique_code'],
			[$t_layers, 'layer_name'],
			[$t_layers, 'sort as layerSort'],
			[$t_masta_japanese_root, 'unique_code as item_unique_code'],
			[$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language] . ' as item_japanese'],
			[$t_layer_elements, 'id as layerElementId'],
			[$t_layer_elements, 'form_id as formId'],
			[$t_layer_elements, 'voice_id as voiceId'],
			[$t_registered_sentence_elements, 'id as sentenceElementId'],
			[$t_registered_sentence_elements, 'japanese_id as ' . $str_snake_to_camel_japanese_id],
			[$t_registered_sentence_elements, 'japanese_element_id as japaneseElementId'],
			[$t_registered_sentence_elements, 'sub_classification_id as subClassificationId'],
			[$t_registered_sentence_elements, 'japanese'],
			[$t_registered_sentence_elements, 'label_id as labelId'],
			[$t_registered_sentence_elements, 'sort as rseSort']
		];

		$strSQL_from = "
			FROM
				(
					(
						$t_layers
						LEFT JOIN $t_masta_japanese_root
							ON $t_layers.masta_japanese_root_id = $t_masta_japanese_root.id
					)
					INNER JOIN $t_layer_elements
						ON $t_layers.id = $t_layer_elements.layer_id
				)
				INNER JOIN $t_registered_sentence_elements
					ON $t_layer_elements.registered_sentence_element_id = $t_registered_sentence_elements.id
		";

		$arr_strSQL_where = [
			[
				[
					[$t_layers, 'id', '=', $int_layer_id, 'PDO::PARAM_INT', '']
				],
				''
			]
		];

		$arr_strSQL_order = [
			[$t_registered_sentence_elements, 'sort', 'ASC']
		];

		$strSQL_option = '';

		list($pdo_has_error, $select_has_error, $e, $arr_waypoint_items) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
		handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

		$result_sentence = '';

		if (!empty($arr_waypoint_items)) {
			foreach ($arr_waypoint_items as $selected) {

				$leId = (int)$selected['layerElementId'];
				$overrides = fetch_arr_overrides_by_layer_element_id($leId, $int_selected_language);

				$prefix = '';
				$out = '';
				$suffix = '';

				if (empty($overrides)) {

					if ($selected['formId'] == 0 || $selected['voiceId'] == 0) {
						$base = $selected['japanese'];
					} else {
						$arr_indicator_labels = get_arr_indicator_label($selected['labelId'], false, $int_selected_language);
						$arr_inflected_label = get_arr_inflected_label($arr_indicator_labels, $selected['japaneseId'], $selected['japaneseElementId'], $selected['subClassificationId'], $selected['formId'], $selected['voiceId'], false, $int_selected_language);
						$base = !empty($arr_inflected_label['japanese']) ? $arr_inflected_label['japanese'] : $selected['japanese'];
					}

					$out = $base;

				} else {

					$arr_indicator_labels = get_arr_indicator_label($selected['labelId'], false, $int_selected_language);
					$arr_inflected_label = get_arr_inflected_label($arr_indicator_labels, $selected['japaneseId'], $selected['japaneseElementId'], $selected['subClassificationId'], $selected['formId'], $selected['voiceId'], false, $int_selected_language);
					$base = !empty($arr_inflected_label['japanese']) ? $arr_inflected_label['japanese'] : $selected['japanese'];

					list($prefix, $out, $suffix) = get_data_override_text_parts_for_update($base, $overrides);
				}

				$result_sentence .= $prefix . $out . $suffix;
			}
		}

		respond_success([
			'item_title' => $result_sentence
		]);

	} catch (Throwable $e) {
		respond_exception($e);
	}

