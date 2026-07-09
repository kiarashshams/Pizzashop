<?php

session_start();

include "config.php";


// Check id from URL

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: menu.php");
    exit();

}


$id = $_GET["id"];



// Prepared Statement

$stmt = $conn->prepare(
    "SELECT * FROM pizzas WHERE id = ?"
);


$stmt->bind_param("i", $id);


$stmt->execute();


$result = $stmt->get_result();



if($result->num_rows == 0){

    header("Location: menu.php");
    exit();

}


$pizza = $result->fetch_assoc();





if(!isset($_SESSION["cart"])){

    $_SESSION["cart"] = [];

}





if(isset($_SESSION["cart"][$id])){


    $_SESSION["cart"][$id]["quantity"]++;


}
else{


    $_SESSION["cart"][$id] = [


        "id" => $pizza["id"],


        "name" => $pizza["name"],


        "price" => $pizza["price"],


        "quantity" => 1


    ];


}



$stmt->close();



header("Location: cart.php");

exit();


?>