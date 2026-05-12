<?php


/******************************************************
 *  GRAMMAR OUTLINE
 *  
 ******************************************************/
function recursive_build_html_grammar_outline($contents_tree_flags, $i, $seen, $seen_target_table, $did_build_grammar_outline, $root_id, $target_id, $arr_allow_display, $arr_bookmarks_data, $arr_belongs, $map_parent_to_children, $int_selected_language){

	global
		$arr_str_notice_loop,
		$int_hypernym_hyponym;

	++$i;
	if($i>10000){
		echo $arr_str_notice_loop[$int_selected_language];
		exit;
	}

	$str_grammar_outline_recursive = '';

	foreach ($seen as $element) {
		if ($element['id'] === $target_id && $element['table'] === $seen_target_table) {
			return array($seen, $str_grammar_outline_recursive, $did_build_grammar_outline);
		}
	}
	$seen[] = [
		'id'=>$target_id,
		'table'=>$seen_target_table
	];

	$root_id_checker = $target_id;
	$str_check_array_target = 'masta_japanese_root_id';
	$check_array = fetch_arr_grammar_usage_children_by_attribute($target_id, $int_hypernym_hyponym, $map_parent_to_children, $int_selected_language);

	$flag_ul = false;
	$str_ul = '';
	$str_li = '';
	$arr_li = [];

	if(!empty($check_array)){
		$flag_ul = true;
		foreach($check_array as $check_loop){
			$target_id_current = $check_loop[$str_check_array_target];
			$add_str_li = '';
			
			list($seen, $add_str_li, $did_build_grammar_outline) = recursive_build_html_grammar_outline($contents_tree_flags, $i, $seen, $seen_target_table, $did_build_grammar_outline, $root_id, $target_id_current, $arr_allow_display, $arr_bookmarks_data, $arr_belongs, $map_parent_to_children, $int_selected_language);
			$str_li .= $add_str_li;
			$arr_li[] = $add_str_li;
		}
	}

	if($flag_ul){
		$str_li = implode('', $arr_li);
		$state_class = !empty($contents_tree_flags['openOutline']) ? ' completed' : '';
		$str_ul = '<ul class="grammarOutlineUl' . $state_class . '">' . $str_li . '</ul>';
	}
	
	if(in_array($target_id,$arr_allow_display)){
		list($str_div_grammar_outline_text, $str_explanation_button) = build_html_explanation_button($contents_tree_flags['doDisplayGrammarOutlineLabelButtonsExplanation'], $target_id, $int_selected_language);
		$arr_div_grammar_outline_button = fetch_arr_masta_japanese_root_from_parent_to_child($arr_belongs, true, $target_id, $arr_allow_display, $map_parent_to_children, $int_selected_language);

		if(!empty($str_li)){
			$contents_tree_flags['doDisplayGrammarOutlineUlOpener'] = true;
		}
		else{
			$contents_tree_flags['doDisplayGrammarOutlineUlOpener'] = false;
		}

		$str_div_grammar_outline_contents_container = build_html_grammar_outline_container($contents_tree_flags, $arr_bookmarks_data, $arr_div_grammar_outline_button, $str_explanation_button, $str_div_grammar_outline_text, $int_selected_language);

		$str_div_grammar_outline_container = '<div class="divGrammarOutlineContainer">'.$str_div_grammar_outline_contents_container.'</div>';
		$str_grammar_outline_recursive = '<li class="grammarOutlineLi">'.$str_div_grammar_outline_container.$str_ul.'</li>';
	
		$did_build_grammar_outline = true;
	
		if($root_id === $target_id){
			if(empty($str_grammar_outline_recursive)){
				return array($seen, $str_grammar_outline_recursive, $did_build_grammar_outline);
			}
			$state_class_top = !empty($contents_tree_flags['openOutline']) ? ' completed' : '';
			$str_grammar_outline_recursive =
				'<ul class="grammarOutlineUlTopElement' . $state_class_top . '">' .
					$str_grammar_outline_recursive .
				'</ul>';
		}
	}   
	return array($seen, $str_grammar_outline_recursive, $did_build_grammar_outline);
}


function build_html_grammar_outline_container($contents_tree_flags, $arr_bookmarks_data, $arr_div_grammar_outline_button, $str_explanation_button, $str_div_grammar_outline_text, $int_selected_language){
	
	global
		$str_grammarOutlineLabelButtonUlOpenerCompleted,
		$str_grammarOutlineLabelButtonUlOpener;

	$str_div_grammar_outline_contents_container = '';

	$str_divGrammarOutlineGrammarsUl = '';
	$str_grammarOutlineUlOpenerButton = '';
	$str_grammarsUlOpenerButton = '';

	if(isset($contents_tree_flags['doDisplayGrammarOutlineUlOpener']) && $contents_tree_flags['doDisplayGrammarOutlineUlOpener']){
		$state_class = !empty($contents_tree_flags['openOutline']) ? ' completed' : '';
		$buttonUlOpener = !empty($contents_tree_flags['openOutline']) ? $str_grammarOutlineLabelButtonUlOpenerCompleted[$int_selected_language] : $str_grammarOutlineLabelButtonUlOpener[$int_selected_language];
		
		$str_grammarOutlineUlOpenerButton = '<button class="grammarOutlineLabelButton grammarOutlineLabelButtonOpener grammarOutlineLabelButtonGrammarOutlineUlOpener'.$state_class.'">'.$buttonUlOpener.'</button>';
	}
	
	list($str_divGrammarOutlineGrammarsUl, $str_grammarsUlOpenerButton) = build_html_grammar_outline_grammar($contents_tree_flags, $arr_bookmarks_data, $arr_div_grammar_outline_button, $int_selected_language);

	$str_buttons_container = $str_grammarOutlineUlOpenerButton.$str_grammarsUlOpenerButton;
	$str_text_container = '
	<div class="divGrammarOutlineTextContainer">'.
		$str_explanation_button.'
		<div class="divGrammarOutlineText">'.
			escape_html($str_div_grammar_outline_text).
		'</div>
	</div>';

	if($contents_tree_flags['doDisplayGrammarOutlineCheckbox']){
		$str_div_grammar_outline_Label_contents =
		'<div class="divGrammarOutlineLabelContainer">
			<input type="checkbox" class="grammarOutlineCheckbox grammarOutlineTextCheckbox">'.
			$str_buttons_container.
			$str_text_container.'
		</div>'.$str_divGrammarOutlineGrammarsUl;
	}
	else{
		$str_div_grammar_outline_Label_contents =
		'<div class="divGrammarOutlineLabelContainer">'.
			$str_buttons_container.
			$str_text_container.'
		</div>'.$str_divGrammarOutlineGrammarsUl;
	}

	$str_div_grammar_outline_contents_container = '<div class="divGrammarOutlineContentsContainer">'.$str_div_grammar_outline_Label_contents.'</div>';
	return $str_div_grammar_outline_contents_container;
}


function build_html_explanation_button($doDisplayGrammarOutlineLabelButtonsExplanation, $target_id, $int_selected_language){
	
	global
		$int_grammar_outline_status,
		$int_hidden_in_grammar_outline,
		$arr_columns_masta_japanese_root,
		$int_masta_japanese_category_id_terminology,
		$int_masta_japanese_category_id_grammar_relation,
		$t_masta_japanese_section,
		$str_snake_to_camel_unique_code,
		$str_column_root_kana,
		$str_grammarOutlineLabelButtonExplanation,
		$str_grammarOutlineLabelButtonExplanationMarker;

	$str_html = '';
	$str_div_grammar_outline_text = '';

	
	$arr_belongs_for_explanationButton = [
		$int_grammar_outline_status,
		$int_hidden_in_grammar_outline
	];

	$arr_contents_target = fetch_arr_masta_japanese_root_default($target_id, $int_selected_language);
	$str_div_grammar_outline_text = $arr_contents_target[$arr_columns_masta_japanese_root[$int_selected_language]];

	if(
		intval($arr_contents_target['category_id']) === $int_masta_japanese_category_id_terminology ||
		intval($arr_contents_target['category_id']) === $int_masta_japanese_category_id_grammar_relation
	){
	
		$arr_strSQL_select = [
			[$t_masta_japanese_section,'id']
		];
	
		$strSQL_from = ' FROM ' .$t_masta_japanese_section;
	
		$arr_strSQL_where = [
			[
				[
					[$t_masta_japanese_section,'root_id','=',$arr_contents_target['id'],'PDO::PARAM_INT','']
				],
				''
			]
		];

		$arr_strSQL_order = [];

		$strSQL_option = '';

		list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_section) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);

		if ($pdo_has_error == FLAG_TRUE || $select_has_error == FLAG_TRUE || $e !== null) {
			return array($str_div_grammar_outline_text, '');
		}

		$arr_matched = [];
		
		if ($doDisplayGrammarOutlineLabelButtonsExplanation && !empty($arr_masta_japanese_section)){
			$str_html = '<button class="grammarOutlineLabelButton grammarOutlineLabelButtonExplanation terminologyButtonsExplanation" data-japanese-id="'.$arr_contents_target['id'].'" data-unique-code="'.$arr_contents_target[$str_snake_to_camel_unique_code].'" data-japanese="'.$arr_contents_target[$arr_columns_masta_japanese_root[$int_selected_language]].'" data-kana="'.$arr_contents_target[$str_column_root_kana].'" data-category-id="'.$arr_contents_target['category_id'].'" title="'.$str_grammarOutlineLabelButtonExplanation[$int_selected_language].'">'.$str_grammarOutlineLabelButtonExplanationMarker[$int_selected_language].'</button>';
		}
		else{
			$str_html = '<button class="grammarOutlineLabelButton grammarOutlineLabelButtonExplanation terminologyButtonsExplanation hidden" data-japanese-id="'.$arr_contents_target['id'].'" data-unique-code="'.$arr_contents_target[$str_snake_to_camel_unique_code].'" data-japanese="'.$arr_contents_target[$arr_columns_masta_japanese_root[$int_selected_language]].'" data-kana="'.$arr_contents_target[$str_column_root_kana].'" data-category-id="'.$arr_contents_target['category_id'].'" title="'.$str_grammarOutlineLabelButtonExplanation[$int_selected_language].'">'.$str_grammarOutlineLabelButtonExplanationMarker[$int_selected_language].'</button>';
		}
		return array($str_div_grammar_outline_text, $str_html);
	}
	return array($str_div_grammar_outline_text, $str_html);
}


function build_html_grammar_outline_grammar($contents_tree_flags, $arr_bookmarks_data, $arr_div_grammar_outline_button, $int_selected_language){

	global
		$int_hidden_in_grammar_outline,
		$str_grammarOutlineLabelButtonUlOpener;

	$str_divGrammarOutlineGrammarsUl = '';
	$arr_li = [];
	$str_grammarsUlOpenerButton = '';

	if(!empty($arr_div_grammar_outline_button) && $contents_tree_flags['doDisplayGrammarOutlineGrammars']){
		foreach($arr_div_grammar_outline_button as $loop_div_grammar_outline_button){
			$str_div_grammar_outline_grammars = build_html_grammar_outline_item($contents_tree_flags, $arr_bookmarks_data, $loop_div_grammar_outline_button, $int_selected_language);
			$arr_li[] = $str_div_grammar_outline_grammars;
		}

		$str_divGrammarOutlineGrammarsLi = implode('', $arr_li);
		$str_divGrammarOutlineGrammarsUl = '<ul class="divGrammarOutlineGrammarsUl">'.$str_divGrammarOutlineGrammarsLi.'
		</ul>';

		$arr_derived_grammars = array_filter($arr_div_grammar_outline_button, function ($element) use ($int_hidden_in_grammar_outline) {
			return isset($element['grammar_outline_status']) && $element['grammar_outline_status'] === $int_hidden_in_grammar_outline;
		});
		$count_div_grammar_outline_button = count($arr_div_grammar_outline_button);
		$count_derived_grammars = count($arr_derived_grammars);

		switch (true) {
			case ($count_derived_grammars === COUNT_EMPTY):
				$str_grammarsUlOpenerButton = '<button class="grammarOutlineLabelButton grammarOutlineLabelButtonOpener grammarOutlineLabelButtonGrammarsUlOpener">'.$str_grammarOutlineLabelButtonUlOpener[$int_selected_language].'</button>';
				break;
			
			case ($count_derived_grammars === $count_div_grammar_outline_button):
				$str_grammarsUlOpenerButton = '<button class="grammarOutlineLabelButton grammarOutlineLabelButtonOpener grammarOutlineLabelButtonGrammarsUlOpener derivedGrammarsButtonOpener">'.$str_grammarOutlineLabelButtonUlOpener[$int_selected_language].'</button>';
				break;

			default:   
				$str_grammarsUlOpenerButton = '<button class="grammarOutlineLabelButton grammarOutlineLabelButtonOpener grammarOutlineLabelButtonGrammarsUlOpener">'.$str_grammarOutlineLabelButtonUlOpener[$int_selected_language].'</button>';
				break;
		}
				
	}
	else{
		$str_divGrammarOutlineGrammarsUl = '<ul class="divGrammarOutlineGrammarsUl"></ul>';
	}
	return array($str_divGrammarOutlineGrammarsUl, $str_grammarsUlOpenerButton);
}


function build_html_grammar_outline_item($contents_tree_flags, $arr_bookmarks_data, $array, $int_selected_language){
	
	global
		$arr_columns_masta_japanese_root,
		$str_column_root_kana,
		$str_snake_to_camel_unique_code,
		$str_avoid_null_proxy,
		$int_hidden_in_grammar_outline,
		$int_masta_japanese_category_id_grammar,
		$int_masta_japanese_category_id_terminology,
		$int_masta_japanese_category_id_grammar_relation,
		$str_grammarOutlineLabelButtonExplanation,
		$str_grammarOutlineLabelButtonExplanationMarker;


	$str_div_grammar_outline_grammars = '';

	$doDisplayGrammarOutlineCheckbox = $contents_tree_flags['doDisplayGrammarOutlineCheckbox'];
	$doDisplayGrammarOutlineLabelButtonsExplanation = $contents_tree_flags['doDisplayGrammarOutlineLabelButtonsExplanation'];
	$doDisplayDerivedGrammars = $contents_tree_flags['doDisplayDerivedGrammars'];
	$displayInGrammarView = $contents_tree_flags['displayInGrammarView'];
	
	$masta_japanese_root_id = intval($array['masta_japanese_root_id']);
	$title = escape_html($array[$arr_columns_masta_japanese_root[$int_selected_language]]);
	$kana = isset($array[$str_column_root_kana]) ? escape_html($array[$str_column_root_kana]) : '';
	$data_example = escape_html(apply_remove_original_tags($array['root_example']));
	$str_example = apply_text_for_output($array['root_example']);
	$grammar_unique_code = escape_html($array[$str_snake_to_camel_unique_code]);
	$category_id = intval($array['category_id']);
	$grammar_outline_status = null;

	if (isset($array['grammar_outline_status'])) {
		$grammar_outline_status = intval($array['grammar_outline_status']);
	}

	if($str_example === $str_avoid_null_proxy){
		$str_example = $title;
	}
	
	$str_li_classnames = '';
	$str_input_classnames = '';
	$str_div_grammar_outline_explanation_button = '';

	if($doDisplayDerivedGrammars && $grammar_outline_status === $int_hidden_in_grammar_outline){
		$str_li_classnames = 'divGrammarOutlineGrammarsLi derivedGrammarLi';
		if($displayInGrammarView){
			$str_input_classnames = 'grammarOutlineCheckbox grammarOutlineGrammarCheckbox derivedGrammarCheckbox';
		}
		else{
			$str_input_classnames = 'grammarOutlineCheckbox grammarOutlineGrammarCheckbox derivedGrammarCheckbox grammarOutlineLessonContentsCheckbox';
		}
		if(
			$doDisplayGrammarOutlineLabelButtonsExplanation &&
			(
				$category_id === $int_masta_japanese_category_id_grammar || 
				$category_id === $int_masta_japanese_category_id_terminology ||
				$category_id === $int_masta_japanese_category_id_grammar_relation
			)
		)
		{
			$str_div_grammar_outline_explanation_button = '<button class="grammarOutlineLabelButton grammarOutlineLabelButtonExplanation derivedGrammarButtonsExplanation" data-japanese-id="'.$masta_japanese_root_id.'" data-unique-code="'.$grammar_unique_code.'" data-japanese="'.$title.'" data-kana="'.$kana.'" data-category-id="'.$category_id.'" title="'.$str_grammarOutlineLabelButtonExplanation[$int_selected_language].'">'.$str_grammarOutlineLabelButtonExplanationMarker[$int_selected_language].'</button>';
		}
		else{
			$str_div_grammar_outline_explanation_button = '';
		}
	}
	else{
		$str_li_classnames = 'divGrammarOutlineGrammarsLi basicGrammarLi';
		if($displayInGrammarView){
			$str_input_classnames = 'grammarOutlineCheckbox grammarOutlineGrammarCheckbox basicGrammarCheckbox';
		}
		else{
			$str_input_classnames = 'grammarOutlineCheckbox grammarOutlineGrammarCheckbox basicGrammarCheckbox grammarOutlineLessonContentsCheckbox';
		}
		if(
			$doDisplayGrammarOutlineLabelButtonsExplanation &&
			(
				$category_id === $int_masta_japanese_category_id_grammar || 
				$category_id === $int_masta_japanese_category_id_terminology ||
				$category_id === $int_masta_japanese_category_id_grammar_relation
			)
		)
		{
			$str_div_grammar_outline_explanation_button = '<button class="grammarOutlineLabelButton grammarOutlineLabelButtonExplanation basicGrammarButtonsExplanation" data-japanese-id="'.$masta_japanese_root_id.'" data-unique-code="'.$grammar_unique_code.'" data-japanese="'.$title.'" data-kana="'.$kana.'" data-category-id="'.$category_id.'" title="'.$str_grammarOutlineLabelButtonExplanation[$int_selected_language].'">'.$str_grammarOutlineLabelButtonExplanationMarker[$int_selected_language].'</button>';
		}
		else{
			$str_div_grammar_outline_explanation_button = '';
		}
	}

	$html_bookmark_star = '';

	$room_unique_code = $arr_bookmarks_data['room_unique_code'] ?? '';
	$map_grammar_unique_code = $arr_bookmarks_data['map_grammar_unique_code'] ?? null;

	if ($room_unique_code !== '' && is_array($map_grammar_unique_code)) {

		$unique_id = uniqid();
		$is_bookmarked =
			isset($map_grammar_unique_code[$grammar_unique_code]) &&
			empty($map_grammar_unique_code[$grammar_unique_code]['deleted_at']);

		$html_bookmark_star = build_html_bookmark_star(
			$unique_id,
			$grammar_unique_code,
			$is_bookmarked,
			$room_unique_code
		);
	}

	if($doDisplayGrammarOutlineCheckbox){
		$str_div_grammar_outline_grammars = '
		<li class="'.$str_li_classnames.'" data-unique-code="'.$grammar_unique_code.'">
			<input 
				type="checkbox" 
				name="'.$grammar_unique_code.'" 
				value="'.$grammar_unique_code.'" 
				class="'.$str_input_classnames.'" 
				data-japanese-id="'.$masta_japanese_root_id.'"
				data-japanese="'.$title.'"
				data-title="'.$title.'"
				data-example="'.$data_example.'"
				data-unique-code="'.$grammar_unique_code.'"
				data-category-id="'.$category_id.'"
			>'.
			$str_div_grammar_outline_explanation_button.'
			<div class="divGrammarOutlineInputLabel divGrammarOutlineInputLabelTitle">'.
				$title.'
			</div>
			<div class="divGrammarOutlineInputLabel divGrammarOutlineInputLabelExample hidden">'.
				$str_example.'
			</div>'.
			$html_bookmark_star.'
		</li>';
	}
	else{
		$str_div_grammar_outline_grammars = '
		<li class="divGrammarOutlineGrammarsLi">'.
			$str_div_grammar_outline_explanation_button.'
			<div class="divGrammarOutlineInputLabel divGrammarOutlineInputLabelTitle">'.
				$title.'
			</div>
			<div class="divGrammarOutlineInputLabel divGrammarOutlineInputLabelExample hidden">'.
				$str_example.'
			</div>'.
			$html_bookmark_star.'
		</li>';
	}

	return $str_div_grammar_outline_grammars;
}