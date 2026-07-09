<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include dirname(__DIR__) . "/security.php";;

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Pizza Shop</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet" href="assets/css/style.css">

</head>


<body>


<nav class="navbar navbar-expand-lg navbar-dark bg-danger">


<div class="container">


<a class="navbar-brand fw-bold" href="index.php">

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

Home

</a>

</li>



<li class="nav-item">

<a class="nav-link" href="menu.php">

Menu

</a>

</li>



<li class="nav-item">

<a class="nav-link" href="cart.php">

🛒 Cart

</a>

</li>



<?php

if(isset($_SESSION["user_id"])){

?>


<li class="nav-item">

<a class="nav-link" href="profile.php">

👤 <?php echo clean($_SESSION["username"]); ?>

</a>

</li>



<li class="nav-item">

<a 
class="nav-link"
href="logout.php"
onclick="return confirm('Are you sure you want to logout?');">

Logout

</a>

</li>



<?php

}

else{

?>


<li class="nav-item">

<a class="nav-link" href="login.php">

Login

</a>

</li>



<li class="nav-item">

<a class="nav-link" href="register.php">

Register

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