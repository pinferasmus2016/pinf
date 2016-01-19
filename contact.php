<?php
if(isset($_POST['name']) && isset($_POST["mail"]) && isset($_POST["message"]))
{
    $message = $_POST['name'] . " (" . $_POST['mail'] . ") says:\n" . $_POST["message"];
    
    mail("pinferasmus2016@gmail.com", "Contact form", $message);
    
    echo "Message was sent.";
}
?>