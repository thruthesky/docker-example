<?php
include 'functions.php';

debug_log("New php runtime begin with: ".$_SERVER["REQUEST_URI"]." at: ".date('r'));
if ( str_contains($_SERVER["REQUEST_URI"], "favicon.ico") ) {
    debug_log("favicon.ico -> exit");
    exit;
}

include 'db/db.php';



/* Prepared statement, stage 1: prepare */
$stmt = $mysqli->stmt_init();
$re = $stmt->prepare("INSERT INTO users(name, age) VALUES (?, ?)");

if ( ! $re ) {
    echo $mysqli->error;
}

$name = "One more";
$age = 33;
$stmt->bind_param("si", $name, $age);

$stmt->execute();
