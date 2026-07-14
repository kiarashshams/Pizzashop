<?php

session_start();

include "config.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: cart.php");
    exit();

}

$id = (int)$_GET["id"];

$action = $_GET["action"] ?? "";

if(!isset($_SESSION["cart"][$id])){

    header("Location: cart.php");
    exit();

}

// Get current product category

$stmt = $conn->prepare("
SELECT category
FROM pizzas
WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$product = $stmt->get_result()->fetch_assoc();

$stmt->close();

if(!$product){

    header("Location: cart.php");
    exit();

}

if($action == "increase"){

    $totalPizza = 0;
    $totalDrink = 0;

    foreach($_SESSION["cart"] as $item){

        $stmt = $conn->prepare("
        SELECT category
        FROM pizzas
        WHERE id = ?
        ");

        $stmt->bind_param("i", $item["id"]);
        $stmt->execute();

        $row = $stmt->get_result()->fetch_assoc();

        if($row){

            if($row["category"] == "Pizza"){

                $totalPizza += $item["quantity"];

            }
            elseif($row["category"] == "Drink"){

                $totalDrink += $item["quantity"];

            }

        }

        $stmt->close();

    }

    if(
        $product["category"] == "Pizza" &&
        $totalPizza >= 30
    ){

        $_SESSION["cart_error"] =
        "Maximum 30 pizzas are allowed in one order.";

    }
    elseif(
        $product["category"] == "Drink" &&
        $totalDrink >= 40
    ){

        $_SESSION["cart_error"] =
        "Maximum 40 drinks are allowed in one order.";

    }
    else{

        $_SESSION["cart"][$id]["quantity"]++;

    }

}

elseif($action == "decrease"){

    if($_SESSION["cart"][$id]["quantity"] > 1){

        $_SESSION["cart"][$id]["quantity"]--;

    }
    else{

        unset($_SESSION["cart"][$id]);

    }

}

header("Location: cart.php");

exit();

?>