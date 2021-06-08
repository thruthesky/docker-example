<?php
include 'functions.php';

$uri = $_SERVER["REQUEST_URI"] ?? 'at CLI';
debug_log("New php runtime: $uri at: " . date('r'));
if (str_contains($uri, "favicon.ico")) {
    debug_log("favicon.ico -> exit");
    exit;
}

include 'db/db.php';

db()->query("TRUNCATE users");

//
//insert
for ($i = 1; $i < 6; $i++) {
    db()->insert('users', ['name' => 'Person' . $i, 'age' => 30 + $i, 'address' => 'seoul']);
}

$rows = db()->rows("users", ['address' => 'seoul']);
print_r($rows);

//delete 1 - 조건 1개
//db()->delete(table: 'users', conds: ['age' => 31]);

//delete 2 - 조건 2개
//db()->delete(table: 'users', conds: "name = 'Person2' OR  age = 33");

//delete 3 - 조건 3개 이상
//db()->delete(table: 'users', conds: "name = 'Person4' AND  age = 34 AND address = 'seoul'");

//update 조건 맞는 모든 데이터 업데이트 - 조건 X
// db()->update('user', ['name' => 'gg', 'age' => 35, 'address' => 'gangnam']);
//
////select 조건 맞는 모든 데이터 조회 - 조건 X
//db()->rows();
//
////select 조건에 맞는 데이터 1개만 가져오기 - 조건1개
//db()->row();
//
////select 조건에 맞는 데이터 1개만 가져오기 - 조건2개
//db()->row();
//
////select 조건에 맞는 데이터 1개만 가져오기 - 조건3개 이상
//db()->row();
//
//
////select 조건에 맞는 데이터 중 특정 컬럼 값만 가져오기 - 조건 X
//db()->column();
//
////select 조건에 맞는 데이터 중 특정 컬럼 값만 가져오기 - 조건 1개
//db()->column();
//
////select 조건에 맞는 데이터 중 특정 컬럼 값만 가져오기 - 조건 2개
//db()->column();
//
////select 조건에 맞는 데이터 중 특정 컬럼 값만 가져오기 - 조건 3개 이상
//db()->column();
//