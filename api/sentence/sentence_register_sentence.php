<?php

    header('Content-Type: application/json; charset=utf-8');

    require_once __DIR__ . '/../_bootstrap.php';

    try {

        $current_user = wp_get_current_user();
        $current_user_id = intval($current_user->ID);

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

        if (!isset($input['send_grammar_unique_code'])) { respond_error('Value not found: send_grammar_unique_code', 400); }
        if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }
        if (!isset($input['arr_link_id_add_sort'])) { respond_error('Value not found: arr_link_id_add_sort', 400); }

        $int_selected_language = intval($input['int_selected_language'] ?? $int_used_language_jpn);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        $unique_code = trim((string)($input['send_grammar_unique_code'] ?? ''));
        if ($unique_code === '') {
            respond_error('Invalid value: send_grammar_unique_code', 400);
        }
        $unique_code = escape_html($unique_code);

        $t_masta_japanese_root_id = fetch_masta_japanese_root_id_from_unique_code($unique_code, $int_selected_language);
        if (intval($t_masta_japanese_root_id) === 0) {
            respond_error('The record does not exist', 400);
        }

        $add_sort = count_next_sort(
            $t_registered_sentences,
            'masta_japanese_root_id',
            $t_masta_japanese_root_id,
            $int_selected_language
        );

        $arr_link_id_add_sort = is_array($input['arr_link_id_add_sort']) ? $input['arr_link_id_add_sort'] : [];

        $str_group_of_words_japanese = escape_html($input['str_group_of_words_japanese'] ?? '');
        $str_group_of_words_kana = escape_html($input['str_group_of_words_kana'] ?? '');

        // unique_code生成（既存関数を使用）
        $generated = generate_unique_code($t_registered_sentences, 'unique_code', 'id', $int_selected_language);
        if ($generated === null) {
            respond_error('Failed to generate unique code', 500);
        }

        // PDO開始（参考ファイルと同じ）
        $pdo = connect_to_database();
        if (!($pdo instanceof PDO)) {
            respond_error('Database connection failed', 500);
        }

        $pdo->beginTransaction();

        // 既存文章チェック（必要なら条件を増やしてください：例 masta_japanese_root_id も含める等）
        $sql_check = 'SELECT id FROM ' . $t_registered_sentences . ' WHERE sentence = ? LIMIT 1';
        $stmt_check = $pdo->prepare($sql_check);
        $stmt_check->bindValue(1, $str_group_of_words_japanese, PDO::PARAM_STR);
        $stmt_check->execute();
        $exists_id = $stmt_check->fetchColumn();

        if (!empty($exists_id)) {
            $pdo->rollBack();
            respond_error('This sentence has already been registered.', 409);
        }

        // t_registered_sentences INSERT
        $sql_insert_sentence =
            'INSERT INTO ' . $t_registered_sentences . ' (
                unique_code,
                user_id,
                masta_japanese_root_id,
                sentence,
                sentence_kana,
                is_published,
                is_published_shorts,
                is_published_video,
                sort
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $stmt_insert_sentence = $pdo->prepare($sql_insert_sentence);
        $stmt_insert_sentence->bindValue(1, $generated, PDO::PARAM_STR);
        $stmt_insert_sentence->bindValue(2, $current_user_id, PDO::PARAM_INT);
        $stmt_insert_sentence->bindValue(3, $t_masta_japanese_root_id, PDO::PARAM_INT);
        $stmt_insert_sentence->bindValue(4, $str_group_of_words_japanese, PDO::PARAM_STR);
        $stmt_insert_sentence->bindValue(5, $str_group_of_words_kana, PDO::PARAM_STR);
        $stmt_insert_sentence->bindValue(6, FLAG_TRUE, PDO::PARAM_INT);
        $stmt_insert_sentence->bindValue(7, FLAG_FALSE, PDO::PARAM_INT);
        $stmt_insert_sentence->bindValue(8, FLAG_FALSE, PDO::PARAM_INT);
        $stmt_insert_sentence->bindValue(9, $add_sort, PDO::PARAM_INT);
        $stmt_insert_sentence->execute();

        $t_registered_sentence_id = intval($pdo->lastInsertId());
        if ($t_registered_sentence_id <= 0) {
            throw new RuntimeException('Failed to insert registered_sentence');
        }

        // t_registered_sentence_elements INSERT（繰り返し）
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
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

        $stmt_insert_element = $pdo->prepare($sql_insert_element);

        // 未登録語彙テーブル INSERT
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

            $stmt_insert_element->bindValue(1,  $t_registered_sentence_id, PDO::PARAM_INT);
            $stmt_insert_element->bindValue(2,  $loop_link_id_add_sort['idName'] ?? '', PDO::PARAM_STR);
            $stmt_insert_element->bindValue(3,  intval($loop_link_id_add_sort['uniqueKey'] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(4,  intval($loop_link_id_add_sort[$str_snake_to_camel_japanese_id] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(5,  intval($loop_link_id_add_sort[$str_snake_to_camel_japanese_element_id] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(6,  intval($loop_link_id_add_sort[$str_snake_to_camel_sub_classification_id] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(7,  intval($loop_link_id_add_sort[$str_snake_to_camel_form_id] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(8,  intval($loop_link_id_add_sort[$str_snake_to_camel_label_id] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(9,  intval($loop_link_id_add_sort[$str_snake_to_camel_voice_id] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(10, (string)($loop_link_id_add_sort['boundsTop'] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(11, (string)($loop_link_id_add_sort['boundsLeft'] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(12, intval($loop_link_id_add_sort[$str_snake_to_camel_link_id] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(13, intval($loop_link_id_add_sort['linkType'] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(14, (string)($loop_link_id_add_sort[$str_snake_to_camel_japanese] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(15, (string)($loop_link_id_add_sort[$str_snake_to_camel_kana] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(16, (string)($loop_link_id_add_sort[$str_snake_to_camel_sub_classification] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(17, (string)($loop_link_id_add_sort['phraseClauseType'] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(18, intval($loop_link_id_add_sort['phraseClauseId'] ?? 0), PDO::PARAM_INT);
            $stmt_insert_element->bindValue(19, (string)($loop_link_id_add_sort['japanesePhraseClause'] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(20, (string)($loop_link_id_add_sort['kanaPhraseClause'] ?? ''), PDO::PARAM_STR);
            $stmt_insert_element->bindValue(21, intval($loop_link_id_add_sort['sort'] ?? 0), PDO::PARAM_INT);

            $stmt_insert_element->execute();

            $registered_sentence_elements_id = intval($pdo->lastInsertId());
            if ($registered_sentence_elements_id <= 0) {
                throw new RuntimeException('Failed to insert registered_sentence_element');
            }

            // ★必須：未登録語彙の保存（山本様の仕様）
            if (
                intval($loop_link_id_add_sort[$str_snake_to_camel_japanese_id] ?? 0) <= 1 &&
                intval($loop_link_id_add_sort[$str_snake_to_camel_sub_classification_id] ?? 0) !== $int_Num
            ) {
                $stmt_insert_new_word->bindValue(1, $registered_sentence_elements_id, PDO::PARAM_INT);
                $stmt_insert_new_word->bindValue(2, intval($loop_link_id_add_sort[$str_snake_to_camel_japanese_id] ?? 0), PDO::PARAM_INT);
                $stmt_insert_new_word->bindValue(3, intval($loop_link_id_add_sort[$str_snake_to_camel_sub_classification_id] ?? 0), PDO::PARAM_INT);
                $stmt_insert_new_word->bindValue(4, (string)($loop_link_id_add_sort[$str_snake_to_camel_japanese] ?? ''), PDO::PARAM_STR);
                $stmt_insert_new_word->bindValue(5, (string)($loop_link_id_add_sort[$str_snake_to_camel_kana] ?? ''), PDO::PARAM_STR);
                $stmt_insert_new_word->execute();
            }
        }

        $pdo->commit();
        $pdo = null;

        respond_success($arr_link_id_add_sort);

    } catch (Throwable $e) {

        if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
            $pdo->rollBack();
        }

        respond_exception($e, 'registered_sentence_create_unhandled');
    }

