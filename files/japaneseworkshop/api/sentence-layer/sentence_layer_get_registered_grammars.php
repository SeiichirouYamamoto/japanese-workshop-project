<?php

    header('Content-Type: application/json; charset=utf-8');

    require_once __DIR__ . '/../_bootstrap.php';

    try {

        $user_level = get_user_level();

        if ($user_level === null) {
            respond_error('Login required', 401);
        }

        // ここは必要に応じて is_teacher_level に差し替え可能です
        if (!is_admin_level($user_level)) {
            respond_error('Forbidden', 403);
        }

        $raw = file_get_contents('php://input');
        $input = json_decode($raw, true);

        if (!is_array($input)) {
            respond_error('Invalid JSON', 400);
        }

        if (!isset($input['int_layer_id'])) { respond_error('Value not found: int_layer_id', 400); }
        if (!isset($input['arr_elements'])) { respond_error('Value not found: arr_elements', 400); }
        if (!isset($input['int_selected_language'])) { respond_error('Value not found: int_selected_language', 400); }

        $int_layer_id = intval($input['int_layer_id']);
        if ($int_layer_id <= 0) {
            respond_error('Invalid value: int_layer_id', 400);
        }

        $int_selected_language = intval($input['int_selected_language']);
        if ($int_selected_language < 0) {
            respond_error('Invalid value: int_selected_language', 400);
        }

        $arr_elements = is_array($input['arr_elements']) ? array_values($input['arr_elements']) : [];
        $arr_elements = array_map('intval', $arr_elements);
        $arr_elements = array_values(array_unique($arr_elements));
        sort($arr_elements);

        $arr_strSQL_select = [
            [$t_layer_elements,'id'],
            [$t_layer_elements,'registered_sentence_element_id as sentenceElementId']
        ];

        $strSQL_from = ' FROM ' . $t_layer_elements;

        $arr_strSQL_where = [
            [
                [
                    [$t_layer_elements,'layer_id','=',$int_layer_id,'PDO::PARAM_INT','']
                ],
                ''
            ]
        ];

        $arr_strSQL_order = [];

        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $arr_layer_elements) = execute_select_and_fetch_all(
            $arr_strSQL_select,
            $strSQL_from,
            $arr_strSQL_where,
            $arr_strSQL_order,
            $strSQL_option
        );
        handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

        $arr_layer_elements = array_map('intval', array_column($arr_layer_elements, 'sentenceElementId'));
        $arr_layer_elements = array_values(array_unique($arr_layer_elements));
        sort($arr_layer_elements);

        $commonElements = array_values(array_intersect($arr_elements, $arr_layer_elements));
        $notCommonElements = array_values(array_diff($arr_elements, $arr_layer_elements));

        $arr_layer_elements_for_loop = [
            ['type' => 'current', 'array' => $commonElements],
            ['type' => 'others',  'array' => $notCommonElements]
        ];

        $relations = fetch_all_root_parent_child_relations($int_selected_language);
        $map_parent_to_children = [];
        foreach ($relations as $row) {
            $map_parent_to_children[$row['masta_japanese_root_id_parent']][] = $row;
        }

        $arr_strSQL_select_result = [
            [$t_masta_japanese_root,'id'],
            [$t_masta_japanese_root,$arr_columns_masta_japanese_root[$int_selected_language].' as ' . $str_snake_to_camel_japanese],
            [$t_masta_japanese_root,$str_column_root_kana . ' as ' . $str_snake_to_camel_kana],
            [$t_masta_japanese_root,'root_example as ' . $str_snake_to_camel_root_example],
            [$t_masta_japanese_sub_category,'category_id as ' . $str_snake_to_camel_category_id]
        ];

        $resultArray = [];

        foreach ($arr_layer_elements_for_loop as $loop) {
            $type = $loop['type'];

            // もしothersも取得したいなら消す
            if ($type === 'others') {
                $resultArray[] = [
                    'type' => $type,
                    'arrGrammar' => [],
                    'arrJapaneseParticle' => []
                ];
                continue;
            }

            $ids = (isset($loop['array']) && is_array($loop['array'])) ? $loop['array'] : [];
            $ids = array_values(array_unique(array_map('intval', $ids)));
            sort($ids);

            $arrGrammar = [];
            $arrJapaneseParticle = [];
            $arr_japanese_ids_for_particles = [];

            if (!empty($ids)) {
                $strSQL_from = " FROM ($t_masta_japanese_root INNER JOIN $t_masta_japanese_sub_category ON $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id) INNER JOIN $t_registered_sentence_elements ON $t_masta_japanese_root.id = $t_registered_sentence_elements.japanese_id";
                $arr_strSQL_where = [[[[$t_registered_sentence_elements,'id','IN',$ids,'PDO::PARAM_INT','']], '']];
                $arr_strSQL_order = [];
                $strSQL_option = '';

                list($pdo_has_error, $select_has_error, $e, $rowsSelected) = execute_select_and_fetch_all(
                    $arr_strSQL_select_result,
                    $strSQL_from,
                    $arr_strSQL_where,
                    $arr_strSQL_order,
                    $strSQL_option
                );
                handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

                $seenGrammarIds = [];
                foreach ($rowsSelected as $r) {
                    $id = intval($r['id']);
                    $category_id = intval($r[$str_snake_to_camel_category_id]);
                    if ($category_id === $int_masta_japanese_category_id_grammar) {
                        if (!isset($seenGrammarIds[$id])) {
                            $r[$str_snake_to_camel_root_example] = apply_remove_original_tags($r[$str_snake_to_camel_root_example]);
                            $arrGrammar[] = $r;
                            $seenGrammarIds[$id] = FLAG_TRUE;
                        }
                    } elseif ($category_id === $int_masta_japanese_category_id_particle_with_label) {
                        $parents = fetch_arr_grammar_usage_children_by_attribute($id, $int_hypernym_hyponym, $map_parent_to_children, $int_selected_language);
                        $parents = array_map('intval', array_column($parents, 'masta_japanese_root_id'));
                        if (!empty($parents)) {
                            $arr_japanese_ids_for_particles = array_merge($arr_japanese_ids_for_particles, $parents);
                            foreach ($parents as $p) {
                                foreach ([$int_grammar_outline_status] as $belong) {
                                    $children = fetch_arr_grammar_usage_children_by_attribute($p, $belong, $map_parent_to_children, $int_selected_language);
                                    if (!empty($children)) {
                                        $arr_japanese_ids_for_particles = array_merge($arr_japanese_ids_for_particles, array_column($children, 'masta_japanese_root_id'));
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $arr_japanese_ids_for_particles = array_values(array_unique(array_map('intval', $arr_japanese_ids_for_particles)));

            if (!empty($arr_japanese_ids_for_particles)) {
                $strSQL_from = " FROM $t_masta_japanese_root INNER JOIN $t_masta_japanese_sub_category ON $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id";
                $arr_strSQL_where = [[[[$t_masta_japanese_root,'id','IN',$arr_japanese_ids_for_particles,'PDO::PARAM_INT','']], '']];
                $arr_strSQL_order = [];
                $strSQL_option = '';

                list($pdo_has_error, $select_has_error, $e, $particleRows) = execute_select_and_fetch_all(
                    $arr_strSQL_select_result,
                    $strSQL_from,
                    $arr_strSQL_where,
                    $arr_strSQL_order,
                    $strSQL_option
                );
                handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

                foreach ($particleRows as $rp) {
                    $rp[$str_snake_to_camel_root_example] = apply_remove_original_tags($rp[$str_snake_to_camel_root_example]);
                    $arrGrammar[] = $rp;
                    // もしgrammarsとparticleを分けたい場合はarrJapaneseParticleに入れる
                    // $arrJapaneseParticle[] = $rp;
                }
            }

            $resultArray[] = [
                'type' => $type,
                'arrGrammar' => $arrGrammar,
                'arrJapaneseParticle' => $arrJapaneseParticle
            ];
        }

        // --- ここから下は元コードのまま（ただし $arr_predicate_ids 等の未定義は別途要確認） ---

        $strSQL_from = " FROM $t_masta_japanese_root INNER JOIN $t_masta_japanese_sub_category ON $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id";
        $arr_strSQL_where = [[[[$t_masta_japanese_root,'id','IN',$arr_predicate_ids,'PDO::PARAM_INT','']], '']];
        $arr_strSQL_order = [[$t_masta_japanese_root,'sort','ASC']];
        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $predicateRows) = execute_select_and_fetch_all($arr_strSQL_select_result, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
        handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

        foreach ($predicateRows as $k => $v) {
            if (isset($v[$str_snake_to_camel_root_example])) {
                $predicateRows[$k][$str_snake_to_camel_root_example] = apply_remove_original_tags($v[$str_snake_to_camel_root_example]);
            }
        }

        $resultArray[] = [
            'type' => 'predicate',
            'arrPredicate' => $predicateRows
        ];

        $strSQL_from = " FROM $t_masta_japanese_root INNER JOIN $t_masta_japanese_sub_category ON $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id";
        $arr_strSQL_where = [[[[$t_masta_japanese_sub_category,'id','=',$int_masta_japanese_sub_category_id_terminology_particle,'PDO::PARAM_INT','']], '']];
        $arr_strSQL_order = [[$t_masta_japanese_root,'sort','ASC']];
        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $particleRows) = execute_select_and_fetch_all($arr_strSQL_select_result, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
        handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

        foreach ($particleRows as $k => $v) {
            if (isset($v[$str_snake_to_camel_root_example])) {
                $particleRows[$k][$str_snake_to_camel_root_example] = apply_remove_original_tags($v[$str_snake_to_camel_root_example]);
            }
        }

        $resultArray[] = [
            'type' => 'particles',
            'arrParticles' => $particleRows
        ];

        $strSQL_from = " FROM $t_masta_japanese_root INNER JOIN $t_masta_japanese_sub_category ON $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id";
        $arr_strSQL_where = [[[[$t_masta_japanese_root,'sub_category_id','=',$int_masta_japanese_sub_category_id_inflection,'PDO::PARAM_INT','']], '']];
        $arr_strSQL_order = [[$t_masta_japanese_root,'sort','ASC']];
        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $inflectionRows) = execute_select_and_fetch_all($arr_strSQL_select_result, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
        handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

        foreach ($inflectionRows as $k => $v) {
            if (isset($v[$str_snake_to_camel_root_example])) {
                $inflectionRows[$k][$str_snake_to_camel_root_example] = apply_remove_original_tags($v[$str_snake_to_camel_root_example]);
            }
        }

        $resultArray[] = [
            'type' => 'inflection',
            'arrInflection' => $inflectionRows
        ];

        $strSQL_from = " FROM $t_masta_japanese_root INNER JOIN $t_masta_japanese_sub_category ON $t_masta_japanese_root.sub_category_id = $t_masta_japanese_sub_category.id";
        $arr_strSQL_where = [[[[$t_masta_japanese_root,'id','IN',$arr_special_term_ids,'PDO::PARAM_INT','']], '']];
        $arr_strSQL_order = [[$t_masta_japanese_root,'sort','ASC']];
        $strSQL_option = '';

        list($pdo_has_error, $select_has_error, $e, $specialTermsRows) = execute_select_and_fetch_all($arr_strSQL_select_result, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option);
        handle_database_error_and_respond($pdo_has_error, $select_has_error, $e);

        foreach ($specialTermsRows as $k => $v) {
            if (isset($v[$str_snake_to_camel_root_example])) {
                $specialTermsRows[$k][$str_snake_to_camel_root_example] = apply_remove_original_tags($v[$str_snake_to_camel_root_example]);
            }
        }

        $resultArray[] = [
            'type' => 'specialTerms',
            'arrSpecialTerms' => $specialTermsRows
        ];

        respond_success($resultArray);

    } catch (Throwable $e) {
        respond_exception($e, 'layer_get_layer_grammars_unhandled');
    }

