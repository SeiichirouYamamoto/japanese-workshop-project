<?php

/**
 * Wise Overlay中身
 */
function build_html_wise_main_panel_overlay_contents($int_selected_language){

    return [
        build_html_global_canvas(),
        build_html_panel_overlay_shared_contents_ui_search($int_selected_language),
        build_html_panel_overlay_shared_contents_ui_category($int_selected_language),
        build_html_panel_overlay_shared_contents_ui_bookmark($int_selected_language),
        build_html_panel_overlay_shared_contents_ui_history($int_selected_language),
        build_html_panel_overlay_shared_contents_ui_selected_contents($int_selected_language),
    ];
}


/**
 * Overlay bundle
 */
function build_html_wise_panel_overlay_bundle($contents = '', $bundle_id = 'wisePanelOverlayBundle'){

    return '
    <div id="'.$bundle_id.'" class="wisePanelOverlayBundle">'.
        build_html_wise_join_contents($contents).
    '</div>';
}






function build_html_panel_overlay_shared_contents_ui_search($int_selected_language){

	$str_html = '';
	
	$str_html =
	'<div id="panelOverlaySharedContentsUi" class="panelOverlaySharedContentsUi wiseHitItem leftPositionModal panelOverlaySharedContentsUiTopLevel">
		<div class="panelOverlaySharedContentsUiTitleContainer">
			<div class="panelOverlaySharedContentsUiTitle">検索</div>
		</div>
		<div id="panelOverlaySharedContentsUiSearchArea" class="panelOverlaySharedContentsUiSearchArea">
			<form id="panelOverlaySharedContentsUiSearchForm" class="panelOverlaySharedContentsUiSearchForm">
				<input type="text" id="panelOverlaySharedContentsUiSearchInput" class="panelOverlaySharedContentsUiSearchInput panelOverlaySharedContentsUiTextInputArea" placeholder="検索ワードを入力">
			</form>
			<button id="panelOverlaySharedContentsUiSearchButton" class="panelOverlaySharedContentsUiButtons">検索</button>
		</div>' .
		build_html_loading_spinner('panelOverlaySharedContentsUiLoadingAddContents') .
		'<div class="panelOverlaySharedContentsUiContents leftPositionModalContents">
			<ul id="panelOverlaySharedContentsUiUl" class="panelOverlaySharedContentsUiSelectableList">
			</ul>
		</div>
	</div>';

	return $str_html;
}


function build_html_panel_overlay_shared_contents_ui_category($int_selected_language){
	
	global
		$arr_columns_masta_japanese_sub_category,
		$str_option_value_default,
		$str_option_value_select_all;

	$str_html = '';

	$arr_masta_japanese_sub_category = fetch_arr_masta_japanese_sub_categories_for_grammar($int_selected_language);

	$html_dropdown_menu_options = '';
	foreach($arr_masta_japanese_sub_category as $loop_masta_japanese_sub_category){
		$int_option_value = escape_html_with_nl2br($loop_masta_japanese_sub_category['id']);
		$str_option_text_content = escape_html_with_nl2br($loop_masta_japanese_sub_category[$arr_columns_masta_japanese_sub_category[$int_selected_language]]);
		$html_dropdown_menu_options =
		$html_dropdown_menu_options.'<option value="'.$int_option_value.'">'.$str_option_text_content.'</option>';
	}

	$html_dropdown_menu_options = '<option value="'.SELECT_ALL.'">'.$str_option_value_default.'</option>'.$html_dropdown_menu_options;
	$html_dropdown_menu = '
	<div id="panelOverlaySharedContentsUiFromCategoryDropDownMenuArea">
		<select id="panelOverlaySharedContentsUiFromCategorySelectId" name="panelOverlaySharedContentsUiFromCategorySelectId">'.
			$html_dropdown_menu_options.
		'</select>
		<button id="panelOverlaySharedContentsUiFromCategorySelectAllButton">'.$str_option_value_select_all.'</button>
	</div>';
		
	$str_html =
	'<div id="panelOverlaySharedContentsUiFromCategory" class="panelOverlaySharedContentsUi wiseHitItem leftPositionModal panelOverlaySharedContentsUiTopLevel">
		<div class="panelOverlaySharedContentsUiTitleContainer">
			<div class="panelOverlaySharedContentsUiTitle">Category</div>
		</div>'.
		$html_dropdown_menu.
		build_html_loading_spinner('panelOverlaySharedContentsUiLoadingAddContentsFromCategory') .
		'<div class="panelOverlaySharedContentsUiContents leftPositionModalContents">
			<ul id="panelOverlaySharedContentsUiFromCategoryUl" class="panelOverlaySharedContentsUiSelectableList">
			</ul>
		</div>
	</div>';

	return $str_html;
}


function build_html_panel_overlay_shared_contents_ui_bookmark($int_selected_language){

    global
        $arr_bookmark_filter,
        $str_option_value_default,
        $str_option_value_select_all;

    $str_html = '';

    // -----------------------------
    // Bookmark status dropdown
    // -----------------------------
    $html_dropdown_menu_options = '';

    foreach ($arr_bookmark_filter as $int_status => $arr_status_info) {

        // 言語別タイトル
        $str_option_text_content = escape_html_with_nl2br(
            $arr_status_info['title'][$int_selected_language]
        );

        $int_option_value = escape_html_with_nl2br($int_status);

        $html_dropdown_menu_options .=
            '<option value="'.$int_option_value.'">'.$str_option_text_content.'</option>';
    }

    // SELECT_ALL を先頭に追加
    $html_dropdown_menu_options =
        '<option value="'.SELECT_ALL.'">'.$str_option_value_default.'</option>'
        .$html_dropdown_menu_options;

    $html_dropdown_menu = '
    <div id="panelOverlaySharedContentsUiFromBookmarkDropDownMenuArea">
        <select id="panelOverlaySharedContentsUiFromBookmarkSelectId"
                name="panelOverlaySharedContentsUiFromBookmarkSelectId">'.
            $html_dropdown_menu_options.
        '</select>
        <button id="panelOverlaySharedContentsUiFromBookmarkSelectAllButton">'.
            $str_option_value_select_all.
        '</button>
    </div>';

    // -----------------------------
    // Modal HTML
    // -----------------------------
    $str_html =
    '<div id="panelOverlaySharedContentsUiFromBookmark"
          class="panelOverlaySharedContentsUi wiseHitItem leftPositionModal panelOverlaySharedContentsUiTopLevel">

        <div class="panelOverlaySharedContentsUiTitleContainer">
            <div class="panelOverlaySharedContentsUiTitle">Bookmark</div>
        </div>'.

        $html_dropdown_menu.
		build_html_loading_spinner('panelOverlaySharedContentsUiLoadingAddContentsFromBookmark') .
		'<div class="panelOverlaySharedContentsUiContents leftPositionModalContents">
            <ul id="panelOverlaySharedContentsUiFromBookmarkUl"
                class="panelOverlaySharedContentsUiSelectableList">
            </ul>
        </div>
    </div>';

    return $str_html;
}


function build_html_panel_overlay_shared_contents_ui_history($int_selected_language){
	
	global
		$str_option_value_select_all;

	$str_html = '';
		
	$str_html =
	'<div id="panelOverlaySharedContentsUiFromHistory" class="panelOverlaySharedContentsUi wiseHitItem leftPositionModal panelOverlaySharedContentsUiTopLevel">
		<div class="panelOverlaySharedContentsUiTitleContainer">
			<div class="panelOverlaySharedContentsUiTitle">History</div>
		</div>
		<div id="panelOverlaySharedContentsUiFromHistoryDropDownMenuArea">
			<button id="panelOverlaySharedContentsUiFromHistorySelectAllButton">'.$str_option_value_select_all.'</button>
		</div>' .
		build_html_loading_spinner('panelOverlaySharedContentsUiLoadingAddContentsFromHistory') .
		'<div class="panelOverlaySharedContentsUiContents leftPositionModalContents">
			<ul id="panelOverlaySharedContentsUiFromHistoryUl" class="panelOverlaySharedContentsUiSelectableList">
			</ul>
		</div>
	</div>';

	return $str_html;
}


function build_html_panel_overlay_shared_contents_ui_selected_contents($int_selected_language){

	global
		$arr_str_button_caption_exit;

	$str_html = '';
	
	// マジックナンバー
	$str_html =
	'<div id="panelOverlaySharedContentsUiSelectedContents" class="panelOverlaySharedContentsUi wiseHitItem panelOverlaySharedContentsUiTopLevel">
		<div class="panelOverlaySharedContentsUiTitleContainer">
			<div class="panelOverlaySharedContentsUiTitle">リスト</div>
			<button id="panelOverlaySharedContentsUiSelectedContentsCloseButton" class="panelOverlaySharedContentsUiButtons">'.$arr_str_button_caption_exit[$int_selected_language].'</button>
		</div>
			
		<div class="panelOverlaySharedContentsUiChangeModalButtonContainer">
			<button
				id="panelOverlaySharedContentsUiChangeModalButtonSearch"
				class="panelOverlaySharedContentsUiChangeModalButton panelOverlaySharedContentsUiChangeModalNavButton"
				data-view="ADD"
			>Search</button>

			<button
				id="panelOverlaySharedContentsUiChangeModalButtonCategory"
				class="panelOverlaySharedContentsUiChangeModalButton panelOverlaySharedContentsUiChangeModalNavButton"
				data-view="CATEGORY"
			>Category</button>

			<button
				id="panelOverlaySharedContentsUiChangeModalButtonBookmark"
				class="panelOverlaySharedContentsUiChangeModalButton panelOverlaySharedContentsUiChangeModalNavButton"
				data-view="BOOKMARK"
			>Bookmark</button>

			<button
				id="panelOverlaySharedContentsUiChangeModalButtonHistory"
				class="panelOverlaySharedContentsUiChangeModalButton panelOverlaySharedContentsUiChangeModalNavButton"
				data-view="HISTORY"
			>History</button>
			<button id="panelOverlaySharedContentsUiSelectedContentsShowInsightsButton" class="panelOverlaySharedContentsUiChangeModalButton showGrammarInsightsButton">Insights</button>
			<button id="panelOverlaySharedContentsUiSelectedContentsShowExplanationButton" class="panelOverlaySharedContentsUiChangeModalButton showWisePanelGrammarExplanationButton">Explanation</button>
		</div>

		<div class="panelOverlaySharedContentsUiContents">
			<ul id="panelOverlaySharedContentsUiSelectedContentsListUl" class="panelOverlaySharedContentsUiSelectableList">
			</ul>
		</div>
	</div>';

	return $str_html;
}

