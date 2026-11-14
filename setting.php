<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$database_host 			= '127.0.0.1';
$database_username 		= 'mcqvybxr_fidsi_new_u';
$database_password 		= 'skip';
$database_name 			= 'mcqvybxr_fidsi_new';

//open mysql connection
$mysqli = new mysqli($database_host, $database_username, $database_password, $database_name);

//output error and exit if there's an error
if ($mysqli->connect_error) {
    die('Error : ('. $mysqli->connect_errno .') '. $mysqli->connect_error);
}
?>