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

        if (!isset($input['currentUrl'])) {
            respond_error('Value not found: currentUrl', 400);
        }
        if (!isset($input['id'])) {
            respond_error('Value not found: id', 400);
        }
        if (!isset($input['unique_code'])) {
            respond_error('Value not found: unique_code', 400);
        }
        if (!isset($input['int_selected_language'])) {
            respond_error('Value not found: int_selected_language', 400);
        }

        $currentUrl = escape_html((string)$input['currentUrl']);
        $id = intval($input['id']);
        $unique_code = escape_html((string)$input['unique_code']);
        $int_selected_language = intval($input['int_selected_language']);

        if ($currentUrl === '') {
            respond_error('Invalid value: currentUrl', 400);
        }
        if ($id <= 0) {
            respond_error('Invalid value: id', 400);
        }
        if ($unique_code === '') {
            respond_error('Invalid value: unique_code', 400);
        }
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        $pdo = connect_to_database();
        if (!($pdo instanceof PDO)) {
            respond_error('Database Error', 500);
        }

        $pdo->beginTransaction();

        delete_targets_for_manage_page($pdo, $currentUrl, $id, $unique_code, $int_selected_language);

        $pdo->commit();

        respond_success(['int_selected_language' => $int_selected_language]);

    } catch (Throwable $e) {

        if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
            $pdo->rollBack();
        }

        respond_exception($e, 'delete_targets_for_manage_page_unhandled');
    }

