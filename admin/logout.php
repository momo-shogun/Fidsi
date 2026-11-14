<?php
session_start();
include_once'../setting.php';
session_destroy();
header('location:index.php');
?>