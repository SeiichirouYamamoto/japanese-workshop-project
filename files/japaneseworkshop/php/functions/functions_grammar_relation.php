<?php


/******************************************************
 *  ITEM
 *  
 ******************************************************/
function build_html_section_grammar_relation(
    $t_masta_japanese_root_id,
    $arr_targets_visible,
    $int_selected_language,
    $t_sets,
    $t_items,
    $section_class_base
) {
	
	global
		$str_grammarOutlineLabelButtonExplanation,
		$str_grammarOutlineLabelButtonExplanationMarker,
		$int_allow_visible_in_grammar_view,
		$int_not_allow_visible_in_grammar_view;

    $str_html = '';

	$arr_labels = [
		[
			'progress' => '進捗',
			'complete' => '完了',
		],
		[
			'progress' => '進度',
			'complete' => '完成',
		],
		[
			'progress' => 'Progress',
			'complete' => 'Complete',
		],
	];

    $arr_sets = get_arr_grammar_relation_sets_for_section(
        $t_sets,
        $t_items,
        $t_masta_japanese_root_id,
        $int_selected_language
    );

    if (!empty($arr_sets)) {
        foreach ($arr_sets as $row) {
            $set_japanese_root_id = $row['masta_japanese_root_id'];
            $alreadyLearned = $row['alreadyLearned'];

            $str_contents = build_html_grammar_view_root(
                $set_japanese_root_id,
                $arr_targets_visible,
                $int_selected_language
            );

            $str_title = $row['title'];
			$str_title = apply_text_for_output($str_title);

            $str_unique_code = $row['unique_code'];
            $str_kana = $row['kana'];
            $int_category_id = $row['category_id'];


			$total_items = intval($row['total_items'] ?? 0);
			$learned_items = intval($row['learned_items'] ?? 0);

			$str_title_status = '';

			if ($total_items > 0) {
				$label_key = ($learned_items >= $total_items) ? 'complete' : 'progress';
				$label = $arr_labels[$int_selected_language][$label_key];

				// $str_title_status = $label . ' ' . $learned_items . '/' . $total_items;
				$str_title_status = '(' . $label . ' ' . $learned_items . '/' . $total_items . ')';
			}

			$str_heading = $str_title . ' ' . $str_title_status;
			
			$str_heading = '<h3>' . $str_heading . '</h3>';

            $str_explanation_button =
                '<button class="grammarOutlineLabelButton grammarOutlineLabelButtonExplanation" ' .
                'data-japanese-id="' . $set_japanese_root_id . '" ' .
                'data-unique-code="' . $str_unique_code . '" ' .
                'data-japanese="' . $str_title . '" ' .
                'data-kana="' . $str_kana . '" ' .
                'data-category-id="' . $int_category_id . '" ' .
                'title="' . $str_grammarOutlineLabelButtonExplanation[$int_selected_language] . '">' .
                $str_grammarOutlineLabelButtonExplanationMarker[$int_selected_language] .
                '</button>';

            $str_hide_button = $alreadyLearned ? '' : '<button class="' . $section_class_base . 'HideButton sectionGrammarRelationHideButton">hide</button>';

            $str_buttons_container = '';
			if (
				($arr_targets_visible['grammar_set_buttons_visible'] ?? $int_not_allow_visible_in_grammar_view)
					=== $int_allow_visible_in_grammar_view
			) {
                $str_buttons_container =
                    '<div class="' . $section_class_base . 'ButtonsContainer sectionGrammarRelationButtonsContainer">' .
                        $str_explanation_button .
                        $str_hide_button .
                    '</div>';
            }

            $section_class = $alreadyLearned
                ? $section_class_base . 'AlreadyLearned sectionGrammarRelationAlreadyLearned sectionStandard'
                : $section_class_base . ' sectionGrammarRelation sectionStandard';

			$str_add = '<section class="' . $section_class . '">' . $str_heading . $str_buttons_container . $str_contents . '</section>';


            $str_html .= $str_add;
        }
    }

    return $str_html;

}



function get_arr_grammar_relation_sets_for_section($t_relation_sets, $t_relation_items, $t_masta_japanese_root_id, $int_selected_language) {

	global
		$str_snake_to_camel_unique_code;

	$rows = get_arr_relation_set_summaries($t_relation_sets, $t_relation_items, $t_masta_japanese_root_id, $int_selected_language);
	if (empty($rows)) {
		return [];
	}

	$arr = [];
	foreach ($rows as $r) {
		$total_items = intval($r['total_items']);
		$learned_items = intval($r['learned_items']);
		$already = ($total_items > 0 && $total_items === $learned_items) ? FLAG_TRUE : FLAG_FALSE;

		$title_sel_clean = apply_text_for_output($r['title_sel']);
		$title_jpn_clean = apply_text_for_output($r['title_jpn']);

		$arr[] = [
			'masta_japanese_root_id' => intval($r['set_root_id']),
			'title' => $title_sel_clean,
			'alreadyLearned' => $already,
			'unique_code' => $r[$str_snake_to_camel_unique_code],
			'kana' => $r['title_kana'],
			'category_id' => intval($r['category_id']),
			'total_items' => $total_items,
			'learned_items' => $learned_items
		];
	}
	return $arr;
}


function get_arr_grammar_relation_sets_for_tree($t_relation_sets, $t_relation_items, $int_selected_language, $requireAllLearned = true) {
	
	global
		$str_sql_where_is_in,
		$t_masta_japanese_root,
		$str_snake_to_camel_unique_code,
		$arr_columns_masta_japanese_root,
		$int_used_language_jpn,
		$str_column_root_kana,
		$t_masta_japanese_sub_category;

	$rows = get_arr_relation_set_summaries($t_relation_sets, $t_relation_items, null, $int_selected_language);
	if (empty($rows)) {
		return [];
	}

	$included_set_ids = [];
	$set_meta = [];
	foreach ($rows as $s) {
		$total = intval($s['total_items']);
		$learned = intval($s['learned_items']);
		$already = ($total > 0 && $total === $learned);
		if ($requireAllLearned) {
			if ($already) {
				$sid = intval($s['id']);
				$included_set_ids[] = $sid;
				$set_meta[$sid] = [
					'masta_japanese_root_id' => intval($s['set_root_id']),
					'title' => apply_text_for_output($s['title_sel'])
				];
			}
		} else {
			if ($learned > 0) {
				$sid = intval($s['id']);
				$included_set_ids[] = $sid;
				$set_meta[$sid] = [
					'masta_japanese_root_id' => intval($s['set_root_id']),
					'title' => apply_text_for_output($s['title_sel'])
				];
			}
		}
	}
	if (empty($included_set_ids)) {
		return [];
	}

	$arr_strSQL_select = [
		[$t_relation_items, 'set_id'],
		[$t_relation_items, 'masta_japanese_root_id']
	];
	$strSQL_from = ' FROM '.$t_relation_items;

	$arr_where_inner = [
		[$t_relation_items, 'set_id', $str_sql_where_is_in, $included_set_ids, 'PDO::PARAM_INT', '']
	];

	if (!$requireAllLearned) {
		$learned_ids = get_arr_learned_ids($int_selected_language);
		if (empty($learned_ids)) {
			return [];
		}
		array_unshift(
			$arr_where_inner,
			[$t_relation_items, 'masta_japanese_root_id', $str_sql_where_is_in, $learned_ids, 'PDO::PARAM_INT', 'And']
		);
	}

	$arr_strSQL_where = [[$arr_where_inner, '']];
	$arr_strSQL_order = [];
	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_items) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	$root_ids_needed = [];
	$set_to_root_ids = [];
	foreach ($arr_items as $row) {
		$sid = intval($row['set_id']);
		$rid = intval($row['masta_japanese_root_id']);
		$set_to_root_ids[$sid][] = $rid;
		$root_ids_needed[$rid] = true;
	}
	if (empty($root_ids_needed)) {
		return [];
	}

	$root_ids = array_map('intval', array_keys($root_ids_needed));
	$arr_strSQL_select = [
		[$t_masta_japanese_root, 'id'],
		[$t_masta_japanese_root, 'id as masta_japanese_root_id'],
		[$t_masta_japanese_root, 'root_example'],
		[$t_masta_japanese_root, 'unique_code as '.$str_snake_to_camel_unique_code],
		[$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_used_language_jpn]],
		[$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language]],
		[$t_masta_japanese_root, $str_column_root_kana],
		[$t_masta_japanese_sub_category, 'category_id']
	];
	$strSQL_from = " FROM $t_masta_japanese_root
		INNER JOIN $t_masta_japanese_sub_category ON $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id";
	$arr_strSQL_where = [
		[
			[
				[$t_masta_japanese_root, 'id', $str_sql_where_is_in, $root_ids, 'PDO::PARAM_INT', '']
			],
			''
		]
	];
	$arr_strSQL_order = [];
	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_roots) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	$root_map = [];
	foreach ($arr_roots as $r) {
		$root_map[intval($r['masta_japanese_root_id'])] = $r;
	}

	$arr_matched = [];
	foreach ($set_to_root_ids as $sid => $ids) {
		if (!isset($set_meta[$sid])) {
			continue;
		}
		$set_root_id = $set_meta[$sid]['masta_japanese_root_id'];
		$title = $set_meta[$sid]['title'];
		if (!isset($arr_matched[$set_root_id])) {
			$arr_matched[$set_root_id] = ['title' => $title, 'array' => []];
		}
		foreach ($ids as $rid) {
			if (isset($root_map[$rid])) {
				$arr_matched[$set_root_id]['array'][] = $root_map[$rid];
			}
		}
	}
	return $arr_matched;
}


function get_arr_relation_set_summaries($t_relation_sets, $t_relation_items, $seed_root_id, $int_selected_language) {

	global
		$t_masta_japanese_root,
		$str_snake_to_camel_unique_code,
		$arr_columns_masta_japanese_root,
		$int_used_language_jpn,
		$str_column_root_kana,
		$t_masta_japanese_sub_category,
		$str_sql_where_is_in;

    $base_select = [
        [$t_relation_sets, 'id'],
        [$t_relation_sets, 'masta_japanese_root_id as set_root_id'],
        [$t_masta_japanese_root, 'unique_code as ' . $str_snake_to_camel_unique_code],
        [$t_masta_japanese_root, 'root_example'],
        [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_used_language_jpn] . ' as title_jpn'],
        [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language] . ' as title_sel'],
        [$t_masta_japanese_root, $str_column_root_kana . ' as title_kana'],
        [$t_masta_japanese_sub_category, 'category_id'],
    ];
    $from = " FROM $t_relation_sets
        INNER JOIN $t_relation_items ON $t_relation_sets.id = $t_relation_items.set_id
        INNER JOIN $t_masta_japanese_root ON $t_masta_japanese_root.id = $t_relation_sets.masta_japanese_root_id
        INNER JOIN $t_masta_japanese_sub_category ON $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id";

    if ($seed_root_id !== null) {
        $base_where = [
            [
                [
                    [$t_relation_items, 'masta_japanese_root_id', '=', intval($seed_root_id), 'PDO::PARAM_INT', '']
                ],
                ''
            ]
        ];
    } else {
        $base_where = [];
    }
    $order = [];
    $option = ' GROUP BY ' . $t_relation_sets . '.id';

    list($pdo_has_error, $select_has_error, $e, $base_rows) = execute_select_and_fetch_all($base_select, $from, $base_where, $order, $option);
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    if (empty($base_rows)) {
        return [];
    }

    $set_ids = array_map(fn($r) => intval($r['id']), $base_rows);
    $set_ids = array_values(array_unique($set_ids));

    $total_select = [
        [$t_relation_items, 'set_id'],
        ['', 'COUNT(*) as total_items']
    ];
    $total_from = " FROM $t_relation_items";
    $total_where_inner = [
        [$t_relation_items, 'set_id', $str_sql_where_is_in, $set_ids, 'PDO::PARAM_INT', '']
    ];
    $total_where = [[$total_where_inner, '']];
    $total_option = ' GROUP BY ' . $t_relation_items . '.set_id';

    list($pdo_has_error, $select_has_error, $e, $total_rows) = execute_select_and_fetch_all($total_select, $total_from, $total_where, $order, $total_option);
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    $total_map = [];
    if (!empty($total_rows)) {
        foreach ($total_rows as $tr) {
            $total_map[intval($tr['set_id'])] = intval($tr['total_items']);
        }
    }

    $learned_ids = get_arr_learned_ids($int_selected_language);
    $learned_map = [];

    if (!empty($learned_ids)) {
        $learned_select = [
            [$t_relation_items, 'set_id'],
            ['', 'COUNT(*) as learned_items']
        ];
        $learned_from = " FROM $t_relation_items";
        $learned_where_inner = [
            [$t_relation_items, 'set_id', $str_sql_where_is_in, $set_ids, 'PDO::PARAM_INT', 'And'],
            [$t_relation_items, 'masta_japanese_root_id', $str_sql_where_is_in, $learned_ids, 'PDO::PARAM_INT', '']
        ];
        $learned_where = [[$learned_where_inner, '']];
        $learned_option = ' GROUP BY ' . $t_relation_items . '.set_id';

        list($pdo_has_error, $select_has_error, $e, $learned_rows) = execute_select_and_fetch_all($learned_select, $learned_from, $learned_where, $order, $learned_option);
        handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

        if (!empty($learned_rows)) {
            foreach ($learned_rows as $lr) {
                $learned_map[intval($lr['set_id'])] = intval($lr['learned_items']);
            }
        }
    }

    foreach ($base_rows as &$r) {
        $sid = intval($r['id']);
        $r['total_items'] = $total_map[$sid] ?? 0;
        $r['learned_items'] = $learned_map[$sid] ?? 0;
    }
    unset($r);

    return $base_rows;
}