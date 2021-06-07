<?php
include 'functions.php';

debug_log("New php runtime: ".$_SERVER["REQUEST_URI"]." at: ".date('r'));
if ( str_contains($_SERVER["REQUEST_URI"], "favicon.ico") ) {
    debug_log("favicon.ico -> exit");
    exit;
}

include 'db/db.php';

db()->insert('users', ['name' => '000uuu', 'age' => 34, 'address' => 'seoul']);

// db()->update();
// db()->delete();
// db()->rows();
// db()->row();
// db()->column();






