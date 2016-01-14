<?php

//Include config parameters in order to connect to the database
require_once("../config.inc.php");

//Connect to the server
$mysqli = new mysqli($DB_server, $DB_username, $DB_password, $DB_dbname);

if ($mysqli->connect_errno)
    die("Unable to connect to database.");

$data = array();
$data[] = $_POST["country"];
$data[] = $_POST["university_name"];
$data[] = $_POST["university_lat"];
$data[] = $_POST["university_lon"];
$data[] = $_POST["university_city"];
$data[] = $_POST["university_img"];

$stringData = implode("','", $data);

$mysqli->query("INSERT INTO `universities` (`id_country`, `name`, `lat`, `lng`, `city_name`, `img`) VALUES ('" . $stringData . "')") or die($mysqli->error);

echo "University added, id " . $mysqli->insert_id;

$mysqli->close();

?>