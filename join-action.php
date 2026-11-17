<?php
include_once 'setting.php';

$co_name    = $_POST['co_name'];
$co_website = $_POST['co_website'];
$co_nature  = $_POST['co_nature'];
$name       = $_POST['name'];
$mobile     = $_POST['mobile'];
$email      = $_POST['email'];
$message    = $_POST['message'];

$insert = $mysqli->prepare("INSERT INTO fds_join (co_name, co_website, co_nature, name, mobile, email, message) VALUES (?,?,?,?,?,?,?)");
$insert->bind_param("sssssss", $co_name, $co_website, $co_nature, $name, $mobile, $email, $message);
if($insert->execute()){
    echo 200;
}else{
    echo 300;
}
$insert->close();