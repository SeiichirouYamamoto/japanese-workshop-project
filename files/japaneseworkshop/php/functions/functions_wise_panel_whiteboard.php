<?php


function build_html_wise_whiteboard_body($int_selected_language)
{
    $html_canvas = build_html_wise_canvas($int_selected_language);

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '',
        'wisePanelWhiteboardViewMainContentArea',
        'wisePanelMainContentArea wiseContainersMainContentArea'
    );

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_canvas
        . $html_main_content_area
		. '<div id="wiseBoardMarksLayer" class="wiseBoardMarksLayer wisePointerNone"></div>',
        '',
        'wiseZoomStage',
        'wisePanelMainContentContainer wisePanelWhiteboardZoomStage'
    );

    return build_html_wise_panel_view(
        'wisePanelWhiteboardView',
        '',
        '',
        $html_main_content_container,
        ''
    );
}

function build_html_wise_canvas($int_selected_language)
{
    $str_canvas = '';

    $str_canvas .= '<div id="canvasLinkedContainer"></div>';
    $str_canvas .= '<canvas id="wiseCanvasOriginal" class="wiseCanvas"></canvas>';
    $str_canvas .= '<canvas id="wiseCanvasHandWriting" class="wiseCanvas"></canvas>';

    return '<div id="wiseCanvasContainer" class="wiseCanvasContainerRoot">' . $str_canvas . '</div>';
}


function build_html_wise_whiteboard_ui($int_selected_language){

    return [
        build_html_wise_whiteboard_ui_zoom(),

        build_html_wise_whiteboard_ui_context_menu($int_selected_language),

        build_html_wise_whiteboard_ui_form_list($int_selected_language),
        build_html_wise_whiteboard_ui_settings($int_selected_language),
        build_html_wise_whiteboard_ui_label_list($int_selected_language),

        build_html_wise_whiteboard_ui_create_sticky_note($int_selected_language),

        build_html_wise_whiteboard_ui_word_information($int_selected_language),
        build_html_wise_whiteboard_ui_word_search_set($int_selected_language),

        build_html_wise_whiteboard_ui_history($int_selected_language),
        build_html_wise_whiteboard_ui_created_word_history($int_selected_language),
        build_html_wise_whiteboard_ui_chart_history($int_selected_language),
        build_html_wise_whiteboard_ui_action_history($int_selected_language),

    ];
	
}

/******************************************************
 *  UI
 *  
 ******************************************************/

function build_html_wise_whiteboard_ui_context_menu($int_selected_language)
{
    $html_contents = ''
        . '<ul id="wiseContextMenuUl">'
            . '<li id="wiseContextMenuDelete" class="wiseContextLi wiseContextLiWord wiseContextLiStickyNote wiseContextLiTextArea">削除</li>'
            . '<li id="wiseContextMenuInflection" class="wiseContextLi wiseContextLiWord wiseContextLiPuzzlePiece">語形変化</li>'
            . '<li id="wiseContextMenuChangeLabel" class="wiseContextLi wiseContextLiWord">表記変更</li>'
            . '<li id="wiseContextMenuWordInformation" class="wiseContextLi wiseContextLiWord wiseContextLiPuzzlePiece">単語情報</li>'
            // . '<li id="wiseContextMenuPoliteFormPlainFormTable" class="wiseContextLi wiseContextLiWord">丁寧形普通形表</li>'
            . '<li id="wiseContextMenuCreatePhraseClause" class="wiseContextLi wiseContextLiWord">まとめる</li>'
            . '<li id="wiseContextMenuAlignElements" class="wiseContextLi wiseContextLiWord">並べ替え</li>'
            . '<li id="wiseContextMenuSliceElements" class="wiseContextLi wiseContextLiStickyNote wiseContextLiTextArea">分ける</li>'
        . '</ul>';

    return build_html_wise_panel_ui_item(
        'wiseContextMenu',
        '',
        '',
        '',
        $html_contents,
        'float',
        true,
        'wisePanelUi wiseContextMenuRoot wiseHitItem',
        ''
    );
}

function build_html_wise_whiteboard_ui_zoom() {

    $html_zoom_in  = build_html_magnifier_icon('plus');
    $html_zoom_out = build_html_magnifier_icon('minus');

    $html = '<div id="wiseZoomContainerInner">'
          . '<button id="zoomWhiteboardIn" class="wiseZoomButton">' . $html_zoom_in . '</button>'
          . '<button id="zoomWhiteboardOut" class="wiseZoomButton">' . $html_zoom_out . '</button>'
          . '</div>';

    return build_html_wise_panel_ui_item(
        'wiseZoomContainer',
        '',
        '',
        '',
        $html,
        'float',
        false
    );
}


function build_html_wise_whiteboard_ui_settings($int_selected_language)
{
    global $str_mark_cross;

    $html_zoom_in_icon  = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    $buttons_left = [
        '<button id="wisePanelWhiteboardUiSettingsCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [
        '<button class="wiseUiFontSizeIncreaseButton wiseUiFontSizeButton">' . $html_zoom_in_icon . '</button>',
        '<button class="wiseUiFontSizeDecreaseButton wiseUiFontSizeButton">' . $html_zoom_out_icon . '</button>',
    ];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        '設定',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelWhiteboardUiSettingsHeader'
    );

    $html_toolbar = '';

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '
        <ul id="wisePanelWhiteboardUiSettingsList" class="wisePanelUiList wiseUiFontSizeTarget">
            <li id="wisePanelWhiteboardUiSettingsLiChangeEraserSizeBig" class="wiseUiFontSizeTarget">消しゴムを大きくする</li>
            <li id="wisePanelWhiteboardUiSettingsLiChangeEraserSizeSmall" class="wiseUiFontSizeTarget">消しゴムを小さくする</li>
            <li id="wisePanelWhiteboardUiSettingsLiResizeEvent" class="wiseUiFontSizeTarget">windowをresize</li>
            <li id="wisePanelWhiteboardUiSettingsLiWiseWaitMode" class="wiseUiFontSizeTarget">Wait Mode</li>
        </ul>
        ',
        'wisePanelWhiteboardUiSettingsMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = '';

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelWhiteboardUiSettingsMainContentContainer'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelWhiteboardUiSettings',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'float',
		true,
		'wisePanelUi wiseHitItem wisePanelWhiteboardUi wisePanelWhiteboardUiRight'
    );
}


function build_html_wise_whiteboard_ui_word_search($int_selected_language)
{
    global
        $arr_select_form_style,
        $arr_select_output_style,
        $arr_select_order_style,
        $arr_select_kana_visible,
        $arr_select_matching_type,
        $arr_mastery_level,
        $t_masta_japanese_classification,
        $t_masta_japanese_root,
        $arr_columns_masta_japanese_root,
        $arr_select_select_all,
        $arr_select_learning_scope,
        $str_mark_cross;

    $str_select_form_style = '';
    foreach ($arr_select_form_style as $key => $loop_form_style) {
        $int_option_value = $key;
        $str_option_text_content = escape_html_with_nl2br($loop_form_style[$int_selected_language]);
        $str_select_form_style .= '<option value="' . $int_option_value . '">' . $str_option_text_content . '</option>';
    }

    $str_select_output_style = '';
    foreach ($arr_select_output_style as $key => $loop_output_style) {
        $int_option_value = $key;
        $str_option_text_content = escape_html_with_nl2br($loop_output_style[$int_selected_language]);
        $str_select_output_style .= '<option value="' . $int_option_value . '">' . $str_option_text_content . '</option>';
    }

    $str_select_order_style = '';
    foreach ($arr_select_order_style as $key => $loop_order_style) {
        $int_option_value = $key;
        $str_option_text_content = escape_html_with_nl2br($loop_order_style[$int_selected_language]);
        $str_select_order_style .= '<option value="' . $int_option_value . '">' . $str_option_text_content . '</option>';
    }

    $str_select_kana_visible = '';
    foreach ($arr_select_kana_visible as $key => $loop_kana_visible) {
        $int_option_value = $key;
        $str_option_text_content = escape_html_with_nl2br($loop_kana_visible[$int_selected_language]);
        $str_select_kana_visible .= '<option value="' . $int_option_value . '">' . $str_option_text_content . '</option>';
    }

    $str_select_matching_type = '';
    foreach ($arr_select_matching_type as $key => $loop_matching_type) {
        $int_option_value = $key;
        $str_option_text_content = escape_html_with_nl2br($loop_matching_type[$int_selected_language]);
        $str_select_matching_type .= '<option value="' . $int_option_value . '">' . $str_option_text_content . '</option>';
    }

    $str_select_mastery_level = '';
    foreach ($arr_mastery_level as $key => $loop_mastery_level) {
        $int_option_value = $key;
        $str_option_text_content = escape_html_with_nl2br($loop_mastery_level[$int_selected_language]);
        $str_select_mastery_level .= '<option value="' . $int_option_value . '">' . $str_option_text_content . '</option>';
    }

    $arr_strSQL_select = [
        [$t_masta_japanese_classification, 'id'],
        [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language]]
    ];

    $strSQL_from = " FROM
                    $t_masta_japanese_classification
                    INNER JOIN $t_masta_japanese_root
                    ON
                    $t_masta_japanese_classification.masta_japanese_root_id = $t_masta_japanese_root.id
                    ";

    $arr_strSQL_where = [];

    $arr_strSQL_order = [
        [$t_masta_japanese_classification, 'sort', 'ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_classification) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    $arr_masta_japanese_classification = array_column($arr_masta_japanese_classification, null, 'id');

    $options = [];
    $options[] = ['id' => SELECT_ALL, 'label' => $arr_select_select_all[$int_selected_language]];

    ksort($arr_masta_japanese_classification, SORT_NUMERIC);
    foreach ($arr_masta_japanese_classification as $row) {
        $options[] = [
            'id' => (int)$row['id'],
            'label' => $row[$arr_columns_masta_japanese_root[$int_selected_language]]
        ];
    }

    $str_select_japanese_classification = '';
    foreach ($options as $opt) {
        $str_select_japanese_classification .=
            '<option value="' . escape_html_with_nl2br($opt['id']) . '">' . escape_html_with_nl2br($opt['label']) . '</option>';
    }

    $str_learning_scope_area = '';
    foreach ($arr_select_learning_scope as $key => $loop_knowledge_area) {
        $int_option_value_selection = $key;
        $str_option_text_content_selection = escape_html_with_nl2br($loop_knowledge_area[$int_selected_language]);
        $str_learning_scope_area .= '<option value="' . $int_option_value_selection . '">' . $str_option_text_content_selection . '</option>';
    }

    $html_zoom_in_icon = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    $buttons_left = [
        '<button id="wisePanelWhiteboardUiWordSearchCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [
        '<button class="wiseUiFontSizeIncreaseButton wiseUiFontSizeButton">' . $html_zoom_in_icon . '</button>',
        '<button class="wiseUiFontSizeDecreaseButton wiseUiFontSizeButton">' . $html_zoom_out_icon . '</button>',
        '<button id="wisePanelWhiteboardUiWordSearchOpenOptionsButton" class="wisePanelWhiteboardUiButton">options</button>',
    ];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        '単語検索',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelWhiteboardUiWordSearchHeader'
    );

    $html_toolbar = build_html_wise_panel_toolbar(
        '
        <div id="wisePanelWhiteboardUiWordSearchSearchArea" class="wisePanelWhiteboardUiSearchArea">
            <form id="wisePanelWhiteboardUiWordSearchSearchForm" class="wisePanelWhiteboardUiSearchForm">
                <input type="text" id="wisePanelWhiteboardUiWordSearchSearchInput" class="wisePanelWhiteboardUiSearchInput wisePanelWhiteboardUiTextInputArea" placeholder="検索ワードを入力">
            </form>
            <button id="wisePanelWhiteboardUiWordSearchSearchButton" class="wisePanelWhiteboardUiSearchAreaButton">検索</button>
            <button id="wisePanelWhiteboardUiWordSearchCreateButton" class="wisePanelWhiteboardUiSearchAreaButton hidden">作成</button>
        </div>
        ',
        'wisePanelWhiteboardUiWordSearchToolbar'
    );

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '
        <div id="wisePanelWhiteboardUiWordSearchDisplayAreaContainer" class="wisePanelWhiteboardUiContentsContainer">
            <ul id="wisePanelWhiteboardUiWordSearchList" class="wisePanelWhiteboardUiSelectableList wiseUiFontSizeTarget">
            </ul>
        </div>
        ',
        'wisePanelWhiteboardUiWordSearchMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = build_html_loading_spinner('wisePanelWhiteboardUiWordSearchLoading');

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelWhiteboardUiWordSearchMainContentContainer'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelWhiteboardUiWordSearch',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'float',
        true,
        'wisePanelUi wiseHitItem wisePanelWhiteboardUi wisePanelWhiteboardUiRight'
    );
}

function build_html_wise_whiteboard_ui_word_search_options($int_selected_language)
{
    global
        $arr_select_form_style,
        $arr_select_output_style,
        $arr_select_order_style,
        $arr_select_kana_visible,
        $arr_select_matching_type,
        $arr_mastery_level,
        $t_masta_japanese_classification,
        $t_masta_japanese_root,
        $arr_columns_masta_japanese_root,
        $arr_select_select_all,
        $arr_select_learning_scope,
        $str_mark_cross;

    $str_select_form_style = '';
    foreach ($arr_select_form_style as $key => $loop_form_style) {
        $int_option_value = $key;
        $str_option_text_content = escape_html_with_nl2br($loop_form_style[$int_selected_language]);
        $str_select_form_style .= '<option value="' . $int_option_value . '">' . $str_option_text_content . '</option>';
    }

    $str_select_output_style = '';
    foreach ($arr_select_output_style as $key => $loop_output_style) {
        $int_option_value = $key;
        $str_option_text_content = escape_html_with_nl2br($loop_output_style[$int_selected_language]);
        $str_select_output_style .= '<option value="' . $int_option_value . '">' . $str_option_text_content . '</option>';
    }

    $str_select_order_style = '';
    foreach ($arr_select_order_style as $key => $loop_order_style) {
        $int_option_value = $key;
        $str_option_text_content = escape_html_with_nl2br($loop_order_style[$int_selected_language]);
        $str_select_order_style .= '<option value="' . $int_option_value . '">' . $str_option_text_content . '</option>';
    }

    $str_select_kana_visible = '';
    foreach ($arr_select_kana_visible as $key => $loop_kana_visible) {
        $int_option_value = $key;
        $str_option_text_content = escape_html_with_nl2br($loop_kana_visible[$int_selected_language]);
        $str_select_kana_visible .= '<option value="' . $int_option_value . '">' . $str_option_text_content . '</option>';
    }

    $str_select_matching_type = '';
    foreach ($arr_select_matching_type as $key => $loop_matching_type) {
        $int_option_value = $key;
        $str_option_text_content = escape_html_with_nl2br($loop_matching_type[$int_selected_language]);
        $str_select_matching_type .= '<option value="' . $int_option_value . '">' . $str_option_text_content . '</option>';
    }

    $str_select_mastery_level = '';
    foreach ($arr_mastery_level as $key => $loop_mastery_level) {
        $int_option_value = $key;
        $str_option_text_content = escape_html_with_nl2br($loop_mastery_level[$int_selected_language]);
        $str_select_mastery_level .= '<option value="' . $int_option_value . '">' . $str_option_text_content . '</option>';
    }

    $arr_strSQL_select = [
        [$t_masta_japanese_classification, 'id'],
        [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language]]
    ];

    $strSQL_from = " FROM
                    $t_masta_japanese_classification
                    INNER JOIN $t_masta_japanese_root
                    ON
                    $t_masta_japanese_classification.masta_japanese_root_id = $t_masta_japanese_root.id
                    ";

    $arr_strSQL_where = [];

    $arr_strSQL_order = [
        [$t_masta_japanese_classification, 'sort', 'ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_classification) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    $arr_masta_japanese_classification = array_column($arr_masta_japanese_classification, null, 'id');

    $options = [];
    $options[] = ['id' => SELECT_ALL, 'label' => $arr_select_select_all[$int_selected_language]];

    ksort($arr_masta_japanese_classification, SORT_NUMERIC);
    foreach ($arr_masta_japanese_classification as $row) {
        $options[] = [
            'id' => (int)$row['id'],
            'label' => $row[$arr_columns_masta_japanese_root[$int_selected_language]]
        ];
    }

    $str_select_japanese_classification = '';
    foreach ($options as $opt) {
        $str_select_japanese_classification .=
            '<option value="' . escape_html_with_nl2br($opt['id']) . '">' . escape_html_with_nl2br($opt['label']) . '</option>';
    }

    $str_learning_scope_area = '';
    foreach ($arr_select_learning_scope as $key => $loop_knowledge_area) {
        $int_option_value_selection = $key;
        $str_option_text_content_selection = escape_html_with_nl2br($loop_knowledge_area[$int_selected_language]);
        $str_learning_scope_area .= '<option value="' . $int_option_value_selection . '">' . $str_option_text_content_selection . '</option>';
    }

    $html_zoom_in_icon = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    $buttons_left = [
        '<button id="wisePanelWhiteboardUiWordSearchOptionsCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [
        '<button id="wisePanelWhiteboardUiWordSearchOptionsResetButton" class="wisePanelWhiteboardUiButton">reset</button>',
        '<button class="wiseUiFontSizeIncreaseButton wiseUiFontSizeButton">' . $html_zoom_in_icon . '</button>',
        '<button class="wiseUiFontSizeDecreaseButton wiseUiFontSizeButton">' . $html_zoom_out_icon . '</button>',
    ];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        '検索オプション',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelWhiteboardUiWordSearchOptionsHeader'
    );

    $html_toolbar = '';

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '
        <div id="wisePanelWhiteboardUiWordSearchOptionsFormStyle" class="wisePanelWhiteboardUiOptionsContainer">
            <div class="wisePanelWhiteboardUiOptionsHeadline">出力するスタイル</div>
            <select id="wisePanelWhiteboardUiWordSearchSelectFormStyle" name="wisePanelWhiteboardUiWordSearchSelectFormStyle">
                ' . $str_select_form_style . '
            </select>
        </div>

        <div id="wisePanelWhiteboardUiWordSearchOptionsOutputStyle" class="wisePanelWhiteboardUiOptionsContainer">
            <div class="wisePanelWhiteboardUiOptionsHeadline">出力先</div>
            <select id="wisePanelWhiteboardUiWordSearchSelectOutputStyle" name="wisePanelWhiteboardUiWordSearchSelectOutputStyle">
                ' . $str_select_output_style . '
            </select>
        </div>

        <div id="wisePanelWhiteboardUiWordSearchOptionsOrderStyle" class="wisePanelWhiteboardUiOptionsContainer">
            <div class="wisePanelWhiteboardUiOptionsHeadline">順序</div>
            <select id="wisePanelWhiteboardUiWordSearchSelectOrderStyle" name="wisePanelWhiteboardUiWordSearchSelectOrderStyle">
                ' . $str_select_order_style . '
            </select>
        </div>

        <div id="wisePanelWhiteboardUiWordSearchOptionsKanaVisible" class="wisePanelWhiteboardUiOptionsContainer">
            <div class="wisePanelWhiteboardUiOptionsHeadline">かな表示</div>
            <select id="wisePanelWhiteboardUiWordSearchSelectKanaVisible" name="wisePanelWhiteboardUiWordSearchSelectKanaVisible">
                ' . $str_select_kana_visible . '
            </select>
        </div>

        <div id="wisePanelWhiteboardUiWordSearchOptionsMatchingType" class="wisePanelWhiteboardUiOptionsContainer">
            <div class="wisePanelWhiteboardUiOptionsHeadline">文字列一致</div>
            <select id="wisePanelWhiteboardUiWordSearchSelectMatchingType" name="wisePanelWhiteboardUiWordSearchSelectMatchingType">
                ' . $str_select_matching_type . '
            </select>
        </div>

        <div id="wisePanelWhiteboardUiWordSearchOptionsKnowledgeArea" class="wisePanelWhiteboardUiOptionsContainer">
            <div class="wisePanelWhiteboardUiOptionsHeadline">学習範囲</div>
            <select id="wisePanelWhiteboardUiWordSearchSelectKnowledgeArea" name="wisePanelWhiteboardUiWordSearchSelectKnowledgeArea">
                ' . $str_learning_scope_area . '
            </select>
        </div>

        <div id="wisePanelWhiteboardUiWordSearchOptionsJapaneseClassification" class="wisePanelWhiteboardUiOptionsContainer">
            <div class="wisePanelWhiteboardUiOptionsHeadline">品詞</div>
            <select id="wisePanelWhiteboardUiWordSearchSelectJapaneseClassification" name="wisePanelWhiteboardUiWordSearchSelectJapaneseClassification">
                ' . $str_select_japanese_classification . '
            </select>
        </div>

        <div id="wisePanelWhiteboardUiWordSearchOptionsMasteryLevel" class="wisePanelWhiteboardUiOptionsContainer">
            <div class="wisePanelWhiteboardUiOptionsHeadline">Level</div>
            <select id="wisePanelWhiteboardUiWordSearchSelectMasteryLevel" name="wisePanelWhiteboardUiWordSearchSelectMasteryLevel">
                ' . $str_select_mastery_level . '
            </select>
        </div>
        ',
        'wisePanelWhiteboardUiWordSearchOptionsMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = '';

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelWhiteboardUiWordSearchOptionsMainContentContainer'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelWhiteboardUiWordSearchOptions',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'float',
        true,
        'wisePanelUi wiseHitItem wisePanelWhiteboardUi wisePanelWhiteboardUiLeft'
    );
}

function build_html_wise_whiteboard_ui_word_search_set($int_selected_language)
{
    $html = '';
    $html .= build_html_wise_whiteboard_ui_word_search($int_selected_language);
    $html .= build_html_wise_whiteboard_ui_word_search_options($int_selected_language);

    return $html;
}

function build_html_wise_whiteboard_ui_create_sticky_note($int_selected_language)
{
    global $str_mark_cross;

    $buttons_left = [
        '<button id="wisePanelWhiteboardUiCreateStickyNoteCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        '付箋作成',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelWhiteboardUiCreateStickyNoteHeader'
    );

    $html_toolbar = '';

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '
        <div id="wisePanelWhiteboardUiCreateStickyNoteDrawingColors" class="wisePanelWhiteboardUiCreateStickyNoteDrawingColors">
            <div id="wisePanelWhiteboardUiCreateStickyNoteDrawingColorsWhite" class="wisePanelWhiteboardUiCreateStickyNoteButtonSelectColor SelectColorWhite" data-drawing-color="white"></div>
            <div id="wisePanelWhiteboardUiCreateStickyNoteDrawingColorsRed" class="wisePanelWhiteboardUiCreateStickyNoteButtonSelectColor SelectColorRed" data-drawing-color="red"></div>
            <div id="wisePanelWhiteboardUiCreateStickyNoteDrawingColorsBlue" class="wisePanelWhiteboardUiCreateStickyNoteButtonSelectColor SelectColorBlue" data-drawing-color="blue"></div>
            <div id="wisePanelWhiteboardUiCreateStickyNoteDrawingColorsOrange" class="wisePanelWhiteboardUiCreateStickyNoteButtonSelectColor SelectColorOrange" data-drawing-color="orange"></div>
            <div id="wisePanelWhiteboardUiCreateStickyNoteDrawingColorsGreen" class="wisePanelWhiteboardUiCreateStickyNoteButtonSelectColor SelectColorGreen" data-drawing-color="green"></div>
            <div id="wisePanelWhiteboardUiCreateStickyNoteDrawingColorsYellow" class="wisePanelWhiteboardUiCreateStickyNoteButtonSelectColor SelectColorYellow" data-drawing-color="yellow"></div>
        </div>
        ',
        'wisePanelWhiteboardUiCreateStickyNoteMainContentArea'
    );

    $html_loading = '';

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelWhiteboardUiCreateStickyNoteMainContentContainer'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelWhiteboardUiCreateStickyNote',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'float',
        true,
        'wisePanelUi wiseHitItem wisePanelWhiteboardUi wisePanelWhiteboardUiRight'
    );
}

function build_html_wise_whiteboard_ui_label_list($int_selected_language)
{
    global $str_mark_cross;

    $html_zoom_in_icon  = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    $buttons_left = [
        '<button id="wisePanelWhiteboardUiLabelListCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [
        '<button class="wiseUiFontSizeIncreaseButton wiseUiFontSizeButton">' . $html_zoom_in_icon . '</button>',
        '<button class="wiseUiFontSizeDecreaseButton wiseUiFontSizeButton">' . $html_zoom_out_icon . '</button>',
    ];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        '表記変更',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelWhiteboardUiLabelListHeader'
    );

    $html_toolbar = '';

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '
        <ul id="wisePanelWhiteboardUiLabelListList" class="wisePanelUiList wiseUiFontSizeTarget">
        </ul>
        ',
        'wisePanelWhiteboardUiLabelListMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = '';

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelWhiteboardUiLabelListMainContentContainer'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelWhiteboardUiLabelList',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'float',
        true,
        'wisePanelUi wiseHitItem wisePanelWhiteboardUi wisePanelWhiteboardUiRight'
    );
}


function build_html_wise_whiteboard_ui_form_list($int_selected_language)
{
    global $str_mark_cross;

    $html_zoom_in_icon  = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    $buttons_left = [
        '<button id="wisePanelWhiteboardUiFormListCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [
        '<button class="wiseUiFontSizeIncreaseButton wiseUiFontSizeButton">' . $html_zoom_in_icon . '</button>',
        '<button class="wiseUiFontSizeDecreaseButton wiseUiFontSizeButton">' . $html_zoom_out_icon . '</button>',
    ];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        '活用形一覧',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelWhiteboardUiFormListHeader'
    );

    $html_toolbar = '';

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '
        <ul id="wisePanelWhiteboardUiFormListUl" class="wisePanelUiList wiseUiFontSizeTarget">
        </ul>
        ',
        'wisePanelWhiteboardUiFormListMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = '';

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelWhiteboardUiFormListMainContentContainer'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelWhiteboardUiFormList',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'float',
        true,
        'wisePanelUi wiseHitItem wisePanelWhiteboardUi wisePanelWhiteboardUiRight'
    );
}


function build_html_wise_whiteboard_ui_word_information($int_selected_language)
{
    global $str_mark_cross;

    $buttons_left = [
        '<button id="wisePanelWhiteboardUiWordInformationCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        '単語情報',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelWhiteboardUiWordInformationHeader'
    );

    $html_toolbar = '';

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '
        <div id="wisePanelWhiteboardUiWordInformationResult">
            <div id="wisePanelWhiteboardUiWordInformationResultJapaneseContainer">
                <div class="wisePanelWhiteboardUiWordInformationResultHeadline">単語</div>
            </div>
            <div id="wisePanelWhiteboardUiWordInformationResultKanaContainer">
                <div class="wisePanelWhiteboardUiWordInformationResultHeadline">ひらがな</div>
            </div>
            <div id="wisePanelWhiteboardUiWordInformationResultExplanationContainer">
                <div class="wisePanelWhiteboardUiWordInformationResultHeadline">説明</div>
            </div>
        </div>
        ',
        'wisePanelWhiteboardUiWordInformationMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = '';

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelWhiteboardUiWordInformationMainContentContainer'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelWhiteboardUiWordInformation',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'float',
        true,
        'wisePanelUi wiseHitItem wisePanelWhiteboardUi wisePanelWhiteboardUiLeft'
    );
}