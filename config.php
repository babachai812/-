<?php
$host = 'localhost';
$user = 'root';
$password = 'usbw';
$database = 'fitness_db';

$db = new mysqli($host, $user, $password, $database);

if ($db->connect_error) {
    die('Ошибка: ' . $db->connect_error);
}

$db->set_charset('utf8');
session_start();
?>