<?php

session_start();

include "config.php";

if(!isset($_SESSION["user_id"])){
    header("Location: login.php");
    exit();
}

if(!isset($_SESSION["cart"]) || count($_SESSION["cart"])==0){
    header("Location: cart.php");
    exit();
}

$user_id=$_SESSION["user_id"];

if($_SERVER["REQUEST_METHOD"]!="POST"){

include "includes/header.php";

?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card shadow">

<div class="card-body">

<h2 class="mb-4 text-center">🧾 Checkout</h2>

<form method="POST">

<div class="mb-3">
<label class="form-label">📍 Delivery Address</label>
<input
type="text"
name="address"
class="form-control"
required>
</div>

<div class="mb-3">
<label class="form-label">📞 Phone Number</label>
<input
type="text"
name="phone"
class="form-control"
required>
</div>

<div class="mb-3">
<label class="form-label">📝 Order Notes (Optional)</label>
<textarea
name="notes"
class="form-control"
rows="4"
placeholder="Example: No onions, extra spicy..."></textarea>
</div>

<div class="text-end">
<button class="btn btn-danger btn-lg">
✅ Confirm Order
</button>
<a href="cart.php" class="btn btn-secondary btn-lg">
Cancel
</a>
</div>

</form>

</div>

</div>

</div>

</div>

</div>

<?php

include "includes/footer.php";
exit();

}

$address=trim($_POST["address"]);
$phone=trim($_POST["phone"]);
$notes=trim($_POST["notes"]);

$total=0;

foreach($_SESSION["cart"] as $item){

$stmtPrice=$conn->prepare("
SELECT price
FROM pizzas
WHERE id=?
");

$stmtPrice->bind_param("i",$item["id"]);
$stmtPrice->execute();

$pizza=$stmtPrice->get_result()->fetch_assoc();

$total += $pizza["price"]*$item["quantity"];

$stmtPrice->close();

}

try{

$conn->begin_transaction();

$stmt=$conn->prepare("
INSERT INTO orders
(user_id,total_price,address,phone,notes)
VALUES (?,?,?,?,?)
");

$stmt->bind_param(
"idsss",
$user_id,
$total,
$address,
$phone,
$notes
);

$stmt->execute();

$order_id=$conn->insert_id;

$stmtItem=$conn->prepare("
INSERT INTO order_items
(order_id,pizza_id,quantity,price)
VALUES (?,?,?,?)
");

foreach($_SESSION["cart"] as $item){

$pizza_id=$item["id"];
$quantity=$item["quantity"];

$getPrice=$conn->prepare("
SELECT price
FROM pizzas
WHERE id=?
");

$getPrice->bind_param("i",$pizza_id);
$getPrice->execute();

$currentPizza=$getPrice->get_result()->fetch_assoc();

$price=$currentPizza["price"];

$getPrice->close();

$stmtItem->bind_param(
"iiid",
$order_id,
$pizza_id,
$quantity,
$price
);

$stmtItem->execute();

}

$stmtItem->close();

$conn->commit();

unset($_SESSION["cart"]);

}catch(Exception $e){

$conn->rollback();

die("Order failed: ".$e->getMessage());

}

include "includes/header.php";

?>

<div class="container mt-5">

<div class="card shadow-lg border-0">

<div class="card-body text-center py-5">

<img
src="assets/images/order_success.jpg"
class="img-fluid mb-4"
style="max-width:420px;"
alt="Order Success">

<h1 class="text-success fw-bold">

✅ Order Completed!

</h1>

<h4 class="mb-3">

Order #<?php echo $order_id; ?>

</h4>

<p class="lead">

Thank you for your order.

</p>

<p class="text-muted mb-4">

Your delicious meal is now being prepared.

</p>

<a
href="menu.php"
class="btn btn-success btn-lg me-2">

🍕 Order Again

</a>

<a
href="my_orders.php"
class="btn btn-primary btn-lg">

📦 My Orders

</a>

</div>

</div>

</div>

<?php

include "includes/footer.php";

?>