<?php


/******************************************************
 *  SQL
 ******************************************************/
$str_sql_where_between = 'BETWEEN';
$str_sql_where_is_null = 'IS NULL';
$str_sql_where_is_not_null = 'IS NOT NULL';
$str_sql_where_is_in = 'IN';
$str_sql_where_is_not_in = 'NOT IN';
$str_sql_where_is_exists = 'EXISTS';
$str_sql_where_is_not_exists = 'NOT EXISTS';

$sql_exists_registered_sentences  = "SELECT 1 FROM $t_layers WHERE $t_layers.registered_sentence_id = $t_registered_sentences.id";

$str_t_masta_japanese_root_1 = $t_masta_japanese_root.'_1';
$str_t_masta_japanese_root_2 = $t_masta_japanese_root.'_2';
$str_t_masta_japanese_root_3 = $t_masta_japanese_root.'_3';