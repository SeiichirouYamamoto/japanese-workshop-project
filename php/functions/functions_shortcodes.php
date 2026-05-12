<?php
/******************************************************
 *  HOME 
 *  
 ******************************************************/
add_shortcode('jws_home_page_shortcode', 'jws_home_page');
function jws_home_page(){

	global
		$str_quizButtonShowOtherQuestions,
		$str_globalVerticalToolbarSelectorButton_id,
		$str_globalVerticalToolbarOpenWiseButton_id,
		$str_globalVerticalToolbarManageRoomsButton_id,
		$str_globalVerticalToolbarLaserButton_id;

	$int_selected_language = jws_get_language_index();

	$html = '';
	$html .= build_html_welcome_user_section($int_selected_language);
	$html .= build_html_landing_page($int_selected_language);
	return $html;

}




/******************************************************
 *  VIP
 *  
 ******************************************************/
add_shortcode('jws_request_to_vip_page_shortcode', 'jws_request_to_vip_page');
function jws_request_to_vip_page() {

    $int_selected_language = jws_get_language_index();

    if (empty($_GET['token'])) {
        return;
    }

    $token = escape_html($_GET['token']);

    $html = build_html_request_to_vip_page($token, $int_selected_language);
    return $html;
}


add_shortcode('jws_vip_invites_page_shortcode', 'jws_vip_invites_page');
function jws_vip_invites_page() {

    $int_selected_language = jws_get_language_index();

    $user_level = get_user_level();
    if (!is_admin_level($user_level)) {
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        handle_post_vip_invites($int_selected_language);
    }

    $html = build_html_vip_invites_page($int_selected_language);
    return $html;
}


add_shortcode('jws_vip_requests_page_shortcode', 'jws_vip_requests_page');
function jws_vip_requests_page() {

    $int_selected_language = jws_get_language_index();

    $user_level = get_user_level();
    if (!is_admin_level($user_level)) {
        return;
    }

    $html = build_html_vip_requests_page($int_selected_language);
    return $html;
}



/******************************************************
 *  Dashboard
 *  
 ******************************************************/
add_shortcode('jws_dashboard_page_shortcode', 'jws_dashboard_page');
function jws_dashboard_page() {

	$int_selected_language = jws_get_language_index();

	$user_level = get_user_level();
	if (!is_member_level($user_level)) {
		return;
	}

	$ret = jws_require_single_session_for_page($int_selected_language);
	if (is_string($ret)) {
		return $ret;
	}

	$html = '';
	$html .= build_html_welcome_user_section($int_selected_language);
	$html .= build_html_dashboard_page($int_selected_language);

	
	if (is_teacher_level($user_level)) {
		global
			$str_globalVerticalToolbarSelectorButton_id,
			$str_globalVerticalToolbarOpenWiseButton_id,
			$str_globalVerticalToolbarManageRoomsButton_id,
			$str_globalVerticalToolbarLaserButton_id;

		$arr_targets_action = [
			$str_globalVerticalToolbarSelectorButton_id => true,
			$str_globalVerticalToolbarOpenWiseButton_id => true,
			$str_globalVerticalToolbarManageRoomsButton_id => true,
			$str_globalVerticalToolbarLaserButton_id => true
		];

		$html .= build_html_global_laser_pointer($arr_targets_action, $int_selected_language);
	}

	return $html;
}



/******************************************************
 *  GUEST
 *  
 ******************************************************/
add_shortcode('jws_grammar_point_guest_page_shortcode', 'jws_grammar_point_guest_page');
function jws_grammar_point_guest_page() {

	global
		$int_Free_Member,
		$allow_grammar_view_feature_capabilities_default,
		$allow_grammar_view_content_section_capabilities_default;

	$int_selected_language = jws_get_language_index();

	$request_method = 'GET';
	validate_request_method($request_method, $int_selected_language);

	if (isset($_GET['grammar_unique_code'])) {
		$grammar_unique_code = escape_html($_GET['grammar_unique_code']);
	} else {
		return;
	}

	$room_unique_code = escape_html($_SESSION['dashboard']['room_unique_code'] ?? '');

	$html = '';

	$html .= build_html_welcome_user_section($int_selected_language);
	
	$html .= build_html_grammar_view_zoom_controls($int_selected_language);
	
	$user_level = get_user_level();
	$capability_user_level = $user_level;
	if($capability_user_level === null){
		$capability_user_level = $int_Free_Member;
	}

	$arr_targets_visible = $allow_grammar_view_feature_capabilities_default[$capability_user_level] ?? [];
	$allow_grammar_view_content_section_capabilities = $allow_grammar_view_content_section_capabilities_default[$capability_user_level] ?? [];

	$arr_targets_visible['allow_grammar_view_content_section_capabilities'] = $allow_grammar_view_content_section_capabilities;

	$html .= build_html_grammar_view_page($grammar_unique_code, $room_unique_code, $user_level, $arr_targets_visible, $int_selected_language);

	return $str_grammar_point;
}



/******************************************************
 *  Dashboard Workshop
 *  
 ******************************************************/
add_shortcode('jws_workshop_page_shortcode', 'jws_workshop_page');
function jws_workshop_page(){

	$int_selected_language = jws_get_language_index();

	$user_level = get_user_level();
	if (!is_member_level($user_level)) {
		return;
	}

	$html = '';
	$html .= build_html_welcome_user_section($int_selected_language);
	$html .= build_html_workshop_section($int_selected_language);

	if(is_teacher_level($user_level)){

		global
			$str_globalVerticalToolbarSelectorButton_id,
			$str_globalVerticalToolbarOpenWiseButton_id,
			$str_globalVerticalToolbarManageRoomsButton_id,
			$str_globalVerticalToolbarLaserButton_id;

		$arr_targets_action = [
			$str_globalVerticalToolbarSelectorButton_id => true,
			$str_globalVerticalToolbarOpenWiseButton_id => false,
			$str_globalVerticalToolbarManageRoomsButton_id => false,
			$str_globalVerticalToolbarLaserButton_id => true
		];
		$html .= build_html_global_laser_pointer($arr_targets_action, $int_selected_language);
	}

	return $html;
}


/******************************************************
 *  Dashboard History
 *  
 ******************************************************/
add_shortcode('jws_history_page_shortcode', 'jws_history_page');
function jws_history_page(){

	$int_selected_language = jws_get_language_index();

	$user_level = get_user_level();
	if (!is_member_level($user_level)) {
		return;
	}

	$html = '';
	$html .= build_html_welcome_user_section($int_selected_language);
	$html .= build_html_history_page($int_selected_language);

	if(is_teacher_level($user_level)){
		
		global
			$str_globalVerticalToolbarSelectorButton_id,
			$str_globalVerticalToolbarOpenWiseButton_id,
			$str_globalVerticalToolbarManageRoomsButton_id,
			$str_globalVerticalToolbarLaserButton_id;

		$arr_targets_action = [
			$str_globalVerticalToolbarSelectorButton_id => true,
			$str_globalVerticalToolbarOpenWiseButton_id => false,
			$str_globalVerticalToolbarManageRoomsButton_id => false,
			$str_globalVerticalToolbarLaserButton_id => true
		];
		$html .= build_html_global_laser_pointer($arr_targets_action, $int_selected_language);
	}

	return $html;
}


/******************************************************
 *  Dashboard Bookmarks
 *
 ******************************************************/
add_shortcode('jws_your_bookmarks_page_shortcode', 'jws_your_bookmarks_page');
function jws_your_bookmarks_page()
{
    $int_selected_language = jws_get_language_index();

    $user_level = get_user_level();
    if (!is_student_level($user_level)) {
        return;
    }

    return build_html_your_bookmarks_page($int_selected_language);
}

add_shortcode('jws_room_bookmarks_page_shortcode', 'jws_room_bookmarks_page');
function jws_room_bookmarks_page()
{
    $int_selected_language = jws_get_language_index();

    $user_level = get_user_level();
    if (!is_student_level($user_level)) {
        return;
    }

    return build_html_room_bookmarks_page($int_selected_language);
}

add_shortcode('jws_bookmark_activity_page_shortcode', 'jws_bookmark_activity_page');
function jws_bookmark_activity_page()
{
    $int_selected_language = jws_get_language_index();

    $user_level = get_user_level();
    if (!is_student_level($user_level)) {
        return;
    }

    return build_html_bookmark_activity_page($int_selected_language);
}



/******************************************************
 *  Dashboard Lesson Memos
 *  
 ******************************************************/
add_shortcode('jws_lesson_memos_page_shortcode', 'jws_lesson_memos_page');
function jws_lesson_memos_page() {

    $int_selected_language = jws_get_language_index();

    $user_level = get_user_level();
    if (!is_student_level($user_level)) {
        return;
    }

	$html = '';
	$html .= build_html_lesson_memos_page($int_selected_language);
	
	if(is_teacher_level($user_level)){
		
		global
			$str_globalVerticalToolbarSelectorButton_id,
			$str_globalVerticalToolbarOpenWiseButton_id,
			$str_globalVerticalToolbarManageRoomsButton_id,
			$str_globalVerticalToolbarLaserButton_id;

		$arr_targets_action = [
			$str_globalVerticalToolbarSelectorButton_id => true,
			$str_globalVerticalToolbarOpenWiseButton_id => false,
			$str_globalVerticalToolbarManageRoomsButton_id => false,
			$str_globalVerticalToolbarLaserButton_id => true
		];
		$html .= build_html_global_laser_pointer($arr_targets_action, $int_selected_language);
	}

    return $html;
}



/******************************************************
 *  Dashboard Items
 *  
 ******************************************************/
add_shortcode('jws_grammar_point_page_shortcode', 'jws_grammar_point_page');
function jws_grammar_point_page(){

	global
		$int_Free_Member,
		$allow_grammar_view_feature_capabilities_default,
		$allow_grammar_view_content_section_capabilities_default;

	$int_selected_language = jws_get_language_index();

	$request_method = 'GET';
	validate_request_method($request_method, $int_selected_language);

	if (isset($_GET['grammar_unique_code'])) {
		$grammar_unique_code = escape_html($_GET['grammar_unique_code']);
	} else {
		return;
	}

	$room_unique_code = escape_html($_SESSION['dashboard']['room_unique_code'] ?? '');

	$html = '';

	$html .= build_html_welcome_user_section($int_selected_language);
	
	$html .= build_html_grammar_view_zoom_controls($int_selected_language);
	
	$user_level = get_user_level();
	if($user_level === null){
		return;
	}

	$arr_targets_visible = $allow_grammar_view_feature_capabilities_default[$user_level] ?? [];
	$allow_grammar_view_content_section_capabilities = $allow_grammar_view_content_section_capabilities_default[$user_level] ?? [];

	$arr_targets_visible['allow_grammar_view_content_section_capabilities'] = $allow_grammar_view_content_section_capabilities;

	$html .= build_html_grammar_view_page($grammar_unique_code, $room_unique_code, $user_level, $arr_targets_visible, $int_selected_language);

	if(is_teacher_level($user_level)){
		
		global
			$str_globalVerticalToolbarSelectorButton_id,
			$str_globalVerticalToolbarOpenWiseButton_id,
			$str_globalVerticalToolbarManageRoomsButton_id,
			$str_globalVerticalToolbarLaserButton_id;

		$arr_targets_action = [
			$str_globalVerticalToolbarSelectorButton_id => true,
			$str_globalVerticalToolbarOpenWiseButton_id => false,
			$str_globalVerticalToolbarManageRoomsButton_id => false,
			$str_globalVerticalToolbarLaserButton_id => true
		];
		$html .= build_html_global_laser_pointer($arr_targets_action, $int_selected_language);
		
	}

	return $html;
}


add_shortcode('jws_homework_page_shortcode', 'jws_homework_page');
function jws_homework_page(){


	$int_selected_language = jws_get_language_index();

	$html = '';

	$user_level = get_user_level();
	if(is_teacher_level($user_level)){
		
		global
			$str_globalVerticalToolbarSelectorButton_id,
			$str_globalVerticalToolbarOpenWiseButton_id,
			$str_globalVerticalToolbarManageRoomsButton_id,
			$str_globalVerticalToolbarLaserButton_id;

		$arr_targets_action = [
			$str_globalVerticalToolbarSelectorButton_id => true,
			$str_globalVerticalToolbarOpenWiseButton_id => false,
			$str_globalVerticalToolbarManageRoomsButton_id => false,
			$str_globalVerticalToolbarLaserButton_id => true
		];
		$html .= build_html_global_laser_pointer($arr_targets_action, $int_selected_language);

	}

	$html .= build_html_grammar_view_zoom_controls($int_selected_language);

	$html .= build_html_homework_page($int_selected_language);

	return $html;
}




/******************************************************
 *  Dashboard Quizzes
 *  
 ******************************************************/
add_shortcode('jws_quiz_landing_page_shortcode', 'jws_quiz_landing_page');
function jws_quiz_landing_page(){

	global
		$int_mastery_level_jws_beginner,
		$str_option_value_default,
		$str_option_value_array,
		$str_quizButtonShowOtherQuestions,
		$str_globalVerticalToolbarSelectorButton_id,
		$str_globalVerticalToolbarOpenWiseButton_id,
		$str_globalVerticalToolbarManageRoomsButton_id,
		$str_globalVerticalToolbarLaserButton_id;


	$int_selected_language = jws_get_language_index();

	$pageType = 'landing';
	
	$str_select_quiz_section = switch_build_html_select_quiz_section($pageType, $int_selected_language);

	$str_quiz_contents_container = '<div id="quizContentsContainer"></div>';

	$str_next_button_label = $str_quizButtonShowOtherQuestions[$int_selected_language];
	$str_quizFeedbackScreen = build_html_quiz_feedback_screen($pageType, $str_next_button_label, $int_selected_language);

	$str_html_overlay_close_button = build_html_overlay_close_button();
	
	$str_landing_page_quizzes_screen =
	'<div id="landingPageQuizzesScreen" class="wiseScreenModal hidden">'.
		$str_html_overlay_close_button.
		'<div class="modalScrollableContainer">'.
			$str_quiz_contents_container.'
		</div>
	</div>';

	$str_landing_page_quizzes_screen =
	'<div id="landingPageQuizzesScreenOverlay" class="quizOverlay">'.$str_landing_page_quizzes_screen.$str_quizFeedbackScreen.'</div>';

	$str_quiz_container = $str_select_quiz_section.$str_landing_page_quizzes_screen;
	echo $str_quiz_container;
	
}

add_shortcode('jws_japanese_particle_quiz_page_shortcode', 'jws_japanese_particle_quiz_page');
function jws_japanese_particle_quiz_page(){

	global
		$int_mastery_level_jws_beginner,
		$str_option_value_default,
		$str_option_value_array,
		$str_quizButtonShowOtherQuestions,
		$str_globalVerticalToolbarSelectorButton_id,
		$str_globalVerticalToolbarOpenWiseButton_id,
		$str_globalVerticalToolbarManageRoomsButton_id,
		$str_globalVerticalToolbarLaserButton_id;


	$int_selected_language = jws_get_language_index();
	
	$int_mastery_level = $int_mastery_level_jws_beginner;
	if (isset($_GET['masteryLevel'])) {
		$int_mastery_level = intval($_GET['masteryLevel']);
	}
	elseif (isset($_SESSION['quiz_settings_mastery_level'])) {
		$int_mastery_level = $_SESSION['quiz_settings_mastery_level'];
	}

	$unique_code_type = $str_option_value_default;
	$arr_grammar_unique_code = [];
	if (isset($_GET['uniqueCode'])) {
		$unique_code_type = validate_quiz_unique_code_and_get_room_unique_code($int_selected_language);
	}
	elseif (
		$unique_code_type === $str_option_value_default && 
		isset($_GET['createFromArray']) && 
		intval($_GET['createFromArray']) === 1
	) {
		$unique_code_type = $str_option_value_array;
		$arr_grammar_unique_code = isset($_GET['arr_grammar_unique_code']) ? $_GET['arr_grammar_unique_code'] : [];
	}

	$arr_sub_category = [];
	if (isset($_GET['arr_sub_category'])) {
		$arr_sub_category = $_GET['arr_sub_category'];
	}
	elseif (isset($_SESSION['quiz_settings_sub_category'])) {
		$arr_sub_category = $_SESSION['quiz_settings_sub_category'];
	}

	$pageType = 'quiz';
	list($str_japanese_particle_quiz, $str_quiz_history_prompt) = get_data_japanese_particle_quiz($pageType, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $int_selected_language);

	$str_next_button_label = $str_quizButtonShowOtherQuestions[$int_selected_language];
	$str_quizFeedbackScreen = build_html_quiz_feedback_screen($pageType, $str_next_button_label, $int_selected_language);

	echo $str_japanese_particle_quiz.$str_quizFeedbackScreen;

	$arr_targets_action = [
		$str_globalVerticalToolbarSelectorButton_id => true,
		$str_globalVerticalToolbarOpenWiseButton_id => false,
		$str_globalVerticalToolbarManageRoomsButton_id => false,
		$str_globalVerticalToolbarLaserButton_id => true
	];
	$str_global_laser_pointer = build_html_global_laser_pointer($arr_targets_action, $int_selected_language);
	echo $str_global_laser_pointer;
}


add_shortcode('jws_grammar_quiz_page_shortcode', 'jws_grammar_quiz_page');
function jws_grammar_quiz_page(){

	global
		$int_mastery_level_jws_beginner,
		$str_option_value_default,
		$str_option_value_array,
		$str_quizButtonShowOtherQuestions,
		$str_globalVerticalToolbarSelectorButton_id,
		$str_globalVerticalToolbarOpenWiseButton_id,
		$str_globalVerticalToolbarManageRoomsButton_id,
		$str_globalVerticalToolbarLaserButton_id;

	$int_selected_language = jws_get_language_index();
	
	$int_mastery_level = $int_mastery_level_jws_beginner;
	if (isset($_GET['masteryLevel'])) {
		$int_mastery_level = intval($_GET['masteryLevel']);
	}
	elseif (isset($_SESSION['quiz_settings_mastery_level'])) {
		$int_mastery_level = $_SESSION['quiz_settings_mastery_level'];
	}

	$unique_code_type = $str_option_value_default;
	$arr_grammar_unique_code = [];
	if (isset($_GET['uniqueCode'])) {
		$unique_code_type = validate_quiz_unique_code_and_get_room_unique_code($int_selected_language);
	}
	elseif (
		$unique_code_type === $str_option_value_default && 
		isset($_GET['createFromArray']) && 
		intval($_GET['createFromArray']) === 1
	) {
		$unique_code_type = $str_option_value_array;
		$arr_grammar_unique_code = isset($_GET['arr_grammar_unique_code']) ? $_GET['arr_grammar_unique_code'] : [];
	}

	$arr_sub_category = [];
	if (isset($_GET['arr_sub_category'])) {
		$arr_sub_category = $_GET['arr_sub_category'];
	}
	elseif (isset($_SESSION['quiz_settings_sub_category'])) {
		$arr_sub_category = $_SESSION['quiz_settings_sub_category'];
	}

	$pageType = 'quiz';
	list($str_grammar_quiz, $str_quiz_history_prompt) = get_data_grammar_quiz($pageType, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $int_selected_language);
	
	$str_next_button_label = $str_quizButtonShowOtherQuestions[$int_selected_language];
	$str_quizFeedbackScreen = build_html_quiz_feedback_screen($pageType, $str_next_button_label, $int_selected_language);

	echo $str_grammar_quiz.$str_quizFeedbackScreen;

	$arr_targets_action = [
		$str_globalVerticalToolbarSelectorButton_id => true,
		$str_globalVerticalToolbarOpenWiseButton_id => false,
		$str_globalVerticalToolbarManageRoomsButton_id => false,
		$str_globalVerticalToolbarLaserButton_id => true
	];
	$str_global_laser_pointer = build_html_global_laser_pointer($arr_targets_action, $int_selected_language);
	echo $str_global_laser_pointer;
}


add_shortcode('jws_plainform_quiz_page_shortcode', 'jws_plainform_quiz_page');
function jws_plainform_quiz_page(){

	global
		$int_mastery_level_jws_beginner,
		$str_option_value_default,
		$str_option_value_array,
		$str_globalVerticalToolbarSelectorButton_id,
		$str_globalVerticalToolbarOpenWiseButton_id,
		$str_globalVerticalToolbarManageRoomsButton_id,
		$str_globalVerticalToolbarLaserButton_id;

	$int_selected_language = jws_get_language_index();
	
	$int_mastery_level = $int_mastery_level_jws_beginner;
	if (isset($_GET['masteryLevel'])) {
		$int_mastery_level = intval($_GET['masteryLevel']);
	}
	elseif (isset($_SESSION['quiz_settings_mastery_level'])) {
		$int_mastery_level = $_SESSION['quiz_settings_mastery_level'];
	}

	$unique_code_type = $str_option_value_default;
	$arr_grammar_unique_code = [];
	if (isset($_GET['uniqueCode'])) {
		$unique_code_type = validate_quiz_unique_code_and_get_room_unique_code($int_selected_language);
	}
	elseif (
		$unique_code_type === $str_option_value_default && 
		isset($_GET['createFromArray']) && 
		intval($_GET['createFromArray']) === 1
	) {
		$unique_code_type = $str_option_value_array;
		$arr_grammar_unique_code = isset($_GET['arr_grammar_unique_code']) ? $_GET['arr_grammar_unique_code'] : [];
	}

	$arr_japanese_classification = [];
	if (isset($_GET['arr_japanese_classification'])) {
		$arr_japanese_classification = $_GET['arr_japanese_classification'];
	}
	elseif (isset($_SESSION['quiz_settings_japanese_classification'])) {
		$arr_japanese_classification = $_SESSION['quiz_settings_japanese_classification'];
	}


	$pageType = 'quiz';
	$arr_sub_category = [];
	list($str_plainform_quiz, $str_quiz_history_prompt) = get_data_plainform_quiz($pageType, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $arr_japanese_classification, $int_selected_language);

	$str_next_button_label = '';
	$str_quizFeedbackScreen = build_html_quiz_feedback_screen($pageType, $str_next_button_label, $int_selected_language);

	echo $str_plainform_quiz.$str_quizFeedbackScreen;

	$arr_targets_action = [
		$str_globalVerticalToolbarSelectorButton_id => true,
		$str_globalVerticalToolbarOpenWiseButton_id => false,
		$str_globalVerticalToolbarManageRoomsButton_id => false,
		$str_globalVerticalToolbarLaserButton_id => true
	];
	$str_global_laser_pointer = build_html_global_laser_pointer($arr_targets_action, $int_selected_language);
	echo $str_global_laser_pointer;

}


add_shortcode('jws_word_inflection_quiz_page_shortcode', 'jws_word_inflection_quiz_page');
function jws_word_inflection_quiz_page(){

	global
		$int_mastery_level_jws_beginner,
		$str_option_value_default,
		$str_option_value_array,
		$str_quizButtonShowOtherQuestions,
		$str_globalVerticalToolbarSelectorButton_id,
		$str_globalVerticalToolbarOpenWiseButton_id,
		$str_globalVerticalToolbarManageRoomsButton_id,
		$str_globalVerticalToolbarLaserButton_id;

	$int_selected_language = jws_get_language_index();
		
	$int_mastery_level = $int_mastery_level_jws_beginner;
	if (isset($_GET['masteryLevel'])) {
		$int_mastery_level = intval($_GET['masteryLevel']);
	}
	elseif (isset($_SESSION['quiz_settings_mastery_level'])) {
		$int_mastery_level = $_SESSION['quiz_settings_mastery_level'];
	}

	$unique_code_type = $str_option_value_default;
	$arr_grammar_unique_code = [];
	if (isset($_GET['uniqueCode'])) {
		$unique_code_type = validate_quiz_unique_code_and_get_room_unique_code($int_selected_language);
	}
	elseif (
		$unique_code_type === $str_option_value_default && 
		isset($_GET['createFromArray']) && 
		intval($_GET['createFromArray']) === 1
	) {
		$unique_code_type = $str_option_value_array;
		$arr_grammar_unique_code = isset($_GET['arr_grammar_unique_code']) ? $_GET['arr_grammar_unique_code'] : [];
	}

	$arr_japanese_classification = [];
	if (isset($_GET['arr_japanese_classification'])) {
		$arr_japanese_classification = $_GET['arr_japanese_classification'];
	}
	elseif (isset($_SESSION['quiz_settings_japanese_classification'])) {
		$arr_japanese_classification = $_SESSION['quiz_settings_japanese_classification'];
	}

	$arr_inflection = [];
	if (isset($_GET['arr_inflection'])) {
		$arr_inflection = $_GET['arr_inflection'];
	}
	elseif (isset($_SESSION['quiz_settings_inflection'])) {
		$arr_inflection = $_SESSION['quiz_settings_inflection'];
	}

	$pageType = 'quiz';
	$arr_sub_category = [];
	list($str_word_inflection_quiz, $str_quiz_history_prompt) = get_data_word_inflection_quiz($pageType, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $arr_japanese_classification, $arr_inflection, $int_selected_language);
	
	$str_next_button_label = $str_quizButtonShowOtherQuestions[$int_selected_language];
	$str_quizFeedbackScreen = build_html_quiz_feedback_screen($pageType, $str_next_button_label, $int_selected_language);

	echo $str_word_inflection_quiz.$str_quizFeedbackScreen;

	$arr_targets_action = [
		$str_globalVerticalToolbarSelectorButton_id => true,
		$str_globalVerticalToolbarOpenWiseButton_id => false,
		$str_globalVerticalToolbarManageRoomsButton_id => false,
		$str_globalVerticalToolbarLaserButton_id => true
	];
	$str_global_laser_pointer = build_html_global_laser_pointer($arr_targets_action, $int_selected_language);
	echo $str_global_laser_pointer;
}


add_shortcode('jws_sorting_quiz_page_shortcode', 'jws_sorting_quiz_page');
function jws_sorting_quiz_page(){

	global
		$int_mastery_level_jws_beginner,
		$str_option_value_default,
		$str_option_value_array;


	$int_selected_language = jws_get_language_index();

	$int_advance_stage = isset($_GET['advance_stage'])
		? intval($_GET['advance_stage'])
		: FLAG_FALSE;

	$isAdvanceStage = ($int_advance_stage === FLAG_TRUE);

	
	$int_mastery_level = $int_mastery_level_jws_beginner;
	if (isset($_GET['masteryLevel'])) {
		$int_mastery_level = intval($_GET['masteryLevel']);
	}
	elseif (isset($_SESSION['quiz_settings_mastery_level'])) {
		$int_mastery_level = intval($_SESSION['quiz_settings_mastery_level']);
	}

	$unique_code_type = $str_option_value_default;
	$arr_grammar_unique_code = [];
	if (isset($_GET['uniqueCode'])) {
		$unique_code_type = validate_quiz_unique_code_and_get_room_unique_code($int_selected_language);
	}
	elseif (
		$unique_code_type === $str_option_value_default && 
		isset($_GET['createFromArray']) && 
		intval($_GET['createFromArray']) === FLAG_TRUE
	) {
		$unique_code_type = $str_option_value_array;
		$arr_grammar_unique_code = isset($_GET['arr_grammar_unique_code']) ? $_GET['arr_grammar_unique_code'] : [];
	}
	
	$arr_sub_category = [];
	if (isset($_GET['arr_sub_category'])) {
		$arr_sub_category = $_GET['arr_sub_category'];
	}
	elseif (isset($_SESSION['quiz_settings_sub_category'])) {
		$arr_sub_category = $_SESSION['quiz_settings_sub_category'];
	}

	$pageType = 'quiz';
	list($str_sorting_quiz, $str_quiz_history_prompt) = get_data_sorting_quiz($pageType, $isAdvanceStage, $int_mastery_level, $unique_code_type, $arr_grammar_unique_code, $arr_sub_category, $int_selected_language);

	echo $str_sorting_quiz;
}


add_shortcode('jws_sorting_quiz_fullscreen_page_shortcode', 'jws_sorting_quiz_fullscreen_page');
function jws_sorting_quiz_fullscreen_page(){

	global
		$str_globalVerticalToolbarSelectorButton_id,
		$str_globalVerticalToolbarOpenWiseButton_id,
		$str_globalVerticalToolbarManageRoomsButton_id,
		$str_globalVerticalToolbarLaserButton_id;

	$int_selected_language = jws_get_language_index();

	$unique_code = isset($_GET['sentence_unique_code'])
		?escape_html($_GET['sentence_unique_code'])
		: '';

	$int_advance_stage = isset($_GET['advance_stage'])
		? intval($_GET['advance_stage'])
		: '';

	if($int_advance_stage === FLAG_TRUE){
		$isAdvanceStage = true;
	}
	else{
		$isAdvanceStage = false;
	}
	
	$str_sorting_quiz_fullscreen = build_html_sorting_quiz_fullscreen_page($unique_code, $isAdvanceStage, $int_selected_language);
	echo $str_sorting_quiz_fullscreen;

	$arr_targets_action = [
		$str_globalVerticalToolbarSelectorButton_id => true,
		$str_globalVerticalToolbarOpenWiseButton_id => false,
		$str_globalVerticalToolbarManageRoomsButton_id => false,
		$str_globalVerticalToolbarLaserButton_id => true
	];
	$str_global_laser_pointer = build_html_global_laser_pointer($arr_targets_action, $int_selected_language);
	echo $str_global_laser_pointer;

}



/******************************************************
 *  WISE
 *  
 ******************************************************/
add_shortcode('jws_wise_page_shortcode', 'jws_wise_page');
function jws_wise_page(){

	$int_selected_language = jws_get_language_index();

	$user_level = get_user_level();
	if (!is_teacher_level($user_level)) {
		return;
	}

	$ret = jws_require_single_session_for_page($int_selected_language);
	if (is_string($ret)) {
		return $ret;
	}

	$str_wise = build_html_wise_page($int_selected_language);
	echo $str_wise;

}


add_shortcode('jws_register_sentence_page_shortcode', 'jws_register_sentence_page');
function jws_register_sentence_page(){

	$int_selected_language = jws_get_language_index();

	$user_level = get_user_level();
	if (!is_admin_level($user_level)) {
		return;
	}

	$str_register_sentence = build_html_register_sentence_page($int_selected_language);
	echo $str_register_sentence;

}


add_shortcode('jws_create_layers_page_shortcode', 'jws_create_layers_page');
function jws_create_layers_page(){

	$int_selected_language = jws_get_language_index();

	$str_create_layers = build_html_create_layers_page($int_selected_language);
	echo $str_create_layers;

}


add_shortcode('jws_edit_registered_sentence_page_shortcode', 'jws_edit_registered_sentence_page');
function jws_edit_registered_sentence_page(){

	$int_selected_language = jws_get_language_index();

	$user_level = get_user_level();
	if (!is_admin_level($user_level)) {
		return;
	}

	$str_edit_registered_sentence = build_html_edit_registered_sentence_page($int_selected_language);
	echo $str_edit_registered_sentence;

}



/******************************************************
 *  WISE grammar_view
 *  
 ******************************************************/
add_shortcode('jws_grammar_view_for_administrators_page_shortcode', 'jws_grammar_view_for_administrators_page');
function jws_grammar_view_for_administrators_page(){

	$int_selected_language = jws_get_language_index();
	
	if (isset($_GET['grammar_unique_code'])) {
		$unique_code = escape_html($_GET['grammar_unique_code']);
	} else {
		return;
	}
	
	$user_level = get_user_level();
	if (!is_admin_level($user_level)) {
		return;
	}
	
	$str_grammar_view_for_administrators = build_html_grammar_view_for_administrators_page($unique_code, $int_selected_language);

	echo $str_grammar_view_for_administrators;
}


add_shortcode('jws_grammar_view_for_teachers_page_shortcode', 'jws_grammar_view_for_teachers_page');
function jws_grammar_view_for_teachers_page(){

	global
		$str_globalVerticalToolbarSelectorButton_id,
		$str_globalVerticalToolbarOpenWiseButton_id,
		$str_globalVerticalToolbarManageRoomsButton_id,
		$str_globalVerticalToolbarLaserButton_id,
		$allow_grammar_view_feature_capabilities_default,
		$allow_grammar_view_content_section_capabilities_default,
		$arr_allow_visible_override_keys,
		$int_Administrator,
		$int_allow_visible_in_grammar_view,
		$int_not_allow_visible_in_grammar_view;


	$int_selected_language = jws_get_language_index();

	$request_method = 'GET';
	validate_request_method($request_method, $int_selected_language);

	$user_level = get_user_level();
	if(!is_teacher_level($user_level)){
		return;
	}

	if (isset($_GET['grammar_unique_code'])) {
		$grammar_unique_code = escape_html($_GET['grammar_unique_code']);
	} else {
		return;
	}

	$room_unique_code = escape_html($_SESSION['wise']['room_unique_code'] ?? '');

	$html = '';

	$arr_targets_action = [
		$str_globalVerticalToolbarSelectorButton_id => true,
		$str_globalVerticalToolbarOpenWiseButton_id => false,
		$str_globalVerticalToolbarManageRoomsButton_id => false,
		$str_globalVerticalToolbarLaserButton_id => true
	];
	
	$html .= build_html_global_laser_pointer($arr_targets_action, $int_selected_language);
	
	$html .= build_html_welcome_user_section($int_selected_language);
	
	$html .= build_html_grammar_view_zoom_controls($int_selected_language);
	

	$arr_targets_visible = $allow_grammar_view_feature_capabilities_default[$user_level] ?? [];
	$allow_grammar_view_content_section_capabilities = $allow_grammar_view_content_section_capabilities_default[$user_level] ?? [];

	$arr_targets_visible['allow_grammar_view_content_section_capabilities'] = $allow_grammar_view_content_section_capabilities;

	$overrides = [];
	foreach ($arr_allow_visible_override_keys as $key) {
		if (isset($_GET[$key])) {
			$overrides[$key] = intval($_GET[$key]);
		}
	}
	if ($overrides) {
		$arr_targets_visible = array_replace($arr_targets_visible, $overrides);
	}
		
	if(is_admin_level($user_level)){
		$arr_targets_visible['link_to_register_sentence_visible'] = $int_allow_visible_in_grammar_view;
		$arr_targets_visible['link_to_grammar_view_for_administrators_visible'] = $int_allow_visible_in_grammar_view;
	} else {
		$arr_targets_visible['link_to_register_sentence_visible'] = $int_not_allow_visible_in_grammar_view;
		$arr_targets_visible['link_to_grammar_view_for_administrators_visible'] = $int_not_allow_visible_in_grammar_view;
	}

	$html .= build_html_grammar_view_page($grammar_unique_code, $room_unique_code, $user_level, $arr_targets_visible, $int_selected_language);

	return $html;
}



/******************************************************
 *  WISE wise_navigation
 *  
 ******************************************************/
add_shortcode('jws_wise_navigation_page_shortcode', 'jws_wise_navigation_page');
function jws_wise_navigation_page(){

	$int_selected_language = jws_get_language_index();

	$user_level = get_user_level();
	if (!is_student_level($user_level)) {
		return;
	}
	
	$unique_code = isset($_GET['unique_code'])
		?escape_html($_GET['unique_code'])
		: '';

	$t_wise_navigation_id = fetch_wise_navigation_id_from_unique_code($unique_code, $int_selected_language);

	if(!empty($t_wise_navigation_id)){
		$str_wise_navigation = build_html_wise_navigation_page($t_wise_navigation_id, $int_selected_language);
		echo $str_wise_navigation;
	}
}


add_shortcode('jws_select_wise_navigation_page_shortcode', 'jws_select_wise_navigation_page');
function jws_select_wise_navigation_page(){

	$int_selected_language = jws_get_language_index();

	$user_level = get_user_level();
	if (!is_student_level($user_level)) {
		return;
	}

	$unique_code = isset($_GET['unique_code'])
		?escape_html($_GET['unique_code'])
		: '';

	$int_registered_sentence_id = fetch_registered_sentence_id_from_unique_code($unique_code, $int_selected_language);
	if(!empty($int_registered_sentence_id)){
		$str_html = build_html_select_wise_navigation_page($int_registered_sentence_id, $int_selected_language);
		echo $str_html;
	}
}


add_shortcode('jws_manage_wise_navigations_page_shortcode', 'jws_manage_wise_navigations_page');
function jws_manage_wise_navigations_page(){

	$int_selected_language = jws_get_language_index();

	$user_level = get_user_level();
	if (!is_admin_level($user_level)) {
		return;
	}

	$unique_code = isset($_GET['unique_code'])
			?escape_html($_GET['unique_code'])
			: '';

	$str_html = build_html_manage_wise_navigations_page($unique_code, $int_selected_language);
	echo $str_html;

}


add_shortcode('jws_manage_wise_navigation_waypoints_page_shortcode', 'jws_manage_wise_navigation_waypoints_page');
function jws_manage_wise_navigation_waypoints_page(){

	$int_selected_language = jws_get_language_index();

	$user_level = get_user_level();
	if (!is_admin_level($user_level)) {
		return;
	}

	$unique_code = isset($_GET['unique_code'])
			?escape_html($_GET['unique_code'])
			: '';

	$str_html = build_html_manage_wise_navigation_waypoints_page($unique_code, $int_selected_language);
	echo $str_html;

}


add_shortcode('jws_manage_wise_navigation_scripts_page_shortcode', 'jws_manage_wise_navigation_scripts_page');
function jws_manage_wise_navigation_scripts_page(){

	$int_selected_language = jws_get_language_index();

	$user_level = get_user_level();
	if (!is_admin_level($user_level)) {
		return;
	}

	$unique_code = isset($_GET['unique_code'])
			?escape_html($_GET['unique_code'])
			: '';

	$str_html = build_html_manage_wise_navigation_scripts_page($unique_code, $int_selected_language);
	echo $str_html;

}


add_shortcode('jws_manage_wise_navigation_items_page_shortcode', 'jws_manage_wise_navigation_items_page');
function jws_manage_wise_navigation_items_page(){

	$int_selected_language = jws_get_language_index();

	$user_level = get_user_level();
	if (!is_admin_level($user_level)) {
		return;
	}

	$unique_code = isset($_GET['unique_code'])
			?escape_html($_GET['unique_code'])
			: '';

	$str_html = build_html_manage_wise_navigation_items_page($unique_code, $int_selected_language);
	echo $str_html;

}


add_shortcode('jws_check_wise_navigation_sequence_page_shortcode', 'jws_check_wise_navigation_sequence_page');
function jws_check_wise_navigation_sequence_page(){

	$int_selected_language = jws_get_language_index();

	$user_level = get_user_level();
	if (!is_admin_level($user_level)) {
		return;
	}

	$unique_code = isset($_GET['unique_code'])
			?escape_html($_GET['unique_code'])
			: '';

	$str_html = build_html_check_wise_navigation_sequence_page($unique_code, $int_selected_language);
	echo $str_html;

}



/******************************************************
 *  Teaching Rooms
 *  
 ******************************************************/
add_shortcode('jws_manage_rooms_page_shortcode', 'jws_manage_rooms_page');
function jws_manage_rooms_page(){

	$int_selected_language = jws_get_language_index();

	ensure_permission_room($int_selected_language);

	$str_welcome_user = build_html_welcome_user_section($int_selected_language);
	echo $str_welcome_user;

	$str_html = build_html_manage_rooms_page($int_selected_language);
	echo $str_html;
}


add_shortcode('jws_manage_room_lessons_page_shortcode', 'jws_manage_room_lessons_page');
function jws_manage_room_lessons_page(){

	$int_selected_language = jws_get_language_index();

	ensure_permission_room($int_selected_language);

	$str_welcome_user = build_html_welcome_user_section($int_selected_language);
	echo $str_welcome_user;

	$str_html = build_html_manage_room_lessons_page($int_selected_language);
	echo $str_html;

}


add_shortcode('jws_manage_room_lesson_steps_page_shortcode', 'jws_manage_room_lesson_steps_page');
function jws_manage_room_lesson_steps_page(){

	$int_selected_language = jws_get_language_index();

	ensure_permission_room($int_selected_language);

	$str_welcome_user = build_html_welcome_user_section($int_selected_language);
	echo $str_welcome_user;

	$str_html = build_html_manage_room_lesson_steps_page($int_selected_language);
	echo $str_html;

}


add_shortcode('jws_manage_room_lesson_step_units_page_shortcode', 'jws_manage_room_lesson_step_units_page');
function jws_manage_room_lesson_step_units_page(){

	$int_selected_language = jws_get_language_index();

	ensure_permission_room($int_selected_language);

	$str_welcome_user = build_html_welcome_user_section($int_selected_language);
	echo $str_welcome_user;

	$str_html = build_html_manage_room_lesson_step_units_page($int_selected_language);
	echo $str_html;

}


add_shortcode('jws_manage_room_lesson_contents_page_shortcode', 'jws_manage_room_lesson_contents_page');
function jws_manage_room_lesson_contents_page(){

	$int_selected_language = jws_get_language_index();

	ensure_permission_room($int_selected_language);

	$str_html = build_html_manage_room_lesson_contents_page($int_selected_language);
	echo $str_html;

}


add_shortcode('jws_manage_room_bookmarks_page_shortcode', 'jws_manage_room_bookmarks_page');
function jws_manage_room_bookmarks_page(){

	$int_selected_language = jws_get_language_index();

	ensure_permission_room($int_selected_language);

	$str_html = build_html_manage_room_bookmarks_page($int_selected_language);
	echo $str_html;

}


add_shortcode('jws_apply_for_classroom_page_shortcode', 'jws_apply_for_classroom_page');
function jws_apply_for_classroom_page(){

	$int_selected_language = jws_get_language_index();

	$html = '';

	$current_user = wp_get_current_user();
	if ($current_user->ID > 0) {
		$html = build_html_apply_for_classroom_page($int_selected_language);
	}
	
	echo $html;
	
}


add_shortcode('jws_apply_for_classroom_confirm_page_shortcode', 'jws_apply_for_classroom_confirm_page');
function jws_apply_for_classroom_confirm_page(){

	$int_selected_language = jws_get_language_index();

	$html = '';

	$current_user = wp_get_current_user();
	if ($current_user->ID > 0) {
		$html = build_html_apply_for_classroom_confirm_page($int_selected_language);
	}

	echo $html;

}


add_shortcode('jws_manage_room_invite_page_shortcode', 'jws_manage_room_invite_page');
function jws_manage_room_invite_page(){

    $int_selected_language = jws_get_language_index();

    ensure_permission_room($int_selected_language);

    $str_html = build_html_manage_room_invite_page($int_selected_language);
    echo $str_html;

}


add_shortcode('jws_manage_room_requests_page_shortcode', 'jws_manage_room_requests_page');
function jws_manage_room_requests_page(){

    $int_selected_language = jws_get_language_index();

    ensure_permission_room($int_selected_language);

    $str_html = build_html_manage_room_requests_page($int_selected_language);
    echo $str_html;

}



/******************************************************
 *  Account
 *  
 ******************************************************/
add_shortcode('jws_account_page_shortcode', 'jws_account_page');
function jws_account_page(){

	$int_selected_language = jws_get_language_index();

	$str_welcome_user = build_html_welcome_user_section($int_selected_language);
	echo $str_welcome_user;

	echo build_html_account_links_tree();

}

add_shortcode('jws_login_page_shortcode', 'jws_login_page');
function jws_login_page() {

    global $jws_login_error;

    $int_selected_language = jws_get_language_index();

    return build_html_login_page($jws_login_error, $int_selected_language);
}


add_shortcode('jws_logout_page_shortcode', 'jws_logout_page');
function jws_logout_page() {

    $int_selected_language = jws_get_language_index();

    return build_html_logout_page($int_selected_language);
}


add_shortcode('jws_logout_complete_page_shortcode', 'jws_logout_complete_page');
function jws_logout_complete_page() {

    $int_selected_language = jws_get_language_index();

    return build_html_logout_complete_page($int_selected_language);
}


add_shortcode('jws_register_account_page_shortcode', 'jws_register_account_page');
function jws_register_account_page() {

    $int_selected_language = jws_get_language_index();

    return build_html_register_account_page($int_selected_language);
}


add_shortcode('jws_profile_page_shortcode', 'jws_profile_page');
function jws_profile_page() {

    $int_selected_language = jws_get_language_index();

    return build_html_profile_page($int_selected_language);
}


add_shortcode('jws_profil_change_email_page_shortcode', 'jws_profil_change_email_page');
function jws_profil_change_email_page() {

    $int_selected_language = jws_get_language_index();

    return build_html_change_email_page($int_selected_language);
}


add_shortcode('jws_delete_account_page_shortcode', 'jws_delete_account_page');
function jws_delete_account_page() {

    $int_selected_language = jws_get_language_index();

    return build_html_delete_account_page($int_selected_language);
}


add_shortcode('jws_delete_account_complete_page_shortcode', 'jws_delete_account_complete_page');
function jws_delete_account_complete_page() {

    $int_selected_language = jws_get_language_index();

    return build_html_delete_account_complete_page($int_selected_language);
}


add_shortcode('jws_change_password_page_shortcode', 'jws_change_password_page');
function jws_change_password_page() {

    $int_selected_language = jws_get_language_index();

    return build_html_change_password_page($int_selected_language);
}


add_shortcode('jws_lost_password_page_shortcode', 'jws_lost_password_page');
function jws_lost_password_page() {

    $int_selected_language = jws_get_language_index();

    return build_html_lost_password_page($int_selected_language);
}


add_shortcode('jws_reset_password_page_shortcode', 'jws_reset_password_page');
function jws_reset_password_page() {

    $int_selected_language = jws_get_language_index();

    return build_html_reset_password_page($int_selected_language);
}



/******************************************************
 *  Membership
 *  
 ******************************************************/
add_shortcode('jws_about_membership_page_shortcode', 'jws_about_membership_page');
function jws_about_membership_page() {

    $int_selected_language = jws_get_language_index();

    $html = '';
    $html .= build_html_about_membership_page($int_selected_language);

    return $html;
}

add_shortcode('jws_membership_apply_page_shortcode', 'jws_membership_apply_page');
function jws_membership_apply_page() {

    $int_selected_language = jws_get_language_index();

    $html = '';
    $html .= build_html_user_membership_apply_page($int_selected_language);

    return $html;
}

add_shortcode('jws_membership_status_page_shortcode', 'jws_membership_status_page');
function jws_membership_status_page() {

    $int_selected_language = jws_get_language_index();

    $html = '';
    $html .= build_html_user_membership_status_page($int_selected_language);

    return $html;
}


/******************************************************
 *  Info
 *  
 ******************************************************/
add_shortcode('jws_site_info_page_shortcode', 'jws_site_info_page');
function jws_site_info_page(){

	$int_selected_language = jws_get_language_index();

	$str_welcome_user = build_html_welcome_user_section($int_selected_language);
	echo $str_welcome_user;

	echo build_html_site_info_links_tree();

}


add_shortcode('jws_philosophy_page_shortcode', 'jws_philosophy_page');
function jws_philosophy_page(){

	$int_selected_language = jws_get_language_index();

	$str_welcome_user = build_html_welcome_user_section($int_selected_language);
	echo $str_welcome_user;

	$str_mysite_title = get_bloginfo('name');

	$arr_str_about_us_page = [
		'<h1 class="aboutUsH1">'.$str_mysite_title.' の理念</h1>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">Mission (使命・存在意義)</h2>
			<p class="aboutUsText">「共に学び、共に育ち、共に未来を創る」</p>
			<p>
				'.$str_mysite_title.'は、そこに関わる全ての人が共に学び、共に成長しながら、
				よりよい未来を築いていくことを使命としています。一人ひとりの学びが、
				他者とつながり、社会とつながる力となるよう願っています。
			</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">Vision (目指す未来像・なりたい姿)</h2>
			<p class="aboutUsText">「言葉や文化の違いを越えて、すべての人が認め合い、学び合い、育ち合いながら、多様な価値観の中で、自らの夢や可能性を実現できる未来を共に創る。」</p>
			<p>
				'.$str_mysite_title.'は、国籍や文化、背景にかかわらず、すべての人が「自分らしく」学び、
				成長できる社会を目指しています。言語教育を通して、多様性を受け入れ、
				共に歩むための力を育てます。
			</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">Values (価値観・行動指針)</h2>

			<p class="aboutUsText">「多様性の尊重 (Respect for Diversity)」</p>
			<p>私たちは、言語や文化の違いを尊重し、すべての人が持つ個性と価値観を大切にします。</p>

			<p class="aboutUsText">「共感と理解 (Empathy and Understanding)」</p>
			<p>私たちは、他者の立場や考えを理解し、共感することで、より良い学びと成長を実現します。</p>

			<p class="aboutUsText">「成長志向 (Growth Mindset)」</p>
			<p>私たちは、常に成長し続け、挑戦を歓迎し、失敗から学び、共に進化する姿勢を大切にします。</p>

			<p class="aboutUsText">「協力と団結 (Collaboration and Unity)」</p>
			<p>私たちは、協力と団結を通じて、相互に支え合い、共に学び、共に成長する環境を創造します。</p>

			<p class="aboutUsText">「革新と創造性 (Innovation and Creativity)」</p>
			<p>私たちは、新しいアイデアや方法を取り入れ、柔軟な思考で教育の革新を追求します。</p>

			<p class="aboutUsText">「誠実さと責任 (Integrity and Accountability)」</p>
			<p>私たちは、誠実に行動し、全ての関係者に対して責任を持ちます。</p>
		</section>'
		,
		'<h1 class="aboutUsH1">'.$str_mysite_title.' 的理念</h1>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">使命（使命・存在意義）</h2>
			<p class="aboutUsText">「共同學習、共同成長、共同創造未來」</p>
			<p>
				'.$str_mysite_title.'的使命，是讓所有參與其中的人共同學習、共同成長，並攜手建立更美好的未來。我們希望每個人的學習都能成為與他人、與社會連結的力量。
			</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">願景（目標未來・理想形象）</h2>
			<p class="aboutUsText">「超越語言與文化的差異，在多元價值中彼此認同、共同學習、共同成長，共同創造能實現個人夢想與潛能的未來。」</p>
			<p>
				'.$str_mysite_title.'致力於實現一個不論國籍、文化或背景，每個人都能「做自己」並持續學習與成長的社會。我們透過語言教育，培養包容多樣性的力量，一同前行。
			</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">核心價值觀（價值觀・行動指針）</h2>

			<p class="aboutUsText">尊重多樣性（Respect for Diversity）</p>
			<p>我們尊重語言與文化的差異，珍惜每一個人所擁有的個性與價值觀。</p>

			<p class="aboutUsText">同理與理解（Empathy and Understanding）</p>
			<p>我們透過理解與同理他人的立場與想法，實現更好的學習與成長。</p>

			<p class="aboutUsText">成長思維（Growth Mindset）</p>
			<p>我們秉持持續成長的心態，樂於挑戰，從失敗中學習，共同邁向進步。</p>

			<p class="aboutUsText">合作與團結（Collaboration and Unity）</p>
			<p>我們透過合作與團結，相互支持，共同學習，共創成長環境。</p>

			<p class="aboutUsText">創新與創造力（Innovation and Creativity）</p>
			<p>我們擁抱新點子與新方法，以靈活思維追求教育創新。</p>

			<p class="aboutUsText">誠信與責任（Integrity and Accountability）</p>
			<p>我們以誠實的態度行事，並對所有利害關係人負責。</p>
		</section>'
	];

	echo $arr_str_about_us_page[$int_selected_language];

}


add_shortcode('jws_what_japanese_workshop_page_shortcode', 'jws_what_japanese_workshop_page');
function jws_what_japanese_workshop_page(){

	$int_selected_language = jws_get_language_index();
	
	$str_welcome_user = build_html_welcome_user_section($int_selected_language);
	echo $str_welcome_user;

	echo build_html_what_japanese_workshop($int_selected_language);

}


add_shortcode('jws_privacy_policy_and_disclaimer_page_shortcode', 'jws_privacy_policy_and_disclaimer_page');
function jws_privacy_policy_and_disclaimer_page(){

	$int_selected_language = jws_get_language_index();

	$str_welcome_user = build_html_welcome_user_section($int_selected_language);
	echo $str_welcome_user;

	$arr_str_privacy_policy_and_disclaimer = [
		'
		<h1 class="aboutUsH1">プライバシーポリシーおよび免責事項</h1>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">基本方針</h2>
			<p>当サイトは、個人情報の重要性を認識し、個人情報を保護することが社会的責務であると考え、個人情報に関する法令を遵守し、当サイトで取扱う個人情報の取得、利用、管理を適正に行います。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">適用範囲</h2>
			<p>本プライバシーポリシーは、お客様の個人情報もしくはそれに準ずる情報を取り扱う際に、当サイトが遵守する方針を示したものです。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">個人情報の利用目的</h2>
			<p>当サイトは、お客様からご提供いただく情報を以下の目的の範囲内において利用します。</p>
			<ul>
				<li>ご本人確認のため</li>
				<li>お問い合わせ、コメント等の確認・回答のため</li>
				<li>メールマガジン・DM・各種お知らせ等の配信・送付のため</li>
				<li>キャンペーン・アンケート・モニター・取材等の実施のため</li>
				<li>サービスの提供・改善・開発・マーケティングのため</li>
				<li>お客さまの承諾・申込みに基づく、提携事業者・団体等への個人情報の提供のため</li>
				<li>利用規約等で禁じている行為などの調査のため</li>
				<li>その他個別に承諾いただいた目的</li>
			</ul>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">個人情報の管理</h2>
			<p>当サイトは、個人情報の正確性及び安全確保のために、セキュリティ対策を徹底し、個人情報の漏洩、改ざん、不正アクセスなどの危険については、必要かつ適切なレベルの安全対策を実施します。</p>
			<p>当サイトは、第三者に重要な情報を読み取られたり、改ざんされたりすることを防ぐために、SSLによる暗号化を使用しております。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">個人情報の第三者提供</h2>
			<p>当サイトは、以下を含む正当な理由がある場合を除き、個人情報を第三者に提供することはありません。</p>
			<ul>
				<li>ご本人の同意がある場合</li>
				<li>法令に基づく場合</li>
				<li>人の生命・身体・財産の保護に必要な場合</li>
				<li>公衆衛生・児童の健全育成に必要な場合</li>
				<li>国の機関等の法令の定める事務への協力の場合（税務調査、統計調査等）</li>
			</ul>
			<p>当サイトでは、利用目的の達成に必要な範囲内において、他の事業者へ個人情報を委託することがあります。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第三者決済サービス（Stripe）について</h2>
			<p>当サイトでは、有料サービスの決済において、Stripe社が提供する決済プラットフォームを利用します。</p>
			<p>クレジットカード番号等の決済情報は当サイトでは一切取得・保存せず、Stripe社が運営する決済システム上で暗号化され、安全に処理されます。</p>
			<p>Stripe社による個人情報および決済情報の取扱いの詳細については、Stripe社のプライバシーポリシーをご確認ください。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">個人情報に関するお問い合わせ</h2>
			<p>開示、訂正、利用停止等のお申し出があった場合には、所定の方法に基づき対応致します。具体的な方法については、個別にご案内しますので、お問い合わせください。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">Cookie（クッキー）</h2>
			<p>Cookie（クッキー）は、利用者のサイト閲覧履歴を、利用者のコンピュータに保存しておく仕組みです。</p>
			<p>利用者はCookie（クッキー）を無効にすることで収集を拒否することができますので、お使いのブラウザの設定をご確認ください。ただし、Cookie（クッキー）を拒否した場合、当サイトのいくつかのサービス・機能が正しく動作しない場合があります。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">アクセス解析</h2>
			<p>当サイトでは、サイトの分析と改善のためにGoogleが提供している「Google アナリティクス」を利用しています。</p>
			<p>このサービスは、トラフィックデータの収集のためにCookie（クッキー）を使用しています。トラフィックデータは匿名で収集されており、個人を特定するものではありません。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">広告配信</h2>
			<p>当サイトは、第三者配信の広告サービス「Google アドセンス」を利用しています。</p>
			<p>広告配信事業者は、利用者の興味に応じた広告を表示するためにCookie（クッキー）を使用することがあります。これによって利用者のブラウザを識別できるようになりますが、個人を特定するものではありません。</p>
			<p>Cookie（クッキー）を無効にする方法や「Google アドセンス」に関する詳細は、<a href="https://policies.google.com/technologies/ads?gl=jp" target="_blank" rel="noopener noreferrer">https://policies.google.com/technologies/ads?gl=jp</a> をご覧ください。</p>
			<p>また、Amazonのアソシエイトとして、当サイトは適格販売により収入を得ています。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">コメント・お問い合わせフォーム</h2>
			<p>当サイトでは、コメント・お問い合わせフォームに表示されているデータ、そしてスパム検出に役立てるための IP アドレスやブラウザのユーザーエージェント文字列等を収集します。</p>
			<p>メールアドレスから作成される匿名化されたハッシュ文字列は、あなたが「Gravatar」サービスを使用中かどうか確認するため同サービスに提供されることがあります。</p>
			<p>同サービスのプライバシーポリシーは、<a href="https://automattic.com/privacy/" target="_blank" rel="noopener noreferrer">https://automattic.com/privacy/</a> をご覧ください。</p>
			<p>なお、コメントが承認されると、プロフィール画像がコメントとともに一般公開されます。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">他サイトからの埋め込みコンテンツ</h2>
			<p>当サイトには、埋め込みコンテンツ （動画、画像、投稿など）が含まれます。他サイトからの埋め込みコンテンツは、訪問者がそのサイトを訪れた場合とまったく同じように振る舞います。</p>
			<p>これらのサイトは、あなたのデータの収集、Cookie（クッキー）の使用、サードパーティによる追加トラッキングの埋め込み、埋め込みコンテンツとのやりとりの監視を行うことがあります。</p>
			<p>アカウントを使ってそのサイトにログイン中の場合、埋め込みコンテンツとのやりとりのトラッキングも含まれます。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">免責事項</h2>
			<p>当サイトのコンテンツ・情報について、可能な限り正確な情報を掲載するよう努めておりますが、正確性や安全性を保証するものではありません。当サイトに掲載された内容によって生じた損害等の一切の責任を負いかねますのでご了承ください。</p>
			<p>当サイトからリンクやバナーなどによって他のサイトに移動した場合、移動先サイトで提供される情報、サービス等について一切の責任を負いません。</p>
			<p>当サイトで掲載している料金表記について、予告なく変更されることがあります。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">著作権・肖像権</h2>
			<p>当サイトで掲載しているすべてのコンテンツ（文章、画像、動画、音声、ファイル等）の著作権・肖像権等は当サイト所有者または各権利所有者が保有し、許可なく無断利用（転載、複製、譲渡、二次利用等）することを禁止します。また、コンテンツの内容を変形・変更・加筆修正することも一切認めておりません。</p>
			<p>各権利所有者におかれましては、万一掲載内容に問題がございましたら、ご本人様よりお問い合わせください。迅速に対応いたします。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">リンク</h2>
			<p>当サイトは原則リンクフリーです。リンクを行う場合の許可や連絡は不要です。引用する際は、引用元の明記と該当ページへのリンクをお願いします。</p>
			<p>ただし、画像ファイルへの直リンク、インラインフレームを使用したHTMLページ内で表示する形でのリンクはご遠慮ください。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">本プライバシーポリシーの変更</h2>
			<p>当サイトは、本プライバシーポリシーの内容を適宜見直し、その改善に努めます。</p>
			<p>本プライバシーポリシーは、事前の予告なく変更することがあります。</p>
			<p>本プライバシーポリシーの変更は、当サイトに掲載された時点で有効になるものとします。</p>
		</section>
		'
		,

		'<h1 class="aboutUsH1">隱私政策和免責聲明</h1>
		<p>非常歡迎您光臨「Japanese Workshop」（以下簡稱本網站），為了讓您能夠安心的使用本網站的各項服務與資訊，特此向您說明本網站的隱私權保護政策，以保障您的權益，請您詳閱下列內容：</p>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">隱私權保護政策的適用範圍</h2>
			<p>隱私權保護政策內容，包括本網站如何處理在您使用網站服務時收集到的個人識別資料。隱私權保護政策不適用於本網站以外的相關連結網站，也不適用於非本網站所委託或參與管理的人員。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">個人資料的蒐集、處理及利用方式</h2>
			<ul>
				<li>當您造訪本網站或使用本網站所提供之功能服務時，我們將視該服務功能性質，請您提供必要的個人資料，並在該特定目的範圍內處理及利用您的個人資料；非經您書面同意，本網站不會將個人資料用於其他用途。</li>
				<li>本網站在您使用服務信箱、問卷調查等互動性功能時，會保留您所提供的姓名、電子郵件地址、聯絡方式及使用時間等。</li>
				<li>於一般瀏覽時，伺服器會自行記錄相關行徑，包括您使用連線設備的 IP 位址、使用時間、使用的瀏覽器、瀏覽及點選資料記錄等，做為我們增進網站服務的參考依據，此記錄為內部應用，決不對外公佈。</li>
				<li>為提供精確的服務，我們會將收集的問卷調查內容進行統計與分析，分析結果之統計數據或說明文字呈現，除供內部研究外，我們會視需要公佈統計數據及說明文字，但不涉及特定個人之資料。</li>
				<li>您可以隨時向我們提出請求，以更正或刪除本網站所蒐集您錯誤或不完整的個人資料。</li>
			</ul>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">資料之保護</h2>
			<ul>
				<li>本網站主機均設有防火牆、防毒系統等相關的各項資訊安全設備及必要的安全防護措施，加以保護網站及您的個人資料採用嚴格的保護措施，只由經過授權的人員才能接觸您的個人資料，相關處理人員皆簽有保密合約，如有違反保密義務者，將會受到相關的法律處分。</li>
				<li>如因業務需要有必要委託其他單位提供服務時，本網站亦會嚴格要求其遵守保密義務，並且採取必要檢查程序以確定其將確實遵守。</li>
			</ul>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">網站對外的相關連結</h2>
			<p>本網站的網頁提供其他網站的網路連結，您也可經由本網站所提供的連結，點選進入其他網站。但該連結網站不適用本網站的隱私權保護政策，您必須參考該連結網站中的隱私權保護政策。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">與第三人共用個人資料之政策</h2>
			<p>本網站絕不會提供、交換、出租或出售任何您的個人資料給其他個人、團體、私人企業或公務機關，但有法律依據或合約義務者，不在此限。</p>
			<p>前項但書之情形包括不限於：</p>
			<ul>
				<li>經由您書面同意。</li>
				<li>法律明文規定。</li>
				<li>為免除您生命、身體、自由或財產上之危險。</li>
				<li>與公務機關或學術研究機構合作，基於公共利益為統計或學術研究而有必要，且資料經過提供者處理或蒐集者依其揭露方式無從識別特定之當事人。</li>
				<li>當您在網站的行為，違反服務條款或可能損害或妨礙網站與其他使用者權益或導致任何人遭受損害時，經網站管理單位研析揭露您的個人資料是為了辨識、聯絡或採取法律行動所必要者。</li>
				<li>有利於您的權益。</li>
				<li>本網站委託廠商協助蒐集、處理或利用您的個人資料時，將對委外廠商或個人善盡監督管理之責。</li>
			</ul>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第三方金流服務（Stripe）相關說明</h2>
			<p>本網站在提供付費服務之金流處理時，使用 Stripe 公司所提供之金流平台。</p>
			<p>您的信用卡卡號等付款資訊並不會儲存在本網站，而是由 Stripe 之系統直接加密傳輸並安全處理。</p>
			<p>關於 Stripe 如何處理您的個人資料與付款資訊，請參考 Stripe 所公布之隱私權政策。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">Cookie 之使用</h2>
			<p>為了提供您最佳的服務，本網站會在您的電腦中放置並取用我們的 Cookie，若您不願接受 Cookie 的寫入，您可在您使用的瀏覽器功能項中設定隱私權等級為高，即可拒絕 Cookie 的寫入，但可能會導致網站某些功能無法正常執行 。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">隱私權保護政策之修正</h2>
			<p>本網站隱私權保護政策將因應需求隨時進行修正，修正後的條款將刊登於網站上。</p>
		</section>
		<section class="aboutUsSection">
			<h2 class="aboutUsH2">免責聲明</h2>
			<ul>
				<li>任何瀏覽網站的人士，須自行承擔一切風險，本服務不會負責任何因瀏覽或使用本服務而引致之損失。本服務不會作出任何默示的擔保。</li>
				<li>本服務承諾力求網站內容之準確性及完整性，但內容如有錯誤或遺漏，本服務不會承擔任何賠償法律責任，所有本服務內容，將會隨時更改，而不作另行通知。</li>
				<li>本服務可隨時停止或變更網頁資料及有關條款而毋須事前通知用戶。</li>
				<li>本服務不會對使用或連結本網頁而引致任何損害(包括但不限於電腦病毒、系統固障、資料損失)、誹謗、侵犯版權或知識產權所造成的損失，包括但不限於利潤、商譽、使用、資料損失或其他無形損失，本服務不承擔任何直接、間接、附帶、特別、衍生性或懲罰性賠償。</li>
				<li>本服務可能會連接至其他機構所提供的網頁，本服務不會對這些網頁內容作出任何保證或承擔任何責任。使用者如瀏覽這些網頁，將要自己承擔後果。是否使用本網站之服務下載或取得任何資料應由用戶自行考慮且自負風險，因前開任何資料之下載而導致用戶電腦系統之任何損壞或資料流失，本網站不承擔任何責任。</li>
				<li>本服務不會對使用或無法使用本網頁所載資料或就使用本網頁或其中任何資料所採取的任何行動或作出的決定引起合約上或其他方面損害承擔賠償。</li>
			</ul>
		</section>
		'
	];

	echo $arr_str_privacy_policy_and_disclaimer[$int_selected_language];
}


add_shortcode('jws_legal_asct_page_shortcode', 'jws_legal_asct_page');
function jws_legal_asct_page(){

	$int_selected_language = jws_get_language_index();
	
	$str_welcome_user = build_html_welcome_user_section($int_selected_language);
	echo $str_welcome_user;

	echo build_html_legal_asct($int_selected_language);

}


add_shortcode('jws_membership_terms_and_conditions_page_shortcode', 'jws_membership_terms_and_conditions_page');
function jws_membership_terms_and_conditions_page(){

	$int_selected_language = jws_get_language_index();

	$str_welcome_user = build_html_welcome_user_section($int_selected_language);
	echo $str_welcome_user;

	
	$arr_str_membership_terms_and_conditions = [

		'
		<h1 class="aboutUsH1">会員規約（利用規約）</h1>
		<p>本規約は、Japanese Workshop（以下「当サイト」といいます）が提供する各種サービス（以下「本サービス」といいます）の利用条件を定めるものです。本サービスをご利用になる前に本規約をよくお読みいただき、ご同意のうえご利用ください。</p>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第1条（適用範囲）</h2>
			<p>1. 本規約は、当サイトが提供する本サービスの利用に関する一切の関係に適用されます。</p>
			<p>2. 利用者は、本サービスを実際に利用した時点で、本規約の内容に同意したものとみなします。</p>
			<p>3. 本サービスのうち、個別に利用条件が定められている場合は、当該条件が本規約に優先して適用されます。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第2条（定義）</h2>
			<p>本規約において使用する用語の定義は、次の各号のとおりとします。</p>
			<ul>
				<li>「利用者」：本サービスを利用するすべての方</li>
				<li>「会員」：当サイト所定の方法によりアカウント登録を完了した方</li>
				<li>「有料会員」：所定の料金を支払い、有料会員向けサービスを利用する権利を有する会員</li>
				<li>「コンテンツ」：文章、画像、動画、音声、教材、プログラムその他本サービスを通じて提供される一切の情報</li>
			</ul>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第3条（事業者情報）</h2>
			<p>本サービスの運営者に関する情報（事業者名、所在地、連絡先等）は、当サイトに別途掲載する「特定商取引法に基づく表記」に定めるとおりとします。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第4条（会員登録・アカウント管理）</h2>
			<p>1. 会員登録を希望する方は、本規約の内容に同意のうえ、当サイト所定の方法により登録手続きを行うものとします。</p>
			<p>2. 会員は、登録情報に変更が生じた場合、遅滞なく当サイト所定の方法により変更手続きを行うものとします。</p>
			<p>3. 会員は、メールアドレスおよびパスワードその他のログイン情報を自己の責任において厳重に管理するものとし、第三者に貸与、譲渡、共有等をしてはなりません。</p>
			<p>4. ログイン情報の管理不備、使用上の過誤、第三者の使用等に起因して会員または第三者に損害が生じた場合であっても、当サイトは一切の責任を負いません。</p>
			<p>5. 会員は、当サイト所定の手続きにより、いつでも退会することができます。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第5条（サービス内容）</h2>
			<p>1. 当サイトは、日本語学習に関連する教材、オンラインレッスン、学習ツール、情報提供等を内容とする本サービスを提供します。</p>
			<p>2. 本サービスの具体的な内容、提供方法および提供時間等は、当サイトが適宜定め、当サイト上に表示します。</p>
			<p>3. 当サイトは、運営上必要があると判断した場合、利用者への事前の通知なく、本サービスの内容の全部または一部を変更、追加、または終了することができます。</p>
			<p>4. 有料会員向けコンテンツは、所定の料金の支払いが確認された会員のみが利用できるものとします。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第6条（料金・支払方法）</h2>
			<p>1. 本サービスの利用料金は、当サイトが別途定め当サイト上に表示する料金表のとおりとします。</p>
			<p>2. 利用者は、当サイトが指定する決済手段（クレジットカード決済、銀行振込その他当サイトが認める方法）により、利用料金を支払うものとします。</p>
			<p>3. 有料会員サービスは、申込みまたは決済が完了した時点から、当サイトが定める期間にわたり提供されるものとします。</p>
			<p>4. 年額プランその他複数月分を一括して支払うプランについては、期間途中で解約した場合であっても、原則として返金は行いません。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第7条（返金について）</h2>
			<p>1. 利用者の都合による申込み後のキャンセル、解約、返金の請求には応じません。サブスクリプション型の有料会員サービスの自動更新停止は、次回更新日の前日までに当サイト所定の方法により手続きを行った場合、次回以降の利用期間から適用されます。なお、既にお支払い済みの利用期間については返金いたしません。</p>
			<p>2. 当サイトの責に帰すべき重大な不具合により、本サービスの利用が長期間にわたり著しく困難となった場合には、当サイトは個別の事情を考慮し、返金その他の対応について利用者と協議することがあります。</p>
			<p>3. 割引、キャンペーン等の適用は申込時点の条件によるものとし、遡っての適用や差額の返金は行いません。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第8条（禁止事項）</h2>
			<p>利用者は、本サービスの利用にあたり、次の各号に該当する行為またはそのおそれのある行為を行ってはなりません。</p>
			<ul>
				<li>アカウント、ログイン情報を第三者と共有し、または貸与、譲渡する行為</li>
				<li>当サイトが提供するコンテンツを、事前の許可なく転載、複製、改変、頒布、公衆送信等する行為</li>
				<li>スクリーンショット、録画、ダウンロード等により取得したコンテンツを第三者と共有する行為</li>
				<li>本サービスのサーバーまたはネットワークに過度の負荷を与える行為</li>
				<li>不正アクセス、リバースエンジニアリングその他システムの不正利用にあたる行為</li>
				<li>他の利用者、第三者または当サイトに対する誹謗中傷、いやがらせ、差別的言動その他迷惑行為</li>
				<li>法令または公序良俗に違反する行為</li>
				<li>その他、当サイトが不適切であると合理的に判断する行為</li>
			</ul>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第9条（知的財産権）</h2>
			<p>1. 本サービスに含まれるすべてのコンテンツの著作権・肖像権・商標権その他一切の知的財産権は、当サイト所有者または正当な権利者に帰属します。</p>
			<p>2. 利用者は、当サイトの書面による事前の許可なく、本サービスを通じて提供されるコンテンツを、私的利用の範囲を超えて利用してはなりません。</p>
			<p>3. 利用者が本サービスを通じて投稿、送信等した情報について、当サイトは個人が特定されない形で、サービスの運営および品質向上のために利用することができます。</p>
			<p>4. 著作権・肖像権に関する詳細は、当サイトの「プライバシーポリシーおよび免責事項」に定める内容があわせて適用されます。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第10条（個人情報の取扱い）</h2>
			<p>本サービスにおける利用者の個人情報の取扱いについては、当サイトが別途定める「プライバシーポリシー」に従うものとします。利用者は、本サービスを利用することにより、当該プライバシーポリシーに同意したものとみなされます。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第11条（サービスの中断・終了）</h2>
			<p>1. 当サイトは、次の各号のいずれかに該当する場合、利用者への事前通知なく本サービスの提供を一時中断することがあります。</p>
			<ul>
				<li>システムの保守点検、更新等を定期的または緊急に行う場合</li>
				<li>火災、停電、天災地変等の不可抗力により本サービスの提供が困難となった場合</li>
				<li>通信事業者の提供するサービスの障害等により本サービスの提供が困難となった場合</li>
				<li>その他、当サイトがやむを得ないと判断した場合</li>
			</ul>
			<p>2. 当サイトは、運営上の事情により本サービスの全部または一部を終了することがあります。その場合、当サイトは合理的な範囲で事前に当サイト上で告知します。</p>
			<p>3. 本条に基づく中断または終了により利用者に生じた損害について、当サイトは一切の責任を負いません。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第12条（免責事項）</h2>
			<p>1. 当サイトは、本サービスにおいて提供する情報・コンテンツ等について、可能な限り正確な情報を掲載するよう努めますが、その完全性、正確性、安全性、有用性等について保証するものではありません。</p>
			<p>2. 本サービスの利用または利用不能により利用者に生じたいかなる損害についても、当サイトは責任を負いません。</p>
			<p>3. 当サイトからリンクやバナー等により他のサイトへ移動した場合、移動先サイトで提供される情報、サービス等について、当サイトは一切の責任を負いません。</p>
			<p>4. 免責事項の詳細については、当サイトの「プライバシーポリシーおよび免責事項」に定める内容があわせて適用されます。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第13条（利用停止・契約解除）</h2>
			<p>1. 当サイトは、利用者が本規約に違反したと判断した場合、事前の通知なく当該利用者の本サービスの利用停止、会員資格の取消しその他必要と認める措置を講じることができます。</p>
			<p>2. 前項の措置により利用者に損害が生じた場合であっても、当サイトは一切の責任を負いません。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第14条（規約の変更）</h2>
			<p>1. 当サイトは、必要に応じて本規約の内容を変更することがあります。</p>
			<p>2. 本規約を変更する場合、当サイト上への掲載その他当サイトが適当と判断する方法により、その旨および変更後の内容ならびに効力発生日を通知します。</p>
			<p>3. 変更後の本規約の効力発生日以降に利用者が本サービスを利用した場合、当該利用者は変更後の本規約に同意したものとみなされます。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第15条（準拠法・管轄裁判所）</h2>
			<p>本規約の解釈および適用については、当サイトの所在地である台湾の法令を準拠法とし、本サービスに関連して当サイトと利用者との間に生じた紛争については、当サイトの所在地を管轄する裁判所を第一審の専属的合意管轄裁判所とします。</p>
		</section>
		'
		,

		'
		<h1 class="aboutUsH1">會員條款（使用條款）</h1>
		<p>本條款係為規範「Japanese Workshop」（以下簡稱「本網站」）所提供各項服務（以下統稱「本服務」）之使用條件。請您在使用本服務前，詳細閱讀本條款內容，並於同意後再行使用。</p>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第1條（適用範圍）</h2>
			<p>1. 本條款適用於一切與使用本服務相關之行為。</p>
			<p>2. 使用者實際使用本服務之時點，視為已閱讀、理解並同意接受本條款之約束。</p>
			<p>3. 如本服務之部分功能或個別服務另有使用條件者，其特別約定優先於本條款適用。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第2條（定義）</h2>
			<p>本條款中所使用之名詞，定義如下：</p>
			<ul>
				<li>「使用者」：指使用本服務之所有人。</li>
				<li>「會員」：指依本網站所定程序完成帳號註冊者。</li>
				<li>「付費會員」：指支付指定費用，享有付費會員專屬服務權利之會員。</li>
				<li>「內容」：指透過本服務所提供之一切資訊與資料，包括文字、圖片、影音、教材、程式等。</li>
			</ul>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第3條（營運者資訊）</h2>
			<p>本服務之營運者名稱、所在地、聯絡方式等相關資訊，載明於本網站另行刊載之「依特定商業交易法之表示」頁面。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第4條（會員註冊與帳號管理）</h2>
			<p>1. 欲成為會員者，應先閱讀並同意本條款，並依本網站所定程序完成註冊。</p>
			<p>2. 會員如有資料變更，應儘速依本網站程序進行變更，以確保資料之正確性。</p>
			<p>3. 會員有責任妥善保管其登入資訊（包含電子郵件帳號、密碼等），不得出借、轉讓或與第三人共用。</p>
			<p>4. 因登入資訊管理不當、使用疏失或遭第三人不當使用而生之損害，本網站不負任何責任。</p>
			<p>5. 會員得依本網站所定程序申請退會，退會後將無法再使用需會員身分方可使用之服務內容。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第5條（服務內容）</h2>
			<p>1. 本網站提供與日語學習相關之教材、線上課程、學習工具及其他資訊服務。</p>
			<p>2. 本服務之具體內容、提供方式及提供時間等，將由本網站視實際情況而定，並於本網站上公告或說明。</p>
			<p>3. 本網站得視營運需要，於合理範圍內變更、調整、增加、刪除全部或部分服務內容，使用者同意不以此向本網站主張任何請求。</p>
			<p>4. 付費會員專屬內容僅限已完成相關費用支付之會員使用。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第6條（費用與付款方式）</h2>
			<p>1. 本服務之費用，依本網站另行公告之費率表為準。</p>
			<p>2. 使用者應依本網站所指定之付款方式（如信用卡、銀行轉帳或其他方式）支付相關費用。</p>
			<p>3. 付費會員服務，自申請或付款完成之時點起，在本網站所訂之期間內提供。</p>
			<p>4. 年繳方案或其他一次支付多期費用之方案，如於使用期間中途解約者，原則上不予退費。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第7條（關於退費）</h2>
			<p>1. 因使用者個人因素提出之申請後取消、解約或退費之請求，本網站概不受理。訂閱制付費會員服務之自動續約停止，須於下一期扣款日前一日依本網站規定程序完成取消手續，方自次一期起生效；已支付之當期費用恕不退還。</p>
			<p>2. 如因本網站可歸責之重大系統障礙，致使使用者長時間無法正常使用本服務，本網站得視個案情形，與使用者協議相關處理方式。</p>
			<p>3. 折扣、優惠活動或其他促銷方案，僅適用於申請當時之條件，事後不得要求追溯適用或退還差額。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第8條（禁止行為）</h2>
			<p>使用者在使用本服務時，不得從事下列行為或有其虞之行為：</p>
			<ul>
				<li>與第三人共用帳號或將登入資訊出借、轉讓予第三人使用之行為</li>
				<li>未經許可重製、轉載、散布、公開傳輸、改作本服務所提供內容之行為</li>
				<li>以截圖、錄影、下載等方式取得之內容，提供或分享予第三人之行為</li>
				<li>對本服務之伺服器或網路產生過度負荷或干擾其運作之行為</li>
				<li>進行不法入侵、逆向工程或其他侵害系統安全之行為</li>
				<li>對其他使用者、第三人或本網站進行誹謗、中傷、騷擾或其他不當言行之行為</li>
				<li>違反法令或公序良俗之行為</li>
				<li>其他本網站基於合理事由認定為不適當之行為</li>
			</ul>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第9條（智慧財產權）</h2>
			<p>1. 本服務所提供之一切內容，其著作權、肖像權、商標權及其他智慧財產權，均屬本網站所有人或合法權利人所有。</p>
			<p>2. 使用者除於個人合理使用範圍內使用外，不得未經書面同意，以任何形式利用前揭內容，包括但不限於重製、改作、散布、公開傳輸等。</p>
			<p>3. 使用者經由本服務所發佈或傳送之內容，本網站得在不會識別特定個人之前提下，作為服務營運及品質提升之用途。</p>
			<p>4. 有關著作權及肖像權之更詳細規定，並同時適用於本網站「隱私政策和免責聲明」中相關條款之規定。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第10條（個人資料之處理）</h2>
			<p>本服務對使用者個人資料之蒐集、處理及利用，悉依本網站另訂之「隱私政策」辦理。使用者使用本服務，即視為同意隱私政策之內容。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第11條（服務中斷與終止）</h2>
			<p>1. 本網站於下列情形之一，得不事先通知暫停或中斷全部或一部之服務：</p>
			<ul>
				<li>進行系統或設備之維護、保養、更新時</li>
				<li>因天災、停電或其他不可抗力因素致無法提供服務時</li>
				<li>因通訊業者系統故障等非本網站可歸責事由致服務無法提供時</li>
				<li>本網站認為有必要暫停或中斷服務之其他情形</li>
			</ul>
			<p>2. 本網站得因營運因素，終止全部或一部之服務，並將於合理期限前於本網站公告或以其他適當方式通知。</p>
			<p>3. 因前二項所生之任何損害，本網站不負賠償責任。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第12條（免責事項）</h2>
			<p>1. 本網站雖致力於提供正確、完整且安全之內容，但不保證其完全正確性、安全性或適用性。</p>
			<p>2. 使用者因使用或無法使用本服務所生之任何損害，本網站概不負責。</p>
			<p>3. 使用者經由本網站連結至其他網站，該等網站所提供之內容或服務，均由各該網站自行負責，本網站不負任何連帶責任。</p>
			<p>4. 關於免責事項之更詳細規定，並同時適用於本網站「隱私政策和免責聲明」中相關條款之規定。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第13條（使用停止與契約終止）</h2>
			<p>1. 如使用者有違反本條款或有違反之虞，本網站得視情節輕重，逕行暫停其使用本服務、取消其會員資格或採取其他必要措施，無須另行取得使用者同意。</p>
			<p>2. 因前項措施所生之一切損害，概由使用者自行負擔，本網站不負任何賠償責任。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第14條（條款之變更）</h2>
			<p>1. 本網站得視實際需要，隨時變更本條款之內容。</p>
			<p>2. 條款如有變更，本網站將於網站公告或以其他適當方式通知，並自公告或通知所載之生效日起適用。</p>
			<p>3. 使用者於本條款變更生效日後仍繼續使用本服務者，視為已同意變更後之條款。</p>
		</section>

		<section class="aboutUsSection">
			<h2 class="aboutUsH2">第15條（準據法及管轄法院）</h2>
			<p>本條款之解釋與適用，以本網站所在地之中華民國（臺灣）法律為準據法；如因本服務發生任何爭議，雙方並同意以本網站所在地具有管轄權之法院為第一審管轄法院。</p>
		</section>
		'
	];

	echo $arr_str_membership_terms_and_conditions[$int_selected_language];
}


add_shortcode('jws_contact_page_shortcode', 'jws_contact_page');
function jws_contact_page(){

	$int_selected_language = jws_get_language_index();

	$str_welcome_user = build_html_welcome_user_section($int_selected_language);
	echo $str_welcome_user;

	$str_contact_form = build_html_contact_form($int_selected_language);
	echo $str_contact_form;
}



/******************************************************
 *  Admin
 *  
 ******************************************************/
add_shortcode('jws_admin_page_shortcode', 'jws_admin_page');
function jws_admin_page(){

	$int_selected_language = jws_get_language_index();

	$user_level = get_user_level();
	if (!is_admin_level($user_level)) {
        return;
    }

	$str_welcome_user = build_html_welcome_user_section($int_selected_language);
	echo $str_welcome_user;

	global
		$path_vip_invites,
		$path_vip_requests;

	// デバッグ tempo
	$url_vip_invites = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_vip_invites, '/'))
	);
	$url_vip_requests = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_vip_requests, '/'))
	);

	echo 'invites:';
	echo '<br>';
	echo $url_vip_invites;
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo 'requests:';
	echo '<br>';
	echo $url_vip_requests;
	echo '<br>';
	// デバッグ tempo

}


add_shortcode('jws_children_links_page_shortcode', 'jws_children_links_page');
function jws_children_links_page(){

	$int_selected_language = jws_get_language_index();

	$str_welcome_user = build_html_welcome_user_section($int_selected_language);
	echo $str_welcome_user;

	echo build_html_children_links_tree();

}


add_shortcode('jws_select_grammar_level_page_shortcode', 'jws_select_grammar_level_page');
function jws_select_grammar_level_page() {

	$int_selected_language = jws_get_language_index();

	$html = '';

	$html .= build_html_welcome_user_section($int_selected_language);

	$html .= build_html_select_grammar_level_page($int_selected_language);
	
	return $html;
	
}


add_shortcode('jws_select_grammar_page_shortcode', 'jws_select_grammar_page');
function jws_select_grammar_page() {

	$int_selected_language = jws_get_language_index();

	$html = '';

	$html .= build_html_welcome_user_section($int_selected_language);
	
	$html .= build_html_grammar_view_zoom_controls($int_selected_language);

	$html .= build_html_select_grammar_page($int_selected_language);

	return $html;
}


// 練習用
add_shortcode('sample_shortcode', 'sample');
function sample() {

    global
        $t_teaching_material_lesson_contents,
        $t_masta_japanese_root,
        $arr_columns_masta_japanese_root,
        $t_teaching_material_lessons,
        $t_teaching_material_lesson_steps,
        $t_teaching_material_lesson_step_units,
        $t_masta_japanese_sub_category,
        $int_masta_japanese_category_id_grammar,
        $int_masta_japanese_category_id_terminology,
        $t_registered_sentences,
        $t_layers,
        $str_sql_where_is_in;


	$int_selected_language = INDEX_FIRST;

	$user_level = get_user_level();
	if(!is_admin_level($user_level)){
		exit;
	}
	
	// ページ構成ーーーーーーーーーーーーーーーーーーーー
	echo build_html_multisite_pages_for_debug();
	exit;


	// タスク作成ーーーーーーーーーーーーーーーーーーーー

	$map_type = 'lesson_goal_with_usages_for_debug';
	$user_id = null;
	$target_id = intval($_GET['target_id']);
	$target_ids = [3,7,9,11,162];
	$is_direct = false;

	$arr_strSQL_select = [
		[$t_teaching_material_lesson_contents,'masta_japanese_root_id'],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]]
	];
	
	$strSQL_from = " FROM
					(
						(
							(
								(
									$t_teaching_material_lessons
									INNER JOIN $t_teaching_material_lesson_steps
									ON
									$t_teaching_material_lessons.id = $t_teaching_material_lesson_steps.lesson_id
								)
								INNER JOIN $t_teaching_material_lesson_step_units
								ON
								$t_teaching_material_lesson_steps.id = $t_teaching_material_lesson_step_units.lesson_step_id
							)
							INNER JOIN $t_teaching_material_lesson_contents
							ON
							$t_teaching_material_lesson_step_units.id = $t_teaching_material_lesson_contents.step_unit_id
						)
						INNER JOIN $t_masta_japanese_root
						ON
						$t_teaching_material_lesson_contents.masta_japanese_root_id = $t_masta_japanese_root.id
					)
					INNER JOIN $t_masta_japanese_sub_category
					ON
					$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
					";
	

	$arr_strSQL_where = [
		[
			[
				[$t_teaching_material_lessons,'id','=',$target_id,'PDO::PARAM_INT','And'],
				[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_grammar,'PDO::PARAM_INT','']
			],
			'Or'
		],
		[
			[
				[$t_teaching_material_lessons,'id','=',$target_id,'PDO::PARAM_INT','And'],
				[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_terminology,'PDO::PARAM_INT','']
			],
			''
		]
	];
	
	$arr_strSQL_order = [];
	
	$strSQL_option = '';
	
	list($pdo_has_error, $select_has_error, $e, $arr_teaching_material_lesson_contents) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	$str_no_sentence = '';
	$str_no_layer = '';

	if(!empty($arr_teaching_material_lesson_contents)){
		// $arr_teaching_material_lesson_contents = array_column($arr_teaching_material_lesson_contents,'masta_japanese_root_id');
		foreach($arr_teaching_material_lesson_contents as $loop_teaching_material_lesson_contents){

			$t_masta_japanese_root_id = $loop_teaching_material_lesson_contents['masta_japanese_root_id'];
			$arr_strSQL_select = [
				[$t_registered_sentences,'id'],
				[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]]
			];
			
			$strSQL_from = " FROM
							$t_masta_japanese_root
							INNER JOIN $t_registered_sentences
							ON
							$t_masta_japanese_root.id = $t_registered_sentences.masta_japanese_root_id
							";
			
			$arr_strSQL_where = [
				[
					[
						[$t_masta_japanese_root,'id','=',$t_masta_japanese_root_id,'PDO::PARAM_INT','']
					],
					''
				]
			];
			
			$arr_strSQL_order = [];
			
			$strSQL_option = '';
			
			list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
			handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

			if(!empty($arr_masta_japanese_root)){
				$sentence_ids = array_column($arr_masta_japanese_root,'id');

				$arr_strSQL_select = [
					[$t_layers,'id']
				];
				
				$strSQL_from = " FROM $t_layers";
				
				$arr_strSQL_where = [
					[
						[
							[$t_layers,'registered_sentence_id',$str_sql_where_is_in,$sentence_ids,'PDO::PARAM_INT','']
						],
						''
					]
				];
				
				$arr_strSQL_order = [];
				
				$strSQL_option = '';
				
				list($pdo_has_error, $select_has_error, $e, $arr_layers) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
				handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);
				
				if(empty($arr_layers)){
					$str_no_layer .= '<p>'.$arr_masta_japanese_root[INDEX_FIRST][$arr_columns_masta_japanese_root[$int_selected_language]].'</p>';
				}
			}
			else{
				$str_no_sentence .= '<p>'.$loop_teaching_material_lesson_contents[$arr_columns_masta_japanese_root[$int_selected_language]].'</p>';
			}
		}
	}

	if(!$str_no_sentence){
		$str_no_sentence = 'なし';
	}

	echo '<h1>【例文未登録】</h1>';
	echo $str_no_sentence;
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '<br>';

	if(!$str_no_layer){
		$str_no_layer = 'なし';
	}

	echo '<h1>【レイヤー未登録】</h1>';
	echo $str_no_layer;
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '<br>';


	$result_with_usages = get_data_wise_map_view($int_selected_language, $user_id, $map_type, $target_id, $target_ids, $is_direct);
	
	$codes = array_column($result_with_usages['waypoint_data'], 'waypoint_unique_code');
	$table = get_data_grammar_usage_children_from_unique_codes($codes);
	
	$map_type = 'lesson_goal';
	$result_with_tasks = get_data_wise_map_view($int_selected_language, $user_id, $map_type, $target_id, $target_ids, $is_direct);
	
	echo '<h1>【grammar_usage_children】</h1>';
	echo $table;
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '<h1>【lesson_goal】</h1>';
	echo $result_with_tasks['waypoints_html'];
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '<br>';
	echo '<br>';
	exit;
	// タスク作成ーーーーーーーーーーーーーーーーーーーー	

}
