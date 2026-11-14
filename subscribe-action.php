<?php
include_once 'setting.php';

$email      = $_POST['email'];


$insert = $mysqli->prepare("INSERT INTO subscribe (email) VALUES (?)");
$insert->bind_param("s", $email);
if($insert->execute()){
    echo 200;
}else{
    echo 300;
}
$insert->close();