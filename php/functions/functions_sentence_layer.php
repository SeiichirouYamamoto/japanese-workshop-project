<?php

/******************************************************
 *  PAGE
 *  
 ******************************************************/
function build_html_create_layers_page($int_selected_language){


	$str_create_layers = '';

	$user_level = get_user_level();
	if(!is_admin_level($user_level)){
		return $str_create_layers;
	}

	$str_canvas = build_html_wise_canvas($int_selected_language);

	$str_create_layers_body = '
	<div id="wiseBodyCreateLayers" class="wiseBodyRoot">
		<div id="wiseContentCreateLayers" class="wiseContentRoot">'.
			$str_canvas.'
			<div id="wiseContainersMainContentAreaCreateLayers" class="wiseContainersMainContentArea"></div>
			<div id="wiseBoardMarksLayer" class="wiseBoardMarksLayer wisePointerNone"></div>
		</div>
	</div>';

	// マジックナンバー
	$str_create_layers_layersMenu =
		'<div id="sentenceLayerMenuCreateLayers" class="sentenceLayerMenu">
		<div id="sentenceLayerMenuCreateNewLayerButtonContainer" class="">
			<button id="sentenceLayerMenuChangeLayoutButtonCreateLayers" class="sentenceLayerMenuButtons sentenceLayerMenuChangeLayoutButton">切替</button>
			<input type="text" id="sentenceLayerMenuCreateNewLayerInput" class=""></input>
			<button id="sentenceLayerMenuCreateNewLayerButton">新規作成</button>
		</div>
		<div class="sentenceLayerMenuContents">
			<ul id="sentenceLayerMenuUl" class="">
			</ul>
		</div>
		</div>';

	$str_create_layers_sidemenu =
		'<div id="sentenceLayerSideMenuCreateLayers" class="sentenceLayerSideMenu">'.
			$str_create_layers_layersMenu.
		'</div>';

	$str_create_layers = $str_create_layers_body.$str_create_layers_sidemenu;

	$str_create_layers = '<section id="sectionWiseCreateLayers" class="wise-require-fullscreen">'.$str_create_layers.'</section>';
	$str_layerUpdateScreen = build_html_layer_update_screen($int_selected_language);
	$str_layerUpdateOverrideScreen = build_html_layer_update_override_screen($int_selected_language);

	$str_create_layers = $str_create_layers.$str_layerUpdateScreen.$str_layerUpdateOverrideScreen;

	return $str_create_layers;
}



/******************************************************
 *  HTML
 *  
 ******************************************************/
function build_html_layer_update_screen($int_selected_language){

	global
		$str_layerUpdateScreenSideMenuUsedGrammarIdTitlePredicate,
		$str_layerUpdateScreenSideMenuUsedGrammarIdTitleSelectedGrammar,
		$str_layerUpdateScreenSideMenuUsedGrammarIdTitleSelectedJapaneseParticle,
		$str_layerUpdateScreenSideMenuUsedGrammarIdTitleUnselectedGrammar,
		$str_layerUpdateScreenSideMenuUsedGrammarIdTitleUnselectedJapaneseParticle,
		$str_layerUpdateScreenSideMenuUsedGrammarIdTitleParticles,
		$str_layerUpdateScreenSideMenuUsedGrammarIdTitleInflection,
		$str_layerUpdateScreenSideMenuUsedGrammarIdTitleSpecialTerms,
		$str_layerUpdateScreenHeader,
		$str_layerUpdateScreenGrammarIdTitle,
		$str_layerUpdateScreenGrammarJapaneseTitle,
		$str_layerUpdateScreenLayerNameTitle,
		$str_layerUpdateScreenButton,
		$str_layerUpdateScreenLayerElementsDisplayAreaTitle,
		$arr_str_button_caption_update,
		$arr_str_button_caption_exit;

	$str_layerUpdateScreen = '';

	$details_class = 'sentenceLayerUpdateScreenDetails animationSlideIn';
	$summary_class = 'sentenceLayerUpdateScreenDetailsSummarys heading tagGrammarView colorPurpleBlack withBackgroundColor rounded headingSummary';
	$details_div_class = 'detailsDiv detailsDivAddMarginBottom animationSlideIn';
	
	
	$str_contents_predicate = '
		<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdPredicate" class="sentenceLayerUpdateScreenContainer">
			<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdTitlePredicate" class="sentenceLayerUpdateScreenContainerTitle">「'.$str_layerUpdateScreenSideMenuUsedGrammarIdTitlePredicate[$int_selected_language].'」</div>
			<ul id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdPredicateUl" class="sentenceLayerUpdateScreenSideMenuUl"></ul>
		</div>';

	$str_contents_selectedGrammar = '
		<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdSelectedGrammar" class="sentenceLayerUpdateScreenContainer">
			<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdTitleSelectedGrammar" class="sentenceLayerUpdateScreenContainerTitle">「'.$str_layerUpdateScreenSideMenuUsedGrammarIdTitleSelectedGrammar[$int_selected_language].'」</div>
			<ul id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdSelectedGrammartUl" class="sentenceLayerUpdateScreenSideMenuUl"></ul>
		</div>';

	$str_contents_selectedParticle = '
		<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdSelectedJapaneseParticle" class="sentenceLayerUpdateScreenContainer">
			<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdTitleSelectedJapaneseParticle" class="sentenceLayerUpdateScreenContainerTitle">「'.$str_layerUpdateScreenSideMenuUsedGrammarIdTitleSelectedJapaneseParticle[$int_selected_language].'」</div>
			<ul id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdSelectedJapaneseParticleUl" class="sentenceLayerUpdateScreenSideMenuUl"></ul>
		</div>';

	$str_contents_unselectedGrammar = '
		<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdUnselectedGrammar" class="sentenceLayerUpdateScreenContainer">
			<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdTitleUnselectedGrammar" class="sentenceLayerUpdateScreenContainerTitle">「'.$str_layerUpdateScreenSideMenuUsedGrammarIdTitleUnselectedGrammar[$int_selected_language].'」</div>
			<ul id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdUnselectedGrammarUl" class="sentenceLayerUpdateScreenSideMenuUl"></ul>
		</div>';

	$str_contents_unselectedParticle = '
		<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdUnselectedJapaneseParticle" class="sentenceLayerUpdateScreenContainer">
			<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdTitleUnselectedJapaneseParticle" class="sentenceLayerUpdateScreenContainerTitle">「'.$str_layerUpdateScreenSideMenuUsedGrammarIdTitleUnselectedJapaneseParticle[$int_selected_language].'」</div>
			<ul id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdUnselectedJapaneseParticleUl" class="sentenceLayerUpdateScreenSideMenuUl"></ul>
		</div>';

	$str_contents_particles = '
		<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdParticles" class="sentenceLayerUpdateScreenContainer">
			<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdTitleParticles" class="sentenceLayerUpdateScreenContainerTitle">「'.$str_layerUpdateScreenSideMenuUsedGrammarIdTitleParticles[$int_selected_language].'」</div>
			<ul id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdParticlesUl" class="sentenceLayerUpdateScreenSideMenuUl"></ul>
		</div>';

	$str_contents_inflection = '
		<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdInflection" class="sentenceLayerUpdateScreenContainer">
			<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdTitleInflection" class="sentenceLayerUpdateScreenContainerTitle">「'.$str_layerUpdateScreenSideMenuUsedGrammarIdTitleInflection[$int_selected_language].'」</div>
			<ul id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdInflectionUl" class="sentenceLayerUpdateScreenSideMenuUl"></ul>
		</div>';

	$str_contents_specialTerms = '
		<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdSpecialTerms" class="sentenceLayerUpdateScreenContainer">
			<div id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdTitleSpecialTerms" class="sentenceLayerUpdateScreenContainerTitle">「'.$str_layerUpdateScreenSideMenuUsedGrammarIdTitleSpecialTerms[$int_selected_language].'」</div>
			<ul id="sentenceLayerUpdateScreenSideMenuUsedGrammarIdSpecialTermsUl" class="sentenceLayerUpdateScreenSideMenuUl"></ul>
		</div>';

	
	$str_details_predicate = build_html_details_contents($str_contents_predicate,$str_layerUpdateScreenSideMenuUsedGrammarIdTitlePredicate[$int_selected_language],$details_class,$summary_class,$details_div_class);
	$str_details_selectedGrammar = build_html_details_contents($str_contents_selectedGrammar,$str_layerUpdateScreenSideMenuUsedGrammarIdTitleSelectedGrammar[$int_selected_language],$details_class,$summary_class,$details_div_class);
	$str_details_selectedParticle = build_html_details_contents($str_contents_selectedParticle,$str_layerUpdateScreenSideMenuUsedGrammarIdTitleSelectedJapaneseParticle[$int_selected_language],$details_class,$summary_class,$details_div_class);
	$str_details_unselectedGrammar = build_html_details_contents($str_contents_unselectedGrammar,$str_layerUpdateScreenSideMenuUsedGrammarIdTitleUnselectedGrammar[$int_selected_language],$details_class,$summary_class,$details_div_class);
	$str_details_unselectedParticle = build_html_details_contents($str_contents_unselectedParticle,$str_layerUpdateScreenSideMenuUsedGrammarIdTitleUnselectedJapaneseParticle[$int_selected_language],$details_class,$summary_class,$details_div_class);
	$str_details_particles = build_html_details_contents($str_contents_particles,$str_layerUpdateScreenSideMenuUsedGrammarIdTitleParticles[$int_selected_language],$details_class,$summary_class,$details_div_class);
	$str_details_inflection = build_html_details_contents($str_contents_inflection,$str_layerUpdateScreenSideMenuUsedGrammarIdTitleInflection[$int_selected_language],$details_class,$summary_class,$details_div_class);
	$str_details_specialTerms = build_html_details_contents($str_contents_specialTerms,$str_layerUpdateScreenSideMenuUsedGrammarIdTitleSpecialTerms[$int_selected_language],$details_class,$summary_class,$details_div_class);

	$str_layerUpdateScreen = '
	<div id="sentenceLayerUpdateScreen" class="sentenceLayerScreenModal sentenceLayerUpdateScreenModal">
		<h2>'.$str_layerUpdateScreenHeader[$int_selected_language].'</h2>
		<section class="sentenceLayerUpdateScreenSection">
			<div id="sentenceLayerUpdateScreenGrammarId" class="sentenceLayerUpdateScreenContainer">
				<div id="sentenceLayerUpdateScreenGrammarIdTitle" class="sentenceLayerUpdateScreenContainerTitle">「'.$str_layerUpdateScreenGrammarIdTitle[$int_selected_language].'」</div>
				<div id="sentenceLayerUpdateScreenGrammarIdContent" class="sentenceLayerUpdateScreenContainerContent">
					<input type="number" id="sentenceLayerUpdateScreenGrammarIdInput" class="sentenceLayerUpdateScreenContainerInput" />
					<button id="sentenceLayerUpdateScreenGrammarIdButton" class="sentenceLayerUpdateScreenContainerButton">'.$arr_str_button_caption_update[$int_selected_language].'</button>
				</div>
			</div>
			<div id="sentenceLayerUpdateScreenGrammarJapanese" class="sentenceLayerUpdateScreenContainer">
				<div id="sentenceLayerUpdateScreenGrammarJapaneseTitle" class="sentenceLayerUpdateScreenContainerTitle">「'.$str_layerUpdateScreenGrammarJapaneseTitle[$int_selected_language].'」</div>
				<div id="sentenceLayerUpdateScreenGrammarJapaneseContent" class="sentenceLayerUpdateScreenContainerContent"></div>
			</div>
			<div id="sentenceLayerUpdateScreenLayerName" class="sentenceLayerUpdateScreenContainer">
				<div id="sentenceLayerUpdateScreenLayerNameTitle" class="sentenceLayerUpdateScreenContainerTitle">「'.$str_layerUpdateScreenLayerNameTitle[$int_selected_language].'」</div>
				<div id="sentenceLayerUpdateScreenLayerNameContent" class="sentenceLayerUpdateScreenContainerContent">
					<input type="text" id="sentenceLayerUpdateScreenLayerNameInput" class="sentenceLayerUpdateScreenContainerInput" />
					<button id="sentenceLayerUpdateScreenLayerNameUpdateButton" class="sentenceLayerUpdateScreenContainerButton">'.$arr_str_button_caption_update[$int_selected_language].'</button>
				</div>
			</div>
		</section>
		<section class="sentenceLayerUpdateScreenButtonsSection">
			<button id="sentenceLayerUpdateScreenButtonSubmit" class="sentenceLayerUpdateScreenButton">'.$str_layerUpdateScreenButton[$int_selected_language].'</button>
		</section>
	</div>
	<div id="sentenceLayerUpdateScreenSideMenu" class="sentenceLayerScreenModal sentenceLayerUpdateScreenModal">
		<section class="sentenceLayerUpdateScreenSection">'.
			$str_details_predicate.
			$str_details_selectedGrammar.
			$str_details_selectedParticle.
			$str_details_unselectedGrammar.
			$str_details_unselectedParticle.
			$str_details_particles.
			$str_details_inflection.
			$str_details_specialTerms.
		'</section>
	</div>
	<div id="sentenceLayerUpdateScreenLayerElements" class="sentenceLayerScreenModal sentenceLayerUpdateScreenModal">
		<section class="sentenceLayerUpdateScreenSection">
			<div id="sentenceLayerUpdateScreenLayerElementsDisplayArea" class="sentenceLayerUpdateScreenContainer">
				<div id="sentenceLayerUpdateScreenLayerElementsDisplayAreaTitle" class="sentenceLayerUpdateScreenContainerTitle">「'.$str_layerUpdateScreenLayerElementsDisplayAreaTitle[$int_selected_language].'」</div>
				<ul id="sentenceLayerUpdateScreenLayerElementsDisplayAreaUl" class="sentenceLayerUpdateScreenSideMenuUl"></ul>
			</div>
		</section>
		<section class="sentenceLayerUpdateScreenButtonsSection">
			<button id="sentenceLayerUpdateScreenButtonClose" class="sentenceLayerUpdateScreenButton">'.$arr_str_button_caption_exit[$int_selected_language].'</button>
		</section>
	</div>';

	$str_layerUpdateScreen = '<div id="sentenceLayerUpdateScreenOverlay" class="sentenceLayerScreenOverlay">'.$str_layerUpdateScreen.'</div>';

	return $str_layerUpdateScreen;
}


function build_html_layer_update_override_screen($int_selected_language){

	global
		$arr_columns_masta_override;

	$str_html_overlay_close_button = build_html_overlay_close_button();

	$str_layerUpdateOverrideScreen = '';

	$arr_masta_override = fetch_arr_masta_override_list($int_selected_language);
	$op_override = [];
	foreach ($arr_masta_override as $o) {
		$oid = (int)$o['id'];
		$otext = escape_html($o[$arr_columns_masta_override[$int_selected_language]] ?? '');
		$ooperation = escape_html($o['operation'] ?? '');
		$otitle = $ooperation.':'.$otext;
		$op_override[] = '<option value="'.$oid.'">'.$otitle.'</option>';
	}

	$arr_html = [];

	$arr_html[] = '<div id="sentenceLayerUpdateOverrideOverlay" class="sentenceLayerScreenOverlay">';
	$arr_html[] = '<div id="sentenceLayerUpdateOverrideScreen" class="sentenceLayerScreenModal">';
	$arr_html[] = $str_html_overlay_close_button;
	$arr_html[] = '<h2 id="sentenceLayerUpdateOverrideScreenTitle">Override Elements</h2>';

	$arr_html[] = '<div class="modalScrollableContainer">';
	
	$arr_html[] = '<div class="sentenceLayerUpdateOverrideScreenUiContainer">';
	$arr_html[] = '<div class="sentenceLayerUpdateOverrideScreenFormContainer">';

	$arr_html[] = '<div class="sentenceLayerUpdateOverrideScreenFormRow">';
	$arr_html[] = '<label for="sentenceLayerUpdateOverrideOverrideIdSelect">Override</label>';
	$arr_html[] = '<select id="sentenceLayerUpdateOverrideOverrideIdSelect" class="sentenceLayerUpdateOverrideOverrideIdSelect">'.implode('', $op_override).'</select>';
	$arr_html[] = '</div>';

	$arr_html[] = '<div class="sentenceLayerUpdateOverrideScreenFormRow">';
	$arr_html[] = '<label for="sentenceLayerUpdateOverrideDisplayTextInput">Display</label>';
	$arr_html[] = '<input type="text" id="sentenceLayerUpdateOverrideDisplayTextInput" class="sentenceLayerUpdateOverrideDisplayTextInput" placeholder="display text">';
	$arr_html[] = '</div>';

	$arr_html[] = '<div class="sentenceLayerUpdateOverrideScreenFormRow">';
	$arr_html[] = '<label for="sentenceLayerUpdateOverrideHighlightToggleButton">Highlight</label>';
	$arr_html[] = '<div class="sentenceLayerUpdateOverrideToggleButtonContainer">';
	$arr_html[] = '<input type="checkbox" id="sentenceLayerUpdateOverrideHighlightToggleButton" class="sentenceLayerUpdateOverrideToggleButton">';
	$arr_html[] = '<label for="sentenceLayerUpdateOverrideHighlightToggleButton" class="sentenceLayerUpdateOverrideToggleLabel"></label>';
	$arr_html[] = '</div>';
	$arr_html[] = '</div>';


	$arr_html[] = '<div class="sentenceLayerUpdateOverrideScreenFormRow">';
	$arr_html[] = '<label for="sentenceLayerUpdateOverrideSortInput">Sort</label>';
	$arr_html[] = '<input type="number" id="sentenceLayerUpdateOverrideSortInput" class="sentenceLayerUpdateOverrideSortInput" value="'.SORT_FIRST.'">';
	$arr_html[] = '</div>';


	$arr_html[] = '</div>';
	$arr_html[] = '<button type="button" id="sentenceLayerUpdateOverrideCreateButton" class="sentenceLayerUpdateOverrideCreateButton" data-action="override:element:create">Create</button>';
	$arr_html[] = '</div>';

	$arr_html[] = '<div id="sentenceLayerUpdateOverrideScreenDisplayArea">';
	$arr_html[] = '<ul id="sentenceLayerUpdateOverrideScreenlayerUpdateOverridesUl"></ul>';
	$arr_html[] = build_html_loading_spinner('sentenceLayerUpdateOverrideScreenDisplayAreaLoading');
	$arr_html[] = '<div>';

	$arr_html[] = '</div>';

	$arr_html[] = '</div>';
	$arr_html[] = '</div>';

	$str_layerUpdateOverrideScreen = implode("\n", $arr_html);
	
	return $str_layerUpdateOverrideScreen;
}

/******************************************************
 *  TOOLS
 *  
 ******************************************************/

function get_data_override_text_parts_for_update($base, $overrides){

	global
		$int_masta_operation_id_replace_fixed,
		$int_masta_operation_id_add_before_fixed,
		$int_masta_operation_id_add_after_fixed,
		$int_masta_operation_id_omit,
		$int_masta_operation_id_replace_free,
		$int_masta_operation_id_add_before_free,
		$int_masta_operation_id_add_after_free;

    $out = $base;
    $prefix = '';
    $suffix = '';

    foreach ($overrides as $ov) {
        $op_id    = intval($ov['operation_id']);
		$txt_free  = str_replace(['[', ']'], '', (string)$ov['display_text']);
		$txt_fixed = str_replace(['[', ']'], '', (string)$ov['display_text_from_masta']);

        $isFixed = in_array($op_id, [
            $int_masta_operation_id_replace_fixed,
            $int_masta_operation_id_add_before_fixed,
            $int_masta_operation_id_add_after_fixed,
            $int_masta_operation_id_omit
        ], true);

        $val = $isFixed ? $txt_fixed : $txt_free;

        switch ($op_id) {
            case $int_masta_operation_id_omit:
                $out = '_';
                break;

            case $int_masta_operation_id_replace_fixed:
            case $int_masta_operation_id_replace_free:
                $out = '【'.$val.'】';
                break;

            case $int_masta_operation_id_add_before_fixed:
            case $int_masta_operation_id_add_before_free:
                $prefix .= '【'.$val.'】+';
                break;

            case $int_masta_operation_id_add_after_fixed:
            case $int_masta_operation_id_add_after_free:
                $suffix .= '+【'.$val.'】';
                break;
        }
    }

    return [$prefix, $out, $suffix];
}