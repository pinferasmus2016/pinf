<?php

//Include config parameters in order to connect to the database
require_once("../config.inc.php");

//Connect to the server
$mysqli = new mysqli($DB_server, $DB_username, $DB_password, $DB_dbname);

if ($mysqli->connect_errno)
    die("Unable to connect to database.");

if ($stmt = $mysqli->prepare("INSERT INTO `subjects`(`code`, `name`, `credits`, `language`, `semester`, `keywords`) VALUES (?,?,?,?,?,?)"))
{
    $stmt->bind_param("ssisss", $_POST["subject_code"], $_POST["subject_name"], $_POST["subject_ects"], $_POST["subject_language"], $_POST["subject_semester"], $_POST["subject_keywords"]);
    $stmt->execute();
    $stmt->close();
}

$subject_id = $mysqli->insert_id;

if ($stmt = $mysqli->prepare("INSERT INTO `uni_deg_sub`(`id_university`,`id_degree`,`id_subject`) VALUES (?,?,?)"))
{
    $stmt->bind_param("iii", $_POST["university"], $_POST["degree"], $subject_id);
    $stmt->execute();
    $stmt->close();
}

echo "Subject added, id " . $subject_id;

$mysqli->close();

?>