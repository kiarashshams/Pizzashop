<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include_once dirname(__DIR__) . "/security.php";

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Pizza Shop</title>

<link rel="icon" type="image/png" href="assets/images/pizza.png">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="preconnect" href="https://fonts.googleapis.com">

<!-- Bootstrap Icons -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<link rel="preconnect" href="https://fonts.googleapis.com">

<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="/Pizzashop/assets/css/style.css">

</head>


<body>

<?php

$cartCount = 0;

if(isset($_SESSION["cart"])){

    foreach($_SESSION["cart"] as $item){

        $cartCount += $item["quantity"];

    }

}

?>

<nav class="navbar navbar-expand-lg navbar-dark bg-danger">


<div class="container">


<a class="navbar-brand fw-bold fs-4" href="index.php">

🍕 Pizza Shop

</a>



<button
class="navbar-toggler"
type="button"
data-bs-toggle="collapse"
data-bs-target="#navbarNav">

<span class="navbar-toggler-icon"></span>

</button>



<div class="collapse navbar-collapse" id="navbarNav">


<ul class="navbar-nav ms-auto">


<li class="nav-item">

<a class="nav-link" href="index.php">
    <i class="bi bi-house-door-fill"></i> Home
</a>

</li>



<li class="nav-item">

<a class="nav-link" href="menu.php">
    <i class="bi bi-grid-fill"></i> Menu
</a>

</li>

<?php if(isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] == 1){ ?>

<li class="nav-item">

<a class="nav-link" href="admin_dashboard.php">

<i class="bi bi-speedometer2"></i>
Admin Dashboard

</a>

</li>

<?php } ?>

<li class="nav-item">

<a class="nav-link position-relative" href="cart.php">

<i class="bi bi-cart-fill"></i> Cart

<?php if($cartCount > 0){ ?>

<span
class="position-absolute top-0 start-100 translate-middle badge rounded-pill cart-badge">

<?php echo $cartCount; ?>

<span class="visually-hidden">
items in cart
</span>

</span>

<?php } ?>

</a>

</li>



<?php

if(isset($_SESSION["user_id"])){

?>


<li class="nav-item">

<a class="nav-link" href="profile.php">
    <i class="bi bi-person-circle"></i>
    <?php echo clean($_SESSION["username"]); ?>
</a>

</li>



<li class="nav-item">

<a class="nav-link"
href="logout.php"
onclick="return confirm('Are you sure you want to logout?');">
    <i class="bi bi-box-arrow-right"></i> Logout
</a>

</li>



<?php

}else{

?>

<li class="nav-item">

<a class="nav-link" href="login.php">
    <i class="bi bi-box-arrow-in-right"></i> Login
</a>

</li>

<li class="nav-item">

<a class="nav-link" href="register.php">
    <i class="bi bi-person-plus-fill"></i> Register
</a>

</li>

<?php

}

?>


</ul>


</div>


</div>


</nav>



<div class="container mt-4">