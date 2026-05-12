<?php

	require_once __DIR__ . '/../_bootstrap.php';

	$int_selected_language = INDEX_FIRST;

	$current_user = wp_get_current_user();
	$current_user_id = $current_user->ID;

	$user_level = get_user_level();

	if($user_level < $int_Administrator){
		echo '使用できません。';
		exit;
	}

	$arr_strSQL_select = [
		[$t_masta_japanese_root,'id as japaneseRootId'],
		[$t_masta_japanese_root,'search_criteria'],
		[$t_japanese_labels,'id as ' . $str_snake_to_camel_label_id],
		[$t_japanese_elements,'id as elementId'],
		[$t_masta_japanese_label,'id as mastaLabelId'],
		[$t_masta_japanese_label,'label_japanese'],
		[$t_masta_japanese_label,'label_kana'],
		[$t_japanese_elements,'masta_japanese_sub_classification_id']
	];

	$strSQL_from = " FROM
					(
						(
						$t_masta_japanese_label
						INNER JOIN $t_japanese_labels
						ON
						$t_masta_japanese_label.id = $t_japanese_labels.masta_japanese_label_id
						)
						INNER JOIN $t_japanese_elements
						ON
						$t_japanese_labels.japanese_element_id = $t_japanese_elements.id
					)
					INNER JOIN $t_masta_japanese_root
					ON
					$t_japanese_elements.masta_japanese_root_id = $t_masta_japanese_root.id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_word,'PDO::PARAM_INT','And'],
				[$t_masta_japanese_root,'root_japanese_polite','=',$str_avoid_null_proxy,'PDO::PARAM_STR','And'],
				[$t_japanese_labels,'main_label','=',1,'PDO::PARAM_INT','']
			],
			'Or'
		],
		[
			[
				[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_word,'PDO::PARAM_INT','And'],
				[$t_masta_japanese_root,'root_japanese_polite','=','','PDO::PARAM_STR','And'],
				[$t_japanese_labels,'main_label','=',1,'PDO::PARAM_INT','']
			],
			'Or'
		],
		[
			[
				[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_word,'PDO::PARAM_INT','And'],
				[$t_masta_japanese_root,'root_japanese_polite',$str_sql_where_is_null,'','PDO::PARAM_STR','And'],
				[$t_japanese_labels,'main_label','=',1,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_masta_japanese_root,'id','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(!empty($arr_masta_japanese_root)){

		foreach($arr_masta_japanese_root as $loot_masta_japanese_root){

			$t_masta_japanese_root_id = intval($loot_masta_japanese_root['japaneseRootId']);
			$t_masta_japanese_label_id = intval($loot_masta_japanese_root['mastaLabelId']);
			$t_japanese_element_id = intval($loot_masta_japanese_root['elementId']);
			$t_masta_japanese_sub_classification_id = intval($loot_masta_japanese_root['masta_japanese_sub_classification_id']);
			$t_masta_form_root_id = $int_PoliteFormAffirmativeNotPastTense;
			$int_label_id = intval($loot_masta_japanese_root[$str_snake_to_camel_label_id]);
			$int_voice_id = $int_id_default;
			$str_search_criteria = escape_html($loot_masta_japanese_root['search_criteria']);
			$str_japanese = escape_html($loot_masta_japanese_root['label_japanese']);
			$str_kana = escape_html($loot_masta_japanese_root['label_kana']);

			if($int_V1KU <= $t_masta_japanese_sub_classification_id && $t_masta_japanese_sub_classification_id <= $int_V3Z){

				$arr_indicator_labels = get_arr_indicator_label($int_label_id, false, $int_selected_language);
				$arr_inflected_label = get_arr_inflected_label($arr_indicator_labels, $t_masta_japanese_root_id, $t_japanese_element_id, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_voice_id, false, $int_selected_language);
				$str_japanese = $arr_inflected_label[$str_snake_to_camel_japanese];
				$str_kana = $arr_inflected_label[$str_snake_to_camel_kana];
			}

			$update_table = $t_masta_japanese_root;

			$arr_updateSQL = [
				['search_criteria',':update_search_criteria',$str_search_criteria.' '.$str_japanese.' '.$str_kana,'PDO::PARAM_STR'],
				['root_japanese_polite',':update_root_japanese_polite',$str_japanese,'PDO::PARAM_STR'],
				['root_kana_polite',':update_root_kana_polite',$str_kana,'PDO::PARAM_STR']
			];

			$arr_whereSQL = [
				['id',':where_id',$t_masta_japanese_root_id,'PDO::PARAM_INT','']
			];

			list($pdo_has_error, $insert_has_error, $e) = execute_update_data($update_table, $arr_updateSQL, $arr_whereSQL);

		}
		exit;
	}
