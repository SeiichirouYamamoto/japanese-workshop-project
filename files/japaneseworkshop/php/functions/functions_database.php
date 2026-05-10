<?php



/******************************************************
 *  DATABASE
 *  
 ******************************************************/
function connect_to_database(){

	$host = $_SERVER['HTTP_HOST'];
	$host = explode(':', $host)[0];
	$directory_name = explode('.', $host)[0];

	require dirname(ABSPATH, 2) . '/external_connections_' . $directory_name . '/master.php';

	try{
		$pdo = new PDO('mysql:host='. $dsn['host'] . ';dbname=' . $dsn['dbname'] . ';charset=utf8',$dsn['user'],$dsn['pass']);
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);
	}

	catch (PDOException $e) {
		error_log('[DB ERROR] ' . $e->getMessage());
		$pdo = null;
	}

	return $pdo;

}


/******************************************************
 *  EXECUTE
 *  
 ******************************************************/
function execute_select_and_fetch_all($arr_strSQL_select, $strSQL_from, $arr_strSQL_where, $arr_strSQL_order, $strSQL_option){

	global
		$str_sql_where_between,
		$str_sql_where_is_null,
		$str_sql_where_is_not_null,
		$str_sql_where_is_in,
		$str_sql_where_is_not_in,
		$str_sql_where_is_exists,
		$str_sql_where_is_not_exists;

	$e = null;

	$strSQL = 'Select ';

	foreach($arr_strSQL_select as $value){
		if($value[INDEX_FIRST] === ''){
			$target_column = $value[INDEX_SECOND];
		}
		else{
			$target_column = $value[INDEX_FIRST] . '.' . $value[INDEX_SECOND];
		}
		$strSQL .= $target_column . ', ';
	}

	$strSQL = mb_substr($strSQL, 0, -2, "UTF-8");

	if(empty($arr_strSQL_where)){
		$strSQL .= $strSQL_from;
	}
	else {
		$last_key = array_key_last($arr_strSQL_where);
		$strSQL .= $strSQL_from . ' Where (';
		foreach($arr_strSQL_where as $key_strSQL_where => $loop_strSQL_where){
			$strSQL .= '(';
			$arr_target = $loop_strSQL_where[INDEX_FIRST];
			foreach($arr_target as $value){
				if($value[INDEX_FIRST] === ''){
					$target_column = $value[INDEX_SECOND];
				}
				else{
					$target_column = $value[INDEX_FIRST] . '.' . $value[INDEX_SECOND];
				}
				if($value[INDEX_THIRD] === $str_sql_where_between){
					if(!empty($value[INDEX_SEVENTH])){
						$strSQL .= $target_column . ' BETWEEN ? AND ?) ' . $value[INDEX_SEVENTH] . ' (';
					}
					else{
						$strSQL .= $target_column . ' BETWEEN ? AND ?)';
					}
				}
				elseif($value[INDEX_THIRD] === $str_sql_where_is_null){
					if(!empty($value[INDEX_SIXTH])){
						$strSQL .= $target_column . ' ' . $str_sql_where_is_null . ') ' . $value[INDEX_SIXTH] . ' (';
					}
					else{
						$strSQL .= $target_column . ' ' . $str_sql_where_is_null . ')';
					}
				}
				elseif($value[INDEX_THIRD] === $str_sql_where_is_not_null){
					if(!empty($value[INDEX_SIXTH])){
						$strSQL .= $target_column . ' ' . $str_sql_where_is_not_null . ') ' . $value[INDEX_SIXTH] . ' (';
					}
					else{
						$strSQL .= $target_column . ' ' . $str_sql_where_is_not_null . ')';
					}
				}
				elseif ($value[INDEX_THIRD] === $str_sql_where_is_in || $value[INDEX_THIRD] === $str_sql_where_is_not_in) {
					$placeholders = implode(',', array_fill(0, count($value[INDEX_FOURTH]), '?'));
					if (!empty($value[INDEX_SIXTH])) {
						$strSQL .= $target_column . ' ' . $value[INDEX_THIRD] . " ($placeholders)) " . $value[INDEX_SIXTH] . ' (';
					} else {
						$strSQL .= $target_column . ' ' . $value[INDEX_THIRD] . " ($placeholders))";
					}
				}
				elseif ($value[INDEX_THIRD] === $str_sql_where_is_exists || $value[INDEX_THIRD] === $str_sql_where_is_not_exists) {
					if (!empty($value[INDEX_SIXTH])) {
						$strSQL .= $value[INDEX_THIRD] . ' (' . $value[INDEX_SECOND] . ')) ' . $value[INDEX_SIXTH] . ' (';
					} else {
						$strSQL .= $value[INDEX_THIRD] . ' (' . $value[INDEX_SECOND] . '))';
					}
				}
				else{
					if(!empty($value[INDEX_SIXTH])){
						$strSQL .= $target_column . ' ' . $value[INDEX_THIRD] . ' ?) ' . $value[INDEX_SIXTH] . ' (';
					}
					else{
						$strSQL .= $target_column . ' ' . $value[INDEX_THIRD] . ' ?)';
					}
				}
			}
			if ($key_strSQL_where === $last_key) {
				$strSQL = $strSQL.')';
			} else {
				$strSQL = $strSQL.') '.$loop_strSQL_where[INDEX_SECOND].' (';
			}
		}
	}

	if(empty($arr_strSQL_order)){
		$strSQL = $strSQL;
	}
	else {
		$strSQL .= ' Order BY ';
		foreach($arr_strSQL_order as $value){
			if($value[INDEX_FIRST] === ''){
				$target_column = $value[INDEX_SECOND];
			}
			else{
				$target_column = $value[INDEX_FIRST] . '.' . $value[INDEX_SECOND];
			}
			$strSQL .= $target_column . ' ' . $value[INDEX_THIRD] . ' , ';
		}
		$strSQL = mb_substr($strSQL, 0, -2, "UTF-8");
	}

	$strSQL .= ' ' . $strSQL_option . ';';

	$pdo = connect_to_database();

	if(empty($pdo) === true){
		$pdo_has_error = FLAG_TRUE;
		$select_has_error = FLAG_FALSE;
		$all = null;
		return array($pdo_has_error, $select_has_error, null, $all);
	}

	try{

		$database_values = $pdo-> prepare($strSQL);

		if(!empty($arr_strSQL_where)){
			$int_prepare_value = 1;
			foreach($arr_strSQL_where as $loop_strSQL_where){
				$arr_target = $loop_strSQL_where[INDEX_FIRST];
				foreach($arr_target as $value){
					if($value[INDEX_THIRD] === $str_sql_where_between){
						$database_values -> bindvalue($int_prepare_value, $value[INDEX_FOURTH], PDO::PARAM_INT);
						++$int_prepare_value;
						$database_values -> bindvalue($int_prepare_value, $value[INDEX_FIFTH], PDO::PARAM_INT);
						++$int_prepare_value;
					}
					elseif($value[INDEX_THIRD] === $str_sql_where_is_null){
						$int_prepare_value = $int_prepare_value;
					}
					elseif($value[INDEX_THIRD] === $str_sql_where_is_not_null){
						$int_prepare_value = $int_prepare_value;
					}
					elseif ($value[INDEX_THIRD] === $str_sql_where_is_in || $value[INDEX_THIRD] === $str_sql_where_is_not_in) {
						foreach ($value[INDEX_FOURTH] as $v) {
							$database_values->bindValue($int_prepare_value, $v, constant($value[INDEX_FIFTH]));
							++$int_prepare_value;
						}
					}
					elseif ($value[INDEX_THIRD] === $str_sql_where_is_exists || $value[INDEX_THIRD] === $str_sql_where_is_not_exists) {
						continue;
					}
					else{
						$database_values -> bindvalue($int_prepare_value, $value[INDEX_FOURTH], constant($value[INDEX_FIFTH]));
						++$int_prepare_value;
					}
				}
			}
		}

		$database_values -> execute();

		$all = $database_values->fetchAll();
		$select_has_error = FLAG_FALSE;

	}
	catch (PDOException $e) {

		$all = null;
		$select_has_error = FLAG_TRUE;

	}

	$pdo_has_error = FLAG_FALSE;
	$pdo = null;

	return array($pdo_has_error, $select_has_error, $e ?? null, $all);

}


function execute_insert_data($insert_table, $arr_insertSQL) {

    $e = null;
    $last_insert_id = null;

    $insertSQL = 'INSERT INTO ' . $insert_table . ' (';

    foreach ($arr_insertSQL as $loop_insertSQL) {
        $insertSQL .= $loop_insertSQL[INDEX_FIRST] . ',';
    }

    $insertSQL = mb_substr($insertSQL, 0, -1, 'UTF-8') . ') VALUES (';

    foreach ($arr_insertSQL as $loop_insertSQL) {
        $insertSQL .= $loop_insertSQL[INDEX_SECOND] . ',';
    }

    $insertSQL = mb_substr($insertSQL, 0, -1, 'UTF-8') . ')';

    $pdo = connect_to_database();

    if (empty($pdo) === true) {
        $pdo_has_error = FLAG_TRUE;
        $insert_has_error = FLAG_FALSE;
        return array($pdo_has_error, $insert_has_error, $e, $last_insert_id);
    }

    $pdo->beginTransaction();

    try {
        $insert_data = $pdo->prepare($insertSQL);

        foreach ($arr_insertSQL as $key => $loop_insertSQL) {
            $int_prepare_value = $key + 1;
            $insert_data->bindValue(
                $int_prepare_value,
                $loop_insertSQL[INDEX_THIRD],
                constant($loop_insertSQL[INDEX_FOURTH])
            );
        }

        $insert_data->execute();
        $last_insert_id = $pdo->lastInsertId();

        $pdo->commit();
        $insert_has_error = FLAG_FALSE;

    } catch (PDOException $exception) {

        $pdo->rollBack();
        $insert_has_error = FLAG_TRUE;
        $e = $exception->getMessage();

    }

    $pdo_has_error = FLAG_FALSE;
    $pdo = null;

    return array($pdo_has_error, $insert_has_error, $e, $last_insert_id);
}


function execute_update_data($update_table, $arr_updateSQL, $arr_whereSQL){

	$e = null;

	$updateSQL = 'UPDATE '.$update_table.' SET ';

	foreach($arr_updateSQL as $loop_updateSQL){
		$updateSQL = $updateSQL . $loop_updateSQL[INDEX_FIRST].' = '.$loop_updateSQL[INDEX_SECOND].', ';
	}

	$updateSQL = mb_substr($updateSQL, 0, -2, "UTF-8");
	$updateSQL = $updateSQL.' WHERE ';

	foreach($arr_whereSQL as $loop_whereSQL){
		$updateSQL = $updateSQL . $loop_whereSQL[INDEX_FIRST].' = '.$loop_whereSQL[INDEX_SECOND].' '.$loop_whereSQL[INDEX_FIFTH];
	}

	$pdo = connect_to_database();

	if(empty($pdo)){

		$pdo_has_error = FLAG_TRUE;
		$update_has_error = FLAG_FALSE;
		return array($pdo_has_error, $update_has_error, $e);

	}

	$pdo -> beginTransaction();

	try{
		$update_data = $pdo -> prepare($updateSQL);

		foreach($arr_updateSQL as $loop_updateSQL){
			$update_data -> bindValue($loop_updateSQL[INDEX_SECOND], $loop_updateSQL[INDEX_THIRD], constant($loop_updateSQL[INDEX_FOURTH]));
		}
		foreach($arr_whereSQL as $loop_whereSQL){
			$update_data -> bindValue($loop_whereSQL[INDEX_SECOND], $loop_whereSQL[INDEX_THIRD], constant($loop_whereSQL[INDEX_FOURTH]));
		}

		$update_data -> execute();

		$pdo -> commit();
		$update_has_error = FLAG_FALSE;

	}
	catch (PDOException $exception) {

		$pdo -> rollBack();
		$update_has_error = FLAG_TRUE;
		$e = $exception->getMessage();

	}

	$pdo_has_error = FLAG_FALSE;
	$pdo = null;
	return array($pdo_has_error, $update_has_error, $e);
}


function execute_delete_data($target_table, $str_deleteSQL, $arr_values){

	$e = null;

	$deleteSQL = 'DELETE FROM '.$target_table.' WHERE '.$str_deleteSQL;

	$pdo = connect_to_database();

	if(empty($pdo)){
		$pdo_has_error = FLAG_TRUE;
		$delete_has_error = FLAG_FALSE;
		return array($pdo_has_error, $delete_has_error, $e);
	}

	$pdo -> beginTransaction();

	try{
		$delete_data = $pdo -> prepare($deleteSQL);

		foreach($arr_values as $key => $loop_values){
			$int_prepare_value = $key + 1;
			$delete_data -> bindValue($int_prepare_value, $loop_values[INDEX_FIRST], constant($loop_values[INDEX_SECOND]));
		}

		$delete_data -> execute();

		$pdo -> commit();
		$delete_has_error = FLAG_FALSE;

	}
	catch (PDOException $exception) {

		$pdo -> rollBack();
		$delete_has_error = FLAG_TRUE;
		$e = $exception->getMessage();

	}

	$pdo_has_error = FLAG_FALSE;
	$pdo = null;
	return array($pdo_has_error, $delete_has_error, $e);
}



/******************************************************
 *  HANDLE
 *  
 ******************************************************/

function handle_database_error_and_redirect($pdo_has_error, $query_has_error, $e = null, $int_selected_language){

	global
		$arr_str_mistake_connect_database,
		$arr_str_mistake_select_table;

	if($pdo_has_error == FLAG_TRUE){
		$str_notice = $arr_str_mistake_connect_database[$int_selected_language];
		fail_and_redirect_home($str_notice,$int_selected_language);
	}

	if($query_has_error == FLAG_TRUE){
		$str_notice = $arr_str_mistake_select_table[$int_selected_language];
		fail_and_redirect_home($str_notice,$int_selected_language);
	}
}


function handle_database_error_and_exit($pdo_has_error, $query_has_error, $e = null){
	if($pdo_has_error == FLAG_TRUE || $query_has_error == FLAG_TRUE){
		exit();
	}
}


function handle_database_error_and_respond($pdo_has_error, $query_has_error, $e = null) {

    if ($pdo_has_error == FLAG_TRUE || $query_has_error == FLAG_TRUE || $e !== null) {
		
		$logFile = __DIR__ . '/database_error.log';
		$log = [];
		$log[] = date('[Y-m-d H:i:s]');
		$log[] = 'PDO_ERROR=' . (int)$pdo_has_error;
		$log[] = 'QUERY_ERROR=' . (int)$query_has_error;
		if ($strSQL !== null) {
			$log[] = 'SQL=' . $strSQL;
		}
		if ($arr_bind_values !== null) {
			$log[] = 'BIND_VALUES=' . json_encode(
				$arr_bind_values,
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
			);
		}
		if ($e instanceof Throwable) {
			$log[] = 'EXCEPTION=' . $e->getMessage();
			$log[] = 'FILE=' . $e->getFile();
			$log[] = 'LINE=' . $e->getLine();
		}
		$log[] = str_repeat('-', 80);
		file_put_contents(
			$logFile,
			implode(' | ', $log) . PHP_EOL,
			FILE_APPEND | LOCK_EX
		);
		
        respond_error('Database Error', 500);
    }
}


function handle_database_error_log($context, $e = null, $extra = []) {

    $message = '[DB ERROR] ' . $context;

    if ($e instanceof Throwable) {
        $message .= ' | ' . $e->getMessage();
    }

    if (!empty($extra)) {
        $message .= ' | ' . json_encode($extra, JSON_UNESCAPED_UNICODE);
    }

    error_log($message);
}


function handle_database_error_and_throw($pdo_has_error, $query_has_error, $e = null, $tag = '') {

    if ($pdo_has_error == FLAG_TRUE || $query_has_error == FLAG_TRUE || $e !== null) {
        if ($e !== null) {
            error_log($tag . ': ' . (string)$e);
        }
        throw new RuntimeException('Database Error');
    }
}