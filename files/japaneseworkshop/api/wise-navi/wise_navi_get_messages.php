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

        if (!isset($input['int_selected_language'])) {
            respond_error('Value not found: int_selected_language', 400);
        }

        if (!isset($input['message_type'])) {
            respond_error('Value not found: message_type', 400);
        }

        $int_selected_language = intval($input['int_selected_language']);
        $message_type = (string)$input['message_type'];

        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        if ($message_type === '') {
            respond_error('Invalid value: message_type', 400);
        }

        if (!isset($arr_wise_navigation_messages[$int_selected_language][$message_type])) {
            respond_error('Value not found: messages', 404);
        }

        $messages = $arr_wise_navigation_messages[$int_selected_language][$message_type];

        respond_success([
            'messages' => $messages
        ]);

    } catch (Throwable $e) {
        respond_exception($e, 'wise_navigation_messages_unhandled');
    }

