<?php

/******************************************************
 *  PAGE 
 *  
 ******************************************************/
function build_html_sorting_quiz_fullscreen_page($unique_code, $isAdvanceStage, $int_selected_language){

	global
		$t_registered_sentence_elements,
		$int_phrase_clause_id_target,
		$t_japanese_elements,
		$str_sortingQuizPieceListContainerLiButtonsKana,
		$str_sortingQuizPieceListContainerLiButtonsInflection,
		$str_phrase_clause_container,
		$str_snake_to_camel_japanese_id,
		$str_snake_to_camel_japanese_element_id,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$str_quizButtonFinishQuiz;

	$str_html = '';

	$arr_already_learned_list = [];
	if(isset($_GET['formList'])) {
		$arr_already_learned_list = $_GET['formList'];
		$arr_already_learned_list = array_map('escape_html',$arr_already_learned_list);
	}
	elseif (isset($_SESSION['arr_already_learned_list'])) {
		$arr_already_learned_list = $_SESSION['arr_already_learned_list'];
	}
	else{
		$arr_already_learned_list = get_arr_temp_already_learned_list($int_selected_language);
	}
	
	$arr_registered_sentence = fetch_arr_registered_sentence_by_unique_code($unique_code, $int_selected_language);

	if(empty($arr_registered_sentence)){
		return $str_html;
	}

	$arr_registered_sentence = $arr_registered_sentence[INDEX_FIRST];
	$int_registered_sentence_id = $arr_registered_sentence['id'];

	$str_quiz_translation = fetch_str_registered_sentence_answer_by_id($int_registered_sentence_id, $int_selected_language);

	if(!empty($str_quiz_translation)){
		$arr_registered_sentence['sentence'] = $str_quiz_translation;
	}

	$arr_strSQL_select = [
		[$t_registered_sentence_elements, 'id'],
		[$t_registered_sentence_elements, 'registered_sentence_id'],
		[$t_registered_sentence_elements, 'id_name as idName'],
		[$t_registered_sentence_elements, 'unique_key as uniqueKey'],
		[$t_registered_sentence_elements, 'japanese_id as ' . $str_snake_to_camel_japanese_id],
		[$t_registered_sentence_elements, 'japanese_element_id as ' . $str_snake_to_camel_japanese_element_id],
		[$t_registered_sentence_elements, 'sub_classification_id as subClassificationId'],
		[$t_registered_sentence_elements, 'form_id as formId'],
		[$t_registered_sentence_elements, 'label_id as labelId'],
		[$t_registered_sentence_elements, 'voice_id as voiceId'],
		[$t_registered_sentence_elements, 'bounds_top as boundsTop'],
		[$t_registered_sentence_elements, 'bounds_left as boundsLeft'],
		[$t_registered_sentence_elements, 'link_id as linkId'],
		[$t_registered_sentence_elements, 'link_type as linkType'],
		[$t_registered_sentence_elements, 'japanese'],
		[$t_registered_sentence_elements, 'kana'],
		[$t_registered_sentence_elements, 'sub_classification as subClassification'],
		[$t_registered_sentence_elements, 'phrase_clause_type as phraseClauseType'],
		[$t_registered_sentence_elements, 'phrase_clause_id as phraseClauseId'],
		[$t_registered_sentence_elements, 'japanese_phrase_clause as japanesePhraseClause'],
		[$t_registered_sentence_elements, 'kana_phrase_clause as kanaPhraseClause'],
		[$t_registered_sentence_elements, 'sort']
	];

	$strSQL_from = ' FROM ' .$t_registered_sentence_elements;

	$arr_strSQL_where = [
		[
			[
				[$t_registered_sentence_elements,'registered_sentence_id','=',$int_registered_sentence_id,'PDO::PARAM_INT','And'],
				[$t_registered_sentence_elements,'phrase_clause_id','=',$int_phrase_clause_id_target,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_registered_sentence_elements,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_registered_sentence_elements) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	shuffle($arr_registered_sentence_elements);

	$str_sortingQuizPieceListContainerLi = '';

	foreach($arr_registered_sentence_elements as $loop_registered_sentence_elements){

		$int_unique_key = escape_html($loop_registered_sentence_elements['uniqueKey']);
		$int_japanese_id = escape_html($loop_registered_sentence_elements[$str_snake_to_camel_japanese_id]);
		$int_japanese_element_id = escape_html($loop_registered_sentence_elements['japaneseElementId']);
		$int_sub_classification_id = escape_html($loop_registered_sentence_elements['subClassificationId']);
		$int_form_id = escape_html($loop_registered_sentence_elements['formId']);
		$int_label_id = escape_html($loop_registered_sentence_elements['labelId']);
		$int_voice_id = escape_html($loop_registered_sentence_elements['voiceId']);

		$str_japanese = escape_html($loop_registered_sentence_elements['japanese']);
		$str_kana = escape_html($loop_registered_sentence_elements['kana']);
		$str_japanesePhraseClause = escape_html($loop_registered_sentence_elements['japanesePhraseClause']);
		$str_kanaPhraseClause = escape_html($loop_registered_sentence_elements['kanaPhraseClause']);
		
		$str_sortingQuizPieceListContainerLiButtons = '';

		if($isAdvanceStage){

			$masta_japanese_root_id_from_form_id = fetch_masta_japanese_root_id_from_masta_form_root_id($int_form_id, $int_selected_language);
			$masta_japanese_root_id_from_voice_id = fetch_masta_japanese_root_id_from_masta_form_root_id($int_voice_id, $int_selected_language);

			$isAlreadyLearnedForm = in_array($masta_japanese_root_id_from_form_id, $arr_already_learned_list);
			$isAlreadyLearnedVoice = in_array($masta_japanese_root_id_from_voice_id, $arr_already_learned_list);

			if ($isAlreadyLearnedForm && $isAlreadyLearnedVoice) {

				$arr_strSQL_select = [
					[$t_japanese_elements,'id'],
					[$t_japanese_elements,'masta_japanese_sub_classification_id'],
					[$t_japanese_elements,'masta_form_root_id'],
					[$t_japanese_elements,'voice_id']
				];
			
				$strSQL_from = ' FROM ' .$t_japanese_elements;
			
				$arr_strSQL_where = [
					[
						[
							[$t_japanese_elements,'id','=',$int_japanese_element_id,'PDO::PARAM_INT','']
						],
						''
					]
				];
			
				$arr_strSQL_order = [];
			
				$strSQL_option = '';
			
				list($pdo_has_error, $select_has_error, $e, $arr_japanese_elements) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
				handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);
	
				if(!empty($arr_japanese_elements)){
	
					$arr_japanese_elements = $arr_japanese_elements[INDEX_FIRST];
					$int_japanese_id = $int_japanese_id;
					$int_japanese_element_id = $int_japanese_element_id;
					$int_sub_classification_id = $arr_japanese_elements['masta_japanese_sub_classification_id'];
					$int_form_id = $arr_japanese_elements['masta_form_root_id'];
					$int_label_id = $int_label_id;
					$int_voice_id = $arr_japanese_elements['voice_id'];
		
					$arr_indicator_labels = get_arr_indicator_label($int_label_id, false, $int_selected_language);
					$arr_inflected_label = get_arr_inflected_label($arr_indicator_labels, $int_japanese_id, $int_japanese_element_id, $int_sub_classification_id, $int_form_id, $int_voice_id, false, $int_selected_language);
	
					$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
					$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
		
					$str_sortingQuizPieceListContainerLiButtons = '
					<div class="sortingQuizPieceListContainerLiButtonsContainer">
						<button class="sortingQuizPieceListContainerLiButtonsKana">'.$str_sortingQuizPieceListContainerLiButtonsKana[$int_selected_language].'</button>
						<button class="sortingQuizPieceListContainerLiButtonsInflection">'.$str_sortingQuizPieceListContainerLiButtonsInflection[$int_selected_language].'</button>
					</div>';
				}
				else{
					$str_sortingQuizPieceListContainerLiButtons = '
					<div class="sortingQuizPieceListContainerLiButtonsContainer">
					<button class="sortingQuizPieceListContainerLiButtonsKana">'.$str_sortingQuizPieceListContainerLiButtonsKana[$int_selected_language].'</button>
					</div>';
				}
			}
			else{
				$str_sortingQuizPieceListContainerLiButtons = '
				<div class="sortingQuizPieceListContainerLiButtonsContainer">
				<button class="sortingQuizPieceListContainerLiButtonsKana">'.$str_sortingQuizPieceListContainerLiButtonsKana[$int_selected_language].'</button>
				</div>';
			}
		}
		else{
			$str_sortingQuizPieceListContainerLiButtons = '
			<div class="sortingQuizPieceListContainerLiButtonsContainer">
			<button class="sortingQuizPieceListContainerLiButtonsKana">'.$str_sortingQuizPieceListContainerLiButtonsKana[$int_selected_language].'</button>
			</div>';
		}

		if($loop_registered_sentence_elements['phraseClauseType'] === $str_phrase_clause_container){
			$str_japanese_result = $str_japanesePhraseClause.$str_japanese;
			$str_kana_result = $str_kanaPhraseClause.$str_kana;
		}
		else{
			$str_japanese_result = $str_japanese;
			$str_kana_result = $str_kana;
		}

		$str_sortingQuizPieceListContainerLi_add = '
		<li 
			class="sortingQuizPieceListContainerLi wiseUiFontSizeTarget" 
			data-unique-key="'.$int_unique_key.'"
			data-japanese-id="'.$int_japanese_id.'"
			data-japanese-element-id="'.$int_japanese_element_id.'"
			data-sub-classification-id="'.$int_sub_classification_id.'"
			data-form-id="'.$int_form_id.'"
			data-label-id="'.$int_label_id.'"
			data-voice-id="'.$int_voice_id.'"
			data-japanese="'.$str_japanese.'"
			data-kana="'.$str_kana.'"
			data-japanese-phrase-clause="'.$str_japanesePhraseClause.'"
			data-kana-phrase-clause="'.$str_kanaPhraseClause.'"
			data-japanese-result="'.$str_japanese_result.'"
			data-kana-result="'.$str_kana_result.'"
		>
			<div class="sortingQuizPieceListContainerLiJapanese">'.
				$str_japanese_result.'
			</div>'.
			$str_sortingQuizPieceListContainerLiButtons.'
		</li>';
		$str_sortingQuizPieceListContainerLi = $str_sortingQuizPieceListContainerLi.$str_sortingQuizPieceListContainerLi_add;
	}

	$str_next_button = '';
	$str_finish_button = '<button class="sortingQuizMenuBarButtonFinishQuiz quizContentsButton">'.$str_quizButtonFinishQuiz[$int_selected_language].'</button>';
	$str_sorting_successScreen = build_html_sorting_quiz_success_screen($str_next_button, $str_finish_button, $int_selected_language);
	$str_sorting_failureScreen = build_html_sorting_quiz_failure_screen($str_next_button, $str_finish_button, $int_selected_language);
	$str_sorting_usersManualScreen = build_html_sorting_quiz_users_manual_screen(false, $isAdvanceStage, $int_selected_language);
	$str_sorting_inflectionScreen = build_html_sorting_quiz_inflection_screen($int_selected_language);
	$str_sorting_quiz_menubar_tools = build_html_sorting_quiz_fullscreen_menubar_tools($unique_code, $int_selected_language);
	$str_sortingQuizPieceListContainer = build_html_sorting_quiz_piece_list_container(false, $str_sortingQuizPieceListContainerLi, $int_selected_language);

	$str_sortingQuizContentsLeftContainer = '<div id="sortingQuizContentsLeftContainer">'.$str_sorting_quiz_menubar_tools.$str_sortingQuizPieceListContainer.'</div>';

	$str_sortingQuizQuestion = '<div id="sortingQuizQuestion" class="wiseUiFontSizeTarget">'.$arr_registered_sentence['sentence'].'</div>';
	$str_sortingQuizZone = '<div id="sortingQuizZone" class="sortingQuizZone"></div>';
	$str_sortingQuizContentsRightContainer = '
		<div id="sortingQuizContentsRightContainer">'.
			$str_sortingQuizQuestion.
			$str_sortingQuizZone.'
		</div>';

	$str_sortingQuizPuzzleContents = '<div id="sortingQuizContents">'.$str_sortingQuizContentsLeftContainer.$str_sortingQuizContentsRightContainer.'</div>';
	$str_sortingQuizBody =  '<div id="sortingQuizBody" class="sortingQuizFull">'.$str_sortingQuizPuzzleContents.'</div>';
	$str_wiseBodyQuiz = '<div id="wiseBodyQuiz" class="wiseBodyRoot"><div id="wiseContentQuiz" class="wiseContentRoot">'.$str_sortingQuizBody.'</div></div>';

	$str_html = '';
	$str_html .= $str_sorting_successScreen;
	$str_html .= $str_sorting_failureScreen;
	$str_html .= $str_sorting_usersManualScreen;
	$str_html .= $str_sorting_inflectionScreen;
	$str_html .= $str_wiseBodyQuiz;

	$str_html = '<section id="sectionSortingQuizFullScreen" class="wise-require-fullscreen">'.$str_html.'</section>';

	return $str_html;
}



/******************************************************
 *  HTML SECTION 
 *  
 ******************************************************/
function switch_build_html_select_quiz_section($pageType, $int_selected_language){

	global
		$str_selectQuizContainerQuestion,
		$arr_selectQuizContainerOption_wise,
		$arr_selectQuizContainerOption_landing,
		$arr_str_button_caption_confirm,
		$arr_str_button_caption_go,
		$str_selectQuizContainerGoToQuiz,
		$str_selectQuizContainerOptionDefault;

	$str_html = '';
	
	$str_confirm_button = '';
		
	$heading = $str_selectQuizContainerGoToQuiz[$int_selected_language];
	$section_id = 'landingPageSelectQuizSection';
	$section_class = 'landingPageContentsSection';
	$section_description = $str_selectQuizContainerQuestion[$int_selected_language];
	$arr_selectQuizContainerOption = $arr_selectQuizContainerOption_landing;
	$str_next_button = '<button id="selectQuizContainerConfirmButton" class="quizContentsButton">'.$arr_str_button_caption_go[$int_selected_language].'</button>';

	$str_html = build_html_select_quiz_section(
		$arr_selectQuizContainerOption,
		$int_selected_language,
		$heading,
		$str_selectQuizContainerOptionDefault[$int_selected_language],
		$str_next_button,
		$section_id,
		$section_class,
		$section_description,
		'selectQuizContainerStudyTopic',
		''
	);

	return $str_html;
}

function build_html_select_quiz_select(
    array $groups,
    int $int_selected_language,
    string $placeholder,
    string $select_id = 'selectQuizContainerStudyTopic',
    string $selected_value = ''
): string {
    global
        $int_mastery_level_select_all;

    $html = '<select id="' . escape_html($select_id) . '">';
    $html .= '<option value="">' . escape_html($placeholder) . '</option>';

    foreach ($groups as $group_key => $group) {
        $title_arr = isset($group['title']) ? $group['title'] : ['', ''];
        $group_label = isset($title_arr[$int_selected_language])
            ? $title_arr[$int_selected_language]
            : (is_array($title_arr) ? reset($title_arr) : (string)$title_arr);

        $html .= '<optgroup label="' . escape_html($group_label) . '" data-group="' . escape_html($group_key) . '">';

        $items = isset($group['items']) && is_array($group['items']) ? $group['items'] : [];

        foreach ($items as $item) {
            $value = isset($item['title']) ? $item['title'] : '';
            $level = isset($item['level']) ? (int)$item['level'] : 0;
            $label_arr = isset($item['label']) ? $item['label'] : ['', ''];
            $label = isset($label_arr[$int_selected_language])
                ? $label_arr[$int_selected_language]
                : (is_array($label_arr) ? reset($label_arr) : (string)$label_arr);

            if ($level !== $int_mastery_level_select_all && !has_level_sentences($level, $int_selected_language)) {
                continue;
            }

            $sel = ($value === $selected_value) ? ' selected' : '';
            $html .= '<option value="' . escape_html($value) . '" data-level="' . escape_html($level) . '"' . $sel . '>'
                . escape_html($label)
                . '</option>';
        }

        $html .= '</optgroup>';
    }

    $html .= '</select>';

    return $html;
}

function build_html_select_quiz_section(
    array $groups,
    int $int_selected_language,
    string $heading,
    string $placeholder,
    string $str_next_button,
    string $section_id = 'selectQuizSection',
    string $section_class = 'sectionStandard',
    string $section_description = '',
    string $select_id = 'selectQuizContainerStudyTopic',
    string $selected_value = ''
): string {

    $html = '<section id="' . escape_html($section_id) . '" class="' . $section_class . '">';
    $html .= '<h2>' . escape_html($heading) . '</h2>';

    if ($section_description !== '') {
        $html .= '<p>' . escape_html($section_description) . '</p>';
    }

    $html .= build_html_select_quiz_select(
        $groups,
        $int_selected_language,
        $placeholder,
        $select_id,
        $selected_value
    );

    $html .= $str_next_button;
    $html .= '</section>';

    return $html;
}



/******************************************************
 *  HTML QIUZ CORE
 *  
 ******************************************************/


function build_html_quiz_main_section($pageType, $int_quiz, $arr_quiz_items, $str_quiz_prompt, $str_send_question, $str_send_answer, $int_mastery_level, $arr_japanese_classification, $arr_inflection, $int_selected_language){

	global
		$arr_quiz_data,
		$str_quizButtonShowOtherQuestions,
		$str_quizButtonShowNextQuestion,
		$str_quizButtonCopy,
		$str_quizButtonOpenSettingsScreen,
		$str_quizButtonOpenHistoryScreen,
		$int_quiz_sortingQuiz;

    $str_quiz_main_section = '';
    $arr_target_data = $arr_quiz_data[$int_quiz];
	
	list($data_level, $data_jc, $data_inf) = generate_quiz_data_attributes($int_mastery_level, $arr_japanese_classification, $arr_inflection);
	
    $str_quiz_header = build_html_quiz_header($str_quiz_prompt, $int_selected_language);
    $str_quiz_items = build_html_quiz_items($pageType, $int_quiz, $arr_quiz_items, $data_level, $data_jc, $data_inf, $int_selected_language);


    if ($pageType === 'landing') {
        $str_next_button = '<button class="'.$arr_target_data['quizButtonToPage'].' quizButtonToPage quizContentsButton"'.$data_level.$data_jc.$data_inf.'">'.$str_quizButtonShowOtherQuestions[$int_selected_language].'</button>';
        $str_quizContentsNoticeContainer = '';
        $str_main_section_class = 'sectionQuizContents animationFadeIn';
    } elseif ($pageType === 'quiz') {
        $str_next_button = '<button class="'.$arr_target_data['quizButtonNextQuestion'].' quizButtonNextQuestion quizContentsButton">'.$str_quizButtonShowNextQuestion[$int_selected_language].'</button>';
        $str_quizContentsNoticeContainer = build_html_quiz_contents_notice_container($int_quiz, $str_send_question, $str_send_answer, $int_selected_language);
        $str_main_section_class = 'sectionQuizContents animationFadeIn';
    } else {
        $str_next_button = '<button class="'.$arr_target_data['quizButtonNextQuestion'].' wiseQuizButtonNextQuestion quizContentsButton">'.$str_quizButtonShowNextQuestion[$int_selected_language].'</button>';
        $str_quizContentsNoticeContainer = '';
        $str_main_section_class = 'sectionQuizContents animationFadeIn';
    }

    $str_copy_button = '<button class="'.$arr_target_data['quizButtonCopy'].' quizButtonCopy quizContentsButton">'.$str_quizButtonCopy[$int_selected_language].'</button>';
    $str_open_settings_screen_button = '';
    $str_open_history_screen_button = '';
    $str_zoom_big_button = '';
    $str_zoom_small_button = '';
    if ($pageType === 'wise') {
        $user_level = get_user_level();
        if (is_teacher_level($user_level)) {
            $str_open_settings_screen_button = '<button class="quizButtonOpenSettingsScreen quizContentsButton">'.$str_quizButtonOpenSettingsScreen[$int_selected_language].'</button>';
            $str_open_history_screen_button = '<button class="quizButtonOpenHistoryScreen quizContentsButton">'.$str_quizButtonOpenHistoryScreen[$int_selected_language].'</button>';
        }
    }
    if ($int_quiz !== $int_quiz_sortingQuiz) {

		$html_zoom_in_icon  = build_html_magnifier_icon('plus');
		$html_zoom_out_icon = build_html_magnifier_icon('minus');

		$str_zoom_big_button =
			'<button class="quizButtonZoomBig quizZoomButton quizContentsButton">'
			. $html_zoom_in_icon .
			'</button>';

		$str_zoom_small_button =
			'<button class="quizButtonZoomSmall quizZoomButton quizContentsButton">'
			. $html_zoom_out_icon .
			'</button>';
	}

	$str_quiz_controls = '';

	if ($pageType !== 'wise') {
		$str_quiz_controls = build_html_quiz_controls($str_next_button, $str_open_settings_screen_button, $str_open_history_screen_button, $str_zoom_big_button, $str_zoom_small_button, $str_copy_button, $int_selected_language);
	}


    $str_quiz_main_section =
    $str_quiz_controls.
    '<section id="'.$arr_target_data['main_section_id'].'" class="'.$str_main_section_class.'">'.
        '<h3>'.$arr_target_data['quiz_title'][$int_selected_language].'</h3>'.
        $str_quiz_header.
        $str_quiz_items.
    '</section>'.
    $str_quizContentsNoticeContainer;

    return $str_quiz_main_section;
}


function build_html_quiz_items($pageType, $int_quiz, $arr_quiz_items, $data_level, $data_jc, $data_inf, $int_selected_language){

	global
		$int_quiz_plainformQuiz,
		$arr_quiz_data,
		$str_quizButtonHint;

    $str_quiz_items = '';

    $str_quiz_item_class = 'quizItem';
    if ($int_quiz === $int_quiz_plainformQuiz) {
        $str_quiz_item_class = 'quizItem quizItemMargin';
    }

    foreach ($arr_quiz_items as $loop_quiz_items) {
        $str_quiz_item = '';
        $str_quiz_item_stages = '';

        $arr_stages = $loop_quiz_items['stages'];
        $has_hint = !empty($arr_stages) && count($arr_stages) >= 2;

        foreach (array_keys($arr_stages) as $index) {
            $stage = $arr_stages[$index];

            $div_quiz_stage = '';
            $str_quiz_item_frame = '';

            $stage_type = $stage['stage_type'];
            $str_quiz_translation = $stage['translation'];
            $arr_questions = $stage['questions'];
            $question = $arr_questions['question'];
            $item = $arr_questions['item'];

            $arr_answer_information = $stage['arr_answer_information'];
            $arr_explanation_information = $stage['arr_explanation_information'];

            $strings = $arr_answer_information['strings'];
            $arrays = $arr_answer_information['arrays'];
            $str_correct_answer = $strings['str_correct_answer'];
            $str_distractor_answer = $strings['str_distractor_answer'];
            $arr_correct_answers = $arrays['arr_correct_answers'];
            $arr_distractor_answers = $arrays['arr_distractor_answers'];

            $str_explanation = $arr_explanation_information['str_explanation'];
            $str_original_sentence = $arr_explanation_information['str_original_sentence'];
            $str_furigana = $arr_explanation_information['str_furigana'];

            $div_quiz_stage_class = '';
            $str_hint_button = '';
            if ($index === INDEX_FIRST) {
                $div_quiz_stage_class = 'quizStage';
                $str_hint_button = $has_hint ? '<button class="'.$arr_quiz_data[$int_quiz]['quizButtonHint'].' quizButtonHint quizItemButton">'.$str_quizButtonHint[$int_selected_language].'</button>' : '';
            } elseif ($index === array_key_last($arr_stages)) {
                $div_quiz_stage_class = 'quizStage hidden animationSlideIn';
                $str_hint_button = '';
            } else {
                $div_quiz_stage_class = 'quizStage hidden animationSlideIn';
                $str_hint_button = $has_hint ? '<button class="'.$arr_quiz_data[$int_quiz]['quizButtonHint'].' quizButtonHint quizItemButton">'.$str_quizButtonHint[$int_selected_language].'</button>' : '';
            }

            $str_quiz_translation_div = '';
            if (!empty($str_quiz_translation)) {
                $str_quiz_translation_div = '<div class="quizTranslation wiseUiFontSizeTarget">'.$str_quiz_translation.'</div>';
            }

            if ($stage_type === 'sorting') {
                $str_quiz_item_frame = $item;
            } else {
                switch ($stage_type) {
                    case 'input':
                        $str_quiz_contents = build_html_quiz_interaction_input($pageType, $int_quiz, $arr_correct_answers, $str_explanation, $str_original_sentence, $str_furigana, $str_hint_button, $data_level, $data_jc, $data_inf, $int_selected_language);
                        break;
                    case 'multiple_choices':
                        $str_quiz_contents = build_html_quiz_interaction_multiple_choices($pageType, $int_quiz, $str_correct_answer, $arr_distractor_answers, $str_explanation, $str_original_sentence, $str_furigana, $str_hint_button, $data_level, $data_jc, $data_inf, $int_selected_language);
                        break;
                    case 'choices':
                        $str_quiz_contents = build_html_quiz_interaction_choices($pageType, $int_quiz, $str_correct_answer, $str_distractor_answer, $str_explanation, $str_original_sentence, $str_furigana, $str_hint_button, $data_level, $data_jc, $data_inf, $int_selected_language);
                        break;
                    default:
                        $str_quiz_contents = '';
                }

                $class = 'frame frameDeepBlue frameQuiz';
                $caution_class = 'frameTitleQuiz';
                $str_quiz_item_frame = build_html_div_frame($class, escape_html_with_nl2br($question), $str_quiz_contents, $caution_class);
            }

            $div_quiz_stage = '
            <div class="'.$div_quiz_stage_class.'">'.
                $str_quiz_translation_div.
                $str_quiz_item_frame.'
            </div>';

            $str_quiz_item_stages .= $div_quiz_stage;
        }

        $str_quiz_item = '
        <div class="'.$str_quiz_item_class.'" data-current-stage-index="'.INDEX_FIRST.'">'.
            $str_quiz_item_stages.'
        </div>';

        $str_quiz_items .= $str_quiz_item;
    }

    $str_quiz_items = '<section class="quizItems">'.
        $str_quiz_items.
    '</section>';

    return $str_quiz_items;
}


function build_html_quiz_header($str_quiz_prompt, $int_selected_language){

	$str_quiz_header = '';
	
	$str_quiz_prompt_div = '';
	$str_quiz_translation_div = '';
	$str_quiz_translation_button_div = '';

	if(!empty($str_quiz_prompt)){
		$user_level = get_user_level();
		if(is_teacher_level($user_level)){
			$str_quiz_prompt_div = '<div class="quizPrompt wiseUiFontSizeTarget" contenteditable="true">'.$str_quiz_prompt.'</div>';
		}
		else{
			$str_quiz_prompt_div = '<div class="quizPrompt wiseUiFontSizeTarget">'.$str_quiz_prompt.'</div>';
		}
	}
		
	$str_quiz_header = '
	<div class="quizHeader">'.
		$str_quiz_prompt_div.'
	</div>';

	return $str_quiz_header;
}


function build_html_quiz_contents_notice_container($int_quiz, $str_send_question, $str_send_answer, $int_selected_language){

	global
	    $arr_quiz_data;

	$url = 'https://docs.google.com/forms/d/e/1FAIpQLSdUTMkOWT-e_bGZoJ65xn5NaamvH8fOjbTjfqdp_Uyvymr7vQ/viewform?usp=pp_url';
	$url = $url.'&entry.2085976859='.$arr_quiz_data[$int_quiz]['quiz_type'];
	$url = $url.'&entry.885808398='.$str_send_question;
	$url = $url.'&entry.934144797='.$str_send_answer;

	$str_mysite_title = get_bloginfo('name');

	$arr_str_quizContentsNoticeContainer = [
		'<div class="quizContentsNoticeContainer">
			<h2>注意事項</h2>
			<p>このクイズは【'.$str_mysite_title.'】が管理するデータベースをもとに、システムが自動で問題を生成しております。</p>
			<p>問題に誤りがあった場合は<a href="'.$url.'" target="_blank" rel="noopener noreferrer">こちらをクリックしてください</a>。</p>
		</div>'
		,
		'<div class="quizContentsNoticeContainer">
			<h2>注意事項</h2>
			<p>本測驗以【'.$str_mysite_title.'】所管理的資料庫為基礎，系統會自動產生問題。</p>
			<p>如果您發現問題中有錯誤，<a href="'.$url.'" target="_blank" rel="noopener noreferrer">請按一下這裡</a>。</p>
		</div>'

	];

	return $arr_str_quizContentsNoticeContainer[$int_selected_language];
}


/******************************************************
 *  HTML QIUZ INTERACTION
 *  
 ******************************************************/
function build_html_quiz_interaction_input($pageType, $int_quiz, $arr_correct_answers, $str_explanation, $str_original_sentence, $str_furigana, $str_hint_button, $data_level, $data_jc, $data_inf, $int_selected_language){

	global
		$str_quizInputPlaceholder,
		$arr_str_button_caption_show_answer;

    $str_correct_answer_japanese = $arr_correct_answers['japanese'];
    $str_correct_answer_kana = $arr_correct_answers['kana'];

    $str_quiz_input = '<input type="text" name="quizAnswer" class="quizInput wiseUiFontSizeTarget" placeholder="'.escape_html($str_quizInputPlaceholder[$int_selected_language]).'">';

    $str_answer_button =
        '<button class="quizCheckAnswerButtonInput quizCheckAnswerButton quizItemButton"'
        .' data-quiz-type="'.intval($int_quiz).'"'
        .' data-explanation="'.escape_html($str_explanation).'"'
        .' data-correct-answer-japanese="'.escape_html($str_correct_answer_japanese).'"'
        .' data-correct-answer-kana="'.escape_html($str_correct_answer_kana).'"'
        .' data-japanese="'.escape_html($str_original_sentence).'"'
        .' data-furigana="'.escape_html($str_furigana).'"'
        .' data-quiz-source-page="'.escape_html($pageType).'"'
        .$data_level.$data_jc.$data_inf.'>'
        .escape_html($arr_str_button_caption_show_answer[$int_selected_language]).'</button>';

    return
        '<div class="quizInteraction">'
        .$str_quiz_input
        .$str_answer_button
        .$str_hint_button
        .'</div>';
}


function build_html_quiz_interaction_choices($pageType, $int_quiz, $str_correct_answer, $str_distractor_answer, $str_explanation, $str_original_sentence, $str_furigana, $str_hint_button, $data_level, $data_jc, $data_inf, $int_selected_language){

	global
	    $arr_str_button_caption_show_answer;

    $str_correct_answer_label =
        '<label class="quizChoice wiseUiFontSizeTarget">'
        .'<input type="radio" name="quizAnswer" value="correct" data-answer="'.escape_html($str_correct_answer).'">'
        .escape_html($str_correct_answer)
        .'</label>';

    $str_distractor_answer_label =
        '<label class="quizChoice wiseUiFontSizeTarget">'
        .'<input type="radio" name="quizAnswer" value="distractor" data-answer="'.escape_html($str_distractor_answer).'">'
        .escape_html($str_distractor_answer)
        .'</label>';

    $choices = [$str_correct_answer_label, $str_distractor_answer_label];
    shuffle($choices);

    $str_quiz_choices =
        '<div class="quizChoices">'
        .$choices[INDEX_FIRST]
        .$choices[INDEX_SECOND]
        .'</div>';

    $str_answer_button =
        '<button class="quizCheckAnswerButtonChoices quizCheckAnswerButton quizItemButton"'
        .' data-quiz-type="'.intval($int_quiz).'"'
        .' data-explanation="'.escape_html($str_explanation).'"'
        .' data-japanese="'.escape_html($str_original_sentence).'"'
        .' data-furigana="'.escape_html($str_furigana).'"'
        .' data-quiz-source-page="'.escape_html($pageType).'"'
        .$data_level.$data_jc.$data_inf.'>'
        .escape_html($arr_str_button_caption_show_answer[$int_selected_language]).'</button>';

    return
        '<div class="quizInteraction">'
        .$str_quiz_choices
        .$str_answer_button
        .$str_hint_button
        .'</div>';
}


function build_html_quiz_interaction_multiple_choices($pageType, $int_quiz, $str_correct_answer, $arr_distractor_answers, $str_explanation, $str_original_sentence, $str_furigana, $str_hint_button, $data_level, $data_jc, $data_inf, $int_selected_language){
	global
		$int_arr_distractor_answers_max_count,
		$arr_str_button_caption_show_answer;

    $str_correct_answer_label =
        '<label class="quizChoice wiseUiFontSizeTarget">'
        .'<input type="radio" name="quizAnswer" value="correct" data-answer="'.escape_html($str_correct_answer).'">'
        .escape_html($str_correct_answer)
        .'</label>';

    $choices = [$str_correct_answer_label];

    shuffle($arr_distractor_answers);
    $arr_distractor_answers = array_slice($arr_distractor_answers, 0, $int_arr_distractor_answers_max_count);

    foreach ($arr_distractor_answers as $loop_distractor_answers) {
        $ans = $loop_distractor_answers['answer'];
        $choices[] =
            '<label class="quizChoice wiseUiFontSizeTarget">'
            .'<input type="radio" name="quizAnswer" value="distractor" data-answer="'.escape_html($ans).'">'
            .escape_html($ans)
            .'</label>';
    }

    shuffle($choices);

    $str_quiz_choices =
        '<div class="quizChoices">'.implode('', $choices).'</div>';

    $str_answer_button =
        '<button class="quizCheckAnswerButtonChoices quizCheckAnswerButton quizItemButton"'
        .' data-quiz-type="'.intval($int_quiz).'"'
        .' data-explanation="'.escape_html($str_explanation).'"'
        .' data-japanese="'.escape_html($str_original_sentence).'"'
        .' data-furigana="'.escape_html($str_furigana).'"'
        .' data-quiz-source-page="'.escape_html($pageType).'"'
        .$data_level.$data_jc.$data_inf.'>'
        .escape_html($arr_str_button_caption_show_answer[$int_selected_language]).'</button>';

    return
        '<div class="quizInteraction">'
        .$str_quiz_choices
        .$str_answer_button
        .$str_hint_button
        .'</div>';
}



/******************************************************
 *  HTML QIUZ UI 
 *  
 ******************************************************/

function build_html_quiz_controls($str_next_button, $str_open_settings_screen_button, $str_open_history_screen_button, $str_zoom_big_button, $str_zoom_small_button, $str_copy_button, $int_selected_language){

	$str_quiz_controls = '';

	// デバッグ tempo コピーのjavascriptを直したら消す
	$str_copy_button = '';
	// デバッグ tempo コピーのjavascriptを直したら消す
	
	$str_quiz_controls = '
	<div class="quizControls">'.
		$str_next_button.
		$str_open_settings_screen_button.
		$str_open_history_screen_button.
		$str_zoom_big_button.
		$str_zoom_small_button.
		$str_copy_button.'
	</div>';
	
	return $str_quiz_controls;
}




function build_html_quiz_feedback_screen($pageType, $str_next_button_label, $int_selected_language)
{
    $str_html_overlay_close_button = build_html_overlay_close_button();

    $str_quizFeedbackScreen =
        '<div id="quizFeedbackScreen" class="quizScreenModal">' .
            $str_html_overlay_close_button .
            build_html_quiz_feedback_contents($pageType, $str_next_button_label, $int_selected_language) .
        '</div>';

    return '<div id="quizOverlayFeedback" class="quizOverlay">' . $str_quizFeedbackScreen . '</div>';
}

function build_html_quiz_feedback_contents($pageType, $str_next_button_label, $int_selected_language)
{
    $str_button = '';

    if (empty($str_next_button_label)) {
        $str_button = '<button id="quizFeedbackScreenNextButton" class="quizContentsButton hidden" data-page-type="' . escape_html($pageType) . '">' . $str_next_button_label . '</button>';
    }
    else {
        $str_button = '<button id="quizFeedbackScreenNextButton" class="quizContentsButton" data-page-type="' . escape_html($pageType) . '">' . $str_next_button_label . '</button>';
    }

    return '
        <h2 id="quizFeedbackScreenTitle"></h2>
        <div class="modalScrollableContainer">
            <section id="quizFeedbackScreenSectionMessages" class="quizScreenSection">
                <div id="quizFeedbackScreenContentMessageYourAnswer" class="quizScreenContainerContent quizFeedbackScreenSectionMessage wiseUiFontSizeTarget"></div>
                <div id="quizFeedbackScreenContentMessageCorrectAnswer" class="quizScreenContainerContent quizFeedbackScreenSectionMessage wiseUiFontSizeTarget"></div>
                <div id="quizFeedbackScreenContentMessageJapanese" class="quizScreenContainerContent quizFeedbackScreenSectionMessage wiseUiFontSizeTarget"></div>
                <div id="quizFeedbackScreenContentMessageFurigana" class="quizScreenContainerContent quizFeedbackScreenSectionMessage wiseUiFontSizeTarget"></div>
            </section>
            <section id="quizFeedbackScreenSectionExplanation" class="quizScreenSection">
                <div id="quizFeedbackScreenContentExplanationContainer">
                    <h3 id="quizFeedbackScreenSectionExplanationTitle"></h3>
                    <ul id="quizFeedbackScreenContentExplanationUl"></ul>
                    <div id="quizFeedbackScreenContentExplanationToGrammarButtonsContainer"></div>
                </div>
            </section>
            <section id="quizFeedbackScreenSectionAdvertisement" class="quizScreenSection hidden">
                <div id="quizFeedbackScreenAdvertisementContainer"></div>
            </section>
            <section class="quizScreenSection quizScreenButtonsSection">
                <div>' .
                    $str_button .
                '</div>
            </section>
        </div>';
}


function build_html_quiz_history_screen($int_selected_language){
	
	global
		$str_quizHistoryScreenTitle;

	$str_html_overlay_close_button = build_html_overlay_close_button();

	$str_quiz_history_screen = '';

	// マジックナンバー
	$str_quiz_history_screen =
	'<div id="quizHistoryScreen" class="quizScreenModal">'.
		$str_html_overlay_close_button.'
		<h2>'.$str_quizHistoryScreenTitle[$int_selected_language].'</h2>
		<div id="quizHistoryScreenTableContainer" class="modalScrollableContainer">
		</div>
	</div>';
	$str_quiz_history_screen = '<div id="quizOverlayHistory" class="quizOverlay">'.$str_quiz_history_screen.'</div>';

	return $str_quiz_history_screen;
}



/******************************************************
 *  HTML SORTING QIUZ 
 *  
 ******************************************************/

function build_html_sorting_quiz_menubar_tools($unique_code, $int_selected_language){

	global
		$arr_str_button_caption_submit,
		$str_quizMenuBarButtonReStart,
		$str_quizMenuBarButtonOpenUsersManual;

	$str_html = '';
	
	$html_zoom_in_icon  = build_html_magnifier_icon('plus');
	$html_zoom_out_icon = build_html_magnifier_icon('minus');

	$str_html =
		'<div id="sortingQuizMenuBarTools">
			<div id="sortingQuizMenuBarButtonConfirm" class="quizContentsButton quizContentsButtonConfirm" data-unique-code="'.$unique_code.'">'.$arr_str_button_caption_submit[$int_selected_language].'</div>
			<div id="sortingQuizMenuBarButtonReStart"
				class="quizContentsButton"
				data-action="quiz:sortingQuiz:restart"
				data-action-target="quizOverlayUsersManual">'.
				$str_quizMenuBarButtonReStart[$int_selected_language].
			'</div>
			<div id="sortingQuizMenuBarButtonOpenUsersManual"
				class="quizContentsButton"
				data-action="quiz:sortingQuiz:usersManual:show"
				data-action-target="quizOverlayUsersManual">'.
				$str_quizMenuBarButtonOpenUsersManual[$int_selected_language].
			'</div>
			<div id="sortingQuizMenuBarButtonChangeSizeContainer">
				<div id="sortingQuizMenuBarButtonChangeSizeBig" class="quizContentsButton">'.$html_zoom_in_icon.'</div>
				<div id="sortingQuizMenuBarButtonChangeSizeSmall" class="quizContentsButton">'.$html_zoom_out_icon.'</div>
			</div>
		</div>';

	return $str_html;
}


function build_html_sorting_quiz_fullscreen_menubar_tools($unique_code, $int_selected_language){

	global
		$arr_str_button_caption_submit,
		$str_quizMenuBarButtonReStart,
		$str_quizButtonFinishQuiz,
		$str_quizMenuBarButtonOpenUsersManual;

	$str_html = '';
	
	// マジックナンバー
	$html_zoom_in_icon = build_html_magnifier_icon('plus');
	$html_zoom_out_icon = build_html_magnifier_icon('minus');

	$str_html =
		'<div id="sortingQuizMenuBarTools">
			<div id="sortingQuizMenuBarButtonConfirm" class="quizContentsButton quizContentsButtonConfirm" data-unique-code="'.$unique_code.'">'.$arr_str_button_caption_submit[$int_selected_language].'</div>
			<div id="sortingQuizMenuBarButtonReStart"
				class="quizContentsButton"
				data-action="quiz:sortingQuiz:restart"
				data-action-target="quizOverlayUsersManual">'.
				$str_quizMenuBarButtonReStart[$int_selected_language].'
			</div>
			<div class="sortingQuizMenuBarButtonFinishQuiz quizContentsButton">'.$str_quizButtonFinishQuiz[$int_selected_language].'</div>
			<div id="sortingQuizMenuBarButtonOpenUsersManual"
				class="quizContentsButton"
				data-action="quiz:sortingQuiz:usersManual:show"
				data-action-target="quizOverlayUsersManual">'.
				$str_quizMenuBarButtonOpenUsersManual[$int_selected_language].'
			</div>
			<div id="sortingQuizMenuBarButtonChangeSizeContainer">
				<div id="sortingQuizMenuBarButtonChangeSizeBig" class="quizContentsButton">'.$html_zoom_in_icon.'</div>
				<div id="sortingQuizMenuBarButtonChangeSizeSmall" class="quizContentsButton">'.$html_zoom_out_icon.'</div>
			</div>
		</div>';

	return $str_html;
}


function build_html_sorting_quiz_piece_list_container($isFlexible, $str_sortingQuizPieceListContainerLi, $int_selected_language){

	$str_sortingQuizPieceListContainer = '';
	$str_sortingQuizPieceListContainer = '<ul id="sortingQuizPieceListContainerUl">'.$str_sortingQuizPieceListContainerLi.'</ul>';

	if($isFlexible){
		$str_sortingQuizPieceListContainer = '<div id="sortingQuizPieceListContainer" class="sortingQuizFlex">'.$str_sortingQuizPieceListContainer.'</div>';
	}
	else{
		$str_sortingQuizPieceListContainer = '<div id="sortingQuizPieceListContainer">'.$str_sortingQuizPieceListContainer.'</div>';

	}
	return $str_sortingQuizPieceListContainer;
}


function build_html_sorting_quiz_inflection_screen($int_selected_language){

	global
		$str_quizSuccessScreenTitle;

	$str_html_overlay_close_button = build_html_overlay_close_button();

	$str_sorting_inflectionScreen = '';
	$str_sorting_inflectionScreen =
	'<div id="quizInflectionScreen" class="quizScreenModal">'.
		$str_html_overlay_close_button.'
		<h2>'.$str_quizSuccessScreenTitle[$int_selected_language].'</h2>
		<div class="modalScrollableContainer">
			<section class="quizScreenSection">
				<div id="quizInflectionScreenContainer" class="quizScreenContainer">' .
					build_html_loading_spinner('quizInflectionScreenLoading') .
					'<ul id="quizInflectionScreenUl">
					</ul>
				</div>
			</section>
		</div>
	</div>';
	$str_sorting_inflectionScreen = '<div id="quizOverlayInflection" class="quizOverlay">'.$str_sorting_inflectionScreen.'</div>';

	return $str_sorting_inflectionScreen;
}


function build_html_sorting_quiz_success_screen($str_next_button, $str_finish_button, $int_selected_language){

	global
		$str_quizSuccessScreenCorrect,
		$str_quizSuccessScreenExpectedAnswerTitle,
		$str_quizSuccessScreenYourAnswerTitle,
		$str_quizSuccessScreenYourAnswerFuriganaTitle;

	$str_html_overlay_close_button = build_html_overlay_close_button();

	$str_sorting_successScreen = '';
	$str_sorting_successScreen =
	'<div id="quizSuccessScreen" class="quizScreenModal">'.
		$str_html_overlay_close_button.'
		<h2>'.$str_quizSuccessScreenCorrect[$int_selected_language].'</h2>
		<div class="modalScrollableContainer">
			<section class="quizScreenSection">
				<div id="quizSuccessScreenExpectedAnswer" class="quizScreenContainer">
					<div id="quizSuccessScreenExpectedAnswerTitle" class="quizScreenContainerTitle">「'.$str_quizSuccessScreenExpectedAnswerTitle[$int_selected_language].'」</div>
					<div id="quizSuccessScreenExpectedAnswerContent" class="quizScreenContainerContent wiseUiFontSizeTarget"></div>
				</div>
				<div id="quizSuccessScreenYourAnswer" class="quizScreenContainer">
					<div id="quizSuccessScreenYourAnswerTitle" class="quizScreenContainerTitle">「'.$str_quizSuccessScreenYourAnswerTitle[$int_selected_language].'」</div>
					<div id="quizSuccessScreenYourAnswerContent" class="quizScreenContainerContent wiseUiFontSizeTarget"></div>
				</div>
				<div id="quizSuccessScreenYourAnswerFurigana" class="quizScreenContainer">
					<div id="quizSuccessScreenYourAnswerFuriganaTitle" class="quizScreenContainerTitle">「'.$str_quizSuccessScreenYourAnswerFuriganaTitle[$int_selected_language].'」</div>
					<div id="quizSuccessScreenYourAnswerFuriganaContent" class="quizScreenContainerContent wiseUiFontSizeTarget"></div>
				</div>
			</section>
			<section class="quizScreenSection quizScreenButtonsSection">
				<div>'.
					$str_next_button.
					$str_finish_button.'
				</div>
			</section>
		</div>
	</div>';
	$str_sorting_successScreen = '<div id="quizOverlaySuccess" class="quizOverlay">'.$str_sorting_successScreen.'</div>';

	return $str_sorting_successScreen;
}


function build_html_sorting_quiz_failure_screen($str_next_button, $str_finish_button, $int_selected_language){

	global
		$str_quizFailureScreen,
		$str_quizFailureScreenMistakeMessage,
		$str_quizFailureScreenInflectionHint,
		$str_quizFailureScreenButtonChallengeAgain,
		$arr_str_button_caption_show_answer,
		$str_quizFailureScreenCorrectAnswerTitle;

	$str_html_overlay_close_button = build_html_overlay_close_button();

	$str_sorting_failureScreen = '';
	$str_sorting_failureScreen =
	'<div id="quizFailureScreen" class="quizScreenModal">'.
		$str_html_overlay_close_button.'
		<h2>'.$str_quizFailureScreen[$int_selected_language].'</h2>
		<div class="modalScrollableContainer">
			<section id="quizFailureScreenSectionMessages" class="quizScreenSection">
				<div id="quizFailureScreenMistakeMessage" class="quizScreenContainerContent wiseUiFontSizeTarget">'.$str_quizFailureScreenMistakeMessage[$int_selected_language].'</div>
				<div id="quizFailureScreenInflectionHint" class="quizScreenContainerContent wiseUiFontSizeTarget hidden">'.$str_quizFailureScreenInflectionHint[$int_selected_language].'</div>
			</section>
			<section id="quizFailureScreenSectionButtons" class="quizScreenSection quizScreenButtonsSection">
				<button id="quizFailureScreenButtonChallengeAgain" class="quizScreenButton">'.$str_quizFailureScreenButtonChallengeAgain[$int_selected_language].'</button>
				<button id="quizFailureScreenButtonShowCorrectAnswer" class="quizScreenButton">'.$arr_str_button_caption_show_answer[$int_selected_language].'</button>
			</section>
			<section id="quizFailureScreenSectionCorrectAnswer" class="quizScreenSection hidden">
				<div id="quizFailureScreenCorrectAnswer" class="quizScreenContainer">
					<div id="quizFailureScreenCorrectAnswerTitle" class="quizScreenContainerTitle">「'.$str_quizFailureScreenCorrectAnswerTitle[$int_selected_language].'」</div>
					<div id="quizFailureScreenCorrectAnswerContent" class="quizScreenContainerContent wiseUiFontSizeTarget"></div>
				</div>
				<div>'.
					$str_next_button.
					$str_finish_button.'
				</div>
			</section>
		</div>
	</div>';
	$str_sorting_failureScreen = '<div id="quizOverlayFailure" class="quizOverlay">'.$str_sorting_failureScreen.'</div>';

	return $str_sorting_failureScreen;
}


function build_html_sorting_quiz_users_manual_screen($pageType, $isAdvanceStage, $int_selected_language){

	global
		$arr_str_button_caption_exit,
		$str_sortingQuizPieceListContainerLiButtonsInflection;

	$str_html_overlay_close_button = build_html_overlay_close_button();

	$str_usersManualScreen = '';
	$arr_usersManual = [];

	// マジックナンバー
	if($isAdvanceStage){
		$arr_usersManual = [
			'
			<section class="quizUsersManualScreenSection">
				<h2>これは並べ替えクイズです。単語を並べ替えて正しい文章を作ってください。</h2>
				<ul class="quizUsersManualScreenSectionUl">
					<li>左側にある単語をクリックして右側へ移動させてください。</li>
					<li>間違えた場合は右側の単語をクリックしてください。左へ戻ります。</li>
				</ul>
			</section>
			<section class="quizUsersManualScreenSection">
				<ul class="quizUsersManualScreenSectionUl">
					<li>語順だけではなく、「語形変化」にも注意しましょう。単語を正しい形に変換してください。</li>
					<li>1.「'.$str_sortingQuizPieceListContainerLiButtonsInflection[$int_selected_language].'」ボタンを押します</li>
					<li>2.正しい語形を選んでください</li>
					<li>例：です → じゃありません(否定)</li>
				</ul>
			</section>
			<section class="quizUsersManualScreenSection">
				<ul class="quizUsersManualScreenSectionUl">
					<li>終わったら、「送信」ボタンを押してください。結果が表示されます。</li>
				</ul>
			</section>
			'
			,
			'
			<section class="quizUsersManualScreenSection">
				<h2>這是一個重新排序測驗。請重新排列單詞以創建正確的句子。</h2>
				<ul class="quizUsersManualScreenSectionUl">
					<li>請單擊左側的單詞,使單詞移至右側。</li>
					<li>按錯的話,請單擊右側單詞。單詞會回左側。</li>
				</ul>
			</section>
			<section class="quizUsersManualScreenSection">
				<ul class="quizUsersManualScreenSectionUl">
					<li>不僅要注意詞序，也要注意「詞形變化」。請將單詞轉換為正確的詞形。</li>
					<li>1.按「'.$str_sortingQuizPieceListContainerLiButtonsInflection[$int_selected_language].'」按鈕</li>
					<li>2.選擇正確的詞形</li>
					<li>例：です → じゃありません(否定)</li>
				</ul>
			</section>
			<section class="quizUsersManualScreenSection">
				<ul class="quizUsersManualScreenSectionUl">
					<li>完成後，請按「提交」按鈕。將顯示結果。</li>
				</ul>
			</section>
			'
		];
	}
	else{
		$arr_usersManual = [
			'
			<section class="quizUsersManualScreenSection">
				<h2>これは並べ替えクイズです。単語を並べ替えて正しい文章を作ってください。</h2>
				<ul class="quizUsersManualScreenSectionUl">
					<li>左側にある単語をクリックして右側へ移動させてください。</li>
					<li>間違えた場合は右側の単語をクリックしてください。左へ戻ります。</li>
					<li>終わったら、「送信」ボタンを押してください。結果が表示されます。</li>
				</ul>
			</section>
			'
			,
			'
			<section class="quizUsersManualScreenSection">
				<h2>這是一個重新排序測驗。請重新排列單詞以創建正確的句子。</h2>
				<ul class="quizUsersManualScreenSectionUl">
					<li>請單擊左側的單詞,使單詞移至右側。</li>
					<li>按錯的話,請單擊右側單詞。單詞會回左側。</li>
					<li>完成後，請按「提交」按鈕。將顯示結果。</li>
				</ul>
			</section>
			'
		];
	}
	

	$str_usersManualScreen =
	'<div id="quizUsersManualScreen" class="quizScreenModal">'.
		$str_html_overlay_close_button.
		'<div class="modalScrollableContainer">'.
			$arr_usersManual[$int_selected_language].'
			<section class="quizScreenSection quizScreenButtonsSection">
				<button id="quizUsersManualScreenButtonClose" class="quizScreenButton">'.$arr_str_button_caption_exit[$int_selected_language].'</button>
			</section>
		</div>
	</div>';

	if (!isset($_SESSION['quizOverlayUsersManual']) && ($pageType === 'quiz')) {
		$str_usersManualScreen = '<div id="quizOverlayUsersManual" class="quizOverlay overlay-on">'.$str_usersManualScreen.'</div>';
		$_SESSION['quizOverlayUsersManual'] = 'seen';
	}
	else{
		$str_usersManualScreen = '<div id="quizOverlayUsersManual" class="quizOverlay">'.$str_usersManualScreen.'</div>';
	}

	return $str_usersManualScreen;
}



/******************************************************
 *  DATA 
 *  
 ******************************************************/
function get_data_plainform_quiz($pageType, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $arr_japanese_classification, $int_selected_language){

	global
		$int_quiz_plainformQuiz,
		$t_masta_japanese_classification,
		$int_masta_japanese_classification_id_verb,
		$t_masta_japanese_root,
		$t_japanese_labels,
		$str_column_masta_japanese_label_id,
		$t_japanese_elements,
		$t_masta_japanese_sub_category,
		$t_masta_japanese_sub_classification,
		$int_masta_japanese_category_id_word,
		$str_column_main_label,
		$arr_columns_masta_japanese_root,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$str_inflected_label_inflection_process,
		$str_column_label_japanese,
		$str_column_label_kana,
		$int_affirmativeNotPastTense,
		$int_negativeNotPastTense,
		$int_affirmativePastTense,
		$int_negativePastTense,
		$int_OriginalForm,
		$int_NaiForm,
		$int_TaForm,
		$int_NakattaForm,
		$str_stages_of_inflection_get_baseform_japanese,
		$str_stages_of_inflection_get_baseform_kana,
		$arr_affirmative_negative_tense,
		$str_quizMessageQuestionInput,
		$str_quizMessageQuestionWhich;

	$str_html = '';
	$int_quiz = $int_quiz_plainformQuiz;

	$arr_quiz_items = [];
	$str_send_question = '';
	$str_send_answer = '';
	$arr_inflection = [];

	$arr_strSQL_where_japanese_classification = [];
	if (!empty($arr_japanese_classification)) {
		$last_key_japanese_classification = end(array_keys($arr_japanese_classification));
		foreach($arr_japanese_classification as $key => $item){
			if ($key === $last_key_japanese_classification) {
				$arr_strSQL_where_japanese_classification[] = [$t_masta_japanese_classification,'id','=',intval($item),'PDO::PARAM_INT',''];
			} else {
				$arr_strSQL_where_japanese_classification[] = [$t_masta_japanese_classification,'id','=',intval($item),'PDO::PARAM_INT','Or'];
			}
		}
	}
	else{
		$arr_strSQL_where_japanese_classification = [
			[$t_masta_japanese_classification,'id','=',$int_masta_japanese_classification_id_verb,'PDO::PARAM_INT','']
		];
	}

	$t_masta_japanese_root_2 = $t_masta_japanese_root.'_2';
	 
	$arr_strSQL_select = [
		[$t_japanese_labels,'id'],
		[$t_japanese_labels,'japanese_element_id'],
		[$t_japanese_labels,$str_column_masta_japanese_label_id],
		[$t_japanese_elements,'masta_japanese_root_id'],
		[$t_japanese_elements,'masta_japanese_sub_classification_id'],
		[$t_japanese_elements,'voice_id'],
		[$t_masta_japanese_root_2,$arr_columns_masta_japanese_root[$int_selected_language]]
	];

	$strSQL_from = " FROM
					(
						(
							(
								(
									(
										$t_japanese_labels
										INNER JOIN $t_japanese_elements
										ON
										$t_japanese_labels.japanese_element_id = $t_japanese_elements.id
									)
									INNER JOIN $t_masta_japanese_root
									ON
									$t_japanese_elements.masta_japanese_root_id = $t_masta_japanese_root.id
								)
								INNER JOIN $t_masta_japanese_sub_category
								ON
								$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
							)
							INNER JOIN $t_masta_japanese_sub_classification
							ON
							$t_japanese_elements.masta_japanese_sub_classification_id = $t_masta_japanese_sub_classification.id
						)
						INNER JOIN $t_masta_japanese_classification
						ON
						$t_masta_japanese_sub_classification.classification_id = $t_masta_japanese_classification.id
					)
					INNER JOIN $t_masta_japanese_root as $t_masta_japanese_root_2
					ON
					$t_masta_japanese_classification.masta_japanese_root_id = $t_masta_japanese_root_2.id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_word,'PDO::PARAM_INT','']
			],
			'And'
		],
		[
			[
				[$t_japanese_labels,$str_column_main_label,'=',FLAG_TRUE,'PDO::PARAM_INT','']
			],
			'And'
		],
		[
			$arr_strSQL_where_japanese_classification,
			''
		]                
	];

	$arr_japanese_labels = get_data_quiz_target($int_quiz, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $int_selected_language);

	if (empty($arr_japanese_labels)) {
		// マジックナンバー
		$str_quiz_prompt = 'Not Record In plainform_quiz';
		$str_html = build_html_quiz_main_section($pageType, $int_quiz, $arr_quiz_items, $str_quiz_prompt, $str_send_question, $str_send_answer, $int_mastery_level, $arr_japanese_classification, $arr_inflection, $int_selected_language);
		$str_quiz_history_prompt = '';

		return array($str_html, $str_quiz_history_prompt);
	}
	
	$t_japanese_labels_id = intval($arr_japanese_labels['id']);
	$t_japanese_element_id = intval($arr_japanese_labels['japanese_element_id']);
	$t_masta_japanese_root_id = intval($arr_japanese_labels['masta_japanese_root_id']);
	$t_masta_japanese_sub_classification_id = intval($arr_japanese_labels['masta_japanese_sub_classification_id']);
	$int_voice_id = intval($arr_japanese_labels['voice_id']);
	$str_classification = escape_html_with_nl2br($arr_japanese_labels[$arr_columns_masta_japanese_root[$int_selected_language]]);
	$int_masta_japanese_label_id = intval($arr_japanese_labels[$str_column_masta_japanese_label_id]);

	$arr_indicator_labels = get_arr_indicator_label($t_japanese_labels_id, false, $int_selected_language);

	$arr_plainform = [
		$int_affirmativeNotPastTense => $int_OriginalForm,
		$int_negativeNotPastTense => $int_NaiForm,
		$int_affirmativePastTense => $int_TaForm,
		$int_negativePastTense => $int_NakattaForm
	];
	
	foreach($arr_plainform as $key_plainform => $t_masta_form_root_id){
		
		$str_plainformQuizTableTrAdd = '';
		$str_plainformQuizTableTdPlain = '';
		$str_plainformQuizTableTdTitle = '';
		
		$arr_inflected_label = get_arr_inflected_label($arr_indicator_labels, $t_masta_japanese_root_id, $t_japanese_element_id, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_voice_id, true, $int_selected_language);

		$str_correct_answer_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
		$str_correct_answer_kana = $arr_inflected_label[$str_snake_to_camel_kana];
		$str_correct_answer = $str_correct_answer_japanese;

		list($isInflectionFound, $str_distractor_answer, $str_distractor_answer_kana) = generate_data_distractor_answer_by_sub_classification($str_correct_answer, $arr_indicator_labels[$str_column_label_japanese], $arr_indicator_labels[$str_column_label_kana], $t_masta_japanese_root_id, $t_masta_form_root_id, $int_voice_id, $int_masta_japanese_label_id, $int_selected_language);

		$arr_explanation = $arr_inflected_label[$str_inflected_label_inflection_process];
		$str_explanation = '';
		foreach($arr_explanation as $loop_explanation){
			$str_explanation_add = $loop_explanation['explanation'];
			if($loop_explanation === reset($arr_explanation)){
				$str_explanation = $str_explanation_add;
			}
			else{
				$str_explanation = $str_explanation.','.$str_explanation_add;
			}
		}

		$str_original_sentence = '';
		$str_furigana = '';
		
		$arr_correct_answers = [
			'japanese' => $str_correct_answer_japanese,
			'kana' => $str_correct_answer_kana
		];
		$arr_distractor_answers = [];
		$arr_answer_information = [
			'strings' => [
				'str_correct_answer' => $str_correct_answer,
				'str_distractor_answer' => $str_distractor_answer
			],
			'arrays' => [
				'arr_correct_answers' => $arr_correct_answers,
				'arr_distractor_answers' => $arr_distractor_answers
			]
		];
		$arr_explanation_information = [
			'str_explanation' => $str_explanation,
			'str_original_sentence' => $str_original_sentence,
			'str_furigana' => $str_furigana
		];

		$arr_quiz_items[] = [
			 'stages'=>[
				[
					'stage_type' => 'input',
					'translation' => '',
					'questions' => [
						'question' => '「'.$arr_affirmative_negative_tense[$key_plainform][$int_selected_language].'」: '.$str_quizMessageQuestionInput[$int_selected_language],
						'item' => ''
					],
					'arr_answer_information' => $arr_answer_information,
					'arr_explanation_information' => $arr_explanation_information
				],
				[
					'stage_type' => 'choices',
					'translation' => '',
					'questions' => [
						'question' => '「'.$arr_affirmative_negative_tense[$key_plainform][$int_selected_language].'」: '.$str_quizMessageQuestionWhich[$int_selected_language],
						'item' => ''
					],
					'arr_answer_information' => $arr_answer_information,
					'arr_explanation_information' => $arr_explanation_information
				]
			]
		];

		$str_send_answer = $str_send_answer.'「'.$arr_affirmative_negative_tense[$key_plainform][$int_selected_language].': '.$str_correct_answer.' / '.$str_distractor_answer.'」';
	}

	
	$str_japanese_polite = $arr_inflected_label[$str_stages_of_inflection_get_baseform_japanese];
	$str_kana_polite = $arr_inflected_label[$str_stages_of_inflection_get_baseform_kana];

	$str_plainformQuizChartHeader = $str_japanese_polite.' ('.$str_kana_polite.')';
	if($str_japanese_polite === $str_kana_polite){
		$str_plainformQuizChartHeader = $str_japanese_polite;
	}

	$str_quiz_prompt = $str_plainformQuizChartHeader;
	$str_send_question = $str_plainformQuizChartHeader;

	$str_html = build_html_quiz_main_section($pageType, $int_quiz, $arr_quiz_items, $str_quiz_prompt, $str_send_question, $str_send_answer, $int_mastery_level, $arr_japanese_classification, $arr_inflection, $int_selected_language);

	$str_quiz_history_prompt = $str_japanese_polite;

	return array($str_html, $str_quiz_history_prompt);
}


function get_data_japanese_particle_quiz($pageType, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $int_selected_language){

	global
		$int_quiz_japaneseParticleQuiz,
		$arr_case_particles,
		$t_registered_sentence_elements,
		$t_japanese_labels,
		$t_japanese_elements,
		$t_registered_sentences,
		$t_masta_japanese_root,
		$t_masta_japanese_sub_category,
		$int_JapaneseParticle,
		$int_masta_japanese_category_id_grammar,
		$str_snake_to_camel_japanese_id,
		$arr_case_particles_reject_pairs,
		$str_quizMessageQuestionWhich;

	$int_quiz = $int_quiz_japaneseParticleQuiz;

	$arr_quiz_items = [];
	$str_send_question = '';
	$str_send_answer = '';
	$arr_japanese_classification = [];
	$arr_inflection = [];

	$str_html = '';
	$str_html_choices_ul = '';

	$flag = false;
	$i_seenJapaneseRootFound = COUNT_FIRST;

	while (!$flag) {

		if ($i_seenJapaneseRootFound > 3) {
			// マジックナンバー
			$str_quiz_prompt = 'Not Record In japanese_particle_quiz';
			$str_html = build_html_quiz_main_section($pageType, $int_quiz, $arr_quiz_items, $str_quiz_prompt, $str_send_question, $str_send_answer, $int_mastery_level, $arr_japanese_classification, $arr_inflection, $int_selected_language);
			$str_quiz_history_prompt = '';

			return array($str_html, $str_quiz_history_prompt);
		}
		
		$weighted_particles = [];
		foreach ($arr_case_particles as $item) {
			for ($i = INDEX_FIRST; $i < $item['weight']; $i++) {
				$weighted_particles[] = $item;
			}
		}
		
		$randomNumber = rand(0, count($weighted_particles) - 1);
		$arr_correct_answers = $weighted_particles[$randomNumber];

		$int_case_particles_id = $arr_correct_answers['masta_japanese_label_id'];
		$str_correct_answer = $arr_correct_answers['answer'];

		$arr_strSQL_select = [
			[$t_registered_sentence_elements,'registered_sentence_id'],
			[$t_registered_sentences,'unique_code'],
			[$t_registered_sentences,'masta_japanese_root_id'],
			[$t_registered_sentences,'sentence']
		];

		$strSQL_from = " FROM
						(
							(
							(
								(
									$t_registered_sentence_elements
									INNER JOIN $t_japanese_labels
									ON
									$t_registered_sentence_elements.label_id = $t_japanese_labels.id
								)
								INNER JOIN $t_japanese_elements
								ON
								$t_japanese_labels.japanese_element_id = $t_japanese_elements.id
							)
							INNER JOIN $t_registered_sentences
							ON
							$t_registered_sentence_elements.registered_sentence_id = $t_registered_sentences.id
							)
							INNER JOIN $t_masta_japanese_root
							ON
							$t_registered_sentences.masta_japanese_root_id = $t_masta_japanese_root.id
						)
						INNER JOIN $t_masta_japanese_sub_category
						ON
						$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
						";

		$arr_strSQL_where = [
			[
				[
					[$t_japanese_labels,'masta_japanese_label_id','=',$int_case_particles_id,'PDO::PARAM_INT','And'],
					[$t_japanese_elements,'masta_japanese_sub_classification_id','=',$int_JapaneseParticle,'PDO::PARAM_INT','And'],
					[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_grammar,'PDO::PARAM_INT','']
				],
				''
			]
		];

		$arr_search_registered_sentence_id = get_data_quiz_target($int_quiz, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $int_selected_language);

		if(!empty($arr_search_registered_sentence_id)){
			$flag = true;
		}

		++$i_seenJapaneseRootFound;
	}

	$int_registered_sentence_id = intval($arr_search_registered_sentence_id['registered_sentence_id']);
	$sentence_unique_code = escape_html($arr_search_registered_sentence_id['unique_code']);

	$arr_strSQL_select = [
		[$t_japanese_labels,'masta_japanese_label_id'],
		[$t_registered_sentence_elements,'japanese'],
		[$t_registered_sentence_elements,'kana'],
		[$t_registered_sentence_elements,'japanese_id as ' . $str_snake_to_camel_japanese_id]
	];

	$strSQL_from = " FROM
					$t_registered_sentence_elements
					INNER JOIN $t_japanese_labels
					ON
					$t_registered_sentence_elements.label_id = $t_japanese_labels.id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_registered_sentence_elements,'registered_sentence_id','=',$int_registered_sentence_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_registered_sentence_elements,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_registered_sentence_elements) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);
	
	$str_quiz_prompt = '';
	$str_send_question = '';
	
	$str_original_sentence = '';
	$str_furigana = '';

	foreach($arr_registered_sentence_elements as $loop_registered_sentence_elements){
		if($loop_registered_sentence_elements['masta_japanese_label_id'] == $int_case_particles_id){
			$str_japanese = ' <span class="colorChangerHighlightEmRed">[ ★ ]</span> ';
			$str_send_question_add = '[ ★ ]';
		}
		else{
			$str_japanese = $loop_registered_sentence_elements['japanese'];
			$str_send_question_add = $str_japanese;
		}
		$str_quiz_prompt = $str_quiz_prompt.$str_japanese;
		$str_send_question = $str_send_question.$str_send_question_add;
		
		$str_original_sentence = $str_original_sentence.$loop_registered_sentence_elements['japanese'].' ';
		$str_furigana = $str_furigana.$loop_registered_sentence_elements['kana'].' ';
	}

	$remove_ids = [$int_case_particles_id];

	foreach ($arr_case_particles_reject_pairs as $pair) {
		if (in_array($int_case_particles_id, $pair)) {
			$remove_ids = array_merge($remove_ids, $pair);
		}
	}
	$remove_ids = array_unique($remove_ids);
	$arr_distractor_answers = array_values(array_filter($arr_case_particles, function ($item) use ($remove_ids) {
		return !in_array($item['masta_japanese_label_id'], $remove_ids);
	}));

	$randomNumberDistractor = rand(0, count($arr_distractor_answers)-1);

	$int_case_particles_id_distractor = $arr_distractor_answers[$randomNumberDistractor]['masta_japanese_label_id'];
	$str_distractor_answer = $arr_distractor_answers[$randomNumberDistractor]['answer'];

	$str_send_answer = $str_correct_answer.' / '.$str_distractor_answer;

	$str_explanation = '';
	$user_level = get_user_level();
	if(is_teacher_level($user_level)){
		$str_explanation = $sentence_unique_code;
	}
	
	$arr_answer_information = [
		'strings' => [
			'str_correct_answer' => $str_correct_answer,
			'str_distractor_answer' => $str_distractor_answer
		],
		'arrays' => [
			'arr_correct_answers' => $arr_correct_answers,
			'arr_distractor_answers' => $arr_distractor_answers
		]
	];
	$arr_explanation_information = [
		'str_explanation' => $str_explanation,
		'str_original_sentence' => $str_original_sentence,
		'str_furigana' => $str_furigana
	];
	

	$str_quiz_translation = fetch_str_registered_sentence_answer_by_id($int_registered_sentence_id, $int_selected_language);
	$arr_quiz_items = [
		[
			'stages'=>[
				[
					'stage_type' => 'multiple_choices',
					'translation' => '',
					'questions' => [
						'question' => $str_quizMessageQuestionWhich[$int_selected_language],
						'item' => ''
					],
					'arr_answer_information' => $arr_answer_information,
					'arr_explanation_information' => $arr_explanation_information
				],
				[
					'stage_type' => 'multiple_choices',
					'translation' => $str_quiz_translation,
					'questions' => [
						'question' => $str_quizMessageQuestionWhich[$int_selected_language],
						'item' => ''
					],
					'arr_answer_information' => $arr_answer_information,
					'arr_explanation_information' => $arr_explanation_information
				],
				[
					'stage_type' => 'choices',
					'translation' => $str_quiz_translation,
					'questions' => [
						'question' => $str_quizMessageQuestionWhich[$int_selected_language],
						'item' => ''
					],
					'arr_answer_information' => $arr_answer_information,
					'arr_explanation_information' => $arr_explanation_information
				]
			]
		]
	];


	$str_html = build_html_quiz_main_section($pageType, $int_quiz, $arr_quiz_items, $str_quiz_prompt, $str_send_question, $str_send_answer, $int_mastery_level, $arr_japanese_classification, $arr_inflection, $int_selected_language);

	$str_quiz_history_prompt = escape_html($arr_search_registered_sentence_id['sentence']);

	return array($str_html, $str_quiz_history_prompt);
}


function get_data_grammar_quiz(
    $pageType,
    $int_mastery_level,
    $unique_code_type,
    $arr_grammar_unique_code,
    $arr_sub_category,
    $int_selected_language
){

    $pick = mt_rand(0, 1) === 1 ? 'fromJapaneseElements' : 'fromInflection';

    if ($pick === 'fromJapaneseElements') {
        list($ok, $html, $history) = try_get_data_quiz_from_japanese_elements($pageType, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $int_selected_language);
        if ($ok) {
            return array($html, $history);
        }
        list($ok2, $html2, $history2) = try_get_data_quiz_from_inflection($pageType, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $int_selected_language);
        if ($ok2) {
            return array($html2, $history2);
        }
    } else {
        list($ok, $html, $history) = try_get_data_quiz_from_inflection($pageType, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $int_selected_language);
        if ($ok) {
            return array($html, $history);
        }
        list($ok2, $html2, $history2) = try_get_data_quiz_from_japanese_elements($pageType, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $int_selected_language);
        if ($ok2) {
            return array($html2, $history2);
        }
    }

	global
		$int_quiz_grammarQuiz;

    $int_quiz = $int_quiz_grammarQuiz;
    $arr_quiz_items = [];
    $str_quiz_prompt = 'Not Record In grammar_quiz';
    $str_send_question = '';
    $str_send_answer = '';
    $arr_japanese_classification = [];
    $arr_inflection = [];

    $html = build_html_quiz_main_section($pageType, $int_quiz, $arr_quiz_items, $str_quiz_prompt, $str_send_question, $str_send_answer, $int_mastery_level, $arr_japanese_classification, $arr_inflection, $int_selected_language);
    return array($html, '');
}


function get_data_word_inflection_quiz($pageType, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $arr_japanese_classification, $arr_inflection, $int_selected_language){

	global
		$arr_inflection_for_quiz,
		$int_masta_japanese_category_id_word,
		$int_masta_japanese_classification_id_verb,
		$int_quiz_wordInflectionQuiz,
		$str_inflected_label_inflection_process,
		$str_column_label_japanese,
		$str_column_label_kana,
		$str_column_main_label,
		$str_column_masta_japanese_label_id,
		$str_stages_of_inflection_get_baseform_japanese,
		$str_stages_of_inflection_get_baseform_kana,
		$str_quizMessageQuestionInput,
		$str_quizMessageQuestionWhich,
		$str_snake_to_camel_form,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$str_snake_to_camel_voice,
		$t_japanese_elements,
		$t_japanese_labels,
		$t_masta_japanese_classification,
		$t_masta_japanese_root,
		$t_masta_japanese_sub_category,
		$t_masta_japanese_sub_classification;


	$str_word_inflection_quiz = '';
	$int_quiz = $int_quiz_wordInflectionQuiz;

	$arr_quiz_items = [];
	$str_send_question = '';
	$str_send_answer = '';
	
	$arr_strSQL_where_japanese_classification = [];
	if (!empty($arr_japanese_classification)) {
		$last_key_japanese_classification = end(array_keys($arr_japanese_classification));
		foreach($arr_japanese_classification as $key => $item){
			if ($key === $last_key_japanese_classification) {
				$arr_strSQL_where_japanese_classification[] = [$t_masta_japanese_classification,'id','=',intval($item),'PDO::PARAM_INT',''];
			} else {
				$arr_strSQL_where_japanese_classification[] = [$t_masta_japanese_classification,'id','=',intval($item),'PDO::PARAM_INT','Or'];
			}
		}
	}
	else{
		$arr_strSQL_where_japanese_classification = [
			[$t_masta_japanese_classification,'id','=',$int_masta_japanese_classification_id_verb,'PDO::PARAM_INT','']
		];
	}
	
	$availableForm = [];
	if (!empty($arr_inflection)) {
		$availableForm = $arr_inflection;
	} else {
		$availableForm = $arr_inflection_for_quiz;
	}

	$arr_strSQL_select = [
		[$t_japanese_labels,'id'],
		[$t_japanese_labels,'japanese_element_id'],
		[$t_japanese_labels,$str_column_masta_japanese_label_id],
		[$t_japanese_elements,'masta_japanese_root_id'],
		[$t_japanese_elements,'masta_japanese_sub_classification_id'],
		[$t_japanese_elements,'voice_id']
	];

	$strSQL_from = " FROM
					(
						(
							(
								(
									$t_japanese_labels
									INNER JOIN $t_japanese_elements
									ON
									$t_japanese_labels.japanese_element_id = $t_japanese_elements.id
								)
								INNER JOIN $t_masta_japanese_root
								ON
								$t_japanese_elements.masta_japanese_root_id = $t_masta_japanese_root.id
							)
							INNER JOIN $t_masta_japanese_sub_category
							ON
							$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
						)
						INNER JOIN $t_masta_japanese_sub_classification
						ON
						$t_japanese_elements.masta_japanese_sub_classification_id = $t_masta_japanese_sub_classification.id
					)
					INNER JOIN $t_masta_japanese_classification
					ON
					$t_masta_japanese_sub_classification.classification_id = $t_masta_japanese_classification.id
					";

	$arr_strSQL_where_label = [
		[
			[
				[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_word,'PDO::PARAM_INT','']
			],
			'And'
		],
		[
			[
				[$t_japanese_labels,$str_column_main_label,'=',FLAG_TRUE,'PDO::PARAM_INT','']
			],
			'And'
		],
		[
			$arr_strSQL_where_japanese_classification,
			''
		]                
	];

	$isJapaneseRootFound = false;
	$seenJapaneseRootFound = [];
	$i_seenJapaneseRootFound = COUNT_FIRST;

	while (!$isJapaneseRootFound) {

		if ($i_seenJapaneseRootFound > 3) {
			// マジックナンバー
			$str_quiz_prompt = 'Not Record In word_inflection_quiz';
			$str_word_inflection_quiz = build_html_quiz_main_section($pageType, $int_quiz, $arr_quiz_items, $str_quiz_prompt, $str_send_question, $str_send_answer, $int_mastery_level, $arr_japanese_classification, $arr_inflection, $int_selected_language);
			$str_quiz_history_prompt = '';

			return array($str_word_inflection_quiz, $str_quiz_history_prompt);
		}

		$arr_strSQL_where_seen_items = [];

		foreach($seenJapaneseRootFound as $item){
			if ($item === end($seenJapaneseRootFound)) {
				$arr_strSQL_where_seen_items[] = [$t_masta_japanese_root,'id','<>',$item,'PDO::PARAM_INT',''];
			} else {
				$arr_strSQL_where_seen_items[] = [$t_masta_japanese_root,'id','<>',$item,'PDO::PARAM_INT','And'];
			}
		}

		if(!empty($arr_strSQL_where_seen_items)){
			$arr_strSQL_where_seen = [
				[
					$arr_strSQL_where_seen_items,
					'And'
				]
			];
		}
		else{
			$arr_strSQL_where_seen = [];
		}
	
		$arr_strSQL_where = array_merge($arr_strSQL_where_seen, $arr_strSQL_where_label);

		$arr_japanese_labels = get_data_quiz_target($int_quiz, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $int_selected_language);

		++$i_seenJapaneseRootFound;

		if(empty($arr_japanese_labels)){
			continue;
		}
				
		$int_labels_id = intval($arr_japanese_labels['id']);
		$int_japanese_element_id = intval($arr_japanese_labels['japanese_element_id']);
		$int_japanese_id = intval($arr_japanese_labels['masta_japanese_root_id']);
		$int_sub_classification_id = intval($arr_japanese_labels['masta_japanese_sub_classification_id']);
		$int_voice_id = intval($arr_japanese_labels['voice_id']);
		$int_masta_japanese_label_id = intval($arr_japanese_labels[$str_column_masta_japanese_label_id]);

		$arr_indicator_labels = get_arr_indicator_label($int_labels_id, false, $int_selected_language);
		$str_japanese = $arr_indicator_labels[$str_column_label_japanese];
		$str_kana = $arr_indicator_labels[$str_column_label_kana];

		$int_form_id = intval($availableForm[array_rand($availableForm)]);

		$arr_inflected_label = get_arr_inflected_label($arr_indicator_labels, $int_japanese_id, $int_japanese_element_id, $int_sub_classification_id, $int_form_id, $int_voice_id, false, $int_selected_language);

		$str_japanese_polite = $arr_inflected_label[$str_stages_of_inflection_get_baseform_japanese];
		$str_kana_polite = $arr_inflected_label[$str_stages_of_inflection_get_baseform_kana];
		
		$str_correct_answer_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
		$str_correct_answer_kana = $arr_inflected_label[$str_snake_to_camel_kana];
		$str_correct_answer = $str_correct_answer_japanese;
		
		$str_form = $arr_inflected_label[$str_snake_to_camel_form] !== null ? $arr_inflected_label[$str_snake_to_camel_form] : '';
		$str_voice = $arr_inflected_label[$str_snake_to_camel_voice] !== null ? $arr_inflected_label[$str_snake_to_camel_voice] : '';

		if($str_voice !== ''){
			$str_inflection = $str_voice;
		}
		else{
			$str_inflection = $str_form;
		}

		list($isInflectionFound, $str_distractor_answer, $str_distractor_answer_kana) = generate_data_distractor_answer_by_sub_classification($str_correct_answer, $str_japanese, $str_kana, $int_japanese_id, $int_form_id, $int_voice_id, $int_masta_japanese_label_id, $int_selected_language);
		
		if($isInflectionFound){
			$isJapaneseRootFound = true;
			break;
		}        
		$seenJapaneseRootFound[] = $int_japanese_id;
	}

	$str_question = $str_japanese_polite.' ('.$str_kana_polite.')';
	if($str_japanese_polite === $str_kana_polite){
		$str_question = $str_japanese_polite;
	}
	$str_send_question = $str_question;
	$str_send_answer = $str_correct_answer.' / '.$str_distractor_answer;

	$arr_explanation = $arr_inflected_label[$str_inflected_label_inflection_process];
	$str_explanation = '';

	foreach($arr_explanation as $loop_explanation){
		$str_explanation_add = $loop_explanation['explanation'];
		if($loop_explanation === reset($arr_explanation)){
			$str_explanation = $str_explanation_add;
		}
		else{
			$str_explanation = $str_explanation.','.$str_explanation_add;
		}
	}
	
	$str_original_sentence = '';
	$str_furigana = '';
	
	$arr_correct_answers = [
		'japanese' => $str_correct_answer_japanese,
		'kana' => $str_correct_answer_kana
	];
	$arr_distractor_answers = [];
	$arr_answer_information = [
		'strings' => [
			'str_correct_answer' => $str_correct_answer,
			'str_distractor_answer' => $str_distractor_answer
		],
		'arrays' => [
			'arr_correct_answers' => $arr_correct_answers,
			'arr_distractor_answers' => $arr_distractor_answers
		]
	];
	$arr_explanation_information = [
		'str_explanation' => $str_explanation,
		'str_original_sentence' => $str_original_sentence,
		'str_furigana' => $str_furigana
	];
	
	$str_quiz_translation = '';
	$arr_quiz_items = [
		[
			'stages'=>[
				[
					'stage_type' => 'input',
					'translation' => '',
					'questions' => [
						'question' => '「'.$str_inflection.'」:'.$str_quizMessageQuestionInput[$int_selected_language],
						'item' => ''
					],
					'arr_answer_information' => $arr_answer_information,
					'arr_explanation_information' => $arr_explanation_information
				],
				[
					'stage_type' => 'choices',
					'translation' => '',
					'questions' => [
						'question' => '「'.$str_inflection.'」:'.$str_quizMessageQuestionWhich[$int_selected_language],
						'item' => ''
					],
					'arr_answer_information' => $arr_answer_information,
					'arr_explanation_information' => $arr_explanation_information
				]
			]
		]
	];

	$str_quiz_prompt = $str_question;

	$str_word_inflection_quiz_main_section = build_html_quiz_main_section($pageType, $int_quiz, $arr_quiz_items, $str_quiz_prompt, $str_send_question, $str_send_answer, $int_mastery_level, $arr_japanese_classification, $arr_inflection, $int_selected_language);

	$str_word_inflection_quiz = $str_word_inflection_quiz_main_section;
	$str_quiz_history_prompt = $str_japanese_polite;

	return array($str_word_inflection_quiz, $str_quiz_history_prompt);
}


function get_data_sorting_quiz($pageType, $isAdvanceStage, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $int_selected_language){

	global
		$int_quiz_sortingQuiz,
		$t_registered_sentences,
		$t_masta_japanese_root,
		$t_masta_japanese_sub_category,
		$t_registered_sentence_elements,
		$int_phrase_clause_id_target,
		$t_japanese_elements,
		$str_snake_to_camel_japanese_id,
		$str_snake_to_camel_japanese_element_id,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$str_phrase_clause_container,
		$str_sortingQuizPieceListContainerLiButtonsKana,
		$str_sortingQuizPieceListContainerLiButtonsInflection,
		$arr_quiz_data,
		$str_quizButtonShowOtherQuestions,
		$str_quizButtonShowNextQuestion,
		$str_quizButtonFinishQuiz;

	$str_html = '';
	$int_quiz = $int_quiz_sortingQuiz;

	$arr_quiz_items = [];
	$str_send_question = '';
	$str_send_answer = '';
	$arr_japanese_classification = [];
	$arr_inflection = [];

	$arr_already_learned_list = [];
	if (isset($_SESSION['arr_already_learned_list'])) {
		$arr_already_learned_list = $_SESSION['arr_already_learned_list'];
	}

	$arr_strSQL_select = [
		[$t_registered_sentences,'id'],
		[$t_registered_sentences,'unique_code'],
		[$t_registered_sentences,'masta_japanese_root_id'],
		[$t_registered_sentences,'sentence']
	];

	$strSQL_from = " FROM
					(
						$t_registered_sentences
						INNER JOIN $t_masta_japanese_root
						ON
						$t_registered_sentences.masta_japanese_root_id = $t_masta_japanese_root.id
					)
					INNER JOIN $t_masta_japanese_sub_category
					ON
					$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_registered_sentences,'is_published','=',FLAG_TRUE,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_registered_sentence = get_data_quiz_target($int_quiz, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $int_selected_language);
	
	if(empty($arr_registered_sentence)){
		// マジックナンバー
		$str_quiz_prompt = 'Not Record In sorting_quiz';
		$str_html = build_html_quiz_main_section($pageType, $int_quiz, $arr_quiz_items, $str_quiz_prompt, $str_send_question, $str_send_answer, $int_mastery_level, $arr_japanese_classification, $arr_inflection, $int_selected_language);
		$str_quiz_history_prompt = '';

		return array($str_html, $str_quiz_history_prompt);
	}

	$int_registered_sentence_id = $arr_registered_sentence['id'];
	$sentence_unique_code = $arr_registered_sentence['unique_code'];

	$arr_strSQL_select = [
		[$t_registered_sentence_elements, 'id'],
		[$t_registered_sentence_elements, 'registered_sentence_id'],
		[$t_registered_sentence_elements, 'id_name as idName'],
		[$t_registered_sentence_elements, 'unique_key as uniqueKey'],
		[$t_registered_sentence_elements, 'japanese_id as ' . $str_snake_to_camel_japanese_id],
		[$t_registered_sentence_elements, 'japanese_element_id as ' . $str_snake_to_camel_japanese_element_id],
		[$t_registered_sentence_elements, 'sub_classification_id as subClassificationId'],
		[$t_registered_sentence_elements, 'form_id as formId'],
		[$t_registered_sentence_elements, 'label_id as labelId'],
		[$t_registered_sentence_elements, 'voice_id as voiceId'],
		[$t_registered_sentence_elements, 'bounds_top as boundsTop'],
		[$t_registered_sentence_elements, 'bounds_left as boundsLeft'],
		[$t_registered_sentence_elements, 'link_id as linkId'],
		[$t_registered_sentence_elements, 'link_type as linkType'],
		[$t_registered_sentence_elements, 'japanese'],
		[$t_registered_sentence_elements, 'kana'],
		[$t_registered_sentence_elements, 'sub_classification as subClassification'],
		[$t_registered_sentence_elements, 'phrase_clause_type as phraseClauseType'],
		[$t_registered_sentence_elements, 'phrase_clause_id as phraseClauseId'],
		[$t_registered_sentence_elements, 'japanese_phrase_clause as japanesePhraseClause'],
		[$t_registered_sentence_elements, 'kana_phrase_clause as kanaPhraseClause'],
		[$t_registered_sentence_elements, 'sort']
	];

	$strSQL_from = ' FROM ' .$t_registered_sentence_elements;

	$arr_strSQL_where = [
		[
			[
				[$t_registered_sentence_elements,'registered_sentence_id','=',$int_registered_sentence_id,'PDO::PARAM_INT','And'],
				[$t_registered_sentence_elements,'phrase_clause_id','=',$int_phrase_clause_id_target,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_registered_sentence_elements,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_registered_sentence_elements) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	shuffle($arr_registered_sentence_elements);

	$str_sortingQuizPieceListContainerLi = '';
	$str_send_question = '';

	foreach($arr_registered_sentence_elements as $loop_registered_sentence_elements){
		$int_unique_key = escape_html($loop_registered_sentence_elements['uniqueKey']);
		$int_japanese_id = intval($loop_registered_sentence_elements[$str_snake_to_camel_japanese_id]);
		$int_japanese_element_id = intval($loop_registered_sentence_elements['japaneseElementId']);
		$int_sub_classification_id = intval($loop_registered_sentence_elements['subClassificationId']);
		$int_form_id = intval($loop_registered_sentence_elements['formId']);
		$int_label_id = intval($loop_registered_sentence_elements['labelId']);
		$int_voice_id = intval($loop_registered_sentence_elements['voiceId']);

		$str_japanese = escape_html($loop_registered_sentence_elements['japanese']);
		$str_kana = escape_html($loop_registered_sentence_elements['kana']);
		$str_japanesePhraseClause = escape_html($loop_registered_sentence_elements['japanesePhraseClause']);
		$str_kanaPhraseClause = escape_html($loop_registered_sentence_elements['kanaPhraseClause']);
		 
		$str_sortingQuizPieceListContainerLiButtons = '';

		if($isAdvanceStage){

			$masta_japanese_root_id_from_form_id = fetch_masta_japanese_root_id_from_masta_form_root_id($int_form_id, $int_selected_language);
			$masta_japanese_root_id_from_voice_id = fetch_masta_japanese_root_id_from_masta_form_root_id($int_voice_id, $int_selected_language);

			$isAlreadyLearnedForm = in_array($masta_japanese_root_id_from_form_id, $arr_already_learned_list);
			$isAlreadyLearnedVoice = in_array($masta_japanese_root_id_from_voice_id, $arr_already_learned_list);

			if ($isAlreadyLearnedForm && $isAlreadyLearnedVoice) {

				$arr_strSQL_select = [
					[$t_japanese_elements,'id'],
					[$t_japanese_elements,'masta_japanese_sub_classification_id'],
					[$t_japanese_elements,'masta_form_root_id'],
					[$t_japanese_elements,'voice_id']
				];
			
				$strSQL_from = ' FROM ' .$t_japanese_elements;
			
				$arr_strSQL_where = [
					[
						[
							[$t_japanese_elements,'id','=',$int_japanese_element_id,'PDO::PARAM_INT','']
						],
						''
					]
				];
			
				$arr_strSQL_order = [];
			
				$strSQL_option = '';
			
				list($pdo_has_error, $select_has_error, $e, $arr_japanese_elements) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
				handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);
	
				if(!empty($arr_japanese_elements)){
	
					$arr_japanese_elements = $arr_japanese_elements[INDEX_FIRST];
					$int_japanese_id = $int_japanese_id;
					$int_japanese_element_id = $int_japanese_element_id;
					$int_sub_classification_id = $arr_japanese_elements['masta_japanese_sub_classification_id'];
					$int_form_id = $arr_japanese_elements['masta_form_root_id'];
					$int_label_id = $int_label_id;
					$int_voice_id = $arr_japanese_elements['voice_id'];
		
					$arr_indicator_labels = get_arr_indicator_label($int_label_id, false, $int_selected_language);
					$arr_inflected_label = get_arr_inflected_label($arr_indicator_labels, $int_japanese_id, $int_japanese_element_id, $int_sub_classification_id, $int_form_id, $int_voice_id, false, $int_selected_language);
	
					$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
					$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
		
					$str_sortingQuizPieceListContainerLiButtons = '
					<div class="sortingQuizPieceListContainerLiButtonsContainer">
						<button class="sortingQuizPieceListContainerLiButtonsKana">'.$str_sortingQuizPieceListContainerLiButtonsKana[$int_selected_language].'</button>
						<button class="sortingQuizPieceListContainerLiButtonsInflection">'.$str_sortingQuizPieceListContainerLiButtonsInflection[$int_selected_language].'</button>
					</div>';
				}
				else{
					$str_sortingQuizPieceListContainerLiButtons = '
					<div class="sortingQuizPieceListContainerLiButtonsContainer">
					<button class="sortingQuizPieceListContainerLiButtonsKana">'.$str_sortingQuizPieceListContainerLiButtonsKana[$int_selected_language].'</button>
					</div>';
				}
			}
			else{
				$str_sortingQuizPieceListContainerLiButtons = '
				<div class="sortingQuizPieceListContainerLiButtonsContainer">
				<button class="sortingQuizPieceListContainerLiButtonsKana">'.$str_sortingQuizPieceListContainerLiButtonsKana[$int_selected_language].'</button>
				</div>';
			}

		}
		else{
			$str_sortingQuizPieceListContainerLiButtons = '
			<div class="sortingQuizPieceListContainerLiButtonsContainer">
			<button class="sortingQuizPieceListContainerLiButtonsKana">'.$str_sortingQuizPieceListContainerLiButtonsKana[$int_selected_language].'</button>
			</div>';
		}

		if($loop_registered_sentence_elements['phraseClauseType'] === $str_phrase_clause_container){
			$str_japanese_result = $str_japanesePhraseClause.$str_japanese;
			$str_kana_result = $str_kanaPhraseClause.$str_kana;
		}
		else{
			$str_japanese_result = $str_japanese;
			$str_kana_result = $str_kana;
		}

		$str_sortingQuizPieceListContainerLi_add = '
		<li class="sortingQuizPieceListContainerLi wiseUiFontSizeTarget" 
		data-unique-key="'.$int_unique_key.'"
		data-japanese-id="'.$int_japanese_id.'"
		data-japanese-element-id="'.$int_japanese_element_id.'"
		data-sub-classification-id="'.$int_sub_classification_id.'"
		data-form-id="'.$int_form_id.'"
		data-label-id="'.$int_label_id.'"
		data-voice-id="'.$int_voice_id.'"
		data-japanese="'.$str_japanese.'"
		data-kana="'.$str_kana.'"
		data-japanese-phrase-clause="'.$str_japanesePhraseClause.'"
		data-kana-phrase-clause="'.$str_kanaPhraseClause.'"
		data-japanese-result="'.$str_japanese_result.'"
		data-kana-result="'.$str_kana_result.'"
		>
			<div class="sortingQuizPieceListContainerLiJapanese">'.
				$str_japanese_result.'
			</div>'.
			$str_sortingQuizPieceListContainerLiButtons.'
		</li>';
		$str_sortingQuizPieceListContainerLi = $str_sortingQuizPieceListContainerLi.$str_sortingQuizPieceListContainerLi_add;

		$str_send_question = $str_send_question.$str_japanese_result.' / ';
	}

	$str_html_menubar_tools = build_html_sorting_quiz_menubar_tools($sentence_unique_code, $int_selected_language);
	$str_sortingQuizPieceListContainer = build_html_sorting_quiz_piece_list_container(true, $str_sortingQuizPieceListContainerLi, $int_selected_language);
	
	$str_sortingQuizContentsLeftContainer = '<div id="sortingQuizContentsLeftContainer" class="sortingQuizFlex">'.$str_sortingQuizPieceListContainer.'</div>';
	$str_sortingQuizZone = '<div id="sortingQuizZone" class="sortingQuizFlex"></div>';
	$str_sortingQuizContentsRightContainer = '
		<div id="sortingQuizContentsRightContainer" class="sortingQuizFlex">'.
			$str_sortingQuizZone.'
		</div>';

	$str_sortingQuizPuzzleContents = '<div id="sortingQuizContents">'.$str_sortingQuizContentsLeftContainer.$str_sortingQuizContentsRightContainer.'</div>';
	
	$str_sortingQuizBody =
	'<div id="sortingQuizBody">
		<section id="sortingQuizButtonsSection">'.
			$str_html_menubar_tools.'
		</section>
		<section id="sortingQuizContentsSection">
			'.$str_sortingQuizPuzzleContents.'
		</section>
	</div>';

	$arr_correct_answers = [];
	$arr_distractor_answers = [];
	$arr_answer_information = [
		'strings' => [
			'str_correct_answer' => '',
			'str_distractor_answer' => ''
		],
		'arrays' => [
			'arr_correct_answers' => $arr_correct_answers,
			'arr_distractor_answers' => $arr_distractor_answers
		]
	];
	$arr_explanation_information = [
		'str_explanation' => '',
		'str_original_sentence' => '',
		'str_furigana' => ''
	];
	
	$str_quiz_translation = fetch_str_registered_sentence_answer_by_id($int_registered_sentence_id, $int_selected_language);
	$arr_quiz_items = [
		[
			'stages'=>[
				[
					'stage_type' => 'sorting',
					'translation' => $str_quiz_translation,
					'questions' => [
						'question' => '',
						'item' => $str_sortingQuizBody
					],
					'arr_answer_information' => $arr_answer_information,
					'arr_explanation_information' => $arr_explanation_information
				]
			]
		]
	];
					
	$str_send_question = substr($str_send_question, 0, -3);
	$str_send_answer = $arr_registered_sentence['sentence'];
	$str_quiz_prompt = '';

	$str_html = build_html_quiz_main_section($pageType, $int_quiz, $arr_quiz_items, $str_quiz_prompt, $str_send_question, $str_send_answer, $int_mastery_level, $arr_japanese_classification, $arr_inflection, $int_selected_language);

	$str_next_button = '';
	$str_finish_button = '';
	
	if(($pageType === 'landing')){
		$str_next_button = '<button class="'.$arr_quiz_data[$int_quiz]['quizButtonToPage'].' quizButtonToPage quizContentsButton">'.$str_quizButtonShowOtherQuestions[$int_selected_language].'</button>';
		$str_finish_button = '';
	}
	elseif($pageType === 'quiz'){
		$str_next_button = '<button class="'.$arr_quiz_data[$int_quiz]['quizButtonNextQuestion'].' quizButtonNextQuestion quizContentsButton">'.$str_quizButtonShowNextQuestion[$int_selected_language].'</button>';
		$str_finish_button = '<button class="sortingQuizMenuBarButtonFinishQuiz quizContentsButton">'.$str_quizButtonFinishQuiz[$int_selected_language].'</button>';
	}

	$str_sorting_successScreen = build_html_sorting_quiz_success_screen($str_next_button, $str_finish_button, $int_selected_language);
	$str_sorting_failureScreen = build_html_sorting_quiz_failure_screen($str_next_button, $str_finish_button, $int_selected_language);
	$str_sorting_usersManualScreen = build_html_sorting_quiz_users_manual_screen($pageType, $isAdvanceStage, $int_selected_language);
	$str_sorting_inflectionScreen = build_html_sorting_quiz_inflection_screen($int_selected_language);
		
	$str_html .= $str_sorting_successScreen;
	$str_html .= $str_sorting_failureScreen;
	$str_html .= $str_sorting_usersManualScreen;
	$str_html .= $str_sorting_inflectionScreen;

	$str_quiz_history_prompt = $str_send_answer;

	return array($str_html, $str_quiz_history_prompt);
}


function get_data_quiz_target($int_quiz, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $int_selected_language){

	global
		$t_masta_japanese_root,
		$t_masta_japanese_sub_category,
		$t_registered_sentences,
		$str_option_value_default,
		$str_option_value_array,
		$int_quiz_wordInflectionQuiz,
		$int_quiz_plainformQuiz,
		$int_mastery_level_select_all;

	$arr_masta_japanese_root_ids = [];
	if(
		$unique_code_type === $str_option_value_default ||
		(
			$int_quiz === $int_quiz_wordInflectionQuiz ||
			$int_quiz === $int_quiz_plainformQuiz
		)
	){
		switch ($int_mastery_level) {
			
			case $int_mastery_level_select_all:
				
				$arr_strSQL_where_mastery_level = [];
				break;

			default:
			
				$arr_strSQL_where_mastery_level = [
					[
						[
							[$t_masta_japanese_root,'jws_level','=',$int_mastery_level,'PDO::PARAM_INT','']
						],
						'And'
					]             
				];
				break;
		}
	}
	elseif($unique_code_type === $str_option_value_array){

		$arr_masta_japanese_root_ids_for_search_condition = [];

		foreach ($arr_grammar_unique_code as $grammar_unique_code) {
			$t_masta_japanese_root_id = fetch_masta_japanese_root_id_from_unique_code($grammar_unique_code, $int_selected_language);
			// 未定義id
			if ($t_masta_japanese_root_id !== 0) {
				$arr_masta_japanese_root_ids_for_search_condition[] = $t_masta_japanese_root_id;
			}
		}

		if (empty($arr_masta_japanese_root_ids_for_search_condition)) {
			return [];
		}

		$arr_search_condition_mastery_level = [];

		foreach ($arr_masta_japanese_root_ids_for_search_condition as $index => $id) {
			$condition = [$t_masta_japanese_root,'id','=',$id,'PDO::PARAM_INT','Or'];
			if ($index === count($arr_masta_japanese_root_ids_for_search_condition) - 1) {
				$condition[INDEX_SIXTH] = '';
			}
			$arr_search_condition_mastery_level[] = $condition;
		}

		$arr_strSQL_where_mastery_level = [
			[
				$arr_search_condition_mastery_level,
				'And'
			]
		];

	}
	else{				
		$room_id = fetch_room_id_from_unique_code($unique_code_type, $int_selected_language);
		$arr_masta_japanese_root_ids = get_arr_masta_japanese_root_ids_for_quiz($room_id, $int_selected_language);
		
		if(empty($arr_masta_japanese_root_ids)){
			return [];
		}
		$arr_strSQL_where_mastery_level = [];
	}

	
	$arr_strSQL_where_sub_category = [];
	if(!empty($arr_sub_category)){
		$last_key_sub_category = end(array_keys($arr_sub_category));
		$arr_search_condition_sub_category = [];
		foreach($arr_sub_category as $key => $item){
			if ($key === $last_key_sub_category) {
				$arr_search_condition_sub_category[] = [$t_masta_japanese_sub_category,'id','=',intval($item),'PDO::PARAM_INT',''];
			} else {
				$arr_search_condition_sub_category[] = [$t_masta_japanese_sub_category,'id','=',intval($item),'PDO::PARAM_INT','Or'];
			}
		}
		$arr_strSQL_where_sub_category = [
			[
				$arr_search_condition_sub_category,
				'And'
			]
		];
	}
	
	$arr_strSQL_where = array_merge($arr_strSQL_where_mastery_level, $arr_strSQL_where_sub_category, $arr_strSQL_where);
		
	if(!empty($arr_masta_japanese_root_ids)){

		$arr_strSQL_order = [
			['', '', 'RAND()']
		];
		
		$strSQL_option = '';
		
		list($pdo_has_error, $select_has_error, $e, $arr_quiz_targets_all) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
		handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

		$matched_ids = array_intersect(array_column($arr_quiz_targets_all, 'masta_japanese_root_id'), $arr_masta_japanese_root_ids);

		if(empty($matched_ids)){	
			return [];
		}
		
		$first_matched_id = reset($matched_ids);

		$related_info = array_filter($arr_quiz_targets_all, function($item) use ($first_matched_id) {
			return $item['masta_japanese_root_id'] === $first_matched_id;
		});
		
		$arr_quiz_targets = array_values($related_info);
		
		if (!empty($arr_quiz_targets)) {
			$selected_target = $arr_quiz_targets[INDEX_FIRST];
		} else {
			return [];
		}
	}
	else{

		$arr_strSQL_order = [
			['', '', 'RAND()']
		];
		$strSQL_option = 'LIMIT 1';

		list($pdo_has_error, $select_has_error, $e, $arr_quiz_targets) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
		handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

		if (empty($arr_quiz_targets)) {
			return [];
		}

		$selected_target = $arr_quiz_targets[INDEX_FIRST];

	}

	return $selected_target;
}


function generate_data_distractor_answer_by_sub_classification($str_correct_answer, $str_japanese, $str_kana, $int_japanese_id, $int_form_id, $int_voice_id, $int_masta_japanese_label_id, $int_selected_language){

	global
		$t_masta_japanese_sub_classification,
		$int_masta_japanese_classification_id_verb,
		$int_V3K,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana;

		
	$arr_strSQL_select = [
		[$t_masta_japanese_sub_classification,'id']
	];

	$strSQL_from = ' FROM ' .$t_masta_japanese_sub_classification;

	$arr_strSQL_where = [
		[
			[
				[$t_masta_japanese_sub_classification,'classification_id','=',$int_masta_japanese_classification_id_verb,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_sub_classification) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	$arr_masta_japanese_sub_classification = array_column($arr_masta_japanese_sub_classification, 'id');
	
	$remove_key = array_search($int_V3K, $arr_masta_japanese_sub_classification);
	if ($remove_key !== false) {
		unset($arr_masta_japanese_sub_classification[$remove_key]);
	}
	
	$isInflectionFound = false;
	$seenSubClassifications = [];

	while (count($seenSubClassifications) < count($arr_masta_japanese_sub_classification)) {
		
		$availableSubClassification = array_diff($arr_masta_japanese_sub_classification, $seenSubClassifications);
	
		if (empty($availableSubClassification)) {
			$isInflectionFound = false;
			break;
		}
	
		$randomValueSubClassification = $availableSubClassification[array_rand($availableSubClassification)];
		$seenSubClassifications[] = $randomValueSubClassification;

		$arr_inflected_label_for_distractor_answer = [
			$str_snake_to_camel_japanese=>$str_japanese,
			$str_snake_to_camel_kana=>$str_kana
		];

		$arr_inflected_label_for_distractor_answer = apply_word_inflection($arr_inflected_label_for_distractor_answer, $int_japanese_id, $randomValueSubClassification, $int_form_id, $int_voice_id, $int_masta_japanese_label_id, false, $int_selected_language);

		$str_distractor_answer_japanese = $arr_inflected_label_for_distractor_answer[$str_snake_to_camel_japanese];
		$str_distractor_answer_kana = $arr_inflected_label_for_distractor_answer[$str_snake_to_camel_kana];
		$str_distractor_answer = $str_distractor_answer_japanese;

		if(
			($str_correct_answer !== $str_distractor_answer) && 
			(substr($str_correct_answer, 0, 1) === substr($str_distractor_answer, 0, 1))
		){
			$isInflectionFound = true;
			break;
		}

	}
	return array($isInflectionFound, $str_distractor_answer, $str_distractor_answer_kana);
}



function try_get_data_quiz_from_japanese_elements(
    $pageType,
    $int_mastery_level,
    $unique_code_type,
    $arr_grammar_unique_code,
    $arr_sub_category,
    $int_selected_language
){

	global
		$int_quiz_grammarQuiz,
		$t_masta_japanese_sub_category,
		$t_masta_japanese_root,
		$t_japanese_elements,
		$str_snake_to_camel_japanese_id,
		$str_snake_to_camel_japanese_element_id,
		$t_masta_japanese_sub_classification,
		$int_masta_japanese_category_id_grammar,
		$t_registered_sentence_elements,
		$t_registered_sentences,
		$str_quizMessageQuestionInput,
		$int_used_language_jpn;


    $int_quiz = $int_quiz_grammarQuiz;
    $sentence_unique_code = '';

	$arr_quiz_items = [];
	$str_send_question = '';
	$str_send_answer = '';
	$arr_japanese_classification = [];
	$arr_inflection = [];

    $arr_strSQL_select = [
        [$t_masta_japanese_sub_category, 'category_id'],
        [$t_masta_japanese_root, 'jws_level'],
        [$t_masta_japanese_root, 'id as ' . $str_snake_to_camel_japanese_id],
        [$t_japanese_elements, 'id as ' . $str_snake_to_camel_japanese_element_id],
        [$t_masta_japanese_sub_classification, 'classification_id']
    ];

    $strSQL_from = " FROM
        (
            $t_masta_japanese_sub_classification
            INNER JOIN (
                $t_masta_japanese_sub_category
                INNER JOIN (
                    $t_masta_japanese_root
                    INNER JOIN $t_japanese_elements
                    ON $t_masta_japanese_root.id = $t_japanese_elements.masta_japanese_root_id
                )
                ON $t_masta_japanese_sub_category.id = $t_masta_japanese_root.sub_category_id
            )
            ON $t_masta_japanese_sub_classification.id = $t_japanese_elements.masta_japanese_sub_classification_id
        )";

    $arr_strSQL_where = [
        [
            [
                [$t_masta_japanese_sub_category, 'category_id', '=', $int_masta_japanese_category_id_grammar, 'PDO::PARAM_INT', 'And'],
                [$t_masta_japanese_root, 'jws_level', '=', $int_mastery_level, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_target_element = get_data_quiz_target($int_quiz, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $int_selected_language);
    if (empty($arr_target_element)) {
        return array(false, '', '');
    }

    $int_root_id = intval($arr_target_element[$str_snake_to_camel_japanese_id]);
    $int_element_id = intval($arr_target_element[$str_snake_to_camel_japanese_element_id]);

    $arr_strSQL_select = [
        [$t_registered_sentence_elements, 'registered_sentence_id'],
        [$t_registered_sentence_elements, 'japanese'],
        [$t_registered_sentence_elements, 'kana'],
        [$t_registered_sentences, 'unique_code'],
        [$t_registered_sentences, 'sentence']
    ];

    $strSQL_from = " FROM
        $t_registered_sentence_elements
        INNER JOIN $t_registered_sentences
        ON $t_registered_sentence_elements.registered_sentence_id = $t_registered_sentences.id";

    $arr_strSQL_where = [
        [
            [
                [$t_registered_sentences, 'masta_japanese_root_id', '=', $int_root_id, 'PDO::PARAM_INT', 'And'],
                [$t_registered_sentence_elements, 'japanese_element_id', '=', $int_element_id, 'PDO::PARAM_INT', 'And'],
				[$t_registered_sentences,'is_published','=',FLAG_TRUE,'PDO::PARAM_INT','']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        ['', '', 'RAND()']
    ];
    $strSQL_option = 'LIMIT 1';

    list($pdo_has_error, $select_has_error, $e, $arr_sentence_hit) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);
    if (empty($arr_sentence_hit)) {
        return array(false, '', '');
    }

    $row_sentence_hit = $arr_sentence_hit[INDEX_FIRST];
    $int_registered_sentence_id = intval($row_sentence_hit['registered_sentence_id']);
    $sentence_unique_code = escape_html($row_sentence_hit['unique_code']);
    $original_sentence_text = escape_html($row_sentence_hit['sentence']);
    $str_correct_answer_japanese = $row_sentence_hit['japanese'];
    $str_correct_answer_kana = $row_sentence_hit['kana'];
    $acceptables = [$str_correct_answer_japanese];

    $arr_strSQL_select = [
        [$t_registered_sentence_elements, 'japanese_id as ' . $str_snake_to_camel_japanese_id],
        [$t_registered_sentence_elements, 'japanese_element_id as ' . $str_snake_to_camel_japanese_element_id],
        [$t_registered_sentence_elements, 'japanese'],
        [$t_registered_sentence_elements, 'kana'],
        [$t_registered_sentence_elements, 'sort']
    ];
    $strSQL_from = " FROM $t_registered_sentence_elements";
    $arr_strSQL_where = [
        [
            [
                [$t_registered_sentence_elements, 'registered_sentence_id', '=', $int_registered_sentence_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];
    $arr_strSQL_order = [
        [$t_registered_sentence_elements, 'sort', 'ASC']
    ];

    list($pdo_has_error, $select_has_error, $e, $arr_elements) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, '');
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    $str_quiz_prompt = '';
    $str_send_question = '';
    $str_original_sentence = '';
    $str_furigana = '';

    foreach ($arr_elements as $elm) {
        $is_target = (intval($elm['japaneseElementId']) === $int_element_id);
        if ($is_target) {
            $show = ' <span class="colorChangerHighlightEmRed">[ ★ ]</span> ';
            $send = '[ ★ ]';
        } else {
            $show = $elm['japanese'];
            $send = $elm['japanese'];
        }
        $str_quiz_prompt .= $show;
        $str_send_question .= $send;
        $str_original_sentence .= $elm['japanese'] . ' ';
        $str_furigana .= $elm['kana'] . ' ';
    }

    $user_level = get_user_level();
    $str_explanation = '';
    if (is_teacher_level($user_level)) {
        $str_explanation = $sentence_unique_code;
    }

    $arr_answer_information = [
        'strings' => [
            'str_correct_answer' => $str_correct_answer_japanese,
            'str_distractor_answer' => ''
        ],
        'arrays' => [
            'arr_correct_answers' => [
                'japanese' => $str_correct_answer_japanese,
                'kana' => $str_correct_answer_kana
            ],
            'arr_acceptable_answers' => $acceptables,
            'arr_distractor_answers' => []
        ]
    ];
    $arr_explanation_information = [
        'str_explanation' => $str_explanation,
        'str_original_sentence' => $str_original_sentence,
        'str_furigana' => $str_furigana
    ];

    $str_quiz_translation = fetch_str_registered_sentence_answer_by_id($int_registered_sentence_id, $int_selected_language);

    if ($int_selected_language === $int_used_language_jpn) {
        $arr_quiz_items = [
            [
                'stages' => [
                    [
                        'stage_type' => 'input',
                        'translation' => '',
                        'questions' => [
                            'question' => $str_quizMessageQuestionInput[$int_selected_language],
                            'item' => ''
                        ],
                        'arr_answer_information' => $arr_answer_information,
                        'arr_explanation_information' => $arr_explanation_information
                    ]
                ]
            ]
        ];
    } else {
        $arr_quiz_items = [
            [
                'stages' => [
                    [
                        'stage_type' => 'input',
                        'translation' => $str_quiz_translation,
                        'questions' => [
                            'question' => $str_quizMessageQuestionInput[$int_selected_language],
                            'item' => ''
                        ],
                        'arr_answer_information' => $arr_answer_information,
                        'arr_explanation_information' => $arr_explanation_information
                    ]
                ]
            ]
        ];
    }

    $str_send_answer = $str_correct_answer_japanese;

    $html = build_html_quiz_main_section($pageType, $int_quiz, $arr_quiz_items, $str_quiz_prompt, $str_send_question, $str_send_answer, $int_mastery_level, $arr_japanese_classification, $arr_inflection, $int_selected_language);
    return array(true, $html, $original_sentence_text);
}

function try_get_data_quiz_from_inflection(
    $pageType,
    $int_mastery_level,
    $unique_code_type,
    $arr_grammar_unique_code,
    $arr_sub_category,
    $int_selected_language
){

	global
		$int_quiz_grammarQuiz,
		$t_registered_sentences,
		$t_masta_japanese_root,
		$t_masta_japanese_sub_category,
		$t_registered_sentence_elements,
		$t_masta_form_root,
		$t_masta_japanese_sub_classification,
		$int_masta_form_id_polite_form,
		$int_masta_japanese_classification_id_verb,
		$t_japanese_labels,
		$str_column_masta_japanese_label_id,
		$str_snake_to_camel_japanese_id,
		$str_snake_to_camel_japanese_element_id,
		$str_quizMessageQuestionInput,
		$str_quizMessageQuestionWhich;

    $int_quiz = $int_quiz_grammarQuiz;
    $sentence_unique_code = '';

	$arr_quiz_items = [];
	$str_send_question = '';
	$str_send_answer = '';
	$arr_japanese_classification = [];
	$arr_inflection = [];

	$arr_strSQL_select = [
		[$t_registered_sentences, 'id'],
		[$t_registered_sentences, 'masta_japanese_root_id'],
		[$t_registered_sentences, 'unique_code'],
		[$t_registered_sentences, 'sentence'],
		[$t_registered_sentence_elements, 'id as sentenceElementId'],
		[$t_registered_sentence_elements, 'japanese_id as ' . $str_snake_to_camel_japanese_id],
		[$t_registered_sentence_elements, 'japanese_element_id as ' . $str_snake_to_camel_japanese_element_id],
		[$t_registered_sentence_elements, 'sub_classification_id as subClassificationId'],
		[$t_registered_sentence_elements, 'form_id as formId'],
		[$t_registered_sentence_elements, 'label_id as labelId'],
		[$t_registered_sentence_elements, 'voice_id as voiceId'],
		[$t_registered_sentence_elements, 'japanese'],
		[$t_registered_sentence_elements, 'kana']
	];
	
	$strSQL_from = " FROM
					(
						(
							(
								(
									$t_registered_sentences
									INNER JOIN $t_masta_japanese_root
									ON
									$t_registered_sentences.masta_japanese_root_id = $t_masta_japanese_root.id
								)
							INNER JOIN $t_masta_japanese_sub_category
							ON
							$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
							)
							INNER JOIN $t_registered_sentence_elements
							ON
							$t_registered_sentences.id = $t_registered_sentence_elements.registered_sentence_id
						)
						INNER JOIN $t_masta_form_root
						ON
						$t_registered_sentence_elements.form_id = $t_masta_form_root.id
					)
					INNER JOIN $t_masta_japanese_sub_classification
					ON
					$t_registered_sentence_elements.sub_classification_id = $t_masta_japanese_sub_classification.id
					";

	$arr_strSQL_where_registered_sentences = [
		[
			[
				[$t_masta_form_root,'masta_id','>',$int_masta_form_id_polite_form,'PDO::PARAM_INT','']
			],
			'And'
		],
		[
			[
				[$t_masta_japanese_sub_classification,'classification_id','=',$int_masta_japanese_classification_id_verb,'PDO::PARAM_INT','']
			],
			'And'
		],
		[
			[
				[$t_registered_sentences,'is_published','=',FLAG_TRUE,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$isSentenceFound = false;
	$seenSentenceElements = [];
	$i_seenJapaneseRootFound = COUNT_FIRST;

	while (!$isSentenceFound) {

		if ($i_seenJapaneseRootFound > 3) {
			return array(false, '', '');
		}

		$arr_strSQL_where_seen_items = [];

		foreach($seenSentenceElements as $item){
			if ($item === end($seenSentenceElements)) {
				$arr_strSQL_where_seen_items[] = [$t_registered_sentence_elements,'id','<>',$item,'PDO::PARAM_INT',''];
			} else {
				$arr_strSQL_where_seen_items[] = [$t_registered_sentence_elements,'id','<>',$item,'PDO::PARAM_INT','And'];
			}
		}
		if(!empty($arr_strSQL_where_seen_items)){
			$arr_strSQL_where_seen = [
				[
					$arr_strSQL_where_seen_items,
					'And'
				]
			];
		}
		else{
			$arr_strSQL_where_seen = [];
		}


		$arr_strSQL_where = array_merge($arr_strSQL_where_seen, $arr_strSQL_where_registered_sentences);

		$arr_registered_sentence = get_data_quiz_target($int_quiz, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $int_selected_language);

		++$i_seenJapaneseRootFound;

		if(empty($arr_registered_sentence)){
			continue;
		}

		$sentence_unique_code = $arr_registered_sentence['unique_code'];
		$str_japanese = $arr_registered_sentence['japanese'];
		$str_kana = $arr_registered_sentence['kana'];
		$int_registered_sentence_id = $arr_registered_sentence['id'];
		$int_sentence_element_id = $arr_registered_sentence['sentenceElementId'];
		$t_masta_japanese_root_id = $arr_registered_sentence[$str_snake_to_camel_japanese_id];
		$int_japanese_element_id = $arr_registered_sentence['japaneseElementId'];
		$int_japanese_label_id = $arr_registered_sentence['labelId'];
		$t_masta_japanese_sub_classification_id = $arr_registered_sentence['subClassificationId'];
		$t_masta_form_root_id = $arr_registered_sentence['formId'];
		$int_voice_id = $arr_registered_sentence['voiceId'];

		$arr_strSQL_select_labels_id = [
			[$t_japanese_labels,$str_column_masta_japanese_label_id]
		];
	
		$strSQL_from_labels_id = ' FROM ' .$t_japanese_labels;
	
		$arr_strSQL_where_labels_id = [
			[
				[
					[$t_japanese_labels,'id','=',$int_japanese_label_id,'PDO::PARAM_INT','']
				],
				''
			]
		];
	
		$arr_strSQL_order_labels_id = [];
	
		$strSQL_option_labels_id = '';
	
		list($pdo_has_error, $select_has_error, $e, $arr_japanese_labels) = execute_select_and_fetch_all($arr_strSQL_select_labels_id, $strSQL_from_labels_id, $arr_strSQL_where_labels_id, $arr_strSQL_order_labels_id, $strSQL_option_labels_id);
		handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

		$int_masta_japanese_label_id = $arr_japanese_labels[INDEX_FIRST][$str_column_masta_japanese_label_id];

		$str_correct_answer_japanese = $str_japanese;
		$str_correct_answer_kana = $str_kana;
		$str_correct_answer = $str_correct_answer_japanese;

		list($isInflectionFound, $str_distractor_answer, $str_distractor_answer_kana) = generate_data_distractor_answer_by_sub_classification($str_correct_answer, $str_japanese, $str_kana, $t_masta_japanese_root_id, $t_masta_form_root_id, $int_voice_id, $int_masta_japanese_label_id, $int_selected_language);
		
		if($isInflectionFound){
			$isSentenceFound = true;
			break;
		}        
		$seenSentenceElements[] = $arr_registered_sentence['sentenceElementId'];
	}

	$arr_strSQL_select_registered_sentence_elements = [
		[$t_registered_sentence_elements,'id'],
		[$t_registered_sentence_elements,'japanese'],
		[$t_registered_sentence_elements,'kana']
	];

	$strSQL_from_registered_sentence_elements = " FROM $t_registered_sentence_elements";

	$arr_strSQL_where_registered_sentence_elements = [
		[
			[
				[$t_registered_sentence_elements,'registered_sentence_id','=',$int_registered_sentence_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order_registered_sentence_elements = [
		[$t_registered_sentence_elements,'sort','ASC']
	];

	$strSQL_option_registered_sentence_elements = '';

	list($pdo_has_error, $select_has_error, $e, $arr_registered_sentence_elements) = execute_select_and_fetch_all($arr_strSQL_select_registered_sentence_elements, $strSQL_from_registered_sentence_elements, $arr_strSQL_where_registered_sentence_elements, $arr_strSQL_order_registered_sentence_elements, $strSQL_option_registered_sentence_elements);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	$str_question = '';
	$str_send_question = '';
	$str_send_answer = $str_correct_answer.' / '.$str_distractor_answer;

	$str_original_sentence = '';
	$str_furigana = '';

	foreach($arr_registered_sentence_elements as $loop_registered_sentence_elements){
		if(intval($loop_registered_sentence_elements['id']) === intval($int_sentence_element_id)){
			$str_question_add = ' <span class="colorChangerHighlightEmRed">[ ★ ]</span> ';
			$str_send_question_add = '[ ★ ]';
		}
		else{
			$str_question_add = $loop_registered_sentence_elements['japanese'];
			$str_send_question_add = $str_question_add;
		}
		$str_question = $str_question.$str_question_add;
		$str_send_question = $str_send_question.$str_send_question_add;

		$str_original_sentence = $str_original_sentence.$loop_registered_sentence_elements['japanese'].' ';
		$str_furigana = $str_furigana.$loop_registered_sentence_elements['kana'].' ';
	}

	$str_explanation = '';
	$user_level = get_user_level();
	if(is_teacher_level($user_level)){
		$str_explanation = $sentence_unique_code;
	}
	
	$arr_correct_answers = [
		'japanese' => $str_correct_answer_japanese,
		'kana' => $str_correct_answer_kana
	];
	$arr_distractor_answers = [];
	$arr_answer_information = [
		'strings' => [
			'str_correct_answer' => $str_correct_answer,
			'str_distractor_answer' => $str_distractor_answer
		],
		'arrays' => [
			'arr_correct_answers' => $arr_correct_answers,
			'arr_distractor_answers' => $arr_distractor_answers
		]
	];
	$arr_explanation_information = [
		'str_explanation' => $str_explanation,
		'str_original_sentence' => $str_original_sentence,
		'str_furigana' => $str_furigana
	];
	

	$str_quiz_translation = fetch_str_registered_sentence_answer_by_id($int_registered_sentence_id, $int_selected_language);
	$arr_quiz_items = [
		[
			'stages'=>[
				[
					'stage_type' => 'input',
					'translation' => $str_quiz_translation,
					'questions' => [
						'question' => $str_quizMessageQuestionInput[$int_selected_language],
						'item' => ''
					],
					'arr_answer_information' => $arr_answer_information,
					'arr_explanation_information' => $arr_explanation_information
				],
				[
					'stage_type' => 'choices',
					'translation' => $str_quiz_translation,
					'questions' => [
						'question' => $str_quizMessageQuestionWhich[$int_selected_language],
						'item' => ''
					],
					'arr_answer_information' => $arr_answer_information,
					'arr_explanation_information' => $arr_explanation_information
				]
			]
		]
	];

	$str_quiz_prompt = $str_question;

	$str_grammar_quiz_main_section = build_html_quiz_main_section($pageType, $int_quiz, $arr_quiz_items, $str_quiz_prompt, $str_send_question, $str_send_answer, $int_mastery_level, $arr_japanese_classification, $arr_inflection, $int_selected_language);

	$str_grammar_quiz = $str_grammar_quiz_main_section;
	$str_quiz_history_prompt = escape_html($arr_registered_sentence['sentence']);

    return array(true, $str_grammar_quiz, $str_quiz_history_prompt);

}


function generate_quiz_data_attributes($int_mastery_level, $arr_japanese_classification, $arr_inflection) {

    $data_level = '';
    if (!empty($int_mastery_level) && intval($int_mastery_level) > 0) {
        $data_level = ' data-level="'.intval($int_mastery_level).'"';
    }

    $data_jc = '';
    if (!empty($arr_japanese_classification)) {
        $tmp_jc = is_array($arr_japanese_classification) ? $arr_japanese_classification : [$arr_japanese_classification];
        $vals_jc = array_values(array_filter(array_map('intval', $tmp_jc), function($v){ return $v > 0; }));
        if (!empty($vals_jc)) {
            $data_jc = ' data-japanese-classification="'.escape_html(implode(',', $vals_jc)).'"';
        }
    }

    $data_inf = '';
    if (!empty($arr_inflection)) {
        $tmp_inf = is_array($arr_inflection) ? $arr_inflection : [$arr_inflection];
        $vals_inf = array_values(array_filter(array_map('intval', $tmp_inf), function($v){ return $v > 0; }));
        if (!empty($vals_inf)) {
            $data_inf = ' data-inflection="'.escape_html(implode(',', $vals_inf)).'"';
        }
    }

    return [$data_level, $data_jc, $data_inf];
}



/******************************************************
 *  IDS 
 *  
 ******************************************************/
function get_arr_masta_japanese_root_ids_for_quiz($room_id, $int_selected_language){

    global
        $t_room_lessons,
        $int_learning_status_learned;

    $arr_strSQL_select_room_lessons = [
        [$t_room_lessons, 'id'],
        [$t_room_lessons, 'room_id'],
        [$t_room_lessons, 'teaching_material_lesson_id'],
        [$t_room_lessons, 'title']
    ];

    $strSQL_from_room_lessons = ' FROM ' . $t_room_lessons;

    $arr_strSQL_where_room_lessons = [
        [
            [
                [$t_room_lessons, 'room_id', '=', $room_id, 'PDO::PARAM_INT', 'And'],
                [$t_room_lessons, 'learning_status', '=', $int_learning_status_learned, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order_room_lessons = [
        [$t_room_lessons, 'sort', 'ASC']
    ];

    $strSQL_option_room_lessons = '';

    list($pdo_has_error, $select_has_error, $e, $arr_lessons) = execute_select_and_fetch_all(
        $arr_strSQL_select_room_lessons,
        $strSQL_from_room_lessons,
        $arr_strSQL_where_room_lessons,
        $arr_strSQL_order_room_lessons,
        $strSQL_option_room_lessons
    );
    handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

    if (empty($arr_lessons)) {
        return [];
    }

    $type = 'masta_japanese_root_ids_for_quiz';
    $contents_tree_flags = [];
    $arr_bookmarks_data = [];
    $arr_search_condition_for_category = [];

    $arr_lesson_content_information = get_data_lesson_content_information(
        $type,
        $room_id,
        $contents_tree_flags,
        $arr_lessons,
        $arr_bookmarks_data,
        $arr_search_condition_for_category,
        $int_selected_language
    );

    $arr_masta_japanese_root_ids = $arr_lesson_content_information['arr_masta_japanese_root_ids'] ?? [];

    return $arr_masta_japanese_root_ids;
}

