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

		if (!isset($input['arr_selected'])) { respond_error('Value not found: arr_selected', 400); }
		if (!isset($input['int_layer_id'])) { respond_error('Value not found: int_layer_id', 400); }
		if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }

		$arr_selected = $input['arr_selected'];
		$int_layer_id = intval($input['int_layer_id']);
		$int_selected_language = intval($input['int_selected_language']);

		$pdo = connect_to_database();
		if (empty($pdo)) {
			respond_error('Database connection failed', 500);
		}

		$pdo->beginTransaction();

		$sql = 'SELECT id, registered_sentence_element_id AS sentenceElementId FROM ' . $t_layer_elements . ' WHERE layer_id = ?';
		$stmt = $pdo->prepare($sql);
		$stmt->bindValue(1, $int_layer_id, PDO::PARAM_INT);
		$stmt->execute();
		$arr_layer_elements = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$elmIdMapping = empty($arr_layer_elements) ? [] : array_column($arr_layer_elements, null, 'sentenceElementId');

		$stmt_delete_override = $pdo->prepare('DELETE FROM ' . $t_layer_element_overrides . ' WHERE layer_element_id = ?');
		$stmt_delete_element = $pdo->prepare('DELETE FROM ' . $t_layer_elements . ' WHERE id = ?');
		$stmt_insert_element = $pdo->prepare(
			'INSERT INTO ' . $t_layer_elements . ' (unique_code, layer_id, registered_sentence_element_id, form_id, voice_id, is_highlighted)
			VALUES (?, ?, ?, 0, 0, ?)'
		);

		foreach ($arr_selected as $loop_selected) {
			$currentSentenceElementId = intval($loop_selected['sentenceElementId'] ?? 0);
			$currentSelected = intval($loop_selected['selected'] ?? 0);

			if ($currentSentenceElementId <= 0) {
				throw new Exception('Invalid sentenceElementId');
			}

			if (isset($elmIdMapping[$currentSentenceElementId])) {
				$t_layer_elements_id = intval($elmIdMapping[$currentSentenceElementId]['id']);

				if ($currentSelected === FLAG_FALSE) {
					$stmt_delete_override->bindValue(1, $t_layer_elements_id, PDO::PARAM_INT);
					$stmt_delete_override->execute();

					$stmt_delete_element->bindValue(1, $t_layer_elements_id, PDO::PARAM_INT);
					$stmt_delete_element->execute();
				}

			} else {
				if ($currentSelected === FLAG_TRUE) {
					$generated = generate_unique_code($t_layer_elements, 'unique_code', 'id', $int_selected_language);
					if ($generated === null) {
						throw new Exception('Failed to generate unique code');
					}

					$stmt_insert_element->bindValue(1, $generated, PDO::PARAM_STR);
					$stmt_insert_element->bindValue(2, $int_layer_id, PDO::PARAM_INT);
					$stmt_insert_element->bindValue(3, $currentSentenceElementId, PDO::PARAM_INT);
					$stmt_insert_element->bindValue(4, FLAG_FALSE, PDO::PARAM_INT);
					$stmt_insert_element->execute();
				}
			}
		}

		$pdo->commit();
		$pdo = null;

		respond_success(['success' => true]);

	} catch (Throwable $e) {

		if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
			$pdo->rollBack();
		}

		respond_exception($e);
	}

