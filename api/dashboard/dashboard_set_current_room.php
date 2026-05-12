<?php

	header('Content-Type: application/json; charset=utf-8');

	require_once __DIR__ . '/../_bootstrap.php';

	try {

		$user_level = get_user_level();
		if ($user_level === null) {
			respond_error('Login required', 401);
		}

		$raw = file_get_contents('php://input');
		$input = json_decode($raw, true);

		if (!is_array($input)) {
			respond_error('Invalid JSON', 400);
		}

		if (!isset($input['int_selected_language'])) {
			respond_error('Value not found: int_selected_language', 400);
		}

		if (!isset($input['room_unique_code'])) {
			respond_error('Value not found: room_unique_code', 400);
		}

		$int_selected_language = (int)$input['int_selected_language'];
		$room_unique_code = trim((string)$input['room_unique_code']);

		if ($room_unique_code === '') {
			respond_error('Invalid room_unique_code', 400);
		}

		$room_id = fetch_room_id_from_unique_code($room_unique_code, $int_selected_language);
		if ($room_id <= 0) {
			respond_error('Room not found', 404);
		}

		$can_access = ensure_user_can_access_room(
			$room_id,
			$int_selected_language
		);

		if (!$can_access) {
			respond_error('Forbidden', 403);
		}

		$_SESSION['dashboard']['room_unique_code'] = $room_unique_code;

		respond_success([
			'success' => true
		]);

	} catch (Throwable $e) {

		respond_exception($e);
	}
