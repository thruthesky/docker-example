<?php
include 'functions.php';
//
$uri = $_SERVER["REQUEST_URI"] ?? 'at CLI';
echo $uri;
debug_log("New php runtime: $uri at: " . date('r'));
if (str_contains($uri, "favicon.ico")) {
    debug_log("favicon.ico -> exit");
    exit;
}

include 'db/db.php';

header("Content-Type: application/json");
if (!isset($_REQUEST['route'])) {
    echo json_encode([
        'response' => 'error_route_is_not_set',
        'request' => $_REQUEST,
    ]);
} else if ($_REQUEST['route'] == 'user.register') {
    include 'controller/user/register.php';
} else if ($_REQUEST['route'] == 'user.login') {
    include 'controller/user/login.php';
}


