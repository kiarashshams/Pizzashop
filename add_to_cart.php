<?php

session_start();

include "config.php";

// Check id from URL
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {

    header("Location: menu.php");
    exit();

}

$id = (int)$_GET["id"];

// Get product
$stmt = $conn->prepare("
SELECT id, category, in_stock
FROM pizzas
WHERE id = ?
");

$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){

    header("Location: menu.php");
    exit();

}

$product = $result->fetch_assoc();

$stmt->close();

// Check stock
if($product["in_stock"] == 0){

    $_SESSION["cart_error"] =
    "This product is currently out of stock.";

    header("Location: menu.php");
    exit();

}

// Create cart if needed
if(!isset($_SESSION["cart"])){

    $_SESSION["cart"] = [];

}

// Calculate totals
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

    if($row["category"] == "Pizza"){

        $totalPizza += $item["quantity"];

    }else{

        $totalDrink += $item["quantity"];

    }

    $stmt->close();

}

// Check limits
if($product["category"] == "Pizza" && $totalPizza >= 30){

    $_SESSION["cart_error"] =
    "Maximum 30 pizzas are allowed in one order.";

    header("Location: cart.php");
    exit();

}

if($product["category"] == "Drink" && $totalDrink >= 40){

    $_SESSION["cart_error"] =
    "Maximum 40 drinks are allowed in one order.";

    header("Location: cart.php");
    exit();

}

// Add item
if(isset($_SESSION["cart"][$id])){

    $_SESSION["cart"][$id]["quantity"]++;

}
else{

    $_SESSION["cart"][$id] = [

        "id" => $product["id"],
        "quantity" => 1

    ];

}

header("Location: menu.php");
exit();

?>