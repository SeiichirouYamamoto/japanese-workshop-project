<?php

/******************************************************
 *  BODY
 *
 ******************************************************/
function build_html_wise_grammar_explanation_body($isWise, $int_selected_language)
{

    $html_zoom_in_icon  = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    // ===== buttons =====
    $buttons_left = [
		build_html_wise_panel_close_button('wisePanelGrammarExplanationViewCloseButton'),
		build_html_wise_panel_expand_button('wisePanelGrammarExplanationViewExpandButton'),
		build_html_wise_panel_split_button('wisePanelGrammarExplanationViewSplitButton'),
		build_html_wise_panel_nav_button('wisePanelGrammarExplanationViewBackButton', 'wisePanelViewNavigationButton', '←'),
		build_html_wise_panel_nav_button('wisePanelGrammarExplanationViewForwardButton', 'wisePanelViewNavigationButton', '→'),
    ];

    $buttons_right = [];

    if ($isWise) {
        $buttons_right[] = '<button id="reloadGrammarExplanation">Reload</button>';
        $buttons_right[] = '<button id="showGrammarExplanationHistory">History</button>';
        $buttons_right[] = '<button id="generateExampleChart">Chart</button>';
        $buttons_right[] = '<button id="linkToGrammarView">Page</button>';
    }

    $buttons_right[] = '<button class="grammarViewZoomIn grammarViewZoomButton">' . $html_zoom_in_icon . '</button>';
    $buttons_right[] = '<button class="grammarViewZoomOut grammarViewZoomButton">' . $html_zoom_out_icon . '</button>';

	$header_content = '';

    // ===== header =====
    $html_header = build_html_wise_panel_header(
        '文法説明',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelGrammarExplanationViewHeader'
    );

    // ===== main content area =====
    $html_main_content_area = build_html_wise_panel_main_content_area(
        '
        <div id="wisePanelGrammarExplanationViewMainContentAreaDefault">
            <button id="wisePanelGrammarExplanationViewReloadButton">Reload</button>
        </div>
        ',
        'wisePanelGrammarExplanationViewMainContentArea'
    );

    // ===== loading =====
    $html_loading = build_html_loading_spinner('wisePanelGrammarExplanationViewLoading');

    // ===== container =====
    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelGrammarExplanationViewMainContentContainer'
    );

    // ===== handle =====
    $html_handle = build_html_wise_panel_view_handle(
        '',
        'wisePanelGrammarExplanationViewHandle',
    );

    // ===== toolbar（今回は未使用） =====
    $html_toolbar = '';

    // ===== view =====
    return build_html_wise_panel_view(
        'wisePanelGrammarExplanationView',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        $html_handle
    );
}


/******************************************************
 *  UI
 *
 ******************************************************/
function build_html_wise_grammar_explanation_ui($int_selected_language)
{
    global $str_mark_cross;

    $html_zoom_in_icon  = build_html_magnifier_icon('plus');
    $html_zoom_out_icon = build_html_magnifier_icon('minus');

    // ===== buttons =====
    $buttons_left = [
        '<button id="wisePanelGrammarExplanationUiHistoryCloseButton" class="wisePanelUiCloseButton">' . $str_mark_cross . '</button>',
    ];

    $buttons_right = [
        '<button class="wiseUiFontSizeIncreaseButton wiseUiFontSizeButton">' . $html_zoom_in_icon . '</button>',
        '<button class="wiseUiFontSizeDecreaseButton wiseUiFontSizeButton">' . $html_zoom_out_icon . '</button>',
    ];

	$header_content = '';

    // ===== header =====
    $html_header = build_html_wise_panel_header(
        '検索オプション',
        $buttons_left,
        $buttons_right,
        $header_content,
        'wisePanelGrammarExplanationUiHistoryHeader'
    );

    // ===== toolbar =====
    $html_toolbar = '';

    // ===== main content area =====
    $html_main_content_area = build_html_wise_panel_main_content_area(
        '
        <ul id="wisePanelGrammarExplanationUiHistoryList" class="wisePanelUiList">
        </ul>
        ',
        'wisePanelGrammarExplanationUiHistoryMainContentArea',
		'wisePanelMainContentArea wiseScrollableArea'
    );

    // ===== loading =====
    $html_loading = build_html_loading_spinner('wisePanelGrammarExplanationUiHistoryLoading');

    // ===== container =====
    $html_main_content_container = build_html_wise_panel_main_content_container(
        $html_main_content_area,
        $html_loading,
        'wisePanelGrammarExplanationUiHistoryMainContentContainer'
    );

    // ===== ui =====
    return build_html_wise_panel_ui_item(
        'wisePanelGrammarExplanationUiHistory',
        $html_header,
        $html_toolbar,
        $html_main_content_container,
        '',
        'side'
    );
}