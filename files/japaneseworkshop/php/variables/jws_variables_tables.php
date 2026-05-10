<?php


/******************************************************
 *  DATABASE TABLE NAMES （ここからテーブル名）
 ******************************************************/
$prefix = 'y57ah5ym_1_';
$t_get_new_word_create_new_word = $prefix.'t_get_new_word_create_new_word';
$t_get_new_word_register_sentence = $prefix.'t_get_new_word_register_sentence';
$t_grammar_alternative_items = $prefix.'t_grammar_alternative_items';
$t_grammar_alternative_sets = $prefix.'t_grammar_alternative_sets';
$t_grammar_comparison_items = $prefix.'t_grammar_comparison_items';
$t_grammar_comparison_sets = $prefix.'t_grammar_comparison_sets';
$t_grammar_correspondence_items = $prefix.'t_grammar_correspondence_items';
$t_grammar_correspondence_sets = $prefix.'t_grammar_correspondence_sets';
$t_grammar_family_items = $prefix.'t_grammar_family_items';
$t_grammar_family_sets = $prefix.'t_grammar_family_sets';
$t_grammar_usage_categories = $prefix.'t_grammar_usage_categories';
$t_grammar_usage_children = $prefix.'t_grammar_usage_children';
$t_grammar_usage_parents = $prefix.'t_grammar_usage_parents';
$t_japanese_elements = $prefix.'t_japanese_elements';
$t_japanese_labels = $prefix.'t_japanese_labels';
$t_japanese_main_images = $prefix.'t_japanese_main_images';
$t_japanese_main_links = $prefix.'t_japanese_main_links';
$t_layer_element_overrides = $prefix.'t_layer_element_overrides';
$t_layer_elements = $prefix.'t_layer_elements';
$t_layers = $prefix.'t_layers';
$t_masta_form = $prefix.'t_masta_form';
$t_masta_form_root = $prefix.'t_masta_form_root';
$t_masta_inflection = $prefix.'t_masta_inflection';
$t_masta_japanese = $prefix.'t_masta_japanese';
$t_masta_japanese_attribute = $prefix.'t_masta_japanese_attribute';
$t_masta_japanese_category = $prefix.'t_masta_japanese_category';
$t_masta_japanese_classification = $prefix.'t_masta_japanese_classification';
$t_masta_japanese_description = $prefix.'t_masta_japanese_description';
$t_masta_japanese_label = $prefix.'t_masta_japanese_label';
$t_masta_japanese_main = $prefix.'t_masta_japanese_main';
$t_masta_japanese_root = $prefix.'t_masta_japanese_root';
$t_masta_japanese_section = $prefix.'t_masta_japanese_section';
$t_masta_japanese_sub_category = $prefix.'t_masta_japanese_sub_category';
$t_masta_japanese_sub_classification = $prefix.'t_masta_japanese_sub_classification';
$t_masta_override = $prefix.'t_masta_override';
$t_masta_override_operation = $prefix.'t_masta_override_operation';
$t_masta_step_unit_type = $prefix.'t_masta_step_unit_type';
$t_masta_wise_map_node = $prefix.'t_masta_wise_map_node';
$t_masta_wise_map_node_type = $prefix.'t_masta_wise_map_node_type';
$t_masta_wise_navigation_script = $prefix.'t_masta_wise_navigation_script';
$t_registered_sentence_elements = $prefix.'t_registered_sentence_elements';
$t_registered_sentence_translations = $prefix.'t_registered_sentence_translations';
$t_registered_sentences = $prefix.'t_registered_sentences';
$t_rooms = $prefix.'t_rooms';
$t_room_homeworks = $prefix.'t_room_homeworks';
$t_room_lesson_contents = $prefix.'t_room_lesson_contents';
$t_room_lesson_dates = $prefix.'t_room_lesson_dates';
$t_room_lesson_step_units = $prefix.'t_room_lesson_step_units';
$t_room_lesson_steps = $prefix.'t_room_lesson_steps';
$t_room_lessons = $prefix.'t_room_lessons';
$t_room_memo_dates = $prefix.'t_room_memo_dates';
$t_room_memos = $prefix.'t_room_memos';
$t_room_users = $prefix.'t_room_users';
$t_room_user_input_data = $prefix.'t_room_user_input_data';
$t_room_whiteboards = $prefix.'t_room_whiteboards';
$t_teaching_material_lesson_contents = $prefix.'t_teaching_material_lesson_contents';
$t_teaching_material_lesson_step_units = $prefix.'t_teaching_material_lesson_step_units';
$t_teaching_material_lesson_steps = $prefix.'t_teaching_material_lesson_steps';
$t_teaching_material_lessons = $prefix.'t_teaching_material_lessons';
$t_teaching_material_levels = $prefix.'t_teaching_material_levels';
$t_teaching_material_sets = $prefix.'t_teaching_material_sets';
$t_usage_historys = $prefix.'t_usage_historys';
$t_usage_textarea_values = $prefix.'t_usage_textarea_values';
$t_user_bookmarks = $prefix.'t_user_bookmarks';
$t_user_lessons = $prefix.'t_user_lessons';
$t_user_membership = $prefix.'t_user_membership';
$t_user_membership_apply = $prefix.'t_user_membership_apply';
$t_user_vip_invite_tokens = $prefix.'t_user_vip_invite_tokens';
$t_user_vip_requests = $prefix.'t_user_vip_requests';
$t_user_vip_invite_token_uses = $prefix.'t_user_vip_invite_token_uses';
$t_usermeta = 'y57ah5ym_usermeta';
$t_users = 'y57ah5ym_users';
$t_wise_map_node_can_do_links = $prefix.'t_wise_map_node_can_do_links';
$t_wise_map_node_links = $prefix.'t_wise_map_node_links';
$t_wise_map_node_usage_links = $prefix.'t_wise_map_node_usage_links';
$t_wise_navigation_items = $prefix.'t_wise_navigation_items';
$t_wise_navigation_scripts = $prefix.'t_wise_navigation_scripts';
$t_wise_navigation_waypoints = $prefix.'t_wise_navigation_waypoints';
$t_wise_navigations = $prefix.'t_wise_navigations';

/******************************************************
 *  COLUMN NAME ARRAYS （ここからカラム名）
 *  多言語カラムの取得に使用します。
 *  index 0 = Japanese
 *  index 1 = Chinese
 ******************************************************/

// masta_japanese_○○
$arr_columns_masta_japanese_root = ['root_japanese','root_chinese'];
$arr_columns_masta_japanese_section = ['section_japanese','section_chinese'];
$arr_columns_masta_japanese_main = ['main_japanese','main_chinese'];
$arr_columns_masta_japanese_description = ['description_japanese','description_chinese'];
$arr_columns_masta_japanese_attribute = ['attribute_japanese','attribute_chinese'];
$str_column_root_kana = 'root_kana';


// masta_labels
$str_column_masta_japanese_label_id = 'masta_japanese_label_id';
$str_column_label_japanese = 'label_japanese';
$str_column_label_kana = 'label_kana';
$str_column_main_label = 'main_label';

// grammar_usage
$arr_columns_grammar_usage_categories = ['usage_category_japanese','usage_category_chinese'];

// masta_teaching_material
$arr_columns_masta_teaching_material_sets = ['sets_japanese','sets_chinese'];
$arr_columns_masta_teaching_material_levels = ['level_japanese','level_chinese'];
$arr_columns_masta_teaching_material_lessons = ['lesson_japanese','lesson_chinese'];
$arr_columns_masta_teaching_material_lesson_objectives = ['objective_japanese','objective_chinese'];
$arr_columns_masta_teaching_material_lesson_steps = ['step_japanese','step_chinese'];

// masta_japanese_sub_category
$arr_columns_masta_japanese_sub_category = ['sub_category_japanese','sub_category_chinese'];


// masta_japanese_sub_classification
$arr_columns_masta_japanese_sub_classification = ['sub_classification_japanese','sub_classification_chinese'];


$arr_columns_masta_step_unit_types = ['unit_type_japanese','unit_type_chinese'];

$arr_columns_masta_override = ['display_japanese','display_chinese'];

$arr_columns_masta_wise_map_node = ['node_japanese','node_chinese'];

// wise_navigation
$arr_columns_wise_navigation_script_message_template = ['script_template_japanese','script_template_chinese'];
$arr_columns_wise_navigation_script_message = ['message_japanese','message_chinese'];