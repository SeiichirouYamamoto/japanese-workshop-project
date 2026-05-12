<?php

/******************************************************
 *  命名規則（関数の責務を明確にするための接頭辞ルール）
 *
 * build_html_〇〇_page
 *   ページ単位の最終的なHTML文字列を生成する。
 *   レイアウト・構成を含み、ショートコードや画面表示の起点となる。
 *
 * build_html_
 *   ページを構成する部品（パーツ）をHTML文字列として生成する。
 *   単体で表示可能だが、最終ページの責務は持たない。
 *
 * switch_build_html_
 *   条件分岐を行い、どの build_html_ 関数を呼び出すかを決定する。
 *   自身ではHTMLを組み立てず、生成処理は委譲する。
 *   主に pageType・mode・quiz種別などによる振り分けを担当する。
 *
 * fetch_arr_
 *   データベースから生の配列データを取得する。
 *   原則として加工や表示用変換は行わない。
 *   引数には、検索条件（ID・状態・フラグなど）を指定する。
 *
 * get_arr_
 *   データベースから取得したデータをもとに、
 *   加工・整形・絞り込みを行った配列データを返す。
 *   表示やロジックでそのまま利用できる形であることを前提とする。
 *   引数には、検索条件や取得条件を指定する。
 *
 * get_data_
 *   複数の関連データ（例：html / flag / title / status など）を
 *   ひとまとめにして返すための取得関数。
 *   呼び出し側で分岐判断に使われることを前提とする。
 *   引数には、検索条件や取得条件を指定する。
 *
 * generate_
 *   引数として渡された材料（値・配列・設定など）をもとに、
 *   新しい値・構造・表現を作り出す。
 *   既存データの取得や単純な加工ではなく、
 *   「新しく成立させる」ことが責務。
 *   引数には、生成に必要な材料を渡す。
 *
 * apply_
 *   引数として渡された既存の値・配列・構造を基準に、
 *   ルール・差分・設定・装飾などを適用する。
 *   新しいものをゼロから生成するのではなく、
 *   元のデータに意味や変更を重ねることを目的とする。
 *   原則として副作用は持たない。
 *   引数には、加工対象となる材料を渡す。
 *
 * execute_
 *   database専用。ほかのファイルでは使わない。
 *   登録・更新・削除などの実行系処理を行う。
 *   データベース操作や副作用を伴う処理を明示するための接頭辞。
 *
 * handle_
 *   すでに発生した状態（結果・エラー・例外など）を受け取り、
 *   それに対する対応処理（分岐・終了・通知など）を行う。
 *   処理そのものではなく「事後対応」を担当する。
 *
 * escape_
 *   表示・出力・DB操作の前に値を安全な形に変換する。
 *   XSS・型不正・意図しない入力を防ぐことを目的とする。
 *
 * validate_
 *   入力値や状態が正しいかを検証する。
 *   原則として副作用は持たず、判定結果を返す。
 *
 * ensure_
 *   前提条件や必須状態を保証する。
 *   条件を満たさない場合は、エラー・例外・強制終了などを行う。
 * 
 * count_
 *   配列やデータ構造をもとに、
 *   件数・回数・内訳などを集計して数値を返す。
 *   副作用は持たず、純粋な計算処理を行う。
 * 
 * 
 * 
 * domain	必須	wise / workshop / quiz / grammar	機能の大分類
 * feature	任意	sorting / particle	サブ機能・クイズ種別 2階層もあり得る
 * action	必須
 * 
| 動詞       | 用途             |
| -------- | -------------- |
show	表示・可視化（開く・展開）
hide	非表示
toggle	ON/OFF
navigate	画面遷移（naviより明確）
create	新規作成
update	更新
delete	削除
submit	送信
restart	再開始（quiz系専用）
select	選択
focus	フォーカス移動


 ******************************************************/

/******************************************************
 *  ESCAPE
 *  
 ******************************************************/
function escape_html($s){
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}


function escape_html_with_nl2br($s){
	return nl2br(escape_html($s));
}



/******************************************************
 *  TOKEN
 *  
 ******************************************************/
function generate_csrf_token(){
	$toke_byte = openssl_random_pseudo_bytes(16);
	$csrf_token = bin2hex($toke_byte);
	$_SESSION['csrf_token'] = $csrf_token;
	return $csrf_token;
}


function validate_csrf_token() {
	
	if (!isset($_POST['csrf_token'])) {
		return false;
	}
	
	$token_post = $_POST['csrf_token'];
	
	if (!isset($_SESSION['csrf_token'])) {
		return false;
	}
	
	$token_session = $_SESSION['csrf_token'];
	unset($_SESSION['csrf_token']);
	
	return $token_post === $token_session;
}



/******************************************************
 *  ENSURE
 *  
 ******************************************************/
function ensure_permission_room($int_selected_language){

	$user_level = get_user_level();
	if(is_teacher_level($user_level)){
		return;
	}
	exit;
}


function ensure_user_can_access_room($room_id, $int_selected_language){

	global
		$t_room_users,
		$t_rooms;

	$current_user = wp_get_current_user();
	$current_user_id = (int)$current_user->ID;

    $arr_strSQL_select = [
        [$t_room_users, 'id']
    ];
    
    $strSQL_from = "
        FROM
            $t_rooms
            INNER JOIN $t_room_users
            ON
            $t_rooms.id = $t_room_users.room_id
    ";

    $arr_strSQL_where = [
        [
            [
                [$t_room_users, 'user_id', '=', $current_user_id, 'PDO::PARAM_INT', 'And'],
                [$t_rooms, 'id', '=', $room_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_rooms, 'sort', 'ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_room_users) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    if (empty($arr_room_users)) {
        return false;
    }

    return true;
}
/******************************************************
 *  VALIDATION
 *  
 ******************************************************/
function validate_request_method($request_method, $int_selected_language){
	if($_SERVER['REQUEST_METHOD'] !== $request_method) {
		redirect_to_home_with_notice($int_selected_language);
	}
}


function validate_integer_or_redirect($value, $int_selected_language){
	if(!preg_match("/^[0-9]+$/",$value)){
		redirect_to_home_with_notice($int_selected_language);
	}
}


function validate_integer_or_exit($value){
	if(!preg_match("/^[0-9]+$/",$value)){
		exit;
	}
}


function validate_quiz_unique_code_and_get_room_unique_code($int_selected_language){

	global
		$t_room_users,
		$t_rooms,
		$str_option_value_default;

	$unique_code = escape_html($_GET['uniqueCode'] ?? '');
	$room_id = fetch_room_id_from_unique_code($unique_code, $int_selected_language);
	
	$current_user = wp_get_current_user();
	$current_user_id = $current_user->ID;

	$arr_strSQL_select = [
		[$t_room_users,'id'],
		[$t_room_users,'room_id'],
		[$t_room_users,'user_id'],
		[$t_rooms,'unique_code']
	];
	
	$strSQL_from = " FROM
				$t_rooms
				INNER JOIN $t_room_users
				ON
				$t_rooms.id = $t_room_users.room_id 
				";
	
	$arr_strSQL_where = [
		[
			[
				[$t_room_users,'room_id','=',$room_id,'PDO::PARAM_INT','And'],
				[$t_room_users,'user_id','=',$current_user_id,'PDO::PARAM_INT','And'],
				[$t_room_users,'confirmed','=',FLAG_TRUE,'PDO::PARAM_INT','']
			],
			''
		]
	];
	
	$arr_strSQL_order = [];
	
	$strSQL_option = '';
	
	list($pdo_has_error, $select_has_error, $e, $arr_room_users) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	if(!empty($arr_room_users)){
		return $arr_room_users[INDEX_FIRST]['unique_code'];
	}
	else{
		return $str_option_value_default;
	}

}


function validate_teaching_material_range($int_start_id, $int_end_id, $int_selected_language){

	global
		$t_teaching_material_sets,
		$t_teaching_material_levels,
		$t_teaching_material_lessons;

	$arr_ids = [
		'start' => $int_start_id,
		'end' => $int_end_id
	];
	$arr_comparison = [];

	// 未定義id
	$t_teaching_material_sets_id = 0;

	foreach($arr_ids as $key => $loop_ids){

		$arr_strSQL_select = [
			[$t_teaching_material_sets,'id'],
			[$t_teaching_material_lessons,'sort']
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
					[$t_teaching_material_lessons,'id','=',intval($loop_ids),'PDO::PARAM_INT','']
				],
				''
			]
		];
		
		$arr_strSQL_order = [];
		
		$strSQL_option = '';
		
		list($pdo_has_error, $select_has_error, $e, $arr_sort_info) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
		handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

		if(empty($arr_sort_info)){
			return $t_teaching_material_sets_id;
		}

		$arr_sort_info = $arr_sort_info[INDEX_FIRST];

		$arr_comparison[] = $arr_sort_info['id'];

	}
	
	// 未定義id
	$t_teaching_material_sets_id = ($arr_comparison[INDEX_FIRST] === $arr_comparison[INDEX_SECOND]) ? $arr_comparison[INDEX_FIRST] : 0;

	return $t_teaching_material_sets_id;
}



/******************************************************
 *  EXIST
 *  
 ******************************************************/
function layers_exist($int_registered_sentence_id, $int_selected_language) {
	
	global
		$t_layers;

    $arr_strSQL_select = [
        [$t_layers, 'id']
    ];
    $strSQL_from = ' FROM ' . $t_layers;
    $arr_strSQL_where = [
        [
            [
                [$t_layers, 'registered_sentence_id', '=', $int_registered_sentence_id, 'PDO::PARAM_INT', '']
            ]
            ,
            ''
        ]
    ];
    $arr_strSQL_order = [
        [$t_layers, 'sort', 'ASC']
    ];

    list($pdo_has_error, $select_has_error, $e, $arr_layers) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, '');
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    return !empty($arr_layers);
}


function has_level_sentences(int $level, int $int_selected_language): bool
{

	global
		$t_registered_sentences,
		$t_masta_japanese_root;

    $int_mastery_level = $level;

    $arr_strSQL_select = [
        [$t_registered_sentences, 'id']
    ];

    $strSQL_from = " FROM
                    $t_registered_sentences
                    INNER JOIN $t_masta_japanese_root
                    ON
                    $t_registered_sentences.masta_japanese_root_id = $t_masta_japanese_root.id
                    ";

    $arr_strSQL_where = [
        [
            [
                [$t_registered_sentences, 'is_published', '=', FLAG_TRUE, 'PDO::PARAM_INT', 'And'],
                [$t_masta_japanese_root, 'jws_level', '=', $int_mastery_level, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [];
    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_registered_sentences) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );

    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    if ($pdo_has_error || $select_has_error) {
        return false;
    }

    return count($arr_registered_sentences) >= 30;
}



/******************************************************
 *  REDIRECT
 *  
 ******************************************************/
function redirect_to_home_with_notice($int_selected_language){
	
	$str_coution_return_to_home = [
		'情報更新の為、一度 home へ戻ってください',
		'為了重新資料,請回首頁'
	];

	$str_notice = $str_coution_return_to_home[$int_selected_language];
	fail_and_redirect_home($str_notice,$int_selected_language);
	exit();

}


function fail_and_redirect_home($str_notice, $int_selected_language){

	global
		$int_color_blue,
		$int_rgb_r_deep,
		$int_rgb_g_deep,
		$int_rgb_b_deep;

	$button_background_color = 'rgb('
		. $int_rgb_r_deep[$int_color_blue] . ', '
		. $int_rgb_g_deep[$int_color_blue] . ', '
		. $int_rgb_b_deep[$int_color_blue] . ')';

	$button_background_color = escape_html($button_background_color);
	
	$content_form = build_html_form_button_return_to_home(
		$button_background_color,
		$int_selected_language
	);
		
	$str_notice = escape_html_with_nl2br($str_notice);

	$content_message = implode(
		'',
		[
			'<div class="divWide">',
				'<p class="commonTextContent">' . $str_notice . '</p>',
			'</div>'
		]
	);


	$content_notice = $content_message . $content_form;

	$class = 'frame frameRed';
	$caution_class = 'frameTitle cautionRed';
	$frame_notice = build_html_div_frame($class, 'Caution', $content_notice, $caution_class);
	echo $frame_notice;

	exit();

}

function build_html_form_button_return_to_home(
    string $button_background_color,
    int $int_selected_language,
    string $class_div = 'divChoices',
    string $class_input = 'inputChoices'
): string {

    $arr_str_return_to_home = ['ホームに戻る', '回首頁'];
    $label = $arr_str_return_to_home[$int_selected_language] ?? $arr_str_return_to_home[0];

    $url_home_current = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		'/'
	);

    return implode(
        '',
        [
            '<form action="' . esc_url($url_home_current) . '" method="GET">',
                '<div class="' . escape_html($class_div) . '">',
                    '<input class="' . escape_html($class_input) . '" style="background-color:' . escape_html($button_background_color) . '" type="submit" value="' . escape_html($label) . '">',
                '</div>',
            '</form>'
        ]
    );
}



/******************************************************
 *  FETCH
 *  
 ******************************************************/
function fetch_arr_masta_japanese_root_from_unique_code($grammar_unique_code, $int_selected_language){

	global
		$t_masta_japanese_root,
		$arr_columns_masta_japanese_root,
		$str_column_root_kana,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana;

	$arr_strSQL_select = [
		[$t_masta_japanese_root,'id'],
		[$t_masta_japanese_root,'root_example'],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language] . ' as ' . $str_snake_to_camel_japanese],
		[$t_masta_japanese_root,$str_column_root_kana],
		[$t_masta_japanese_root,$str_column_root_kana . ' as ' . $str_snake_to_camel_kana],
	];

	$strSQL_from = " FROM $t_masta_japanese_root";

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_masta_japanese_root,'unique_code','=',$grammar_unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_masta_japanese_root;

}


function fetch_arr_masta_japanese_root_by_search_conditions($arr_strSQL_where, $int_selected_language){
	
	global
		$t_masta_japanese_root,
		$t_masta_japanese,
		$t_masta_japanese_sub_category,
		$arr_columns_masta_japanese_root,
		$int_used_language_jpn,
		$str_column_root_kana,
		$str_snake_to_camel_japanese_id,
		$str_snake_to_camel_unique_code,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$str_snake_to_camel_parent_sort,
		$str_snake_to_camel_category_id;

	$arr_strSQL_select = [
		[$t_masta_japanese_root,'id as '.$str_snake_to_camel_japanese_id],
		[$t_masta_japanese_root,'unique_code as '.$str_snake_to_camel_unique_code],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language].' as '.$str_snake_to_camel_japanese],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_used_language_jpn]],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]],
		[$t_masta_japanese_root,$str_column_root_kana.' as '.$str_snake_to_camel_kana],
		[$t_masta_japanese,'sort as '.$str_snake_to_camel_parent_sort],
		[$t_masta_japanese_sub_category,'category_id as '.$str_snake_to_camel_category_id]
	];
	
	$strSQL_from = " FROM
					(
						$t_masta_japanese_root
						INNER JOIN $t_masta_japanese
						ON
						$t_masta_japanese_root.masta_id = $t_masta_japanese.id
					)
					INNER JOIN $t_masta_japanese_sub_category
					ON
					$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
					";
	
	$arr_strSQL_where = $arr_strSQL_where;
	
	$arr_strSQL_order = [
		[$t_masta_japanese_root,'sub_category_id','ASC'],
		[$t_masta_japanese_root,'jws_level','ASC'],
		[$t_masta_japanese_root,'sort','ASC']
	];
	
	$strSQL_option = '';
	
	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

	return $arr_masta_japanese_root;
}


function fetch_arr_masta_japanese_root_by_bookmarks(
    $int_search_scope,
    $room_id,
    $int_bookmark_filter,
    $int_selected_language
){

    global
        $t_masta_japanese_root,
        $t_masta_japanese_sub_category,
        $t_user_bookmarks,
        $arr_columns_masta_japanese_root,
        $int_used_language_jpn,
        $str_column_root_kana,
        $str_snake_to_camel_japanese_id,
        $str_snake_to_camel_unique_code,
        $str_snake_to_camel_japanese,
        $str_snake_to_camel_kana,
        $str_snake_to_camel_category_id,
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

    }
    elseif ((int)$int_search_scope === $search_scope_room_members) {

        $final_room_id = (int)$room_id;
        $final_user_id = null;

    }
    elseif ((int)$int_search_scope === $search_scope_room_owner_user) {

        $final_room_id = (int)$room_id;

        $room_owner_user_id = (int)fetch_room_owner_user_id_from_room_id(
            $final_room_id,
            $int_selected_language
        );

        if ($room_owner_user_id <= 0) {
            respond_error('Room owner not found.', 404);
            return [];
        }

        $final_user_id = $room_owner_user_id;

    }
    else {
        respond_error('Invalid search scope.', 400);
        return [];
    }

    $arr_strSQL_select = [
        [$t_masta_japanese_root, 'id as ' . $str_snake_to_camel_japanese_id],
        [$t_masta_japanese_root, 'unique_code as ' . $str_snake_to_camel_unique_code],
        [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language] . ' as ' . $str_snake_to_camel_japanese],
        [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_used_language_jpn]],
        [$t_masta_japanese_root, $arr_columns_masta_japanese_root[$int_selected_language]],
        [$t_masta_japanese_root, $str_column_root_kana . ' as ' . $str_snake_to_camel_kana],
        [$t_masta_japanese_sub_category, 'category_id as ' . $str_snake_to_camel_category_id]
    ];

    $strSQL_from = " FROM
                    (
                        $t_user_bookmarks
                        INNER JOIN $t_masta_japanese_root
                        ON
                        $t_user_bookmarks.masta_japanese_root_id = $t_masta_japanese_root.id
                    )
                    INNER JOIN $t_masta_japanese_sub_category
                    ON
                    $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
                    ";

    $arr_strSQL_where = build_arr_where_user_bookmarks_scope(
        (int)$int_search_scope,
        $final_room_id,
        $final_user_id,
        (int)$int_bookmark_filter
    );

    if (empty($arr_strSQL_where)) {
        return [];
    }

    $arr_strSQL_order = [
        [$t_masta_japanese_root, 'sub_category_id', 'ASC'],
        [$t_masta_japanese_root, 'jws_level', 'ASC'],
        [$t_masta_japanese_root, 'sort', 'ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_root) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

    return $arr_masta_japanese_root;

}


function build_arr_where_user_bookmarks_scope(
    int $int_search_scope,
    ?int $room_id,
    ?int $user_id,
    int $int_bookmark_filter
): array {

    global
        $t_user_bookmarks,
        $search_scope_current_user,
        $search_scope_room_members,
        $search_scope_room_owner_user,
        $bookmark_filter_active,
        $bookmark_filter_inactive,
        $str_sql_where_is_null,
        $str_sql_where_is_not_null;

    $arr_where_conditions = [];

    // === スコープ条件 ===
    if ($int_search_scope === $search_scope_current_user) {

        if ($user_id === null || $user_id <= 0) {
            respond_error('Invalid user_id for current_user scope.', 400);
            return [];
        }

        $arr_where_conditions[] =
            [$t_user_bookmarks, 'user_id', '=', $user_id, 'PDO::PARAM_INT', 'And'];

    }
    elseif ($int_search_scope === $search_scope_room_members) {

        if ($room_id === null || $room_id <= 0) {
            respond_error('Invalid room_id for room_members scope.', 400);
            return [];
        }

        // room内の全ブックマーク（user_id条件なし）
        $arr_where_conditions[] =
            [$t_user_bookmarks, 'room_id', '=', $room_id, 'PDO::PARAM_INT', 'And'];

    }
    elseif ($int_search_scope === $search_scope_room_owner_user) {

        if ($room_id === null || $room_id <= 0 || $user_id === null || $user_id <= 0) {
            respond_error('Invalid room_id/user_id for room_owner_user scope.', 400);
            return [];
        }

        $arr_where_conditions[] =
            [$t_user_bookmarks, 'room_id', '=', $room_id, 'PDO::PARAM_INT', 'And'];
        $arr_where_conditions[] =
            [$t_user_bookmarks, 'user_id', '=', $user_id, 'PDO::PARAM_INT', 'And'];

    }
    else {
        respond_error('Invalid search scope.', 400);
        return [];
    }

    // === ブックマーク状態フィルタ（deleted_at）===
    if ($int_bookmark_filter === $bookmark_filter_active) {

        $arr_where_conditions[] =
            [$t_user_bookmarks, 'deleted_at', $str_sql_where_is_null, null, null, ''];

    }
    elseif ($int_bookmark_filter === $bookmark_filter_inactive) {

        $arr_where_conditions[] =
            [$t_user_bookmarks, 'deleted_at', $str_sql_where_is_not_null, null, null, ''];

    }
    elseif ($int_bookmark_filter === SELECT_ALL) {

        return [];

    }
    else {
        respond_error('Invalid bookmark filter.', 400);
        return [];
    }

    return [
        [
            $arr_where_conditions,
            ''
        ]
    ];
}


function fetch_arr_masta_japanese_root_ids_by_lessons($arr_lessons, $int_selected_language) {

	global
		$t_teaching_material_lesson_contents,
		$t_teaching_material_lesson_step_units,
		$t_teaching_material_lesson_steps,
		$t_room_lesson_contents,
		$t_room_lesson_step_units,
		$t_room_lesson_steps,
		$str_sql_where_is_in;

	$tm_lesson_ids = array_values(array_unique(array_map(fn($r) => (int)$r['teaching_material_lesson_id'], $arr_lessons)));
	$room_lesson_ids = array_values(array_unique(array_map(fn($r) => (int)$r['id'], $arr_lessons)));

	$order_dummy = [];
	$option_dummy = '';
	$rows_tm = [];
	$rows_room = [];

	if (!empty($tm_lesson_ids)) {
		$sel_tm = [['DISTINCT ' . $t_teaching_material_lesson_contents, 'masta_japanese_root_id']];
		$from_tm = "
			FROM $t_teaching_material_lesson_contents
			INNER JOIN $t_teaching_material_lesson_step_units
				ON $t_teaching_material_lesson_contents.step_unit_id = $t_teaching_material_lesson_step_units.id
			INNER JOIN $t_teaching_material_lesson_steps
				ON $t_teaching_material_lesson_step_units.lesson_step_id = $t_teaching_material_lesson_steps.id
		";
		$where_tm = [[ [[$t_teaching_material_lesson_steps, 'lesson_id', $str_sql_where_is_in, $tm_lesson_ids, 'PDO::PARAM_INT', '']], '' ]];
		list($e1, $e2, $e, $rows_tm) = execute_select_and_fetch_all($sel_tm, $from_tm, $where_tm, $order_dummy, $option_dummy);
	}

	if (!empty($room_lesson_ids)) {
		$sel_room = [['DISTINCT ' . $t_room_lesson_contents, 'masta_japanese_root_id']];
		$from_room = "
			FROM $t_room_lesson_contents
			INNER JOIN $t_room_lesson_step_units
				ON $t_room_lesson_contents.step_unit_id = $t_room_lesson_step_units.id
			INNER JOIN $t_room_lesson_steps
				ON $t_room_lesson_step_units.lesson_step_id = $t_room_lesson_steps.id
		";
		$where_room = [[ [[$t_room_lesson_steps, 'lesson_id', $str_sql_where_is_in, $room_lesson_ids, 'PDO::PARAM_INT', '']], '' ]];
		list($e3, $e4, $e, $rows_room) = execute_select_and_fetch_all($sel_room, $from_room, $where_room, $order_dummy, $option_dummy);
	}

	$ids = array_merge(
		array_column($rows_tm ?: [], 'masta_japanese_root_id'),
		array_column($rows_room ?: [], 'masta_japanese_root_id')
	);

	return array_values(array_unique($ids, SORT_NUMERIC));
}


function fetch_arr_masta_japanese_sub_categories_for_grammar($int_selected_language){

	global
		$t_masta_japanese_sub_category,
		$arr_columns_masta_japanese_sub_category,
		$int_masta_japanese_category_id_grammar;


	$arr_strSQL_select = [
		[$t_masta_japanese_sub_category,'id'],
		[$t_masta_japanese_sub_category,'category_id'],
		[$t_masta_japanese_sub_category,$arr_columns_masta_japanese_sub_category[$int_selected_language]]
	];

	$strSQL_from = ' FROM ' .$t_masta_japanese_sub_category;

	$arr_strSQL_where = [
		[
			[
				[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_grammar,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_masta_japanese_sub_category,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_sub_category) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_masta_japanese_sub_category;
}


function fetch_arr_masta_japanese_classification_for_quiz($int_selected_language){
	
	global
		$t_masta_japanese_classification,
		$t_masta_japanese_root,
		$arr_columns_masta_japanese_root,
		$str_snake_to_camel_unique_code,
		$int_masta_japanese_classification_id_verb,
		$int_masta_japanese_classification_id_i_adjective,
		$int_masta_japanese_classification_id_na_adjective,
		$int_masta_japanese_classification_id_noun;

	$arr_strSQL_select = [
		[$t_masta_japanese_classification,'id'],
		[$t_masta_japanese_classification,'masta_japanese_root_id'],
		[$t_masta_japanese_root,'unique_code as '.$str_snake_to_camel_unique_code],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]]
	];

	$strSQL_from = " FROM
					$t_masta_japanese_classification
					INNER JOIN $t_masta_japanese_root
					ON
					$t_masta_japanese_classification.masta_japanese_root_id = $t_masta_japanese_root.id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_masta_japanese_classification,'id','=',$int_masta_japanese_classification_id_verb,'PDO::PARAM_INT','Or'],
				[$t_masta_japanese_classification,'id','=',$int_masta_japanese_classification_id_i_adjective,'PDO::PARAM_INT','Or'],
				[$t_masta_japanese_classification,'id','=',$int_masta_japanese_classification_id_na_adjective,'PDO::PARAM_INT','Or'],
				[$t_masta_japanese_classification,'id','=',$int_masta_japanese_classification_id_noun,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_masta_japanese_classification,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_classification) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_masta_japanese_classification;
}


function fetch_arr_masta_form_root_for_quiz($int_selected_language){
	
	global
		$arr_inflection_for_quiz,
		$t_masta_form,
		$t_masta_form_root,
		$t_masta_japanese_root,
		$arr_columns_masta_japanese_root,
		$str_snake_to_camel_unique_code;
	
	$last_key_inflection = end(array_keys($arr_inflection_for_quiz));
	foreach($arr_inflection_for_quiz as $key => $item){
		if ($key === $last_key_inflection) {
			$arr_strSQL_where_inflection[] = [$t_masta_form_root,'id','=',intval($item),'PDO::PARAM_INT',''];
		} else {
			$arr_strSQL_where_inflection[] = [$t_masta_form_root,'id','=',intval($item),'PDO::PARAM_INT','Or'];
		}
	}

	$arr_strSQL_select = [
		[$t_masta_form_root,'id'],
		[$t_masta_form_root,'masta_id'],
		[$t_masta_form_root,'masta_japanese_root_id'],
		[$t_masta_japanese_root,'unique_code as '.$str_snake_to_camel_unique_code],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]]
	];

	$strSQL_from = " FROM
					(
						$t_masta_form
						INNER JOIN $t_masta_form_root
						ON
						$t_masta_form.id = $t_masta_form_root.masta_id
					)
					INNER JOIN $t_masta_japanese_root
					ON
					$t_masta_form_root.masta_japanese_root_id = $t_masta_japanese_root.id
					";

	$arr_strSQL_where = [
		[
			$arr_strSQL_where_inflection,
			''
		]
	];

	$arr_strSQL_order = [
		[$t_masta_form,'sort','ASC'],
		[$t_masta_form_root,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_form_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_masta_form_root;
}


function fetch_arr_masta_japanese_root_from_parent_to_child($arr_belongs, $isOriginalSort, $t_masta_japanese_root_id, $arr_allow_display, $map_parent_to_children, $int_selected_language){

	if(empty($arr_allow_display)){
		$arr_allow_display = get_arr_temp_already_learned_list($int_selected_language);
	}

	$arr_matched = [];
	foreach($arr_belongs as $loop_belongs){
		$arr_target = fetch_arr_grammar_usage_children_by_attribute($t_masta_japanese_root_id, $loop_belongs, $map_parent_to_children, $int_selected_language);
		$arr_matched = array_merge($arr_matched,$arr_target);
	}

	$arr_uniqued = [];
	foreach ($arr_matched as $item) {
		$arr_uniqued[$item['masta_japanese_root_id']] = $item;
	}

	$arr_masta_japanese_root = [];

	if(empty($arr_uniqued)){
		return $arr_masta_japanese_root;
	}

	if($isOriginalSort){
		foreach($arr_uniqued as $loop_uniqued){
			$target_id = $loop_uniqued['masta_japanese_root_id'];
			$grammar_outline_status = $loop_uniqued['grammar_outline_status'];
			if(in_array($target_id, $arr_allow_display)) {
				$add_arr_masta_japanese_root = fetch_arr_masta_japanese_root_default($target_id, $int_selected_language);
				$add_arr_masta_japanese_root['grammar_outline_status'] = $grammar_outline_status;
				$arr_masta_japanese_root[] = $add_arr_masta_japanese_root;
			}
		}
	}
	else{
		foreach($arr_allow_display as $target_id){
			$grammar_outline_status = null;
			foreach ($arr_uniqued as $item) {
				if (intval($item['masta_japanese_root_id']) === intval($target_id)) {
					$grammar_outline_status = intval($item['grammar_outline_status']);
					break;
				}
			}
			if($grammar_outline_status) {
				$add_arr_masta_japanese_root = fetch_arr_masta_japanese_root_default($target_id, $int_selected_language);
				$add_arr_masta_japanese_root['grammar_outline_status'] = $grammar_outline_status;
				$arr_masta_japanese_root[] = $add_arr_masta_japanese_root;
			}
		}
	}
	return $arr_masta_japanese_root;
}

function fetch_arr_form_root_list_all($int_selected_language){

	global
		$t_masta_form,
		$t_masta_form_root,
		$t_masta_japanese_root,
		$arr_columns_masta_japanese_root;

	$arr_strSQL_select = [
		[$t_masta_form_root,'id'],
		[$t_masta_form_root,'masta_japanese_root_id'],
		[$t_masta_japanese_root,'unique_code'],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]]
	];

	$strSQL_from = " FROM
					(
						$t_masta_form
						INNER JOIN $t_masta_form_root
						ON
						$t_masta_form.id = $t_masta_form_root.masta_id
					)
					INNER JOIN $t_masta_japanese_root
					ON
					$t_masta_form_root.masta_japanese_root_id = $t_masta_japanese_root.id
					";

	$arr_strSQL_where = [];

	$arr_strSQL_order = [
		[$t_masta_form,'sort','ASC'],
		[$t_masta_form_root,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_form_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	return $arr_masta_form_root;
}

function fetch_arr_form_root_list($arr_already_learned_list, $int_selected_language){

	global
		$t_masta_form,
		$t_masta_form_root,
		$t_masta_japanese_root,
		$arr_columns_masta_japanese_root;

	$arr_form_list = [];

	$arr_strSQL_select = [
		[$t_masta_form_root,'id'],
		[$t_masta_form_root,'masta_japanese_root_id'],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language].' as title']
	];

	$strSQL_from = " FROM
					(
						$t_masta_form
						INNER JOIN $t_masta_form_root
						ON
						$t_masta_form.id = $t_masta_form_root.masta_id
					)
					INNER JOIN $t_masta_japanese_root
					ON
					$t_masta_form_root.masta_japanese_root_id = $t_masta_japanese_root.id
					";

	$arr_strSQL_where = [];

	$arr_strSQL_order = [
		[$t_masta_form,'sort','ASC'],
		[$t_masta_form_root,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_form_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_masta_form_root)){
		return $arr_form_list;
	}

	if (!empty($arr_already_learned_list)) {
		$arr_form_list_result = array_filter($arr_masta_form_root, function ($item) use ($arr_already_learned_list) {
			return in_array($item['masta_japanese_root_id'], $arr_already_learned_list);
		});
	}
	else{
		$arr_form_list_result = $arr_masta_form_root;
	}

	return $arr_form_list_result;
}


function fetch_arr_voice_form_root_list($arr_already_learned_list, $int_selected_language){

	global
		$t_masta_form,
		$t_masta_form_root,
		$t_masta_japanese_root,
		$arr_columns_masta_japanese_root;

	$arr_voice_list = [];

	$arr_strSQL_select = [
		[$t_masta_form_root,'id'],
		[$t_masta_form_root,'masta_japanese_root_id'],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language].' as title']
	];

	$strSQL_from = " FROM
					(
						$t_masta_form
						INNER JOIN $t_masta_form_root
						ON
						$t_masta_form.id = $t_masta_form_root.masta_id
					)
					INNER JOIN $t_masta_japanese_root
					ON
					$t_masta_form_root.masta_japanese_root_id = $t_masta_japanese_root.id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_masta_form_root,'is_voice','=',FLAG_TRUE,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_masta_form,'sort','ASC'],
		[$t_masta_form_root,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_form_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_masta_form_root)){
		return $arr_voice_list;
	}

	if (!empty($arr_already_learned_list)) {
		$arr_voice_list_result = array_filter($arr_masta_form_root, function ($item) use ($arr_already_learned_list) {
			return in_array($item['masta_japanese_root_id'], $arr_already_learned_list);
		});
	}
	else{
		$arr_voice_list_result = $arr_masta_form_root;
	}

	return $arr_voice_list_result;
}


function fetch_arr_masta_override_list($int_selected_language){

	global
		$t_masta_override,
		$t_masta_override_operation,
		$arr_columns_masta_override;

	$arr_masta_override = [];

	$arr_strSQL_select = [
		[$t_masta_override,'id'],
		[$t_masta_override,$arr_columns_masta_override[$int_selected_language]],
		[$t_masta_override_operation,'operation']
	];

	$strSQL_from = " FROM
					$t_masta_override
					INNER JOIN $t_masta_override_operation
					ON
					$t_masta_override.operation_id  = $t_masta_override_operation.id
					";

	$arr_strSQL_where = [];

	$arr_strSQL_order = [
		[$t_masta_override_operation,'sort','ASC'],
		[$t_masta_override,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_override) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_masta_override;
}


/******************************************************
 *  fetch: 最新1件（user_id）
 ******************************************************/
function fetch_arr_user_membership_apply_latest_by_user_id($current_user_id){

    global
        $t_user_membership_apply;

    $arr_latest_apply = [];

    $arr_strSQL_select = [
        [$t_user_membership_apply, 'id'],
        [$t_user_membership_apply, 'user_id'],
        [$t_user_membership_apply, 'apply_level'],
        [$t_user_membership_apply, 'apply_status'],
        [$t_user_membership_apply, 'agreed_at'],
        [$t_user_membership_apply, 'apply_message'],
        [$t_user_membership_apply, 'created_at'],
        [$t_user_membership_apply, 'updated_at']
    ];

    $strSQL_from = " FROM $t_user_membership_apply ";

    $arr_strSQL_where = [
        [
            [
                [$t_user_membership_apply, 'user_id', '=', (int) $current_user_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_user_membership_apply, 'id', 'DESC']
    ];

    $strSQL_option = ' LIMIT 1 ';

    list($pdo_has_error, $select_has_error, $e, $arr_apply) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );

    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    if(empty($arr_apply)){
        return $arr_latest_apply;
    }

    return $arr_apply[0] ?? $arr_latest_apply;
}


/******************************************************
 *  fetch: 最新1件（user_id + status）
 *
 *  例: pending / approved / rejected / cancelled
 ******************************************************/
function fetch_arr_user_membership_apply_latest_by_user_id_and_status($current_user_id, $apply_status){

    global
        $t_user_membership_apply;

    $arr_latest_apply = [];

    $apply_status = (string) ($apply_status ?? '');
    if($apply_status === ''){
        return $arr_latest_apply;
    }

    $arr_strSQL_select = [
        [$t_user_membership_apply, 'id'],
        [$t_user_membership_apply, 'user_id'],
        [$t_user_membership_apply, 'apply_level'],
        [$t_user_membership_apply, 'apply_status'],
        [$t_user_membership_apply, 'agreed_at'],
        [$t_user_membership_apply, 'apply_message'],
        [$t_user_membership_apply, 'created_at'],
        [$t_user_membership_apply, 'updated_at']
    ];

    $strSQL_from = " FROM $t_user_membership_apply ";

    $arr_strSQL_where = [
        [
            [
                [$t_user_membership_apply, 'user_id', '=', (int) $current_user_id, 'PDO::PARAM_INT', 'And'],
                [$t_user_membership_apply, 'apply_status', '=', $apply_status, 'PDO::PARAM_STR', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_user_membership_apply, 'id', 'DESC']
    ];

    $strSQL_option = ' LIMIT 1 ';

    list($pdo_has_error, $select_has_error, $e, $arr_apply) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );

    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    if(empty($arr_apply)){
        return $arr_latest_apply;
    }

    return $arr_apply[0] ?? $arr_latest_apply;
}


/******************************************************
 *  fetch: id 指定（主にキャンセル・管理画面用）
 ******************************************************/
function fetch_arr_user_membership_apply_by_id($apply_id){

    global
        $t_user_membership_apply;

    $arr_apply_row = [];

    $apply_id = (int) $apply_id;
    if($apply_id < 1){
        return $arr_apply_row;
    }

    $arr_strSQL_select = [
        [$t_user_membership_apply, 'id'],
        [$t_user_membership_apply, 'user_id'],
        [$t_user_membership_apply, 'apply_level'],
        [$t_user_membership_apply, 'apply_status'],
        [$t_user_membership_apply, 'agreed_at'],
        [$t_user_membership_apply, 'apply_message'],
        [$t_user_membership_apply, 'admin_note'],
        [$t_user_membership_apply, 'handled_by'],
        [$t_user_membership_apply, 'handled_at'],
        [$t_user_membership_apply, 'created_at'],
        [$t_user_membership_apply, 'updated_at']
    ];

    $strSQL_from = " FROM $t_user_membership_apply ";

    $arr_strSQL_where = [
        [
            [
                [$t_user_membership_apply, 'id', '=', $apply_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_user_membership_apply, 'id', 'DESC']
    ];

    $strSQL_option = ' LIMIT 1 ';

    list($pdo_has_error, $select_has_error, $e, $arr_apply) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );

    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    if(empty($arr_apply)){
        return $arr_apply_row;
    }

    return $arr_apply[0] ?? $arr_apply_row;
}

/******************************************************
 *  USER LEVEL
 *  
 ******************************************************/
function get_user_level(){

    global 
		$t_user_membership,
		$int_Free_Member;

    if (!is_user_logged_in()) {
        return null;
    }

    $user = wp_get_current_user();
    $id = $user->ID;

    $arr_strSQL_select = [
        [$t_user_membership, 'level']
    ];

    $strSQL_from = ' FROM ' . $t_user_membership;

    $arr_strSQL_where = [
        [
            [
                [$t_user_membership, 'user_id', '=', $id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [];
    $strSQL_option = ' LIMIT 1';

    list($pdo_has_error, $select_has_error, $e, $arr_user_level) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    if (!empty($arr_user_level)) {
        return (int)$arr_user_level[0]['level'];
    }

    return $int_Free_Member;
}

function is_member_level($level){
    
	global
		$int_Free_Member;
	
	return ($level !== null && $level >= $int_Free_Member);
}


function is_student_level($level){
    
	global
		$int_Basic_Student;
	
	return ($level !== null && $level >= $int_Basic_Student);
}


function is_teacher_level($level){
    
	global
		$int_Basic_Teacher;
	
	return ($level !== null && $level >= $int_Basic_Teacher);
}


function is_operator_level($level){
    
	global
		$int_Operator;
	
	return ($level !== null && $level >= $int_Operator);
}


function is_admin_level($level){
    
	global
		$int_Administrator;

    return ($level !== null && $level >= $int_Administrator);
}



/******************************************************
 *  ITEMS
 *  
 ******************************************************/
function build_html_attributes($id = '', $class = '', $options = []) {
    $attrs = '';

    if ($id !== '') {
        $attrs .= ' id="' . escape_html($id) . '"';
    }

    if ($class !== '') {
        $attrs .= ' class="' . escape_html($class) . '"';
    }

    foreach ($options as $key => $value) {
        if ($value === null || $value === false || $value === '') {
            continue;
        }

        if ($value === true) {
            $attrs .= ' ' . escape_html($key);
            continue;
        }

        $attrs .= ' ' . escape_html($key) . '="' . escape_html($value) . '"';
    }

    return $attrs;
}


function build_html_wise_join_contents($contents){

    if (is_array($contents)) {
        return implode('', array_filter($contents, static function($value){
            return $value !== null && $value !== false && $value !== '';
        }));
    }

    if ($contents === null || $contents === false) {
        return '';
    }

    return (string)$contents;
}


function build_html_choice($button_id, $button_text, $arr_request_contents, $str_goto_page, $div_class, $form_class, $input_class, $button_background_color, $new_tab, $send_method, $int_selected_language){

	$str_html = '';

	if (!$new_tab) {
		$str_html = '<form class="'.$form_class.'" action= "'.$str_goto_page.'" method = "'.$send_method.'">';
	}
	else{
		$str_html = '<form class="'.$form_class.'" action= "'.$str_goto_page.'" method = "'.$send_method.'" target = "_blank" rel="noopener">';
	}

	$str_html = $str_html.'<div id ="'.$button_id.'" class = "'.$div_class.'"><input class="'.$input_class.'" style="background-color: '.$button_background_color.'" type="button" onclick="submit();" value="'.$button_text.'"></input></div>';

	if(!empty($arr_request_contents)){
		foreach($arr_request_contents as $loop_request_contents){
			$hidden_class = $loop_request_contents['class'];
			$input_name = $loop_request_contents['name'];
			$input_value = $loop_request_contents['value'];
			$str_html = $str_html.'<input class="'.$hidden_class.'" type="hidden" name="'.escape_html($input_name).'" value= "'.escape_html($input_value).'"></input>';
		}
	}

	$str_html = $str_html.'</form>';

	return $str_html;
}


function build_html_anchor($button_id, $button_text, $str_goto_page, $a_class, $new_tab, $int_selected_language){

	$str_html = '';

	if(!$new_tab){
		$str_html = '<a id="'.$button_id.'" href="'.$str_goto_page.'" class="'.$a_class.'">'.$button_text.'</a>';
	}
	else{
		$str_html = '<a id="'.$button_id.'" target="_blank" href="'.$str_goto_page.'" class="'.$a_class.'">'.$button_text.'</a>';
	}
	return $str_html;
}


function build_html_div_frame($class, $heading, $word, $caution_class) {

    $class = escape_html_with_nl2br($class);
    $heading = escape_html_with_nl2br($heading);
    $caution_class = escape_html_with_nl2br($caution_class);

    $html = '<div class="' . $class . '">';

    if (!empty($heading)) {
        $html .= '<div class="' . $caution_class . '">' . $heading . '</div>';
    }

    $html .= $word . '</div>';

    return $html;
}


function build_html_expand_details_from_attributes($arr_expand, $word, $target_column, $heading, $int_selected_language){

	global
	    $arr_columns_masta_japanese_attribute;

	$heading = escape_html_with_nl2br($heading);
	$details_class = 'expandButtonDetails';

	$word = $word.'<details class="'.$details_class.'"><summary class="contentsSummary">'.$heading.'</summary><div class="detailsContent">';

	foreach($arr_expand as $loop_expand){
		$int_attribute_id = $loop_expand['attribute_id'];
		$p_tag_class = 'summarysContents';

		switch ($int_attribute_id) {
			case 9:
				$add_word = 'A: '.$loop_expand[$target_column];
				break;
			case 11:
			case 12:
			case 13:
				$add_word = $loop_expand[$arr_columns_masta_japanese_attribute[$int_selected_language]].' :  '.$loop_expand[$target_column];
				break;
			case 14:
				$add_word = $loop_expand[$arr_columns_masta_japanese_attribute[$int_selected_language]];
				$p_tag_class = $p_tag_class.' noBackground';
				break;
			default:
				$add_word = $loop_expand[$target_column];
				break;
		}
		$add_word = apply_text_for_output($add_word);


		$add_word_p_tag = '<p class="'.$p_tag_class.'">';
		$user_level = get_user_level();
		if(is_admin_level($user_level)){
			$add_word_p_tag ='<p class="'.$p_tag_class.'" contenteditable="true" spellcheck="false">';
		}
		$add_word = $add_word_p_tag . $add_word . '</p>';
		$word = $word.$add_word;
	}

	$word = $word.'</div></details>';

	return $word;
}


function build_html_details_contents($word, $heading, $details_class, $summary_class, $details_div_class){

	$word = '<details class="'.$details_class.'"><summary class="'.$summary_class.'">'.$heading.'</summary><div class="'.$details_div_class.'">'.$word.'</div></details>';

	return $word;
}


function recursive_find_ancestors($start_id, $map_child_to_parents, &$visited) {
	if (in_array($start_id, $visited)) return;
	$visited[] = $start_id;

	if (!isset($map_child_to_parents[$start_id])) return;

	foreach ($map_child_to_parents[$start_id] as $parent_id) {
		recursive_find_ancestors($parent_id, $map_child_to_parents, $visited);
	}
}


function build_html_overlay_close_button(){

	global
		$str_mark_cross;

	$str_html_overlay_close_button =
    '<button class="overlayCloseButton"
             data-action="overlay:close">' .
        $str_mark_cross .
    '</button>';

	return $str_html_overlay_close_button;
}


function build_html_magnifier_icon($type){

    global $path_images_common;

    $url_images_common = get_home_url(
        get_main_site_id(),
        trailingslashit(ltrim($path_images_common, '/'))
    );

    $file = '';
    $alt  = '';

    if ($type === 'plus') {
        $file = 'magnifierPlus.png';
        $alt  = 'MagnifierPlus';
    } elseif ($type === 'minus') {
        $file = 'magnifierMinus.png';
        $alt  = 'MagnifierMinus';
    } else {
        return '';
    }

    $src = jws_add_file_version($url_images_common . $file);

    return '<img class="imageMagnifier" src="' . $src . '" alt="' . $alt . '" title="' . $alt . '">';
}

function build_html_loading_spinner($id) {
    return
        '<div id="' . $id . '" class="loading-wrapper loading-hidden">' .
            '<div class="loading-spinner"></div>' .
        '</div>';
}
/******************************************************
 *  HTML
 *  
 ******************************************************/
function build_html_welcome_user_section($int_selected_language){

	$str_welcome_user = '';

	if(is_user_logged_in()){
		$current_user = wp_get_current_user();
		$current_user_nickname = $current_user->nickname;

		
		$arr_str_welcome_user = [
			['ようこそ ',' さん'],
			['歡迎 ','']
		];
		// 変更
		$str_welcome_user = '<section id="sectionWelcomeUser" class="sectionStandard"><div class="ribbon5"><h3>'.$arr_str_welcome_user[$int_selected_language][INDEX_FIRST].escape_html_with_nl2br($current_user_nickname).$arr_str_welcome_user[$int_selected_language][INDEX_SECOND].'</h3></div></section>';
	}
	return $str_welcome_user;
}


function build_html_grammar_view_zoom_controls($int_selected_language){

    $html_zoom_in  = build_html_magnifier_icon('plus');
    $html_zoom_out = build_html_magnifier_icon('minus');

    $html =
    '<div id="siteZoomContainer" class="grammarViewZoomContainer">
        <div class="grammarViewZoomIn grammarViewZoomButton">' . $html_zoom_in . '</div>
        <div class="grammarViewZoomOut grammarViewZoomButton">' . $html_zoom_out . '</div>
    </div>';

    return $html;
}


function build_html_contact_form($int_selected_language){

	global
		$str_rgb_blue;

	$button_id = '';

	$arr_str_button_caption_to_contact_form = [
		'お問い合わせフォームへ',
		'往GoogleForm'
	];
	$button_text = $arr_str_button_caption_to_contact_form[$int_selected_language];

	$url_contact_google_form = [
		'https://docs.google.com/forms/d/e/1FAIpQLSexDZJg_41KcE9RQLrqvXF9mWaHWciKqWEmRjNRwoohg0ScGg/viewform?usp=sf_link',
		'https://docs.google.com/forms/d/e/1FAIpQLSdH0IKFjv9h_OppsW9uXG9MCUbG8M0-j6MVZ7HtYg7Cd6mHfQ/viewform?usp=sf_link'
	];

	$str_address_contact_google_form = $url_contact_google_form[$int_selected_language];
	$div_class = 'divChoices';
	$form_class = '';
	$input_class = 'inputChoices';
	$hidden_class = '';
	$button_background_color = "rgb($str_rgb_blue);";
	$new_tab = true;
	$send_method = 'GET';
	$arr_request_contents = [];

	$str_contact_form = build_html_choice($button_id, $button_text, $arr_request_contents, $str_address_contact_google_form, $div_class, $form_class, $input_class, $button_background_color, $new_tab, $send_method, $int_selected_language);

	return $str_contact_form;
}


function build_html_create_account_link(
    string $class_name,
    int $int_selected_language
): string {

    global $path_create_account;

	$url_create_account = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_create_account, '/'))
	);

	$arr_messages = [
        'create_account_title' => [
			'まだアカウントをお持ちでない方',
			'尚未持有帳號者',
			'Those who do not yet have an account',
		],
		'create_account_link' => [
			'新規アカウント作成はこちら',
			'前往建立帳號',
			'Create an account',
		],
	];

    $str_html = '';

    $str_html .= '<div class="' . esc_attr($class_name) . '">';
    $str_html .= '<p>'
        . esc_html($arr_messages['create_account_title'][$int_selected_language])
        . ' <a href="' . esc_url($url_create_account) . '">'
        . esc_html($arr_messages['create_account_link'][$int_selected_language])
        . '</a></p>';
    $str_html .= '</div>';

    return $str_html;
}

function build_html_forgot_password_link(
    string $class_name,
    int $int_selected_language
): string {

    global $path_forgot_password;

    $url_forgot_password = get_home_url(
        get_data_blog_id_from_selected_language($int_selected_language ?? null),
        trailingslashit(ltrim($path_forgot_password, '/'))
    );

    $arr_messages = [
        'forgot_password_title' => [
            'パスワードをお忘れの方',
            '忘記密碼者',
            'Forgot your password?',
        ],
        'forgot_password_link' => [
            'パスワード再設定はこちら',
            '前往重設密碼',
            'Reset your password',
        ],
    ];

    $str_html = '';

    $str_html .= '<div class="' . esc_attr($class_name) . '">';
    $str_html .= '<p>'
        . esc_html($arr_messages['forgot_password_title'][$int_selected_language])
        . ' <a href="' . esc_url($url_forgot_password) . '">'
        . esc_html($arr_messages['forgot_password_link'][$int_selected_language])
        . '</a></p>';
    $str_html .= '</div>';

    return $str_html;
}


function build_html_about_membership_link(
    string $class_name,
    int $int_selected_language
): string {

    global $path_about_membership;
	
	$url_about_membership = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_about_membership, '/'))
	);

    $arr_messages = [
        'about_membership_title' => [
            'より多くの機能をご利用になりたい方',
            '想使用更多功能者',
            'Want access to more features?',
        ],
        'about_membership_link' => [
            '有料会員登録はこちら',
            '前往付費會員註冊',
            'Upgrade membership',
        ],
    ];

    $str_html = '';

    $str_html .= '<div class="' . esc_attr($class_name) . '">';
    $str_html .= '<p>'
        . esc_html($arr_messages['about_membership_title'][$int_selected_language])
        . ' <a href="' . esc_url($url_about_membership) . '" target="_blank" rel="noopener noreferrer">'
        . esc_html($arr_messages['about_membership_link'][$int_selected_language])
        . '</a></p>';
    $str_html .= '</div>';

    return $str_html;
}

function build_html_global_canvas(){
    return '<canvas id="globalCanvas" class="globalCanvas wiseHitItem"></canvas>';
}

function build_html_global_laser_pointer($arr_targets_action, $int_selected_language){

	$str_global_laser_pointer = '';

	$user_level = get_user_level();
	if(!is_teacher_level($user_level)){
		return $str_global_laser_pointer;
	}

	$str_globalCanvas = '';
	$str_globalCanvas = build_html_global_canvas();
	$str_global_laser_pointer = $str_global_laser_pointer.$str_globalCanvas;

	$str_globalVerticalToolbarContainer = build_html_global_vertical_toolbar($arr_targets_action, $int_selected_language);
	$str_global_laser_pointer = $str_global_laser_pointer.$str_globalVerticalToolbarContainer;

	return $str_global_laser_pointer;
}


function build_html_global_vertical_toolbar($arr_targets_action, $int_selected_language){
	
	global
		$path_images_verticalToolbarContainer,
		$str_globalVerticalToolbarSelectorButton_id,
		$str_globalVerticalToolbarOpenWiseButton_id,
		$str_globalVerticalToolbarManageRoomsButton_id,
		$str_globalVerticalToolbarLaserButton_id;

	$str_globalVerticalToolbarContainer = '';
		
	$url_images_verticalToolbarContainer = get_home_url(
		get_main_site_id(),
		trailingslashit(ltrim($path_images_verticalToolbarContainer, '/'))
	);

	if($arr_targets_action[$str_globalVerticalToolbarSelectorButton_id]){
		$str_globalVerticalToolbarContainer .= '
		<div id="'.$str_globalVerticalToolbarSelectorButton_id.'" class="globalVerticalToolbarButton globalVerticalToolbarButtonToggle globalVerticalToolbarButton-selected">
			<img id="globalVerticalToolbarSelectorButtonImage" src="' . jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarSelectorButton.png') . '" alt="セレクト" title="セレクト">
		</div>';
	}
	
	if($arr_targets_action[$str_globalVerticalToolbarOpenWiseButton_id]){
		$str_globalVerticalToolbarContainer .= '
		<div id="'.$str_globalVerticalToolbarOpenWiseButton_id.'" class="globalVerticalToolbarButton globalVerticalToolbarButtonToggle">
			<img id="globalVerticalToolbarOpenWiseButtonImage" src="' . jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarOpenWiseButton.png') . '" alt="ホワイトボード" title="ホワイトボード">
		</div>';
	}

	$user_level = get_user_level();
	if(is_teacher_level($user_level)){
		if($arr_targets_action[$str_globalVerticalToolbarManageRoomsButton_id]){
			$str_globalVerticalToolbarContainer .= '
			<div id="'.$str_globalVerticalToolbarManageRoomsButton_id.'" class="globalVerticalToolbarButton globalVerticalToolbarButtonToggle">
				<img id="globalVerticalToolbarManageRoomsButtonImage" src="' . jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarManageRoomsButton.png') . '" alt="レッスン管理" title="レッスン管理">
			</div>';
		}
	}

	
	if($arr_targets_action[$str_globalVerticalToolbarLaserButton_id]){
		$str_globalVerticalToolbarContainer .= '
		<div id="'.$str_globalVerticalToolbarLaserButton_id.'" class="globalVerticalToolbarButton globalVerticalToolbarButtonToggle">
			<img id="globalVerticalToolbarLaserButtonImage" src="' . jws_add_file_version($url_images_verticalToolbarContainer . 'verticalToolbarLaserButton.png') . '" alt="レーザー" title="レーザー">
		</div>';
	}


	$str_globalVerticalToolbarContainer = '<div id="globalVerticalToolbarContainer">'.$str_globalVerticalToolbarContainer.'</div>';

	return $str_globalVerticalToolbarContainer;
}


function build_html_lesson_room_select_options_for_teacher($int_selected_language){
	
	global
		$t_rooms,
		$str_option_value_default;

	$str_myLessonsForTeacherDropDownSelectRoomAreaOptions = '';

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
				[$t_rooms,'room_owner_user_id','=',$current_user_id,'PDO::PARAM_INT','And'],
				[$t_rooms,'is_published','=',FLAG_TRUE,'PDO::PARAM_INT','']
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

	if(empty($arr_rooms)){
		$str_myLessonsForTeacherDropDownSelectRoomAreaOptions = '';
		return $str_myLessonsForTeacherDropDownSelectRoomAreaOptions;
	}

	$str_myLessonsForTeacherDropDownSelectRoomAreaOptions = '<option value="'.$str_option_value_default.'">'.$str_option_value_default.'</option>';

	foreach($arr_rooms as $loop_room){
		$int_option_value = escape_html_with_nl2br($loop_room['unique_code']);
		$str_option_text_content = escape_html_with_nl2br($loop_room['room_name']);
		$str_myLessonsForTeacherDropDownSelectRoomAreaOptions =
		$str_myLessonsForTeacherDropDownSelectRoomAreaOptions.'<option value="'.$int_option_value.'">'.$str_option_text_content.'</option>';
	}

	return $str_myLessonsForTeacherDropDownSelectRoomAreaOptions;
}


function build_html_table_with_index(array $rows): string
{
    $html = '<table class="wise-table">';
    $html .= '<thead><tr><th>index</th><th>id</th><th>title</th></tr></thead>';
    $html .= '<tbody>';

    foreach ($rows as $i => $row) {
        $id = isset($row['id']) ? (string) $row['id'] : '';
        $title = isset($row['title']) ? (string) $row['title'] : '';

        $html .= '<tr>';
        $html .= '<td>' . $i . '</td>';
        $html .= '<td>' . htmlspecialchars($id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>';
        $html .= '<td>' . htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</td>';
        $html .= '</tr>';
    }

    $html .= '</tbody>';
    $html .= '</table>';

    return $html;
}




/******************************************************
 *  GET
 *  
 ******************************************************/
function get_arr_already_learned_list($arr_base, $int_selected_language) {

    if (empty($arr_base)) return [];

    $arr_base = array_values(array_unique($arr_base));

    $relations = fetch_all_root_parent_child_relations($int_selected_language);
    $map_child_to_parents = [];
    foreach ($relations as $rel) {
        $child_id = (int)$rel['masta_japanese_root_id_child'];
        $parent_id = (int)$rel['masta_japanese_root_id_parent'];
        $map_child_to_parents[$child_id][] = $parent_id;
    }

    $visited_all = [];
    foreach ($arr_base as $base_id) {
        $visited = [];
        recursive_find_ancestors($base_id, $map_child_to_parents, $visited);
        if (!empty($visited)) {
            $visited_all = array_merge($visited_all, $visited);
        }
    }

    $merged = array_merge($arr_base, $visited_all);
    return array_values(array_unique($merged, SORT_NUMERIC));
}


function get_arr_temp_already_learned_list($int_selected_language){

	global
		$t_masta_japanese_root,
		$t_masta_japanese_sub_category,
		$int_masta_japanese_category_id_terminology,
		$int_masta_japanese_category_id_grammar_relation,
		$int_masta_japanese_category_id_grammar;

		
	$arr_strSQL_select = [
		[$t_masta_japanese_root,'id']
	];

	$strSQL_from = " FROM
					$t_masta_japanese_root
					INNER JOIN $t_masta_japanese_sub_category
					ON
					$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_terminology,'PDO::PARAM_INT','Or'],
				[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_grammar_relation,'PDO::PARAM_INT','Or'],
				[$t_masta_japanese_sub_category,'category_id','=',$int_masta_japanese_category_id_grammar,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	$arr_masta_japanese_root = array_column($arr_masta_japanese_root, 'id');

	return $arr_masta_japanese_root;
}


function get_arr_learned_ids($int_selected_language) {
    if (isset($_SESSION['arr_already_learned_list'])) {
        $arr = $_SESSION['arr_already_learned_list'];
    } else {
        $arr = get_arr_temp_already_learned_list($int_selected_language);
    }
    $arr = array_values(array_unique(array_map('intval', $arr)));
    return $arr;
}


function get_arr_indicator_label($id, $doUseDefaultLabel, $int_selected_language){

	global
		$t_japanese_labels,
		$t_masta_japanese_label,
		$str_column_main_label,
		$str_column_masta_japanese_label_id,
		$str_column_label_japanese,
		$str_column_label_kana,
		$str_snake_to_camel_label_id;

	$arr_indicator_labels = [];

	if($doUseDefaultLabel){
		$arr_strSQL_where = [
			[$t_japanese_labels,'japanese_element_id','=',$id,'PDO::PARAM_INT','And'],
			[$t_japanese_labels,$str_column_main_label,'=',FLAG_TRUE,'PDO::PARAM_INT','']
		];
	}
	else{
		$arr_strSQL_where = [
			[$t_japanese_labels,'id','=',$id,'PDO::PARAM_INT','']
		];
	}

	$arr_strSQL_select = [
		[$t_japanese_labels,'id'],
		[$t_japanese_labels,$str_column_masta_japanese_label_id],
		[$t_masta_japanese_label,$str_column_label_japanese],
		[$t_masta_japanese_label,$str_column_label_kana]
	];

	$strSQL_from = " FROM
					$t_japanese_labels
					INNER JOIN $t_masta_japanese_label
					ON
					$t_japanese_labels.masta_japanese_label_id = $t_masta_japanese_label.id
					";

	$arr_strSQL_where = [
		[
			$arr_strSQL_where,
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_japanese_labels) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_japanese_labels)){
		return [];
	}

	$arr_japanese_labels = $arr_japanese_labels[INDEX_FIRST];

	$arr_indicator_labels = [
		$str_snake_to_camel_label_id => $arr_japanese_labels['id'],
		$str_column_masta_japanese_label_id => $arr_japanese_labels[$str_column_masta_japanese_label_id],
		$str_column_label_japanese => $arr_japanese_labels[$str_column_label_japanese],
		$str_column_label_kana => $arr_japanese_labels[$str_column_label_kana]
	];

	return $arr_indicator_labels;
}


function get_arr_inflected_label($arr_indicator_labels, $t_masta_japanese_root_id, $t_japanese_element_id_target, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_voice_id, $doAllowInflection, $int_selected_language){

	global
		$str_snake_to_camel_label_id,
		$str_column_masta_japanese_label_id,
		$str_column_label_japanese,
		$str_column_label_kana,
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana,
		$str_snake_to_camel_sub_classification_id,
		$str_snake_to_camel_form_id,
		$str_snake_to_camel_voice_id,
		$str_snake_to_camel_japanese_id,
		$str_snake_to_camel_japanese_element_id,
		$str_snake_to_camel_sub_classification,
		$str_snake_to_camel_form,
		$str_snake_to_camel_voice;

	$int_label_id = $arr_indicator_labels[$str_snake_to_camel_label_id];
	$int_masta_japanese_label_id = $arr_indicator_labels[$str_column_masta_japanese_label_id];
	$str_japanese = $arr_indicator_labels[$str_column_label_japanese];
	$str_kana = $arr_indicator_labels[$str_column_label_kana];

	$arr_inflected_label = [
		 $str_snake_to_camel_japanese=>$str_japanese,
		 $str_snake_to_camel_kana=>$str_kana
	];

	$arr_inflected_label = apply_word_inflection($arr_inflected_label, $t_masta_japanese_root_id, $t_masta_japanese_sub_classification_id, $t_masta_form_root_id, $int_voice_id, $int_masta_japanese_label_id, $doAllowInflection, $int_selected_language);

	if(empty($arr_inflected_label)){
		return [];
	}

	$t_masta_japanese_sub_classification_id_result = $arr_inflected_label[$str_snake_to_camel_sub_classification_id];
	$t_masta_form_root_id_result = $arr_inflected_label[$str_snake_to_camel_form_id];
	$int_voice_id_result = $arr_inflected_label[$str_snake_to_camel_voice_id];

	$str_japanese_sub_classification = fetch_str_sub_classification_name_by_id($t_masta_japanese_sub_classification_id_result, $int_selected_language);
	$str_japanese_form = fetch_str_form_name_by_form_root_id($t_masta_form_root_id_result, $int_selected_language);
	$str_japanese_voice = get_str_voice_name_by_id($int_voice_id_result, $int_selected_language);

	$arr_inflected_label[$str_snake_to_camel_japanese_id] = $t_masta_japanese_root_id;
	$arr_inflected_label[$str_snake_to_camel_japanese_element_id] = $t_japanese_element_id_target;
	$arr_inflected_label[$str_snake_to_camel_label_id] = $int_label_id;
	$arr_inflected_label[$str_snake_to_camel_sub_classification] = $str_japanese_sub_classification;
	$arr_inflected_label[$str_snake_to_camel_form] = $str_japanese_form;
	$arr_inflected_label[$str_snake_to_camel_voice] = $str_japanese_voice;

	return $arr_inflected_label;
}


function get_data_blog_id_from_selected_language($int_selected_language = null){

    $map = [
        0 => 1,
        1 => 2,
    ];

    if(isset($map[$int_selected_language])){
        return (int)$map[$int_selected_language];
    }

    return (int)get_current_blog_id();
}



/******************************************************
 *  GET / FETCH
 *  ID / UNIQUE CODE
 *  
 ******************************************************/

function fetch_masta_japanese_root_id_from_unique_code($unique_code, $int_selected_language){

	global
	    $t_masta_japanese_root;

	$arr_strSQL_select = [
		[$t_masta_japanese_root,'id']
	];

	$strSQL_from = ' FROM ' .$t_masta_japanese_root;

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_masta_japanese_root,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_masta_japanese_root)){
		// 未定義id
		return 0;
	}
	return $arr_masta_japanese_root[INDEX_FIRST]['id'];
}


function fetch_masta_japanese_root_ids_from_unique_codes($arr_unique_code, $int_selected_language){

	global
		$t_masta_japanese_root,
		$str_sql_where_is_in;

	$arr_strSQL_select = [
		[$t_masta_japanese_root,'id']
	];

	$strSQL_from = ' FROM ' .$t_masta_japanese_root;

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_masta_japanese_root,'unique_code',$str_sql_where_is_in,$arr_unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_masta_japanese_root;
}


function fetch_masta_japanese_root_id_from_masta_form_root_id($int_id, $int_selected_language){

	global
	    $t_masta_form_root;
	
	$arr_strSQL_select = [
		[$t_masta_form_root,'masta_japanese_root_id']
	];

	$strSQL_from = ' FROM ' .$t_masta_form_root;

	$arr_strSQL_where = [
		[
			[
				[$t_masta_form_root,'id','=',$int_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_form_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_masta_form_root)){
		// 未定義id
		return 0;
	}
	return $arr_masta_form_root[INDEX_FIRST]['masta_japanese_root_id'];
}


function fetch_registered_sentence_id_from_unique_code($unique_code, $int_selected_language){

	global
    $t_registered_sentences;

	$arr_strSQL_select = [
		[$t_registered_sentences,'id']
	];

	$strSQL_from = ' FROM ' .$t_registered_sentences;

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_registered_sentences,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_registered_sentence) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_registered_sentence)){
		// 未定義id
		return 0;
	}
	return $arr_registered_sentence[INDEX_FIRST]['id'];
}


function fetch_layer_id_from_unique_code($unique_code, $int_selected_language){

	global
	    $t_layers;
	
	$arr_strSQL_select = [
		[$t_layers,'id']
	];

	$strSQL_from = ' FROM ' .$t_layers;

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_layers,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_layers) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_layers)){
		// 未定義id
		return 0;
	}
	return $arr_layers[INDEX_FIRST]['id'];
}


function fetch_unique_code_from_layer_id($layer_id, $int_selected_language){

	global
		$t_layers,
		$str_snake_to_camel_unique_code;
	
	$arr_strSQL_select = [
		[$t_layers,'unique_code as '.$str_snake_to_camel_unique_code]
	];

	$strSQL_from = ' FROM ' .$t_layers;

	$arr_strSQL_where = [
		[
			[
				[$t_layers,'id','=',$layer_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_layers) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_layers)){
		// 未定義id
		return 0;
	}
	return $arr_layers[INDEX_FIRST][$str_snake_to_camel_unique_code];
}


function fetch_room_id_from_unique_code($unique_code, $int_selected_language){

	global
	    $t_rooms;

	$arr_strSQL_select = [
		[$t_rooms,'id']
	];

	$strSQL_from = ' FROM ' .$t_rooms;

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_rooms,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_rooms) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_rooms)){
		// 未定義id
		return 0;
	}
	return $arr_rooms[INDEX_FIRST]['id'];
}


function fetch_room_owner_user_id_from_room_id($room_id, $int_selected_language){

	global
	    $t_rooms;

	$arr_strSQL_select = [
		[$t_rooms,'room_owner_user_id']
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

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_rooms) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_rooms)){
		// 未定義id
		return 0;
	}
	return $arr_rooms[INDEX_FIRST]['room_owner_user_id'];
}


function fetch_lesson_id_from_unique_code($unique_code, $int_selected_language){

	global
	    $t_room_lessons;

	$arr_strSQL_select = [
		[$t_room_lessons,'id']
	];

	$strSQL_from = ' FROM ' .$t_room_lessons;

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_room_lessons,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_lessons) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_lessons)){
		// 未定義id
		return 0;
	}
	return $arr_lessons[INDEX_FIRST]['id'];
}


function fetch_lesson_step_id_from_unique_code($unique_code, $int_selected_language){

	global
	    $t_room_lesson_steps;

	$arr_strSQL_select = [
		[$t_room_lesson_steps,'id']
	];

	$strSQL_from = ' FROM ' .$t_room_lesson_steps;

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_room_lesson_steps,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_lesson_steps) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_lesson_steps)){
		// 未定義id
		return 0;
	}
	return $arr_lesson_steps[INDEX_FIRST]['id'];
}


function fetch_lesson_step_unit_id_from_unique_code($unique_code, $int_selected_language){

	global
	    $t_room_lesson_step_units;

	$arr_strSQL_select = [
		[$t_room_lesson_step_units,'id']
	];

	$strSQL_from = ' FROM ' .$t_room_lesson_step_units;

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_room_lesson_step_units,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_lesson_step_units) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_lesson_step_units)){
		// 未定義id
		return 0;
	}
	return $arr_lesson_step_units[INDEX_FIRST]['id'];
}


function fetch_masta_form_root_id_from_unique_code($unique_code, $int_selected_language){

	global
		$t_masta_form_root,
		$t_masta_japanese_root;
	
	$arr_strSQL_select = [
		[$t_masta_form_root,'id']
	];

	$strSQL_from = ' FROM ' .$t_masta_form_root;

	$strSQL_from = " FROM
					$t_masta_form_root
					INNER JOIN $t_masta_japanese_root
					ON
					$t_masta_form_root.masta_japanese_root_id = $t_masta_japanese_root.id
					";

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_masta_japanese_root,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_form_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_masta_form_root)){
		// 未定義id
		return 0;
	}
	return $arr_masta_form_root[INDEX_FIRST]['id'];
}


function fetch_masta_japanese_classification_id_from_unique_code($unique_code, $int_selected_language){

	global
		$t_masta_japanese_classification,
		$t_masta_japanese_root;
	
	$arr_strSQL_select = [
		[$t_masta_japanese_classification,'id']
	];

	$strSQL_from = " FROM
					$t_masta_japanese_classification
					INNER JOIN $t_masta_japanese_root
					ON
					$t_masta_japanese_classification.masta_japanese_root_id = $t_masta_japanese_root.id
					";

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_masta_japanese_root,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_classification) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_masta_japanese_classification)){
		// 未定義id
		return 0;
	}
	return $arr_masta_japanese_classification[INDEX_FIRST]['id'];
}


function fetch_category_id_from_masta_japanese_root_id($t_masta_japanese_root_id, $int_selected_language){

	global
		$t_masta_japanese_root,
		$t_masta_japanese_sub_category;

	$arr_strSQL_select = [
		[$t_masta_japanese_sub_category,'category_id']
	];

	$strSQL_from = " FROM
					$t_masta_japanese_root
					INNER JOIN $t_masta_japanese_sub_category
					ON
					$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
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
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_masta_japanese_root)){
		// 未定義id
		return 0;
	}
	return $arr_masta_japanese_root[INDEX_FIRST]['category_id'];
}


function fetch_arr_rooms_from_unique_code($unique_code, $int_selected_language){

	global
	    $t_rooms;

	$arr_strSQL_select = [
		[$t_rooms,'id'],
		[$t_rooms,'room_name'],
		[$t_rooms,'unique_code'],
		[$t_rooms,'room_type']
	];

	$strSQL_from = ' FROM ' .$t_rooms;

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_rooms,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_rooms) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_rooms;
}

function fetch_arr_rooms_with_owner_from_unique_code($unique_code, $int_selected_language){

	global
	    $t_rooms;

	$arr_strSQL_select = [
		[$t_rooms,'id'],
		[$t_rooms,'unique_code'],
		[$t_rooms,'room_owner_user_id'],
		[$t_rooms,'room_type']
	];

	$strSQL_from = ' FROM ' .$t_rooms;

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_rooms,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_rooms) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_rooms;
}

function check_room_user_exists($room_id, $current_user_id, $int_selected_language){

	global
	    $t_room_users;

	$arr_strSQL_select = [
		[$t_room_users,'id'],
		[$t_room_users,'room_id'],
		[$t_room_users,'user_id'],
		[$t_room_users,'confirmed']
	];
	
	$strSQL_from = ' FROM ' .$t_room_users;
	
	$arr_strSQL_where = [
		[
			[
				[$t_room_users,'room_id','=',$room_id,'PDO::PARAM_INT','And'],
				[$t_room_users,'user_id','=',$current_user_id,'PDO::PARAM_INT','']
			],
			''
		]
	];
	
	$arr_strSQL_order = [];
	
	$strSQL_option = '';
	
	list($pdo_has_error, $select_has_error, $e, $arr_room_users) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	return !empty($arr_room_users);
}

function fetch_arr_room_users_from_room_id($room_id, $int_selected_language){

	global
	    $t_room_users;

	$arr_strSQL_select = [
		[$t_room_users,'id'],
		[$t_room_users,'room_id'],
		[$t_room_users,'user_id'],
		[$t_room_users,'confirmed']
	];
	
	$strSQL_from = ' FROM ' .$t_room_users;
	
	$arr_strSQL_where = [
		[
			[
				[$t_room_users,'room_id','=',$room_id,'PDO::PARAM_INT','']
			],
			''
		]
	];
	
	$arr_strSQL_order = [];
	
	$strSQL_option = '';
	
	list($pdo_has_error, $select_has_error, $e, $arr_room_users) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	return $arr_room_users;
}

function fetch_arr_room_users_from_user_id($user_id, $int_selected_language){

    global
        $t_room_users;

    $arr_strSQL_select = [
        [$t_room_users,'id'],
        [$t_room_users,'room_id'],
        [$t_room_users,'user_id'],
        [$t_room_users,'confirmed']
    ];

    $strSQL_from = ' FROM ' . $t_room_users;

    $arr_strSQL_where = [
        [
            [
                [$t_room_users,'user_id','=',$user_id,'PDO::PARAM_INT','']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [];
    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_room_users) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    return $arr_room_users;
}


function fetch_arr_pending_room_users_by_user_id($user_id, $int_selected_language){

    global
        $t_room_users;

    $arr_strSQL_select = [
        [$t_room_users,'id'],
        [$t_room_users,'room_id'],
        [$t_room_users,'user_id'],
        [$t_room_users,'confirmed']
    ];

    $strSQL_from = ' FROM ' . $t_room_users;

    $arr_strSQL_where = [
        [
            [
                [$t_room_users,'user_id','=',$user_id,'PDO::PARAM_INT','And'],
                [$t_room_users,'confirmed','=',0,'PDO::PARAM_INT','']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [];
    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_room_users) =
        execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);

    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    return $arr_room_users;
}


function fetch_arr_users_by_ids($arr_user_ids){

    $arr_user_ids = array_values(array_unique(array_map('intval', $arr_user_ids)));
    if (empty($arr_user_ids)) {
        return [];
    }

    $wp_users = get_users([
        'include'  => $arr_user_ids,
        'blog_id' => 0,
        'fields'  => 'all'
    ]);

    $arr_users_map = [];
    foreach ($wp_users as $u) {
        $id = intval($u->ID);
        $nicename = $u->user_nicename !== '' ? $u->user_nicename : $u->user_login;

        $arr_users_map[$id] = [
            'id' => $id,
            'nickname' => $nicename
        ];
    }

    return $arr_users_map;
}


function fetch_arr_registered_sentences_from_wise_navigation_id($t_wise_navigation_id, $int_selected_language){

	global
		$t_wise_navigations,
		$t_registered_sentences;
	
	$arr_strSQL_select = [
		[$t_registered_sentences,'id'],
		[$t_registered_sentences,'sentence']
	];

	$strSQL_from = " FROM
					$t_wise_navigations
					INNER JOIN $t_registered_sentences
					ON
					$t_wise_navigations.registered_sentence_id = $t_registered_sentences.id
					";

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

	list($pdo_has_error, $select_has_error, $e, $arr_registered_sentences) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);

    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_registered_sentences;
}


function fetch_wise_navigation_id_from_unique_code($unique_code, $int_selected_language){

	global
	    $t_wise_navigations;
	
	$arr_strSQL_select = [
		[$t_wise_navigations,'id']
	];

	$strSQL_from = ' FROM ' .$t_wise_navigations;

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_wise_navigations,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_wise_navigations) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_wise_navigations)){
		// 未定義id
		return 0;
	}
	return $arr_wise_navigations[INDEX_FIRST]['id'];
}


function fetch_wise_navigation_waypoint_id_from_unique_code($unique_code, $int_selected_language){

	global
	    $t_wise_navigation_waypoints;
	
	$arr_strSQL_select = [
		[$t_wise_navigation_waypoints,'id']
	];

	$strSQL_from = ' FROM ' .$t_wise_navigation_waypoints;

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_wise_navigation_waypoints,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_wise_navigation_waypoints) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_wise_navigation_waypoints)){
		// 未定義id
		return 0;
	}
	return $arr_wise_navigation_waypoints[INDEX_FIRST]['id'];
}

function fetch_arr_wise_navigation_from_wise_navigation_id($t_wise_navigation_id, $int_selected_language){

	global
        $t_wise_navigations;

    $arr_strSQL_select = [
        [$t_wise_navigations, 'id'],
        [$t_wise_navigations, 'title']
    ];

    $strSQL_from = ' FROM ' . $t_wise_navigations;

    $arr_strSQL_where = [
        [
            [
                [$t_wise_navigations, 'id', '=', $t_wise_navigation_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_wise_navigations) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

	return $arr_wise_navigations;

}


function fetch_wise_navigation_script_id_from_unique_code($unique_code, $int_selected_language){

	global
	    $t_wise_navigation_scripts;
	
	$arr_strSQL_select = [
		[$t_wise_navigation_scripts,'id']
	];

	$strSQL_from = ' FROM ' .$t_wise_navigation_scripts;

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_wise_navigation_scripts,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_wise_navigation_scripts) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_wise_navigation_scripts)){
		// 未定義id
		return 0;
	}
	return $arr_wise_navigation_scripts[INDEX_FIRST]['id'];
}


function get_data_grammar_usage_children_from_unique_codes($array){

	global
		$t_masta_japanese_root,
		$t_grammar_usage_children,
		$t_grammar_usage_parents,
		$str_sql_where_is_in;

	$int_selected_language = INDEX_FIRST;

	$arr_strSQL_select = [
		[$t_grammar_usage_children,'id'],
		[$t_masta_japanese_root,'root_japanese as title']
	];

	$strSQL_from = " FROM
					$t_masta_japanese_root
					INNER JOIN $t_grammar_usage_children
					ON
					$t_masta_japanese_root.id = $t_grammar_usage_children.masta_japanese_root_id
					INNER JOIN $t_grammar_usage_parents
					ON
					$t_grammar_usage_children.parent_id = $t_grammar_usage_parents.masta_japanese_root_id
					";

	$arr_strSQL_where = [
		[
			[
				['BINARY '.$t_masta_japanese_root,'unique_code',$str_sql_where_is_in,$array,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_grammar_usage_parents,'usage_category_id','ASC'],
		[$t_grammar_usage_parents,'sort','ASC'],
		[$t_grammar_usage_children,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);
	
	if(empty($arr_masta_japanese_root)){return '';}

	$table = build_html_table_with_index($arr_masta_japanese_root);
	return $table;
}


function fetch_arr_masta_japanese_root_default($t_masta_japanese_root_id, $int_selected_language){

	global
		$t_masta_japanese_root,
		$t_masta_japanese_sub_category,
		$arr_columns_masta_japanese_root,
		$int_used_language_jpn,
		$str_snake_to_camel_unique_code,
		$str_column_root_kana;

	$arr_strSQL_select = [
		[$t_masta_japanese_root,'id'],
		[$t_masta_japanese_root,'id as masta_japanese_root_id'],
		[$t_masta_japanese_root,'root_example'],
		[$t_masta_japanese_root,'unique_code as '.$str_snake_to_camel_unique_code],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_used_language_jpn]],
		[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language]],
		[$t_masta_japanese_root,$str_column_root_kana],
		[$t_masta_japanese_sub_category,'category_id']
	];

	$strSQL_from = " FROM
					$t_masta_japanese_root
					INNER JOIN $t_masta_japanese_sub_category
					ON
					$t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_masta_japanese_root,'id','=',$t_masta_japanese_root_id,'PDO::PARAM_INT','']
			],
			''
		]
	];
	// 未定義id

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(empty($arr_masta_japanese_root)){
		return [];
	}
	$arr_masta_japanese_root = $arr_masta_japanese_root[INDEX_FIRST];

	return $arr_masta_japanese_root;
}


function fetch_str_registered_sentence_answer_by_id($int_registered_sentence_id, $int_selected_language){

	global
	    $t_registered_sentence_translations;
	
	$str_quiz_translation = '';

	$arr_strSQL_select = [
		[$t_registered_sentence_translations,'answer']
	];

	$strSQL_from = ' FROM ' .$t_registered_sentence_translations;

	$arr_strSQL_where = [
		[
			[
				[$t_registered_sentence_translations,'registered_sentence_id','=',$int_registered_sentence_id,'PDO::PARAM_INT','And'],
				[$t_registered_sentence_translations,'language_id','=',$int_selected_language,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_registered_sentence_foreign_language_answers) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if(!empty($arr_registered_sentence_foreign_language_answers)){
		$str_quiz_translation = $arr_registered_sentence_foreign_language_answers[INDEX_FIRST]['answer'];
	}
	return $str_quiz_translation;
}


function get_registered_sentence_base_by_language($int_selected_language){

	global
		$t_registered_sentences,
		$t_registered_sentence_translations,
		$str_snake_to_camel_unique_code,
		$int_used_language_jpn;

	if($int_selected_language === $int_used_language_jpn){

		$arr_strSQL_select = [
			[$t_registered_sentences,'id'],
			[$t_registered_sentences,'unique_code as '.$str_snake_to_camel_unique_code],
			[$t_registered_sentences,'sentence'],
			[$t_registered_sentences,'sentence_kana'],
			[$t_registered_sentences,'sentence as japaneseText'],
			[$t_registered_sentences,'sentence as foreignLanguageText']
		];

	}
	else{

		$arr_strSQL_select = [
			[$t_registered_sentences,'id'],
			[$t_registered_sentences,'unique_code as '.$str_snake_to_camel_unique_code],
			[$t_registered_sentences,'sentence'],
			[$t_registered_sentences,'sentence_kana'],
			[$t_registered_sentences,'sentence as japaneseText'],
			[$t_registered_sentence_translations,'answer as foreignLanguageText']
		];

	}

	return $arr_strSQL_select;
}

function get_arr_registered_sentences_with_multilingual_text($arr_search_condition, $int_selected_language){

	global
		$t_registered_sentences,
		$t_registered_sentence_translations,
		$str_snake_to_camel_unique_code,
		$int_used_language_jpn;

	$arr_strSQL_select = get_registered_sentence_base_by_language($int_selected_language);
	$arr_strSQL_where = $arr_search_condition;

	if($int_selected_language === $int_used_language_jpn){
		
		$strSQL_from = " FROM $t_registered_sentences";
		
		$arr_strSQL_order = [
			[$t_registered_sentences,'sort','ASC']
		];
	
		$strSQL_option = '';
	}
	else{
				
		$strSQL_from = " FROM
						$t_registered_sentences
						INNER JOIN $t_registered_sentence_translations
						ON
						$t_registered_sentences.id = $t_registered_sentence_translations.registered_sentence_id
						";

		$arr_new = [
			[
				[$t_registered_sentence_translations,'language_id','=',$int_selected_language,'PDO::PARAM_INT','']
			],
			'And'
		];

		array_unshift($arr_strSQL_where, $arr_new);
		
		$arr_strSQL_order = [
			[$t_registered_sentences,'sort','ASC']
		];
	
		$strSQL_option = '';
	}

	list($pdo_has_error, $select_has_error, $e, $arr_target) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);

	return $arr_target;
}


function fetch_arr_registered_sentence_by_root_id($t_masta_japanese_root_id, $int_selected_language){

	global
	    $t_registered_sentences;

	$arr_strSQL_select = [
		[$t_registered_sentences,'id'],
		[$t_registered_sentences,'sentence']
	];

	$strSQL_from = ' FROM ' .$t_registered_sentences;

	$arr_strSQL_where = [
		[
			[
				[$t_registered_sentences,'is_published','=',FLAG_TRUE,'PDO::PARAM_INT','']
			],
			'And'
		],
		[
			[
				[$t_registered_sentences,'masta_japanese_root_id','=',$t_masta_japanese_root_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_registered_sentence) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_registered_sentence;
}


function fetch_arr_registered_sentence_by_unique_code($unique_code, $int_selected_language){

	global
	    $t_registered_sentences;

	$arr_strSQL_select = [
		[$t_registered_sentences,'id'],
		[$t_registered_sentences,'sentence']
	];

	$strSQL_from = ' FROM ' .$t_registered_sentences;

	$arr_strSQL_where = [
		[
			[
				[$t_registered_sentences,'is_published','=',FLAG_TRUE,'PDO::PARAM_INT','']
			],
			'And'
		],
		[
			[
				['BINARY '.$t_registered_sentences,'unique_code','=',$unique_code,'PDO::PARAM_STR','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_registered_sentences,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_registered_sentence) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_registered_sentence;
}


function fetch_arr_room_user_input_data($room_id, $t_masta_japanese_root_id, $int_selected_language){

	global
	    $t_room_user_input_data;

	$arr_strSQL_select = [
		[$t_room_user_input_data,'id'],
		[$t_room_user_input_data,'input_data']
	];

	$strSQL_from = ' FROM ' .$t_room_user_input_data;

	$arr_strSQL_where = [
		[
			[
				[$t_room_user_input_data,'room_id','=',$room_id,'PDO::PARAM_INT','And'],
				[$t_room_user_input_data,'masta_japanese_root_id','=',$t_masta_japanese_root_id,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_room_user_input_data,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_room_user_input_data) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_room_user_input_data;
}


function fetch_arr_active_recall($t_masta_japanese_root_id, $int_selected_language){

	global
		$t_masta_japanese_section,
		$t_masta_japanese_main,
		$arr_columns_masta_japanese_main,
		$int_used_language_jpn,
		$int_masta_japanese_attribute_id_activeRecall;

	$arr_strSQL_select = [
		[$t_masta_japanese_main,'id'],
		[$t_masta_japanese_main,$arr_columns_masta_japanese_main[$int_used_language_jpn].' as japaneseText'],
		[$t_masta_japanese_main,$arr_columns_masta_japanese_main[$int_selected_language].' as foreignLanguageText']
	];
	
	$strSQL_from = " FROM
					$t_masta_japanese_section
					INNER JOIN $t_masta_japanese_main
					ON
					$t_masta_japanese_section.id = $t_masta_japanese_main.masta_japanese_section_id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_masta_japanese_section,'root_id','=',$t_masta_japanese_root_id,'PDO::PARAM_INT','And'],
				[$t_masta_japanese_section,'attribute_id','=',$int_masta_japanese_attribute_id_activeRecall,'PDO::PARAM_INT','']
			],
			''
		]
	];

	$arr_strSQL_order = [
		[$t_masta_japanese_section,'sort','ASC'],
		[$t_masta_japanese_main,'sort','ASC']
	];

	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_active_recall) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
	handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	return $arr_active_recall;
}


function fetch_arr_overrides_by_layer_element_id($layer_element_id, $int_selected_language){
	
	global
		$t_layer_element_overrides,
		$t_masta_override,
		$t_masta_override_operation,
		$str_snake_to_camel_form_id,
		$arr_columns_masta_override;

    $arr_strSQL_select = [
        [$t_layer_element_overrides, 'id as leoId'],
        [$t_layer_element_overrides, 'sort'],
        [$t_layer_element_overrides, 'display_text'],
        [$t_layer_element_overrides, 'is_highlighted'],
        [$t_masta_override_operation, 'operation'],
        [$t_masta_override, 'operation_id'],
        [$t_masta_override, 'form_id as ' . $str_snake_to_camel_form_id],
        [$t_masta_override, 'voice_id as voiceId'],
        [$t_masta_override, $arr_columns_masta_override[$int_selected_language].' as display_text_from_masta']
    ];
    $strSQL_from = " FROM (($t_layer_element_overrides
                     INNER JOIN $t_masta_override
                       ON $t_layer_element_overrides.masta_override_id = $t_masta_override.id)
                     INNER JOIN $t_masta_override_operation
                       ON $t_masta_override.operation_id = $t_masta_override_operation.id)";
    $arr_strSQL_where = [
		[
			[
				[ $t_layer_element_overrides, 'layer_element_id', '=', intval($layer_element_id), 'PDO::PARAM_INT', '' ]
			]
			,
			''
		]
	];
    $arr_strSQL_order = [
		[ $t_layer_element_overrides, 'sort', 'ASC' ]
	];
    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $rows) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);
    return $rows ?: [];
}

function fetch_arr_room_lesson_dates_by_room_id($room_id, $int_selected_language) {

    global $t_room_lesson_dates;

    $arr_strSQL_select = [
        [$t_room_lesson_dates, 'id'],
        [$t_room_lesson_dates, 'lesson_date'],
        [$t_room_lesson_dates, 'lesson_seq']
    ];

    $strSQL_from = ' FROM ' . $t_room_lesson_dates;

    $arr_strSQL_where = [
        [
            [
                [$t_room_lesson_dates, 'room_id', '=', $room_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_room_lesson_dates, 'lesson_date', 'DESC'],
        [$t_room_lesson_dates, 'lesson_seq', 'DESC'],
        [$t_room_lesson_dates, 'id', 'DESC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $rows) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

    return $rows ?: [];
}


function fetch_lesson_date_by_lesson_date_id($lesson_date_id, $int_selected_language) {

    global $t_room_lesson_dates;

    $arr_strSQL_select = [
        [$t_room_lesson_dates, 'room_id'],
        [$t_room_lesson_dates, 'lesson_date']
    ];

    $strSQL_from = ' FROM ' . $t_room_lesson_dates;

    $arr_strSQL_where = [
        [
            [
                [$t_room_lesson_dates, 'id', '=', $lesson_date_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [];
    $strSQL_option = ' LIMIT 1';

    list($pdo_has_error, $select_has_error, $e, $rows) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

    if (!$rows || !isset($rows[0]['room_id'])) {
        return null;
    }

    return [
        'room_id' => (int)$rows[0]['room_id'],
        'lesson_date' => isset($rows[0]['lesson_date']) ? (string)$rows[0]['lesson_date'] : ''
    ];
}

function fetch_arr_room_whiteboards_by_lesson_date_id($lesson_date_id, $int_selected_language) {

    global $t_room_whiteboards;

    $arr_strSQL_select = [
        [$t_room_whiteboards, 'id'],
        [$t_room_whiteboards, 'lesson_date_id'],
        [$t_room_whiteboards, 'board_order'],
        [$t_room_whiteboards, 'board_title'],
        [$t_room_whiteboards, 'movable_snapshot_json'],
        [$t_room_whiteboards, 'canvas_ops_gz'],
        [$t_room_whiteboards, 'canvas_ops_format'],
        [$t_room_whiteboards, 'background_image_path'],
        [$t_room_whiteboards, 'revision'],
        [$t_room_whiteboards, 'created_at'],
        [$t_room_whiteboards, 'updated_at']
    ];

    $strSQL_from = ' FROM ' . $t_room_whiteboards;

    $arr_strSQL_where = [
        [
            [
                [$t_room_whiteboards, 'lesson_date_id', '=', $lesson_date_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_room_whiteboards, 'board_order', 'ASC'],
        [$t_room_whiteboards, 'id', 'ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $rows) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

    return $rows ?: [];
}

function fetch_arr_room_memos_by_lesson_date_id(int $lesson_date_id, int $int_selected_language): array
{
    global $t_room_memos;

    $arr_strSQL_select = [
        [$t_room_memos, 'id'],
        [$t_room_memos, 'lesson_date_id'],
        [$t_room_memos, 'memo_order'],
        [$t_room_memos, 'memo_title'],
    ];

    $strSQL_from = ' FROM ' . $t_room_memos;

    $arr_strSQL_where = [
        [
            [
                [$t_room_memos, 'lesson_date_id', '=', $lesson_date_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$t_room_memos, 'memo_order', 'ASC'],
        [$t_room_memos, 'id', 'ASC'],
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $rows) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

    return $rows ?: [];
}

function fetch_arr_lesson_memo_by_memo_id($memo_id, $int_selected_language)
{
    global $t_room_memos;

    $arr_strSQL_select = [
        [$t_room_memos, 'id'],
        [$t_room_memos, 'memo_text'],
        [$t_room_memos, 'updated_at']
    ];

    $strSQL_from = ' FROM ' . $t_room_memos;

    $arr_strSQL_where = [
        [
            [
                [$t_room_memos, 'id', '=', $memo_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $rows) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );
    handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

    return $rows ?: [];
}

function fetch_arr_room_homework_by_homework_id_and_target_date(int $room_id, int $homework_id, string $target_date, int $int_selected_language): array
{
    global $t_room_homeworks;

    if ($room_id <= 0 || $homework_id <= 0 || $target_date === '') {
        return [];
    }

    $arr_strSQL_select = [
        [$t_room_homeworks, 'id'],
        [$t_room_homeworks, 'room_id'],
        [$t_room_homeworks, 'target_date'],
        [$t_room_homeworks, 'content_json'],
        [$t_room_homeworks, 'form_list_json'],
        [$t_room_homeworks, 'created_at'],
        [$t_room_homeworks, 'updated_at'],
    ];

    $strSQL_from = ' FROM ' . $t_room_homeworks;

    $arr_strSQL_where = [
        [
            [
                [$t_room_homeworks, 'id', '=', $homework_id, 'PDO::PARAM_INT', 'And'],
                [$t_room_homeworks, 'room_id', '=', $room_id, 'PDO::PARAM_INT', 'And'],
                [$t_room_homeworks, 'target_date', '=', $target_date, 'PDO::PARAM_STR', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [];
    $strSQL_option = ' LIMIT 1';

    list($pdo_has_error, $select_has_error, $e, $arr_rows) = execute_select_and_fetch_all(
        $arr_strSQL_select,
        $strSQL_from,
        $arr_strSQL_where,
        $arr_strSQL_order,
        $strSQL_option
    );

    handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);

    if (!is_array($arr_rows) || empty($arr_rows)) {
        return [];
    }

    return $arr_rows[INDEX_FIRST];
}

function fetch_arr_room_member_users_with_email($room_id, $int_selected_language) {

	global
		$t_room_users,
		$t_users;

	$arr_strSQL_select = [
		[$t_room_users, 'id'],
		[$t_room_users, 'room_id'],
		[$t_room_users, 'user_id'],
		[$t_room_users, 'confirmed'],
		[$t_users, 'user_email']
	];

	$strSQL_from = " FROM
					$t_room_users
					INNER JOIN $t_users
					ON
					$t_room_users.user_id = $t_users.id
					";

	$arr_strSQL_where = [
		[
			[
				[$t_room_users, 'room_id', '=', $room_id, 'PDO::PARAM_INT', 'And'],
				[$t_room_users, 'confirmed', '=', 1, 'PDO::PARAM_INT', '']
			],
			''
		]
	];

	$arr_strSQL_order = [];
	$strSQL_option = '';

	list($pdo_has_error, $select_has_error, $e, $arr_room_member_users) =
		execute_select_and_fetch_all(
			$arr_strSQL_select,
			$strSQL_from,
			$arr_strSQL_where,
			$arr_strSQL_order,
			$strSQL_option
		);

	handle_database_error_and_redirect(
		$pdo_has_error,
		$select_has_error,
		$e,
		$int_selected_language
	);

	return $arr_room_member_users;
}


function get_data_workshop_mode($room_unique_code, $user_level) {

    global
        $workshop_trial_unique_code,
        $workshop_no_room_unique_code,
        $int_Basic_Student;

    // trial（未ログイン含む）
    if ($room_unique_code === $workshop_trial_unique_code) {
        return 'trial';
    }

    // no room（basic / plus）
    if ($room_unique_code === $workshop_no_room_unique_code) {

        // 未ログインは trial 扱い
        if ($user_level === null) {
            return 'trial';
        }

        if ((int)$user_level === (int)$int_Basic_Student) {
            return 'basic';
        }

        return 'plus';
    }

    // premium / vip
    return 'inRoom';
}



function get_data_workshop_context($room_unique_code, $int_selected_language) {

	$user_level = get_user_level();
    $mode = get_data_workshop_mode($room_unique_code, $user_level);

    $room_id = null;
    $room_type = null;

    if ($mode === 'inRoom') {

        $arr_rooms = fetch_arr_rooms_from_unique_code(
            $room_unique_code,
            $int_selected_language
        );

        $room_id = (int)($arr_rooms[INDEX_FIRST]['id'] ?? 0);
        $room_type = (int)($arr_rooms[INDEX_FIRST]['room_type'] ?? 0);

        if ($room_id <= 0) {
            return [
                'mode' => $mode,
                'room_id' => null,
                'room_type' => null,
                'room_found' => false
            ];
        }
    }

    return [
        'mode' => $mode,
        'room_id' => $room_id,
        'room_type' => $room_type,
        'room_found' => true
    ];
}


function fetch_int_max_sort($target_table, $target_column, $value, $int_selected_language, $sort_column = 'sort', $pdo_param_type = 'PDO::PARAM_INT'){
    $arr_strSQL_select = [
        ['', 'MAX(' . $sort_column . ') AS max_sort']
    ];

    $strSQL_from = ' FROM ' . $target_table;

    $arr_strSQL_where = [
        [
            [
                [$target_table, $target_column, '=', $value, $pdo_param_type, '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [
        [$target_table, $sort_column, 'ASC']
    ];

    $strSQL_option = '';

    list($pdo_has_error, $select_has_error, $e, $arr_result) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

	if (!is_array($arr_result) || empty($arr_result)) {
        return null;
    }
	else{
		return $arr_result[INDEX_FIRST]['max_sort'];
	}

}


function count_next_sort($target_table, $target_column, $value, $int_selected_language, $sort_column = 'sort', $pdo_param_type = 'PDO::PARAM_INT'){
    $max_sort = fetch_int_max_sort($target_table, $target_column, $value, $int_selected_language, $sort_column, $pdo_param_type);
    if ($max_sort === null) {
        return SORT_FIRST;
    }
    return $max_sort + 1;
}


function notify_homework_added_mail($to_email, $subject, $body, $from_email) {

	$to_email = trim((string)$to_email);
	if ($to_email === '') {
		return false;
	}

	mb_language('uni');
	mb_internal_encoding('UTF-8');

	$headers = '';
	$headers .= 'From: Japanese Workshop <' . $from_email . ">\r\n";
	$headers .= 'Reply-To: ' . $from_email . "\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
	$headers .= "Content-Transfer-Encoding: 8bit\r\n";

	$params = '-f ' . escapeshellarg($from_email);

	return mb_send_mail($to_email, $subject, $body, $headers, $params);
}


function customSort($a, $b){

    global
        $str_snake_to_camel_category_id,
        $str_snake_to_camel_parent_sort,
        $str_snake_to_camel_level,
        $str_snake_to_camel_sort;

    $a_category_id = $a[$str_snake_to_camel_category_id] ?? 0;
    $b_category_id = $b[$str_snake_to_camel_category_id] ?? 0;

    $result = $a_category_id - $b_category_id;

    if ($result == 0) {
        $a_parent_sort = $a[$str_snake_to_camel_parent_sort] ?? 0;
        $b_parent_sort = $b[$str_snake_to_camel_parent_sort] ?? 0;
        $result = $a_parent_sort - $b_parent_sort;
    }

    if ($result == 0) {
        $a_level = $a[$str_snake_to_camel_level] ?? 0;
        $b_level = $b[$str_snake_to_camel_level] ?? 0;
        $result = $a_level - $b_level;
    }

    if ($result == 0) {
        $a_sort = $a[$str_snake_to_camel_sort] ?? 0;
        $b_sort = $b[$str_snake_to_camel_sort] ?? 0;
        $result = $a_sort - $b_sort;
    }

    return $result;
}


function customSortKana($a, $b){

	global
		$str_snake_to_camel_kana;

	return strcmp($a[$str_snake_to_camel_kana], $b[$str_snake_to_camel_kana]);
}


function customSortLabels($a, $b) {

	global
		$str_snake_to_camel_japanese,
		$str_snake_to_camel_kana;

	if (preg_match('/\d+/', $a[$str_snake_to_camel_japanese], $matchesA) && preg_match('/\d+/', $b[$str_snake_to_camel_japanese], $matchesB)) {
		return intval($matchesA[INDEX_FIRST]) - intval($matchesB[INDEX_FIRST]);
	}

	return strcmp($a[$str_snake_to_camel_kana], $b[$str_snake_to_camel_kana]);
}


function jws_add_file_version($url){

    $path = parse_url($url, PHP_URL_PATH);
    $file = $_SERVER['DOCUMENT_ROOT'] . $path;

    if(file_exists($file)){
        return $url . '?v=' . filemtime($file);
    }

    return $url;
}



/******************************************************
 *  GENERATE
 *  
 ******************************************************/
function generate_random_string() {
	
	global
	    $int_unique_code_max_length;

	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$random_unique_code = '';

	for ($i = INDEX_FIRST; $i < $int_unique_code_max_length; $i++) {
		$random_unique_code .= $characters[rand(0, strlen($characters) - 1)];
	}

	return $random_unique_code;
}


function generate_unique_code($target_table, $target_column, $select_column, $int_selected_language, $max_attempts = 32) {
    for ($i = 0; $i < $max_attempts; $i++) {
        $candidate = generate_random_string();

        $arr_strSQL_select = [
            [$target_table, $select_column]
        ];
        $strSQL_from = ' FROM ' . $target_table;
        $arr_strSQL_where = [
            [
                [
                    ['BINARY ' . $target_table, $target_column, '=', $candidate, 'PDO::PARAM_STR', '']
                ],
                ''
            ]
        ];
        $arr_strSQL_order = [];
        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $arr_targets) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
        handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

        if (empty($arr_targets)) {
            return $candidate;
        }
    }
    return null;
}


function generate_arr_recording_where_conditions(
    $table,
    $key_column,
    $key_value,
    $recording_shorts,
    $recording_video,
    $int_is_recording_shorts,
    $int_is_recording_video
){
    $conditions = [
        [$table, $key_column, '=', $key_value, 'PDO::PARAM_INT', '']
    ];

    if ($recording_shorts === $int_is_recording_shorts) {
        $conditions[0][5] = 'And';
        $conditions[] = [$table, 'is_hidden_shorts', '=', FLAG_FALSE, 'PDO::PARAM_INT', ''];
    }
    elseif ($recording_video === $int_is_recording_video) {
        $conditions[0][5] = 'And';
        $conditions[] = [$table, 'is_hidden_video', '=', FLAG_FALSE, 'PDO::PARAM_INT', ''];
    }

    return [
        [
            $conditions,
            ''
        ]
    ];
}


function generate_redirect_url_after_login(int $int_selected_language): ?string
{
    global
        $int_Free_Member,
        $int_Basic_Student,
        $path_dashboard;

    if (!is_user_logged_in()) {
        return null;
    }

	$url_dashboard = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_dashboard, '/'))
	);

	return $url_dashboard;
}


function execute_update_room_whiteboard_state($whiteboard_id, $lesson_date_id, $state) {

	$movable_snapshot = [];
	if (isset($state['movableElementsSnapshot']) && is_array($state['movableElementsSnapshot'])) {
		$movable_snapshot = $state['movableElementsSnapshot'];
	}

	$stroke_history = [];
	if (isset($state['strokeHistory']) && is_array($state['strokeHistory'])) {
		$stroke_history = $state['strokeHistory'];
	}

	$canvas_meta = null;
	if (isset($state['canvasMeta']) && is_array($state['canvasMeta'])) {
		$canvas_meta = $state['canvasMeta'];
	}

	$title = null;
	if (isset($state['title']) && is_string($state['title'])) {
		$title = $state['title'];
	}

	// movable_snapshot_json は「movable単体」でも良いですが、
	// 復元で canvasMeta を使うので同梱しておくのがおすすめです。
	$movable_snapshot_json = json_encode(
		[
			'movableElementsSnapshot' => $movable_snapshot,
			'canvasMeta' => $canvas_meta
		],
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);

	if ($movable_snapshot_json === false) {
		respond_error('Failed to encode movable snapshot', 500);
	}

	// canvas_ops_gz は strokeHistory を中心に、必要なら title/canvasMeta も同梱
	$ops = [
		'strokeHistory' => $stroke_history,
		'canvasMeta' => $canvas_meta,
		'title' => $title
	];

	$ops_json = json_encode(
		$ops,
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);

	if ($ops_json === false) {
		respond_error('Failed to encode canvas ops', 500);
	}

	$canvas_ops_gz = gzencode($ops_json, 6);
	if ($canvas_ops_gz === false) {
		respond_error('Failed to gzip canvas ops', 500);
	}

	/* ------------------------------
		UPDATE（効率重視：クエリ1回）
	------------------------------ */

	global $t_room_whiteboards;

	$pdo = connect_to_database();
	if (empty($pdo)) {
		respond_error('Database connection failed', 500);
	}

	$pdo->beginTransaction();

	try {

		$sql = '
			UPDATE ' . $t_room_whiteboards . '
			SET
				movable_snapshot_json = :movable_snapshot_json,
				canvas_ops_gz = :canvas_ops_gz,
				canvas_ops_format = :canvas_ops_format,
				revision = revision + 1
			WHERE
				id = :whiteboard_id
				AND lesson_date_id = :lesson_date_id
			LIMIT 1
		';

		$stmt = $pdo->prepare($sql);

		$stmt->bindValue(':movable_snapshot_json', $movable_snapshot_json, PDO::PARAM_STR);
		$stmt->bindValue(':canvas_ops_gz', $canvas_ops_gz, PDO::PARAM_LOB);
		$stmt->bindValue(':canvas_ops_format', 'ops_gzip_json_v1', PDO::PARAM_STR);

		$stmt->bindValue(':whiteboard_id', (int)$whiteboard_id, PDO::PARAM_INT);
		$stmt->bindValue(':lesson_date_id', (int)$lesson_date_id, PDO::PARAM_INT);

		$stmt->execute();

		$affected = (int)$stmt->rowCount();

		if ($affected <= 0) {
			$pdo->rollBack();
			// id が存在しない / lesson_date_id と紐付いていない
			respond_error('Whiteboard not found', 404);
		}

		$pdo->commit();

	} catch (PDOException $exception) {

		$pdo->rollBack();
		respond_exception($exception);

	}
}


function execute_create_room_whiteboard_with_state($room_id, $lesson_date_id, $board_title, $state) {

	$movable_snapshot = [];
	if (isset($state['movableElementsSnapshot']) && is_array($state['movableElementsSnapshot'])) {
		$movable_snapshot = $state['movableElementsSnapshot'];
	}

	$stroke_history = [];
	if (isset($state['strokeHistory']) && is_array($state['strokeHistory'])) {
		$stroke_history = $state['strokeHistory'];
	}

	$canvas_meta = null;
	if (isset($state['canvasMeta']) && is_array($state['canvasMeta'])) {
		$canvas_meta = $state['canvasMeta'];
	}

	$title = null;
	if (isset($state['title']) && is_string($state['title'])) {
		$title = $state['title'];
	}

	// movable_snapshot_json は movable + canvasMeta を同梱（復元が楽）
	$movable_snapshot_json = json_encode(
		[
			'movableElementsSnapshot' => $movable_snapshot,
			'canvasMeta' => $canvas_meta
		],
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);

	if ($movable_snapshot_json === false) {
		respond_error('Failed to encode movable snapshot', 500);
	}

	// canvas_ops_gz は strokeHistory を中心に title/canvasMeta も同梱
	$ops = [
		'strokeHistory' => $stroke_history,
		'canvasMeta' => $canvas_meta,
		'title' => $title
	];

	$ops_json = json_encode(
		$ops,
		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
	);

	if ($ops_json === false) {
		respond_error('Failed to encode canvas ops', 500);
	}

	$canvas_ops_gz = gzencode($ops_json, 6);
	if ($canvas_ops_gz === false) {
		respond_error('Failed to gzip canvas ops', 500);
	}

	$canvas_ops_format = 'ops_gzip_json_v1';

	$pdo = connect_to_database();
	if (!($pdo instanceof PDO)) {
		respond_error('Database connection failed', 500);
	}

	global $t_room_lesson_dates;
	global $t_room_whiteboards;

	$pdo->beginTransaction();

	try {

		// lesson_date_id が room_id に紐づくか確認
		$stmt = $pdo->prepare(
			'SELECT room_id
			FROM ' . $t_room_lesson_dates . '
			WHERE id = :lesson_date_id
			LIMIT 1'
		);
		$stmt->execute([
			':lesson_date_id' => $lesson_date_id
		]);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if (!$row) {
			$pdo->rollBack();
			respond_error('Lesson date not found', 404);
		}

		$lesson_room_id = (int)$row['room_id'];
		if ($lesson_room_id !== (int)$room_id) {
			$pdo->rollBack();
			respond_error('Forbidden', 403);
		}

		// 同 lesson_date_id 内で board_order を発行（max + 1）
		$stmt = $pdo->prepare(
			'SELECT COALESCE(MAX(board_order), 0) AS max_order
			FROM ' . $t_room_whiteboards . '
			WHERE lesson_date_id = :lesson_date_id'
		);
		$stmt->execute([
			':lesson_date_id' => $lesson_date_id
		]);
		$max_order = (int)$stmt->fetchColumn();
		$board_order = $max_order + 1;

		// もし state.title があって、かつ board_title が未指定ならタイトルとして採用したい場合：
		// if ($board_title === null && $title !== null) {
		// 	$board_title = $title;
		// }

		// 必要ならここで背景画像など初期値を決める（現状は null）
		$background_image_path = null;

		$stmt = $pdo->prepare(
			'INSERT INTO ' . $t_room_whiteboards . ' (
				lesson_date_id,
				board_order,
				board_title,
				movable_snapshot_json,
				canvas_ops_gz,
				canvas_ops_format,
				background_image_path,
				revision
			) VALUES (
				:lesson_date_id,
				:board_order,
				:board_title,
				:movable_snapshot_json,
				:canvas_ops_gz,
				:canvas_ops_format,
				:background_image_path,
				:revision
			)'
		);

		$stmt->bindValue(':lesson_date_id', (int)$lesson_date_id, PDO::PARAM_INT);
		$stmt->bindValue(':board_order', (int)$board_order, PDO::PARAM_INT);

		if ($board_title === null) {
			$stmt->bindValue(':board_title', null, PDO::PARAM_NULL);
		} else {
			$stmt->bindValue(':board_title', $board_title, PDO::PARAM_STR);
		}

		$stmt->bindValue(':movable_snapshot_json', $movable_snapshot_json, PDO::PARAM_STR);
		$stmt->bindValue(':canvas_ops_gz', $canvas_ops_gz, PDO::PARAM_LOB);
		$stmt->bindValue(':canvas_ops_format', $canvas_ops_format, PDO::PARAM_STR);

		if ($background_image_path === null) {
			$stmt->bindValue(':background_image_path', null, PDO::PARAM_NULL);
		} else {
			$stmt->bindValue(':background_image_path', $background_image_path, PDO::PARAM_STR);
		}

		// 作成時は 1
		$stmt->bindValue(':revision', 1, PDO::PARAM_INT);

		$stmt->execute();

		$whiteboard_id = (int)$pdo->lastInsertId();
		if ($whiteboard_id <= 0) {
			$pdo->rollBack();
			respond_error('Failed to create whiteboard', 500);
		}

		$pdo->commit();

		$label = $board_title !== null ? $board_title : ('Board ' . $board_order);

		respond_success([
			'whiteboard_id' => $whiteboard_id,
			'lesson_date_id' => (int)$lesson_date_id,
			'board_order' => (int)$board_order,
			'board_title' => $board_title,
			'label' => $label,
			'canvas_ops_format' => $canvas_ops_format,
			'revision' => 1
		]);

	} catch (PDOException $exception) {

		$pdo->rollBack();
		respond_exception($exception);

	}
}



/******************************************************
 *  APPLY
 *  
 ******************************************************/
function apply_original_tags_to_html($word){
	$word = str_replace('[red]', '<span class="originalTagChangers colorChangerHighlightEmRed">', $word);
	$word = str_replace('[/red]', '</span>', $word);
	$word = str_replace('[blue]', '<span class="originalTagChangers colorChangerHighlightEmBlue">', $word);
	$word = str_replace('[/blue]', '</span>', $word);
	$word = str_replace('[underline]', '<span class="originalTagChangers underLine">', $word);
	$word = str_replace('[/underline]', '</span>', $word);
	$word = str_replace('[lang]', '«', $word);
	$word = str_replace('[/lang]', '»', $word);
	$word = str_replace('[slot]', '[', $word);
	$word = str_replace('[/slot]', ']', $word);
	$word = str_replace('[term]', '<span class="originalTagChangers grammarViewText termText">', $word);
	$word = str_replace('[/term]', '</span>', $word);
	$word = str_replace('[expression]', '『', $word);
	$word = str_replace('[/expression]', '』', $word);
	$word = str_replace('[form]', '<span class="originalTagChangers grammarViewText formText">', $word);
	$word = str_replace('[/form]', '</span>', $word);
	$word = str_replace('[correct]', '<span class="originalTagChangers colorChangerHighlightEmRed">', $word);
	$word = str_replace('[/correct]', '</span>', $word);
	$word = str_replace('[miss]', '<span class="originalTagChangers colorChangerHighlightEmBlue">', $word);
	$word = str_replace('[/miss]', '</span>', $word);
	$word = str_replace('[before]', '<span class="originalTagChangers colorChangerHighlightEmBlue">', $word);
	$word = str_replace('[/before]', '</span>', $word);
	$word = str_replace('[after]', '<span class="originalTagChangers colorChangerHighlightEmRed">', $word);
	$word = str_replace('[/after]', '</span>', $word);
	$word = str_replace('[emphasis]', '<span class="originalTagChangers grammarViewText emphasisText">', $word);
	$word = str_replace('[/emphasis]', '</span>', $word);
	$word = str_replace('[strike]', '<s>', $word);
	$word = str_replace('[/strike]', '</s>', $word);
	$word = str_replace('[mute]', '', $word);
	$word = str_replace('[/mute]', '', $word);
	$word = str_replace('[jpn]', '', $word);
	$word = str_replace('[/jpn]', '', $word);
	$word = str_replace('[cht]', '', $word);
	$word = str_replace('[/cht]', '', $word);
	$word = str_replace('[eng]', '', $word);
	$word = str_replace('[/eng]', '', $word);
	return $word;
}


function apply_remove_original_tags($word){
	$word = str_replace('[red]', '', $word);
	$word = str_replace('[/red]', '', $word);
	$word = str_replace('[blue]', '', $word);
	$word = str_replace('[/blue]', '', $word);
	$word = str_replace('[underline]', '', $word);
	$word = str_replace('[/underline]', '', $word);
	$word = str_replace('[lang]', '«', $word);
	$word = str_replace('[/lang]', '»', $word);
	$word = str_replace('[slot]', '[', $word);
	$word = str_replace('[/slot]', ']', $word);
	$word = str_replace('[term]', '', $word);
	$word = str_replace('[/term]', '', $word);
	$word = str_replace('[expression]', '『', $word);
	$word = str_replace('[/expression]', '』', $word);
	$word = str_replace('[form]', '', $word);
	$word = str_replace('[/form]', '', $word);
	$word = str_replace('[correct]', '', $word);
	$word = str_replace('[/correct]', '', $word);
	$word = str_replace('[miss]', '', $word);
	$word = str_replace('[/miss]', '', $word);
	$word = str_replace('[before]', '', $word);
	$word = str_replace('[/before]', '', $word);
	$word = str_replace('[after]', '', $word);
	$word = str_replace('[/after]', '', $word);
	$word = str_replace('[emphasis]', '', $word);
	$word = str_replace('[/emphasis]', '', $word);
	$word = str_replace('[strike]', '', $word);
	$word = str_replace('[/strike]', '', $word);
	$word = str_replace('[mute]', '', $word);
	$word = str_replace('[/mute]', '', $word);
	$word = str_replace('[jpn]', '', $word);
	$word = str_replace('[/jpn]', '', $word);
	$word = str_replace('[cht]', '', $word);
	$word = str_replace('[/cht]', '', $word);
	$word = str_replace('[eng]', '', $word);
	$word = str_replace('[/eng]', '', $word);
	return $word;
}


function apply_text_for_output($text) {
	$text = escape_html_with_nl2br($text);
	$text = apply_original_tags_to_html($text);
	return $text;
}



/******************************************************
 *  RESPOND
 *  
 ******************************************************/

function respond_success($data = [], $extra = []) {
	echo json_encode(array_merge([
		'status' => 'success',
		'data' => $data
	], $extra), JSON_UNESCAPED_UNICODE);
	exit;
}

function respond_error($message, $code = 400) {
	error_log("respond_error: $message");
	http_response_code($code);
	echo json_encode([
		'status' => 'error',
		'message' => $message
	], JSON_UNESCAPED_UNICODE);
	exit;
}

function respond_exception(Throwable $e, $log_message = 'Exception occurred') {

	error_log($log_message . ': ' . $e->getMessage());
    error_log('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
    error_log($e->getTraceAsString());

	http_response_code(500);
	echo json_encode([
		'status' => 'error',
		'message' => 'Internal Server Error'
	], JSON_UNESCAPED_UNICODE);
	exit;
}



/******************************************************
 *  SINGLE SESSION
 *  
 ******************************************************/
/**
 * 全API共通: 1 user = 1 device の単一セッションを強制する
 *
 * 前提:
 * - Cookie: wise_session
 * - DB: $t_user_membership (user_id UNIQUE)
 * - t_user_membership.active_session_token を使用
 *
 * 成功時: wp_user_id を返す
 * 失敗時: 401/403 を返して exit（respond_error があればそれを使用）
 */

function jws_set_single_session_cookie($token, $expires_ts) {

    setcookie(
        'wise_session',
        $token,
        [
            'expires' => $expires_ts,
            'path' => '/',
            'secure' => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );
}

function jws_clear_single_session_cookie() {

    setcookie(
        'wise_session',
        '',
        [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => is_ssl(),
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );
}

function jws_issue_single_session_token($wp_user_id, $remember = false)
{
    global
        $t_user_membership;

    $token = bin2hex(random_bytes(32));
    $now = current_time('mysql');

    if ($remember) {
        $expires_ts = time() + 60 * 60 * 24 * 30;
    } else {
        $expires_ts = time() + 60 * 60 * 12;
    }

    $expires_at = date('Y-m-d H:i:s', $expires_ts);

    $device_id = isset($_POST['device_id'])
        ? sanitize_text_field($_POST['device_id'])
        : null;

    $update_table = $t_user_membership;

    $arr_updateSQL = [
        ['active_session_token', ':update_active_session_token', $token, 'PDO::PARAM_STR'],
        ['active_device_id', ':update_active_device_id', $device_id, $device_id === null ? 'PDO::PARAM_NULL' : 'PDO::PARAM_STR'],
        ['active_session_updated_at', ':update_active_session_updated_at', $now, 'PDO::PARAM_STR'],
    ];

    $arr_whereSQL = [
        ['user_id', ':where_user_id', (int)$wp_user_id, 'PDO::PARAM_INT', '']
    ];

    list($pdo_has_error, $update_has_error, $e) = execute_update_data(
        $update_table,
        $arr_updateSQL,
        $arr_whereSQL
    );

    handle_database_error_and_respond($pdo_has_error, $update_has_error, $e);

    jws_set_single_session_cookie($token, $expires_ts);

    return [true, $token, $expires_ts];
}

function jws_require_single_session(int $int_selected_language = 0) {

    global
        $t_user_membership;

    $arr_messages = [
        'not_logged_in' => [
            'ログインが必要です。',
            '需要登入。',
            'Login is required.'
        ],
        'no_session' => [
            'セッションがありません。再ログインしてください。',
            '找不到工作階段。請重新登入。',
            'No session found. Please log in again.'
        ],
        'membership_missing' => [
            '会員情報が見つかりません。',
            '找不到會員資料。',
            'Membership information was not found.'
        ],
        'session_invalid' => [
            'セッションが無効です。再ログインしてください。',
            '工作階段無效。請重新登入。',
            'Session is invalid. Please log in again.'
        ],
        'session_conflict' => [
            '他の端末でログインされています。再ログインしてください。',
            '您已在其他裝置登入。請重新登入。',
            'You are logged in on another device. Please log in again.'
        ],
    ];

    $lang = isset($arr_messages['not_logged_in'][$int_selected_language])
        ? $int_selected_language
        : 0;

    if (!is_user_logged_in()) {
        respond_error(
            'NOT_LOGGED_IN: ' . $arr_messages['not_logged_in'][$lang],
            401
        );
        exit;
    }

    $user = wp_get_current_user();
    $user_id = (int)$user->ID;

    if ($user_id <= 0) {
        respond_error(
            'NOT_LOGGED_IN: ' . $arr_messages['not_logged_in'][$lang],
            401
        );
        exit;
    }

    $client_token = $_COOKIE['wise_session'] ?? '';
    $client_token = is_string($client_token) ? $client_token : '';

    if ($client_token === '') {
        respond_error(
            'NO_SESSION: ' . $arr_messages['no_session'][$lang],
            401
        );
        exit;
    }

    $arr_strSQL_select = [
        [$t_user_membership, 'active_session_token']
    ];

    $strSQL_from = ' FROM ' . $t_user_membership;

    $arr_strSQL_where = [
        [
            [
                [$t_user_membership, 'user_id', '=', $user_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [];
    $strSQL_option = ' LIMIT 1';

    list($pdo_has_error, $select_has_error, $e, $arr_session) =
        execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            $strSQL_option
        );

    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    if (empty($arr_session)) {
        respond_error(
            'MEMBERSHIP_MISSING: ' . $arr_messages['membership_missing'][$lang],
            403
        );
        exit;
    }

    $server_token = (string)($arr_session[0]['active_session_token'] ?? '');

    if ($server_token === '') {
        respond_error(
            'SESSION_INVALID: ' . $arr_messages['session_invalid'][$lang],
            401
        );
        exit;
    }

    if (!hash_equals($server_token, $client_token)) {
        respond_error(
            'SESSION_CONFLICT: ' . $arr_messages['session_conflict'][$lang],
            401
        );
        exit;
    }

    return $user_id;
}

/******************************************************
 *  SINGLE SESSION for PAGE
 *  
 ******************************************************/
function jws_get_request_uri_for_redirect_to(): string {

    $request_uri = (string)($_SERVER['REQUEST_URI'] ?? '/');

	$url_home_current = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		'/'
	);

	$base_path = (string) parse_url(
		$url_home_current,
		PHP_URL_PATH
	); // 例: "/cht/"

    $base_path = '/' . trim($base_path, '/') . '/';
    if ($base_path === '//') {
        $base_path = '/';
    }

    if ($base_path !== '/' && strpos($request_uri, $base_path) === 0) {
        // 先頭の "/cht/" を取り除く（先頭 "/" は残す）
        $request_uri = substr($request_uri, strlen($base_path) - 1);
        if ($request_uri === '' || $request_uri[0] !== '/') {
            $request_uri = '/' . $request_uri;
        }
    }

    return $request_uri;
}

function build_html_form_button_go_to_login(
    int $int_selected_language,
    string $redirect_to,
    string $reason = '',
    string $button_background_color = '#666',
    string $class_div = 'divChoices',
    string $class_input = 'inputChoices'
): string {

    global $path_login;

    $arr_label = [
        'ログインへ',
        '前往登入',
        'Go to Login'
    ];

    $label = $arr_label[$int_selected_language] ?? $arr_label[0];

    $path = $path_login ?? '/account/login/';
    $login_base_url = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path, '/'))
	);

    // build_html_login_page は redirect_to を GET/POST から拾える設計なので GET に付けます
    $url_login = add_query_arg(
        [
            'redirect_to' => rawurlencode($redirect_to),
            'reason' => $reason
        ],
        $login_base_url
    );

    return implode(
        '',
        [
            '<form action="' . esc_url($url_login) . '" method="GET">',
                '<div class="' . escape_html($class_div) . '">',
                    '<input class="' . escape_html($class_input) . '" style="background-color:' . escape_html($button_background_color) . '" type="submit" value="' . escape_html($label) . '">',
                '</div>',
            '</form>'
        ]
    );
}

function build_html_single_session_error_page(int $int_selected_language): string {

    $reason = $_GET['reason'] ?? '';
    $reason = is_string($reason) ? $reason : '';

    $arr_messages = [
        'session_conflict' => [
            '他の端末でログインされたため、再ログインが必要です。',
            '您已在其他裝置登入，因此需要重新登入。',
            'You are logged in on another device. Please log in again.'
        ],
        'no_session' => [
            'セッションがありません。再ログインしてください。',
            '找不到工作階段。請重新登入。',
            'No session found. Please log in again.'
        ],
        'not_logged_in' => [
            'ログインが必要です。',
            '需要登入。',
            'Login is required.'
        ],
        'session_invalid' => [
            'セッションが無効です。再ログインしてください。',
            '工作階段無效。請重新登入。',
            'Session is invalid. Please log in again.'
        ],
        'membership_missing' => [
            '会員情報が見つかりません。',
            '找不到會員資料。',
            'Membership information was not found.'
        ],
        'default' => [
            'アクセスできません。再ログインしてください。',
            '無法存取。請重新登入。',
            'Access denied. Please log in again.'
        ]
    ];

    $arr_titles = [
        'セッションエラー',
        '工作階段錯誤',
        'Session Error'
    ];

    // 強制ログアウト対象（必要なものだけに絞る）
    $arr_force_logout_reasons = [
        'session_conflict',
        'no_session',
        'session_invalid'
    ];

    if (in_array($reason, $arr_force_logout_reasons, true)) {

		// 戻るキャッシュ対策
		nocache_headers();

		// 単一セッションCookie削除
		jws_clear_single_session_cookie();

		// PHPセッション削除
		if (session_status() === PHP_SESSION_ACTIVE) {

			$_SESSION = [];

			if (ini_get('session.use_cookies')) {

				$params = session_get_cookie_params();

				setcookie(
					session_name(),
					'',
					time() - 42000,
					$params['path'],
					$params['domain'],
					$params['secure'],
					$params['httponly']
				);
			}

			session_destroy();
		}

		// WPログアウト
		if (is_user_logged_in()) {
			global $jws_skip_logout_redirect;
			$jws_skip_logout_redirect = true;
			wp_logout();
		}
	}

    $message_arr = $arr_messages[$reason] ?? $arr_messages['default'];
    $title = $arr_titles[$int_selected_language] ?? $arr_titles[0];
    $message = $message_arr[$int_selected_language] ?? $message_arr[0];

    $redirect_to = jws_get_request_uri_for_redirect_to();

    $str_html = '';
    $str_html .= '<section class="sectionStandard">';
    $str_html .= '<h2>' . escape_html($title) . '</h2>';
    $str_html .= '<p>' . escape_html($message) . '</p>';
    $str_html .= build_html_form_button_go_to_login($int_selected_language, $redirect_to, $reason);
    $str_html .= '</section>';

    return $str_html;
}


/**
 * ページ用：単一セッションを強制（失敗時は「ログインへ」HTMLを返す）
 * - 失敗時：HTML（メッセージ＋ログインへ）を返す
 * - 成功時：user_id(int) を返す
 */
function jws_require_single_session_for_page(int $int_selected_language) {

    global
        $t_user_membership;

    if (!is_user_logged_in()) {
        jws_clear_single_session_cookie();
        $_GET['reason'] = 'not_logged_in';
        return build_html_single_session_error_page($int_selected_language);
    }

    $user = wp_get_current_user();
    $user_id = (int)$user->ID;

    if ($user_id <= 0) {
        jws_clear_single_session_cookie();
        $_GET['reason'] = 'not_logged_in';
        return build_html_single_session_error_page($int_selected_language);
    }

    $client_token = $_COOKIE['wise_session'] ?? '';
    $client_token = is_string($client_token) ? $client_token : '';

    if ($client_token === '') {
        jws_clear_single_session_cookie();
        $_GET['reason'] = 'no_session';
        return build_html_single_session_error_page($int_selected_language);
    }

    $arr_strSQL_select = [
        [$t_user_membership, 'active_session_token']
    ];

    $strSQL_from = ' FROM ' . $t_user_membership;

    $arr_strSQL_where = [
        [
            [
                [$t_user_membership, 'user_id', '=', $user_id, 'PDO::PARAM_INT', '']
            ],
            ''
        ]
    ];

    $arr_strSQL_order = [];
    $strSQL_option = ' LIMIT 1';

    list($pdo_has_error, $select_has_error, $e, $arr_session) =
        execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            $strSQL_option
        );

    handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

    if (empty($arr_session)) {
        jws_clear_single_session_cookie();
        $_GET['reason'] = 'membership_missing';
        return build_html_single_session_error_page($int_selected_language);
    }

    $server_token = (string)($arr_session[0]['active_session_token'] ?? '');

    if ($server_token === '') {
        jws_clear_single_session_cookie();
        $_GET['reason'] = 'session_invalid';
        return build_html_single_session_error_page($int_selected_language);
    }

    if (!hash_equals($server_token, $client_token)) {
        jws_clear_single_session_cookie();
        $_GET['reason'] = 'session_conflict';
        return build_html_single_session_error_page($int_selected_language);
    }

    return $user_id;
}


/******************************************************
 *  MANAGE TARGETS
 *  
 ******************************************************/
function build_html_manage_targets($manage_target, $target_array, $target_unique_code, $target_title, $target_address, $target_placeholder, $int_selected_language, $options = []){

	global
		$path_manage_room_bookmarks,
		$path_manage_room_invite,
		$path_manage_room_requests,
		$path_check_wise_navigation_sequence,
		$str_update,
		$arr_str_button_caption_submit,
		$int_learning_status_not_started,
		$int_learning_status_learning,
		$int_learning_status_learned,
		$arr_learning_status,
		$t_masta_step_unit_type,
		$arr_columns_masta_step_unit_types,
		$t_masta_wise_navigation_script,
		$str_class_fixed_font;

    $str_html = '';
    $str_html_contents = '';
	
	$url_manage_room_bookmarks = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_manage_room_bookmarks, '/'))
	);

	$url_manage_room_invite = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_manage_room_invite, '/'))
	);

	$url_manage_room_requests = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_manage_room_requests, '/'))
	);

	$url_check_wise_navigation_sequence = get_home_url(
		get_data_blog_id_from_selected_language($int_selected_language ?? null),
		trailingslashit(ltrim($path_check_wise_navigation_sequence, '/'))
	);

    $prefix = isset($options['prefix']) ? $options['prefix'] : 'roomContents';
    $create_input_name = isset($options['create_input_name']) ? $options['create_input_name'] : 'roomName';
    $submit_button_id = isset($options['submit_button_id']) ? $options['submit_button_id'] : 'roomContentsCreateNewButton';
    $open_next_in_blank = isset($options['open_next_in_blank']) ? boolval($options['open_next_in_blank']) : ($manage_target === 'room');
    $bookmarks_address = isset($options['bookmarks_address']) ? $options['bookmarks_address'] : ($manage_target === 'room' ? $url_manage_room_bookmarks : '');
	$invite_address = isset($options['invite_address'])
		? $options['invite_address']
		: ($manage_target === 'room' ? $url_manage_room_invite : '');

	$requests_address = isset($options['requests_address'])
		? $options['requests_address']
		: ($manage_target === 'room' ? $url_manage_room_requests : '');

    $check_sequence_address = isset($options['check_sequence_address']) ? $options['check_sequence_address'] : ($manage_target === 'wise_navigation' ? $url_check_wise_navigation_sequence : '');
    $extra_edit_field_keys = isset($options['extra_edit_field_keys']) && is_array($options['extra_edit_field_keys']) ? $options['extra_edit_field_keys'] : [];
    $extra_edit_field_labels = isset($options['extra_edit_field_labels']) && is_array($options['extra_edit_field_labels']) ? $options['extra_edit_field_labels'] : [];
    $extra_edit_field_input = isset($options['extra_edit_field_input']) ? $options['extra_edit_field_input'] : 'textarea';
    $editable_fields = isset($options['editable_fields']) && is_array($options['editable_fields']) ? $options['editable_fields'] : [];

    $label_update = isset($str_update[$int_selected_language]) ? $str_update[$int_selected_language] : 'update';
    $label_submit = isset($arr_str_button_caption_submit[$int_selected_language]) ? $arr_str_button_caption_submit[$int_selected_language] : 'submit';

    $select_options_unit_type = [];
    $select_options_script_type = [];

    if (empty($editable_fields)) {
        if ($manage_target === 'room') {
            $editable_fields = [
                ['field' => 'room_name', 'label' => 'room_name', 'input' => 'input']
            ];
        } elseif ($manage_target === 'room_lesson' || $manage_target === 'lesson') {
            $editable_fields = [
                ['field' => 'title', 'label' => 'title', 'input' => 'input']
            ];
        } elseif ($manage_target === 'lesson_step') {
            $editable_fields = [
                ['field' => 'step_name', 'label' => 'step_name', 'input' => 'input']
            ];
        } elseif ($manage_target === 'lesson_step_unit') {
            $arr_strSQL_select = [
                [$t_masta_step_unit_type, 'id'],
                [$t_masta_step_unit_type, $arr_columns_masta_step_unit_types[$int_selected_language]]
            ];
            $strSQL_from = ' FROM ' . $t_masta_step_unit_type;
            $arr_strSQL_where = [];
            $arr_strSQL_order = [
                [$t_masta_step_unit_type, 'sort', 'ASC']
            ];
            $strSQL_option = '';
            list($pdo_has_error, $select_has_error, $e, $arr_masta_step_unit_types) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
            handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);
            foreach ($arr_masta_step_unit_types as $rec) {
                $select_options_unit_type[] = [
                    'value' => $rec['id'],
                    'text' => $rec[$arr_columns_masta_step_unit_types[$int_selected_language]]
                ];
            }
            $editable_fields = [
                ['field' => 'unit_type', 'label' => 'unit_type', 'input' => 'select', 'options' => $select_options_unit_type]
            ];
        } elseif ($manage_target === 'wise_navigation_script') {
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
            list($pdo_has_error, $select_has_error, $e, $arr_masta_scripts) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
            handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);
            foreach ($arr_masta_scripts as $rec) {
                $select_options_script_type[] = [
                    'value' => $rec['id'],
                    'text' => $rec['script_key']
                ];
            }
            $editable_fields = [
                ['field' => 'script_type_id', 'label' => 'script_type_id', 'input' => 'select', 'options' => $select_options_script_type]
            ];
        } elseif ($manage_target === 'wise_navigation') {
            $editable_fields = [
                ['field' => 'title', 'label' => 'title', 'input' => 'input']
            ];
        } elseif ($manage_target === 'wise_navigation_waypoint') {
            $editable_fields = [
                ['field' => 'title', 'label' => 'title', 'input' => 'input']
            ];
        }
    }

    $section_update_button = '
    <div class="'.$prefix.'SectionUpdateContainer">
        <button id="manageSectionUpdateAllButton" class="'.$prefix.'SectionUpdateAllButton" data-manage-target="'.escape_html($manage_target).'">'.escape_html($label_update).'</button>
    </div>';

    foreach ($target_array as $key => $target) {
        $str_html_contents_to_next = '';
        $str_html_contents_to_bookmarks = '';
		$str_html_contents_to_invite = '';
		$str_html_contents_to_requests = '';
        $str_html_contents_to_check_sequence = '';

        if ($open_next_in_blank) {
            $str_html_contents_to_next = '
            <form action="'.escape_html($target_address).'" method="GET" target="_blank" rel="noopener">
                <div class="">
                    <input class="" type="submit" value="'.escape_html($label_submit).'">
                    <input type="hidden" name="unique_code" value="'.escape_html($target[$target_unique_code]).'">
                </div>
            </form>';
        } else {
            $str_html_contents_to_next = '
            <form action="'.escape_html($target_address).'" method="GET">
                <div class="">
                    <input class="" type="submit" value="'.escape_html($label_submit).'">
                    <input type="hidden" name="unique_code" value="'.escape_html($target[$target_unique_code]).'">
                </div>
            </form>';
        }

        if (!empty($bookmarks_address)) {
            $str_html_contents_to_bookmarks = '
            <form action="'.escape_html($bookmarks_address).'" method="GET" target="_blank" rel="noopener">
                <div class="">
                    <input class="" type="submit" value="bookmarks">
                    <input type="hidden" name="unique_code" value="'.escape_html($target[$target_unique_code]).'">
                </div>
            </form>';
        }

		if (!empty($invite_address)) {
			$str_html_contents_to_invite = '
			<form action="'.escape_html($invite_address).'" method="GET" target="_blank" rel="noopener">
				<div class="">
					<input class="" type="submit" value="invite">
					<input type="hidden" name="unique_code" value="'.escape_html($target[$target_unique_code]).'">
				</div>
			</form>';
		}

		if (!empty($requests_address)) {
			$str_html_contents_to_requests = '
			<form action="'.escape_html($requests_address).'" method="GET" target="_blank" rel="noopener">
				<div class="">
					<input class="" type="submit" value="requests">
					<input type="hidden" name="unique_code" value="'.escape_html($target[$target_unique_code]).'">
				</div>
			</form>';
		}


        if (!empty($check_sequence_address)) {
            $str_html_contents_to_check_sequence = '
            <form action="'.escape_html($check_sequence_address).'" method="GET" target="_blank" rel="noopener">
                <div class="">
                    <input class="" type="submit" value="sequence">
                    <input type="hidden" name="unique_code" value="'.escape_html($target[$target_unique_code]).'">
                </div>
            </form>';
        }

        $str_html_contents_delete = '<button class="'.$prefix.'DeleteButton" data-id="'.escape_html($target['id']).'">del</button>';

        $str_html_contents_sort = '
        <div class="'.$prefix.'EditContentsSortButtonsContainer">
            <button class="'.$prefix.'EditContentsSortPreviousButton '.$prefix.'EditContentsSortButton" data-id="'.escape_html($target['id']).'">△</button>
            <button class="'.$prefix.'EditContentsSortNextButton '.$prefix.'EditContentsSortButton" data-id="'.escape_html($target['id']).'">▽</button>
        </div>';

        $str_html_contents_radio = '';
		if (isset($target['learning_status'])) {

			$int_current_learning_status = intval($target['learning_status']);

			$str_html_contents_radio = '<div class="editContentsRadioContainer">';

			foreach ($arr_learning_status as $int_learning_status => $arr_status) {

				$str_checked = ($int_current_learning_status === intval($int_learning_status)) ? 'checked' : '';

				$str_id = $arr_status['html_id_class'] . $key;
				$str_class = $arr_status['html_id_class'];
				$str_name = 'learningStatus' . $key;

				$str_label = $arr_status['title'][$int_selected_language] ?? $arr_status['title'][0];

				$str_html_contents_radio .= '
					<input type="radio"
						id="' . $str_id . '"
						class="learningStatusRadioButton ' . $str_class . '"
						name="' . $str_name . '"
						value="' . intval($int_learning_status) . '"
						data-unique-code="' . escape_html($target[$target_unique_code]) . '"
						' . $str_checked . '>
					<label for="' . $str_id . '">' . escape_html($str_label) . '</label>
				';
			}

			$str_html_contents_radio .= '</div>';
		}

        $str_html_contents_publish = '';
        if (isset($target['is_published'])) {
            $checkedPublished = intval($target['is_published']) === 1 ? 'checked' : '';
            $str_html_contents_publish = '
            <div class="editContentsCheckboxContainer">
                <input type="checkbox" id="isPublished'.$key.'" class="'.$prefix.'IsPublishedCheckbox editableElement" name="isPublished'.$key.'" value="1"
                    data-unique-code="'.escape_html($target[$target_unique_code]).'"
                    data-id="'.escape_html($target['id']).'"
                    data-column="is_published"
                    data-original="'.(intval($target['is_published']) === 1 ? '1' : '0').'"
                    '.($checkedPublished).'>
                <label for="isPublished'.$key.'">published</label>
            </div>';
        }

        $str_main_edit_fields = '';
        if (!empty($editable_fields)) {
            foreach ($editable_fields as $ef_idx => $ef) {
                $field = isset($ef['field']) ? $ef['field'] : '';
                if ($field === '') continue;
                $label_text = isset($ef['label']) ? $ef['label'] : $field;
                $input_type = isset($ef['input']) ? $ef['input'] : 'input';
                $options_arr = isset($ef['options']) && is_array($ef['options']) ? $ef['options'] : [];
                $raw_value = isset($target[$field]) ? $target[$field] : '';
                $value_attr = escape_html($raw_value);
                $value_text = escape_html($raw_value);
                $input_id = $prefix.'MainField'.$key.'_'.$ef_idx;

                if ($input_type === 'select') {
                    $options_html = '';
                    foreach ($options_arr as $opt) {
                        $opt_val = escape_html(isset($opt['value']) ? $opt['value'] : '');
                        $opt_text = escape_html(isset($opt['text']) ? $opt['text'] : $opt_val);
                        $selected = ((string)$opt_val === (string)$raw_value) ? ' selected' : '';
                        $options_html .= '<option value="'.$opt_val.'"'.$selected.'>'.$opt_text.'</option>';
                    }
                    $str_main_edit_fields .= '
                    <label for="'.$input_id.'">'.escape_html($label_text).'</label>
                    <select id="'.$input_id.'" class="'.$prefix.'MainEditInput editableElement"
                        data-unique-code="'.escape_html($target[$target_unique_code]).'"
                        data-id="'.escape_html($target['id']).'"
                        data-column="'.escape_html($field).'"
                        data-original="'.$value_attr.'">'.$options_html.'</select>';
                } elseif ($input_type === 'textarea') {
                    $str_main_edit_fields .= '
                    <label for="'.$input_id.'">'.escape_html($label_text).'</label>
                    <textarea id="'.$input_id.'" class="'.$prefix.'MainEditTextarea editableElement '.$str_class_fixed_font.'"
                        data-unique-code="'.escape_html($target[$target_unique_code]).'"
                        data-id="'.escape_html($target['id']).'"
                        data-column="'.escape_html($field).'"
                        data-original="'.$value_text.'" rows="4" cols="40">'.$value_text.'</textarea>';
                } else {
                    $str_main_edit_fields .= '
                    <label for="'.$input_id.'">'.escape_html($label_text).'</label>
                    <input type="text" id="'.$input_id.'" class="'.$prefix.'MainEditInput editableElement"
                        data-unique-code="'.escape_html($target[$target_unique_code]).'"
                        data-id="'.escape_html($target['id']).'"
                        data-column="'.escape_html($field).'"
                        data-original="'.$value_attr.'" value="'.$value_attr.'">';
                }
            }

            $str_script_title = '';
            if ($manage_target === 'wise_navigation_script') {
                $script_uc = isset($target[$target_unique_code]) ? strval($target[$target_unique_code]) : '';
                if ($script_uc !== '') {
                    $str_script_title = get_str_wise_navigation_script_edit_title($script_uc, $int_selected_language);
                }
            }

            $main_container_classes = $prefix.'MainEditContainer'.($manage_target === 'wise_navigation_script' ? ' naviContentsMainEditContainer' : '');
            $main_inner_html = '';
            if ($manage_target === 'wise_navigation_script' && $str_script_title !== '') {
                $main_inner_html .= '<h5 class="editContentsTitle">'.$str_script_title.'</h5>';
            }
            $main_inner_html .= '<div class="mainEditFields '.$prefix.'MainEditFields">'.$str_main_edit_fields.'</div>';

            $str_main_edit_fields = '
            <div class="mainEditContainer '.$main_container_classes.'" data-unique-code="'.escape_html($target[$target_unique_code]).'">
                '.$main_inner_html.'
            </div>';
        }

        $str_html_contents_extras = '';
        if (!empty($extra_edit_field_keys)) {
            $labels = $extra_edit_field_labels;
            if (empty($labels) || count($labels) !== count($extra_edit_field_keys)) {
                $labels = $extra_edit_field_keys;
            }

            $str_fields = '';
            foreach ($extra_edit_field_keys as $idx => $col_name) {
                $label_text = escape_html($labels[$idx]);
                $raw_value = isset($target[$col_name]) ? $target[$col_name] : '';
                $value_attr = escape_html($raw_value);
                $value_text = escape_html($raw_value);
                $input_id = $prefix.'ExtraField'.$key.'_'.$idx;

                if ($extra_edit_field_input === 'input') {
                    $str_fields .= '
                    <label for="'.$input_id.'">'.$label_text.'</label>
                    <input type="text" id="'.$input_id.'" class="'.$prefix.'ExtraInput editableElement"
                        data-unique-code="'.escape_html($target[$target_unique_code]).'"
                        data-id="'.escape_html($target['id']).'"
                        data-column="'.escape_html($col_name).'"
                        data-original="'.$value_attr.'" value="'.$value_attr.'">';
                } else {
                    $str_fields .= '
                    <label for="'.$input_id.'">'.$label_text.'</label>
                    <textarea id="'.$input_id.'" class="'.$prefix.'ExtraTextarea editableElement '.$str_class_fixed_font.'"
                        data-unique-code="'.escape_html($target[$target_unique_code]).'"
                        data-id="'.escape_html($target['id']).'"
                        data-column="'.escape_html($col_name).'"
                        data-original="'.$value_text.'" rows="4" cols="40">'.$value_text.'</textarea>';
                }
            }

            $extra_container_id = $prefix.'ExtraContainer'.$key;
            $hidden_class = ($manage_target === 'wise_navigation_script') ? ' hidden' : '';

            $toggle_button_html = '';
            if ($manage_target === 'wise_navigation_script') {
                $toggle_button_html = '
                <div class="'.$prefix.'ExtraToggleContainer">
                    <button type="button" class="'.$prefix.'ExtraToggleButton" data-target="'.$extra_container_id.'">show</button>
                </div>';
            }

            $str_html_contents_extras = '
            <div class="'.$prefix.'ExtraWrapper" data-unique-code="'.escape_html($target[$target_unique_code]).'">
                '.$toggle_button_html.'
                <div id="'.$extra_container_id.'" class="'.$prefix.'ExtraContainer'.$hidden_class.'" data-unique-code="'.escape_html($target[$target_unique_code]).'">
                    <div class="'.$prefix.'ExtraFieldsContainer">'.$str_fields.'</div>
                </div>
            </div>';
        }

        $str_html_contents_add = '
            <div class="editContentsContainer" data-unique-code="'.escape_html($target[$target_unique_code]).'">'.
                $str_main_edit_fields.
                $str_html_contents_to_next.
				$str_html_contents_to_invite.
				$str_html_contents_to_requests.
                // $str_html_contents_to_bookmarks.
                $str_html_contents_to_check_sequence.
                $str_html_contents_delete.
                $str_html_contents_sort.
                $str_html_contents_radio.
                $str_html_contents_publish.
                $str_html_contents_extras.'
            </div>';

        $str_html_contents .= $str_html_contents_add;
    }

    $str_html_contents = '<div class="editSectionContainer">'.$section_update_button.$str_html_contents.'</div>';

    $str_html_edit_section = '<h2>Edit</h2>'.$str_html_contents;
	$str_html_edit_section = '<section class="sectionStandard">' . $str_html_edit_section . '</section>';

    $str_html_create_explanation = '<p>'.escape_html_with_nl2br($target_placeholder).'</p>';

    if ($manage_target === 'lesson_step_unit') {
        $arr_strSQL_select = [
            [$t_masta_step_unit_type, 'id'],
            [$t_masta_step_unit_type, $arr_columns_masta_step_unit_types[$int_selected_language]]
        ];
        $strSQL_from = ' FROM ' . $t_masta_step_unit_type;
        $arr_strSQL_where = [];
        $arr_strSQL_order = [
            [$t_masta_step_unit_type, 'sort', 'ASC']
        ];
        $strSQL_option = '';
        list($pdo_has_error, $select_has_error, $e, $arr_masta_step_unit_types) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
        handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);
        $str_add_divstbox = '';
        foreach ($arr_masta_step_unit_types as $key => $loop_masta_step_unit_types) {
            $int_option_value = escape_html($loop_masta_step_unit_types['id']);
            $str_option_text_content = escape_html($loop_masta_step_unit_types[$arr_columns_masta_step_unit_types[$int_selected_language]]);
            $str_add_divstbox .= '<option value="'.$int_option_value.'">'.$str_option_text_content.'</option>';
        }
        $str_html_create_new_inputbox = '
        <div id="divInputBox">
            <select class="'.$prefix.'CreateNewData" name="'.$create_input_name.'">'.$str_add_divstbox.'</select>
        </div>
        <button id="'.$submit_button_id.'">'.escape_html($label_submit).'</button>';
    } elseif ($manage_target === 'wise_navigation_script') {
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
        list($pdo_has_error, $select_has_error, $e, $arr_masta_scripts) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
        handle_database_error_and_redirect($pdo_has_error, $select_has_error, $e, $int_selected_language);
        $str_add_divstbox = '';
        foreach ($arr_masta_scripts as $key => $loop_script) {
            $int_option_value = escape_html($loop_script['id']);
            $str_option_text_content = escape_html($loop_script['script_key']);
            $str_add_divstbox .= '<option value="'.$int_option_value.'">'.$str_option_text_content.'</option>';
        }
        $str_html_create_new_inputbox = '
        <div id="divInputBox">
            <select class="'.$prefix.'CreateNewData" name="'.$create_input_name.'">'.$str_add_divstbox.'</select>
        </div>
        <button id="'.$submit_button_id.'">'.escape_html($label_submit).'</button>';
    } else {
        $str_html_create_new_inputbox = '
        <div class="divInputBox">
            <textarea class="'.$prefix.'CreateNewData '.$str_class_fixed_font.'" name="'.$create_input_name.'" rows="10" cols="40"></textarea>
        </div>
        <button id="'.$submit_button_id.'">'.escape_html($label_submit).'</button>';
    }

    $str_html_create_new = '<div class="createNewSectionContainer">'.$str_html_create_explanation.$str_html_create_new_inputbox.'</div>';

    $str_html_create_section = '<h2>Create New</h2>'.$str_html_create_new;
	$str_html_create_section = '<section class="sectionStandard">' . $str_html_create_section . '</section>';

    $str_html = $str_html_edit_section.$str_html_create_section;

    $str_html .= '<script>(function(){if(typeof window._manageTargetsExtrasToggleInit==="undefined"){window._manageTargetsExtrasToggleInit=true;document.addEventListener("click",function(e){var btn=e.target.closest("button[data-target]");if(!btn)return;if(btn.className&&btn.className.indexOf("ExtraToggleButton")===-1)return;var id=btn.getAttribute("data-target");if(!id)return;var box=document.getElementById(id);if(!box)return;var isHidden=box.classList.contains("hidden");if(isHidden){box.classList.remove("hidden");btn.textContent="hide";}else{box.classList.add("hidden");btn.textContent="show";}});}})();</script>';

    return $str_html;
}



/******************************************************
 *  SHOW PAGES
 *  
 ******************************************************/

function get_data_page_shortcodes($post_id) {
    $content = get_post_field('post_content', $post_id);

    if ($content === null || $content === '') {
        return '';
    }

    $pattern = get_shortcode_regex();
    $shortcodes = array();

    if (preg_match_all('/' . $pattern . '/s', $content, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $shortcode_match) {
            $shortcodes[] = $shortcode_match[2];
        }
    }

    if (empty($shortcodes)) {
        return '';
    }

    $shortcodes = array_unique($shortcodes);

    return implode(', ', $shortcodes);
}


function generate_html_pages_table_rows_recursive($parent_id, $depth) {
    $children = get_pages(array(
        'parent' => $parent_id,
        'sort_column' => 'menu_order',
        'post_status' => 'publish'
    ));

    if (empty($children)) {
        return '';
    }

    $rows = '';

    foreach ($children as $child) {
        $hide_flag = get_post_meta($child->ID, 'hide_from_children_links', true);
        if ($hide_flag === '1') {
            continue;
        }

        $indent = str_repeat('— ', $depth);
        $shortcodes = get_data_page_shortcodes($child->ID);

        $rows .= '<tr>'
            . '<td>' . intval($child->ID) . '</td>'
            . '<td>' . esc_html($indent . $child->post_title) . '</td>'
            . '<td>' . intval($child->post_parent) . '</td>'
            . '<td>' . esc_html(get_permalink($child->ID)) . '</td>'
            . '<td>' . esc_html($shortcodes) . '</td>'
            . '</tr>';

        $rows .= generate_html_pages_table_rows_recursive($child->ID, $depth + 1);
    }

    return $rows;
}


function build_html_pages_table_for_blog($blog_id) {
    switch_to_blog($blog_id);

    $rows = generate_html_pages_table_rows_recursive(0, 0);

    restore_current_blog();

    if ($rows === '') {
        return '';
    }

    $html = '<table class="debugPagesTable">'
        . '<thead><tr>'
        . '<th>ID</th>'
        . '<th>Title</th>'
        . '<th>Parent ID</th>'
        . '<th>URL</th>'
        . '<th>Shortcodes</th>'
        . '</tr></thead>'
        . '<tbody>'
        . $rows
        . '</tbody></table>';

    return $html;
}


function count_page_depth($post_id) {
    $ancestors = get_post_ancestors($post_id);

    if (!is_array($ancestors)) {
        return 0;
    }

    return count($ancestors);
}


function generate_html_pages_tree_recursive($parent_id = 0) {
    $children = get_pages(array(
        'parent' => $parent_id,
        'sort_column' => 'menu_order',
        'post_status' => 'publish'
    ));

    if (empty($children)) {
        return '';
    }

    $str = '<ul class="childrenLinksTreeUl">';

    foreach ($children as $child) {
        $hide_flag = get_post_meta($child->ID, 'hide_from_children_links', true);
        if ($hide_flag === '1') {
            continue;
        }

		$depth = count_page_depth($child->ID);
        $indent = str_repeat('— ', $depth);

        $str .= '<li class="childrenLinksTreeLi">'
            . esc_html($indent . $child->post_title);
        // $str .= '<li class="childrenLinksTreeLi">'
        //     . esc_html($indent . $child->post_title)
        //     . ' <span class="pageIdLabel">(ID: ' . intval($child->ID) . ')</span>';

        $str .= generate_html_pages_tree_recursive($child->ID);

        $str .= '</li>';
    }

    $str .= '</ul>';

    return $str;
}


function build_html_pages_tree_for_blog($blog_id) {
    switch_to_blog($blog_id);

    $html = generate_html_pages_tree_recursive(0);

    restore_current_blog();

    return $html;
}


function build_html_multisite_pages_for_debug() {
    $sites = get_sites(array(
        'public' => 1,
        'archived' => 0,
        'deleted' => 0
    ));

    if (empty($sites)) {
        return '';
    }

    $html = '<div class="debugMultisitePageTree">';

    foreach ($sites as $site) {
        $blog_id = (int)$site->blog_id;
        $details = get_blog_details($blog_id);
        $site_title = $details ? $details->blogname : 'Blog ID ' . $blog_id;

        $html .= '<section class="debugSiteBlock">';
        $html .= '<h2 class="debugSiteTitle">'
            . esc_html($site_title)
            . ' (Blog ID: ' . $blog_id . ')</h2>';

        $html .= build_html_pages_tree_for_blog($blog_id);
        $html .= build_html_pages_table_for_blog($blog_id);

        $html .= '</section>';
    }

    $html .= '</div>';

    return $html;
}



/******************************************************
 *  DEBUG
 *  
 ******************************************************/

function debug($name, $value) {
	echo "<table>";
	foreach($value as $k => $v) {
		echo "<tr><td>{$k}</td><td>";
		if(is_array($v)) {
			debug($k, $v);
		} else {
			echo $v;
		}
		echo "</td></tr>";
	}
	echo "</table>";
}

function debug_japanese_root($array){

	global
		$t_masta_japanese_root,
		$arr_columns_masta_japanese_root,
		$int_used_language_jpn;

	$int_selected_language = INDEX_FIRST;

	foreach($array as $loop){

		$arr_strSQL_select = [
			[$t_masta_japanese_root,'id'],
			[$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_used_language_jpn]]
		];

		$strSQL_from = ' FROM ' .$t_masta_japanese_root;

		$arr_strSQL_where = [
			[
				[
					[$t_masta_japanese_root,'id','=',$loop,'PDO::PARAM_INT','']
				],
				''
			]
		];

		$arr_strSQL_order = [];

		$strSQL_option = '';

		list($pdo_has_error, $select_has_error, $e, $arr_masta_japanese_root) = execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
		handle_database_error_and_exit($pdo_has_error, $select_has_error, $e);

		echo 'id:'.$arr_masta_japanese_root[INDEX_FIRST]['id'].' / japanese:'.$arr_masta_japanese_root[INDEX_FIRST][$arr_columns_masta_japanese_root[$int_used_language_jpn]];
		echo '<br>';
	}
}

// sqlを書き出し
// $logFile = __DIR__ . "/sql_debug.log"; 
// file_put_contents($logFile, date("[Y-m-d H:i:s]") . " SQL: " . $strSQL . PHP_EOL, FILE_APPEND);

// エラーを書き出し
// $logFile = __DIR__ . '/database_error.log';
// $log = [];
// $log[] = date('[Y-m-d H:i:s]');
// $log[] = 'PDO_ERROR=' . (int)$pdo_has_error;
// $log[] = 'QUERY_ERROR=' . (int)$query_has_error;
// if ($strSQL !== null) {
// 	$log[] = 'SQL=' . $strSQL;
// }
// if ($arr_bind_values !== null) {
// 	$log[] = 'BIND_VALUES=' . json_encode(
// 		$arr_bind_values,
// 		JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
// 	);
// }
// if ($e instanceof Throwable) {
// 	$log[] = 'EXCEPTION=' . $e->getMessage();
// 	$log[] = 'FILE=' . $e->getFile();
// 	$log[] = 'LINE=' . $e->getLine();
// }
// $log[] = str_repeat('-', 80);
// file_put_contents(
// 	$logFile,
// 	implode(' | ', $log) . PHP_EOL,
// 	FILE_APPEND | LOCK_EX
// );


// sampleページのみ処理
// $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
// if ($requestUri === '/sample/' || $requestUri === 'cht/sample/') {
// }


// $start_time = microtime(true);
// $end_time = microtime(true);
// print_r( '処理時間 = ' . ($end_time - $start_time) . '秒' );
