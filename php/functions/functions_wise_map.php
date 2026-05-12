<?php

/******************************************************
 *  PAGE
 *  
 ******************************************************/


/******************************************************
 *  HTML
 *  
 ******************************************************/
function build_html_wise_map_components(array $map_data, array $options): array {

	$goal_text = escape_html($map_data['goal_data']['goal_text'], ENT_QUOTES, 'UTF-8');
	// 未定義id
	$goal_unique_code = $map_data['goal_data']['goal_unique_code'] ?? 0;

	$goal_html = '<div class="' . escape_html($options['goal_class']) . '"><span class="' . escape_html($options['goal_icon_class']) . '"></span>' . $goal_text . '</div>';

	$waypoints_html = '';

	foreach ($map_data['route_data'] as $wp) {
		$wp_unique_code = escape_html($wp['waypoint_unique_code']);
		$wp_title = escape_html($wp['waypoint_title'], ENT_QUOTES, 'UTF-8');
		$id_prefix = $options['id_prefix'];

		$waypoints_html .= '<li class="' . escape_html($options['waypoint_class']) . '" data-waypoint-unique-code="' . $wp_unique_code . '" data-goal-unique-code="' . $goal_unique_code . '">';
		$waypoints_html .= '<span class="' . escape_html($options['waypoint_title_class']) . '">' . $wp_title . '</span>';
		$waypoints_html .= '<div class="' . escape_html($options['toggle_container_class']) . '">';
		$waypoints_html .= '<input type="checkbox" id="' . $id_prefix . $wp_unique_code . '" class="' . escape_html($options['toggle_button_class']) . '" data-waypoint-unique-code="' . $wp_unique_code . '" data-goal-unique-code="' . $goal_unique_code . '" data-action="' . escape_html($options['action']) . '">';
		$waypoints_html .= '<label for="' . $id_prefix . $wp_unique_code . '" class="' . escape_html($options['toggle_label_class']) . '"></label>';
		$waypoints_html .= '</div>';
		$waypoints_html .= '</li>';
	}

	return [
		'goal_html' => $goal_html,
		'waypoints_html' => $waypoints_html
	];
}

/******************************************************
 *  SCREEN
 *  
 ******************************************************/
function build_html_wise_map_screen($isNavi, $int_selected_language){

	$str_html_overlay_close_button = build_html_overlay_close_button();

	$str_wiseMapScreen = '';
	$str_wiseNaviMessageScreen = '';
	
	if($isNavi){
		$str_screen_id = "wiseNaviScreen";
		$str_wiseNaviMessageScreen = '<div id="wiseMapMessageScreen" class="wiseNaviMessage wiseUiFontSizeTarget"></div>';
	}
	else{
		$str_screen_id = "wiseMapScreen";
	}
	
	$str_wiseMapScreen = '
	<div id="wiseMapScreenOverlay" class="wisePanelScreenOverlay">
		<div id="' . $str_screen_id . '">

			<div id="wiseMapScreenButtonsContainer" class="wiseMapScreenButtonsContainerGlobal">
				'.$str_html_overlay_close_button.'
				<div class="modalScrollableContainer">
					<div id="wiseMapControlButtons">
						<button id="wiseMapToggleUIButton" class="wiseMapControlButton" data-state="open">UI Panel</button>
						<button id="wiseMapToggleUsagesListButton" class="wiseMapControlButton" data-state="close">Usages Panel</button>
						<button id="wiseMapToggleTasksListButton" class="wiseMapControlButton" data-state="close">Tasks Panel</button>
					</div>
					<div id="wiseMapScreenTabs" class="wiseMapScreenTabGroup">
						<button class="wiseMapScreenTabButton" data-action="map:show" data-action-target="wiseMapFocusPointScreen">Focus Point</button>
						<button class="wiseMapScreenTabButton" data-action="map:show" data-action-target="wiseMapLessonStepGoalScreen">Usages</button>
						<button class="wiseMapScreenTabButton" data-action="map:show" data-action-target="wiseMapLessonGoalScreen">Tasks</button>
					</div>
				</div>
			</div>

			<section id="wiseMapFocusPointScreen" class="wiseMapScreenSection">
				<div id="wiseMapFocusPointScreenLoading" class="wiseMapScreenLoading loading-hidden">
					<div class="wiseMapScreenLoadingSpinner"></div>
				</div>
				<div id="wiseMapScreenUiUxContainerFocus" class="wiseMapScreenUiUxContainer">
					<div id="wiseMapScreenUiContainerFocus" class="wiseMapScreenUiContainer">
						<div id="wiseMapScreenUiDisplayAreaFocus" class="wiseMapScreenUiDisplayArea">
							<div id="wiseMapScreenUiDisplayAreaUlFocus" class="wiseMapScreenUiDisplayAreaUl"></div>
						</div>
					</div>
					<div id="wiseMapScreenUsagesListContainerFocus" class="wiseMapScreenUsagesListContainer wiseMapScreenListContainer hidden">
						<div id="wiseMapScreenUsagesListDisplayAreaFocus" class="wiseMapScreenListDisplayArea">
							<ul id="wiseMapScreenUsagesListDisplayAreaUlFocus" class="wiseMapScreenListDisplayAreaUl"></ul>
						</div>
					</div>
					<div id="wiseMapScreenTasksListContainerFocus" class="wiseMapScreenTasksListContainer wiseMapScreenListContainer hidden">
						<div id="wiseMapScreenTasksListDisplayAreaFocus" class="wiseMapScreenListDisplayArea">
							<ul id="wiseMapScreenTasksListDisplayAreaUlFocus" class="wiseMapScreenListDisplayAreaUl"></ul>
						</div>
					</div>
					<div id="wiseMapScreenUxContainerFocus" class="wiseMapScreenUxContainer">
						<div id="wiseMapScreenNodeDisplayAreaFocus" class="wiseMapScreenNodeDisplayArea"></div>
						<div id="wiseMapScreenMapContainerFocus" class="wiseMapScreenMapContainer">
							<div id="wiseMapScreenMapContainerLoadingFocus" class="wiseMapScreenMapContainerLoading loading-hidden">
								<div class="wiseMapScreenMapContainerLoadingSpinner"></div>
							</div>
							<div id="wiseMapScreenMapDisplayAreaFocus" class="wiseMapScreenMapDisplayArea"></div>
						</div>
					</div>
				</div>
			</section>

			<section id="wiseMapLessonStepGoalScreen" class="wiseMapScreenSection hidden">
				<div id="wiseMapLessonStepGoalScreenLoading" class="wiseMapScreenLoading loading-hidden">
					<div class="wiseMapScreenLoadingSpinner"></div>
				</div>
				<div id="wiseMapScreenUiUxContainerLessonStep" class="wiseMapScreenUiUxContainer">
					<div id="wiseMapScreenUiContainerLessonStep" class="wiseMapScreenUiContainer">
						<div id="wiseMapScreenUiDisplayAreaLessonStep" class="wiseMapScreenUiDisplayArea">
							<div id="wiseMapScreenUiDisplayAreaUlLessonStep" class="wiseMapScreenUiDisplayAreaUl"></div>
						</div>
					</div>
					<div id="wiseMapScreenUsagesListContainerLessonStep" class="wiseMapScreenUsagesListContainer wiseMapScreenListContainer hidden">
						<div id="wiseMapScreenUsagesListDisplayAreaLessonStep" class="wiseMapScreenListDisplayArea">
							<ul id="wiseMapScreenUsagesListDisplayAreaUlLessonStep" class="wiseMapScreenListDisplayAreaUl"></ul>
						</div>
					</div>
					<div id="wiseMapScreenTasksListContainerLessonStep" class="wiseMapScreenTasksListContainer wiseMapScreenListContainer hidden">
						<div id="wiseMapScreenTasksListDisplayAreaLessonStep" class="wiseMapScreenListDisplayArea">
							<ul id="wiseMapScreenTasksListDisplayAreaUlLessonStep" class="wiseMapScreenListDisplayAreaUl"></ul>
						</div>
					</div>
					<div id="wiseMapScreenUxContainerLessonStep" class="wiseMapScreenUxContainer">
						<div id="wiseMapScreenNodeDisplayAreaLessonStep" class="wiseMapScreenNodeDisplayArea"></div>
						<div id="wiseMapScreenMapContainerLessonStep" class="wiseMapScreenMapContainer">
							<div id="wiseMapScreenMapContainerLoadingLessonStep" class="wiseMapScreenMapContainerLoading loading-hidden">
								<div class="wiseMapScreenMapContainerLoadingSpinner"></div>
							</div>
							<div id="wiseMapScreenMapDisplayAreaLessonStep" class="wiseMapScreenMapDisplayArea"></div>
						</div>
					</div>
				</div>
			</section>

			<section id="wiseMapLessonGoalScreen" class="wiseMapScreenSection hidden">
				<div id="wiseMapLessonGoalScreenLoading" class="wiseMapScreenLoading loading-hidden">
					<div class="wiseMapScreenLoadingSpinner"></div>
				</div>
				<div id="wiseMapScreenUiUxContainerLesson" class="wiseMapScreenUiUxContainer">
					<div id="wiseMapScreenUiContainerLesson" class="wiseMapScreenUiContainer">
						<div id="wiseMapScreenUiDisplayAreaLesson" class="wiseMapScreenUiDisplayArea">
							<div id="wiseMapScreenUiDisplayAreaUlLesson" class="wiseMapScreenUiDisplayAreaUl"></div>
						</div>
					</div>
					<div id="wiseMapScreenUsagesListContainerLesson" class="wiseMapScreenUsagesListContainer wiseMapScreenListContainer hidden">
						<div id="wiseMapScreenUsagesListDisplayAreaLesson" class="wiseMapScreenListDisplayArea">
							<ul id="wiseMapScreenUsagesListDisplayAreaUlLesson" class="wiseMapScreenListDisplayAreaUl"></ul>
						</div>
					</div>
					<div id="wiseMapScreenTasksListContainerLesson" class="wiseMapScreenTasksListContainer wiseMapScreenListContainer hidden">
						<div id="wiseMapScreenTasksListDisplayAreaLesson" class="wiseMapScreenListDisplayArea">
							<ul id="wiseMapScreenTasksListDisplayAreaUlLesson" class="wiseMapScreenListDisplayAreaUl"></ul>
						</div>
					</div>
					<div id="wiseMapScreenUxContainerLesson" class="wiseMapScreenUxContainer">
						<div id="wiseMapScreenNodeDisplayAreaLesson" class="wiseMapScreenNodeDisplayArea"></div>
						<div id="wiseMapScreenMapContainerLesson" class="wiseMapScreenMapContainer">
							<div id="wiseMapScreenMapContainerLoadingLesson" class="wiseMapScreenMapContainerLoading loading-hidden">
								<div class="wiseMapScreenMapContainerLoadingSpinner"></div>
							</div>
							<div id="wiseMapScreenMapDisplayAreaLesson" class="wiseMapScreenMapDisplayArea"></div>
						</div>
					</div>
				</div>
			</section>
		</div>' . 
		$str_wiseNaviMessageScreen . '
	</div>';

	return $str_wiseMapScreen;
}

/******************************************************
 *  GOAL
 *  
 ******************************************************/
// デバッグ 未実装
function get_data_user_goal(int $target_id, bool $is_direct = true, int $int_selected_language): array {
}

// デバッグ 未実装
function get_data_user_goal_route($user_goal_data) {
}

/******************************************************
 *  MILESTONE
 *  
 ******************************************************/
// デバッグ 未実装
function get_data_milestone(int $target_id, bool $is_direct = true, int $int_selected_language): array {
}

// デバッグ 未実装
function get_data_milestone_route($milestone_id) {
}

/******************************************************
 *  LESSON GOAL
 *  
 ******************************************************/
function get_data_lesson_goal_from_ids(array $masta_japanese_root_ids, bool $is_direct = true, int $int_selected_language): array
{

    if (empty($masta_japanese_root_ids)) {
        return [
            'goal_data' => ['goal_text' => ''],
            'route_data' => []
        ];
    }

    $route_steps = get_data_lesson_step_goal_from_ids($masta_japanese_root_ids, $is_direct, $int_selected_language);
    $route_usages = $route_steps['route_data'];
	$route_data = get_data_lesson_goal_route($route_usages, $is_direct, $int_selected_language);

	return [
        'goal_data' => [
            'goal_text' => 'Tasks from Selected Grammar'
        ],
        'route_data' => $route_data
    ];

}

function get_data_lesson_goal(int $target_id, bool $is_direct = false, int $int_selected_language): array {

	global
		$t_teaching_material_lessons,
		$arr_columns_masta_teaching_material_lessons;

    $arr_strSQL_select = [
        [$t_teaching_material_lessons, 'id as lesson_id'],
        [$t_teaching_material_lessons, $arr_columns_masta_teaching_material_lessons[$int_selected_language] . ' as lesson_title']
    ];
    $strSQL_from = " FROM $t_teaching_material_lessons ";
    $arr_strSQL_where = [
        [
            [
                [$t_teaching_material_lessons, 'id', '=', $target_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];
    $arr_strSQL_order = [];

    list($pdo_has_error, $select_has_error, $e, $arr_lessons) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        ''
    );
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    if (empty($arr_lessons)) {
        return [
            'goal_data' => ['goal_text' => ''],
            'route_data' => []
        ];
    }

	
    $route_usages = get_arr_lesson_usages_route($target_id, $is_direct, $int_selected_language);

    $lesson_title = $arr_lessons[INDEX_FIRST]['lesson_title'] ?? '';
	$route_data = get_data_lesson_goal_route($route_usages, $is_direct, $int_selected_language);

	return [
		'goal_data' => ['goal_text' => $lesson_title],
		'route_data' => $route_data
	];

}


function get_data_lesson_goal_with_usages_for_debug(int $target_id, bool $is_direct = false, int $int_selected_language): array {

	global
		$t_teaching_material_lessons,
		$arr_columns_masta_teaching_material_lessons;

    $arr_strSQL_select = [
        [$t_teaching_material_lessons, 'id as lesson_id'],
        [$t_teaching_material_lessons, $arr_columns_masta_teaching_material_lessons[$int_selected_language] . ' as lesson_title']
    ];
    $strSQL_from = " FROM $t_teaching_material_lessons ";
    $arr_strSQL_where = [
        [
            [
                [$t_teaching_material_lessons, 'id', '=', $target_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];
    $arr_strSQL_order = [];

    list($pdo_has_error, $select_has_error, $e, $arr_lessons) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        ''
    );
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    if (empty($arr_lessons)) {
        return [
            'goal_data' => ['goal_text' => ''],
            'route_data' => []
        ];
    }

    $lesson_title = $arr_lessons[INDEX_FIRST]['lesson_title'] ?? '';
    $route_data = get_arr_lesson_usages_route($target_id, $is_direct, $int_selected_language);

    return [
        'goal_data' => ['goal_text' => $lesson_title],
        'route_data' => $route_data
    ];
}


function get_data_lesson_goal_route(array $route_usages, bool $is_direct = false, int $int_selected_language): array {

	global
		$t_grammar_usage_children,
		$t_masta_japanese_root,
		$arr_columns_masta_japanese_root,
		$str_sql_where_is_in,
		$t_masta_wise_map_node,
		$arr_columns_masta_wise_map_node,
		$t_wise_map_node_usage_links,
		$int_masta_wise_map_node_type_lesson_goal;

    $waypoint_codes = [];
    $child_order = [];
    $seq = COUNT_FIRST;
    foreach ($route_usages as $wp) {
        if (!empty($wp['waypoint_unique_code'])) {
            $waypoint_codes[] = (string) $wp['waypoint_unique_code'];
        }
        if (!empty($wp['items']) && is_array($wp['items'])) {
            foreach ($wp['items'] as $it) {
                $uc = (string) $it['item_unique_code'];
                if (!isset($child_order[$uc])) {
                    $child_order[$uc] = $seq++;
                }
            }
        }
    }
    $waypoint_codes = array_values(array_unique($waypoint_codes));

    if (empty($waypoint_codes)) {
        return [];
    }

    $arr_strSQL_select = [
        [$t_grammar_usage_children, 'id as child_id'],
        [$t_masta_japanese_root, 'unique_code'],
        [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language] . ' as child_title']
    ];
    $strSQL_from = " FROM $t_masta_japanese_root
                     INNER JOIN $t_grammar_usage_children
                     ON $t_masta_japanese_root.id = $t_grammar_usage_children.masta_japanese_root_id ";
    $arr_strSQL_where = [
        [
            [
                ['BINARY ' . $t_masta_japanese_root, 'unique_code', $str_sql_where_is_in, $waypoint_codes, 'PDO::PARAM_STR', '']
            ],
            ''
        ]
    ];
    $arr_strSQL_order = [];
    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_children_info) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    $__wp_index = array_flip($waypoint_codes);
    usort($arr_children_info, function ($a, $b) use ($__wp_index) {
        $ia = $__wp_index[$a['unique_code']] ?? PHP_INT_MAX;
        $ib = $__wp_index[$b['unique_code']] ?? PHP_INT_MAX;
        return $ia <=> $ib;
    });

    $function_child_ids = array_values(array_unique(array_map(fn($r) => (int) $r['child_id'], $arr_children_info)));

    if (empty($function_child_ids)) {
        return [];
    }

    $arr_strSQL_select = [
        [$t_masta_wise_map_node, 'id as node_id'],
        [$t_masta_wise_map_node, $arr_columns_masta_wise_map_node[$int_selected_language] . ' as node_title'],
        [$t_wise_map_node_usage_links, 'grammar_usage_child_id as link_child_id']
    ];
    $strSQL_from = " FROM $t_wise_map_node_usage_links
                     INNER JOIN $t_masta_wise_map_node
                     ON $t_masta_wise_map_node.id = $t_wise_map_node_usage_links.masta_wise_map_node_id ";
    $arr_strSQL_where = [
        [
            [
                [$t_wise_map_node_usage_links, 'grammar_usage_child_id', $str_sql_where_is_in, $function_child_ids, 'PDO::PARAM_INT', 'And'],
                [$t_masta_wise_map_node, 'node_type', '=', $int_masta_wise_map_node_type_lesson_goal, 'PDO::PARAM_INT', 'And'],
                [$t_masta_wise_map_node, 'is_published', '=', FLAG_TRUE, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];
    $arr_strSQL_order = [
        [$t_masta_wise_map_node, 'id', 'ASC']
    ];
    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_tasks_raw) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    $func_index = [];
    foreach ($function_child_ids as $i => $cid) {
        $func_index[(int) $cid] = $i;
    }

    usort($arr_tasks_raw, function ($a, $b) use ($func_index) {
        $ia = $func_index[(int) $a['link_child_id']] ?? PHP_INT_MAX;
        $ib = $func_index[(int) $b['link_child_id']] ?? PHP_INT_MAX;
        if ($ia !== $ib) return $ia <=> $ib;
        return (int) $a['node_id'] <=> (int) $b['node_id'];
    });

    $child_map = [];
    foreach ($arr_children_info as $c) {
        $child_map[(int) $c['child_id']] = [
            'unique_code' => (string) $c['unique_code'],
            'title' => (string) $c['child_title']
        ];
    }

    $waypoint_nodes = [];
    $seen_waypoint_items = [];
    $first_item_order_by_node = [];
	$str_prefix_wise = 'wise-';

    foreach ($arr_tasks_raw as $task_row) {
        $node_id = (int) $task_row['node_id'];
        $child_id = (int) $task_row['link_child_id'];

        if (!isset($waypoint_nodes[$node_id])) {
            $waypoint_nodes[$node_id] = [
                'waypoint_id' => $node_id,
                'waypoint_unique_code' => $str_prefix_wise . $node_id,
                'waypoint_title' => (string) $task_row['node_title'],
                'items' => []
            ];
            $seen_waypoint_items[$node_id] = [];
            $first_item_order_by_node[$node_id] = $func_index[$child_id] ?? PHP_INT_MAX;
        } else {
            $child_order_pos = $func_index[$child_id] ?? PHP_INT_MAX;
            if ($child_order_pos < $first_item_order_by_node[$node_id]) {
                $first_item_order_by_node[$node_id] = $child_order_pos;
            }
        }

        if (isset($child_map[$child_id])) {
            $waypoint_code = $child_map[$child_id]['unique_code'];

            $route_items = [];
            foreach ($route_usages as $route_node) {
                if (!empty($route_node['waypoint_unique_code']) && $route_node['waypoint_unique_code'] === $waypoint_code) {
                    $route_items = $route_node['items'] ?? [];
                    break;
                }
            }

			if (!empty($route_items)) {
				if (!isset($waypoint_nodes[$node_id]['items'])) {
					$waypoint_nodes[$node_id]['items'] = [];
				}
				if (!isset($seen_waypoint_items[$node_id])) {
					$seen_waypoint_items[$node_id] = [];
				}

				foreach ($route_items as $it) {
					$uc = (string)($it['item_unique_code'] ?? '');
					if ($uc === '') continue;

					$prio = (int)($it['priority'] ?? PRIORITY_FIRST);
					$sortVal = isset($it['sort']) ? (int)$it['sort'] : (isset($child_order[$uc]) ? (int)$child_order[$uc] : PHP_INT_MAX);

					if (!isset($seen_waypoint_items[$node_id][$uc])) {
						$seen_waypoint_items[$node_id][$uc] = count($waypoint_nodes[$node_id]['items']);
						$waypoint_nodes[$node_id]['items'][] = [
							'item_unique_code' => $uc,
							'item_title'       => (string)($it['item_title'] ?? ''),
							'priority'         => $prio,
							'sort'             => $sortVal
						];
					} else {
						$idx = $seen_waypoint_items[$node_id][$uc];
						$curPri  = (int)($waypoint_nodes[$node_id]['items'][$idx]['priority'] ?? PRIORITY_FIRST);
						$curSort = (int)($waypoint_nodes[$node_id]['items'][$idx]['sort'] ?? PHP_INT_MAX);

						if ($prio < $curPri) {
							$waypoint_nodes[$node_id]['items'][$idx]['priority'] = $prio;
						}
						if ($sortVal < $curSort) {
							$waypoint_nodes[$node_id]['items'][$idx]['sort'] = $sortVal;
						}
					}
				}
			}
        }
    }

    $route_data = array_values($waypoint_nodes);
    usort($route_data, function ($a, $b) use ($first_item_order_by_node) {
        $oa = $first_item_order_by_node[$a['waypoint_id']] ?? PHP_INT_MAX;
        $ob = $first_item_order_by_node[$b['waypoint_id']] ?? PHP_INT_MAX;
        if ($oa !== $ob) return $oa <=> $ob;
        return $a['waypoint_id'] <=> $b['waypoint_id'];
    });
	
	$route_data = apply_route_data_grouping_by_min_priority($route_data);
	foreach ($route_data as &$wp) {
		$minPri = $wp['waypoint_priority'] ?? PRIORITY_FIRST;
		if ($minPri !== PRIORITY_FIRST) {
			// マジックナンバー
			$wp['waypoint_title'] = '【補足】:' . $wp['waypoint_title'];
		}
	}
	unset($wp);

    return $route_data;
}


function get_arr_lesson_usages_route(int $lesson_id, bool $is_direct = false, int $int_selected_language): array {

	global
	    $t_teaching_material_lesson_steps;

    $arr_strSQL_select_steps = [
        [$t_teaching_material_lesson_steps, 'id as lesson_step_id'],
        [$t_teaching_material_lesson_steps, 'sort as step_sort']
    ];
    $strSQL_from_steps = " FROM $t_teaching_material_lesson_steps ";
    $arr_strSQL_where_steps = [
        [
            [
                [$t_teaching_material_lesson_steps, 'lesson_id', '=', $lesson_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];
    $arr_strSQL_order_steps = [
        [$t_teaching_material_lesson_steps, 'sort', 'ASC'],
        [$t_teaching_material_lesson_steps, 'id', 'ASC']
    ];

    list($pdo_has_error, $select_has_error, $e, $arr_steps) = execute_select_and_fetch_all(
        $arr_strSQL_select_steps,
        $strSQL_from_steps,
        $arr_strSQL_where_steps,
        $arr_strSQL_order_steps,
        ''
    );
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    if (empty($arr_steps)) {
        return [];
    }

    $wmap = [];
    $seen_per_wp = [];
    $global_pos = [];
    $seq = COUNT_FIRST;

    foreach ($arr_steps as $s) {
        $step_id = (int) $s['lesson_step_id'];
        $step_data = get_data_lesson_step_goal($step_id, $is_direct, $int_selected_language);

        foreach ($step_data['route_data'] as $wp) {
            $pid = (string) $wp['waypoint_unique_code'];
            $ptitle = (string) $wp['waypoint_title'];

            if (!isset($wmap[$pid])) {
                $wmap[$pid] = [
                    'waypoint_unique_code' => $pid,
                    'waypoint_title' => $ptitle,
                    'items' => []
                ];
                $seen_per_wp[$pid] = [];
            }

            if (!empty($wp['items']) && is_array($wp['items'])) {
                foreach ($wp['items'] as $it) {
                    $cid = (string) $it['item_unique_code'];
                    if (!isset($global_pos[$cid])) {
                        $global_pos[$cid] = $seq++;
                    }
                    if (!isset($seen_per_wp[$pid][$cid])) {
                        $seen_per_wp[$pid][$cid] = count($wmap[$pid]['items']);
                        $wmap[$pid]['items'][] = [
                            'item_unique_code' => $cid,
                            'item_title' => (string) $it['item_title'],
                            'priority' => (int) ($it['priority'] ?? PRIORITY_FIRST),
                            'sort' => $global_pos[$cid]
                        ];
                    } else {
                        $idx = $seen_per_wp[$pid][$cid];
                        $curp = (int) $wmap[$pid]['items'][$idx]['priority'];
                        $newp = (int) ($it['priority'] ?? PRIORITY_FIRST);
                        if ($newp > $curp) {
                            $wmap[$pid]['items'][$idx]['priority'] = $newp;
                        }
                    }
                }
            }
        }
    }

    foreach ($wmap as &$wp) {
        usort($wp['items'], fn($a, $b) => $a['sort'] <=> $b['sort']);
        $wp['_order'] = !empty($wp['items']) ? $wp['items'][INDEX_FIRST]['sort'] : PHP_INT_MAX;
    }
    unset($wp);

    $route_data = array_values($wmap);
    usort($route_data, function ($a, $b) {
        $cmp = $a['_order'] <=> $b['_order'];
        if ($cmp !== 0) return $cmp;
        return strcmp($a['waypoint_title'], $b['waypoint_title']);
    });
	$route_data = apply_route_data_grouping_by_min_priority($route_data);

    foreach ($route_data as &$n) {
        foreach ($n['items'] as &$it) {
            $it['sort'] = (int) $it['sort'];
        }
        unset($it);
        unset($n['_order']);
    }
    unset($n);

    return $route_data;
}



/******************************************************
 *  LESSON STEP GOAL
 *  
 ******************************************************/
function get_data_lesson_step_goal_from_ids(array $masta_japanese_root_ids, bool $is_direct = true, int $int_selected_language): array
{
	global
		$t_masta_japanese_root,
		$t_masta_japanese_sub_category,
		$arr_columns_masta_japanese_root,
		$str_sql_where_is_in,
		$int_masta_japanese_category_id_grammar,
		$int_masta_japanese_category_id_terminology;

    if (empty($masta_japanese_root_ids)) {
        return [
            'goal_data' => ['goal_text' => ''],
            'route_data' => []
        ];
    }

    $arr_strSQL_select = [
        [$t_masta_japanese_root, 'id as masta_japanese_root_id'],
        [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language]],
		[$t_masta_japanese_root, 'unique_code  as child_unique_code']
    ];

    $strSQL_from = "
					FROM 
					$t_masta_japanese_root
					INNER JOIN $t_masta_japanese_sub_category
					ON
					$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
				";

	$use_terminology = false;
	if($use_terminology){
		$arr_strSQL_where = [
			[
				[
					[$t_masta_japanese_root, 'id', $str_sql_where_is_in, $masta_japanese_root_ids, 'PDO::PARAM_INT', 'And'],
					[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_grammar,'PDO::PARAM_INT','']
				]
				,
				'Or'
			],
			[
				[
					[$t_masta_japanese_root, 'id', $str_sql_where_is_in, $masta_japanese_root_ids, 'PDO::PARAM_INT', 'And'],
					[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_terminology,'PDO::PARAM_INT','']
				]
				,
				''
			]
		];
	}
	else{
		$arr_strSQL_where = [
			[
				[
					[$t_masta_japanese_root, 'id', $str_sql_where_is_in, $masta_japanese_root_ids, 'PDO::PARAM_INT', 'And'],
					[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_grammar,'PDO::PARAM_INT','']
				]
				,
				''
			]
		];
	}

    $arr_strSQL_order = [];

    list($pdo_has_error, $select_has_error, $e, $arr_step_data) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        ''
    );
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    $child_meta = [];
    $ordered_ids = [];
    $i = INDEX_FIRST;
    foreach ($arr_step_data as $row) {
        $c_unique_code = $row['child_unique_code'];
        if (!isset($child_meta[$c_unique_code])) {
            $child_meta[$c_unique_code] = [
                'name' => $row[$arr_columns_masta_japanese_root[$int_selected_language]],
                'lesson_priority' => 0,
                'sort' => $i++
            ];
            $ordered_ids[] = $c_unique_code;
        }
    }

    $route_data = get_data_lesson_step_goal_route($ordered_ids, $child_meta, $is_direct, $int_selected_language);

    return [
        'goal_data' => [
            'goal_text' => 'Usages from Selected Grammar'
        ],
        'route_data' => $route_data
    ];
}


function get_data_lesson_step_goal(int $step_id, bool $is_direct = true, int $int_selected_language): array
{
	global
		$t_teaching_material_lesson_steps,
		$t_teaching_material_lesson_step_units,
		$t_teaching_material_lesson_contents,
		$t_masta_japanese_root,
		$t_masta_japanese_sub_category,
		$arr_columns_masta_japanese_root,
		$arr_columns_masta_teaching_material_lesson_steps,
		$int_masta_japanese_category_id_grammar,
		$int_masta_japanese_category_id_terminology;

    $arr_strSQL_select = [
        [$t_teaching_material_lesson_contents, 'id'],
        [$t_teaching_material_lesson_contents, 'masta_japanese_root_id'],
        [$t_teaching_material_lesson_contents, 'priority'],
        [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language]],
		[$t_masta_japanese_root, 'unique_code  as child_unique_code '],
        [$t_teaching_material_lesson_steps, $arr_columns_masta_teaching_material_lesson_steps[$int_selected_language]]
    ];

    $strSQL_from = "
			FROM 
			(
				(
					(
						$t_teaching_material_lesson_steps
						INNER JOIN $t_teaching_material_lesson_step_units
							ON $t_teaching_material_lesson_steps.id = $t_teaching_material_lesson_step_units.lesson_step_id
					)
					INNER JOIN $t_teaching_material_lesson_contents
						ON $t_teaching_material_lesson_step_units.id = $t_teaching_material_lesson_contents.step_unit_id
				)
				INNER JOIN $t_masta_japanese_root
				ON $t_teaching_material_lesson_contents.masta_japanese_root_id = $t_masta_japanese_root.id
			)
			INNER JOIN $t_masta_japanese_sub_category
			ON
			$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
			";

	$use_terminology = false;
	if($use_terminology){
		$arr_strSQL_where = [
			[
				[
					[$t_teaching_material_lesson_steps, 'id', '=', $step_id, 'PDO::PARAM_INT', 'And'],
					[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_grammar,'PDO::PARAM_INT','']
				],
				'Or'
			],
			[
				[
					[$t_teaching_material_lesson_steps, 'id', '=', $step_id, 'PDO::PARAM_INT', 'And'],
					[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_terminology,'PDO::PARAM_INT','']
				],
				''
			]
		];
	}
	else{
		$arr_strSQL_where = [
			[
				[
					[$t_teaching_material_lesson_steps, 'id', '=', $step_id, 'PDO::PARAM_INT', 'And'],
					[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_grammar,'PDO::PARAM_INT','']
				],
				''
			]
		];
	}


    $arr_strSQL_order = [
        [$t_teaching_material_lesson_steps, 'sort', 'ASC'],
        [$t_teaching_material_lesson_step_units, 'sort', 'ASC'],
        [$t_teaching_material_lesson_contents, 'sort', 'ASC']
    ];

    list($pdo_has_error, $select_has_error, $e, $arr_step_data) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        ''
    );
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    $child_meta = [];
    $ordered_ids = [];
    $i = INDEX_FIRST;
    foreach ($arr_step_data as $row) {
        $c_unique_code = $row['child_unique_code'];
        if (!isset($child_meta[$c_unique_code])) {
            $child_meta[$c_unique_code] = [
                'name' => $row[$arr_columns_masta_japanese_root[$int_selected_language]],
                'lesson_priority' => (int) $row['priority'],
                'sort' => $i++
            ];
            $ordered_ids[] = $c_unique_code;
        }
    }

    $route_data = get_data_lesson_step_goal_route($ordered_ids, $child_meta, $is_direct, $int_selected_language);

    $goal_data = [
        'goal_text' => $arr_step_data[INDEX_FIRST][$arr_columns_masta_teaching_material_lesson_steps[$int_selected_language]] ?? ''
    ];

    return [
        'goal_data' => $goal_data,
        'route_data' => $route_data
    ];
}


function get_data_lesson_step_goal_route(array $ordered_ids, array $child_meta, bool $is_direct, int $int_selected_language): array
{
	global
		$int_masta_grammar_usage_tier_root,
		$int_masta_grammar_usage_tier_core,
		$int_masta_grammar_usage_tier_detail,
		$int_masta_japanese_category_id_terminology;

    $relations = fetch_all_root_parent_child_relations($int_selected_language);

    $relation_map = [];
    $tree_map = [];
    $roots = [];

    foreach ($relations as $r) {
        $c = $r['child_unique_code'];
        $p = $r['parent_unique_code'];
        $t = (int)$r['tier'];
        $relPriority = (int)($r['priority'] ?? PRIORITY_FIRST);
        $relation_map[$c][] = [
            'waypoint_unique_code' => $p,
            'waypoint_title' => $r['parent_name'],
            'tier' => $t,
            'priority' => $relPriority,
            'parent_category_id' => isset($r['parent_category_id']) ? (int)$r['parent_category_id'] : null,
            'child_category_id' => isset($r['child_category_id']) ? (int)$r['child_category_id'] : null
        ];
        $tree_map[$p][] = $c;
        if ($t === $int_masta_grammar_usage_tier_root && !in_array($p, $roots, true)) {
            $roots[] = $p;
        }
    }

    $wmap = [];

    $add_item = function(
        string $wmap_p_unique_code,
        string $wmap_p_title,
        string $wmap_c_unique_code,
        array $wmap_c_meta,
        ?int $rel_priority = null
    ) use (&$wmap) {
        if (!isset($wmap[$wmap_p_unique_code])) {
            $wmap[$wmap_p_unique_code] = [
                'waypoint_unique_code' => $wmap_p_unique_code,
                'waypoint_title' => $wmap_p_title,
                'items' => []
            ];
        }
        $wmap[$wmap_p_unique_code]['items'][] = [
            'item_unique_code' => $wmap_c_unique_code,
            'item_title' => $wmap_c_meta['name'],
            'priority' => $rel_priority ?? (int)($wmap_c_meta['lesson_priority'] ?? PRIORITY_FIRST),
            'sort' => (int)$wmap_c_meta['sort']
        ];
    };

    foreach ($ordered_ids as $c_unique_code) {
        if ($is_direct) {
            foreach ($relation_map[$c_unique_code] ?? [] as $rel) {
                $add_item(
                    $rel['waypoint_unique_code'],
                    $rel['waypoint_title'],
                    $c_unique_code,
                    $child_meta[$c_unique_code],
                    (int)($rel['priority'] ?? ($child_meta[$c_unique_code]['lesson_priority'] ?? PRIORITY_FIRST))
                );
            }
            continue;
        }

        $child_cat = null;
        if (!empty($relation_map[$c_unique_code])) {
            foreach ($relation_map[$c_unique_code] as $rel) {
                if (isset($rel['child_category_id'])) {
                    $child_cat = (int)$rel['child_category_id'];
                    break;
                }
            }
        }

        if ($child_cat === $int_masta_japanese_category_id_terminology) {
            $picked = null;
            $picked_priority = null;
            if (!empty($relation_map[$c_unique_code])) {
                foreach ($relation_map[$c_unique_code] as $rel) {
                    if (isset($rel['parent_category_id']) && (int)$rel['parent_category_id'] === $int_masta_japanese_category_id_terminology) {
                        $pval = (int)($rel['priority'] ?? ($child_meta[$c_unique_code]['lesson_priority'] ?? PRIORITY_FIRST));
                        if ($picked === null || $pval < $picked_priority) {
                            $picked = $rel;
                            $picked_priority = $pval;
                        }
                    }
                }
            }
            if ($picked !== null) {
                $add_item(
                    $picked['waypoint_unique_code'],
                    $picked['waypoint_title'],
                    $c_unique_code,
                    $child_meta[$c_unique_code],
                    $picked_priority
                );
                continue;
            }
            $add_item(
                $c_unique_code,
                $child_meta[$c_unique_code]['name'],
                $c_unique_code,
                $child_meta[$c_unique_code],
                (int)($child_meta[$c_unique_code]['lesson_priority'] ?? PRIORITY_FIRST)
            );
            continue;
        }

        $stack = [['node' => $c_unique_code, 'seed' => null]];
        $visited = [];
        $found_core = false;

        while ($stack) {
            $frame = array_pop($stack);
            $cur = $frame['node'];
            $seed = $frame['seed'];
            foreach ($relation_map[$cur] ?? [] as $rel) {
                $pid = $rel['waypoint_unique_code'];
                if (in_array($pid, $visited, true)) {
                    continue;
                }
                $visited[] = $pid;
                $seed2 = $seed;
                if ($seed2 === null) {
                    $seed2 = isset($rel['priority'])
                        ? (int)$rel['priority']
                        : (int)($child_meta[$c_unique_code]['lesson_priority'] ?? PRIORITY_FIRST);
                }
                if ($rel['tier'] === $int_masta_grammar_usage_tier_core) {
                    $add_item(
                        $pid,
                        $rel['waypoint_title'],
                        $c_unique_code,
                        $child_meta[$c_unique_code],
                        $seed2
                    );
                    $found_core = true;
                } elseif ($rel['tier'] === $int_masta_grammar_usage_tier_detail) {
                    $stack[] = ['node' => $pid, 'seed' => $seed2];
                }
            }
        }
        if (!$found_core) {
            foreach ($relation_map[$c_unique_code] ?? [] as $rel) {
                $add_item(
                    $rel['waypoint_unique_code'],
                    $rel['waypoint_title'],
                    $c_unique_code,
                    $child_meta[$c_unique_code],
                    (int)($rel['priority'] ?? ($child_meta[$c_unique_code]['lesson_priority'] ?? PRIORITY_FIRST))
                );
            }
        }
    }

    foreach ($wmap as &$wp) {
        usort($wp['items'], fn($a, $b) => $a['sort'] <=> $b['sort']);
    }
    unset($wp);

    $order = [];
    foreach ($roots as $r) {
        apply_tree_preorder_traversal($r, $tree_map, $order);
    }

    $route_data = [];
    foreach (array_values(array_intersect($order, array_keys($wmap))) as $pid) {
        $route_data[] = $wmap[$pid];
    }

    $unique_map = [];
    foreach ($route_data as $wp) {
        $code = $wp['waypoint_unique_code'];
        $minPri = PHP_INT_MAX;
        if (!empty($wp['items'])) {
            foreach ($wp['items'] as $it) {
                $p = isset($it['priority']) ? (int)$it['priority'] : PHP_INT_MAX;
                if ($p < $minPri) $minPri = $p;
            }
        }
        if (!isset($unique_map[$code])) {
            $wp['waypoint_priority'] = $minPri;
            $unique_map[$code] = $wp;
        } else {
            $curMin = isset($unique_map[$code]['waypoint_priority']) ? (int)$unique_map[$code]['waypoint_priority'] : PHP_INT_MAX;
            if ($minPri < $curMin) {
                $wp['waypoint_priority'] = $minPri;
                $unique_map[$code] = $wp;
            }
        }
    }
    $route_data = array_values($unique_map);

    $route_data = apply_route_data_grouping_by_min_priority($route_data);

    return $route_data;
}


function get_data_lesson_step_goal_from_focus_point(array $usage_root_ids, bool $is_direct = true, int $int_selected_language): array
{
	global
		$t_masta_japanese_root,
		$t_masta_japanese_sub_category,
		$arr_columns_masta_japanese_root,
		$str_sql_where_is_in,
		$int_masta_japanese_category_id_grammar;

    if (empty($usage_root_ids)) {
        return [
            'goal_data' => ['goal_text' => ''],
            'route_data' => []
        ];
    }

    $arr_strSQL_select = [
        [$t_masta_japanese_root, 'id'],
        [$t_masta_japanese_root, 'unique_code as child_unique_code'],
        [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language] . ' as child_title'],
        [$t_masta_japanese_sub_category, 'category_id']
    ];
    $strSQL_from = " FROM $t_masta_japanese_root
                     INNER JOIN $t_masta_japanese_sub_category
                     ON $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id ";
    $arr_strSQL_where = [
        [
            [
                [$t_masta_japanese_root, 'id', $str_sql_where_is_in, $usage_root_ids, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    list($pdo_has_error, $select_has_error, $e, $rows) = execute_select_and_fetch_all(
        $arr_strSQL_select, $strSQL_from, $arr_strSQL_where, [], ''
    );
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    if (empty($rows)) {
        return [
            'goal_data' => ['goal_text' => ''],
            'route_data' => []
        ];
    }

    $grammar_ids = [];
    $usage_self_rows = [];
    foreach ($rows as $r) {
        if ((int)$r['category_id'] === $int_masta_japanese_category_id_grammar) {
            $grammar_ids[] = (int)$r['id'];
        } else {
            $usage_self_rows[] = $r;
        }
    }
    $grammar_ids = array_values(array_unique(array_filter($grammar_ids, fn($v) => (int)$v > 0)));

    $route_from_grammar = [];
    if (!empty($grammar_ids)) {
        $step_from_grammar = get_data_lesson_step_goal_from_ids($grammar_ids, $is_direct, $int_selected_language);
        $route_from_grammar = $step_from_grammar['route_data'] ?? [];
    }

    $route_from_usage = [];
    if (!empty($usage_self_rows)) {
        foreach ($usage_self_rows as $r) {
            $route_from_usage[] = [
                'waypoint_unique_code' => (string)$r['child_unique_code'],
                'waypoint_title' => (string)$r['child_title'],
                'items' => [],
                'waypoint_priority' => PRIORITY_FIRST
            ];
        }
    }

    $merged = apply_wise_map_route_data_merge($route_from_grammar, $route_from_usage);

    return [
        'goal_data' => ['goal_text' => 'Usages from Focus Point'],
        'route_data' => $merged
    ];
}


function apply_tree_preorder_traversal(string $node_unique_code, array $tree_map, array &$order): void
{
    $order[] = $node_unique_code;
    if (isset($tree_map[$node_unique_code])) {
        foreach ($tree_map[$node_unique_code] as $child) {
            apply_tree_preorder_traversal($child, $tree_map, $order);
        }
    }
}


function apply_route_data_grouping_by_min_priority(array $route_data): array
{
    $buckets = [];
    foreach ($route_data as $wp) {
        $minPri = isset($wp['waypoint_priority']) ? (int)$wp['waypoint_priority'] : PRIORITY_FIRST;

        if (!empty($wp['items'])) {
            foreach ($wp['items'] as $it) {
                if (isset($it['priority'])) {
                    $p = (int)$it['priority'];
                    if ($p < $minPri) $minPri = $p;
                }
            }
        }

        $wp['waypoint_priority'] = $minPri;
        $buckets[$minPri][] = $wp;
    }

    ksort($buckets, SORT_NUMERIC);
    $out = [];
    foreach ($buckets as $list) {
        foreach ($list as $wp) {
            $out[] = $wp;
        }
    }
    return $out;
}



/******************************************************
 *  FOCUS POINT
 *  
 ******************************************************/
function get_data_focus_point(int $t_registered_sentence_id = 0, int $int_selected_language): array {

	global
		$t_registered_sentences,
		$t_layers,
		$t_layer_elements,
		$t_registered_sentence_elements,
		$str_snake_to_camel_form_id;

    $arr_strSQL_select = [
        [$t_registered_sentences,'id as registeredSentenceId'],
        [$t_registered_sentences,'unique_code as goal_unique_code'],
        [$t_registered_sentences,'masta_japanese_root_id as masta_japanese_root_id_registered_sentences'],
        [$t_registered_sentences,'sentence'],
        [$t_layers,'id as layerId'],
        [$t_layers,'unique_code as waypoint_unique_code'],
        [$t_layers,'masta_japanese_root_id as masta_japanese_root_id_layers'],
        [$t_layers,'layer_name'],
        [$t_layer_elements,'id as layerElementId'],
        [$t_layer_elements,'unique_code as item_unique_code'],
        [$t_layer_elements,'form_id as ' . $str_snake_to_camel_form_id],
        [$t_layer_elements,'voice_id as voiceId'],
        [$t_layer_elements,'is_highlighted'],
        [$t_registered_sentence_elements,'id as sentenceElementId'],
        [$t_registered_sentence_elements,'japanese'],
        [$t_registered_sentence_elements,'sort']
    ];

    $strSQL_from = " FROM
                    (
                        (
                            $t_registered_sentences
                            INNER JOIN $t_layers
                            ON $t_registered_sentences.id = $t_layers.registered_sentence_id 
                        )
                        INNER JOIN $t_layer_elements
                        ON $t_layers.id = $t_layer_elements.layer_id
                    )
                    INNER JOIN $t_registered_sentence_elements
                    ON $t_layer_elements.registered_sentence_element_id = $t_registered_sentence_elements.id
                    ";

    $arr_strSQL_where = [
        [
            [
                [$t_registered_sentences,'id','=',$t_registered_sentence_id,'PDO::PARAM_INT','']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_layers,'sort','ASC'],
        [$t_registered_sentence_elements,'sort','ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_registered_sentence_elements) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    $result = get_data_focus_point_route($arr_registered_sentence_elements, $int_selected_language);

	$target_ids = array_values(array_unique(array_map(
		'intval',
		array_filter(
			array_column($arr_registered_sentence_elements, 'masta_japanese_root_id_layers'),
			static function ($v) { return !empty($v) && intval($v) > 0; }
		)
	)));

	$tasks_by_waypoint = [];
	$usages_by_waypoint = [];

	if (!empty($target_ids)) {
		$usages_res = get_data_lesson_step_goal_from_focus_point($target_ids, false, $int_selected_language);
		$route_usages = $usages_res['route_data'] ?? [];

		$tasks_res = get_data_lesson_goal_route($route_usages, false, $int_selected_language);

		$usages_by_waypoint = $route_usages;
		$tasks_by_waypoint = $tasks_res;
	}


	$result['tasks'] = $tasks_by_waypoint;
	$result['usages'] = $usages_by_waypoint;

    return $result;
}


function get_data_focus_point_route(array $array = [], int $int_selected_language): array {
    if (empty($array)) {
        return [];
    }

    $goal_data = [
        'goal_unique_code' => $array[INDEX_FIRST]['goal_unique_code'],
        'goal_text' => $array[INDEX_FIRST]['sentence']
    ];

    $route_data = [];
    foreach ($array as $row) {
        $waypointUniqueCode = $row['waypoint_unique_code'];
        if (!isset($route_data[$waypointUniqueCode])) {
            $route_data[$waypointUniqueCode] = [
                'waypoint_unique_code' => $waypointUniqueCode,
                'waypoint_title' => $row['layer_name'],
                'items' => []
            ];
        }
        $route_data[$waypointUniqueCode]['items'][] = [
            'item_unique_code' => $row['item_unique_code'],
            'item_title' => $row['japanese'],
            'priority' => (int)$row['is_highlighted'],
            'sort' => (int)$row['sort']
        ];
    }

    $route_data = array_values($route_data);

    foreach ($route_data as &$waypoint) {
        usort($waypoint['items'], function($a, $b) {
            return $a['sort'] <=> $b['sort'];
        });
    }

    return [
        'goal_data'  => $goal_data,
        'route_data' => $route_data
    ];
}


function apply_wise_map_route_data_merge(array $a, array $b): array
{
    $index = [];
    $order_seq = COUNT_FIRST;
    $appearance = [];

    $ingest = function (array $src) use (&$index, &$appearance, &$order_seq) {
        foreach ($src as $wp) {
            $wcode = (string)$wp['waypoint_unique_code'];
            if (!isset($index[$wcode])) {
                $index[$wcode] = [
                    'waypoint_unique_code' => $wcode,
                    'waypoint_title' => (string)($wp['waypoint_title'] ?? ''),
                    'items' => []
                ];
                $appearance[$wcode] = $order_seq++;
            } else {
                if (($index[$wcode]['waypoint_title'] ?? '') === '' && !empty($wp['waypoint_title'])) {
                    $index[$wcode]['waypoint_title'] = (string)$wp['waypoint_title'];
                }
            }

            if (!empty($wp['items']) && is_array($wp['items'])) {
                foreach ($wp['items'] as $it) {
                    $icode = (string)($it['item_unique_code'] ?? '');
                    if ($icode === '') continue;

                    if (!isset($index[$wcode]['items'][$icode])) {
                        $index[$wcode]['items'][$icode] = [
                            'item_unique_code' => $icode,
                            'item_title' => (string)($it['item_title'] ?? ''),
                            'priority' => PRIORITY_FIRST,
                            'sort' => (int)($it['sort'] ?? PHP_INT_MAX)
                        ];
                    } else {
                        $cur = $index[$wcode]['items'][$icode];
                        $index[$wcode]['items'][$icode] = [
                            'item_unique_code' => $icode,
                            'item_title' => $cur['item_title'] !== '' ? $cur['item_title'] : (string)($it['item_title'] ?? ''),
                            'priority' => PRIORITY_FIRST,
                            'sort' => min((int)$cur['sort'], (int)($it['sort'] ?? PHP_INT_MAX))
                        ];
                    }
                }
            }
        }
    };

    $ingest($a);
    $ingest($b);

    $route = [];
    foreach ($index as $wcode => $wp) {
        $items = array_values($wp['items']);
        usort($items, fn($x, $y) => $x['sort'] <=> $y['sort']);
        $wp['items'] = $items;
        $wp['waypoint_priority'] = PRIORITY_FIRST;
        $wp['_order'] = $appearance[$wcode] ?? PHP_INT_MAX;
        $route[] = $wp;
    }

    usort($route, function ($a, $b) {
        $oa = (int)($a['_order'] ?? PHP_INT_MAX);
        $ob = (int)($b['_order'] ?? PHP_INT_MAX);
        if ($oa !== $ob) return $oa <=> $ob;
        return strcmp((string)$a['waypoint_title'], (string)$b['waypoint_title']);
    });

    foreach ($route as &$n) {
        unset($n['_order']);
    }
    unset($n);

    $route = apply_route_data_grouping_by_min_priority($route);
    return $route;
}



/******************************************************
 *  GENERATE
 *  
 ******************************************************/

function generate_sentence_base_from_japanese(array $selected_transform, int $int_selected_language) : string
{

    global 
		$str_snake_to_camel_japanese_id,
		$str_snake_to_camel_japanese_element_id,
		$str_snake_to_camel_sub_classification_id,
		$str_snake_to_camel_form_id;

    $t_masta_form_root_id = intval($selected_transform[$str_snake_to_camel_form_id]);
    $int_voice_id = intval($selected_transform['voiceId']);

    if ($t_masta_form_root_id === 0 || $int_voice_id === 0) {
        return $selected_transform['japanese'];
    }

    $t_masta_japanese_root_id = $selected_transform[$str_snake_to_camel_japanese_id];
    $t_japanese_element_id = $selected_transform[$str_snake_to_camel_japanese_element_id];
    $t_masta_japanese_sub_classification_id = $selected_transform[$str_snake_to_camel_sub_classification_id];
    $int_label_id = $selected_transform['labelId'];

    $arr_indicator_labels = get_arr_indicator_label($int_label_id, false, $int_selected_language);
    $arr_inflected_label = get_arr_inflected_label(
        $arr_indicator_labels,
        $t_masta_japanese_root_id,
        $t_japanese_element_id,
        $t_masta_japanese_sub_classification_id,
        $t_masta_form_root_id,
        $int_voice_id,
        false,
        $int_selected_language
    );

    return !empty($arr_inflected_label['japanese'])
        ? $arr_inflected_label['japanese']
        : $selected_transform['japanese'];
}


function generate_wise_navigation_message_from_script_type(
    $script_type_id,
    $script_message,
    $script_message_template,
    $script_id,
    $item_japanese,
    $int_selected_language,
    $arr_wise_navigation_scripts
) {
	global
		$int_masta_script_type_id_message_free,
		$int_masta_script_type_id_message_starting_point,
		$int_masta_script_type_id_message_next_point,
		$int_masta_script_type_id_message_combine_ready,
		$int_masta_script_type_id_message_combine_result,
		$int_masta_script_type_id_message_intro_steps_overview,
		$int_masta_script_type_id_message_intro_steps_go,
		$int_masta_script_type_id_message_no_analysis,
		$int_masta_script_type_id_message_count_particle,
		$int_masta_script_type_id_message_explanation_for_particle,
		$int_masta_script_type_id_message_count_grammar,
		$int_masta_script_type_id_message_explanation_for_grammar,
		$int_masta_script_type_id_message_count_inflection,
		$int_masta_script_type_id_message_explanation_for_inflection,
		$int_masta_script_type_id_message_count_sentence,
		$int_masta_script_type_id_message_explanation_for_sentence;

    $script_message = strval($script_message);
    $template = strval($script_message_template);
    if ($template === '') {
        $template = $script_message;
    }

    if ($script_type_id === $int_masta_script_type_id_message_free) {
        return $script_message;
    }

    if (
        $script_type_id === $int_masta_script_type_id_message_starting_point ||
        $script_type_id === $int_masta_script_type_id_message_next_point ||
        $script_type_id === $int_masta_script_type_id_message_combine_ready ||
        $script_type_id === $int_masta_script_type_id_message_combine_result ||
        $script_type_id === $int_masta_script_type_id_message_intro_steps_overview ||
        $script_type_id === $int_masta_script_type_id_message_intro_steps_go ||
        $script_type_id === $int_masta_script_type_id_message_no_analysis
    ) {
        return $template;
    }

	if ($script_type_id === $int_masta_script_type_id_message_count_particle) {
		$count = count_by_script_type($arr_wise_navigation_scripts, $int_masta_script_type_id_message_explanation_for_particle);
		return str_replace('{count_particle}', strval($count), $template);
	}

	if ($script_type_id === $int_masta_script_type_id_message_count_grammar) {
		$count = count_by_script_type($arr_wise_navigation_scripts, $int_masta_script_type_id_message_explanation_for_grammar);
		return str_replace('{count_grammar}', strval($count), $template);
	}

	if ($script_type_id === $int_masta_script_type_id_message_count_inflection) {
		$count = count_by_script_type($arr_wise_navigation_scripts, $int_masta_script_type_id_message_explanation_for_inflection);
		return str_replace('{count_inflection}', strval($count), $template);
	}

	if ($script_type_id === $int_masta_script_type_id_message_count_sentence) {
		$count = count_by_script_type($arr_wise_navigation_scripts, $int_masta_script_type_id_message_explanation_for_sentence);
		return str_replace('{count_sentence}', strval($count), $template);
	}

    if (
		$script_type_id === $int_masta_script_type_id_message_explanation_for_particle ||
		$script_type_id === $int_masta_script_type_id_message_explanation_for_grammar ||
		$script_type_id === $int_masta_script_type_id_message_explanation_for_inflection ||
		$script_type_id === $int_masta_script_type_id_message_explanation_for_sentence
	) {
        $term = strval($item_japanese);
        if ($term === '') {
            return '';
        }
        return str_replace('{masta_japanese_root}', $term, $template);
    }

    return $script_message;
}



/******************************************************
 *  APPLY
 *  COUNT
 *  
 ******************************************************/
function apply_override_infomation($base, $overrides, $isHighlightedLayer){

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
		$txt_free  = (string)($ov['display_text'] ?? '');
		$txt_fixed = (string)($ov['display_text_from_masta'] ?? '');
        $is_highlighted = ((int)($ov['is_highlighted'] ?? FLAG_FALSE) === FLAG_TRUE) ? FLAG_TRUE : FLAG_FALSE;

        $isFixed = in_array($op_id, [
            $int_masta_operation_id_replace_fixed,
            $int_masta_operation_id_add_before_fixed,
            $int_masta_operation_id_add_after_fixed,
            $int_masta_operation_id_omit
        ], true);

        $val = $isFixed ? $txt_fixed : $txt_free;
		if ($isHighlightedLayer && $is_highlighted) {
			$val = '<span class="wiseGrammarParticle">' . $val . '</span>';
		}

        switch ($op_id) {
            case $int_masta_operation_id_omit:
                $out = '';
                break;

            case $int_masta_operation_id_replace_fixed:
            case $int_masta_operation_id_replace_free:
                $out = $val;
                break;

            case $int_masta_operation_id_add_before_fixed:
            case $int_masta_operation_id_add_before_free:
                $prefix .= $val;
                break;

            case $int_masta_operation_id_add_after_fixed:
            case $int_masta_operation_id_add_after_free:
                $suffix .= $val;
                break;
        }
    }

    return [$prefix, $out, $suffix];
}


function apply_form_voice_override_to_transform(array $selected_transform, array $selected_overrides) : array
{

    global
        $str_snake_to_camel_form_id;

    if (empty($selected_overrides)) { return $selected_transform; }
    foreach ($selected_overrides as $ov) {
        $mf = isset($ov[$str_snake_to_camel_form_id]) ? intval($ov[$str_snake_to_camel_form_id]) : 0;
        $mv = isset($ov['voiceId']) ? intval($ov['voiceId']) : 0;
        if ($mf !== 0) { $selected_transform[$str_snake_to_camel_form_id] = $mf; }
        if ($mv !== 0) { $selected_transform['voiceId'] = $mv; }
    }
    return $selected_transform;
}


function count_by_script_type($rows, int $target_type_id) : int
{
    if (!is_array($rows)) {
        return 0;
    }
    $count = 0;
    foreach ($rows as $row) {
        $st = isset($row['script_type_id']) ? intval($row['script_type_id']) : 0;
        if ($st === $target_type_id) {
            $count++;
        }
    }
    return $count;
}


/******************************************************
 *  GET
 *  
 ******************************************************/

function switch_get_data_wise_map($map_type = '', $target_id = null, $target_ids = [], $is_direct = true, $int_selected_language = INDEX_FIRST) {
	
    switch($map_type) {
        case 'user_goal':
            break;

        case 'milestone':
            break;

        case 'lesson_goal':
            $result = get_data_lesson_goal($target_id, $is_direct, $int_selected_language);
            break;
        case 'lesson_goal_from_ids':
            $result = get_data_lesson_goal_from_ids($target_ids, $is_direct, $int_selected_language);
            break;
        case 'lesson_goal_with_usages_for_debug':
            $result = get_data_lesson_goal_with_usages_for_debug($target_id, $is_direct, $int_selected_language);
            break;

        case 'lesson_step_goal':
            $result = get_data_lesson_step_goal($target_id, $is_direct, $int_selected_language);
            break;

		case 'lesson_step_goal_from_ids':
			$result = get_data_lesson_step_goal_from_ids($target_ids, $is_direct, $int_selected_language);
			break;

        case 'focus_point':
			$result = get_data_focus_point($target_id, $int_selected_language);
            break;

        default:
            throw new Exception('Invalid map type');
    }

    return $result;
}

function get_arr_wise_navigations($t_wise_navigation_id, $int_selected_language){
	
	global
	    $t_wise_navigations;

	$arr_strSQL_select = [
		[$t_wise_navigations,'id'],
		[$t_wise_navigations,'unique_code'],
		[$t_wise_navigations,'title']
	];

	$strSQL_from = " FROM $t_wise_navigations";

	$arr_strSQL_where = [
		[
			[
				[$t_wise_navigations,'id','=',$t_wise_navigation_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_wise_navigations) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_wise_navigations;
}


function get_data_wise_map_sentence_from_waypoints(array $arr_layer_unique_codes, array $t_layer_unique_codes_for_highlight, int $int_selected_language) : array
{
	global
		$t_layers,
		$t_masta_japanese_root,
		$t_layer_elements,
		$t_registered_sentence_elements,
		$str_snake_to_camel_japanese_id,
		$str_snake_to_camel_japanese_element_id,
		$str_snake_to_camel_sub_classification_id,
		$str_snake_to_camel_form_id,
		$arr_columns_masta_japanese_root;

    $arr_waypoints = [];
    foreach ($arr_layer_unique_codes as $t_layer_unique_code) {
        $t_layer_id = fetch_layer_id_from_unique_code($t_layer_unique_code, $int_selected_language);

		$arr_strSQL_select = [
			[$t_layers, 'id as layerId'],
			[$t_layers, 'unique_code'],
			[$t_layers, 'layer_name'],
			[$t_layers, 'sort as layerSort'],
			[$t_masta_japanese_root, 'unique_code as item_unique_code'],
			[$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language] . ' as item_japanese'],
			[$t_layer_elements, 'id as layerElementId'],
			[$t_layer_elements, 'form_id as ' . $str_snake_to_camel_form_id],
			[$t_layer_elements, 'voice_id as voiceId'],
			[$t_layer_elements, 'is_highlighted'],
			[$t_registered_sentence_elements, 'id as sentenceElementId'],
			[$t_registered_sentence_elements, 'japanese_id as ' . $str_snake_to_camel_japanese_id],
			[$t_registered_sentence_elements, 'japanese_element_id as ' . $str_snake_to_camel_japanese_element_id],
			[$t_registered_sentence_elements, 'sub_classification_id as ' . $str_snake_to_camel_sub_classification_id],
			[$t_registered_sentence_elements, 'japanese'],
			[$t_registered_sentence_elements, 'label_id as labelId'],
			[$t_registered_sentence_elements, 'sort as rseSort']
		];

        $strSQL_from = " FROM
                        (
                            (
                                $t_layers
                                LEFT JOIN $t_masta_japanese_root
                                ON $t_layers.masta_japanese_root_id = $t_masta_japanese_root.id
                            )
                            INNER JOIN $t_layer_elements
                            ON $t_layers.id = $t_layer_elements.layer_id
                        )
                        INNER JOIN $t_registered_sentence_elements
                        ON $t_layer_elements.registered_sentence_element_id = $t_registered_sentence_elements.id
                        ";

        $arr_strSQL_where = [
            [
                [
                    [$t_layers, 'id', '=', $t_layer_id, 'PDO::PARAM_INT', '']
                ],
                ''
            ]
        ];

        $arr_strSQL_order = [[$t_registered_sentence_elements, 'sort', 'ASC']];
        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $arr_waypoint_items) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
        handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

        if (!empty($arr_waypoint_items)) {
            $arr_waypoints[] = $arr_waypoint_items;
        }
    }

    $result_sentence = '';
    $result_sentence_plain = '';
    $highlighted_unique_code = null;
    $highlighted_japanese = null;

    if (!empty($arr_waypoints)) {
        foreach ($arr_waypoints as $waypoint) {
            foreach ($waypoint as $row) {
                if (isset($row['unique_code']) && in_array($row['unique_code'], $t_layer_unique_codes_for_highlight, true)) {
                    $highlighted_unique_code = $row['item_unique_code'] ?? null;
                    $highlighted_japanese = $row['item_japanese'] ?? null;
                    break 2;
                }
            }
        }
    }

    if (!empty($arr_waypoints)) {
        $groups = [];
        foreach ($arr_waypoints as $waypoint) {
            foreach ($waypoint as $item) {
                $groups[$item['rseSort']][] = $item;
            }
        }
        ksort($groups);

        foreach ($groups as $sort => $elements) {
            if (empty($elements)) { continue; }

            $selected_display = null;
            foreach ($elements as $el) {
                if (in_array($el['unique_code'], $t_layer_unique_codes_for_highlight, true)) {
                    $selected_display = $el;
                    break;
                }
            }
            if (!$selected_display) {
                $selected_display = $elements[INDEX_FIRST];
            }

            $selected_transform = $elements[INDEX_FIRST];
            $max_layer_sort = isset($selected_transform['layerSort']) ? intval($selected_transform['layerSort']) : PHP_INT_MIN;
            foreach ($elements as $el) {
                $ls = isset($el['layerSort']) ? intval($el['layerSort']) : PHP_INT_MIN;
                if ($ls > $max_layer_sort) {
                    $max_layer_sort = $ls;
                    $selected_transform = $el;
                }
            }

            $isHighlightedLayer = in_array($selected_display['unique_code'], $t_layer_unique_codes_for_highlight, true);
            $isHighlightedElement = $isHighlightedLayer ? intval($selected_display['is_highlighted']) : FLAG_FALSE;

            $overrideMap = [];
            $hasAnyNoOverride = false;
            foreach ($elements as $el) {
                $leId = intval($el['layerElementId']);
                if (!isset($overrideMap[$leId])) {
                    $rows = fetch_arr_overrides_by_layer_element_id($leId, $int_selected_language);
                    $overrideMap[$leId] = $rows;
                    if (count($rows) === COUNT_EMPTY) { $hasAnyNoOverride = true; }
                }
            }

            $prefix = '';
			$out = '';
			$suffix = '';

			$selectedLeId = intval($selected_transform['layerElementId']);
			$selectedOverrides = $overrideMap[$selectedLeId] ?? [];

			$selected_transform = apply_form_voice_override_to_transform($selected_transform, $selectedOverrides);

			$base = generate_sentence_base_from_japanese($selected_transform, $int_selected_language);

			if ($hasAnyNoOverride) {
				$prefix = '';
				$out = $base;
				$suffix = '';
			} else {
				list($prefix, $out, $suffix) = apply_override_infomation($base, $selectedOverrides, $isHighlightedLayer);
			}

            $out_for_display = $out;
            if ($isHighlightedLayer && $isHighlightedElement) {
                $out_for_display = '<span class="wiseGrammarParticle">' . $out . '</span>';
            }

            $segment_html = $prefix . $out_for_display . $suffix;
            $segment_plain = html_entity_decode(strip_tags($prefix . $out . $suffix), ENT_QUOTES | ENT_HTML5, 'UTF-8');

            $result_sentence .= $segment_html;
            $result_sentence_plain .= $segment_plain;
        }
    }

    return [
        'item_title' => $result_sentence,
        'item_plain_sentence' => $result_sentence_plain,
        'item_unique_code' => $highlighted_unique_code,
        'item_japanese' => $highlighted_japanese
    ];
}


function get_arr_wise_navigation_waypoints($t_wise_navigation_id, $int_selected_language){
	
	global
		$t_wise_navigations,
		$t_wise_navigation_waypoints;

	$arr_strSQL_select = [
		[$t_wise_navigation_waypoints,'id'],
		[$t_wise_navigation_waypoints,'unique_code'],
		[$t_wise_navigation_waypoints,'title']
	];

	$strSQL_from = "
			FROM $t_wise_navigations
			INNER JOIN $t_wise_navigation_waypoints
				ON $t_wise_navigations.id = $t_wise_navigation_waypoints.wise_navigation_id";

	$arr_strSQL_where = [
		[
			[
				[$t_wise_navigations,'id','=',$t_wise_navigation_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_wise_navigations,'sort','ASC'],
		[$t_wise_navigation_waypoints,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_wise_navigation_waypoints) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_wise_navigation_waypoints;
}


function get_arr_wise_navigation_scripts($target_id, $int_selected_language){
	
	global
		$t_wise_navigation_scripts,
		$t_masta_wise_navigation_script,
		$arr_columns_wise_navigation_script_message,
		$arr_columns_wise_navigation_script_message_template;

	$arr_strSQL_select = [
        [$t_wise_navigation_scripts, 'id'],
        [$t_wise_navigation_scripts, 'unique_code'],
        [$t_wise_navigation_scripts, 'script_type_id'],
        [$t_wise_navigation_scripts, $arr_columns_wise_navigation_script_message[$int_selected_language] . ' AS script_message'],
        [$t_masta_wise_navigation_script, $arr_columns_wise_navigation_script_message_template[$int_selected_language] . ' AS script_message_template']
    ];

	$strSQL_from = "
			FROM $t_wise_navigation_scripts
			INNER JOIN $t_masta_wise_navigation_script
				ON $t_wise_navigation_scripts.script_type_id = $t_masta_wise_navigation_script.id";

    $arr_strSQL_where = [
        [
            [
                [$t_wise_navigation_scripts, 'wise_navigation_waypoint_id', '=', $target_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
		[$t_wise_navigation_scripts,'sort','ASC']
	];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_wise_navigation_scripts) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_wise_navigation_scripts;
}


function get_arr_wise_navigation_items($target_id, $int_selected_language){
	
	global
	    $t_wise_navigation_items;

	$arr_strSQL_select = [
        [$t_wise_navigation_items, 'id'],
        [$t_wise_navigation_items, 'unique_code'],
        [$t_wise_navigation_items, 'layer_id'],
        [$t_wise_navigation_items, 'is_new']
    ];

    $strSQL_from = "
            FROM $t_wise_navigation_items";

    $arr_strSQL_where = [
        [
            [
                [$t_wise_navigation_items, 'wise_navigation_script_id', '=', $target_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
		[$t_wise_navigation_items,'sort','ASC']
	];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_wise_navigation_items) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_wise_navigation_items;
}







function get_data_wise_map_view(
	$int_selected_language,
	$user_id = null, $map_type = '',
	$target_id = null,
	$target_ids = [],
	$is_direct = true
)
{

    $map_data = switch_get_data_wise_map($map_type, $target_id, $target_ids, $is_direct, $int_selected_language);
    if (empty($map_data)) {
        return [
            'goal_html' => '',
            'waypoints_html' => ''
        ];
    }

    $goal_html = '';
    $waypoints_html = '';
	$extra = [];

	switch($map_type) {
        case 'user_goal':
            break;

        case 'milestone':
            break;

        case 'lesson_goal':
        case 'lesson_goal_from_ids':
        case 'lesson_goal_with_usages_for_debug':
			$html_parts = build_html_wise_map_components($map_data, [
				'goal_class' => 'wiseMapGoal wiseUiFontSizeTarget',
				'goal_icon_class' => 'iconGoal',
				'waypoint_class' => 'wiseMapWaypoint',
				'waypoint_title_class' => 'waypointTitle',
				'toggle_container_class' => 'waypointToggleButtonContainer',
				'toggle_button_class' => 'waypointToggleButton',
				'toggle_label_class' => 'waypointToggleLabel',
				'action' => 'map:lesson:toggle',
				'id_prefix' => 'lesson-'
			]);
			$goal_html = $html_parts['goal_html'];
			$waypoints_html = $html_parts['waypoints_html'];
            break;
			
		case 'lesson_step_goal':
        case 'lesson_step_goal_from_ids':
			$html_parts = build_html_wise_map_components($map_data, [
				'goal_class' => 'wiseMapGoal wiseUiFontSizeTarget',
				'goal_icon_class' => 'iconGoal',
				'waypoint_class' => 'wiseMapWaypoint',
				'waypoint_title_class' => 'waypointTitle',
				'toggle_container_class' => 'waypointToggleButtonContainer',
				'toggle_button_class' => 'waypointToggleButton',
				'toggle_label_class' => 'waypointToggleLabel',
				'action' => 'map:lessonStep:toggle',
				'id_prefix' => 'lessonStep-'
			]);
			$goal_html = $html_parts['goal_html'];
			$waypoints_html = $html_parts['waypoints_html'];
            break;

        case 'focus_point':
			$html_parts = build_html_wise_map_components($map_data, [
				'goal_class' => 'wiseMapGoal wiseUiFontSizeTarget',
				'goal_icon_class' => 'iconGoal',
				'waypoint_class' => 'wiseMapWaypoint',
				'waypoint_title_class' => 'waypointTitle',
				'toggle_container_class' => 'waypointToggleButtonContainer',
				'toggle_button_class' => 'waypointToggleButton',
				'toggle_label_class' => 'waypointToggleLabel',
				'action' => 'map:focusPoint:toggle',
				'id_prefix' => 'waypoint-'
			]);
			$goal_html = $html_parts['goal_html'];
			$waypoints_html = $html_parts['waypoints_html'];
			$extra = [
				'tasks' => $map_data['tasks'] ?? [],
				'usages' => $map_data['usages'] ?? []
			];
			break;

        default:
            throw new Exception('Invalid map type');
    }

	return array_merge([
		'goal_html' => $goal_html,
		'waypoints_html' => $waypoints_html,
		'waypoint_data' => $map_data['route_data'] ?? []
	], $extra);
}
