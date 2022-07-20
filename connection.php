<?php

date_default_timezone_set('Europe/Lisbon');//or change to whatever timezone you want

// Conexao da MON

header('Content-Type: text/html; charset=UTF-8');

$servername = "localhost";
$username = "system";
$password = "lazerx0!";
$dbname = "mon";


// Project MON
if($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '94.126.144.9' || $_SERVER['SERVER_NAME'] == '192.168.11.162' )
{
    define("PROJECT_WEB", $_SERVER['DOCUMENT_ROOT']."/mon");
    define("SERVER_WEB", $_SERVER['SERVER_NAME']."/mon/");
}
else if($_SERVER['SERVER_NAME'] == 'mon.lazertelecom.com')
{
    define("PROJECT_WEB", $_SERVER['DOCUMENT_ROOT']);
    define("SERVER_WEB", $_SERVER['SERVER_NAME']."/");
}


error_reporting(E_ALL & ~E_NOTICE);
// & ~E_NOTICE
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

$mon3 = mysqli_connect($servername, $username, $password, $dbname);
if (!$mon3) {
    echo "Failed to connect to MySQL mon3: " . mysqli_connect_error();
}

//header('Access-Control-Allow-Methods: POST');

mysqli_set_charset($mon3,"utf8");

// Conexao da QGIS

$dbname_QGIS = "qgis";

$qgis= mysqli_connect($servername, $username, $password, $dbname_QGIS);
if (!$qgis) {
    echo "Failed to connect to MySQL mon3: " . mysqli_connect_error();
}

header('Access-Control-Allow-Methods: POST');

mysqli_set_charset($qgis,"utf8");

// Conexoes dos utilizadores do acesso a MON
// Utilizador da app MON
$_SERVER['PHP_AUTH_USER'] = 'mihaip';

$localuser=$mon3->query("select * from users where username='".$_SERVER['PHP_AUTH_USER']."' ;")->fetch_assoc();
echo mysqli_error($mon3);

?>