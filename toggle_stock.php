<?php

session_start();

if(
    !isset($_SESSION["user_id"]) ||
    $_SESSION["is_admin"] != 1
){

    header("Location: login.php");
    exit();

}

include "config.php";

if(
    !isset($_GET["id"]) ||
    !is_numeric($_GET["id"])
){

    header("Location: admin_products.php");
    exit();

}

$id = (int)$_GET["id"];

// Get current status

$stmt = $conn->prepare("
SELECT in_stock
FROM pizzas
WHERE id = ?
");

$stmt->bind_param("i",$id);

$stmt->execute();

$result = $stmt->get_result();

if($result->num_rows == 0){

    $stmt->close();

    header("Location: admin_products.php");
    exit();

}

$product = $result->fetch_assoc();

$stmt->close();

// Toggle value

$newStatus = ($product["in_stock"] == 1) ? 0 : 1;

// Update

$update = $conn->prepare("
UPDATE pizzas
SET in_stock = ?
WHERE id = ?
");

$update->bind_param(
    "ii",
    $newStatus,
    $id
);

$update->execute();

$update->close();

header("Location: admin_products.php");

exit();

?>