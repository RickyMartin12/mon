<?php
$servername = "94.126.144.9";
$username = "sys";
$password = "12345";
$dbname = "tes_mon";

$mon3 = mysqli_connect($servername, $username, $password, $dbname);
if (!$mon3) {
    echo "Failed to connect to MySQL mon3: " . mysqli_connect_error();
}

?>