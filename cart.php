<?php

session_start();

include "config.php";
include "security.php";
include "includes/header.php";

?>

<div class="container">

<?php

if(isset($_SESSION["cart_error"])){

?>

<div class="alert alert-warning text-center">

<?php

echo clean($_SESSION["cart_error"]);

unset($_SESSION["cart_error"]);

?>

</div>

<?php

}

?>

<h1 class="mb-4 text-center">

🛒 Your Cart

</h1>

<?php

if(!isset($_SESSION["cart"]) || count($_SESSION["cart"]) == 0){

?>

<div class="alert alert-warning text-center">

Your cart is empty.

</div>

<?php

}
else{

$total = 0;

?>

<div class="table-responsive">

<table class="table table-bordered text-center align-middle">

<thead class="table-danger">

<tr>

<th>Item</th>

<th>Price</th>

<th>Quantity</th>

<th>Total</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

foreach($_SESSION["cart"] as $item){

    $stmt = $conn->prepare("
    SELECT id, name, price
    FROM pizzas
    WHERE id = ?
    ");

    $stmt->bind_param("i", $item["id"]);
    $stmt->execute();

    $pizza = $stmt->get_result()->fetch_assoc();

    if(!$pizza){
        $stmt->close();
        continue;
    }

    $subtotal = $pizza["price"] * $item["quantity"];

    $total += $subtotal;

?>

<tr>

<td>

<?php echo clean($pizza["name"]); ?>

</td>

<td>

€ <?php echo number_format($pizza["price"],2); ?>

</td>

<td>

<a
href="update_cart.php?id=<?php echo $pizza["id"]; ?>&action=decrease"
class="btn btn-sm btn-danger">

-

</a>

<span class="mx-3 fw-bold">

<?php echo clean($item["quantity"]); ?>

</span>

<a
href="update_cart.php?id=<?php echo $pizza["id"]; ?>&action=increase"
class="btn btn-sm btn-success">

+

</a>

</td>

<td>

€ <?php echo number_format($subtotal,2); ?>

</td>

<td>

<a
href="remove_from_cart.php?id=<?php echo $pizza["id"]; ?>"
class="btn btn-outline-danger btn-sm">

Remove

</a>

</td>

</tr>

<?php

$stmt->close();

}

?>

</tbody>

</table>

</div>

<h3 class="text-end">

Total :

<span class="text-danger">

€ <?php echo number_format($total,2); ?>

</span>

</h3>

<div class="text-end mt-3">

<a
href="menu.php"
class="btn btn-secondary">

Continue Shopping

</a>

<a
href="checkout.php"
class="btn btn-danger">

Checkout

</a>

</div>

<?php

}

?>

</div>

<?php

include "includes/footer.php";

?>