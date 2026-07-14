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
include "security.php";
include "includes/header.php";

if(
    !isset($_GET["id"]) ||
    !is_numeric($_GET["id"])
){
    header("Location: admin_orders.php");
    exit();
}

$order_id = (int)$_GET["id"];

/*
|--------------------------------------------------------------------------
| Order Information
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
SELECT
    orders.id,
    orders.total_price,
    orders.order_date,
    users.username,
    users.email,
    orders.address,
    orders.phone,
    orders.notes
FROM orders
JOIN users
ON users.id = orders.user_id
WHERE orders.id = ?
");

$stmt->bind_param("i",$order_id);
$stmt->execute();

$order = $stmt->get_result()->fetch_assoc();

if(!$order){

    header("Location: admin_orders.php");
    exit();

}

$stmt->close();

/*
|--------------------------------------------------------------------------
| Order Items
|--------------------------------------------------------------------------
*/

$stmtItems = $conn->prepare("
SELECT
    pizzas.name,
    pizzas.category,
    order_items.quantity,
    order_items.price
FROM order_items
JOIN pizzas
ON pizzas.id = order_items.pizza_id
WHERE order_items.order_id = ?
");

$stmtItems->bind_param("i",$order_id);
$stmtItems->execute();

$items = $stmtItems->get_result();

?>

<div class="container mt-5">

<div class="card shadow-lg">

<div class="card-header bg-dark text-white">

<h3 class="mb-0">

📦 Order Details

</h3>

</div>

<div class="card-body">

<div class="row mb-4">

<div class="col-md-6">

<h5>

Order ID

</h5>

<p>

#<?php echo clean($order["id"]); ?>

</p>

</div>

<div class="col-md-6">

<h5>

Order Date

</h5>

<p>

<?php echo clean($order["order_date"]); ?>

</p>

</div>

</div>

<div class="row mb-4">

<div class="col-md-6">

<h5>

Customer

</h5>

<p>

<?php echo clean($order["username"]); ?>

</p>

</div>

<div class="col-md-6">

<h5>

Email

</h5>

<p>

<?php echo clean($order["email"]); ?>

</p>

</div>


</div>

<div class="row mb-4">

<div class="col-md-6">

<h5>📞 Phone</h5>

<p>

<?php echo clean($order["phone"]); ?>

</p>

</div>

<div class="col-md-6">

<h5>📍 Address</h5>

<p>

<?php echo clean($order["address"]); ?>

</p>

</div>

</div>

<div class="mb-4">

<h5>📝 Order Notes</h5>

<p>

<?php
if(!empty($order["notes"])){
    echo nl2br(clean($order["notes"]));
}else{
    echo "<span class='text-muted'>No notes provided.</span>";
}
?>

</p>

</div>

<hr>

<h4 class="mb-3">

🍕 Ordered Items

</h4>

<div class="table-responsive">

<table class="table table-bordered align-middle">

<thead class="table-danger">

<tr>

<th>Product</th>

<th>Category</th>

<th>Quantity</th>

<th>Price</th>

<th>Subtotal</th>

</tr>

</thead>

<tbody>

<?php

while($item = $items->fetch_assoc()){

$subtotal = $item["price"] * $item["quantity"];

?>

<tr>

<td>

<?php echo clean($item["name"]); ?>

</td>

<td>

<?php echo clean($item["category"]); ?>

</td>

<td>

<?php echo clean($item["quantity"]); ?>

</td>

<td>

€ <?php echo number_format($item["price"],2); ?>

</td>

<td>

€ <?php echo number_format($subtotal,2); ?>

</td>

</tr>

<?php

}

?>

</tbody>

<tfoot>

<tr class="table-success">

<th colspan="4" class="text-end">

Grand Total

</th>

<th>

€ <?php echo number_format($order["total_price"],2); ?>

</th>

</tr>

</tfoot>

</table>

</div>

<div class="mt-4">

<a
href="admin_orders.php"
class="btn btn-secondary">

⬅ Back to Orders

</a>

</div>

</div>

</div>

</div>

<?php

$stmtItems->close();

include "includes/footer.php";

?>