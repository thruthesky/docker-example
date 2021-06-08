<?php

echo json_encode([
    'response' => [
        'idx' => 1,
        'name' => 'JaeHo Song',
    ],
    'request' => $_REQUEST,
]);

// $user = new UserModel();

// echo $user->register($_REQUEST);