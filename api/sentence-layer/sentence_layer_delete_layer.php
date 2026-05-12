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

        if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }
        if (!isset($input['int_layer_id'])) { respond_error('Value not found: int_layer_id', 400); }

        $int_selected_language = (int)$input['int_selected_language'];
        $int_layer_id = (int)$input['int_layer_id'];

        if ($int_layer_id <= 0) {
            respond_error('invalid int_layer_id', 400);
        }

        $pdo = connect_to_database();
        if (!($pdo instanceof PDO)) {
            respond_error('Database connection failed', 500);
        }

        $pdo->beginTransaction();

        $sql_delete_overrides =
            'DELETE o
            FROM ' . $t_layer_element_overrides . ' o
            INNER JOIN ' . $t_layer_elements . ' e
                ON o.layer_element_id = e.id
            WHERE e.layer_id = ?';

        $stmt_delete_overrides = $pdo->prepare($sql_delete_overrides);
        $stmt_delete_overrides->bindValue(1, $int_layer_id, PDO::PARAM_INT);
        $stmt_delete_overrides->execute();

        $sql_delete_elements = 'DELETE FROM ' . $t_layer_elements . ' WHERE layer_id = ?';
        $stmt_delete_elements = $pdo->prepare($sql_delete_elements);
        $stmt_delete_elements->bindValue(1, $int_layer_id, PDO::PARAM_INT);
        $stmt_delete_elements->execute();

        $sql_delete_layer = 'DELETE FROM ' . $t_layers . ' WHERE id = ?';
        $stmt_delete_layer = $pdo->prepare($sql_delete_layer);
        $stmt_delete_layer->bindValue(1, $int_layer_id, PDO::PARAM_INT);
        $stmt_delete_layer->execute();

        $pdo->commit();
        $pdo = null;

        respond_success(['success' => true]);

    } catch (Throwable $e) {

        if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
            $pdo->rollBack();
        }

        respond_exception($e);
    }

