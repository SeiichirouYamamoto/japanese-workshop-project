<?php
// wp-load.php を上位階層へ探索して読み込む（APIの場所が変わっても壊れにくい）
$dir = __DIR__;
$wp_load = null;

for ($i = 0; $i < 20; $i++) { // 念のため上限
    $candidate = $dir . '/wp-load.php';
    if (file_exists($candidate)) {
        $wp_load = $candidate;
        break;
    }
    $parent = dirname($dir);
    if ($parent === $dir) break; // ルートまで来た
    $dir = $parent;
}

if ($wp_load === null) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'wp-load.php not found']);
    exit;
}

require_once $wp_load;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}