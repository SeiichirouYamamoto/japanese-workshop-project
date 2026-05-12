<?php

// 定数・変数
require_once dirname(__FILE__) . '/jws_constants.php';
require_once dirname(__FILE__) . '/jws_variables.php';


$first_layers = [
    'functions_database',
    'functions_common',
    'functions_page',
    'functions_api'
];

$last_layers = [
    'functions_shortcodes',
    'functions_actions',
    'functions_filters',
    'functions_hooks'
];

jws_require_php_dir_with_ordered_layers(
    '/php/functions',
    $first_layers,
    $last_layers,
    'functions_'
);

/**
 * 指定ディレクトリ内の PHP を require_once する（first / mid / last の順序制御あり）
 *
 * - first_layers: 必ず先頭で、配列の順番どおりに require
 * - mid: ディレクトリ内の残りを natsort で require
 * - last_layers: 必ず末尾で、配列の順番どおりに require
 *
 * @param string $dir_rel       例: '/php/functions' または 'php/functions'
 * @param array  $first_layers  例: ['functions_database', 'functions_common', ...]
 * @param array  $last_layers   例: ['functions_shortcodes', 'functions_actions', ...]
 * @param string $prefix        対象ファイル名の接頭辞（例: 'functions_'）
 * @return array require した「ハンドル（拡張子なし）」の配列
 */
function jws_require_php_dir_with_ordered_layers($dir_rel, $first_layers, $last_layers, $prefix = 'functions_') {

    $dir_rel = '/' . ltrim($dir_rel, '/');

    $dir_abs = dirname(__FILE__) . $dir_rel;

    if (!is_dir($dir_abs)) {
        return [];
    }

    $files = glob($dir_abs . '/*.php');
    if ($files === false) {
        return [];
    }

    natsort($files);

    $first_set = array_fill_keys($first_layers, true);
    $last_set = array_fill_keys($last_layers, true);

    $mid_files = [];
    foreach ($files as $abs_path) {

        $base_name = basename($abs_path);
        $handle = pathinfo($base_name, PATHINFO_FILENAME);

        // 接頭辞が違うものは除外したい場合（必要なければこの if は削除OK）
        if ($prefix !== '' && strpos($handle, $prefix) !== 0) {
            continue;
        }

        if (isset($first_set[$handle]) || isset($last_set[$handle])) {
            continue;
        }

        $mid_files[] = [
            'handle' => $handle,
            'abs' => $abs_path
        ];
    }

    $required_first = [];
    $required_mid = [];
    $required_last = [];

    // 1) First layers: order guaranteed
    foreach ($first_layers as $handle) {

        $abs = $dir_abs . '/' . $handle . '.php';

        if (!file_exists($abs)) {
            continue;
        }

        require_once $abs;
        $required_first[] = $handle;
    }

    // 2) Middle: natsort 済みの残り（順序はファイル名基準）
    foreach ($mid_files as $info) {

        if (!file_exists($info['abs'])) {
            continue;
        }

        require_once $info['abs'];
        $required_mid[] = $info['handle'];
    }

    // 3) Last layers: order guaranteed
    foreach ($last_layers as $handle) {

        $abs = $dir_abs . '/' . $handle . '.php';

        if (!file_exists($abs)) {
            continue;
        }

        require_once $abs;
        $required_last[] = $handle;
    }

    return array_merge($required_first, $required_mid, $required_last);
}
