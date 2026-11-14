<?php
session_start();
include_once'../setting.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == ''){
	header('location:'.BASE_URL.'');
}else{
	if($_SESSION['user_id'] != "FIDSI")
	header('location:'.BASE_URL.'');
}
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
    body{
        background-color: #efefef;
    }
    .brand-logo img{
        height: 50px;
    }
    </style>
    <title>FIDSI Admin</title>
</head>
<body>
<header class="bg-white shadow-sm mb-4 py-2">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="brand-logo"><img src="../assets/images/logo/Fidsi-logo-black.png" alt=""></div>
            <div class="log-out"><a href="logout.php">Log Out</a></div>
        </div>
    </div>
</header>