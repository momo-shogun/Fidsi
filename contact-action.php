<?php
include_once 'setting.php';

$name       = $_POST['name'];
$mobile     = $_POST['mobile'];
$email      = $_POST['email'];
$subject    = $_POST['subject'];
$message    = $_POST['message'];

$insert = $mysqli->prepare("INSERT INTO fds_contact (name, mobile, email, subject, message) VALUES (?,?,?,?,?)");
$insert->bind_param("sssss", $name, $mobile, $email, $subject, $message);
if($insert->execute()){
    echo 200;
}else{
    echo 300;
}
$insert->close();