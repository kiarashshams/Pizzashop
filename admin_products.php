<?php
session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["is_admin"] != 1){
    header("Location: login.php");
    exit();
}

include "config.php";
include "security.php";
include "includes/header.php";

$search = trim($_GET["search"] ?? "");
$category = $_GET["category"] ?? "All";

$sql = "SELECT * FROM pizzas
WHERE (name LIKE CONCAT('%',?,'%')
OR description LIKE CONCAT('%',?,'%'))";

$params = [$search,$search];
$types = "ss";

if($category != "All"){
    $sql .= " AND category = ?";
    $types .= "s";
    $params[] = $category;
}

$sql .= " ORDER BY category,name";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types,...$params);
$stmt->execute();

$res = $stmt->get_result();
?>

<div class="container mt-4">

<h2 class="mb-4">

🍕 Product Management

</h2>

<div class="d-flex justify-content-between mb-3">

<form class="row g-2" method="get">

<div class="col">

<input
class="form-control"
name="search"
value="<?php echo clean($search); ?>"
placeholder="Search">

</div>

<div class="col">

<select
class="form-select"
name="category">

<?php foreach(["All","Pizza","Drink"] as $c){ ?>

<option
value="<?php echo $c; ?>"
<?php if($category==$c) echo "selected"; ?>>

<?php echo $c; ?>

</option>

<?php } ?>

</select>

</div>

<div class="col-auto">

<button class="btn btn-primary">

Search

</button>

</div>

</form>

<a
href="add_product.php"
class="btn btn-success">

➕ Add Product

</a>

</div>

<div class="table-responsive">

<table class="table table-striped align-middle">

<thead>

<tr>

<th>Image</th>

<th>Name</th>

<th>Description</th>

<th>Category</th>

<th>Price</th>

<th>Status</th>

<th>Actions</th>

</tr>

</thead>

<tbody>

<?php while($p = $res->fetch_assoc()){ ?>

<tr>

<td>

<img
src="assets/images/<?php echo clean($p["image"]); ?>"
style="width:70px;height:70px;object-fit:cover;">

</td>

<td>

<?php echo clean($p["name"]); ?>

</td>

<td>

<?php echo clean($p["description"]); ?>

</td>

<td>

<span class="badge bg-secondary">

<?php echo clean($p["category"]); ?>

</span>

</td>

<td>

€ <?php echo number_format($p["price"],2); ?>

</td>

<td>

<?php if($p["in_stock"] == 1){ ?>

<span class="badge bg-success">

In Stock

</span>

<?php }else{ ?>

<span class="badge bg-danger">

Out of Stock

</span>

<?php } ?>

</td>

<td>

<a
class="btn btn-warning btn-sm"
href="edit_product.php?id=<?php echo $p["id"]; ?>">

Edit

</a>

<?php if($p["in_stock"] == 1){ ?>

<a
class="btn btn-secondary btn-sm"
href="toggle_stock.php?id=<?php echo $p["id"]; ?>"
onclick="return confirm('Mark this product as Out of Stock?')">

Out of Stock

</a>

<?php }else{ ?>

<a
class="btn btn-success btn-sm"
href="toggle_stock.php?id=<?php echo $p["id"]; ?>"
onclick="return confirm('Mark this product as In Stock?')">

In Stock

</a>

<?php } ?>

<form
action="delete_product.php"
method="post"
style="display:inline">

<input
type="hidden"
name="id"
value="<?php echo $p["id"]; ?>">

<button
class="btn btn-danger btn-sm"
onclick="return confirm('Delete product?')">

Delete

</button>

</form>

</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

</div>

<?php

$stmt->close();

include "includes/footer.php";

?>