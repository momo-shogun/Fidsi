<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$database_host 			= 'localhost';
$database_username 		= 'mcqvybxr_fds';
$database_password 		= '!LpJV^Mi!;2g@@TG';
$database_name 			= 'mcqvybxr_fds';

//open mysql connection
$mysqli = new mysqli($database_host, $database_username, $database_password, $database_name);

//output error and exit if there's an error
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}
?>