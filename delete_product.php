<?php
session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["is_admin"]!=1){
    header("Location: login.php");
    exit();
}

include "config.php";

if($_SERVER["REQUEST_METHOD"]!="POST"){
    header("Location: admin_products.php");
    exit();
}

if(!isset($_POST["id"]) || !is_numeric($_POST["id"])){
    header("Location: admin_products.php");
    exit();
}

$id=(int)$_POST["id"];

$get=$conn->prepare("SELECT image FROM pizzas WHERE id=?");
$get->bind_param("i",$id);
$get->execute();
$product=$get->get_result()->fetch_assoc();
$get->close();

$delete=$conn->prepare("DELETE FROM pizzas WHERE id=?");
$delete->bind_param("i",$id);

if($delete->execute()){

    $file="assets/images/".$product["image"];

    if(file_exists($file)){
        @unlink($file);
    }

}

$delete->close();

header("Location: admin_products.php");
exit();
?>