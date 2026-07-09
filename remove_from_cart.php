<?php

session_start();


if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: cart.php");
    exit();

}


$id = $_GET["id"];



if(isset($_SESSION["cart"][$id])){


    unset($_SESSION["cart"][$id]);


}



header("Location: cart.php");

exit();


?>