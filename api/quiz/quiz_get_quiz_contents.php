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
		$quiz_type = escape_html($input['quiz_type'] ?? '');
		$page_type = escape_html($input['page_type'] ?? '');
		$room_unique_code = escape_html($input['room_unique_code'] ?? '');

		$is_advance_stage_num = (int)($input['is_advance_stage_num'] ?? FLAG_FALSE);
		$is_advance_stage = ($is_advance_stage_num === FLAG_TRUE);

		if ($quiz_type === '') {
			respond_error('Value not found: quiz_type', 400);
		}

		if ($page_type === '') {
			respond_error('Value not found: page_type', 400);
		}

		$room_id = fetch_room_id_from_unique_code($room_unique_code, $int_selected_language);

		$int_mastery_level = $int_mastery_level_jws_beginner;
		$unique_code_type = $str_option_value_default;
		$arr_grammar_unique_code = [];
		$arr_japanese_classification = [];
		$arr_sub_category = [];
		$arr_inflection = [];

		$str_quiz_settings_screen = '';
		$str_quiz_history_screen = '';
		$str_quiz_contents = '';
		$str_quiz_history_prompt = '';

		if ($page_type === 'wise') {

			if (isset($_SESSION['quiz_settings_mastery_level'])) {
				$int_mastery_level = $_SESSION['quiz_settings_mastery_level'];
			}

			$unique_code_type = $room_unique_code !== '' ? $room_unique_code : $str_option_value_default;

			if (isset($_SESSION['quiz_settings_sub_category'])) {
				$arr_sub_category = $_SESSION['quiz_settings_sub_category'];
			}

			if (isset($_SESSION['quiz_settings_japanese_classification'])) {
				$arr_japanese_classification = $_SESSION['quiz_settings_japanese_classification'];
			}

			if (isset($_SESSION['quiz_settings_inflection'])) {
				$arr_inflection = $_SESSION['quiz_settings_inflection'];
			}

		}

		switch ($quiz_type) {

			case 'japaneseParticle':
			case 'japaneseParticleBeginner':
			case 'japaneseParticleBasic':
			case 'japaneseParticleIntermediate':
			case 'japaneseParticleAdvanced':
			case 'japaneseParticleExpert':
			case 'japaneseParticleMaster':

				switch ($quiz_type) {
					case 'japaneseParticleBeginner':
						$int_mastery_level = $int_mastery_level_jws_beginner;
						break;

					case 'japaneseParticleBasic':
						$int_mastery_level = $int_mastery_level_jws_basic;
						break;

					case 'japaneseParticleIntermediate':
						$int_mastery_level = $int_mastery_level_jws_intermediate;
						break;

					case 'japaneseParticleAdvanced':
						$int_mastery_level = $int_mastery_level_jws_advanced;
						break;

					case 'japaneseParticleExpert':
						$int_mastery_level = $int_mastery_level_jws_expert;
						break;

					case 'japaneseParticleMaster':
						$int_mastery_level = $int_mastery_level_jws_master;
						break;
				}

				list($str_quiz_contents, $str_quiz_history_prompt) = get_data_japanese_particle_quiz($page_type, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $int_selected_language);
				break;

			case 'wordInflection':
			case 'wordInflectionVerbDictionary':
			case 'wordInflectionVerbNai':
			case 'wordInflectionVerbTe':
			case 'wordInflectionVerbTa':
			case 'wordInflectionVerbBa':
			case 'wordInflectionVerbVolitional':
			case 'wordInflectionVerbImperative':
			case 'wordInflectionPotentialVerb':
			case 'wordInflectionPassiveVerb':
			case 'wordInflectionCausativeVerb':
			case 'wordInflectionHonorificVerb':
			case 'wordInflectionCausativePassiveVerb':

				$arr_sub_category = [];

				switch ($quiz_type) {
					case 'wordInflectionVerbDictionary':
						$arr_inflection = [$int_DictionaryForm];
						break;

					case 'wordInflectionVerbNai':
						$arr_inflection = [$int_NaiForm];
						break;

					case 'wordInflectionVerbTe':
						$arr_inflection = [$int_TeForm];
						break;

					case 'wordInflectionVerbTa':
						$arr_inflection = [$int_TaForm];
						break;

					case 'wordInflectionVerbBa':
						$arr_inflection = [$int_BaForm];
						break;

					case 'wordInflectionVerbVolitional':
						$arr_inflection = [$int_VolitionalForm];
						break;

					case 'wordInflectionVerbImperative':
						$arr_inflection = [$int_ImperativeForm];
						break;

					case 'wordInflectionPotentialVerb':
						$arr_inflection = [$int_PotentialVerb];
						break;

					case 'wordInflectionPassiveVerb':
						$arr_inflection = [$int_PassiveVerb];
						break;

					case 'wordInflectionCausativeVerb':
						$arr_inflection = [$int_CausativeVerb];
						break;

					case 'wordInflectionHonorificVerb':
						$arr_inflection = [$int_HonorificVerb];
						break;

					case 'wordInflectionCausativePassiveVerb':
						$arr_inflection = [$int_CausativePassiveVerb];
						break;
				}

				list($str_quiz_contents, $str_quiz_history_prompt) = get_data_word_inflection_quiz($page_type, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $arr_japanese_classification, $arr_inflection, $int_selected_language);
				break;

			case 'grammar':
			case 'grammarBeginner':
			case 'grammarBasic':
			case 'grammarIntermediate':
			case 'grammarAdvanced':
			case 'grammarExpert':
			case 'grammarMaster':

				switch ($quiz_type) {
					case 'grammarBeginner':
						$int_mastery_level = $int_mastery_level_jws_beginner;
						break;

					case 'grammarBasic':
						$int_mastery_level = $int_mastery_level_jws_basic;
						break;

					case 'grammarIntermediate':
						$int_mastery_level = $int_mastery_level_jws_intermediate;
						break;

					case 'grammarAdvanced':
						$int_mastery_level = $int_mastery_level_jws_advanced;
						break;

					case 'grammarExpert':
						$int_mastery_level = $int_mastery_level_jws_expert;
						break;

					case 'grammarMaster':
						$int_mastery_level = $int_mastery_level_jws_master;
						break;
				}

				list($str_quiz_contents, $str_quiz_history_prompt) = get_data_grammar_quiz($page_type, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $int_selected_language);
				break;

			case 'plainform':
			case 'plainformVerb':
			case 'plainformIAdjective':
			case 'plainformNaAdjective':
			case 'plainformNoun':

				$arr_sub_category = [];

				switch ($quiz_type) {
					case 'plainformVerb':
						$arr_japanese_classification = [$int_masta_japanese_classification_id_verb];
						break;

					case 'plainformIAdjective':
						$arr_japanese_classification = [$int_masta_japanese_classification_id_i_adjective];
						break;

					case 'plainformNaAdjective':
						$arr_japanese_classification = [$int_masta_japanese_classification_id_na_adjective];
						break;

					case 'plainformNoun':
						$arr_japanese_classification = [$int_masta_japanese_classification_id_noun];
						break;
				}

				list($str_quiz_contents, $str_quiz_history_prompt) = get_data_plainform_quiz($page_type, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $arr_japanese_classification, $int_selected_language);
				break;

			case 'sorting':
			case 'sortingBeginner':
			case 'sortingBasic':
			case 'sortingIntermediate':
			case 'sortingAdvanced':
			case 'sortingExpert':
			case 'sortingMaster':

				switch ($quiz_type) {
					case 'sortingBeginner':
						$int_mastery_level = $int_mastery_level_jws_beginner;
						break;

					case 'sortingBasic':
						$int_mastery_level = $int_mastery_level_jws_basic;
						break;

					case 'sortingIntermediate':
						$int_mastery_level = $int_mastery_level_jws_intermediate;
						break;

					case 'sortingAdvanced':
						$int_mastery_level = $int_mastery_level_jws_advanced;
						break;

					case 'sortingExpert':
						$int_mastery_level = $int_mastery_level_jws_expert;
						break;

					case 'sortingMaster':
						$int_mastery_level = $int_mastery_level_jws_master;
						break;
				}

				list($str_quiz_contents, $str_quiz_history_prompt) = get_data_sorting_quiz($page_type, $is_advance_stage, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $int_selected_language);
				break;

			default:
				respond_error('invalid quiz_type', 400);
		}

		$str_quiz_contents = $str_quiz_contents . $str_quiz_settings_screen . $str_quiz_history_screen;

		respond_success([
			'html' => $str_quiz_contents,
			'quizHistoryPrompt' => $str_quiz_history_prompt
		]);

	} catch (Throwable $e) {
		respond_exception($e);
	}

