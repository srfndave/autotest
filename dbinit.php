<?php

$ini = parse_ini_file('app.ini');

$servername = $ini['db_server'];
$username = $ini['db_user'];
$password = $ini['db_password'];
$dbname = $ini['db_name'];

global $pdo;

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
