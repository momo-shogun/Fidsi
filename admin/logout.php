<?php
session_start();
include_once __DIR__ . '/../setting.php';
session_destroy();
header('Location: /admin');
?>