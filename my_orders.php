<?php

session_start();

if(!isset($_SESSION["user_id"])){

    header("Location: login.php");
    exit();

}

include "config.php";
include "security.php";
include "includes/header.php";

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
SELECT *
FROM orders
WHERE user_id = ?
ORDER BY order_date DESC
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$orders = $stmt->get_result();

?>

<div class="container mt-5">

<h2 class="mb-4">

📦 My Orders

</h2>

<?php

if($orders->num_rows == 0){

?>

<div class="alert alert-warning">

You don't have any orders yet.

</div>

<?php

}

while($order = $orders->fetch_assoc()){

?>

<div class="card shadow mb-4">

<div class="card-header bg-danger text-white">

<div class="d-flex justify-content-between">

<div>

<strong>

Order #<?php echo clean($order["id"]); ?>

</strong>

</div>

<div>

<?php echo clean($order["order_date"]); ?>

</div>

</div>

</div>

<div class="card-body">


<div class="row mb-3">

<div class="col-md-6">

<strong>📞 Phone</strong><br>

<?php echo clean($order["phone"]); ?>

</div>

<div class="col-md-6">

<strong>📍 Address</strong><br>

<?php echo clean($order["address"]); ?>

</div>

</div>

<div class="mb-3">

<strong>📝 Order Notes</strong><br>

<?php
if(!empty($order["notes"])){
    echo nl2br(clean($order["notes"]));
}else{
    echo "<span class='text-muted'>No notes provided.</span>";
}
?>

</div>

<table class="table">

<thead>

<tr>

<th>Item</th>

<th>Quantity</th>

<th>Price</th>

<th>Total</th>

</tr>

</thead>

<tbody>

<?php

$itemStmt = $conn->prepare("

SELECT
p.name,
oi.quantity,
oi.price

FROM order_items oi

JOIN pizzas p

ON oi.pizza_id = p.id

WHERE oi.order_id = ?

");

$itemStmt->bind_param("i",$order["id"]);
$itemStmt->execute();

$items = $itemStmt->get_result();

while($item = $items->fetch_assoc()){

?>

<tr>

<td>

<?php echo clean($item["name"]); ?>

</td>

<td>

<?php echo clean($item["quantity"]); ?>

</td>

<td>

€ <?php echo number_format($item["price"],2); ?>

</td>

<td>

€ <?php echo number_format($item["price"]*$item["quantity"],2); ?>

</td>

</tr>

<?php

}

$itemStmt->close();

?>

</tbody>

</table>

<div class="text-end">

<h5>

Total :

<span class="text-danger">

€ <?php echo number_format($order["total_price"],2); ?>

</span>

</h5>

</div>

</div>

</div>

<?php

}

$stmt->close();

?>

</div>

<?php

include "includes/footer.php";

?>