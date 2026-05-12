<?php

/******************************************************
 *  BODY
 *
 ******************************************************/
function build_html_wise_grammar_insights_body($int_selected_language)
{
    global
        $int_grammar_insights_display_titles,
        $int_grammar_insights_attribute_postJson,
        $int_grammar_insights_display_examples,
        $int_grammar_insights_user_input_data,
        $int_grammar_insights_sentences,
        $int_grammar_insights_random_sentences,
        $int_grammar_insights_active_recall,
        $int_grammar_insights_download_items,
        $int_grammar_insights_attribute_buttons,
        $int_grammar_insights_create_quiz_links,
        $int_grammar_insights_attribute_links,
        $int_grammar_insights_upsert_homework,
        $arr_str_grammar_insights_display_titles,
        $arr_str_grammar_insights_display_examples,
        $arr_str_grammar_insights_user_input_data,
        $arr_str_grammar_insights_sentences,
        $arr_str_grammar_insights_random_sentences,
        $arr_str_grammar_insights_active_recall,
        $arr_str_grammar_insights_download_items,
        $arr_str_grammar_insights_create_quiz_links,
        $arr_str_grammar_insights_upsert_homework,
        $arr_homework_days,
        $arr_homework_method,
        $arr_str_button_caption_exit;

	$buttons_left = [
		build_html_wise_panel_close_button('wisePanelGrammarInsightsViewCloseButton'),
		build_html_wise_panel_expand_button('wisePanelGrammarInsightsViewExpandButton'),
		build_html_wise_panel_split_button('wisePanelGrammarInsightsViewSplitButton'),
    ];

    $arr_options = [
        $int_grammar_insights_display_titles => [
            'text' => $arr_str_grammar_insights_display_titles[$int_selected_language],
            'attribute' => $int_grammar_insights_attribute_postJson
        ],
        $int_grammar_insights_display_examples => [
            'text' => $arr_str_grammar_insights_display_examples[$int_selected_language],
            'attribute' => $int_grammar_insights_attribute_postJson
        ],
        $int_grammar_insights_user_input_data => [
            'text' => $arr_str_grammar_insights_user_input_data[$int_selected_language],
            'attribute' => $int_grammar_insights_attribute_postJson
        ],
        $int_grammar_insights_sentences => [
            'text' => $arr_str_grammar_insights_sentences[$int_selected_language],
            'attribute' => $int_grammar_insights_attribute_postJson
        ],
        $int_grammar_insights_random_sentences => [
            'text' => $arr_str_grammar_insights_random_sentences[$int_selected_language],
            'attribute' => $int_grammar_insights_attribute_postJson
        ],
        $int_grammar_insights_active_recall => [
            'text' => $arr_str_grammar_insights_active_recall[$int_selected_language],
            'attribute' => $int_grammar_insights_attribute_postJson
        ],
        $int_grammar_insights_download_items => [
            'text' => $arr_str_grammar_insights_download_items[$int_selected_language],
            'attribute' => $int_grammar_insights_attribute_buttons
        ],
        $int_grammar_insights_create_quiz_links => [
            'text' => $arr_str_grammar_insights_create_quiz_links[$int_selected_language],
            'attribute' => $int_grammar_insights_attribute_links
        ],
        $int_grammar_insights_upsert_homework => [
            'text' => $arr_str_grammar_insights_upsert_homework[$int_selected_language],
            'attribute' => $int_grammar_insights_attribute_links
        ]
    ];

    $html_dropdown_menu_add_list = '';

    foreach ($arr_options as $key => $loop_options) {
        $int_option_value = escape_html_with_nl2br($key);
        $str_option_text_content = escape_html_with_nl2br($loop_options['text']);
        $str_option_attribute = escape_html_with_nl2br($loop_options['attribute']);

        if ($int_option_value === $int_grammar_insights_display_titles) {
            $html_dropdown_menu_add_list .= '<option value="' . $int_option_value . '" data-attribute="' . $str_option_attribute . '" selected>' . $str_option_text_content . '</option>';
        }
        else {
            $html_dropdown_menu_add_list .= '<option value="' . $int_option_value . '" data-attribute="' . $str_option_attribute . '">' . $str_option_text_content . '</option>';
        }
    }

    $html_dropdown_menu =
        '<div id="wisePanelGrammarInsightsViewDropDownMenuArea">
            <select id="wisePanelGrammarInsightsViewDropDownMenuSelect" name="wisePanelGrammarInsightsViewDropDownMenuSelect">' .
                $html_dropdown_menu_add_list .
            '</select>
        </div>';

    $html_zoom_in_icon = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    $buttons_right = [
        $html_dropdown_menu,
        '<button id="wisePanelGrammarInsightsViewButtonsContainerReSearchButton" class="wisePanelGrammarInsightsViewButtonsContainerButton">再検索</button>',
        '<button id="wisePanelGrammarInsightsViewButtonsContainerShowExplanationButton" class="wisePanelGrammarInsightsViewButtonsContainerButton showWisePanelGrammarExplanationButton">Explanation</button>',
        '<button id="wisePanelGrammarInsightsViewButtonsContainerZoomBigButton" class="wisePanelGrammarInsightsViewButtonsContainerButton wiseUiFontSizeIncreaseButton wiseUiFontSizeButton">' . $html_zoom_in_icon . '</button>',
        '<button id="wisePanelGrammarInsightsViewButtonsContainerZoomSmallButton" class="wisePanelGrammarInsightsViewButtonsContainerButton wiseUiFontSizeDecreaseButton wiseUiFontSizeButton">' . $html_zoom_out_icon . '</button>'
    ];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        '',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelGrammarInsightsViewHeader'
    );

    $html_homework_right_container = build_html_grammar_insights_homework_right_container(
        $arr_homework_days,
        $arr_homework_method,
        $int_selected_language
    );

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '
			<div id="grammarInsightsTitlesDisplayArea" class="grammarInsightsDisplayArea hidden"></div>
			<div id="grammarInsightsExamplesDisplayArea" class="grammarInsightsDisplayArea hidden"></div>
			<div id="grammarInsightsUserInputDataDisplayArea" class="grammarInsightsDisplayArea hidden"></div>
			<div id="grammarInsightsSentencesDisplayArea" class="grammarInsightsDisplayArea hidden"></div>
			<div id="grammarInsightsRandomSentencesDisplayArea" class="grammarInsightsDisplayArea hidden"></div>
			<div id="grammarInsightsLanguageFunctionsDisplayArea" class="grammarInsightsDisplayArea hidden"></div>
			<div id="grammarInsightsActiveRecallDisplayArea" class="grammarInsightsDisplayArea hidden"></div>
			<div id="grammarInsightsQuizLinksDisplayArea" class="grammarInsightsDisplayArea hidden"></div>

			<div id="grammarInsightsHomeworkLinkDisplayArea" class="grammarInsightsDisplayArea hidden">
				<div id="grammarInsightsHomeworkLinkDisplayAreaDualPaneContainer">
					<div id="grammarInsightsHomeworkLinkDisplayAreaLeftContainer">' .
						build_html_loading_spinner('grammarInsightsHomeworkLinkDisplayAreaLeftContainerLoading') .
						'<ul id="grammarInsightsHomeworkLinkDisplayAreaLeftContainerUl" class="grammarInsightsHomeworkLinkDisplayAreaUl"></ul>
					</div>' .
					$html_homework_right_container .
				'</div>
				<div id="grammarInsightsHomeworkLinkDisplayAreaAContainer"></div>
			</div>

			<div id="grammarInsightsButtonsDisplayArea" class="grammarInsightsDisplayArea hidden">
				<button id="grammarInsightsButtonsDisplayAreaDownloadPDFsOneColumnButton" class="grammarInsightsButtonsDisplayAreaButtons">PDF</button>
				<button id="grammarInsightsButtonsDisplayAreaDownloadPDFsTwoColumnsButton" class="grammarInsightsButtonsDisplayAreaButtons">PDFTwoCol</button>
				<button id="grammarInsightsButtonsDisplayAreaDownloadHTMLsOneColumnButton" class="grammarInsightsButtonsDisplayAreaButtons">HTML</button>
				<button id="grammarInsightsButtonsDisplayAreaDownloadHTMLsTwoColumnsButton" class="grammarInsightsButtonsDisplayAreaButtons">HTMLTwoCol</button>
				<button id="grammarInsightsButtonsDisplayAreaDownloadHTMLsTwoColumnsNoZipButton" class="grammarInsightsButtonsDisplayAreaButtons">HTMLNoZip</button>
			</div>
        ',
        'wisePanelGrammarInsightsViewMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = build_html_loading_spinner('grammarInsightsLoading');

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelGrammarInsightsViewMainContentContainer'
    );

    $html_toolbar = '';
    $html_handle = '';

    return build_html_wise_panel_view(
        'wisePanelGrammarInsightsView',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        $html_handle
    );
}


function build_html_grammar_insights_homework_right_container($homework_days, $homework_methods, $int_selected_language)
{
    global
        $arr_inflection_for_quiz,
        $str_homework_method_inflection,
        $str_homework_method_plainform,
        $str_snake_to_camel_unique_code,
        $arr_columns_masta_japanese_root;

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

    $arr_masta_japanese_classification = fetch_arr_masta_japanese_classification_for_quiz($int_selected_language);

    $html = '<div id="grammarInsightsHomeworkLinkDisplayAreaRightContainer">
                <div id="grammarInsightsHomeworkLinkDisplayAreaRightContainerButtonsContainer">
                    <button class="grammarInsightsHomeworkLinkDisplayAreaRightContainerDaySelector" data-day="1">D1</button>
                    <button class="grammarInsightsHomeworkLinkDisplayAreaRightContainerDaySelector" data-day="3">D3</button>
                    <button class="grammarInsightsHomeworkLinkDisplayAreaRightContainerDaySelector" data-day="5">D5</button>
                    <button id="grammarInsightsHomeworkLinkDisplayAreaRightContainerUpsertHomework">Upsert Homework</button>
                </div>';

    foreach ($homework_days as $day) {
        $html .= '<div class="grammarInsightsHomeworkLinkDisplayAreaRightContainerDayStatusContainer" data-day="' . escape_html($day) . '">';

        $html .= '<div class="grammarInsightsHomeworkLinkDisplayAreaRightContainerDayStatusContainerDynamic">';
        foreach ($homework_methods as $method) {
            $method_id = escape_html($method['id']);
            $method_title = escape_html($method['title']);

            if ($method_id === $str_homework_method_inflection || $method_id === $str_homework_method_plainform) {
                continue;
            }

            $ul_id = $method_id . 'Ul' . escape_html($day);
            $html .= '<div class="grammarInsightsHomeworkLinkDisplayAreaRightContainerTaskTypeContainer" data-type="' . $method_id . '">';
            $html .= '<div class="taskTitle">' . $method_title . '</div>';
            $html .= '<ul id="' . $ul_id . '" class="grammarInsightsHomeworkLinkDisplayAreaRightContainerUl grammarInsightsHomeworkLinkDisplayAreaUl" data-type="' . $method_id . '"></ul>';
            $html .= '</div>';
        }
        $html .= '</div>';

        $html .= '<div class="grammarInsightsHomeworkLinkDisplayAreaRightContainerDayStatusContainerStatic">';
        foreach ($homework_methods as $method) {
            $method_id = escape_html($method['id']);
            $method_title = escape_html($method['title']);
            $ul_id = $method_id . 'Ul' . escape_html($day);

            if ($method_id !== $str_homework_method_inflection && $method_id !== $str_homework_method_plainform) {
                continue;
            }

            $html_li = '';
            if ($method_id === $str_homework_method_inflection) {
                foreach ($sortedArray as $key => $items) {
                    $grammar_unique_code = escape_html($items[$str_snake_to_camel_unique_code]);
                    $str_japanese = escape_html($items[$arr_columns_masta_japanese_root[$int_selected_language]]);
                    $html_li .= '<li class="grammarInsightsHomeworkLinkDisplayAreaRightContainerLi grammarInsightsHomeworkLinkDisplayAreaLi grammarInsightsHomeworkLinkDisplayAreaLiWithInput" data-unique-code="' . $grammar_unique_code . '">';
                    $html_li .= '<input type="checkbox" class="grammarInsightsHomeworkLinkDisplayAreaLiCheckbox" data-unique-code="' . $grammar_unique_code . '" id="checkbox_' . $method_id . '_' . $day . '_' . $key . '">';
                    $html_li .= '<label for="checkbox_' . $method_id . '_' . $day . '_' . $key . '">' . $str_japanese . '</label>';
                    $html_li .= '</li>';
                }
            }
            elseif ($method_id === $str_homework_method_plainform) {
                foreach ($arr_masta_japanese_classification as $key => $items) {
                    $grammar_unique_code = escape_html($items[$str_snake_to_camel_unique_code]);
                    $str_japanese = escape_html($items[$arr_columns_masta_japanese_root[$int_selected_language]]);
                    $html_li .= '<li class="grammarInsightsHomeworkLinkDisplayAreaRightContainerLi grammarInsightsHomeworkLinkDisplayAreaLi grammarInsightsHomeworkLinkDisplayAreaLiWithInput" data-unique-code="' . $grammar_unique_code . '">';
                    $html_li .= '<input type="checkbox" class="grammarInsightsHomeworkLinkDisplayAreaLiCheckbox" data-unique-code="' . $grammar_unique_code . '" id="checkbox_' . $method_id . '_' . $day . '_' . $key . '">';
                    $html_li .= '<label for="checkbox_' . $method_id . '_' . $day . '_' . $key . '">' . $str_japanese . '</label>';
                    $html_li .= '</li>';
                }
            }

            $html .= '<div class="grammarInsightsHomeworkLinkDisplayAreaRightContainerTaskTypeContainer" data-type="' . $method_id . '">';
            $html .= '<div class="taskTitle">' . $method_title . '</div>';
            $html .= '<ul id="' . $ul_id . '" class="grammarInsightsHomeworkLinkDisplayAreaRightContainerUl grammarInsightsHomeworkLinkDisplayAreaUl" data-type="' . $method_id . '">' . $html_li . '</ul>';
            $html .= '</div>';
        }
        $html .= '</div>';

        $html .= '</div>';
    }

    $html .= '</div>';

    return $html;
}


/******************************************************
 *  UI
 *
 ******************************************************/
function build_html_wise_grammar_insights_ui($int_selected_language)
{
    $html = '';
    $html .= build_html_grammar_insights_update_user_input_data_overlay($int_selected_language);
    return $html;
}


function build_html_grammar_insights_update_user_input_data_overlay($int_selected_language)
{
    $html_overlay_close_button = build_html_overlay_close_button();

    $html = '';
    $str_title = 'Update User Input Data';

    $html =
        '<div id="wiseUpdateUserInputDataScreen" class="wiseScreenModal">' .
            $html_overlay_close_button .
            '<h2 id="wiseUpdateUserInputDataTitle">' . $str_title . '</h2>' .
            '<button id="wiseUpdateUserInputDataScreenUpdateButton">Update</button>' .
            '<div id="wiseUpdateUserInputDataContentsContainer" class="modalScrollableContainer">' .
                build_html_loading_spinner('wiseUpdateUserInputDataLoading') .
                '<div id="wiseUpdateUserInputDataContents">' .
                    '<ul id="wiseUpdateUserInputDataList"></ul>' .
                '</div>' .
            '</div>' .
        '</div>';

    $html =
        '<div id="wiseUpdateUserInputDataScreenOverlay" class="wisePanelScreenOverlay">' . $html . '</div>';

    return $html;
}