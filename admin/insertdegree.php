<?php

//Include config parameters in order to connect to the database
require_once("../config.inc.php");

//Connect to the server
$mysqli = new mysqli($DB_server, $DB_username, $DB_password, $DB_dbname);

if ($mysqli->connect_errno)
    die("Unable to connect to database.");

if ($stmt = $mysqli->prepare("INSERT INTO `degrees` (`name`) VALUES (?)"))
{
    $stmt->bind_param("s", $_POST["degree_name"]);
    $stmt->execute();
    $stmt->close();
}

echo "Degree added, id " . $mysqli->insert_id;

$mysqli->close();

?>