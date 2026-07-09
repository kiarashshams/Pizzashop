<?php

session_start();

include "config.php";


// Check login

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}


// Check cart

if (!isset($_SESSION["cart"]) || count($_SESSION["cart"]) == 0) {

    header("Location: cart.php");
    exit();

}


$user_id = $_SESSION["user_id"];

$total = 0;


// Calculate total

foreach ($_SESSION["cart"] as $item) {

    $total += $item["price"] * $item["quantity"];

}



try {


    // Start transaction

    $conn->begin_transaction();



    // Insert order

    $stmt = $conn->prepare(
        "INSERT INTO orders (user_id, total_price)
         VALUES (?, ?)"
    );


    $stmt->bind_param(
        "id",
        $user_id,
        $total
    );


    $stmt->execute();



    // Get order id

    $order_id = $conn->insert_id;



    // Insert order items

    $stmtItem = $conn->prepare(

        "INSERT INTO order_items
        (order_id, pizza_id, quantity, price)
        VALUES (?, ?, ?, ?)"

    );



    foreach ($_SESSION["cart"] as $item) {


        $pizza_id = $item["id"];

        $quantity = $item["quantity"];

        $price = $item["price"];



        $stmtItem->bind_param(

            "iiid",

            $order_id,

            $pizza_id,

            $quantity,

            $price

        );


        $stmtItem->execute();


    }



    // Save everything

    $conn->commit();



    // Empty cart

    unset($_SESSION["cart"]);



}

catch(Exception $e){


    // Cancel everything

    $conn->rollback();


    die("Order failed: " . $e->getMessage());


}



include "includes/header.php";

?>


<div class="text-center mt-5">


    <h1 class="text-success">

        ✅ Order Completed!

    </h1>


    <p class="lead">

        Thank you for your order.

    </p>


    <a href="menu.php" class="btn btn-primary">

        Order Again

    </a>


    <a href="profile.php" class="btn btn-success">

        My Profile

    </a>


</div>



<?php

include "includes/footer.php";

?>