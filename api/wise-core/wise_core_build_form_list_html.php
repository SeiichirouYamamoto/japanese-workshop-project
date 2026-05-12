<?php

    header('Content-Type: application/json; charset=utf-8');

    require_once __DIR__ . '/../_bootstrap.php';

    try {

        $user_level = get_user_level();
	
		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		$user_id = jws_require_single_session();

		if (!is_teacher_level($user_level)) {
			respond_error('Forbidden', 403);
		}

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);

        if (!is_array($input)) {
            respond_error('Invalid JSON', 400);
        }

        if (!isset($input['int_selected_language'])) {
            respond_error('Value not found: int_selected_language', 400);
        }

        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        $arr_already_learned_list = [];

        if (isset($_SESSION['arr_already_learned_list'])) {
            $arr_already_learned_list = $_SESSION['arr_already_learned_list'];
        }

        $arr_form_list = fetch_arr_form_root_list($arr_already_learned_list, $int_selected_language);

        $str_form_list = '';

        foreach ($arr_form_list as $loop_form_list) {
            $word = $loop_form_list[$arr_columns_masta_japanese_root[$int_selected_language]] ?? '';
            $t_masta_form_root_id = intval($loop_form_list['id'] ?? 0);
            $str_form_list .= '<li class="wisePanelWhiteboardUiFormListLi wiseUiFontSizeTarget" data-form-id="' . $t_masta_form_root_id . '">' . $word . '</li>';
        }

        $str_form_list = '<li class="wisePanelWhiteboardUiFormListLi wiseUiFontSizeTarget" data-form-id="' . USE_DEFAULT . '">Default</li>' . $str_form_list;

        respond_success(['html' => $str_form_list]);

    } catch (Throwable $e) {
        respond_exception($e, 'form_list_unhandled');
    }

