<?php

session_start();


if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: cart.php");
    exit();

}


$id = $_GET["id"];


$action = $_GET["action"] ?? "";



if(isset($_SESSION["cart"][$id])){


    if($action == "increase"){


        $_SESSION["cart"][$id]["quantity"]++;


    }



    elseif($action == "decrease"){


        $_SESSION["cart"][$id]["quantity"]--;



        if($_SESSION["cart"][$id]["quantity"] <= 0){


            unset($_SESSION["cart"][$id]);


        }


    }


}



header("Location: cart.php");

exit();


?>