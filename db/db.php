<?php


$mysqli = new mysqli('mariadb', 'study', '12345a,*', 'study');
if ($mysqli->connect_errno) {
    throw new RuntimeException('mysqli connection error: ' . $mysqli->connect_error);
}

