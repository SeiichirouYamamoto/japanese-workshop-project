<?php


/******************************************************
 *  BODY
 *
 ******************************************************/
function build_html_wise_chart_body($int_selected_language)
{

    $arr_title_chart = [
        'チャート',
        '圖表',
    ];

    $arr_button_history = [
        '履歴',
        '歷史',
    ];

    $arr_button_chart_mode = [
        'チャート',
        '圖表',
    ];

    $arr_button_polite_plain_table_mode = [
        '丁寧形・普通形',
        '禮貌形・普通形',
    ];

    $arr_button_download = [
        'Download',
        'Download',
    ];

    $str_title_chart = isset($arr_title_chart[$int_selected_language])
        ? $arr_title_chart[$int_selected_language]
        : $arr_title_chart[0];

    $str_button_history = isset($arr_button_history[$int_selected_language])
        ? $arr_button_history[$int_selected_language]
        : $arr_button_history[0];

    $str_button_chart_mode = isset($arr_button_chart_mode[$int_selected_language])
        ? $arr_button_chart_mode[$int_selected_language]
        : $arr_button_chart_mode[0];

    $str_button_polite_plain_table_mode = isset($arr_button_polite_plain_table_mode[$int_selected_language])
        ? $arr_button_polite_plain_table_mode[$int_selected_language]
        : $arr_button_polite_plain_table_mode[0];

    $str_button_download = isset($arr_button_download[$int_selected_language])
        ? $arr_button_download[$int_selected_language]
        : $arr_button_download[0];

		
	$html_zoom_in_icon  = build_html_magnifier_icon('plus');
	$html_zoom_out_icon = build_html_magnifier_icon('minus');


    $buttons_left = [
        build_html_wise_panel_close_button('wisePanelChartViewCloseButton'),
        build_html_wise_panel_expand_button('wisePanelChartViewExpandButton'),
        build_html_wise_panel_split_button('wisePanelChartViewSplitButton'),
		'<button id="wisePanelChartViewModeChartButton" class="wisePanelChartHeaderButton" data-mode="chart">' . escape_html($str_button_chart_mode) . '</button>',
		'<button id="wisePanelChartViewModePolitePlainTableButton" class="wisePanelChartHeaderButton" data-mode="politePlainTable">' . escape_html($str_button_polite_plain_table_mode) . '</button>',
    ];

    $buttons_right = [
        '<button id="wisePanelChartViewHistoryOpenButton" class="wisePanelChartHeaderButton">' . escape_html($str_button_history) . '</button>',
        '<button id="wisePanelChartViewDownloadButton" class="wisePanelChartHeaderButton">' . escape_html($str_button_download) . '</button>',
		'<button id="wisePanelChartViewZoomInButton" class="wisePanelChartHeaderButton wisePanelZoomButton">' . $html_zoom_in_icon . '</button>',
		'<button id="wisePanelChartViewZoomOutButton" class="wisePanelChartHeaderButton wisePanelZoomButton">' . $html_zoom_out_icon . '</button>',
    ];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        $str_title_chart,
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelChartViewHeader'
    );

    $html_main_content_area = build_html_wise_panel_main_content_area(
        build_html_wise_chart_panel_main_contents($int_selected_language),
        'wisePanelChartViewMainContentArea'
    );

    $html_loading = build_html_loading_spinner('wisePanelChartViewLoading');

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelChartViewMainContentContainer'
    );

    $html_handle = build_html_wise_panel_view_handle(
        '',
        'wisePanelChartViewHandle'
    );

    $html_toolbar = '';

    return build_html_wise_panel_view(
        'wisePanelChartView',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        $html_handle
    );
}


/******************************************************
 *  BODY MAIN CONTENTS
 *
 ******************************************************/
function build_html_wise_chart_panel_main_contents($int_selected_language)
{
    return
        build_html_wise_chart_mode_chart($int_selected_language) .
        build_html_wise_chart_mode_polite_plain_table($int_selected_language);
}


/******************************************************
 *  MODE : CHART
 *
 ******************************************************/
function build_html_wise_chart_mode_chart($int_selected_language)
{
    return
        '<div id="wisePanelChartModeChart" class="wisePanelChartMode wiseChartPanelContent" data-mode="chart">' .
            build_html_wise_chart_words_y_view($int_selected_language) .
            build_html_wise_chart_inflections_view($int_selected_language) .
            build_html_wise_chart_words_x_view($int_selected_language) .
            build_html_wise_chart_chart_view($int_selected_language) .
        '</div>';
}


/******************************************************
 *  MODE : POLITE PLAIN TABLE
 *
 ******************************************************/
function build_html_wise_chart_mode_polite_plain_table($int_selected_language)
{
    return
        '<div id="wisePanelChartModePolitePlainTable" class="wisePanelChartMode wiseChartPanelContent" data-mode="politePlainTable">' .
            build_html_wise_polite_plain_table_view($int_selected_language) .
        '</div>';
}


/******************************************************
 *  WORDS Y VIEW
 *
 ******************************************************/
function build_html_wise_chart_words_y_view($int_selected_language)
{

	$arr_button_reset = [
		'reset',
		'reset'
	];

	$arr_button_submit = [
		'送信',
		'提交'
	];

	$arr_button_resubmit = [
		'再送信',
		'再提交'
	];

    $int_inflections_chart = STATUS_FIRST;
    $int_free_chart = STATUS_SECOND;

    $arr_chart_x = [
        $int_inflections_chart => ['語形変化', '詞形變化'],
        $int_free_chart => ['free', 'free'],
    ];

    $str_options = '';

    foreach ($arr_chart_x as $key => $loop_chart_x) {
        $int_option_value = $key;
        $str_option_text_content = escape_html_with_nl2br($loop_chart_x[$int_selected_language]);

        $str_options .= '<option value="' . $int_option_value . '">' . $str_option_text_content . '</option>';
    }

    $str_reset = isset($arr_button_reset[$int_selected_language])
        ? $arr_button_reset[$int_selected_language]
        : $arr_button_reset[0];

    $str_submit = isset($arr_button_submit[$int_selected_language])
        ? $arr_button_submit[$int_selected_language]
        : $arr_button_submit[0];

    $str_resubmit = isset($arr_button_resubmit[$int_selected_language])
        ? $arr_button_resubmit[$int_selected_language]
        : $arr_button_resubmit[0];

    return
        '<div id="wisePanelChartWordsYView" class="wisePanelChartInputBlock wiseChartPanelContent">' .
            '<section class="wisePanelChartSection">' .
                '<textarea id="wisePanelChartWordsYTextarea" class="wiseUiFontSizeTarget" cols="10" rows="10"></textarea>' .
            '</section>' .
            '<section class="wisePanelChartButtonsSection">' .
                '<div id="wisePanelChartDropDownMenuArea">' .
                    '<select id="wisePanelChartDropDownMenuSelect" name="wisePanelChartDropDownMenuSelect">' .
                        $str_options .
                    '</select>' .
                '</div>' .
                '<button id="wisePanelChartViewResetButtonY" class="chartResetButton wisePanelChartButton">' . escape_html($str_reset) . '</button>' .
                '<button id="wisePanelChartViewSubmitButton" class="wisePanelChartButton">' . escape_html($str_submit) . '</button>' .
                '<button id="wisePanelChartViewReSubmitButton" class="wisePanelChartButton">' . escape_html($str_resubmit) . '</button>' .
            '</section>' .
        '</div>';
}


/******************************************************
 *  INFLECTIONS VIEW
 *
 ******************************************************/
function build_html_wise_chart_inflections_view($int_selected_language)
{
    global $arr_columns_masta_japanese_root;

    $str_inflections_li = '';

    $arr_form_list = fetch_arr_form_root_list_all($int_selected_language);

    foreach ($arr_form_list as $key => $loop_form_list) {
        $word = $loop_form_list[$arr_columns_masta_japanese_root[$int_selected_language]];
        $t_masta_form_root_id = $loop_form_list['id'];
        $str_form_list_id = 'wisePanelChartFormListId' . $t_masta_form_root_id;

        $str_inflections_li .=
            '<li class="wisePanelChartInflectionsItem">' .
                '<label class="wisePanelChartInflectionsLabel wiseUiFontSizeTarget" for="' . escape_html($str_form_list_id) . '">' .
                    '<input type="checkbox" id="' . escape_html($str_form_list_id) . '" name="' . escape_html($str_form_list_id) . '" value="' . escape_html($str_form_list_id) . '" class="wisePanelChartInflectionsCheckbox" data-word="' . escape_html($word) . '" data-form-id="' . escape_html($t_masta_form_root_id) . '" data-sort="' . escape_html(SORT_FIRST) . '">' .
                    escape_html($word) .
                '</label>' .
            '</li>';
    }

    return
        '<div id="wisePanelChartInflectionsView" class="wisePanelChartInputBlock wiseChartPanelContent">' .
            '<section class="wisePanelChartSection">' .
                '<ul id="wisePanelChartInflectionsList">' .
                    $str_inflections_li .
                '</ul>' .
            '</section>' .
        '</div>';
}


/******************************************************
 *  WORDS X VIEW
 *
 ******************************************************/
function build_html_wise_chart_words_x_view($int_selected_language)
{
    return
        '<div id="wisePanelChartWordsXView" class="wisePanelChartInputBlock wiseChartPanelContent">' .
            '<section class="wisePanelChartSection">' .
                '<textarea id="wisePanelChartWordsXTextarea" class="wiseUiFontSizeTarget" cols="10" rows="10"></textarea>' .
            '</section>' .
        '</div>';
}


/******************************************************
 *  CHART VIEW
 *
 ******************************************************/
function build_html_wise_chart_chart_view($int_selected_language)
{


	$arr_button_reset = [
		'reset',
		'reset'
	];
    
	$arr_add_rows_and_columns = [
		'行と列を追加',
		'增加行與列'
	];

	
    $str_reset = isset($arr_button_reset[$int_selected_language])
        ? $arr_button_reset[$int_selected_language]
        : $arr_button_reset[0];

    $str_add_rows_and_columns = isset($arr_add_rows_and_columns[$int_selected_language])
        ? $arr_add_rows_and_columns[$int_selected_language]
        : $arr_add_rows_and_columns[0];

    return
        '<div id="wisePanelChartChartView" class="wisePanelChartInputBlock wiseChartPanelContent wiseScrollableAreaContainer">' .
            '<section id="wisePanelChartChartSection" class="wiseScrollableArea">' .
                '<div id="wisePanelChartChartDisplayArea"></div>' .
            '</section>' .
            '<section id="wisePanelChartChartButtonsSection" class="wisePanelChartButtonsSection">' .
                '<button id="wisePanelChartViewResetButtonChart" class="chartResetButton wisePanelChartButton">' . escape_html($str_reset) . '</button>' .
                '<button id="wisePanelChartViewAddRowsAndColumnsButton" class="wisePanelChartButton">' . escape_html($str_add_rows_and_columns) . '</button>' .
            '</section>' .
        '</div>';
}


/******************************************************
 *  POLITE PLAIN TABLE VIEW
 *
 ******************************************************/
function build_html_wise_polite_plain_table_view($int_selected_language)
{
    global
        $int_affirmativeNotPastTense,
        $int_negativeNotPastTense,
        $int_affirmativePastTense,
        $int_negativePastTense,
        $FORM_STYLE_POLITE,
        $FORM_STYLE_PLAIN,
        $int_PoliteFormAffirmativeNotPastTense,
        $int_OriginalForm,
        $int_PoliteFormNegativeNotPastTense,
        $int_NaiForm,
        $int_PoliteFormAffirmativePastTense,
        $int_TaForm,
        $int_PoliteFormNegativePastTense,
        $int_NakattaForm,
        $arr_affirmative_negative_tense,
        $arr_select_form_style,
        $str_wisePoliteFormPlainFormTableButtonsHint,
        $str_wisePoliteFormPlainFormTableButtonsAnswer;

    $str_table_trs = '';

    $arr_masta_form_root = fetch_arr_form_root_list_all($int_selected_language);
    $arr_masta_form_root = array_column($arr_masta_form_root, null, 'id');

    $arr_politeform_plainform = [
        $int_affirmativeNotPastTense => [
            $FORM_STYLE_POLITE => $int_PoliteFormAffirmativeNotPastTense,
            $FORM_STYLE_PLAIN => $int_OriginalForm
        ],
        $int_negativeNotPastTense => [
            $FORM_STYLE_POLITE => $int_PoliteFormNegativeNotPastTense,
            $FORM_STYLE_PLAIN => $int_NaiForm
        ],
        $int_affirmativePastTense => [
            $FORM_STYLE_POLITE => $int_PoliteFormAffirmativePastTense,
            $FORM_STYLE_PLAIN => $int_TaForm
        ],
        $int_negativePastTense => [
            $FORM_STYLE_POLITE => $int_PoliteFormNegativePastTense,
            $FORM_STYLE_PLAIN => $int_NakattaForm
        ]
    ];

    foreach ($arr_politeform_plainform as $key_politeform_plainform => $loop_politeform_plainform) {

        $str_td_polite = '';
        $str_td_plain = '';

        foreach ($loop_politeform_plainform as $key_target => $target) {

            $str_td_target =
                '<div>' .
                    '<textarea class="wisePoliteFormPlainFormTableTextarea wiseUiFontSizeTarget" data-form-id="' . escape_html($target) . '"></textarea>' .
                    '<div class="wisePoliteFormPlainFormTableAnswersDiv wiseUiFontSizeTarget" data-form-id="' . escape_html($target) . '"></div>' .
                '</div>' .
                '<div>' .
                    '<button class="wisePoliteFormPlainFormTableButtons wisePoliteFormPlainFormTableButtonsHints" tabindex="-1" data-form-id="' . escape_html($target) . '" data-unique-code="' . escape_html($arr_masta_form_root[$target]['unique_code']) . '">' . escape_html($str_wisePoliteFormPlainFormTableButtonsHint[$int_selected_language]) . '</button>' .
                    '<button class="wisePoliteFormPlainFormTableButtons wisePoliteFormPlainFormTableButtonsAnswers" tabindex="-1" data-form-id="' . escape_html($target) . '">' . escape_html($str_wisePoliteFormPlainFormTableButtonsAnswer[$int_selected_language]) . '</button>' .
                '</div>';

            if ($key_target === $FORM_STYLE_POLITE) {
                $str_td_polite =
                    '<td class="wisePoliteFormPlainFormTableTd">' .
                        $str_td_target .
                    '</td>';
            } else {
                $str_td_plain =
                    '<td class="wisePoliteFormPlainFormTableTd">' .
                        $str_td_target .
                    '</td>';
            }
        }

        $str_td_title =
            '<td class="wiseUiFontSizeTarget">' .
                escape_html($arr_affirmative_negative_tense[$key_politeform_plainform][$int_selected_language]) .
            '</td>';

        $str_table_trs .=
            '<tr>' .
                $str_td_polite .
                $str_td_title .
                $str_td_plain .
            '</tr>';
    }

    $str_table =
        '<table id="wisePoliteFormPlainFormTable">' .
            '<tr>' .
                '<th class="wiseUiFontSizeTarget">' . escape_html($arr_select_form_style[$FORM_STYLE_POLITE][$int_selected_language]) . '</th>' .
                '<th id="wisePoliteFormPlainFormTableThWord" class="wiseUiFontSizeTarget" contenteditable="plaintext-only"></th>' .
                '<th class="wiseUiFontSizeTarget">' . escape_html($arr_select_form_style[$FORM_STYLE_PLAIN][$int_selected_language]) . '</th>' .
            '</tr>' .
            $str_table_trs .
        '</table>';

		
	$arr_button_reset = [
		'reset',
		'reset'
	];

    $str_reset = isset($arr_button_reset[$int_selected_language])
        ? $arr_button_reset[$int_selected_language]
        : $arr_button_reset[0];

    return
        '<div id="wisePanelPolitePlainTableView" class="wisePanelPolitePlainTableView wiseChartPanelContent wiseScrollableAreaContainer">' .
            '<section id="wisePanelPolitePlainTableSection" class="wiseScrollableArea">' .
                $str_table .
            '</section>' .
			'<section id="wisePanelChartTableButtonsSection" class="wisePanelChartButtonsSection">' .
                '<button id="wisePanelChartTableResetButton" class="tableResetButton wisePanelChartButton">' . escape_html($str_reset) . '</button>' .
            '</section>' .
        '</div>';
}


/******************************************************
 *  UI
 *
 ******************************************************/
function build_html_wise_chart_ui($int_selected_language)
{
    global $str_mark_cross;

    $arr_title_history = [
        '履歴',
        '歷史',
    ];

    $str_title_history = isset($arr_title_history[$int_selected_language])
        ? $arr_title_history[$int_selected_language]
        : $arr_title_history[0];

    $buttons_left = [
        '<button id="wisePanelChartUiHistoryCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        $str_title_history,
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelChartUiHistoryHeader'
    );

    $html_toolbar = '';

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '<ul id="wisePanelChartUiHistoryList" class="wisePanelUiList"></ul>',
        'wisePanelChartUiHistoryMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = build_html_loading_spinner('wisePanelChartUiHistoryLoading');

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelChartUiHistoryMainContentContainer'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelChartUiHistory',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'side'
    );
}