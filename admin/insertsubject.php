<?php

//Include config parameters in order to connect to the database
require_once("../config.inc.php");

//Connect to the server
$mysqli = new mysqli($DB_server, $DB_username, $DB_password, $DB_dbname);

if ($mysqli->connect_errno)
    die("Unable to connect to database.");

$data = array();
$data[] = $_POST["subject_code"];
$data[] = $_POST["subject_name"];
$data[] = $_POST["subject_ects"];
$data[] = $_POST["subject_language"];
$data[] = $_POST["subject_semester"];
$data[] = $_POST["subject_keywords"];

$stringData = implode("','", $data);

$mysqli->query("INSERT INTO `subjects`(`code`, `name`, `credits`, `language`, `semester`, `keywords`) VALUES ('" . $stringData . "')") or die($mysqli->error);

$subject_id = $mysqli->insert_id;

$mysqli->query("INSERT INTO `uni_deg_sub`(`id_university`,`id_degree`,`id_subject`) VALUES ('" . $_POST["university"] . "','" . $_POST["degree"] . "','" . $subject_id . "')") or die($mysqli->error);

echo "Subject added, id " . $subject_id;

$mysqli->close();

?>