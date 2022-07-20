<?php
$db_connect =  mysqli_connect("94.126.144.9", "sys", "12345", "tes_mon");
// Evaluate the connection
if (mysqli_connect_errno()) {
    echo mysqli_connect_error();
    exit();
}else{
    //successful connection
    echo "Yes, successful";
}
?>