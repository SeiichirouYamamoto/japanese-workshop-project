<?php

	header('Content-Type: application/json; charset=utf-8');

	require_once __DIR__ . '/../_bootstrap.php';

	try {

		$raw = file_get_contents('php://input');
		$input = json_decode($raw, true);

		if (!is_array($input)) {
			respond_error('Invalid JSON', 400);
		}

		$int_selected_language = (int)($input['int_selected_language'] ?? $int_used_language_jpn);
		$t_masta_japanese_root_id = (int)($input['send_japanese_id'] ?? $int_id_default);
		$t_japanese_element_id = (int)($input['send_japanese_element_id'] ?? $int_id_default);
		$t_masta_japanese_sub_classification_id = (int)($input['send_sub_classification_id'] ?? $int_id_default);
		$int_label_id = (int)($input['send_label_id'] ?? $int_id_default);
		$int_voice_id = (int)($input['send_voice_id'] ?? $int_id_default);

		if ($t_masta_japanese_root_id <= 0) {
			respond_success(['html' => '']);
		}

		$arr_indicator_labels = get_arr_indicator_label($int_label_id, false, $int_selected_language);

		$arr_already_learned_list = [];

		if (isset($input['formList']) && is_array($input['formList'])) {
			$arr_already_learned_list = array_map('intval', $input['formList']);
		} elseif (isset($_SESSION['arr_already_learned_list']) && is_array($_SESSION['arr_already_learned_list'])) {
			$arr_already_learned_list = $_SESSION['arr_already_learned_list'];
		}

		$arr_form_list = fetch_arr_form_root_list($arr_already_learned_list, $int_selected_language);

		$str_form_list = '';
		$seen = [];

		foreach ($arr_form_list as $loop_form_list) {

			$t_masta_form_root_id = (int)($loop_form_list['id'] ?? 0);
			if ($t_masta_form_root_id <= 0) {
				continue;
			}

			$arr_inflected_label = get_arr_inflected_label(
				$arr_indicator_labels,
				$t_masta_japanese_root_id,
				$t_japanese_element_id,
				$t_masta_japanese_sub_classification_id,
				$t_masta_form_root_id,
				$int_voice_id,
				false,
				$int_selected_language
			);

			$raw_japanese = (string)($arr_inflected_label[$str_snake_to_camel_japanese] ?? '');
			if ($raw_japanese === '') {
				continue;
			}

			if (isset($seen[$raw_japanese])) {
				continue;
			}
			$seen[$raw_japanese] = true;

			$str_japanese = escape_html($raw_japanese);
			$str_form_list .= '<li class="quizInflectionScreenLi wiseUiFontSizeTarget" data-form-id="' . $t_masta_form_root_id . '">' . $str_japanese . '</li>';
		}

		respond_success(['html' => $str_form_list]);

	} catch (Throwable $e) {
		respond_exception($e);
	}

