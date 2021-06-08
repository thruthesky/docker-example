<?php

//에러 디버깅을 위한 사용자 함수
function debug_log(mixed $msg)
{
    if (is_array($msg) || is_object($msg)) {
        $msg = print_r($msg, true);
    }
    error_log("$msg\n", 3, "/docker/logs/php_runtime.log");
}