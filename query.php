<?php

//Include config parameters in order to connect to the database
require_once("config.inc.php");

//Connect to the server
$mysqli = new mysqli($DB_server, $DB_username, $DB_password, $DB_dbname);

if ($mysqli->connect_errno)
    die("Unable to connect to database.");

if (isset($_GET["source"]))
{
    switch($_GET["source"])
    {
        case "countries":
            //Get countries
            $query = $mysqli->query("SELECT * FROM `countries`");

            //Parse result to JSON
            $rows = array();
            
            while ($row = mysqli_fetch_assoc($query))
                $rows[] = $row;
            
            echo json_encode($rows);

            break;
            
        case "universities":
            //Get universities: if country is set, then filter by id_country. In other case, get the full list.
            if (isset($_GET["country"]))
                $query = $mysqli->query("SELECT * FROM `universities` WHERE `id_country` = " . $_GET["country"]);
            else
                $query = $mysqli->query("SELECT * FROM `universities`");
            
            //Parse result to JSON
            $rows = array();
            
            while ($row = mysqli_fetch_assoc($query))
                $rows[] = $row;
            
            echo json_encode($rows);
            break;
            
        case "degrees":
            //Get degrees: if university is set, then filter by id_university. In other case, get the full list.
            if (isset($_GET["university"]))
            {
                $query = $mysqli->query("SELECT * FROM `degrees` WHERE `id` in (SELECT `id_degree` FROM `uni_deg_sub` WHERE `id_university`=" . $_GET["university"] . ")");
            
                //Parse result to JSON
                $rows = array();

                while ($row = mysqli_fetch_assoc($query))
                    $rows[] = $row;

                echo json_encode($rows);
            }
            break;
            
        case "subjects":
            //Get subjects. University and Degree need to be specified
            if (isset($_GET["university"]) && isset($_GET["degree"]))
            {
                $query = $mysqli->query("SELECT * FROM `subjects` WHERE `id` in (SELECT `id_subject` FROM `uni_deg_sub` WHERE `id_university`=" . $_GET["university"] . " and `id_degree`=" . $_GET["degree"] . ")");
            
                //Parse result to JSON
                $rows = array();

                while ($row = mysqli_fetch_assoc($query))
                    $rows[] = $row;

                echo json_encode($rows);
            }
            
            break;
            
        case "search":
            //Get subjects that matches the keywords.
            if (isset($_GET["keywords"]))
            {
                //Array where we will store the matching subjects
                $id_list = array();
                
                //Separate keywords
                $search = explode(' ', strtolower($_GET["keywords"]));
                
                //Get keywords
                $query = $mysqli->query("SELECT `id`, `keywords` FROM `subjects`");
                
                //Match search terms with keywords
                while ($row = mysqli_fetch_assoc($query))
                {
                    $words = explode(' ', strtolower($row["keywords"]));
                    
                    //If there is at least one match, store `id` to retrieve later
                    if (!empty(array_intersect($search, $words)))
                        $id_list[] = $row["id"];
                }
                
                //Get subjects
                if (!empty($id_list))
                {
                    $id_list_string = implode(',', $id_list);
                    $query = $mysqli->query("SELECT * from `subjects` WHERE `id` in (" . $id_list_string . ")");
                    
                    //Parse result to JSON
                    $subjects = array();
                    while ($row = mysqli_fetch_assoc($query))
                    {
                        //Set id_university for each subject
                        $subquery = $mysqli->query("SELECT `id_university` FROM `uni_deg_sub` WHERE `id_subject`=" . $row["id"]);
                        $univ = mysqli_fetch_row($subquery);
                        $row["id_university"] = intval($univ[0]);
                        
                        $subjects[] = $row;
                    }
                    
                    $query = $mysqli->query("SELECT * from `universities` WHERE `id` in (SELECT `id_university` FROM `uni_deg_sub` WHERE `id_subject` in (" . $id_list_string . "))");
                    
                    $universities = array();
                    while ($row = mysqli_fetch_assoc($query))
                        $universities[] = $row;
                    
                    $result["subjects"] = $subjects;
                    $result["universities"] = $universities;
                    
                    echo json_encode($result);
                }
            }
            break;
        default:
    }
}

$mysqli->close();
?>