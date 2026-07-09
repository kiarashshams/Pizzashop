<?php

session_start();
include "config.php";

if (!isset($_SESSION["cart"]) || empty($_SESSION["cart"])) {
    echo "Cart is empty!";
    exit();
}

$cart = $_SESSION["cart"];

?>

<h2>Your Cart 🛒</h2>

<?php

$total = 0;

foreach ($cart as $pizza_id => $qty) {

    $sql = "SELECT * FROM pizzas WHERE id=$pizza_id";
    $result = $conn->query($sql);
    $pizza = $result->fetch_assoc();

    $price = $pizza["price"] * $qty;
    $total += $price;

    echo "<p>";
    echo $pizza["name"] . " - Qty: " . $qty . " - Price: €" . $price;
    echo "</p>";
}

echo "<hr>";
echo "<h3>Total: €" . $total . "</h3>";

?>