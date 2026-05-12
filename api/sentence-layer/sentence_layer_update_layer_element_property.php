<?php

    header('Content-Type: application/json; charset=utf-8');

    require_once __DIR__ . '/../_bootstrap.php';

    try {

		$user_level = get_user_level();
	
		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		$user_id = jws_require_single_session();

		if (!is_admin_level($user_level)) {
			respond_error('Forbidden', 403);
		}

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);

        if (!is_array($input)) {
            respond_error('Invalid JSON', 400);
        }

        if (!isset($input['int_layer_element_id'])) {
            respond_error('Value not found: int_layer_element_id', 400);
        }
        if (!isset($input['target'])) {
            respond_error('Value not found: target', 400);
        }

        $int_layer_element_id = intval($input['int_layer_element_id']);
        if ($int_layer_element_id <= 0) {
            respond_error('Invalid value: int_layer_element_id', 400);
        }

        $target = trim((string)($input['target'] ?? ''));
        if ($target === '') {
            respond_error('Invalid value: target', 400);
        }

        $update_table = $t_layer_elements;
        $arr_updateSQL = [];
        $updated_value = null;

        switch ($target) {

            case 'highlight':
                if (!isset($input['is_highlighted_new'])) {
                    respond_error('Value not found: is_highlighted_new', 400);
                }
                $is_highlighted_new = intval($input['is_highlighted_new']);
                $is_highlighted_new = ($is_highlighted_new === FLAG_TRUE) ? FLAG_TRUE : FLAG_FALSE;

                $arr_updateSQL[] = ['is_highlighted', ':update_is_highlighted', $is_highlighted_new, 'PDO::PARAM_INT'];
                $updated_value = $is_highlighted_new;
                break;

            case $str_snake_to_camel_form_id:
                if (!isset($input[$str_snake_to_camel_form_id])) {
                    respond_error('Value not found: formId', 400);
                }
                $formId = intval($input[$str_snake_to_camel_form_id]);
                if ($formId < 0) {
                    respond_error('Invalid value: formId', 400);
                }

                $arr_updateSQL[] = ['form_id', ':update_form_id', $formId, 'PDO::PARAM_INT'];
                $updated_value = $formId;
                break;

            case $str_snake_to_camel_voice_id:
                if (!isset($input[$str_snake_to_camel_voice_id])) {
                    respond_error('Value not found: voiceId', 400);
                }
                $voiceId = intval($input[$str_snake_to_camel_voice_id]);
                if ($voiceId < 0) {
                    respond_error('Invalid value: voiceId', 400);
                }

                $arr_updateSQL[] = ['voice_id', ':update_voice_id', $voiceId, 'PDO::PARAM_INT'];
                $updated_value = $voiceId;
                break;

            default:
                respond_error('Invalid value: target', 400);
        }

        $arr_whereSQL = [
            ['id', ':where_id', $int_layer_element_id, 'PDO::PARAM_INT', '']
        ];

        list($pdo_has_error, $update_has_error, $e) = execute_update_data($update_table, $arr_updateSQL, $arr_whereSQL);
        handle_database_error_and_respond($pdo_has_error, $update_has_error, $e);

        respond_success(['success' => true]);

    } catch (Throwable $e) {
        respond_exception($e, 'layer_element_update_unhandled');
    }

