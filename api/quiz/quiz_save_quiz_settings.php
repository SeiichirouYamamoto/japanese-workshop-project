<?php

	header('Content-Type: application/json; charset=utf-8');

	require_once __DIR__ . '/../_bootstrap.php';

	try {

		$raw = file_get_contents('php://input');
		$input = json_decode($raw, true);

		if (!is_array($input)) {
			respond_error('Invalid JSON', 400);
		}

		$int_mastery_level = $input['int_mastery_level'] ?? null;

		if ($int_mastery_level === null || $int_mastery_level === '') {
			unset($_SESSION['quiz_settings_mastery_level']);
		} else {
			$_SESSION['quiz_settings_mastery_level'] = (int)$int_mastery_level;
		}

		$arr_sub_category = $input['arr_sub_category'] ?? [];

		if (empty($arr_sub_category)) {
			unset($_SESSION['quiz_settings_sub_category']);
		} else {
			if (!is_array($arr_sub_category)) {
				$arr_sub_category = [$arr_sub_category];
			}
			$arr_sub_category = array_map('intval', $arr_sub_category);
			$_SESSION['quiz_settings_sub_category'] = $arr_sub_category;
		}

		$arr_japanese_classification = $input['arr_japanese_classification'] ?? [];

		if (empty($arr_japanese_classification)) {
			unset($_SESSION['quiz_settings_japanese_classification']);
		} else {
			if (!is_array($arr_japanese_classification)) {
				$arr_japanese_classification = [$arr_japanese_classification];
			}
			$arr_japanese_classification = array_map('intval', $arr_japanese_classification);
			$_SESSION['quiz_settings_japanese_classification'] = $arr_japanese_classification;
		}

		$arr_inflection = $input['arr_inflection'] ?? [];

		if (empty($arr_inflection)) {
			unset($_SESSION['quiz_settings_inflection']);
		} else {
			if (!is_array($arr_inflection)) {
				$arr_inflection = [$arr_inflection];
			}
			$arr_inflection = array_map('intval', $arr_inflection);
			$_SESSION['quiz_settings_inflection'] = $arr_inflection;
		}

		respond_success(['success' => true]);

	} catch (Throwable $e) {
		respond_exception($e);
	}

