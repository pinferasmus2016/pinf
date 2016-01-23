<?php

//Include config parameters in order to connect to the database
require_once("../config.inc.php");

//Connect to the server
$mysqli = new mysqli($DB_server, $DB_username, $DB_password, $DB_dbname);

if ($mysqli->connect_errno)
    die("Unable to connect to database.");

if ($stmt = $mysqli->prepare("INSERT INTO `universities` (`id_country`, `name`, `lat`, `lng`, `city_name`, `img`) VALUES (?, ?, ?, ?, ?, ?)"))
{
    $stmt->bind_param("isddss", $_POST["country"], $_POST["university_name"], $_POST["university_lat"], $_POST["university_lon"], $_POST["university_city"], $_POST["university_img"]);
    $stmt->execute();
    $stmt->close();
}

echo "University added, id " . $mysqli->insert_id;

$mysqli->close();

?>