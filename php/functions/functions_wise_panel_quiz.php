<?php


/******************************************************
 *  whiteboard_quiz_panel : main view
 ******************************************************/

function build_html_wise_quiz_panel_view_main($int_selected_language)
{
    $html_header = build_html_wise_quiz_panel_header($int_selected_language);

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '<div id="quizContentsContainer"></div>',
        'wisePanelQuizMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        '',
        'wisePanelQuizMainContentContainer'
    );

    return build_html_wise_panel_view(
        'wisePanelQuizViewMain',
        $html_header,
        '',
        $html_main_content_container,
        '',
        'wisePanelView wisePanelView-main wisePanelQuizView',
        'wisePanelViewContents wisePanelQuizViewContents'
    );
}


/******************************************************
 *  whiteboard_quiz_panel : header
 *  select を header に仮配置
 ******************************************************/
function build_html_wise_quiz_panel_header($int_selected_language)
{
    global
        $arr_selectQuizContainerOption_wise,
        $str_selectQuizContainerOptionDefault,
        $arr_str_button_caption_confirm;

    $html_select = build_html_select_quiz_select(
        $arr_selectQuizContainerOption_wise,
        $int_selected_language,
        $str_selectQuizContainerOptionDefault[$int_selected_language],
        'selectQuizContainerStudyTopic',
        ''
    );

	
    $arr_button_next = [
		'Next',
		'Next',
	];

	$arr_button_history = [
		'History',
		'History',
	];

	$arr_button_settings = [
		'Settings',
		'Settings',
	];

    $str_button_next = isset($arr_button_next[$int_selected_language])
		? $arr_button_next[$int_selected_language]
		: $arr_button_next[0];

	$str_button_history = isset($arr_button_history[$int_selected_language])
		? $arr_button_history[$int_selected_language]
		: $arr_button_history[0];

	$str_button_settings = isset($arr_button_settings[$int_selected_language])
		? $arr_button_settings[$int_selected_language]
		: $arr_button_settings[0];

	$html_zoom_in_icon  = build_html_magnifier_icon('plus');
	$html_zoom_out_icon = build_html_magnifier_icon('minus');

    $btn_confirm = '<button id="wisePanelQuizHeaderConfirmButton" class="wisePanelQuizHeaderButton wiseHitItem">' . escape_html($arr_str_button_caption_confirm[$int_selected_language]) . '</button>';

    $btn_next = '<button id="wisePanelQuizViewNextButton" class="wisePanelQuizHeaderButton">' . escape_html($str_button_next) . '</button>';

	$btn_history = '<button id="wisePanelQuizHeaderHistoryButton" class="wisePanelQuizHeaderButton">' . escape_html($str_button_history) . '</button>';

	$btn_settings = '<button id="wisePanelQuizHeaderSettingsButton" class="wisePanelQuizHeaderButton">' . escape_html($str_button_settings) . '</button>';

	$btn_zoom_in = '<button id="wisePanelQuizHeaderZoomInButton" class="wisePanelQuizHeaderButton wisePanelZoomButton">' . $html_zoom_in_icon . '</button>';

	$btn_zoom_out = '<button id="wisePanelQuizHeaderZoomOutButton" class="wisePanelQuizHeaderButton wisePanelZoomButton">' . $html_zoom_out_icon . '</button>';

    $buttons_left = [
        build_html_wise_panel_close_button('wisePanelQuizViewCloseButton'),
		build_html_wise_panel_expand_button('wisePanelQuizViewExpandButton'),
		build_html_wise_panel_split_button('wisePanelQuizViewSplitButton'),
    ];

    $buttons_right = [
		$btn_confirm,
		$btn_next,
		$btn_history,
		$btn_settings,
		$btn_zoom_in,
		$btn_zoom_out,
	];

	$header_content = $html_select;

    return build_html_wise_panel_header(
        'Quiz',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelQuizHeader',
        'wisePanelHeader wisePanelQuizHeader',
        [],
        'wisePanelTitle'
    );
}


/******************************************************
 *  whiteboard_quiz_panel : overlay ui
 ******************************************************/
function build_html_wise_quiz_panel_feedback_overlay_ui($int_selected_language)
{
    $html_close_button = build_html_wise_panel_ui_overlay_close_button(
        'wisePanelQuizUiFeedbackCloseButton',
        'wisePanelUiOverlayCloseButton',
        [
            'data-panel-ui-id' => 'wisePanelQuizUiFeedback'
        ]
    );

    $html_screen_contents = build_html_wise_quiz_panel_feedback_screen_contents($int_selected_language);

	$html_contents = build_html_wise_panel_ui_overlay_contents(
		$html_screen_contents,
		$html_close_button,
		'wisePanelUiFeedbackOverlayModal',
		'wisePanelUiFeedbackOverlayScreen'
	);

    return build_html_wise_panel_ui_item(
        'wisePanelQuizUiFeedback',
        '',
        '',
        '',
        $html_contents,
        'overlay',
        true,
    );
}


function build_html_wise_quiz_panel_feedback_screen_contents($int_selected_language)
{
    global
        $str_quizButtonShowOtherQuestions;

    $pageType = 'wise';
    $str_next_button_label = $str_quizButtonShowOtherQuestions[$int_selected_language];

    return build_html_quiz_feedback_contents(
        $pageType,
        $str_next_button_label,
        $int_selected_language
    );
}

/******************************************************
 *  wise_quiz_settings_panel : overlay ui
 ******************************************************/
function build_html_wise_quiz_panel_settings_overlay_ui(
    $int_mastery_level,
    $arr_sub_category,
    $arr_japanese_classification,
    $arr_inflection,
    $int_selected_language
) {
    $html_close_button = build_html_wise_panel_ui_overlay_close_button(
        'wisePanelQuizUiSettingsCloseButton',
        'wisePanelUiOverlayCloseButton',
        [
            'data-panel-ui-id' => 'wisePanelQuizUiSettings'
        ]
    );

    $html_screen_contents = build_html_wise_quiz_panel_settings_screen_contents(
        $int_mastery_level,
        $arr_sub_category,
        $arr_japanese_classification,
        $arr_inflection,
        $int_selected_language
    );

    $html_contents = build_html_wise_panel_ui_overlay_contents(
        $html_screen_contents,
        $html_close_button,
        'wisePanelUiSettingsOverlayModal',
        'wisePanelUiSettingsOverlayScreen'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelQuizUiSettings',
        '',
        '',
        '',
        $html_contents,
        'overlay',
        true,
    );
}

function build_html_wise_quiz_panel_settings_screen_contents(
    $int_mastery_level,
    $arr_sub_category,
    $arr_japanese_classification,
    $arr_inflection,
    $int_selected_language
) {
    global
        $arr_mastery_level,
        $arr_columns_masta_japanese_sub_category,
        $arr_columns_masta_japanese_root,
        $arr_inflection_for_quiz,
        $str_quizSettingsScreenTitle,
        $str_quizSettingsScreenTitleMasteryLevel,
        $str_quizSettingsScreenTitleSubCategory,
        $str_quizSettingsScreenTitleJapaneseClassification,
        $str_quizSettingsScreenTitleWordInflection;

    $str_select_mastery_level_options = '';

    foreach ($arr_mastery_level as $key => $loop_mastery_level) {
        $int_option_value = $key;
        $str_option_text_content = escape_html_with_nl2br($loop_mastery_level[$int_selected_language]);

        if ($key === $int_mastery_level) {
            $str_select_mastery_level_options .= '<option value="'.$int_option_value.'" selected>'.$str_option_text_content.'</option>';
        } else {
            $str_select_mastery_level_options .= '<option value="'.$int_option_value.'">'.$str_option_text_content.'</option>';
        }
    }

    $str_select_mastery_level_select =
    '<select id="quizSettingsScreenContentsSelectMasteryLevel" class="quizSettingsScreenContentsSelect" name="quizSettingsScreenContentsSelectMasteryLevel">'.
        $str_select_mastery_level_options.
    '</select>';

    $arr_masta_japanese_sub_category = fetch_arr_masta_japanese_sub_categories_for_grammar($int_selected_language);

    $str_labels_sub_category = '';

    foreach ($arr_masta_japanese_sub_category as $loop_masta_japanese_sub_category) {
        $checked = in_array($loop_masta_japanese_sub_category['id'], $arr_sub_category) ? ' checked' : '';

        $str_labels_sub_category .=
        '<label>'.
            '<input type="checkbox" name="subCategory" value="'.$loop_masta_japanese_sub_category['id'].'"'.$checked.'>'.
            $loop_masta_japanese_sub_category[$arr_columns_masta_japanese_sub_category[$int_selected_language]].
        '</label>';
    }

    $arr_masta_japanese_classification = fetch_arr_masta_japanese_classification_for_quiz($int_selected_language);

    $str_labels_POS = '';

    foreach ($arr_masta_japanese_classification as $loop_masta_japanese_classification) {
        $checked = in_array($loop_masta_japanese_classification['id'], $arr_japanese_classification) ? ' checked' : '';

        $str_labels_POS .=
        '<label>'.
            '<input type="checkbox" name="pos" value="'.$loop_masta_japanese_classification['id'].'"'.$checked.'>'.
            $loop_masta_japanese_classification[$arr_columns_masta_japanese_root[$int_selected_language]].
        '</label>';
    }

    $arr_masta_form_root = fetch_arr_masta_form_root_for_quiz($int_selected_language);

    $targetMap = [];

    foreach ($arr_masta_form_root as $target) {
        $targetMap[$target['id']] = $target;
    }

    $sortedArray = [];

    foreach ($arr_inflection_for_quiz as $id) {
        if (isset($targetMap[$id])) {
            $sortedArray[] = $targetMap[$id];
        }
    }

    $groupedArray = [];

    foreach ($sortedArray as $item) {
        $groupedArray[$item['masta_id']][] = $item;
    }

    $str_labels_inflection = '';

    foreach ($groupedArray as $arr_target) {
        $str_labels_add = '';

        foreach ($arr_target as $loop_target) {
            $checked = in_array($loop_target['id'], $arr_inflection) ? ' checked' : '';

            $str_labels_add .=
            '<label>'.
                '<input type="checkbox" name="inflection" value="'.$loop_target['id'].'"'.$checked.'>'.
                $loop_target[$arr_columns_masta_japanese_root[$int_selected_language]].
            '</label>';
        }

        $str_labels_inflection .= '<div class="quizSettingsScreenLabelGroup">'.$str_labels_add.'</div>';
    }

    return
    '<h2>'.$str_quizSettingsScreenTitle[$int_selected_language].'</h2>'.
	'<div class="modalScrollableContainer">'.
		'<div id="quizSettingsScreenContentsContainerMasteryLevel" class="quizSettingsScreenContentsContainer">'.
			'<h4>'.$str_quizSettingsScreenTitleMasteryLevel[$int_selected_language].'</h4>'.
			'<div id="quizSettingsScreenContentsMasteryLevelSelectContainer">'.
				$str_select_mastery_level_select.
			'</div>'.
		'</div>'.

		'<div id="quizSettingsScreenContentsContainerSubCategory" class="quizSettingsScreenContentsContainer">'.
			'<h4>'.$str_quizSettingsScreenTitleSubCategory[$int_selected_language].'</h4>'.
			'<div id="quizSettingsScreenLabelsContainerSubCategory" class="quizSettingsScreenLabelsContainer">'.
				$str_labels_sub_category.
			'</div>'.
			'<div class="quizSettingsScreenButtonsContainer">'.
				'<button class="quizSettingsScreenSelectAll">すべて選択</button>'.
				'<button class="quizSettingsScreenDeselectAll">すべて解除</button>'.
			'</div>'.
		'</div>'.

		'<div id="quizSettingsScreenContentsContainerJapaneseClassification" class="quizSettingsScreenContentsContainer">'.
			'<h4>'.$str_quizSettingsScreenTitleJapaneseClassification[$int_selected_language].'</h4>'.
			'<div id="quizSettingsScreenLabelsContainerJapaneseClassification" class="quizSettingsScreenLabelsContainer">'.
				$str_labels_POS.
			'</div>'.
			'<div class="quizSettingsScreenButtonsContainer">'.
				'<button class="quizSettingsScreenSelectAll">すべて選択</button>'.
				'<button class="quizSettingsScreenDeselectAll">すべて解除</button>'.
			'</div>'.
		'</div>'.

		'<div id="quizSettingsScreenContentsContainerWordInflection" class="quizSettingsScreenContentsContainer">'.
			'<h4>'.$str_quizSettingsScreenTitleWordInflection[$int_selected_language].'</h4>'.
			'<div id="quizSettingsScreenLabelsContainerWordInflection" class="quizSettingsScreenLabelsContainer">'.
				$str_labels_inflection.
			'</div>'.
			'<div class="quizSettingsScreenButtonsContainer">'.
				'<button class="quizSettingsScreenSelectAll">すべて選択</button>'.
				'<button class="quizSettingsScreenDeselectAll">すべて解除</button>'.
			'</div>'.
		'</div>'.

		'<div class="quizSettingsScreenButtonsContainer">'.
			'<button class="quizSettingsScreenSubmit">保存</button>'.
		'</div>'.
	'</div>';
}

/******************************************************
 *  wise_quiz_history_panel : overlay ui
 ******************************************************/
function build_html_wise_quiz_panel_history_overlay_ui($int_selected_language)
{
    $html_close_button = build_html_wise_panel_ui_overlay_close_button(
        'wisePanelQuizUiHistoryCloseButton',
        'wisePanelUiOverlayCloseButton',
        [
            'data-panel-ui-id' => 'wisePanelQuizUiHistory'
        ]
    );

    $html_screen_contents = build_html_wise_quiz_panel_history_screen_contents($int_selected_language);

    $html_contents = build_html_wise_panel_ui_overlay_contents(
        $html_screen_contents,
        $html_close_button,
        'wisePanelUiHistoryOverlayModal',
        'wisePanelUiHistoryOverlayScreen'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelQuizUiHistory',
        '',
        '',
        '',
        $html_contents,
        'overlay',
        true,
    );
}

function build_html_wise_quiz_panel_history_screen_contents($int_selected_language)
{
    global
        $str_quizHistoryScreenTitle;

    return
    '<h2>'.$str_quizHistoryScreenTitle[$int_selected_language].'</h2>'.
	'<div id="quizHistoryScreenTableContainer" class="wiseScrollableArea">'.
	'</div>';
}