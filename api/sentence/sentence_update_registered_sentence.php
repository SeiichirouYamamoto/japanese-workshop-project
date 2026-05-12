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

        if (!isset($input['send_sentence_unique_code'])) { respond_error('Value not found: send_sentence_unique_code', 400); }
        if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }
        if (!isset($input['arr_link_id_add_sort'])) { respond_error('Value not found: arr_link_id_add_sort', 400); }

        $unique_code = trim((string)($input['send_sentence_unique_code'] ?? ''));
        if ($unique_code === '') {
            respond_error('Invalid value: send_sentence_unique_code', 400);
        }
        $unique_code = escape_html($unique_code);

        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        $arr_link_id_add_sort = is_array($input['arr_link_id_add_sort'])
            ? $input['arr_link_id_add_sort']
            : [];

        $str_group_of_words_japanese = escape_html($input['str_group_of_words_japanese'] ?? '');
        $str_group_of_words_kana = escape_html($input['str_group_of_words_kana'] ?? '');

        $t_registered_sentence_id = fetch_registered_sentence_id_from_unique_code($unique_code, $int_selected_language);
        $t_registered_sentence_id = intval($t_registered_sentence_id);

        if ($t_registered_sentence_id <= 0) {
            respond_error('The record does not exist', 400);
        }

        $pdo = connect_to_database();
        if (!($pdo instanceof PDO)) {
            respond_error('Database connection failed', 500);
        }

        $pdo->beginTransaction();

        $sql_update_sentence =
            'UPDATE ' . $t_registered_sentences . '
             SET sentence = ?, sentence_kana = ?
             WHERE id = ?';

        $stmt_update_sentence = $pdo->prepare($sql_update_sentence);
        $stmt_update_sentence->bindValue(1, $str_group_of_words_japanese, PDO::PARAM_STR);
        $stmt_update_sentence->bindValue(2, $str_group_of_words_kana, PDO::PARAM_STR);
        $stmt_update_sentence->bindValue(3, $t_registered_sentence_id, PDO::PARAM_INT);
        $stmt_update_sentence->execute();

        $sql_delete_elements =
            'DELETE FROM ' . $t_registered_sentence_elements . '
             WHERE registered_sentence_id = ?';

        $stmt_delete_elements = $pdo->prepare($sql_delete_elements);
        $stmt_delete_elements->bindValue(1, $t_registered_sentence_id, PDO::PARAM_INT);
        $stmt_delete_elements->execute();

        $sql_select_layers =
            'SELECT id
             FROM ' . $t_layers . '
             WHERE registered_sentence_id = ?';

        $stmt_select_layers = $pdo->prepare($sql_select_layers);
        $stmt_select_layers->bindValue(1, $t_registered_sentence_id, PDO::PARAM_INT);
        $stmt_select_layers->execute();
        $arr_layers = $stmt_select_layers->fetchAll(PDO::FETCH_ASSOC);

        $sql_delete_overrides =
            'DELETE o
             FROM ' . $t_layer_element_overrides . ' o
             INNER JOIN ' . $t_layer_elements . ' e
                 ON o.layer_element_id = e.id
             WHERE e.layer_id = ?';

        $stmt_delete_overrides = $pdo->prepare($sql_delete_overrides);

        $sql_delete_layer_elements =
            'DELETE FROM ' . $t_layer_elements . '
             WHERE layer_id = ?';

        $stmt_delete_layer_elements = $pdo->prepare($sql_delete_layer_elements);

        $sql_delete_layer =
            'DELETE FROM ' . $t_layers . '
             WHERE id = ?';

        $stmt_delete_layer = $pdo->prepare($sql_delete_layer);

        if (!empty($arr_layers)) {
            foreach ($arr_layers as $loop_layer) {

                $int_layer_id = intval($loop_layer['id']);

                $stmt_delete_overrides->bindValue(1, $int_layer_id, PDO::PARAM_INT);
                $stmt_delete_overrides->execute();

                $stmt_delete_layer_elements->bindValue(1, $int_layer_id, PDO::PARAM_INT);
                $stmt_delete_layer_elements->execute();

                $stmt_delete_layer->bindValue(1, $int_layer_id, PDO::PARAM_INT);
                $stmt_delete_layer->execute();
            }
        }

        $sql_insert_element =
            'INSERT INTO ' . $t_registered_sentence_elements . ' (
                registered_sentence_id,
                id_name,
                unique_key,
                japanese_id,
                japanese_element_id,
                sub_classification_id,
                form_id,
                label_id,
                voice_id,
                bounds_top,
                bounds_left,
                link_id,
                link_type,
                japanese,
                kana,
                sub_classification,
                phrase_clause_type,
                phrase_clause_id,
                japanese_phrase_clause,
                kana_phrase_clause,
                sort
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )';

        $stmt_insert_element = $pdo->prepare($sql_insert_element);

        $sql_insert_new_word =
            'INSERT INTO ' . $t_get_new_word_register_sentence . ' (
                registered_sentence_elements_id,
                masta_japanese_root_id,
                sub_classification_id,
                japanese,
                kana
            ) VALUES (?, ?, ?, ?, ?)';

        $stmt_insert_new_word = $pdo->prepare($sql_insert_new_word);

        foreach ($arr_link_id_add_sort as $loop_link_id_add_sort) {

            if (!is_array($loop_link_id_add_sort)) {
                continue;
            }

            $loop_link_id_add_sort = array_map('escape_html', $loop_link_id_add_sort);

            $japaneseId = intval($loop_link_id_add_sort[$str_snake_to_camel_japanese_id] ?? 0);
            $subClassificationId = intval($loop_link_id_add_sort[$str_snake_to_camel_sub_classification_id] ?? 0);

            $stmt_insert_element->bindValue(1, $t_registered_sentence_id, PDO::PARAM_INT);
            $stmt_insert_element->bindValue(2, (string)($loop_link_id_add_sort['idName'] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(3, intval($loop_link_id_add_sort['uniqueKey'] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(4, $japaneseId, PDO::PARAM_INT);
            $stmt_insert_element->bindValue(5, intval($loop_link_id_add_sort[$str_snake_to_camel_japanese_element_id] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(6, $subClassificationId, PDO::PARAM_INT);
            $stmt_insert_element->bindValue(7, intval($loop_link_id_add_sort[$str_snake_to_camel_form_id] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(8, intval($loop_link_id_add_sort[$str_snake_to_camel_label_id] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(9, intval($loop_link_id_add_sort[$str_snake_to_camel_voice_id] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(10, (string)($loop_link_id_add_sort['boundsTop'] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(11, (string)($loop_link_id_add_sort['boundsLeft'] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(12, intval($loop_link_id_add_sort['linkId'] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(13, intval($loop_link_id_add_sort['linkType'] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(14, (string)($loop_link_id_add_sort['japanese'] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(15, (string)($loop_link_id_add_sort['kana'] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(16, (string)($loop_link_id_add_sort['subClassification'] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(17, (string)($loop_link_id_add_sort['phraseClauseType'] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(18, intval($loop_link_id_add_sort['phraseClauseId'] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(19, (string)($loop_link_id_add_sort['japanesePhraseClause'] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(20, (string)($loop_link_id_add_sort['kanaPhraseClause'] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(21, intval($loop_link_id_add_sort['sort'] ?? 0), PDO::PARAM_INT);

            $stmt_insert_element->execute();

            $last_insert_id = intval($pdo->lastInsertId());
            if ($last_insert_id <= 0) {
                throw new RuntimeException('Failed to insert registered_sentence_element');
            }

            if ($japaneseId <= 1 && $subClassificationId !== $int_Num) {

                $stmt_insert_new_word->bindValue(1, $last_insert_id, PDO::PARAM_INT);
                $stmt_insert_new_word->bindValue(2, $japaneseId, PDO::PARAM_INT);
                $stmt_insert_new_word->bindValue(3, $subClassificationId, PDO::PARAM_INT);
                $stmt_insert_new_word->bindValue(4, (string)($loop_link_id_add_sort['japanese'] ?? ''), PDO::PARAM_STR);
                $stmt_insert_new_word->bindValue(5, (string)($loop_link_id_add_sort['kana'] ?? ''), PDO::PARAM_STR);
                $stmt_insert_new_word->execute();
            }
        }

        $pdo->commit();
        $pdo = null;

        respond_success([
            'int_selected_language' => $int_selected_language
        ]);

    } catch (Throwable $e) {

        if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
            $pdo->rollBack();
        }

        respond_exception($e, 'registered_sentence_update_unhandled');
    }

