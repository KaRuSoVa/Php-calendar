<?php
// database setting

$sname = "localhost"; // sunucu
$unmae = "root"; // username
$password = ""; // password

$db_name = "offer"; // database name

$conn = mysqli_connect($sname, $unmae, $password, $db_name);

if (!$conn) {
    echo "connection error";
}
$conn->set_charset("utf8");