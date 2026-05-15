<?php

/******************************************************
 *  PAGE
 *  
 ******************************************************/

function build_html_manage_rooms_page($int_selected_language){
	
	global
		$t_rooms,
		$path_manage_room_lessons,
		$arr_str_placeholder_room_name;

	$str_html = '';
	
	$url_manage_room_lessons = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_manage_room_lessons, '/'))
	);

	$current_user = wp_get_current_user();
	$current_user_id = $current_user->ID;

	$arr_strSQL_select = [
		[$t_rooms,'id'],
		[$t_rooms,'unique_code'],
		[$t_rooms,'room_name'],
		[$t_rooms,'room_type'],
		[$t_rooms,'is_published']
	];

	$strSQL_from = ' FROM ' .$t_rooms;

	$arr_strSQL_where = [
		[
			[
				[$t_rooms,'room_owner_user_id','=',$current_user_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_rooms,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_rooms) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	$manage_target = 'room';

	$str_html = build_html_manage_targets(
		$manage_target,
		$arr_rooms,
		'unique_code',
		'room_name',
		$url_manage_room_lessons,
		$arr_str_placeholder_room_name[$int_selected_language],
		$int_selected_language
	);

	return $str_html;

}


function build_html_manage_room_lessons_page($int_selected_language){

	global
		$t_room_lessons,
		$t_rooms,
		$path_manage_room_lesson_steps,
		$arr_str_placeholder_lesson_name;

	$str_html = '';
		
	$url_manage_room_lesson_steps = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_manage_room_lesson_steps, '/'))
	);

	$unique_code = escape_html($_GET['unique_code'] ?? '');
	$room_id = fetch_room_id_from_unique_code($unique_code, $int_selected_language);

	$current_user = wp_get_current_user();
	$current_user_id = $current_user->ID;

	$arr_strSQL_select = [
		[$t_room_lessons,'id'],
		[$t_room_lessons,'unique_code'],
		[$t_room_lessons,'title'],
		[$t_room_lessons,'learning_status'],
		[$t_rooms,'room_name']
	];

	$strSQL_from = " FROM
					$t_rooms
					INNER JOIN $t_room_lessons
					ON
					$t_rooms.id = $t_room_lessons.room_id 
					";

	$arr_strSQL_where = [
		[
			[
				[$t_rooms,'id','=',$room_id,'PDO::PARAM_INT','And'],
				[$t_rooms,'room_owner_user_id','=',$current_user_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_room_lessons,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_lessons) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	$manage_target = 'lesson';

	$str_html = build_html_manage_targets(
		$manage_target,
		$arr_lessons,
		'unique_code',
		'title',
		$url_manage_room_lesson_steps,
		$arr_str_placeholder_lesson_name[$int_selected_language],
		$int_selected_language
	);

	$str_html_multicopy_lessons = build_html_multicopy_lessons_from_teaching_materials_section($int_selected_language);

	
	$str_html_header = '';
	
	if(empty($arr_lessons)){

		$arr_strSQL_select = [
			[$t_rooms,'id'],
			[$t_rooms,'unique_code'],
			[$t_rooms,'room_name']
		];
	
		$strSQL_from = ' FROM ' .$t_rooms;
	
		$arr_strSQL_where = [
			[
				[
					[$t_rooms,'id','=',$room_id,'PDO::PARAM_INT','']
				],
				''
			]
		];
	
		$arr_strSQL_order = [
			[$t_rooms,'sort','ASC']
		];
	
		$strSQL_option = '';
	
		list($pdo_has_error, $select_has_error, $e, $arr_rooms) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
		handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

		if(!empty($arr_lesson_steps)){
			$str_html_header = '<h1>「'.$arr_rooms[INDEX_FIRST]['room_name'].'」</h1>';
		}
	}
	else{
		$str_html_header = '<h1>「'.$arr_lessons[INDEX_FIRST]['room_name'].'」</h1>';
	}

	$str_html = $str_html_header.$str_html.$str_html_multicopy_lessons;

	return $str_html;

}


function build_html_manage_room_lesson_steps_page($int_selected_language){

	global
		$t_room_lesson_steps,
		$t_room_lessons,
		$t_rooms,
		$path_manage_room_lesson_step_units,
		$arr_str_placeholder_lesson_step_name;

	$str_html = '';

	$url_manage_room_lesson_step_units = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_manage_room_lesson_step_units, '/'))
	);

	$unique_code = escape_html($_GET['unique_code'] ?? '');
	$lesson_id = fetch_lesson_id_from_unique_code($unique_code, $int_selected_language);

	$current_user = wp_get_current_user();
	$current_user_id = $current_user->ID;

	$arr_strSQL_select = [
		[$t_room_lesson_steps,'id'],
		[$t_room_lesson_steps,'unique_code'],
		[$t_room_lesson_steps,'step_name'],
		[$t_room_lessons,'title'],
		[$t_rooms,'room_name']
	];

	$strSQL_from = " FROM
					(
						$t_rooms
						INNER JOIN $t_room_lessons
						ON
						$t_rooms.id = $t_room_lessons.room_id 
					)
					INNER JOIN $t_room_lesson_steps
					ON
					$t_room_lessons.id = $t_room_lesson_steps.lesson_id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_room_lessons,'id','=',$lesson_id,'PDO::PARAM_INT','And'],
				[$t_rooms,'room_owner_user_id','=',$current_user_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_room_lesson_steps,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_lesson_steps) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	$manage_target = 'lesson_step';

	$str_html = build_html_manage_targets(
		$manage_target,
		$arr_lesson_steps,
		'unique_code',
		'step_name',
		$url_manage_room_lesson_step_units,
		$arr_str_placeholder_lesson_step_name[$int_selected_language],
		$int_selected_language
	);

	$str_html_header = '';
	if(!empty($arr_lesson_steps)){
		$str_html_header = '<h1>「'.$arr_lesson_steps[INDEX_FIRST]['room_name'].'」'.$arr_lesson_steps[INDEX_FIRST]['title'].'</h1>';
	}
	$str_html = $str_html_header.$str_html;

	return $str_html;

}


function build_html_manage_room_lesson_step_units_page($int_selected_language){
	
	global
		$t_room_lesson_step_units,
		$t_room_lesson_steps,
		$t_room_lessons,
		$t_rooms,
		$path_manage_room_lesson_contents,
		$arr_str_placeholder_lesson_step_unit_name;

	$str_html = '';

	$url_manage_room_lesson_contents = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_manage_room_lesson_contents, '/'))
	);

	$unique_code = escape_html($_GET['unique_code'] ?? '');
	$lesson_step_id = fetch_lesson_step_id_from_unique_code($unique_code, $int_selected_language);

	$current_user = wp_get_current_user();
	$current_user_id = $current_user->ID;

	$arr_strSQL_select = [
		[$t_room_lesson_step_units,'id'],
		[$t_room_lesson_step_units,'unique_code'],
		[$t_room_lesson_step_units,'unit_type'],
		[$t_room_lesson_steps,'step_name'],
		[$t_room_lessons,'title'],
		[$t_rooms,'room_name']
	];

	$strSQL_from = " FROM
					(
						(
							$t_rooms
							INNER JOIN $t_room_lessons
							ON
							$t_rooms.id = $t_room_lessons.room_id 
						)
						INNER JOIN $t_room_lesson_steps
						ON
						$t_room_lessons.id = $t_room_lesson_steps.lesson_id
					)
					INNER JOIN $t_room_lesson_step_units
					ON
					$t_room_lesson_steps.id = $t_room_lesson_step_units.lesson_step_id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_room_lesson_steps,'id','=',$lesson_step_id,'PDO::PARAM_INT','And'],
				[$t_rooms,'room_owner_user_id','=',$current_user_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_room_lesson_step_units,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_lesson_step_units) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	$manage_target = 'lesson_step_unit';

	$str_html = build_html_manage_targets(
		$manage_target, $arr_lesson_step_units,
		'unique_code',
		'unit_type',
		$url_manage_room_lesson_contents,
		$arr_str_placeholder_lesson_step_unit_name[$int_selected_language],
		$int_selected_language
	);

	$str_html_header = '';
	if(!empty($arr_lesson_step_units)){
		$str_html_header = '<h1>「'.$arr_lesson_step_units[INDEX_FIRST]['room_name'].'」'.$arr_lesson_step_units[INDEX_FIRST]['title'].'</h1>';
	}
	$str_html = $str_html_header.$str_html;

	return $str_html;

}


function build_html_manage_room_lesson_contents_page($int_selected_language){
	
	global
		$t_room_lesson_step_units,
		$t_room_lesson_steps,
		$t_room_lessons,
		$t_rooms;

	$str_html = '';

	$unique_code = escape_html($_GET['unique_code'] ?? '');
	$step_unit_id = fetch_lesson_step_unit_id_from_unique_code($unique_code, $int_selected_language);

	$current_user = wp_get_current_user();
	$current_user_id = $current_user->ID;

	$arr_strSQL_select = [
		[$t_room_lesson_step_units,'id'],
		[$t_room_lesson_step_units,'unique_code'],
		[$t_room_lesson_step_units,'unit_type'],
		[$t_room_lesson_steps,'step_name'],
		[$t_room_lessons,'title'],
		[$t_rooms,'room_name']
	];

	$strSQL_from = " FROM
					(
						(
							$t_rooms
							INNER JOIN $t_room_lessons
							ON
							$t_rooms.id = $t_room_lessons.room_id 
						)
						INNER JOIN $t_room_lesson_steps
						ON
						$t_room_lessons.id = $t_room_lesson_steps.lesson_id
					)
					INNER JOIN $t_room_lesson_step_units
					ON
					$t_room_lesson_steps.id = $t_room_lesson_step_units.lesson_step_id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_room_lesson_step_units,'id','=',$step_unit_id,'PDO::PARAM_INT','And'],
				[$t_rooms,'room_owner_user_id','=',$current_user_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_room_lesson_steps,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_lesson_step_units) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);
	
	$str_html_header = '';
	if(!empty($arr_lesson_step_units)){
		$str_html_header = '<h1>'.$arr_lesson_step_units[INDEX_FIRST]['room_name'].' '.$arr_lesson_step_units[INDEX_FIRST]['title'].' '.$arr_lesson_step_units[INDEX_FIRST]['step_name'].' '.$arr_lesson_step_units[INDEX_FIRST]['unit_type'].'</h1>';
	}
	
	$str_html_add_contents = build_html_manage_room_modal_ui_add_contents($int_selected_language);
	$str_html_selected_contents = build_html_manage_room_modal_ui_selected_contents($int_selected_language);
	
	$str_html_body = '<div id="manageLessonContentsBody" class="wise-require-fullscreen">'.$str_html_header.$str_html_add_contents.$str_html_selected_contents.'</div>';
	$str_html = '<section>'.$str_html_body.'</section>';

	return $str_html;

}


function build_html_manage_room_bookmarks_page($int_selected_language){

	global
		$t_rooms,
		$arr_columns_masta_japanese_sub_category,
		$str_option_value_select_all,
		$arr_bookmark_filter;

	$unique_code = escape_html($_GET['unique_code'] ?? '');
	$room_id = fetch_room_id_from_unique_code($unique_code, $int_selected_language);

	$current_user = wp_get_current_user();
	$current_user_id = $current_user->ID;

	$arr_strSQL_select = [
		[$t_rooms,'id'],
		[$t_rooms,'unique_code'],
		[$t_rooms,'room_name']
	];

	$strSQL_from = ' FROM ' .$t_rooms;

	$arr_strSQL_where = [
		[
			[
				[$t_rooms,'id','=',$room_id,'PDO::PARAM_INT','And'],
				[$t_rooms,'room_owner_user_id','=',$current_user_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_rooms,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_rooms) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	$str_html_header = '';
	if(!empty($arr_rooms)){
		$str_html_header = '<h1>「'.$arr_rooms[INDEX_FIRST]['room_name'].'」</h1>';
	}


	$arr_masta_japanese_sub_category = fetch_arr_masta_japanese_sub_categories_for_grammar($int_selected_language);

	$html_dropdown_menu_options = '';
	foreach($arr_masta_japanese_sub_category as $loop_masta_japanese_sub_category){
		$int_option_value = escape_html_with_nl2br($loop_masta_japanese_sub_category['id']);
		$str_option_text_content = escape_html_with_nl2br($loop_masta_japanese_sub_category[$arr_columns_masta_japanese_sub_category[$int_selected_language]]);
		$html_dropdown_menu_options =
		$html_dropdown_menu_options.'<option value="'.$int_option_value.'">'.$str_option_text_content.'</option>';
	}

	$html_dropdown_menu_options = '<option value="'.SELECT_ALL.'">'.$str_option_value_select_all.'</option>'.$html_dropdown_menu_options;
	$html_dropdown_menu = '
	<div id="manageRoomBookmarksDropDownMenuArea">
		<select id="manageRoomBookmarksSelectId" name="manageRoomBookmarksSelectId">'.
		$html_dropdown_menu_options.
		'</select>
	</div>';

	foreach ($arr_bookmark_filter as $status) {
		$title = escape_html($status['title'][$int_selected_language]);
		$id_class = escape_html($status['html_id_class']);
	
		$str_html .= '
			<div class="manageRoomBookmarksBox" id="' . $id_class . '">
				<h5>' . $title . '</h5>
				<ul id="manageRoomBookmarksBoxList' . ucfirst($id_class) . '" class="manageRoomBookmarksBoxUl"></ul>
			</div>';
	}

	$str_html = '
	<div id="manageRoomBookmarksBody">'.
		$str_html_header.
		$html_dropdown_menu.'
		<div class="manageRoomBookmarksBoxiesContainer">'.
			$str_html.'
		</div>
	</div>';

	return $str_html;

}


function build_html_apply_for_classroom_page($int_selected_language){

    global
        $int_Basic_Student,
        $int_Plus_Student,
        $int_Premium_Student,
        $int_VIP_Student,
        $t_room_users,
        $int_color_blue,
        $int_rgb_r_deep,
        $int_rgb_g_deep,
        $int_rgb_b_deep,
        $str_class_fixed_font,
        $path_apply_for_classroom_confirm,
        $int_unique_code_max_length,
        $arr_str_placeholder_room_unique_code,
        $arr_str_button_caption_submit,
        $arr_str_apply_for_classroom_title,
        $arr_str_apply_for_classroom_already_applied;

    $html = '';

    $current_user = wp_get_current_user();
    $current_user_id = $current_user->ID;
    $user_level = get_user_level();
	
	$url_apply_for_classroom_confirm = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_apply_for_classroom_confirm, '/'))
	);

    $int_allow_max_lesson_amount = 0;

    switch (true) {
        case ($user_level === $int_Basic_Student):
            $int_allow_max_lesson_amount = 0;
            break;
        case ($user_level === $int_Plus_Student):
            $int_allow_max_lesson_amount = 0;
            break;
        case ($user_level === $int_Premium_Student):
            $int_allow_max_lesson_amount = 1;
            break;
        case ($user_level >= $int_VIP_Student):
            $int_allow_max_lesson_amount = 1;
            break;
        default:
            $int_allow_max_lesson_amount = 1;
            break;
    }

    // 1) 未承認申請がある場合は「取消ボタン」を表示して終了
    $arr_pending_applications = fetch_arr_pending_room_users_by_user_id($current_user_id, $int_selected_language);

    if (count($arr_pending_applications) > 0) {

        $button_color = $int_color_blue;
        $button_background_color = "rgb( $int_rgb_r_deep[$button_color], $int_rgb_g_deep[$button_color], $int_rgb_b_deep[$button_color]);";

        $html_cancel = '';

        foreach ($arr_pending_applications as $row) {

            $room_user_id = (int)$row['id'];

            $html_cancel .= '
            <form action="' . $url_apply_for_classroom_confirm . '" method="POST" class="jwsCancelApplicationForm">
                ' . wp_nonce_field('cancel_room_application', 'cancel_room_application_nonce', true, false) . '
                <input type="hidden" name="process_type" value="cancel">
                <input type="hidden" name="room_user_id" value="' . $room_user_id . '">
                <div class="divChoices">
                    <input
                        class="inputChoices"
                        style="background-color: ' . escape_html_with_nl2br($button_background_color) . '"
                        type="submit"
                        value="申請を取り消す"
                    >
                </div>
            </form>
            ';
        }

        $html = '
        <section class="sectionStandard">
            <h3>' . $arr_str_apply_for_classroom_title[$int_selected_language] . '</h3>
            <p>' . escape_html_with_nl2br($arr_str_apply_for_classroom_already_applied[$int_selected_language]) . '</p>
            ' . $html_cancel . '
        </section>
        ';

        return $html;
    }

    // 2) 申請数チェック → 申請フォーム表示
    $arr_lesson_amount_checker = fetch_arr_room_users_from_user_id($current_user_id, $int_selected_language);

    if (count($arr_lesson_amount_checker) < $int_allow_max_lesson_amount) {

        $button_color = $int_color_blue;
        $button_background_color = "rgb( $int_rgb_r_deep[$button_color], $int_rgb_g_deep[$button_color], $int_rgb_b_deep[$button_color]);";

        $html_form = '
        <form action="' . $url_apply_for_classroom_confirm . '" method="POST">
            ' . wp_nonce_field('apply_for_classroom', 'apply_for_classroom_nonce', true, false) . '
            <input type="hidden" name="process_type" value="apply">
            <div class="divInputBox">
                <input 
                    class="inputBox ' . $str_class_fixed_font . '" 
                    name="room_unique_code" 
                    type="text" 
                    maxlength="' . $int_unique_code_max_length . '" 
                    placeholder="' . escape_html_with_nl2br($arr_str_placeholder_room_unique_code[$int_selected_language]) . '" 
                    required
                >
            </div>
            <div class="divChoices">
                <input 
                    class="inputChoices" 
                    style="background-color: ' . escape_html_with_nl2br($button_background_color) . '" 
                    type="submit" 
                    value="' . escape_html_with_nl2br($arr_str_button_caption_submit[$int_selected_language]) . '"
                >
            </div>
        </form>
        ';

        $html = '
        <section class="sectionStandard">
            <h3>' . $arr_str_apply_for_classroom_title[$int_selected_language] . '</h3>
            ' . $html_form . '
        </section>
        ';

        return $html;
    }

    $html = $arr_str_apply_for_classroom_already_applied[$int_selected_language];
    return $html;
}


function build_html_apply_for_classroom_confirm_page($int_selected_language){

    global
        $t_room_users,
        $arr_str_apply_for_classroom_application_errored,
        $arr_str_apply_for_classroom_application_submitted,
        $arr_str_apply_for_classroom_already_applied;

    // 任意：取消用メッセージがあれば使う（無ければ既存の errored/submitted を流用）
    global
        $arr_str_apply_for_classroom_application_canceled,
        $arr_str_apply_for_classroom_cancel_errored;

    $html = '';

    // ------------------------------------------------------------
    // 「申請 / 取消」の分岐（←ご質問2の答え）
    // ------------------------------------------------------------
    $process_type = isset($_POST['process_type']) ? sanitize_text_field((string)$_POST['process_type']) : 'apply';

    $current_user = wp_get_current_user();
    $current_user_id = (int)$current_user->ID;

    // ------------------------------------------------------------
    // cancel: 未承認（confirmed = 0）のみ取り消し
    // ------------------------------------------------------------
    if ($process_type === 'cancel') {

        if (
            !isset($_POST['cancel_room_application_nonce']) ||
            !wp_verify_nonce($_POST['cancel_room_application_nonce'], 'cancel_room_application')
        ) {
            $html = $arr_str_apply_for_classroom_cancel_errored[$int_selected_language]
                ?? $arr_str_apply_for_classroom_application_errored[$int_selected_language];
            return $html;
        }

        $room_user_id = isset($_POST['room_user_id']) ? (int)$_POST['room_user_id'] : 0;

        if ($room_user_id <= 0) {
            $html = $arr_str_apply_for_classroom_cancel_errored[$int_selected_language]
                ?? $arr_str_apply_for_classroom_application_errored[$int_selected_language];
            return $html;
        }

        $target_table = $t_room_users;

        // 本人の未承認申請だけ削除（SQL側で担保）
        $str_deleteSQL = 'id = ? AND user_id = ? AND confirmed = ?';

        $arr_values = [
            [$room_user_id, 'PDO::PARAM_INT'],
            [$current_user_id, 'PDO::PARAM_INT'],
            [0, 'PDO::PARAM_INT']
        ];

        list($pdo_has_error, $delete_has_error, $e) = execute_delete_data($target_table, $str_deleteSQL, $arr_values);
        handle_database_error_and_redirect($pdo_has_error, $delete_has_error, $e, $int_selected_language);

        $html = $arr_str_apply_for_classroom_application_canceled[$int_selected_language]
            ?? $arr_str_apply_for_classroom_application_submitted[$int_selected_language];

        return $html;
    }

    // ------------------------------------------------------------
    // apply: 入室申請
    // ------------------------------------------------------------
    if (
        !isset($_POST['apply_for_classroom_nonce']) ||
        !wp_verify_nonce($_POST['apply_for_classroom_nonce'], 'apply_for_classroom')
    ) {
        $html = $arr_str_apply_for_classroom_application_errored[$int_selected_language];
        return $html;
    }

    $unique_code = isset($_POST['room_unique_code']) ? sanitize_text_field((string)$_POST['room_unique_code']) : '';

    if ($unique_code === '') {
        $html = $arr_str_apply_for_classroom_application_errored[$int_selected_language];
        return $html;
    }

    $arr_rooms = fetch_arr_rooms_from_unique_code($unique_code, $int_selected_language);

    if (empty($arr_rooms)) {
        $html = $arr_str_apply_for_classroom_application_errored[$int_selected_language];
        return $html;
    }

    $int_room_id = (int)$arr_rooms[INDEX_FIRST]['id'];

	global
		$int_color_blue,
		$int_rgb_r_deep,
		$int_rgb_g_deep,
		$int_rgb_b_deep;

	$button_background_color = 'rgb('
		. $int_rgb_r_deep[$int_color_blue] . ', '
		. $int_rgb_g_deep[$int_color_blue] . ', '
		. $int_rgb_b_deep[$int_color_blue] . ')';

    if (!check_room_user_exists($int_room_id, $current_user_id, $int_selected_language)) {

        $arr_insertSQL = [
            ['room_id','?',$int_room_id,'PDO::PARAM_INT'],
            ['user_id','?',$current_user_id,'PDO::PARAM_INT'],
            ['confirmed','?',FLAG_FALSE,'PDO::PARAM_INT']
        ];

        list($pdo_has_error, $insert_has_error, $e, $last_insert_id) = execute_insert_data($t_room_users, $arr_insertSQL);

        handle_database_error_and_redirect($pdo_has_error, $insert_has_error, $e, $int_selected_language);

        // $html = $arr_str_apply_for_classroom_application_submitted[$int_selected_language];


		$html = $arr_str_apply_for_classroom_application_submitted[$int_selected_language];
		$html .= build_html_form_button_return_to_home($button_background_color, $int_selected_language);

        return $html;
    }

    $html = $arr_str_apply_for_classroom_already_applied[$int_selected_language];
	$html .= build_html_form_button_return_to_home($button_background_color, $int_selected_language);
	
    return $html;
}


function build_html_manage_room_invite_page($int_selected_language){

	$html = '';
	$unique_code = escape_html($_GET['unique_code'] ?? '');

	$arr_rooms = fetch_arr_rooms_from_unique_code($unique_code, $int_selected_language);

	// デバッグ tempo
	echo 'room_invite';
	debug('', $arr_rooms);
	// デバッグ tempo

	return $html;
}

function build_html_manage_room_requests_page($int_selected_language){

	global
		$arr_str_apply_for_classroom_application_errored,
		$str_update,
		$str_class_fixed_font;

	$html = '';
	$unique_code = escape_html($_GET['unique_code'] ?? '');

	$arr_rooms = fetch_arr_rooms_from_unique_code($unique_code, $int_selected_language);
	if (empty($arr_rooms)) {
		$html = $arr_str_apply_for_classroom_application_errored[$int_selected_language];
		return $html;
	}

	ensure_permission_room($int_selected_language);

	$int_room_id = intval($arr_rooms[INDEX_FIRST]['id']);
	$arr_room_users = fetch_arr_room_users_from_room_id($int_room_id, $int_selected_language);

	$arr_user_ids = [];
	foreach ($arr_room_users as $ru) {
		$arr_user_ids[] = intval($ru['user_id']);
	}
	$arr_users_map = fetch_arr_users_by_ids($arr_user_ids);

	$room_name = escape_html($arr_rooms[INDEX_FIRST]['room_name'] ?? '');
	$label_update = $str_update[$int_selected_language] ?? 'update';

	$html .= '
	<section class="sectionStandard">
		<h3>Classroom Requests</h3>
		<p class="'.$str_class_fixed_font.'">Room: '.$room_name.'</p>
	</section>';

	if (empty($arr_room_users)) {
		$html .= '<section class="sectionStandard"><p>申請はありません。</p></section>';
		return $html;
	}

	// ★ ここが重要：ボタンを editSectionContainer の中に入れる
	$html .= '
	<section class="sectionStandard">
		<div class="editSectionContainer">

			<div class="roomContentsSectionUpdateContainer">
				<button
					id="manageSectionUpdateAllButton"
					class="roomContentsSectionUpdateAllButton"
					data-manage-target="room_user"
				>'.escape_html($label_update).'</button>
			</div>

			<ul class="roomRequestsList">';

	foreach ($arr_room_users as $idx => $ru) {

		$room_user_id = intval($ru['id']);
		$user_id = intval($ru['user_id']);
		$confirmed = intval($ru['confirmed']) === 1 ? 1 : 0;

		$nickname = $arr_users_map[$user_id]['nickname'] ?? ('user#' . $user_id);

		$selected0 = $confirmed === 0 ? ' selected' : '';
		$selected1 = $confirmed === 1 ? ' selected' : '';

		$html .= '
				<li class="roomRequestsItem" data-room-user-id="'.intval($room_user_id).'">
					<div class="roomRequestsNickname">'.escape_html($nickname).'</div>

					<div class="roomRequestsConfirmed">
						<select
							class="editableElement roomContentsConfirmedSelect"
							data-id="'.intval($room_user_id).'"
							data-column="confirmed"
							data-original="'.($confirmed === 1 ? '1' : '0').'">
							<option value="0"'.$selected0.'>pending</option>
							<option value="1"'.$selected1.'>confirmed</option>
						</select>
					</div>
				</li>';
	}

	$html .= '
			</ul>
		</div>
	</section>';

	return $html;
}


/******************************************************
 *  ITEMS
 *  
 ******************************************************/
function build_html_multicopy_lessons_from_teaching_materials_section($int_selected_language){
	
	global
		$t_teaching_material_sets,
		$arr_columns_masta_teaching_material_sets,
		$t_teaching_material_levels,
		$arr_columns_masta_teaching_material_levels,
		$t_teaching_material_lessons,
		$arr_columns_masta_teaching_material_lessons,
		$arr_str_button_caption_submit;

	$str_html = '';

	$arr_strSQL_select = [
		[$t_teaching_material_sets,$arr_columns_masta_teaching_material_sets[$int_selected_language]],
		[$t_teaching_material_levels,$arr_columns_masta_teaching_material_levels[$int_selected_language]],
		[$t_teaching_material_lessons,$arr_columns_masta_teaching_material_lessons[$int_selected_language]],
		[$t_teaching_material_lessons,'id']
	];
	
	$strSQL_from = " FROM
					(
						$t_teaching_material_sets
						INNER JOIN $t_teaching_material_levels
						ON
						$t_teaching_material_sets.id = $t_teaching_material_levels.set_id
					)
					INNER JOIN $t_teaching_material_lessons
					ON
					$t_teaching_material_levels.id = $t_teaching_material_lessons.level_id
					";
	
	$arr_strSQL_where = [
		[
			[
				[$t_teaching_material_sets,'is_published','=',FLAG_TRUE,'PDO::PARAM_INT','']
			],
			''
		]
	];
	
	$arr_strSQL_order = [
		[$t_teaching_material_sets,'sort','ASC'],
		[$t_teaching_material_levels,'sort','ASC'],
		[$t_teaching_material_lessons,'sort','ASC']
	];
	
	$strSQL_option = '';
	
	list($pdo_has_error, $select_has_error, $e, $arr_teaching_material_sets) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	$html_dropdown_menu_options = '';
	
	foreach($arr_teaching_material_sets as $loop_teaching_material_sets){
		$int_option_value = escape_html_with_nl2br($loop_teaching_material_sets['id']);
		$str_option_text_content = escape_html_with_nl2br($loop_teaching_material_sets[$arr_columns_masta_teaching_material_sets[$int_selected_language]].' '.$loop_teaching_material_sets[$arr_columns_masta_teaching_material_levels[$int_selected_language]].' '.$loop_teaching_material_sets[$arr_columns_masta_teaching_material_lessons[$int_selected_language]]);
		$html_dropdown_menu_options =
		$html_dropdown_menu_options.'<option value="'.$int_option_value.'">'.$str_option_text_content.'</option>';
	}

	$html_dropdown_menu = '
	<div id="multicopyLessonsFromTeachingMaterialsDropDownMenuArea">
		<select id="multicopyLessonsFromTeachingMaterialsStartId" name="multicopyLessonsFromTeachingMaterialsStartId">'.
		$html_dropdown_menu_options.
		'</select>
		<select id="multicopyLessonsFromTeachingMaterialsEndId" name="multicopyLessonsFromTeachingMaterialsEndId">'.
		$html_dropdown_menu_options.
		'</select>
	</div>';

	$str_button ='<button id="multicopyLessonsFromTeachingMaterialsCopyButton">'.$arr_str_button_caption_submit[$int_selected_language].'</button>';
	$str_container = '<div class="copySectionContainer">'.$str_button.'</div>';
	$str_header = '<h2>MultiCopy Lessons</h2>';

	$str_section = $str_header.$str_html.$html_dropdown_menu.$str_container;
	$str_html = '<section class="sectionStandard">' . $str_section . '</section>';

	return $str_html;
}


function build_html_manage_room_modal_ui_add_contents($int_selected_language){

	global
		$arr_select_matching_type,
		$arr_select_japanese_category_id_search_lesson_contents;

	$str_html = '';
	$str_html_selecte_matching_type = '';
	$str_html_selecte_attribute = '';

	foreach($arr_select_matching_type as $key => $loop_matching_type){
		$int_option_value = $key;
		$str_option_text_content = escape_html_with_nl2br($loop_matching_type[$int_selected_language]);
		$str_html_selecte_matching_type =
		$str_html_selecte_matching_type.'<option value="'.$int_option_value.'">'.$str_option_text_content.'</option>';
	}

	foreach($arr_select_japanese_category_id_search_lesson_contents as $key => $loop_japanese_category_id_search_lesson_contents){
		$int_option_value = $key;
		$str_option_text_content = escape_html_with_nl2br($loop_japanese_category_id_search_lesson_contents[$int_selected_language]);
		$str_html_selecte_attribute =
		$str_html_selecte_attribute.'<option value="'.$int_option_value.'">'.$str_option_text_content.'</option>';
	}

	// マジックナンバー
	$str_html =
	'<div id="manageRoomModalUiAddContents" class="manageRoomModal lessonContentsLeftModal leftPositionModal manageRoomModalUiTopLevel manageRoomModal-open">
		<div class="manageRoomModalUiTitleContainer">
			<div class="manageRoomModalUiTitle">検索</div>
		</div>
		<div id="manageRoomModalUiAddContentsSearchArea" class="manageRoomModalUiSearchArea">
			<form id="manageRoomModalUiAddContentsSearchForm" class="manageRoomModalUiSearchForm">
				<input type="text" id="manageRoomModalUiAddContentsSearchInput" class="manageRoomModalUiSearchInput manageRoomModalUiTextInputArea" placeholder="検索ワードを入力">
			</form>
			<button id="manageRoomModalUiAddContentsSearchButton" class="manageRoomModalUiSearchButton">検索</button>
		</div>
		<div id="manageRoomModalUiAddContentsDropDownMenuArea">
			<select id="manageRoomModalUiAddContentsSelectMatchingType" name="manageRoomModalUiAddContentsSelectMatchingType">'.
			$str_html_selecte_matching_type.
			'</select>
			<select id="manageRoomModalUiAddContentsSelectAttribute" name="manageRoomModalUiAddContentsSelectAttribute">'.
			$str_html_selecte_attribute.
			'</select>
		</div>' .
		build_html_loading_spinner('manageRoomModalUiLoadingAddContents') .
		'<div class="manageRoomModalUiContents leftPositionModalContents">
			<ul id="manageRoomModalUiAddContentsUl" class="manageRoomModalUiSelectableList">
			</ul>
		</div>
	</div>';

	return $str_html;
}


function build_html_manage_room_modal_ui_selected_contents($int_selected_language){

	$str_html = '';

	// マジックナンバー
	$str_html =
	'<div id="manageRoomModalUiSelectedContents" class="manageRoomModal lessonContentsRightModal manageRoomModalUiTopLevel manageRoomModal-open">
		<div class="manageRoomModalUiTitleContainer">
			<div id="manageRoomModalUiSelectedContentsTitle" class="manageRoomModalUiTitle">リスト</div>
			<button id="manageRoomModalUiSelectedContentsConfirmButton" class="lessonContentsRightModalButtons">Exit</button>
		</div>
		<div class="manageRoomModalUiContents">
			<ul id="manageRoomModalUiSelectedContentsUl" class="manageRoomModalUiSelectableList"></ul>
		</div>
	</div>';

	return $str_html;
}


function get_arr_homework_items($arr_grammar_unique_code, $room_id, $int_selected_language) {

	global
		$str_snake_to_camel_grammar_unique_code,
		$str_snake_to_camel_japanese,
		$str_homework_method_inputData,
		$str_homework_method_activeRecall,
		$str_homework_method_registeredSentences;

	$arr_homework_items = [];

	foreach ($arr_grammar_unique_code as $item_grammar_unique_code) {
		$item = [];

		$item_grammar_unique_code = (string)$item_grammar_unique_code;
		$item[$str_snake_to_camel_grammar_unique_code] = $item_grammar_unique_code;

		$arr_masta_japanese_root = fetch_arr_masta_japanese_root_from_unique_code(
			$item_grammar_unique_code,
			$int_selected_language
		);

		if (empty($arr_masta_japanese_root)) {
            continue;
        }
		
		$t_masta_japanese_root_id = $arr_masta_japanese_root[INDEX_FIRST]['id'];
		$item[$str_snake_to_camel_japanese] = escape_html($arr_masta_japanese_root[INDEX_FIRST][$str_snake_to_camel_japanese]);

		$arr_room_user_input_data = fetch_arr_room_user_input_data($room_id, $t_masta_japanese_root_id, $int_selected_language);
		$item[$str_homework_method_inputData] = empty($arr_room_user_input_data) ? FLAG_FALSE : FLAG_TRUE;

		$arr_active_recall = fetch_arr_active_recall($t_masta_japanese_root_id, $int_selected_language);
		$item[$str_homework_method_activeRecall] = empty($arr_active_recall) ? FLAG_FALSE : FLAG_TRUE;

		$arr_registered_sentence = fetch_arr_registered_sentence_by_root_id($t_masta_japanese_root_id, $int_selected_language);
		$item[$str_homework_method_registeredSentences] = empty($arr_registered_sentence) ? FLAG_FALSE : FLAG_TRUE;

		$arr_homework_items[] = $item;
	}

	return $arr_homework_items;
}

function get_data_bookmarks(
    $int_search_scope,
    $room_unique_code,
    $int_bookmark_filter,
    $int_selected_language
){
    global
        $t_rooms,
        $t_user_bookmarks,
        $t_masta_japanese_root,
        $str_snake_to_camel_japanese,
        $arr_columns_masta_japanese_root,
        $str_column_root_kana,
        $str_snake_to_camel_kana,
        $str_snake_to_camel_root_example,
        $search_scope_current_user,
        $search_scope_room_members,
        $search_scope_room_owner_user;

    $current_user = wp_get_current_user();
    $current_user_id = (int)$current_user->ID;

    $final_room_id = null;
    $final_user_id = null;

    // === 検索スコープ確定 ===
    if ((int)$int_search_scope === $search_scope_current_user) {

        $final_room_id = null;
        $final_user_id = $current_user_id;

    } else {

        // current_user 以外は room_id が必須なので、ここで解決する
        $ctx = get_data_workshop_context($room_unique_code, $int_selected_language);

        if ($ctx['mode'] !== 'inRoom' || empty($ctx['room_found']) || $ctx['room_id'] === null) {
            respond_error('Room scope is not available in this mode.', 400);
            return [];
        }

        $final_room_id = (int)$ctx['room_id'];

        if ((int)$int_search_scope === $search_scope_room_members) {

            $final_user_id = null;

        } elseif ((int)$int_search_scope === $search_scope_room_owner_user) {

            $room_owner_user_id = (int)fetch_room_owner_user_id_from_room_id(
                $final_room_id,
                $int_selected_language
            );

            if ($room_owner_user_id <= 0) {
                respond_error('Room owner not found.', 404);
                return [];
            }

            $final_user_id = $room_owner_user_id;

        } else {
            respond_error('Invalid search scope.', 400);
            return [];
        }
    }

    $arr_strSQL_select = [
        [$t_user_bookmarks, 'id'],
        [$t_user_bookmarks, 'user_id'],
        [$t_user_bookmarks, 'room_id'],
        [$t_user_bookmarks, 'masta_japanese_root_id'],
        [$t_user_bookmarks, 'status'],
        [$t_user_bookmarks, 'deleted_at'],
        [$t_masta_japanese_root, 'unique_code'],
        [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language] . ' as ' . $str_snake_to_camel_japanese],
        [$t_masta_japanese_root, $str_column_root_kana . ' as ' . $str_snake_to_camel_kana],
        [$t_masta_japanese_root, 'root_example as ' . $str_snake_to_camel_root_example],
		[$t_rooms, 'room_name as room_name'],
    ];

	$strSQL_from = " FROM
					$t_user_bookmarks
					INNER JOIN $t_masta_japanese_root
						ON $t_user_bookmarks.masta_japanese_root_id = $t_masta_japanese_root.id
					LEFT JOIN $t_rooms
						ON $t_user_bookmarks.room_id = $t_rooms.id";

    $arr_strSQL_where = build_arr_where_user_bookmarks_scope(
        (int)$int_search_scope,
        $final_room_id,
        $final_user_id,
        (int)$int_bookmark_filter
    );

    if (empty($arr_strSQL_where)) {
        return [
            'search_scope' => (int)$int_search_scope,
            'room_id' => $final_room_id,
            'room_unique_code' => $room_unique_code,
            'user_id' => $final_user_id,
            'arr_user_bookmarks' => [],
            'map_grammar_unique_code' => []
        ];
    }

    $arr_strSQL_order = [
        [$t_user_bookmarks, 'id', 'ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_user_bookmarks)
        = execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            $strSQL_option
        );

    handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

	$map_grammar_unique_code = [];

	foreach ($arr_user_bookmarks as &$row) {

		// === map_grammar_unique_code 用（既存処理） ===
		$unique_code = (string)($row['unique_code'] ?? '');
		if ($unique_code !== '') {
			$map_grammar_unique_code[$unique_code] = [
				'status' => isset($row['status']) ? (int)$row['status'] : null,
				'deleted_at' => $row['deleted_at'] ?? null,
			];
		}

		// === Saved in: 用（今回追加） ===
		$room_id = (int)($row['room_id'] ?? 0);

		if ($room_id <= 0) {
			$row['bookmark_context_name'] = 'jws-workshop';
		} else {
			$row['bookmark_context_name'] = (string)($row['room_name'] ?? '');
		}
	}
	unset($row);

    return [
        'search_scope' => (int)$int_search_scope,
        'room_id' => $final_room_id,
        'room_unique_code' => $room_unique_code,
        'user_id' => $final_user_id,
        'arr_user_bookmarks' => $arr_user_bookmarks,
        'map_grammar_unique_code' => $map_grammar_unique_code
    ];
}


function build_html_bookmark_star(
    string $unique_id,
    string $grammar_unique_code,
    bool $is_bookmarked,
    ?string $room_unique_code = null
){
    $aria_pressed = $is_bookmarked ? 'true' : 'false';
    $active_class = $is_bookmarked ? ' isActive' : '';
    // $star_char = $is_bookmarked ? '★' : '☆';

    $data_room = ($room_unique_code !== null && $room_unique_code !== '')
        ? ' data-room-unique-code="' . escape_html($room_unique_code) . '"'
        : '';

    return
        '<button
            type="button"
            id="bookmarkStar_' . escape_html($unique_id) . '"
            class="bookmarkStar' . $active_class . '"
			data-action="bookmark:toggle"
            aria-pressed="' . $aria_pressed . '"
            data-grammar-unique-code="' . escape_html($grammar_unique_code) . '"' .
            $data_room . '
            title="ブックマーク"
        >
		</button>';
}
