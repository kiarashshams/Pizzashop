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


// Statistics

$totalUsers = $conn->query("
SELECT COUNT(*) AS total
FROM users
")->fetch_assoc()["total"];


$totalOrders = $conn->query("
SELECT COUNT(*) AS total
FROM orders
")->fetch_assoc()["total"];


$totalProducts = $conn->query("
SELECT COUNT(*) AS total
FROM pizzas
")->fetch_assoc()["total"];


$revenue = $conn->query("
SELECT IFNULL(SUM(total_price),0) AS total
FROM orders
")->fetch_assoc()["total"];


$todayOrders = $conn->query("
SELECT COUNT(*) AS total
FROM orders
WHERE DATE(order_date)=CURDATE()
")->fetch_assoc()["total"];

?>

<div class="container mt-5">

<div class="card shadow-lg border-0 mb-5">

<div class="card-body text-center py-5">

<h1 class="fw-bold">

👑 Admin Dashboard

</h1>

<p class="text-muted fs-5">

Welcome,
<b><?php echo clean($_SESSION["username"]); ?></b>

</p>

<p class="text-muted">

Manage your Pizza Shop from one place.

</p>

</div>

</div>



<div class="row g-4">

<div class="col-md-3">

<div class="card shadow h-100 text-center">

<div class="card-body">

<h1>👥</h1>

<h4>

<?php echo $totalUsers; ?>

</h4>

<p>Total Users</p>

<a
href="admin_users.php"
class="btn btn-primary">

Manage Users

</a>

</div>

</div>

</div>



<div class="col-md-3">

<div class="card shadow h-100 text-center">

<div class="card-body">

<h1>📦</h1>

<h4>

<?php echo $totalOrders; ?>

</h4>

<p>Total Orders</p>

<a
href="admin_orders.php"
class="btn btn-success">

Manage Orders

</a>

</div>

</div>

</div>



<div class="col-md-3">

<div class="card shadow h-100 text-center">

<div class="card-body">

<h1>🍕</h1>

<h4>

<?php echo $totalProducts; ?>

</h4>

<p>Products</p>

<a
href="admin_products.php"
class="btn btn-warning">

Manage Products

</a>

</div>

</div>

</div>



<div class="col-md-3">

<div class="card shadow h-100 text-center">

<div class="card-body">

<h1>💰</h1>

<h4>

€ <?php echo number_format($revenue,2); ?>

</h4>

<p>Total Revenue</p>

<button
class="btn btn-dark"
disabled>

Today:
<?php echo $todayOrders; ?>

</button>

</div>

</div>

</div>

</div>



<hr class="my-5">

<h3 class="mb-4">

Quick Actions

</h3>

<div class="d-flex gap-3 flex-wrap">

<a
href="admin_users.php"
class="btn btn-primary">

👥 Users

</a>

<a
href="admin_orders.php"
class="btn btn-success">

📦 Orders

</a>

<a
href="admin_products.php"
class="btn btn-warning">

🍕 Products

</a>

<a
href="profile.php"
class="btn btn-secondary">

👤 My Profile

</a>

<a
href="logout.php"
class="btn btn-danger">

🚪 Logout

</a>

</div>

</div>

<?php

include "includes/footer.php";

?>