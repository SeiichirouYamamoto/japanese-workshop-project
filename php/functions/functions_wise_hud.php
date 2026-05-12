<?php

/**
 * HUD中身
 */
function build_html_wise_main_hud_contents($arr_visible_right_buttons, $int_selected_language){

    return [
        build_html_wise_vertical_toolbar(false, $int_selected_language),
        build_html_wise_right_vertical_toolbar($arr_visible_right_buttons, $int_selected_language),
		build_html_wise_panel_position_select_ui($int_selected_language),
        build_html_wise_banner_advertisement(false, $int_selected_language),
    ];
}



/******************************************************
 *  TOOLBAR
 *  
 ******************************************************/
function build_html_wise_vertical_toolbar($doRegister, $int_selected_language){

    global
        $path_images_verticalToolbarContainer;

    $html_buttons = '';

    $url_images_verticalToolbarContainer = get_home_url(
        get_main_site_id(),
        trailingslashit(ltrim($path_images_verticalToolbarContainer, '/'))
    );

    $html_buttons .= '
    <button id="wiseVerticalToolbarDrawingButton" class="wiseVerticalToolbarButton wiseLeftVerticalToolbarButton wiseScanTarget wiseScanActor">
        <img src="'.jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarDrawingButton.png').'" alt="ペン" title="ペン">
    </button>';

    $html_buttons .= '
    <button id="wiseVerticalToolbarEraserButton" class="wiseVerticalToolbarButton wiseLeftVerticalToolbarButton wiseScanTarget wiseScanActor">
        <img src="'.jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarEraserButton.png').'" alt="消しゴム" title="消しゴム">
    </button>';

    $html_buttons .= '
    <button id="wiseVerticalToolbarSelectorButton" class="wiseVerticalToolbarButton wiseLeftVerticalToolbarButton wiseScanTarget wiseScanActor">
        <img src="'.jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarSelectorButton.png').'" alt="セレクト" title="セレクト">
    </button>';

    if ($doRegister) {
        $html_buttons .= '
        <button id="wiseVerticalToolbarCreateLinkButton" class="wiseVerticalToolbarButton wiseLeftVerticalToolbarButton wiseScanTarget wiseScanActor">
            <img src="'.jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarCreateLinkButton.png').'" alt="リンク" title="リンク">
        </button>';
    }

    $html_buttons .= '
    <button id="wiseVerticalToolbarMenuListOpenerAddElement" class="wiseVerticalToolbarButton wiseLeftVerticalToolbarButton wiseScanTarget wiseScanActor">
        <img src="'.jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarMenuListOpenerAddElement.png').'" alt="追加" title="追加">
    </button>';

    $html_buttons .= '
    <button id="wiseVerticalToolbarMenuListOpenerTools" class="wiseVerticalToolbarButton wiseLeftVerticalToolbarButton wiseScanTarget wiseScanActor">
        <img src="'.jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarToolsButton.png').'" alt="ツール" title="ツール">
    </button>';

    $html_buttons .= '
    <button id="wiseVerticalToolbarLaserButton" class="wiseVerticalToolbarButton wiseLeftVerticalToolbarButton wiseScanTarget wiseScanActor">
        <img src="'.jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarLaserButton.png').'" alt="レーザー" title="レーザー">
    </button>';

    $html_current_button = '
    <button id="wiseVerticalToolbarCurrentButton" class="wiseVerticalToolbarButton wiseToolbarMainButton wiseToolbarCurrentButton wiseScanTarget" type="button">
        <img src="'.jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarSelectorButton.png').'" alt="セレクト" title="セレクト">
    </button>';

    $html = '
    <div id="wiseVerticalToolbarContainer" class="wiseExpandableToolbar is-collapsed">
        <div class="wiseToolbarButtonsArea">
            '.$html_buttons.'
        </div>
        <div class="wiseToolbarCurrentButtonArea">
            '.$html_current_button.'
        </div>
    </div>';

    $html_colors = build_html_wise_menubar_colors($int_selected_language);
    $html_erasers = build_html_wise_menubar_erasers($int_selected_language);
    $html_links = build_html_wise_menubar_links($int_selected_language);
    $html_tools = build_html_wise_menubar_tools($doRegister, $int_selected_language);

    if ($doRegister) {
        $html_add_element = build_html_wise_menubar_add_element_for_simple($int_selected_language);
    }
    else {
        $html_add_element = build_html_wise_menubar_add_element($int_selected_language);
    }

    $html .= $html_colors;
    $html .= $html_erasers;
    $html .= $html_links;
    $html .= $html_tools;
    $html .= $html_add_element;

    return $html;
}


function build_html_wise_vertical_toolbar_for_editing($int_selected_language){

    global
        $path_images_verticalToolbarContainer;

    $url_images_verticalToolbarContainer = get_home_url(
        get_main_site_id(),
        trailingslashit(ltrim($path_images_verticalToolbarContainer, '/'))
    );

    $html_buttons = '';

    $html_buttons .= '
    <div id="wiseVerticalToolbarEraserButton" class="wiseVerticalToolbarButton wiseLeftVerticalToolbarButton wiseScanTarget wiseScanActor">
        <img src="'.jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarEraserButton.png').'" alt="消しゴム" title="消しゴム">
    </div>';

    $html_buttons .= '
    <div id="wiseVerticalToolbarSelectorButton" class="wiseVerticalToolbarButton wiseLeftVerticalToolbarButton wiseScanTarget wiseScanActor">
        <img src="'.jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarSelectorButton.png').'" alt="セレクト" title="セレクト">
    </div>';

    $html_buttons .= '
    <div id="wiseVerticalToolbarCreateLinkButton" class="wiseVerticalToolbarButton wiseLeftVerticalToolbarButton wiseScanTarget wiseScanActor">
        <img src="'.jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarCreateLinkButton.png').'" alt="リンク" title="リンク">
    </div>';

    $html_buttons .= '
    <div id="wiseVerticalToolbarMenuListOpenerAddElement" class="wiseVerticalToolbarButton wiseLeftVerticalToolbarButton wiseScanTarget wiseScanActor">
        <img src="'.jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarMenuListOpenerAddElement.png').'" alt="追加" title="追加">
    </div>';

    $html_buttons .= '
    <div id="wiseVerticalToolbarMenuListOpenerTools" class="wiseVerticalToolbarButton wiseLeftVerticalToolbarButton wiseScanTarget wiseScanActor">
        <img src="'.jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarToolsButton.png').'" alt="ツール" title="ツール">
    </div>';

    $html_current_button = '
    <button id="wiseVerticalToolbarCurrentButton" class="wiseVerticalToolbarButton wiseToolbarMainButton wiseToolbarCurrentButton wiseScanTarget" type="button">
        <img src="'.jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarSelectorButton.png').'" alt="セレクト" title="セレクト">
    </button>';

    $html = '
    <div id="wiseVerticalToolbarContainer" class="wiseExpandableToolbar is-collapsed">
        <div class="wiseToolbarButtonsArea">
            '.$html_buttons.'
        </div>
        <div class="wiseToolbarCurrentButtonArea">
            '.$html_current_button.'
        </div>
    </div>';

    $html_erasers = build_html_wise_menubar_erasers($int_selected_language);
    $html_links = build_html_wise_menubar_links($int_selected_language);
    $html_tools = build_html_wise_menubar_tools_for_editing($int_selected_language);
    $html_add_element = build_html_wise_menubar_add_element_for_simple($int_selected_language);

    $html .= $html_erasers;
    $html .= $html_links;
    $html .= $html_tools;
    $html .= $html_add_element;

    return $html;
}

function build_html_wise_right_vertical_toolbar($arr_visible_right_buttons, $int_selected_language){

    global
        $path_images_verticalToolbarContainer,
        $str_wiseRightVerticalToolbarButton_id_whiteboard,
        $str_wiseRightVerticalToolbarButton_id_wiseSetup,
        $str_wiseRightVerticalToolbarButton_id_grammarExplanation,
        $str_wiseRightVerticalToolbarButton_id_memoPad,
        $str_wiseRightVerticalToolbarButton_id_chart,
        $str_wiseRightVerticalToolbarButton_id_quiz,
        $str_wiseRightVerticalToolbarButton_id_map,
        $str_wiseRightVerticalToolbarButton_id_imageViewer,
        $str_wiseRightVerticalToolbarButton_id_lessonContents,
        $str_wiseRightVerticalToolbarButton_id_grammarInsights,
        $str_wiseRightVerticalToolbarButton_id_sharedContentsUi;

    $html_buttons = '';

    $url_images_verticalToolbarContainer = get_home_url(
        get_main_site_id(),
        trailingslashit(ltrim($path_images_verticalToolbarContainer, '/'))
    );

    $src_whiteboard = jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarOpenWiseButton.png');

    $html_current_img = '<img src="'.$src_whiteboard.'" alt="whiteboard" title="whiteboard">';

    if (!empty($arr_visible_right_buttons[$str_wiseRightVerticalToolbarButton_id_whiteboard])) {
        $html_buttons .= '
        <button
            id="'.$str_wiseRightVerticalToolbarButton_id_whiteboard.'"
            class="wiseVerticalToolbarButton wiseRightVerticalToolbarButton wiseScanTarget wiseScanActor"
            data-panel-id="wisePanelWhiteboard"
            data-open-position-select="1"
        >
            <img src="'.$src_whiteboard.'" alt="whiteboard" title="whiteboard">
        </button>';
    }

    if (!empty($arr_visible_right_buttons[$str_wiseRightVerticalToolbarButton_id_wiseSetup])) {
        $src = jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarFunctionsButton.png');

        $html_buttons .= '
        <button
            id="'.$str_wiseRightVerticalToolbarButton_id_wiseSetup.'"
            class="wiseVerticalToolbarButton wiseRightVerticalToolbarButton wiseScanTarget wiseScanActor"
            data-panel-id="wisePanelWiseSetup"
            data-open-position-select="1"
        >
            <img src="'.$src.'" alt="setup" title="setup">
        </button>';
    }

    if (!empty($arr_visible_right_buttons[$str_wiseRightVerticalToolbarButton_id_memoPad])) {
        $src = jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarMemoPadButton.png');

        $html_buttons .= '
        <button
            id="'.$str_wiseRightVerticalToolbarButton_id_memoPad.'"
            class="wiseVerticalToolbarButton wiseRightVerticalToolbarButton wiseScanTarget wiseScanActor"
            data-panel-id="wisePanelMemoPad"
            data-open-position-select="1"
        >
            <img src="'.$src.'" alt="memoPad" title="memoPad">
        </button>';
    }

    if (!empty($arr_visible_right_buttons[$str_wiseRightVerticalToolbarButton_id_chart])) {
        $src = jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarChartButton.png');

        $html_buttons .= '
        <button
            id="'.$str_wiseRightVerticalToolbarButton_id_chart.'"
            class="wiseVerticalToolbarButton wiseRightVerticalToolbarButton wiseScanTarget wiseScanActor"
            data-panel-id="wisePanelChart"
            data-open-position-select="1"
        >
            <img src="'.$src.'" alt="chart" title="chart">
        </button>';
    }

    if (!empty($arr_visible_right_buttons[$str_wiseRightVerticalToolbarButton_id_quiz])) {
        $src = jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarQuizButton.png');

        $html_buttons .= '
        <button
            id="'.$str_wiseRightVerticalToolbarButton_id_quiz.'"
            class="wiseVerticalToolbarButton wiseRightVerticalToolbarButton wiseScanTarget wiseScanActor"
            data-panel-id="wisePanelQuiz"
            data-open-position-select="1"
        >
            <img src="'.$src.'" alt="quiz" title="quiz">
        </button>';
    }

    if (!empty($arr_visible_right_buttons[$str_wiseRightVerticalToolbarButton_id_map])) {
        $src = jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarMapButton.png');

        $html_buttons .= '
        <button
            id="'.$str_wiseRightVerticalToolbarButton_id_map.'"
            class="wiseVerticalToolbarButton wiseRightVerticalToolbarButton wiseScanTarget wiseScanActor"
            data-panel-id="wisePanelMap"
            data-open-position-select="1"
        >
            <img src="'.$src.'" alt="map" title="map">
        </button>';
    }

    if (!empty($arr_visible_right_buttons[$str_wiseRightVerticalToolbarButton_id_imageViewer])) {
        $src = jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarImageViewerButton.png');

        $html_buttons .= '
        <button
            id="'.$str_wiseRightVerticalToolbarButton_id_imageViewer.'"
            class="wiseVerticalToolbarButton wiseRightVerticalToolbarButton wiseScanTarget wiseScanActor"
            data-panel-id="wisePanelImageViewer"
            data-open-position-select="1"
        >
            <img src="'.$src.'" alt="imageViewer" title="imageViewer">
        </button>';
    }

    if (!empty($arr_visible_right_buttons[$str_wiseRightVerticalToolbarButton_id_lessonContents])) {
        $src = jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarLessonContentsButton.png');

        $html_buttons .= '
        <button
            id="'.$str_wiseRightVerticalToolbarButton_id_lessonContents.'"
            class="wiseVerticalToolbarButton wiseRightVerticalToolbarButton wiseScanTarget wiseScanActor"
            data-panel-id="wisePanelLessonContents"
            data-open-position-select="1"
        >
            <img src="'.$src.'" alt="lessonContents" title="lessonContents">
        </button>';
    }

    if (!empty($arr_visible_right_buttons[$str_wiseRightVerticalToolbarButton_id_grammarExplanation])) {
        $src = jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarGrammarExplanationButton.png');

        $html_buttons .= '
        <button
            id="'.$str_wiseRightVerticalToolbarButton_id_grammarExplanation.'"
            class="wiseVerticalToolbarButton wiseRightVerticalToolbarButton wiseScanTarget wiseScanActor"
            data-panel-id="wisePanelGrammarExplanation"
            data-open-position-select="1"
        >
            <img src="'.$src.'" alt="grammarExplanation" title="grammarExplanation">
        </button>';
    }

    if (!empty($arr_visible_right_buttons[$str_wiseRightVerticalToolbarButton_id_grammarInsights])) {
        $src = jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarGrammarInsightsButton.png');

        $html_buttons .= '
        <button
            id="'.$str_wiseRightVerticalToolbarButton_id_grammarInsights.'"
            class="wiseVerticalToolbarButton wiseRightVerticalToolbarButton wiseScanTarget wiseScanActor"
            data-panel-id="wisePanelGrammarInsights"
            data-open-position-select="1"
        >
            <img src="'.$src.'" alt="grammarInsights" title="grammarInsights">
        </button>';
    }

    if (!empty($arr_visible_right_buttons[$str_wiseRightVerticalToolbarButton_id_sharedContentsUi])) {
        $src = jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarSearchGrammarButton.png');

        $html_buttons .= '
        <button
            id="'.$str_wiseRightVerticalToolbarButton_id_sharedContentsUi.'"
            class="wiseVerticalToolbarButton wiseRightVerticalToolbarButton wiseScanTarget wiseScanActor"
        >
            <img src="'.$src.'" alt="sharedContentsUi" title="sharedContentsUi">
        </button>';
    }

    $html_current_button = '
    <button id="wiseRightVerticalToolbarCurrentButton" class="wiseVerticalToolbarButton wiseToolbarMainButton wiseToolbarCurrentButton wiseScanTarget" type="button">
        '.$html_current_img.'
    </button>';

    return '
    <div id="wiseRightVerticalToolbarContainer" class="wiseExpandableToolbar is-collapsed">
        <div class="wiseToolbarButtonsArea">
            '.$html_buttons.'
        </div>
        <div class="wiseToolbarCurrentButtonArea">
            '.$html_current_button.'
        </div>
    </div>';
}

/******************************************************
 *  MENU BAR
 *  
 ******************************************************/




function build_html_wise_menubar_add_element($int_selected_language){

	global
		$str_wiseMenuBarAddElementTextBox,
		$str_wiseMenuBarAddElementStickyNote,
		$str_wiseMenuBarAddElementWord;

	$str_menubar_creator = '';

	$str_menubar_creator =
	'<div id="wiseMenuBarAddElement" class="wiseMenuBar">
		<div id="wiseMenuBarAddElementTextBox" class="wiseMenuBarButton">'.$str_wiseMenuBarAddElementTextBox[$int_selected_language].'</div>
		<div id="wiseMenuBarAddElementStickyNote" class="wiseMenuBarButton">'.$str_wiseMenuBarAddElementStickyNote[$int_selected_language].'</div>
		<div id="wiseMenuBarAddElementWord" class="wiseMenuBarButton">'.$str_wiseMenuBarAddElementWord[$int_selected_language].'</div>
	</div>';

	return $str_menubar_creator;
}

function build_html_wise_menubar_add_element_for_simple($int_selected_language){

	global
		$str_wiseMenuBarAddElementTextBox,
		$str_wiseMenuBarAddElementWord;

	$str_menubar_creator = '';

	$str_menubar_creator =
	'<div id="wiseMenuBarAddElement" class="wiseMenuBar">
		<div id="wiseMenuBarAddElementTextBox" class="wiseMenuBarButton">'.$str_wiseMenuBarAddElementTextBox[$int_selected_language].'</div>
		<div id="wiseMenuBarAddElementWord" class="wiseMenuBarButton">'.$str_wiseMenuBarAddElementWord[$int_selected_language].'</div>
	</div>';
	return $str_menubar_creator;
}

function build_html_wise_menubar_erasers($int_selected_language){

	$str_menubar_erasers = '';

	$str_menubar_erasers =
	'<div id="wiseMenuBarErasers" class="wiseMenuBar">
		<div id="wiseMenuBarErasersHandWritingReset" class="wiseMenuBarButton">手書き線をクリア</div>
		<div id="wiseMenuBarErasersElementsEraser" class="wiseMenuBarButton wiseMenuBarEraserToggle">コンテナを消す</div>
	</div>';

	return $str_menubar_erasers;
}

function build_html_wise_menubar_links($int_selected_language){

	$str_menubar_links = '';

	$str_menubar_links =
	'<div id="wiseMenuBarLinks" class="wiseMenuBar">
		<div id="wiseMenuBarLinksToSentence" class="wiseMenuBarButton">優先リンク</div>
	</div>';

	return $str_menubar_links;
}

function build_html_wise_menubar_tools($doRegister, $int_selected_language){

	global
		$str_wiseMenuBarToolsCreateNew;

	$str_menubar_tools = '';

	if($doRegister){
		//マジックナンバー 
		$str_menubar_tools =
		'<div id="wiseMenuBarTools" class="wiseMenuBar">
			<div id="wiseMenuBarToolsRegisterSentence" class="wiseMenuBarButton">登録する</div>
			<div id="wiseMenuBarToolsCallAlreadyRegistered" class="wiseMenuBarButton">登録済み</div>
			<div id="wiseMenuBarToolsCreateNew" class="wiseMenuBarButton">'.$str_wiseMenuBarToolsCreateNew[$int_selected_language].'</div>
			<div id="wiseMenuBarToolsHistorysList" class="wiseMenuBarButton">履歴</div>
			<div id="wiseMenuBarToolsSettingsMenu" class="wiseMenuBarButton">設定</div>
		</div>';
	}
	else{
		$str_menubar_tools =
		'<div id="wiseMenuBarTools" class="wiseMenuBar">
			<div id="wiseMenuBarToolsHistorysList" class="wiseMenuBarButton">履歴</div>
			<div id="wiseMenuBarToolsSettingsMenu" class="wiseMenuBarButton">設定</div>
		</div>';
	}
	return $str_menubar_tools;
}

function build_html_wise_menubar_tools_for_editing($int_selected_language){

	$str_menubar_tools_for_editing = '';

	//マジックナンバー 
	$str_menubar_tools_for_editing =
	'<div id="wiseMenuBarTools" class="wiseMenuBar">
		<div id="wiseMenuBarToolsStartEditing" class="wiseMenuBarButton">編集開始</div>
		<div id="wiseMenuBarToolsReRegisterSentence" class="wiseMenuBarButton hidden">再登録する</div>
		<div id="wiseMenuBarToolsHistorysList" class="wiseMenuBarButton">履歴</div>
		<div id="wiseMenuBarToolsSettingsMenu" class="wiseMenuBarButton">設定</div>
	</div>';

	return $str_menubar_tools_for_editing;
}

function build_html_wise_menubar_colors($int_selected_language){

	$str_menubar_colors = '';

	$str_menubar_colors =
	'<div id="wiseMenuBarDrawingColors" class="wiseMenuBar">
		<div id="wiseMenuBarDrawingColorsBlack" class="wiseMenuBarButtonSelectColor SelectColorBlack" data-drawing-color="black"></div>
		<div id="wiseMenuBarDrawingColorsRed" class="wiseMenuBarButtonSelectColor SelectColorRed" data-drawing-color="red"></div>
		<div id="wiseMenuBarDrawingColorsBlue" class="wiseMenuBarButtonSelectColor SelectColorBlue" data-drawing-color="blue"></div>
		<div id="wiseMenuBarDrawingColorsOrange" class="wiseMenuBarButtonSelectColor SelectColorOrange" data-drawing-color="orange"></div>
		<div id="wiseMenuBarDrawingColorsGreen" class="wiseMenuBarButtonSelectColor SelectColorGreen" data-drawing-color="green"></div>
	</div>';

	return $str_menubar_colors;
}


/******************************************************
 *  MENU
 *  
 ******************************************************/

function build_html_wise_whiteboard_ui_registered_items($int_selected_language)
{
    global $str_mark_cross;

    $html_zoom_in_icon  = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    $buttons_left = [
        '<button id="wisePanelWhiteboardUiRegisteredItemsCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [
        '<button class="wiseUiFontSizeIncreaseButton wiseUiFontSizeButton">' . $html_zoom_in_icon . '</button>',
        '<button class="wiseUiFontSizeDecreaseButton wiseUiFontSizeButton">' . $html_zoom_out_icon . '</button>',
    ];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        '登録済み一覧',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelWhiteboardUiRegisteredItemsHeader'
    );

    $html_toolbar = '';

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '
        <ul id="wisePanelWhiteboardUiRegisteredItemsList" class="wisePanelUiList wiseUiFontSizeTarget">
        </ul>
        ',
        'wisePanelWhiteboardUiRegisteredItemsMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = '';

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelWhiteboardUiRegisteredItemsMainContentContainer'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelWhiteboardUiRegisteredItems',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'float',
        true,
        'wisePanelUi wiseHitItem wisePanelWhiteboardUi wisePanelWhiteboardUiRight'
    );
}

function build_html_wise_whiteboard_ui_history($int_selected_language)
{
    global $str_mark_cross;

    $html_zoom_in_icon  = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    $buttons_left = [
        '<button id="wisePanelWhiteboardUiHistoryCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [
        '<button class="wiseUiFontSizeIncreaseButton wiseUiFontSizeButton">' . $html_zoom_in_icon . '</button>',
        '<button class="wiseUiFontSizeDecreaseButton wiseUiFontSizeButton">' . $html_zoom_out_icon . '</button>',
    ];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        '履歴一覧',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelWhiteboardUiHistoryHeader'
    );

    $html_toolbar = '';

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '
        <ul id="wisePanelWhiteboardUiHistoryList" class="wisePanelUiList wiseUiFontSizeTarget">
            <li id="wisePanelWhiteboardUiHistoryLiCallCreateWordHistory" class="wiseUiFontSizeTarget">単語履歴</li>
            <li id="wisePanelWhiteboardUiHistoryLiCallActionHistory" class="wiseUiFontSizeTarget">動作履歴</li>
        </ul>
        ',
        'wisePanelWhiteboardUiHistoryMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = '';

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelWhiteboardUiHistoryMainContentContainer'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelWhiteboardUiHistory',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'float',
        true,
        'wisePanelUi wiseHitItem wisePanelWhiteboardUi wisePanelWhiteboardUiRight'
    );
}

function build_html_wise_whiteboard_ui_created_word_history($int_selected_language)
{
    global $str_mark_cross;

    $html_zoom_in_icon  = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    $buttons_left = [
        '<button id="wisePanelWhiteboardUiCreatedWordHistoryCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [
        '<button class="wiseUiFontSizeIncreaseButton wiseUiFontSizeButton">' . $html_zoom_in_icon . '</button>',
        '<button class="wiseUiFontSizeDecreaseButton wiseUiFontSizeButton">' . $html_zoom_out_icon . '</button>',
    ];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        '単語履歴',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelWhiteboardUiCreatedWordHistoryHeader'
    );

    $html_toolbar = '';

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '<ul id="wisePanelWhiteboardUiCreatedWordHistoryList" class="wisePanelUiList wiseUiFontSizeTarget"></ul>',
        'wisePanelWhiteboardUiCreatedWordHistoryMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = '';

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelWhiteboardUiCreatedWordHistoryMainContentContainer'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelWhiteboardUiCreatedWordHistory',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'float',
        true,
        'wisePanelUi wiseHitItem wisePanelWhiteboardUi wisePanelWhiteboardUiRight'
    );
}

function build_html_wise_whiteboard_ui_chart_history($int_selected_language)
{
    global $str_mark_cross;

    $html_zoom_in_icon  = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    $buttons_left = [
        '<button id="wisePanelWhiteboardUiChartHistoryCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [
        '<button class="wiseUiFontSizeIncreaseButton wiseUiFontSizeButton">' . $html_zoom_in_icon . '</button>',
        '<button class="wiseUiFontSizeDecreaseButton wiseUiFontSizeButton">' . $html_zoom_out_icon . '</button>',
    ];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        '表履歴',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelWhiteboardUiChartHistoryHeader'
    );

    $html_toolbar = '';

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '<ul id="wisePanelWhiteboardUiChartHistoryList" class="wisePanelUiList wiseUiFontSizeTarget"></ul>',
        'wisePanelWhiteboardUiChartHistoryMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = '';

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelWhiteboardUiChartHistoryMainContentContainer'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelWhiteboardUiChartHistory',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'float',
        true,
        'wisePanelUi wiseHitItem wisePanelWhiteboardUi wisePanelWhiteboardUiRight'
    );
}

function build_html_wise_whiteboard_ui_action_history($int_selected_language)
{
    global $str_mark_cross;

    $html_zoom_in_icon  = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    $buttons_left = [
        '<button id="wisePanelWhiteboardUiActionHistoryCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [
        '<button class="wiseUiFontSizeIncreaseButton wiseUiFontSizeButton">' . $html_zoom_in_icon . '</button>',
        '<button class="wiseUiFontSizeDecreaseButton wiseUiFontSizeButton">' . $html_zoom_out_icon . '</button>',
    ];
	
	$header_content = '';

    $html_header = build_html_wise_panel_header(
        '動作履歴',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelWhiteboardUiActionHistoryHeader'
    );

    $html_toolbar = '';

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '<ul id="wisePanelWhiteboardUiActionHistoryList" class="wisePanelUiList wiseUiFontSizeTarget"></ul>',
        'wisePanelWhiteboardUiActionHistoryMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = '';

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelWhiteboardUiActionHistoryMainContentContainer'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelWhiteboardUiActionHistory',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'float',
        true,
        'wisePanelUi wiseHitItem wisePanelWhiteboardUi wisePanelWhiteboardUiRight'
    );
}

function build_html_wise_whiteboard_ui_create_new_word($int_selected_language)
{
    global
        $t_masta_japanese_classification,
        $t_masta_japanese_sub_classification,
        $t_masta_japanese_root,
        $arr_columns_masta_japanese_root,
        $arr_columns_masta_japanese_sub_classification,
        $str_mark_cross;

    $str_add_divstbox = '';

    $arr_strSQL_select = [
        [$t_masta_japanese_sub_classification, 'id'],
        [$t_masta_japanese_sub_classification, 'classification_id'],
        [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language] . ' as ' . $arr_columns_masta_japanese_sub_classification[$int_selected_language]]
    ];

    $strSQL_from = " FROM
                    (
                        $t_masta_japanese_classification
                        INNER JOIN $t_masta_japanese_sub_classification
                        ON
                        $t_masta_japanese_classification.id = $t_masta_japanese_sub_classification.classification_id
                    )
                    INNER JOIN $t_masta_japanese_root
                    ON
                    $t_masta_japanese_sub_classification.masta_japanese_root_id = $t_masta_japanese_root.id
                    ";

    $arr_strSQL_where = [];

    $arr_strSQL_order = [
        [$t_masta_japanese_classification, 'sort', 'ASC'],
        [$t_masta_japanese_sub_classification, 'sort', 'ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_sub_classification) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    foreach ($arr_masta_japanese_sub_classification as $loop_masta_japanese_sub_classification) {
        $t_masta_japanese_sub_classification_id = escape_html_with_nl2br($loop_masta_japanese_sub_classification['id']);
        $t_masta_japanese_sub_classification_sub_classification = escape_html_with_nl2br(
            $loop_masta_japanese_sub_classification[$arr_columns_masta_japanese_sub_classification[$int_selected_language]]
        );

        $str_add_divstbox .= '<option value="' . $t_masta_japanese_sub_classification_id . '">' . $t_masta_japanese_sub_classification_sub_classification . '</option>';
    }

    $buttons_left = [
        '<button id="wisePanelWhiteboardUiCreateNewWordCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [];

	$header_content = '';

    $html_header = build_html_wise_panel_header(
        '新規単語作成',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelWhiteboardUiCreateNewWordHeader'
    );

    $html_toolbar = '';

    $html_main_content_area = build_html_wise_panel_main_content_area(
        '
        <form id="wisePanelWhiteboardUiCreateNewWordJapaneseForm">
            <input type="text" id="wisePanelWhiteboardUiCreateNewWordJapaneseInput" class="wisePanelWhiteboardUiTextInputArea" placeholder="単語" required>
        </form>
        <form id="wisePanelWhiteboardUiCreateNewWordKanaForm">
            <input type="text" id="wisePanelWhiteboardUiCreateNewWordKanaInput" class="wisePanelWhiteboardUiTextInputArea" placeholder="かな (ひらがなのみ)">
        </form>
        <select id="wisePanelWhiteboardUiCreateNewWordSubClassificationSelect" name="wisePanelWhiteboardUiCreateNewWordSubClassificationSelect">
            ' . $str_add_divstbox . '
        </select>
        <button id="wisePanelWhiteboardUiCreateNewWordConfirmButton">作成</button>
        <div id="wisePanelWhiteboardUiCreateNewWordResult">
            <ul id="wisePanelWhiteboardUiCreateNewWordResultList">
            </ul>
        </div>
        ',
        'wisePanelWhiteboardUiCreateNewWordMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    $html_loading = '';

    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelWhiteboardUiCreateNewWordMainContentContainer'
    );

    return build_html_wise_panel_ui_item(
        'wisePanelWhiteboardUiCreateNewWord',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'float',
        true,
        'wisePanelUi wiseHitItem wisePanelWhiteboardUi wisePanelWhiteboardUiLeft'
    );
}


/******************************************************
 *  SELECT UI
 *  
 ******************************************************/
function build_html_wise_panel_position_select_ui($int_selected_language){

    $arr_str_wise_panel_position_select_title = [
        '表示位置を選択',
        '選擇顯示位置',
        'Select Display Position'
    ];

    $arr_str_wise_panel_position_select_full = [
        '全画面表示',
        '全畫面顯示',
        'Full Screen'
    ];

    $arr_str_wise_panel_position_select_top = [
        '画面上に表示',
        '顯示在上方',
        'Top'
    ];

    $arr_str_wise_panel_position_select_bottom = [
        '画面下に表示',
        '顯示在下方',
        'Bottom'
    ];

    $arr_str_wise_panel_position_select_left = [
        '画面左に表示',
        '顯示在左側',
        'Left'
    ];

    $arr_str_wise_panel_position_select_right = [
        '画面右に表示',
        '顯示在右側',
        'Right'
    ];

    $arr_str_wise_panel_position_select_cancel = [
        'キャンセル',
        '取消',
        'Cancel'
    ];

    $title = $arr_str_wise_panel_position_select_title[$int_selected_language] ?? $arr_str_wise_panel_position_select_title[0];
    $label_full = $arr_str_wise_panel_position_select_full[$int_selected_language] ?? $arr_str_wise_panel_position_select_full[0];
    $label_top = $arr_str_wise_panel_position_select_top[$int_selected_language] ?? $arr_str_wise_panel_position_select_top[0];
    $label_bottom = $arr_str_wise_panel_position_select_bottom[$int_selected_language] ?? $arr_str_wise_panel_position_select_bottom[0];
    $label_left = $arr_str_wise_panel_position_select_left[$int_selected_language] ?? $arr_str_wise_panel_position_select_left[0];
    $label_right = $arr_str_wise_panel_position_select_right[$int_selected_language] ?? $arr_str_wise_panel_position_select_right[0];
    $label_cancel = $arr_str_wise_panel_position_select_cancel[$int_selected_language] ?? $arr_str_wise_panel_position_select_cancel[0];

    return '
	<div
		id="wisePanelPositionSelectOverlay"
		class="wisePanelPositionSelectOverlay hidden"
		data-target-panel-id=""
	>
		<div id="wisePanelPositionSelectDialog" class="wisePanelPositionSelectDialog">
			<div id="wisePanelPositionSelectTitle" class="wisePanelPositionSelectTitle">'.$title.'</div>

			<div id="wisePanelPositionSelectPad" class="wisePanelPositionSelectPad">

				<button
					type="button"
					class="wisePanelPositionSelectOption wisePanelPositionSelectOptionTop"
					data-position="top"
				>'.$label_top.'</button>

				<div class="wisePanelPositionSelectMiddleRow">
					<button
						type="button"
						class="wisePanelPositionSelectOption wisePanelPositionSelectOptionLeft"
						data-position="left"
					>'.$label_left.'</button>

					<button
						type="button"
						class="wisePanelPositionSelectOption wisePanelPositionSelectOptionFull"
						data-position="full"
					>'.$label_full.'</button>

					<button
						type="button"
						class="wisePanelPositionSelectOption wisePanelPositionSelectOptionRight"
						data-position="right"
					>'.$label_right.'</button>
				</div>

				<button
					type="button"
					class="wisePanelPositionSelectOption wisePanelPositionSelectOptionBottom"
					data-position="bottom"
				>'.$label_bottom.'</button>
			</div>

			<div id="wisePanelPositionSelectActions" class="wisePanelPositionSelectActions">
				<button
					type="button"
					id="wisePanelPositionSelectCancelButton"
					class="wisePanelPositionSelectActionButton"
				>'.$label_cancel.'</button>
			</div>
		</div>
	</div>';
}




function build_html_wise_banner_advertisement($doDisplay, $int_selected_language){

	global
    	$url_banner_advertisement;

	$str_banner_advertisement = '';
	$str_banner_advertisement_a = '';

	if($doDisplay){

		// デバッグ　変更
		$str_banner_advertisement_src_url = $url_banner_advertisement.'画像728_90.jpg';
		$str_banner_advertisement_img = '<img id="wiseBannerAdvertisementContainerImage" src="'.jws_add_file_version($str_banner_advertisement_src_url).'" alt="広告" title="広告">';
		$str_banner_advertisement_a = '<a href="https://www.facebook.com/profile.php?id=100093001277657" target="_blank" rel="noopener">'.$str_banner_advertisement_img.'</a>';
		// デバッグ　変更
	}

	$str_banner_advertisement = '<div id="wiseBannerAdvertisementContainer" class="wiseBannerAdvertisementContainer">'.$str_banner_advertisement_a.'</div>';

	return $str_banner_advertisement;
}
