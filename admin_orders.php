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

$query = "
SELECT
    orders.id,
    users.username,
    orders.total_price,
    orders.order_date,
    COUNT(order_items.id) AS items
FROM orders
JOIN users
ON orders.user_id = users.id
LEFT JOIN order_items
ON orders.id = order_items.order_id
GROUP BY orders.id
ORDER BY orders.order_date DESC
";

$result = $conn->query($query);

?>

<div class="container mt-4">

<h2 class="mb-4">

📦 Order Management

</h2>

<?php

if($result->num_rows==0){

?>

<div class="alert alert-warning">

No orders found.

</div>

<?php

}else{

?>

<div class="table-responsive">

<table class="table table-hover align-middle">

<thead class="table-dark">

<tr>

<th>Order ID</th>

<th>Customer</th>

<th>Items</th>

<th>Total</th>

<th>Date</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

while($order=$result->fetch_assoc()){

?>

<tr>

<td>

#<?php echo $order["id"];?>

</td>

<td>

<?php echo clean($order["username"]);?>

</td>

<td>

<?php echo $order["items"];?>

</td>

<td>

€ <?php echo number_format($order["total_price"],2);?>

</td>

<td>

<?php echo clean($order["order_date"]);?>

</td>

<td>

<a
href="view_order.php?id=<?php echo $order["id"];?>"
class="btn btn-primary btn-sm">

View Details

</a>

</td>

</tr>

<?php

}

?>

</tbody>

</table>

</div>

<?php

}

?>

</div>

<?php

include "includes/footer.php";

?>