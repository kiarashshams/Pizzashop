<?php

session_start();

include "config.php";
include "security.php";
include "includes/header.php";

$search = "";

if(isset($_GET["search"])){

    $search = trim($_GET["search"]);

}

// Get Pizzas
$stmtPizza = $conn->prepare("
SELECT *
FROM pizzas
WHERE category = ?
AND name LIKE CONCAT('%', ?, '%')
ORDER BY name
");

$categoryPizza = "Pizza";

$stmtPizza->bind_param(
    "ss",
    $categoryPizza,
    $search
);

$stmtPizza->execute();

$pizzas = $stmtPizza->get_result();

// Get Drinks
$stmtDrink = $conn->prepare("
SELECT *
FROM pizzas
WHERE category = ?
AND name LIKE CONCAT('%', ?, '%')
ORDER BY name
");

$categoryDrink = "Drink";

$stmtDrink->bind_param(
    "ss",
    $categoryDrink,
    $search
);

$stmtDrink->execute();

$drinks = $stmtDrink->get_result();

?>

<div class="row mb-4">

<div class="col-md-6 mx-auto">

<form method="GET">

<div class="input-group">

<input
type="text"
name="search"
class="form-control"
placeholder="🔍 Search pizza or drink..."
value="<?php echo isset($_GET["search"]) ? clean($_GET["search"]) : ""; ?>">

<button
class="btn btn-danger"
type="submit">

Search

</button>

</div>

</form>

</div>

</div>

<?php if($search != ""){ ?>

<div class="alert alert-info text-center">

Showing results for:

<strong>

<?php echo clean($search); ?>

</strong>

</div>

<?php } ?>

<h1 class="mb-4 text-center">

🍕 Our Pizza Menu

</h1>

<div class="row">

<?php while($pizza = $pizzas->fetch_assoc()){ ?>

<div class="col-md-4 mb-4">

<div class="card shadow h-100">

<img
src="assets/images/<?php echo clean($pizza["image"]); ?>"
class="card-img-top"
alt="<?php echo clean($pizza["name"]); ?>">

<div class="card-body text-center">

<h4>

<?php echo clean($pizza["name"]); ?>

</h4>

<h5 class="text-danger">

€ <?php echo number_format($pizza["price"],2); ?>

</h5>

<?php if($pizza["in_stock"] == 1){ ?>

<a
href="add_to_cart.php?id=<?php echo $pizza["id"]; ?>"
class="btn btn-danger w-100">

🛒 Add To Cart

</a>

<?php }else{ ?>

<button
class="btn btn-secondary w-100"
disabled>

Out of Stock

</button>

<?php } ?>

</div>

</div>

</div>

<?php } ?>

<?php if($pizzas->num_rows == 0){ ?>

<div class="alert alert-warning text-center">

No pizzas found.

</div>

<?php } ?>

</div>

<hr class="my-5">

<h1 class="mb-4 text-center">

🥤 Drinks

</h1>

<div class="row">

<?php while($drink = $drinks->fetch_assoc()){ ?>

<div class="col-md-4 mb-4">

<div class="card shadow h-100">

<img
src="assets/images/<?php echo clean($drink["image"]); ?>"
class="card-img-top"
alt="<?php echo clean($drink["name"]); ?>">

<div class="card-body text-center">

<h4>

<?php echo clean($drink["name"]); ?>

</h4>

<h5 class="text-danger">

€ <?php echo number_format($drink["price"],2); ?>

</h5>

<?php if($drink["in_stock"] == 1){ ?>

<a
href="add_to_cart.php?id=<?php echo $drink["id"]; ?>"
class="btn btn-danger w-100">

🛒 Add To Cart

</a>

<?php }else{ ?>

<button
class="btn btn-secondary w-100"
disabled>

Out of Stock

</button>

<?php } ?>

</div>

</div>

</div>

<?php } ?>

<?php if($drinks->num_rows == 0){ ?>

<div class="alert alert-warning text-center">

No drinks found.

</div>

<?php } ?>

</div>

<?php

$stmtPizza->close();
$stmtDrink->close();

include "includes/footer.php";

?>