<?php

/******************************************************
 *  PAGE
 ******************************************************/

function build_html_wise_navigation_whiteboard_panel_ui_contents($int_selected_language){

    return [
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

function build_html_wise_navigation_whiteboard_panel_body($int_selected_language){

	return build_html_wise_whiteboard_body($int_selected_language);
	
}

function build_html_wise_navigation_map_panel_body($int_selected_language){

    return build_html_wise_map_screen(true, $int_selected_language);
}

function build_html_wise_navigation_whiteboard_panel($int_selected_language){

    return build_html_wise_panel(
        'wisePanelWhiteboard',
        'wisePanel-whiteboard',
        build_html_wise_navigation_whiteboard_panel_body($int_selected_language),
        build_html_wise_navigation_whiteboard_panel_ui_contents($int_selected_language)
    );
}

function build_html_wise_navigation_map_panel($int_selected_language){

    return build_html_wise_panel(
        'wisePanelMap',
        'wisePanel-map',
        build_html_wise_navigation_map_panel_body($int_selected_language),
        ''
    );
}

function build_arr_wise_navigation_main_panels($int_selected_language){

    return [
        build_html_wise_navigation_whiteboard_panel($int_selected_language),
        build_html_wise_navigation_map_panel($int_selected_language),
    ];
}


function build_html_wise_navigation_hud_contents($arr_visible_right_buttons, $int_selected_language){

    return [
        build_html_wise_banner_advertisement(false, $int_selected_language),
    ];
}

function build_html_wise_navigation_super_overlay_contents($str_opening, $int_selected_language){

    return [
        build_html_wise_start_overlay(
            $int_selected_language,
            'opening',
            'SCANNING...',
            [
                'user_opening_message' => $str_opening,
                'topic_message' => $str_opening
            ]
        ),
        build_html_wise_ui_lock_overlay()
    ];
}

function build_html_wise_navigation_page($t_wise_navigation_id, $int_selected_language){

    global
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

    $arr_wise_navigations = fetch_arr_wise_navigation_from_wise_navigation_id(
        $t_wise_navigation_id,
        $int_selected_language
    );

    if (empty($arr_wise_navigations)) {
        return '';
    }

    $str_opening = $arr_wise_navigations[INDEX_FIRST]['title'];

    $arr_visible_right_buttons = [
        $str_wiseRightVerticalToolbarButton_id_wiseSetup => false,
        $str_wiseRightVerticalToolbarButton_id_grammarExplanation => false,
        $str_wiseRightVerticalToolbarButton_id_memoPad => false,
        $str_wiseRightVerticalToolbarButton_id_chart => false,
        $str_wiseRightVerticalToolbarButton_id_quiz => false,
        $str_wiseRightVerticalToolbarButton_id_map => false,
        $str_wiseRightVerticalToolbarButton_id_imageViewer => false,
        $str_wiseRightVerticalToolbarButton_id_lessonContents => false,
        $str_wiseRightVerticalToolbarButton_id_grammarInsights => false,
        $str_wiseRightVerticalToolbarButton_id_sharedContentsUi => false,
    ];

    $arr_panels = [
        build_html_wise_navigation_whiteboard_panel($int_selected_language),
        build_html_wise_navigation_map_panel($int_selected_language),
    ];

    $str_panel_container_layer = build_html_wise_panel_container_layer(
        $arr_panels
    );

    $str_panel_overlay_layer = '';

    $str_hud_layer = build_html_wise_hud_layer(
        build_html_wise_navigation_hud_contents($arr_visible_right_buttons, $int_selected_language)
    );

	
    $str_super_overlay_layer = build_html_wise_super_overlay_layer(
        build_html_wise_super_overlay_bundle(
            build_html_wise_navigation_super_overlay_contents($str_opening, $int_selected_language)
        )
    );

    return build_html_wise_page_shell(
        'sectionWise',
        [
			$str_panel_container_layer,
			$str_panel_overlay_layer,
			$str_hud_layer,
			$str_super_overlay_layer
		]
    );
}


function build_html_select_wise_navigation_page($int_registered_sentence_id, $int_selected_language){
	
	global
		$t_wise_navigations,
		$path_wise_navigation,
		$arr_str_button_caption_submit;

    $str_html = '';
	
	$url_wise_navigation = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_wise_navigation, '/'))
	);

    $arr_strSQL_select = [
        [$t_wise_navigations, 'id'],
        [$t_wise_navigations, 'unique_code'],
        [$t_wise_navigations, 'title'],
        [$t_wise_navigations, 'is_published'],
        [$t_wise_navigations, 'sort']
    ];

    $strSQL_from = ' FROM ' . $t_wise_navigations;

    $arr_strSQL_where = [
        [
            [
                [$t_wise_navigations, 'registered_sentence_id', '=', $int_registered_sentence_id, 'PDO::PARAM_INT', 'And'],
                [$t_wise_navigations, 'is_published', '=', FLAG_TRUE, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_wise_navigations, 'sort', 'ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_wise_navigations) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    if (empty($arr_wise_navigations)) {
        return '<ul class="select-wise-navigation"></ul>';
    }

    $str_html .= '<ul class="select-wise-navigation">';

    foreach ($arr_wise_navigations as $row) {
        $title = isset($row['title']) ? $row['title'] : '';
        $unique_code = isset($row['unique_code']) ? $row['unique_code'] : '';

        $str_html .=
            '<li class="select-wise-navigation-item">' .
                '<div class="select-wise-navigation-title">' . escape_html($title) . '</div>' .
                '<form class="select-wise-navigation-form" action="' . escape_html($url_wise_navigation) . '" method="GET" target="_blank" rel="noopener">' .
                    '<input type="hidden" name="unique_code" value="' . escape_html($unique_code) . '">' .
                    '<input class="select-wise-navigation-submit" type="submit" value="' . escape_html($arr_str_button_caption_submit[$int_selected_language]) . '">' .
                '</form>' .
            '</li>';
    }

    $str_html .= '</ul>';

    return $str_html;
}


function build_html_manage_wise_navigations_page($sentence_unique_code, $int_selected_language){
	
	global
		$t_wise_navigations,
		$arr_str_placeholder_wise_navigation_title,
		$str_snake_to_camel_wise_navigation_unique_code,
		$path_manage_wise_navigation_waypoints,
		$path_check_wise_navigation_sequence;

	$str_html = '';
	
	$url_manage_wise_navigation_waypoints = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_manage_wise_navigation_waypoints, '/'))
	);

	$url_check_wise_navigation_sequence = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_check_wise_navigation_sequence, '/'))
	);

	$int_registered_sentence_id = fetch_registered_sentence_id_from_unique_code($sentence_unique_code, $int_selected_language);

	$arr_strSQL_select = [
		[$t_wise_navigations,'id'],
		[$t_wise_navigations,'unique_code as ' . $str_snake_to_camel_wise_navigation_unique_code],
		[$t_wise_navigations,'title'],
		[$t_wise_navigations,'is_published'],
		[$t_wise_navigations,'sort']
	];
	
	$strSQL_from = ' FROM ' .$t_wise_navigations;
	
	$arr_strSQL_where = [
		[
			[
				[$t_wise_navigations,'registered_sentence_id','=',$int_registered_sentence_id,'PDO::PARAM_INT','']
			],
			''
		]
	];
	
	$arr_strSQL_order = [
		[$t_wise_navigations, 'sort', 'ASC']
	];
	
	$strSQL_option = '';
	
	list($pdo_has_error, $select_has_error, $e, $arr_wise_navigations) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);
	
	$manage_target = 'wise_navigation';
    $target_unique_code = $str_snake_to_camel_wise_navigation_unique_code;
    $target_title = 'title';
    $target_address = $url_manage_wise_navigation_waypoints;
    $target_placeholder = $arr_str_placeholder_wise_navigation_title[$int_selected_language];

    $options = [
        'prefix' => 'naviContents',
        'create_input_name' => 'navigationTitle',
        'submit_button_id' => 'naviCreateNewButton',
        'open_next_in_blank' => true,
        'check_sequence_address' => $url_check_wise_navigation_sequence,

        'unique_code_input_name' => 'wise_navigation_unique_code',
        'unique_code_data_attr' => 'data-wise-navigation-unique-code',
        'common_unique_code_data_attr' => 'data-target-unique-code',
        'unique_code_param' => 'wise_navigation_unique_code'
    ];

    $str_html = build_html_manage_targets(
        $manage_target,
        $arr_wise_navigations,
        $target_unique_code,
        $target_title,
        $target_address,
        $target_placeholder,
        $int_selected_language,
        $options
    );

    return $str_html;

}


function build_html_manage_wise_navigation_waypoints_page($unique_code, $int_selected_language){
	
	global
		$t_wise_navigation_waypoints,
		$path_manage_wise_navigation_scripts,
		$str_snake_to_camel_wise_navigation_waypoint_unique_code,
		$arr_str_placeholder_wise_navigation_title;

    $str_html = '';
	
	$url_manage_wise_navigation_scripts = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_manage_wise_navigation_scripts, '/'))
	);

    $int_wise_navigation_id = fetch_wise_navigation_id_from_unique_code($unique_code, $int_selected_language);

    $arr_strSQL_select = [
        [$t_wise_navigation_waypoints, 'id'],
        [$t_wise_navigation_waypoints, 'unique_code as ' . $str_snake_to_camel_wise_navigation_waypoint_unique_code],
        [$t_wise_navigation_waypoints, 'wise_navigation_id'],
        [$t_wise_navigation_waypoints, 'title'],
        [$t_wise_navigation_waypoints, 'sort']
    ];

    $strSQL_from = ' FROM ' . $t_wise_navigation_waypoints;

    $arr_strSQL_where = [
        [
            [
                [$t_wise_navigation_waypoints, 'wise_navigation_id', '=', $int_wise_navigation_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_wise_navigation_waypoints, 'sort', 'ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_wise_navigation_waypoints) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    $manage_target = 'wise_navigation_waypoint';
    $target_unique_code = $str_snake_to_camel_wise_navigation_waypoint_unique_code;
    $target_title = 'title';
    $target_address = $url_manage_wise_navigation_scripts;
    $target_placeholder = $arr_str_placeholder_wise_navigation_title[$int_selected_language];

    $options = [
        'prefix' => 'naviContents',
        'create_input_name' => 'waypointTitle',
        'submit_button_id' => 'naviCreateNewButton',
        'open_next_in_blank' => true,

        'unique_code_input_name' => 'wise_navigation_waypoint_unique_code',
        'unique_code_data_attr' => 'data-wise-navigation-waypoint-unique-code',
        'common_unique_code_data_attr' => 'data-target-unique-code',
        'unique_code_param' => 'wise_navigation_unique_code'
    ];

    $str_html = build_html_manage_targets(
        $manage_target,
        $arr_wise_navigation_waypoints,
        $target_unique_code,
        $target_title,
        $target_address,
        $target_placeholder,
        $int_selected_language,
        $options
    );

    return $str_html;
}


function build_html_manage_wise_navigation_scripts_page($unique_code, $int_selected_language){

	global
		$t_wise_navigation_scripts,
		$t_masta_wise_navigation_script,
		$str_snake_to_camel_wise_navigation_script_unique_code,
		$arr_columns_wise_navigation_script_message,
		$path_manage_wise_navigation_items,
		$arr_str_placeholder_wise_navigation_script_select;

    $str_html = '';
	
	$url_manage_wise_navigation_items = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_manage_wise_navigation_items, '/'))
	);

    $int_wise_navigation_waypoint_id = fetch_wise_navigation_waypoint_id_from_unique_code($unique_code, $int_selected_language);

    $arr_strSQL_select = [
        [$t_wise_navigation_scripts, 'id'],
        [$t_wise_navigation_scripts, 'unique_code as ' . $str_snake_to_camel_wise_navigation_script_unique_code],
        [$t_wise_navigation_scripts, 'wise_navigation_waypoint_id'],
        [$t_wise_navigation_scripts, 'script_type_id'],
        [$t_wise_navigation_scripts, 'sort'],
        [$t_masta_wise_navigation_script, 'script_key']
    ];

    foreach ($arr_columns_wise_navigation_script_message as $col_name) {
        $arr_strSQL_select[] = [$t_wise_navigation_scripts, $col_name];
    }

    $strSQL_from = " FROM
        $t_wise_navigation_scripts
        INNER JOIN $t_masta_wise_navigation_script
        ON $t_wise_navigation_scripts.script_type_id = $t_masta_wise_navigation_script.id";

    $arr_strSQL_where = [
        [
            [
                [$t_wise_navigation_scripts, 'wise_navigation_waypoint_id', '=', $int_wise_navigation_waypoint_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_wise_navigation_scripts, 'sort', 'ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_wise_navigation_scripts) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    $manage_target = 'wise_navigation_script';
    $target_unique_code = $str_snake_to_camel_wise_navigation_script_unique_code;
    $target_title = 'script_key';
    $target_address = $url_manage_wise_navigation_items;
    $target_placeholder = '';

    $options = [
        'prefix' => 'naviContents',
        'create_input_name' => 'script_type_id',
        'submit_button_id' => 'naviCreateNewButton',
        'open_next_in_blank' => true,

        'unique_code_input_name' => 'wise_navigation_script_unique_code',
        'unique_code_data_attr' => 'data-wise-navigation-script-unique-code',
        'common_unique_code_data_attr' => 'data-target-unique-code',
        'unique_code_param' => 'wise_navigation_waypoint_unique_code',

        'extra_edit_field_keys' => $arr_columns_wise_navigation_script_message,
        'extra_edit_field_labels' => $arr_columns_wise_navigation_script_message,
        'extra_edit_field_input' => 'textarea'
    ];

    $str_html = build_html_manage_targets(
        $manage_target,
        $arr_wise_navigation_scripts,
        $target_unique_code,
        $target_title,
        $target_address,
        $target_placeholder,
        $int_selected_language,
        $options
    );

    return $str_html;
}


function build_html_manage_wise_navigation_items_page($unique_code, $int_selected_language){

	global
		$t_wise_navigations,
		$t_wise_navigation_waypoints,
		$t_wise_navigation_scripts,
		$t_masta_wise_navigation_script;

	$str_html = '';

	$wise_navigation_script_id = fetch_wise_navigation_script_id_from_unique_code($unique_code, $int_selected_language);

	$arr_strSQL_select = [
		[$t_wise_navigations,'registered_sentence_id'],
		[$t_wise_navigations,'title as wise_navigations_title'],
		[$t_wise_navigation_waypoints,'title as wise_navigation_waypoints_title'],
		[$t_masta_wise_navigation_script,'script_key']
	];

	$strSQL_from = " FROM
					(
						(
							$t_wise_navigations
							INNER JOIN $t_wise_navigation_waypoints
							ON
							$t_wise_navigations.id = $t_wise_navigation_waypoints.wise_navigation_id
						)
						INNER JOIN $t_wise_navigation_scripts
						ON
						$t_wise_navigation_waypoints.id = $t_wise_navigation_scripts.wise_navigation_waypoint_id
					)
					INNER JOIN $t_masta_wise_navigation_script
					ON
					$t_wise_navigation_scripts.script_type_id = $t_masta_wise_navigation_script.id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_wise_navigation_scripts,'id','=',$wise_navigation_script_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_wise_navigation_waypoints,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_header) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);
	
	if (empty($arr_header)) {
		return '';
	}

	$registered_sentence_id = $arr_header[INDEX_FIRST]['registered_sentence_id'];
	$nav_title = $arr_header[INDEX_FIRST]['wise_navigations_title'];
	$waypoint_title = $arr_header[INDEX_FIRST]['wise_navigation_waypoints_title'];
	$script_key = $arr_header[INDEX_FIRST]['script_key'];

	$str_html_header = '<h1>'.$nav_title.' '.$waypoint_title.' '.$script_key.'</h1>';
	
	$str_html_add_contents = build_html_navi_items_add_contents($registered_sentence_id, $int_selected_language);
	$str_html_selected_contents = build_html_navi_items_selected_contents($int_selected_language);
	
	$str_html_body = '<div id="naviItemsBody" class="wise-require-fullscreen">'.$str_html_header.$str_html_add_contents.$str_html_selected_contents.'</div>';
	$str_html = '<section>'.$str_html_body.'</section>';

	return $str_html;

}


function build_html_check_wise_navigation_sequence_page($unique_code, $int_selected_language){
	
	global
		$path_manage_wise_navigation_waypoints,
		$path_manage_wise_navigation_scripts,
		$path_manage_wise_navigation_items,
		$int_used_language_jpn,
		$t_masta_wise_navigation_script,
		$int_masta_script_type_id_message_explanation_for_particle,
		$int_masta_script_type_id_message_explanation_for_grammar,
		$int_masta_script_type_id_message_explanation_for_inflection,
		$int_used_language_cht,
		$str_sql_where_is_in,
		$t_wise_navigations,
		$t_wise_navigation_waypoints,
		$t_wise_navigation_scripts,
		$t_wise_navigation_items,
		$t_layers,
		$t_masta_japanese_root,
		$arr_columns_masta_japanese_root,
		$arr_wise_navigation_messages,
		$str_class_fixed_font,
		$int_masta_script_type_id_message_free,
		$int_masta_script_type_id_layer_for_content,
		$int_masta_script_type_id_layer_for_goal,
		$int_masta_script_type_id_message_explanation_for_sentence,
		$int_masta_script_type_id_reset_goal;
	
	$url_manage_wise_navigation_waypoints = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_manage_wise_navigation_waypoints, '/'))
	);

	$url_manage_wise_navigation_scripts = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_manage_wise_navigation_scripts, '/'))
	);

	$url_manage_wise_navigation_items = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_manage_wise_navigation_items, '/'))
	);


    $h1 = '<h1>W.I.S.E. NAVIGATION SEQUENCE</h1>';

    $t_wise_navigation_id = fetch_wise_navigation_id_from_unique_code($unique_code, $int_selected_language);
    if (!is_int($t_wise_navigation_id) || $t_wise_navigation_id <= 0) {
		$inner = $h1 . '<h2>wise_navigations title</h2><p>navigation not found</p>';
		return '<section class="sectionStandard">' . $inner . '</section>';
    }

    $rows_nav = get_arr_wise_navigations($t_wise_navigation_id, $int_selected_language);
    $nav_title = (is_array($rows_nav) && !empty($rows_nav)) ? strval($rows_nav[0]['title']) : '';
    $sentence_for_replace = $nav_title;

    $h2 = '<h2>' . escape_html($nav_title ?: 'wise_navigations title') . '</h2>';

    $arr_waypoints = get_arr_wise_navigation_waypoints($t_wise_navigation_id, $int_selected_language);
    if (!is_array($arr_waypoints) || count($arr_waypoints) === 0) {
		$inner = $h1 . $h2 . '<p>no waypoints</p>';
		return '<section class="sectionStandard">' . $inner . '</section>';
    }

    $arr_strSQL_select = [
        [$t_masta_wise_navigation_script, 'id'],
        [$t_masta_wise_navigation_script, 'script_key']
    ];
    $strSQL_from = ' FROM ' . $t_masta_wise_navigation_script;
    $arr_strSQL_where = [];
    $arr_strSQL_order = [
        [$t_masta_wise_navigation_script, 'id', 'ASC']
    ];
    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $rows_masta) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    $map_type_to_key = [];
    if (is_array($rows_masta)) {
        foreach ($rows_masta as $r) {
            $map_type_to_key[intval($r['id'])] = strval($r['script_key']);
        }
    }

    $thead = '<thead><tr>'
           . '<th>waypoint</th>'
           . '<th>script type</th>'
           . '<th>message jpn</th>'
           . '<th>message cht</th>'
           . '</tr></thead>';

    $tbody_groups_html = '';

	$message_explanations = [
		$int_masta_script_type_id_message_explanation_for_particle,
		$int_masta_script_type_id_message_explanation_for_grammar,
		$int_masta_script_type_id_message_explanation_for_inflection
	];

	$get_summary_grammar_lists = function (int $t_wise_navigation_id) 
    use (
        $t_wise_navigations, $t_wise_navigation_waypoints, $t_wise_navigation_scripts,
        $t_wise_navigation_items, $t_layers, $t_masta_japanese_root, $arr_columns_masta_japanese_root,
        $int_used_language_jpn, $int_used_language_cht, $message_explanations, $str_sql_where_is_in
    ) {

		$build = function (int $int_selected_language) 
		use (
			$t_wise_navigations, $t_wise_navigation_waypoints, $t_wise_navigation_scripts,
			$t_wise_navigation_items, $t_layers, $t_masta_japanese_root, $arr_columns_masta_japanese_root,
			$t_wise_navigation_id, $message_explanations, $str_sql_where_is_in
		) {

            $arr_strSQL_select = [
                [$t_masta_japanese_root, 'id'],
                [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language]]
            ];
            $strSQL_from = "
                FROM $t_wise_navigations
                INNER JOIN $t_wise_navigation_waypoints
                    ON $t_wise_navigations.id = $t_wise_navigation_waypoints.wise_navigation_id
                INNER JOIN $t_wise_navigation_scripts
                    ON $t_wise_navigation_waypoints.id = $t_wise_navigation_scripts.wise_navigation_waypoint_id
                INNER JOIN $t_wise_navigation_items
                    ON $t_wise_navigation_scripts.id = $t_wise_navigation_items.wise_navigation_script_id
                LEFT JOIN $t_layers
                    ON $t_layers.id = $t_wise_navigation_items.layer_id
                LEFT JOIN $t_masta_japanese_root
                    ON $t_masta_japanese_root.id = $t_layers.masta_japanese_root_id
            ";
            $arr_strSQL_where = [
                [
                    [
                        [$t_wise_navigations, 'id', '=', $t_wise_navigation_id, 'PDO::PARAM_INT', 'And'],
						[$t_wise_navigation_scripts, 'script_type_id', $str_sql_where_is_in, $message_explanations, 'PDO::PARAM_INT', '']
                    ],
                    ''
                ]
            ];
            $arr_strSQL_order = [
                [$t_wise_navigations, 'sort', 'ASC'],
                [$t_wise_navigation_waypoints, 'sort', 'ASC'],
                [$t_wise_navigation_scripts, 'sort', 'ASC'],
                [$t_wise_navigation_items, 'sort', 'ASC']
            ];
            $strSQL_option = '';

            list($pdo_has_error, $select_has_error, $e, $rows) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
            handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

            $col_title = $arr_columns_masta_japanese_root[$int_selected_language];
            $seen = [];
            $out = [];

            if (is_array($rows) && count($rows) > 0) {
                foreach ($rows as $r) {
                    $root_id = intval($r['id'] ?? 0);
                    if ($root_id <= 0) continue;
                    if (isset($seen[$root_id])) continue;
                    $seen[$root_id] = 1;
                    $title = isset($r[$col_title]) ? strval($r[$col_title]) : '';
                    if ($title === '') continue;
                    $out[] = $title;
                }
            }
            return $out;
        };

        return [
            'jpn' => $build($int_used_language_jpn),
            'cht' => $build($int_used_language_cht)
        ];
    };

    $render_message_group = function(string $wpTitle, string $groupKey, string $typeLabel)
        use (&$tbody_groups_html,
             $arr_wise_navigation_messages,
             $sentence_for_replace,
             $str_class_fixed_font,
             $get_summary_grammar_lists,
             $t_wise_navigation_id) {

        $msgs_jpn = isset($arr_wise_navigation_messages[INDEX_FIRST][$groupKey]) ? $arr_wise_navigation_messages[INDEX_FIRST][$groupKey] : [];
        $msgs_cht = isset($arr_wise_navigation_messages[INDEX_SECOND][$groupKey]) ? $arr_wise_navigation_messages[INDEX_SECOND][$groupKey] : [];

        $msgs_jpn = array_map(function($m) use ($sentence_for_replace){
            return apply_remove_original_tags(strtr($m, ['{sentence}' => $sentence_for_replace]));
        }, $msgs_jpn);
        $msgs_cht = array_map(function($m) use ($sentence_for_replace){
            return apply_remove_original_tags(strtr($m, ['{sentence}' => $sentence_for_replace]));
        }, $msgs_cht);

        $grammar_rows_html = '';
        if ($groupKey === 'summary') {
            $lists = $get_summary_grammar_lists($t_wise_navigation_id);
            $gl_jpn = isset($lists['jpn']) && is_array($lists['jpn']) ? $lists['jpn'] : [];
            $gl_cht = isset($lists['cht']) && is_array($lists['cht']) ? $lists['cht'] : [];

            $row_count_gl = max(count($gl_jpn), count($gl_cht));
            for ($i = 0; $i < $row_count_gl; $i++) {
                $gj = isset($gl_jpn[$i]) ? $gl_jpn[$i] : '';
                $gc = isset($gl_cht[$i]) ? $gl_cht[$i] : $gj;
                $grammar_rows_html .= '<tr>'
                    . '<td class="scriptTypeCell">SUMMARY</td>'
                    . '<td class="' . $str_class_fixed_font . '">' . escape_html($gj) . '</td>'
                    . '<td class="' . $str_class_fixed_font . '">' . escape_html($gc) . '</td>'
                    . '</tr>';
            }
        }

        $msg_rows_html = '';
        $row_count_msg = max(count($msgs_jpn), count($msgs_cht));
        for ($i = 0; $i < $row_count_msg; $i++) {
            $mj = isset($msgs_jpn[$i]) ? $msgs_jpn[$i] : '';
            $mc = isset($msgs_cht[$i]) ? $msgs_cht[$i] : '';
            $msg_rows_html .= '<tr>'
			. '<td class="scriptTypeCell">' . escape_html($typeLabel) . '</td>'
			. '<td class="' . $str_class_fixed_font . '">' . escape_html_with_nl2br($mj) . '</td>'
			. '<td class="' . $str_class_fixed_font . '">' . escape_html_with_nl2br($mc) . '</td>'
			. '</tr>';
        }

        $total_rows = substr_count($grammar_rows_html, '<tr>') + substr_count($msg_rows_html, '<tr>');
        if ($total_rows <= 0) return;

        $all_rows_html = $grammar_rows_html . $msg_rows_html;
        $all_rows_html = preg_replace('/^<tr>/', '<tr><td class="wpTitleCell" rowspan="' . escape_html($total_rows) . '">' . escape_html($wpTitle) . '</td>', $all_rows_html, 1);

        $tbody_groups_html .= '<tbody class="wpGroup">' . $all_rows_html . '</tbody>';
    };

    $render_message_group('opening', 'opening', 'MSG');

    foreach ($arr_waypoints as $wpi => $wp) {
        $waypoint_id = isset($wp['id']) ? intval($wp['id']) : 0;
        $waypoint_title = isset($wp['title']) ? strval($wp['title']) : '';
        $waypoint_uc = isset($wp['unique_code']) ? strval($wp['unique_code']) : '';

        $arr_scripts_base = ($waypoint_id > 0) ? get_arr_wise_navigation_scripts($waypoint_id, $int_selected_language) : [];
        $count_scripts = is_array($arr_scripts_base) ? count($arr_scripts_base) : 0;

        $rows_html = '';
        $printed_waypoint_cell = false;

        if ($count_scripts === 0) {
			$wp_href = '';

			if ($waypoint_uc !== '') {
				$wp_href = add_query_arg(
					'unique_code',
					$waypoint_uc,
					$url_manage_wise_navigation_scripts
				);
			}

			$wp_cell = ($wp_href !== '')
				? '<a href="' . esc_url($wp_href) . '" class="linkWaypoint" target="_blank" rel="noopener noreferrer">'
					. escape_html($waypoint_title)
				. '</a>'
				: escape_html($waypoint_title);

            $rows_html .= '<tr>'
                        . '<td class="wpTitleCell">' . $wp_cell . '</td>'
                        . '<td></td>'
                        . '<td></td>'
                        . '<td></td>'
                        . '</tr>';
        } else {
            $arr_scripts_jpn = get_arr_wise_navigation_scripts($waypoint_id, $int_used_language_jpn);
            $arr_scripts_cht = get_arr_wise_navigation_scripts($waypoint_id, $int_used_language_cht);

            $msg_jpn_by_id = [];
            $tmpl_jpn_by_id = [];
            if (is_array($arr_scripts_jpn)) {
                foreach ($arr_scripts_jpn as $s) {
                    $sid = isset($s['id']) ? intval($s['id']) : 0;
                    $msg_jpn_by_id[$sid] = isset($s['script_message']) ? strval($s['script_message']) : '';
                    $tmpl_jpn_by_id[$sid] = isset($s['script_message_template']) ? strval($s['script_message_template']) : '';
                }
            }

            $msg_cht_by_id = [];
            $tmpl_cht_by_id = [];
            if (is_array($arr_scripts_cht)) {
                foreach ($arr_scripts_cht as $s) {
                    $sid = isset($s['id']) ? intval($s['id']) : 0;
                    $msg_cht_by_id[$sid] = isset($s['script_message']) ? strval($s['script_message']) : '';
                    $tmpl_cht_by_id[$sid] = isset($s['script_message_template']) ? strval($s['script_message_template']) : '';
                }
            }

            foreach ($arr_scripts_base as $si => $sc) {
                $script_id = isset($sc['id']) ? intval($sc['id']) : 0;
                $script_uc = isset($sc['unique_code']) ? strval($sc['unique_code']) : '';
                $script_type_id = isset($sc['script_type_id']) ? intval($sc['script_type_id']) : 0;

                if ($script_type_id === $int_masta_script_type_id_message_free) {
                    $script_type_short = 'MSG';
                } elseif ($script_type_id === $int_masta_script_type_id_layer_for_content) {
                    $script_type_short = 'LAYER';
                } else {
                    $script_type_short = isset($map_type_to_key[$script_type_id])
						? strtoupper(str_replace('_', "\n", $map_type_to_key[$script_type_id]))
						: strval($script_type_id);
                }

                $msg_jpn = '';
                $msg_cht = '';

                if (
                    $script_type_id === $int_masta_script_type_id_layer_for_goal ||
                    $script_type_id === $int_masta_script_type_id_layer_for_content ||
                    $script_type_id === $int_masta_script_type_id_message_explanation_for_particle ||
                    $script_type_id === $int_masta_script_type_id_message_explanation_for_grammar ||
                    $script_type_id === $int_masta_script_type_id_message_explanation_for_inflection ||
                    $script_type_id === $int_masta_script_type_id_message_explanation_for_sentence ||
                    $script_type_id === $int_masta_script_type_id_reset_goal
                ) {
                    $arr_items = get_arr_wise_navigation_items($script_id, $int_used_language_jpn);
                    $arr_items_cht = get_arr_wise_navigation_items($script_id, $int_used_language_cht);

                    $arr_layer_unique_codes_jpn = [];
                    $arr_layer_unique_codes_cht = [];
                    $t_layer_unique_codes_for_highlight_jpn = [];
                    $t_layer_unique_codes_for_highlight_cht = [];

                    if (is_array($arr_items) && count($arr_items) > 0) {
                        foreach ($arr_items as $it) {
                            $layer_id = isset($it['layer_id']) ? intval($it['layer_id']) : 0;
                            if ($layer_id <= 0) continue;
                            $layer_uc = fetch_unique_code_from_layer_id($layer_id, $int_used_language_jpn);
                            if ($layer_uc === null || $layer_uc === '') continue;
                            $arr_layer_unique_codes_jpn[] = $layer_uc;
                            if (isset($it['is_new']) && intval($it['is_new']) === 1) {
                                $t_layer_unique_codes_for_highlight_jpn[] = $layer_uc;
                            }
                        }
                    }

                    if (is_array($arr_items_cht) && count($arr_items_cht) > 0) {
                        foreach ($arr_items_cht as $it) {
                            $layer_id = isset($it['layer_id']) ? intval($it['layer_id']) : 0;
                            if ($layer_id <= 0) continue;
                            $layer_uc = fetch_unique_code_from_layer_id($layer_id, $int_used_language_cht);
                            if ($layer_uc === null || $layer_uc === '') continue;
                            $arr_layer_unique_codes_cht[] = $layer_uc;
                            if (isset($it['is_new']) && intval($it['is_new']) === 1) {
                                $t_layer_unique_codes_for_highlight_cht[] = $layer_uc;
                            }
                        }
                    }

                    $sentence_text_jpn = '';
                    $sentence_text_cht = '';
                    $item_japanese_jpn = '';
                    $item_japanese_cht = '';

                    if (count($arr_layer_unique_codes_jpn) > 0) {
                        $sentence_result_jpn = get_data_wise_map_sentence_from_waypoints(
                            $arr_layer_unique_codes_jpn,
                            $t_layer_unique_codes_for_highlight_jpn,
                            $int_used_language_jpn
                        );
                        if (is_array($sentence_result_jpn)) {
                            $sentence_text_jpn = isset($sentence_result_jpn['item_plain_sentence']) ? strval($sentence_result_jpn['item_plain_sentence']) : '';
                            $item_japanese_jpn = isset($sentence_result_jpn['item_japanese']) ? strval($sentence_result_jpn['item_japanese']) : '';
                        }
                    }

                    if (count($arr_layer_unique_codes_cht) > 0) {
                        $sentence_result_cht = get_data_wise_map_sentence_from_waypoints(
                            $arr_layer_unique_codes_cht,
                            $t_layer_unique_codes_for_highlight_cht,
                            $int_used_language_cht
                        );
                        if (is_array($sentence_result_cht)) {
                            $sentence_text_cht = isset($sentence_result_cht['item_plain_sentence']) ? strval($sentence_result_cht['item_plain_sentence']) : '';
                            $item_japanese_cht = isset($sentence_result_cht['item_japanese']) ? strval($sentence_result_cht['item_japanese']) : '';
                        }
                    }

                    if (
						$script_type_id === $int_masta_script_type_id_layer_for_goal ||
						$script_type_id === $int_masta_script_type_id_layer_for_content ||
						$script_type_id === $int_masta_script_type_id_reset_goal
					) {
                        $msg_jpn = $sentence_text_jpn;
                        $msg_cht = $sentence_text_cht !== '' ? $sentence_text_cht : $sentence_text_jpn;
                    } else {
                        $msg_jpn_src = isset($msg_jpn_by_id[$script_id]) ? $msg_jpn_by_id[$script_id] : '';
                        $tmpl_jpn_src = isset($tmpl_jpn_by_id[$script_id]) ? $tmpl_jpn_by_id[$script_id] : '';
                        $msg_cht_src = isset($msg_cht_by_id[$script_id]) ? $msg_cht_by_id[$script_id] : '';
                        $tmpl_cht_src = isset($tmpl_cht_by_id[$script_id]) ? $tmpl_cht_by_id[$script_id] : '';

                        $msg_jpn = generate_wise_navigation_message_from_script_type(
                            $script_type_id,
                            $msg_jpn_src,
                            $tmpl_jpn_src,
                            $script_id,
                            $item_japanese_jpn,
                            $int_used_language_jpn,
                            $arr_scripts_jpn
                        );
                        $msg_cht = generate_wise_navigation_message_from_script_type(
                            $script_type_id,
                            $msg_cht_src,
                            $tmpl_cht_src,
                            $script_id,
                            ($item_japanese_cht !== '' ? $item_japanese_cht : $item_japanese_jpn),
                            $int_used_language_cht,
                            $arr_scripts_cht
                        );
                    }
                } else {
                    $msg_jpn_src = isset($msg_jpn_by_id[$script_id]) ? $msg_jpn_by_id[$script_id] : '';
                    $tmpl_jpn_src = isset($tmpl_jpn_by_id[$script_id]) ? $tmpl_jpn_by_id[$script_id] : '';
                    $msg_cht_src = isset($msg_cht_by_id[$script_id]) ? $msg_cht_by_id[$script_id] : '';
                    $tmpl_cht_src = isset($tmpl_cht_by_id[$script_id]) ? $tmpl_cht_by_id[$script_id] : '';

                    $msg_jpn = generate_wise_navigation_message_from_script_type(
                        $script_type_id,
                        $msg_jpn_src,
                        $tmpl_jpn_src,
                        $script_id,
                        '',
                        $int_used_language_jpn,
                        $arr_scripts_jpn
                    );
                    $msg_cht = generate_wise_navigation_message_from_script_type(
                        $script_type_id,
                        $msg_cht_src,
                        $tmpl_cht_src,
                        $script_id,
                        '',
                        $int_used_language_cht,
                        $arr_scripts_cht
                    );
                }

                $msg_jpn = apply_remove_original_tags(strval($msg_jpn));
                $msg_cht = apply_remove_original_tags(strval($msg_cht));

                $rows_html .= '<tr>';
				if (!$printed_waypoint_cell) {

					$wp_href = '';
					if ($waypoint_uc !== '') {
						$wp_href = add_query_arg(
							'unique_code',
							$waypoint_uc,
							$url_manage_wise_navigation_scripts
						);
					}

					$wp_cell = ($wp_href !== '')
						? '<a href="' . esc_url($wp_href) . '" class="linkWaypoint" target="_blank" rel="noopener noreferrer">'
							. escape_html($waypoint_title)
						. '</a>'
						: escape_html($waypoint_title);

					$rows_html .= '<td class="wpTitleCell" rowspan="' . (int)$count_scripts . '">' . $wp_cell . '</td>';

					$printed_waypoint_cell = true;
				}

				$msg_href = '';
				if ($script_uc !== '') {

					if ($script_type_id === $int_masta_script_type_id_message_free) {

						$msg_href = add_query_arg(
							'unique_code',
							$waypoint_uc,
							$url_manage_wise_navigation_scripts
						);

					} elseif (
						$script_type_id === $int_masta_script_type_id_layer_for_goal ||
						$script_type_id === $int_masta_script_type_id_layer_for_content ||
						$script_type_id === $int_masta_script_type_id_reset_goal
					) {

						$msg_href = add_query_arg(
							'unique_code',
							$script_uc,
							$url_manage_wise_navigation_items
						);
					}
				}


				// メッセージ本文は「本文」なので、escape_html → nl2br の順が安全
				$msg_jpn_text = escape_html_with_nl2br($msg_jpn);
				$msg_cht_text = escape_html_with_nl2br($msg_cht);

				$msg_jpn_cell = ($msg_href !== '')
					? '<a href="' . esc_url($msg_href) . '" class="linkMessage" target="_blank" rel="noopener noreferrer">'
						. $msg_jpn_text
					. '</a>'
					: $msg_jpn_text;

				$msg_cht_cell = ($msg_href !== '')
					? '<a href="' . esc_url($msg_href) . '" class="linkMessage" target="_blank" rel="noopener noreferrer">'
						. $msg_cht_text
					. '</a>'
					: $msg_cht_text;

                $rows_html .= '<td class="scriptTypeCell">' . escape_html_with_nl2br($script_type_short) . '</td>'
                            . '<td class="' . $str_class_fixed_font . '">' . $msg_jpn_cell . '</td>'
                            . '<td class="' . $str_class_fixed_font . '">' . $msg_cht_cell . '</td>'
                            . '</tr>';
            }
        }

        $tbody_groups_html .= '<tbody class="wpGroup">' . $rows_html . '</tbody>';
    }

    $render_message_group('pre_ending', 'pre_ending', 'MSG');
    $render_message_group('summary', 'summary', 'MSG');
    $render_message_group('ending', 'ending', 'MSG');

    $style = '<style>
    .wiseSequenceTableContainer .wiseSequenceTable{
        border:3px solid #333;
        border-collapse:separate;
        border-spacing:0;
        width:100%;
    }
    .wiseSequenceTableContainer .wiseSequenceTable th,
    .wiseSequenceTableContainer .wiseSequenceTable td{
        padding:8px;
        vertical-align:top;
        background:#fff;
    }
    .wiseSequenceTableContainer .wiseSequenceTable tbody tr:nth-child(even) td {
        background-color: #f9f9f9;
    }
    .wiseSequenceTableContainer .wiseSequenceTable thead th{
        position:sticky; top:0; z-index:1; background:#fafafa;
        border-top:3px solid #333;
        border-bottom:1px solid #ddd;
    }
    .wiseSequenceTableContainer .wiseSequenceTable tbody td{
        border-bottom:1px solid #ddd;
    }
    .wiseSequenceTableContainer .wiseSequenceTable tbody.wpGroup tr:last-child td{
        border-bottom:3px solid #333;
    }
    .wiseSequenceTableContainer .wiseSequenceTable th,
    .wiseSequenceTableContainer .wiseSequenceTable td{
        border-right:3px solid #333;
    }
    .wiseSequenceTableContainer .wpTitleCell{font-weight:600;background:#f7f9fc}
    .wiseSequenceTableContainer .scriptTypeCell{white-space:nowrap;font-weight:600;opacity:.85}
    .wiseSequenceTableContainer a.linkWaypoint,
    .wiseSequenceTableContainer a.linkMessage{text-decoration:none;color:#000;cursor:pointer}
    .wiseSequenceTableContainer a.linkWaypoint:hover,
    .wiseSequenceTableContainer a.linkMessage:hover{text-decoration:underline}
    </style>';

	$table = '<div class="wiseSequenceTableContainer">'
		. $style
		. '<button type="button" class="copyTableBtn" onclick="copyWiseSequenceTable(this)">📋 コピー</button>'
		. '<button type="button" class="copyColBtn"   onclick="copyWiseSequenceColumn(this, \'message jpn\')">📋 message jpn</button>'
		. '<button type="button" class="copyColBtn"   onclick="copyWiseSequenceColumn(this, \'message cht\')">📋 message cht</button>'
		. '<table class="wiseSequenceTable">'
		. $thead
		. $tbody_groups_html
		. '</table>'
		. '</div>'
		. '
		<script>
			function copyWiseSequenceTable(btn){
				const table = findNextTable(btn);
				if(!table) return;
				const grid = buildGrid(table);
				const maxCols = Math.max(...grid.map(r => (r || []).length));
				const lines = grid.map(row => {
					row = row || [];
					const out = row.slice();
					for (let i = out.length; i < maxCols; i++) out[i] = "";
					return out.join("\\t");
				}).join("\\n");
				navigator.clipboard.writeText(lines).then(() => { alert("テーブルをコピーしました。"); }).catch(() => {});
			}

			function copyWiseSequenceColumn(btn, headerText){
				const table = findNextTable(btn);
				if(!table) return;
				const grid = buildGrid(table);
				const header = grid[0] || [];
				const target = (headerText || "").toLowerCase().trim();
				let colIndex = header.findIndex(h => String(h || "").toLowerCase().trim() === target);
				if (colIndex < 0) return;
				const lines = [];
				for (let r = 1; r < grid.length; r++){
					const v = (grid[r] && grid[r][colIndex]) ? String(grid[r][colIndex]).trim() : "";
					if (v !== "") lines.push(v);
				}
				if (lines.length === 0) return;
				navigator.clipboard.writeText(lines.join("\\n")).then(() => {
					alert(header[colIndex] + " をコピーしました。");
				}).catch(() => {});
			}

			function buildGrid(table){
				const rows = Array.from(table.rows);
				const grid = [];
				rows.forEach((tr, r) => {
					grid[r] = grid[r] || [];
					let cIndex = 0;
					Array.from(tr.cells).forEach(cell => {
						while (grid[r][cIndex] !== undefined) cIndex++;
						const colSpan = cell.colSpan || 1;
						const rowSpan = cell.rowSpan || 1;
						const text = (cell.innerText || "")
							.replace(/\\s*\\n\\s*/g, " ")
							.replace(/\\t/g, " ")
							.trim();
						grid[r][cIndex] = text;
						for (let cs = 1; cs < colSpan; cs++) grid[r][cIndex + cs] = "";
						for (let rs = 1; rs < rowSpan; rs++){
							grid[r + rs] = grid[r + rs] || [];
							for (let cs = 0; cs < colSpan; cs++) grid[r + rs][cIndex + cs] = "";
						}
						cIndex += colSpan;
					});
				});
				return grid;
			}

			function findNextTable(btn){
				let node = btn;
				while(node && node.nextElementSibling){
					node = node.nextElementSibling;
					if (node.tagName && node.tagName.toLowerCase() === "table") return node;
				}
				return null;
			}
		</script>
		';

    $inner = $h1 . $h2 . $table;
	return '<section class="sectionStandard">' . $inner . '</section>';
}



/******************************************************
 *  HTML
 *  
 ******************************************************/

function build_html_navi_items_add_contents($registered_sentence_id, $int_selected_language){

	global
		$t_layers,
		$str_snake_to_camel_japanese;

	$str_html = '';
	$str_add_li = '';

	$arr_strSQL_select = [
		[$t_layers,'id'],
		[$t_layers,'unique_code'],
		[$t_layers,'layer_name'],
		[$t_layers,'sort']
	];

	$strSQL_from = ' FROM '.$t_layers;

	$arr_strSQL_where = [
		[
			[
				[$t_layers,'registered_sentence_id','=',$registered_sentence_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_layers,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_layers) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	if (!empty($arr_layers)) {
		foreach ($arr_layers as $key => $layer) {
			$int_layer_id    = intval($layer['id']);
			$str_unique_code = (string)$layer['unique_code'];
			$str_layer_name  = escape_html_with_nl2br($layer['layer_name']);

			$arr_display = get_arr_layer_title_with_highlight($str_unique_code, $int_selected_language, $str_layer_name);

			$str_add_li =
			$str_add_li.'<li class="naviItemsSideMenuAddContentsLi wiseUiFontSizeTarget" '.
				'data-layer-id="'.escape_html_with_nl2br($int_layer_id).'" '.
				'data-unique-code="'.escape_html_with_nl2br($str_unique_code).'" '.
				'data-layer-name="'.escape_html_with_nl2br($str_layer_name).'" '.
				'data-index="'.escape_html_with_nl2br($key).'">'.
					'<div class="searchWordListLiDiv naviItemsSideMenuAddContentsLiDiv display-on">'.
						$arr_display['title'].' : '.$arr_display[$str_snake_to_camel_japanese].
					'</div>'.
			'</li>';
		}
	} else {
		$str_add_li = '<li class="resultEmpty">リスト 0件</li>';
	}

	$str_html =
	'<div id="naviItemsSideMenuAddContents" class="naviItemsModal naviItemsLeftModal leftPositionModal naviItemsSideMenuTopLevel naviItemsModal-open">
		<div class="naviItemsSideMenuTitleContainer">
			<div class="naviItemsSideMenuTitle">レイヤー</div>
		</div>
		<div class="naviItemsSideMenuContents leftPositionModalContents">
			<ul id="naviItemsSideMenuAddContentsUl" class="naviItemsSideMenuSelectableList">'.
			$str_add_li.
			'</ul>
		</div>
	</div>';

	return $str_html;
}

function build_html_navi_items_selected_contents($int_selected_language){

	$str_html = '';

	$str_html =
	'<div id="naviItemsSideMenuSelectedContents" class="naviItemsModal naviItemsRightModal naviItemsSideMenuTopLevel naviItemsModal-open">
		<div class="naviItemsSideMenuTitleContainer">
			<div id="naviItemsSideMenuSelectedContentsTitle" class="naviItemsSideMenuTitle">リスト</div>
			<button id="naviItemsSideMenuSelectedContentsConfirmButton" class="naviItemsRightModalButtons">Exit</button>
		</div>
		<div class="naviItemsSideMenuContents">
			<ul id="naviItemsSideMenuSelectedContentsUl" class="naviItemsSideMenuSelectableList"></ul>
		</div>
	</div>';

	return $str_html;
}



/******************************************************
 *  GET
 *  
 ******************************************************/

function get_str_wise_navigation_script_edit_title($script_unique_code, $int_selected_language) {

	global
		$t_wise_navigation_scripts,
		$t_wise_navigation_waypoints,
		$t_masta_wise_navigation_script,
		$t_layers,
		$t_wise_navigations,
		$t_wise_navigation_items,
		$t_masta_japanese_root,
		$arr_columns_wise_navigation_script_message,
		$arr_columns_wise_navigation_script_message_template,
		$arr_columns_masta_japanese_root,
		$int_masta_script_type_id_message_explanation_for_particle,
		$int_masta_script_type_id_message_explanation_for_grammar,
		$int_masta_script_type_id_message_explanation_for_inflection,
		$int_masta_script_type_id_message_explanation_for_sentence,
		$int_masta_script_type_id_layer_for_goal,
		$int_masta_script_type_id_layer_for_content,
		$int_masta_script_type_id_reset_goal;

    $arr_strSQL_select = [
        [$t_wise_navigation_scripts, 'id'],
        [$t_wise_navigation_scripts, 'unique_code'],
        [$t_wise_navigation_scripts, 'wise_navigation_waypoint_id'],
        [$t_wise_navigation_scripts, 'script_type_id'],
        [$t_wise_navigation_scripts, $arr_columns_wise_navigation_script_message[$int_selected_language] . ' as message'],
        [$t_wise_navigation_waypoints, 'wise_navigation_id'],
        [$t_masta_wise_navigation_script, 'id as masta_script_id'],
        [$t_masta_wise_navigation_script, $arr_columns_wise_navigation_script_message_template[$int_selected_language] . ' as template_message']
    ];
    $strSQL_from = "
        FROM $t_wise_navigation_scripts
        INNER JOIN $t_wise_navigation_waypoints
            ON $t_wise_navigation_waypoints.id = $t_wise_navigation_scripts.wise_navigation_waypoint_id
        LEFT JOIN $t_masta_wise_navigation_script
            ON $t_masta_wise_navigation_script.id = $t_wise_navigation_scripts.script_type_id
    ";
    $arr_strSQL_where = [
        [
            [
                ['BINARY ' . $t_wise_navigation_scripts, 'unique_code', '=', $script_unique_code, 'PDO::PARAM_STR', '']
            ],
            ''
        ]
    ];
    $arr_strSQL_order = [];
    $strSQL_option = '';
    list($pdo_has_error, $select_has_error, $e, $rows_script) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);
    if (empty($rows_script)) {
        return '';
    }

    $script_row = $rows_script[0];
    $script_id = intval($script_row['id']);
    $script_type_id = intval($script_row['script_type_id']);
    $script_message = strval($script_row['message']);
    $script_message_template = strval($script_row['template_message']);
    $wise_navigation_id = intval($script_row['wise_navigation_id']);
    $waypoint_id_of_this_script = intval($script_row['wise_navigation_waypoint_id']);

    $arr_strSQL_select = [
        [$t_wise_navigation_scripts, 'id'],
        [$t_wise_navigation_scripts, 'script_type_id'],
        [$t_wise_navigation_scripts, 'wise_navigation_waypoint_id']
    ];
    $strSQL_from = "
        FROM $t_wise_navigation_scripts
        INNER JOIN $t_wise_navigation_waypoints
            ON $t_wise_navigation_waypoints.id = $t_wise_navigation_scripts.wise_navigation_waypoint_id
    ";
    $arr_strSQL_where = [
        [
            [
                [$t_wise_navigation_waypoints, 'wise_navigation_id', '=', $wise_navigation_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];
    $arr_strSQL_order = [
        [$t_wise_navigation_waypoints, 'sort', 'ASC'],
        [$t_wise_navigation_scripts, 'sort', 'ASC']
    ];
    $strSQL_option = '';
    list($pdo_has_error, $select_has_error, $e, $rows_all_scripts) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    $arr_wise_navigation_scripts = [];
    foreach ($rows_all_scripts as $r) {
        if (intval($r['wise_navigation_waypoint_id']) !== $waypoint_id_of_this_script) continue;
        $arr_wise_navigation_scripts[] = [
            'script_type_id' => intval($r['script_type_id'])
        ];
    }

    $arr_layer_unique_codes = [];
    $arr_strSQL_select = [
        [$t_layers, 'unique_code']
    ];
    $strSQL_from = "
        FROM $t_wise_navigations
        INNER JOIN $t_wise_navigation_waypoints
            ON $t_wise_navigations.id = $t_wise_navigation_waypoints.wise_navigation_id
        INNER JOIN $t_wise_navigation_scripts
            ON $t_wise_navigation_waypoints.id = $t_wise_navigation_scripts.wise_navigation_waypoint_id
        INNER JOIN $t_wise_navigation_items
            ON $t_wise_navigation_items.wise_navigation_script_id = $t_wise_navigation_scripts.id
        INNER JOIN $t_layers
            ON $t_layers.id = $t_wise_navigation_items.layer_id
    ";
    $arr_strSQL_where = [
        [
            [
                [$t_wise_navigations, 'id', '=', $wise_navigation_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];
    $arr_strSQL_order = [
        [$t_wise_navigation_waypoints, 'sort', 'ASC'],
        [$t_wise_navigation_scripts, 'sort', 'ASC'],
        [$t_wise_navigation_items, 'sort', 'ASC']
    ];
    $strSQL_option = '';
    list($pdo_has_error, $select_has_error, $e, $rows_layers_all) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);
    foreach ($rows_layers_all as $lr) {
        $uc = strval($lr['unique_code']);
        if ($uc !== '' && !in_array($uc, $arr_layer_unique_codes, true)) {
            $arr_layer_unique_codes[] = $uc;
        }
    }

    $t_layer_unique_codes_for_highlight = [];
    $arr_strSQL_select = [
        [$t_layers, 'unique_code']
    ];
    $strSQL_from = "
        FROM $t_wise_navigation_items
        INNER JOIN $t_layers
            ON $t_layers.id = $t_wise_navigation_items.layer_id
    ";
    $arr_strSQL_where = [
        [
            [
                [$t_wise_navigation_items, 'wise_navigation_script_id', '=', $script_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];
    $arr_strSQL_order = [
        [$t_wise_navigation_items, 'sort', 'ASC']
    ];
    $strSQL_option = '';
    list($pdo_has_error, $select_has_error, $e, $rows_layers_this) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);
    foreach ($rows_layers_this as $lr2) {
        $uc2 = strval($lr2['unique_code']);
        if ($uc2 !== '' && !in_array($uc2, $t_layer_unique_codes_for_highlight, true)) {
            $t_layer_unique_codes_for_highlight[] = $uc2;
        }
    }

    $item_japanese = '';
    $message_explanations = [
        $int_masta_script_type_id_message_explanation_for_particle,
        $int_masta_script_type_id_message_explanation_for_grammar,
        $int_masta_script_type_id_message_explanation_for_inflection,
        $int_masta_script_type_id_message_explanation_for_sentence
    ];
    if (in_array($script_type_id, $message_explanations, true)) {
        $arr_strSQL_select = [
            [$t_masta_japanese_root, 'id'],
            [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language]]
        ];
        $strSQL_from = "
            FROM $t_wise_navigations
            INNER JOIN $t_wise_navigation_waypoints
                ON $t_wise_navigations.id = $t_wise_navigation_waypoints.wise_navigation_id
            INNER JOIN $t_wise_navigation_scripts
                ON $t_wise_navigation_waypoints.id = $t_wise_navigation_scripts.wise_navigation_waypoint_id
            INNER JOIN $t_wise_navigation_items
                ON $t_wise_navigation_scripts.id = $t_wise_navigation_items.wise_navigation_script_id
            LEFT JOIN $t_layers
                ON $t_layers.id = $t_wise_navigation_items.layer_id
            LEFT JOIN $t_masta_japanese_root
                ON $t_masta_japanese_root.id = $t_layers.masta_japanese_root_id
        ";
        $arr_strSQL_where = [
            [
                [
                    [$t_wise_navigations, 'id', '=', $wise_navigation_id, 'PDO::PARAM_INT', 'And'],
                    [$t_wise_navigation_scripts, 'id', '=', $script_id, 'PDO::PARAM_INT', '']
                ],
                ''
            ]
        ];
        $arr_strSQL_order = [
            [$t_wise_navigations, 'sort', 'ASC'],
            [$t_wise_navigation_waypoints, 'sort', 'ASC'],
            [$t_wise_navigation_scripts, 'sort', 'ASC'],
            [$t_wise_navigation_items, 'sort', 'ASC']
        ];
        $strSQL_option = '';
        list($pdo_has_error, $select_has_error, $e, $rows_term) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
        handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);
        if (!empty($rows_term)) {
            $col_name = $arr_columns_masta_japanese_root[$int_selected_language];
            $item_japanese = strval($rows_term[0][$col_name]);
        }
    }

    if ($script_type_id === $int_masta_script_type_id_layer_for_goal || $script_type_id === $int_masta_script_type_id_layer_for_content || $script_type_id === $int_masta_script_type_id_reset_goal) {
        $built = get_data_wise_map_sentence_from_waypoints($t_layer_unique_codes_for_highlight, $t_layer_unique_codes_for_highlight, $int_selected_language);
        $title_html = isset($built['item_title']) ? strval($built['item_title']) : '';
        return $title_html;
    }

    $title = generate_wise_navigation_message_from_script_type(
        $script_type_id,
        $script_message,
        $script_message_template,
        $script_id,
        $item_japanese,
        $int_selected_language,
        $arr_wise_navigation_scripts
    );
    return apply_remove_original_tags(strval($title));
}

function get_arr_layer_title_with_highlight(string $t_layer_unique_code, int $int_selected_language, string $fallback_escaped_layer_name) : array
{
    $arr = get_data_wise_map_sentence_from_waypoints([$t_layer_unique_code], [$t_layer_unique_code], $int_selected_language);

    $html = (string)($arr['item_title'] ?? '');
    $japanese = (string)($arr['item_japanese'] ?? '');

    if ($html === '') {
        $html = $fallback_escaped_layer_name;
    }

    return [
        'title' => $html,
        $str_snake_to_camel_japanese => $japanese
    ];
}