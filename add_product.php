<?php
session_start();

if(!isset($_SESSION["user_id"]) || $_SESSION["is_admin"] != 1){
    header("Location: login.php");
    exit();
}

include "config.php";
include "security.php";

$message="";

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $name=trim($_POST["name"]);
    $description=trim($_POST["description"]);
    $price=floatval($_POST["price"]);
    $category=$_POST["category"];

    if(
        empty($name) ||
        empty($description) ||
        $price<=0 ||
        !isset($_FILES["image"])
    ){

        $message="<div class='alert alert-danger'>Please fill all fields.</div>";

    }else{

        $allowed=["jpg","jpeg","png","webp"];

        $imageName=basename($_FILES["image"]["name"]);
        $ext=strtolower(pathinfo($imageName,PATHINFO_EXTENSION));

        if(!in_array($ext,$allowed)){

            $message="<div class='alert alert-danger'>Only JPG, PNG and WEBP images are allowed.</div>";

        }else{

            $newName=time()."_".$imageName;

            move_uploaded_file(
                $_FILES["image"]["tmp_name"],
                "assets/images/".$newName
            );

            $stmt=$conn->prepare("
            INSERT INTO pizzas
            (name,description,price,image,category)
            VALUES (?,?,?,?,?)
            ");

            $stmt->bind_param(
                "ssdss",
                $name,
                $description,
                $price,
                $newName,
                $category
            );

            if($stmt->execute()){

                header("Location: admin_products.php");
                exit();

            }else{

                $message="<div class='alert alert-danger'>Database error.</div>";

            }

            $stmt->close();

        }

    }

}

include "includes/header.php";
?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-7">

<div class="card shadow">

<div class="card-body">

<h2 class="text-center mb-4">

➕ Add Product

</h2>

<?php echo $message; ?>

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">

<label>Name</label>

<input
type="text"
name="name"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Description</label>

<textarea
name="description"
class="form-control"
rows="3"
required></textarea>

</div>

<div class="mb-3">

<label>Price (€)</label>

<input
type="number"
step="0.01"
min="0.01"
name="price"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Category</label>

<select
name="category"
class="form-select">

<option value="Pizza">Pizza</option>
<option value="Drink">Drink</option>

</select>

</div>

<div class="mb-3">

<label>Image</label>

<input
type="file"
name="image"
class="form-control"
accept=".jpg,.jpeg,.png,.webp"
required>

</div>

<button
class="btn btn-success w-100">

Add Product

</button>

</form>

<hr>

<a
href="admin_products.php"
class="btn btn-secondary">

⬅ Back

</a>

</div>

</div>

</div>

</div>

</div>

<?php include "includes/footer.php"; ?>