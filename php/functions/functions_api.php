<?php

function delete_targets_for_manage_page($pdo, $currentUrl, $id, $unique_code, $int_selected_language) {

    global
        $t_wise_navigations,
        $t_wise_navigation_waypoints,
        $t_wise_navigation_scripts,
        $t_wise_navigation_items,
        $t_rooms,
        $t_room_lessons,
        $t_room_lesson_steps,
        $t_room_lesson_step_units,
        $t_room_lesson_contents,
        $t_room_users;

    if (empty($pdo) || !($pdo instanceof PDO)) {
        throw new RuntimeException('PDO not provided');
    }

    $arr_targets = [];

    $path = parse_url($currentUrl, PHP_URL_PATH);
    $page = basename(rtrim($path, '/'));

    switch ($page) {

        case 'manage-wise-navigations':
            $arr_targets = [
                ['table' => $t_wise_navigations, 'column' => 'id'],
                ['table' => $t_wise_navigation_waypoints, 'column' => 'wise_navigation_id'],
                ['table' => $t_wise_navigation_scripts, 'column' => 'wise_navigation_waypoint_id'],
                ['table' => $t_wise_navigation_items, 'column' => 'wise_navigation_script_id']
            ];
            break;

        case 'manage-wise-navigation-waypoints':
            $arr_targets = [
                ['table' => $t_wise_navigation_waypoints, 'column' => 'id'],
                ['table' => $t_wise_navigation_scripts, 'column' => 'wise_navigation_waypoint_id'],
                ['table' => $t_wise_navigation_items, 'column' => 'wise_navigation_script_id']
            ];
            break;

        case 'manage-wise-navigation-scripts':
            $arr_targets = [
                ['table' => $t_wise_navigation_scripts, 'column' => 'id'],
                ['table' => $t_wise_navigation_items, 'column' => 'wise_navigation_script_id']
            ];
            break;

        case 'manage-wise-navigation-items':
            $arr_targets = [
                ['table' => $t_wise_navigation_items, 'column' => 'id']
            ];
            break;

        case 'manage-rooms':
            $arr_targets = [
                ['table' => $t_rooms, 'column' => 'id'],
                ['table' => $t_room_lessons, 'column' => 'room_id'],
                ['table' => $t_room_lesson_steps, 'column' => 'lesson_id'],
                ['table' => $t_room_lesson_step_units, 'column' => 'lesson_step_id'],
                ['table' => $t_room_lesson_contents, 'column' => 'step_unit_id']
            ];
            break;

        case 'manage-room-lessons':
            $arr_targets = [
                ['table' => $t_room_lessons, 'column' => 'id'],
                ['table' => $t_room_lesson_steps, 'column' => 'lesson_id'],
                ['table' => $t_room_lesson_step_units, 'column' => 'lesson_step_id'],
                ['table' => $t_room_lesson_contents, 'column' => 'step_unit_id']
            ];
            break;

        case 'manage-room-lesson-steps':
            $arr_targets = [
                ['table' => $t_room_lesson_steps, 'column' => 'id'],
                ['table' => $t_room_lesson_step_units, 'column' => 'lesson_step_id'],
                ['table' => $t_room_lesson_contents, 'column' => 'step_unit_id']
            ];
            break;

        case 'manage-room-lesson-step-units':
            $arr_targets = [
                ['table' => $t_room_lesson_step_units, 'column' => 'id'],
                ['table' => $t_room_lesson_contents, 'column' => 'step_unit_id']
            ];
            break;

        case 'manage-room-lesson-contents':
            $arr_targets = [
                ['table' => $t_room_lesson_contents, 'column' => 'id']
            ];
            break;

        default:
            throw new InvalidArgumentException('Invalid manage page');
    }

    recursive_delete_targets_for_manage_page($pdo, $arr_targets, intval($id), $int_selected_language);

    if ($page === 'manage-rooms') {
        $sql = 'DELETE FROM ' . $t_room_users . ' WHERE room_id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(1, intval($id), PDO::PARAM_INT);
        $stmt->execute();
    }
}


function recursive_delete_targets_for_manage_page($pdo, $arr_targets, $id, $int_selected_language) {

    if (empty($pdo) || !($pdo instanceof PDO)) {
        throw new RuntimeException('PDO not provided');
    }

    if (empty($arr_targets)) {
        return;
    }

    $target_table = $arr_targets[INDEX_FIRST]['table'];
    $target_column = $arr_targets[INDEX_FIRST]['column'];
    $next_arr_targets = array_slice($arr_targets, 1);

    $sql_select = 'SELECT id FROM ' . $target_table . ' WHERE ' . $target_column . ' = ?';
    $stmt_select = $pdo->prepare($sql_select);
    $stmt_select->bindValue(1, intval($id), PDO::PARAM_INT);
    $stmt_select->execute();
    $arr_child_ids = $stmt_select->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($next_arr_targets) && !empty($arr_child_ids)) {
        foreach ($arr_child_ids as $loop) {
            $child_id = intval($loop['id']);
            recursive_delete_targets_for_manage_page($pdo, $next_arr_targets, $child_id, $int_selected_language);
        }
    }

    $sql_delete = 'DELETE FROM ' . $target_table . ' WHERE ' . $target_column . ' = ?';
    $stmt_delete = $pdo->prepare($sql_delete);
    $stmt_delete->bindValue(1, intval($id), PDO::PARAM_INT);
    $stmt_delete->execute();
}



/******************************************************
 *  RESOET
 *  
 ******************************************************/
function resort_records($isPreviousAsNumber, $targets, $target_id, $update_table) {

    $target_id = intval($target_id);
    if ($target_id <= 0) {
        return;
    }

    $targetIndex = -1;
    foreach ($targets as $index => $target) {
        if (intval($target['id'] ?? 0) === $target_id) {
            $targetIndex = $index;
            break;
        }
    }

    if ($targetIndex === -1) {
        return;
    }

    $swapIndex = null;

    if ($isPreviousAsNumber === FLAG_FALSE && $targetIndex < count($targets) - 1) {
        $swapIndex = $targetIndex + 1;
    } elseif ($isPreviousAsNumber === FLAG_TRUE && $targetIndex > INDEX_FIRST) {
        $swapIndex = $targetIndex - 1;
    } else {
        return;
    }

    $swapTarget = $targets[$swapIndex];
    $targets[$swapIndex] = $targets[$targetIndex];
    $targets[$targetIndex] = $swapTarget;
    $targets = array_values($targets);

    $pdo = null;

    try {

        $pdo = connect_to_database();
        if (empty($pdo) || !($pdo instanceof PDO)) {
            throw new Exception('Database connection failed');
        }

        $pdo->beginTransaction();

        $sql = 'UPDATE ' . $update_table . ' SET sort = ? WHERE id = ?';
        $stmt = $pdo->prepare($sql);

        foreach ($targets as $index => $target) {
            $id = intval($target['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Invalid target id');
            }

            $stmt->bindValue(1, intval($index), PDO::PARAM_INT);
            $stmt->bindValue(2, $id, PDO::PARAM_INT);
            $stmt->execute();
        }

        $pdo->commit();
        $pdo = null;

    } catch (Throwable $e) {

        if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
            $pdo->rollBack();
        }

        throw $e;
    }
}
