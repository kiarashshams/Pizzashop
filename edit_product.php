<?php
session_start();
if(!isset($_SESSION["user_id"]) || $_SESSION["is_admin"]!=1){
    header("Location: login.php");
    exit();
}
include "config.php";
include "security.php";

if(!isset($_GET["id"]) || !is_numeric($_GET["id"])){
    header("Location: admin_products.php");
    exit();
}

$id=(int)$_GET["id"];

$stmt=$conn->prepare("SELECT * FROM pizzas WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$product=$stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$product){
    header("Location: admin_products.php");
    exit();
}

$message="";

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $name=trim($_POST["name"]);
    $description=trim($_POST["description"]);
    $price=floatval($_POST["price"]);
    $category=$_POST["category"];

    $image=$product["image"];

    if(isset($_FILES["image"]) && $_FILES["image"]["error"]==0){

        $newName=time()."_".basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"],"assets/images/".$newName);
        $image=$newName;

    }

    $update=$conn->prepare("
    UPDATE pizzas
    SET
    name=?,
    description=?,
    price=?,
    image=?,
    category=?
    WHERE id=?
    ");

    $update->bind_param(
        "ssdssi",
        $name,
        $description,
        $price,
        $image,
        $category,
        $id
    );

    if($update->execute()){
        header("Location: admin_products.php");
        exit();
    }

    $message="<div class='alert alert-danger'>Update failed.</div>";
    $update->close();

}

include "includes/header.php";
?>

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-7">

<div class="card shadow">
<div class="card-body">

<h2 class="text-center mb-4">✏ Edit Product</h2>

<?php echo $message; ?>

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">
<label>Name</label>
<input class="form-control" name="name" required
value="<?php echo clean($product["name"]); ?>">
</div>

<div class="mb-3">
<label>Description</label>
<textarea class="form-control" rows="3" name="description" required><?php echo clean($product["description"]); ?></textarea>
</div>

<div class="mb-3">
<label>Price</label>
<input class="form-control" type="number" step="0.01"
name="price"
value="<?php echo $product["price"]; ?>"
required>
</div>

<div class="mb-3">
<label>Category</label>
<select class="form-select" name="category">
<option value="Pizza" <?php if($product["category"]=="Pizza") echo "selected"; ?>>Pizza</option>
<option value="Drink" <?php if($product["category"]=="Drink") echo "selected"; ?>>Drink</option>
</select>
</div>

<div class="mb-3">
<label>Current Image</label><br>
<img src="assets/images/<?php echo clean($product["image"]);?>" style="width:120px">
</div>

<div class="mb-3">
<label>New Image (optional)</label>
<input type="file" class="form-control" name="image">
</div>

<button class="btn btn-warning w-100">Save Changes</button>

</form>

<hr>

<a href="admin_products.php" class="btn btn-secondary">⬅ Back</a>

</div>
</div>

</div>
</div>
</div>

<?php include "includes/footer.php"; ?>
