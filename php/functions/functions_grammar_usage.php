<?php


/******************************************************
 *  GRAMMAR USAGE
 *  
 ******************************************************/
function get_arr_tree_target_usage_category_ids($int_selected_language){
	
	global
		$t_grammar_usage_categories;
		
	$arr_strSQL_select = [
		[$t_grammar_usage_categories,'id']
	];

	$strSQL_from = ' FROM ' .$t_grammar_usage_categories;

	$arr_strSQL_where = [
			[
				[
					[$t_grammar_usage_categories,'draw_tree','=',FLAG_TRUE,'PDO::PARAM_INT','']
				],
				''
			]
		];

	$arr_strSQL_order = [
		[$t_grammar_usage_categories,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_tree_target_usage_category_ids) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	$arr_tree_target_usage_category_ids = array_column($arr_tree_target_usage_category_ids, 'id');

	return $arr_tree_target_usage_category_ids;
}


function get_arr_tree_usage_category_ids_by_status($int_selected_language, $status) {

	global
		$t_grammar_usage_categories;

    $arr_strSQL_select = [
        [$t_grammar_usage_categories,'id']
    ];

    $strSQL_from = ' FROM ' .$t_grammar_usage_categories;

    $arr_strSQL_where = [
        [
            [
                [$t_grammar_usage_categories,'draw_tree','=',FLAG_TRUE,'PDO::PARAM_INT','And'],
                [$t_grammar_usage_categories,'status','=',$status,'PDO::PARAM_INT','']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_grammar_usage_categories,'sort','ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_tree_usage_category_ids_by_status) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    $arr_tree_usage_category_ids_by_status = array_column($arr_tree_usage_category_ids_by_status, 'id');

    return $arr_tree_usage_category_ids_by_status;
}


function get_arr_root_parent_child_relations($flag_parent_or_child, $arr_elements, $int_selected_language){

	global
		$int_to_parent,
		$int_to_child,
		$int_to_all;

	$arr_seen = [];

	$flag_parent = false;
	$flag_child = false;

	switch($flag_parent_or_child){
		case $int_to_parent:
			$flag_parent = true;
			$flag_child = false;
			break;

		case $int_to_child:
			$flag_parent = false;
			$flag_child = true;
			break;

		case $int_to_all:
			$flag_parent = true;
			$flag_child = true;
			break;

		default:
			$flag_parent = false;
			$flag_child = false;
	}

	$relations = fetch_all_root_parent_child_relations($int_selected_language);
	$map_parent_to_children = [];
	foreach ($relations as $row) {
		$map_parent_to_children[$row['masta_japanese_root_id_parent']][] = $row;
	}

	foreach($arr_elements as $loop_elements){

			$masta_japanese_root_id_current = $loop_elements['masta_japanese_root_id'];

			if($flag_parent){
				$i = INDEX_FIRST;
				$seen = [];
				$seen = recursive_get_arr_root_parent_child_relations($i, $seen, $int_to_parent, $masta_japanese_root_id_current, $map_parent_to_children, $int_selected_language);
				$arr_seen = array_merge($arr_seen,$seen);
			}

			if($flag_child){
				$i = INDEX_FIRST;
				$seen = [];
				$seen = recursive_get_arr_root_parent_child_relations($i, $seen, $int_to_child, $masta_japanese_root_id_current, $map_parent_to_children, $int_selected_language);
				$arr_seen = array_merge($arr_seen,$seen);
			}
	}
	return $arr_seen;
}


function recursive_get_arr_root_parent_child_relations($i, $seen, $parent_or_child, $t_masta_japanese_root_id, $map_parent_to_children, $int_selected_language){

	global
		$arr_str_notice_loop,
		$int_to_parent,
		$int_hypernym_hyponym;

	++$i;
	if($i>10000){
		echo $arr_str_notice_loop[$int_selected_language];
		exit;
	}

	if(in_array($t_masta_japanese_root_id,$seen)){
		return $seen;
	}

	$seen[] = $t_masta_japanese_root_id;

	if($parent_or_child == $int_to_parent){
		$check_array = fetch_arr_grammar_usage_parents_by_attribute($t_masta_japanese_root_id, $int_hypernym_hyponym, 'grammar_outline_status', $int_selected_language);
	}
	else{
		$check_array = fetch_arr_grammar_usage_children_by_attribute($t_masta_japanese_root_id, $int_hypernym_hyponym, $map_parent_to_children, $int_selected_language);
	}


	if(empty($check_array)){
		return $seen;
	}

	foreach($check_array as $check_loop){
		$masta_japanese_root_id_current = $check_loop['masta_japanese_root_id'];
		$seen = recursive_get_arr_root_parent_child_relations($i, $seen, $parent_or_child, $masta_japanese_root_id_current, $map_parent_to_children, $int_selected_language);
	}
	return $seen;
}


function fetch_all_root_parent_child_relations($int_selected_language, $arr_display_types = []) {

	global
		$int_hypernym_hyponym,
		$int_grammar_outline_status,
		$int_hidden_in_grammar_outline,
		$t_grammar_usage_children,
		$t_grammar_usage_parents,
		$t_grammar_usage_categories,
		$t_masta_japanese_root,
		$t_masta_japanese_sub_category,
		$arr_columns_masta_japanese_root;


	if (empty($arr_display_types)) {
		$arr_display_types = [$int_hypernym_hyponym, $int_grammar_outline_status, $int_hidden_in_grammar_outline];
	}

	$arr_strSQL_select = [
		[$t_grammar_usage_children, 'masta_japanese_root_id as masta_japanese_root_id_child'],
		[$t_grammar_usage_parents, 'masta_japanese_root_id as masta_japanese_root_id_parent'],
		[$t_grammar_usage_parents, 'tier'],
		[$t_grammar_usage_children, 'priority'],
		[$t_grammar_usage_children, 'grammar_outline_status'],
        ['parent_root', $arr_columns_masta_japanese_root[$int_selected_language].' as parent_name'],
        ['child_root', $arr_columns_masta_japanese_root[$int_selected_language].' as child_name'],
        ['parent_root', 'unique_code as parent_unique_code'],
        ['child_root', 'unique_code as child_unique_code'],
		['parent_sub',  'category_id as parent_category_id'],
		['child_sub',  'category_id as child_category_id']
	];

	$strSQL_from = "
        FROM $t_grammar_usage_children
        INNER JOIN $t_grammar_usage_parents
            ON $t_grammar_usage_parents.masta_japanese_root_id = $t_grammar_usage_children.parent_id
        INNER JOIN $t_grammar_usage_categories
            ON $t_grammar_usage_categories.id = $t_grammar_usage_parents.usage_category_id 
        INNER JOIN $t_masta_japanese_root AS parent_root
            ON parent_root.id = $t_grammar_usage_parents.masta_japanese_root_id
        INNER JOIN $t_masta_japanese_root AS child_root
            ON child_root.id = $t_grammar_usage_children.masta_japanese_root_id
		INNER JOIN $t_masta_japanese_sub_category AS parent_sub
            ON parent_sub.id = parent_root.sub_category_id
		INNER JOIN $t_masta_japanese_sub_category AS child_sub
            ON child_sub.id = child_root.sub_category_id
    ";

	$arr_strSQL_where_by_display_types = [];
	foreach ($arr_display_types as $index => $display_type) {
		$logic = ($index === count($arr_display_types) - 1) ? '' : 'Or';
		$arr_strSQL_where_by_display_types[] = [$t_grammar_usage_children, 'grammar_outline_status', '=', $display_type, 'PDO::PARAM_INT', $logic];
	}
	$arr_strSQL_where = [
		[
			$arr_strSQL_where_by_display_types,
			''
		]
	];

	$arr_strSQL_order = [
		[$t_grammar_usage_categories,'sort','ASC'],
		[$t_grammar_usage_parents,'sort','ASC'],
		[$t_grammar_usage_children,'sort','ASC']
	];
	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_results) = execute_select_and_fetch_all(
		$arr_strSQL_select,
		$strSQL_from,
		$arr_strSQL_where,
		$arr_strSQL_order,
		$strSQL_option
	);

	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_results;
}