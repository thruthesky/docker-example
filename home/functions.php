<?php

//에러 디버깅을 위한 사용자 함수
function debug_log($msg) {
    error_log("$msg\n", 3, "/docker/logs/php_runtime.log");
}