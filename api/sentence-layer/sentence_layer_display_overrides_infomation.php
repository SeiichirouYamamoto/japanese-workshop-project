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

		if (!isset($input['id'])) {
			respond_error('Value not found: id', 400);
		}

		if (!isset($input['int_selected_language'])) {
			respond_error('Value not found: int_selected_language', 400);
		}

		$id = (int)$input['id'];
		$int_selected_language = (int)$input['int_selected_language'];

		if ($id <= 0) {
			respond_error('invalid id', 400);
		}

		if (!isset($arr_columns_masta_override[$int_selected_language])) {
			respond_error('invalid int_selected_language', 400);
		}

		$arr_strSQL_select = [
			[$t_layer_element_overrides, 'id'],
			[$t_layer_element_overrides, 'layer_element_id'],
			[$t_layer_element_overrides, 'masta_override_id'],
			[$t_layer_element_overrides, 'display_text'],
			[$t_layer_element_overrides, 'is_highlighted'],
			[$t_layer_element_overrides, 'sort'],
			[$t_masta_override, $arr_columns_masta_override[$int_selected_language] . ' as display_text_from_masta'],
			[$t_masta_override_operation, 'operation']
		];

		$strSQL_from = "
			FROM $t_layer_element_overrides
			INNER JOIN $t_masta_override
				ON $t_layer_element_overrides.masta_override_id = $t_masta_override.id
			INNER JOIN $t_masta_override_operation
				ON $t_masta_override.operation_id = $t_masta_override_operation.id
		";

		$arr_strSQL_where = [
			[
				[
					[$t_layer_element_overrides, 'layer_element_id', '=', $id, 'PDO::PARAM_INT', '']
				],
				''
			]
		];

		$arr_strSQL_order = [
			[$t_layer_element_overrides, 'sort', 'ASC']
		];

		$strSQL_option = '';

		list($pdo_has_error, $select_has_error, $e, $arr_overrides) = execute_select_and_fetch_all(
			$arr_strSQL_select,
			$strSQL_from,
			$arr_strSQL_where,
			$arr_strSQL_order,
			$strSQL_option
		);
		handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

		if (empty($arr_overrides)) {
			respond_success(['html' => '']);
		}

		$arr_masta_override = fetch_arr_masta_override_list($int_selected_language);

		$arr_html = [];
		foreach ($arr_overrides as $key => $row) {
			$layer_element_override_id = (int)$row['id'];
			$layer_element_id = (int)$row['layer_element_id'];
			$masta_override_id = (int)$row['masta_override_id'];
			$display_text_free = escape_html($row['display_text'] ?? '');
			$is_highlighted = ((int)($row['is_highlighted'] ?? FLAG_FALSE) === FLAG_TRUE) ? FLAG_TRUE : FLAG_FALSE;
			$sort = (int)$row['sort'];

			$highlight_toggle_id = 'sentenceLayerUpdateOverrideScreenLiHighlightToggleButton' . $key;
			$checked = ($is_highlighted === FLAG_TRUE) ? ' checked' : '';

			$row_options = [];
			foreach ($arr_masta_override as $o) {
				$oid = (int)$o['id'];
				$otext = escape_html($o[$arr_columns_masta_override[$int_selected_language]] ?? '');
				$oop = escape_html($o['operation'] ?? '');
				$otitle = $oop . ':' . $otext;
				$selected = ($oid === $masta_override_id) ? ' selected' : '';
				$row_options[] = '<option value="' . $oid . '"' . $selected . '>' . $otitle . '</option>';
			}
			$str_row_options = implode('', $row_options);

			$arr_html[] =
				'<li class="sentenceLayerUpdateOverrideScreenLi" data-layer-element-override-id="' . $layer_element_override_id . '" data-layer-element-id="' . $layer_element_id . '">' .
					'<div class="sentenceLayerUpdateOverrideScreenLiDataContainer">' .
						'<div class="sentenceLayerUpdateOverrideScreenLiRow">' .
							'<label>Operation</label>' .
							'<select class="sentenceLayerUpdateOverrideScreenLiItem sentenceLayerUpdateOverrideScreenLiMastaOverrideId">' . $str_row_options . '</select>' .
						'</div>' .
						'<div class="sentenceLayerUpdateOverrideScreenLiRow">' .
							'<label>Free Display</label>' .
							'<input type="text" class="sentenceLayerUpdateOverrideScreenLiItem sentenceLayerUpdateOverrideScreenLiFreeDisplayInput" value="' . $display_text_free . '">' .
						'</div>' .
						'<div class="sentenceLayerUpdateOverrideScreenLiRow">' .
							'<label for="' . $highlight_toggle_id . '">Highlight</label>' .
							'<div class="sentenceLayerUpdateOverrideToggleButtonContainer">' .
								'<input type="checkbox" id="' . $highlight_toggle_id . '" class="sentenceLayerUpdateOverrideScreenLiHighlightToggleButton sentenceLayerUpdateOverrideToggleButton"' . $checked . '>' .
								'<label for="' . $highlight_toggle_id . '" class="sentenceLayerUpdateOverrideToggleLabel"></label>' .
							'</div>' .
						'</div>' .
						'<div class="sentenceLayerUpdateOverrideScreenLiRow">' .
							'<label>Sort</label>' .
							'<input type="number" class="sentenceLayerUpdateOverrideScreenLiItem sentenceLayerUpdateOverrideScreenLiSortInput" value="' . $sort . '" placeholder="sort">' .
						'</div>' .
					'</div>' .
					'<div class="sentenceLayerUpdateOverrideScreenLiButtonsContainer">' .
						'<button type="button" class="sentenceLayerUpdateOverrideScreenLiUpdateButton" data-action="override:update" data-layer-element-override-id="' . $layer_element_override_id . '">Update</button>' .
						'<button type="button" class="sentenceLayerUpdateOverrideScreenLiDeleteButton" data-action="override:delete" data-layer-element-override-id="' . $layer_element_override_id . '">Delete</button>' .
					'</div>' .
				'</li>';
		}

		respond_success(['html' => implode("\n", $arr_html)]);

	} catch (Throwable $e) {
		respond_exception($e);
	}

