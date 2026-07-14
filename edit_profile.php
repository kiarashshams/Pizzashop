<?php
session_start();

if(!isset($_SESSION["user_id"])){
    header("Location: login.php");
    exit();
}

include "config.php";
include "security.php";

$message="";

$stmt=$conn->prepare("
SELECT username,email,first_name,last_name,birth_date,profile_image
FROM users
WHERE id=?
");
$stmt->bind_param("i",$_SESSION["user_id"]);
$stmt->execute();
$user=$stmt->get_result()->fetch_assoc();
$stmt->close();

if($_SERVER["REQUEST_METHOD"]=="POST"){

    $first_name=trim($_POST["first_name"]);
    $last_name=trim($_POST["last_name"]);
    $birth_date=$_POST["birth_date"];

    $profile_image=$user["profile_image"];

    if(isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"]==0){

        $ext=strtolower(pathinfo($_FILES["profile_image"]["name"],PATHINFO_EXTENSION));
        $allowed=["jpg","jpeg","png","webp"];

        if(in_array($ext,$allowed)){

            $newName=time()."_".basename($_FILES["profile_image"]["name"]);

            move_uploaded_file(
                $_FILES["profile_image"]["tmp_name"],
                "assets/profile/".$newName
            );

            $profile_image=$newName;
        }
    }

    $update=$conn->prepare("
    UPDATE users
    SET
    first_name=?,
    last_name=?,
    birth_date=?,
    profile_image=?
    WHERE id=?
    ");

    $update->bind_param(
        "ssssi",
        $first_name,
        $last_name,
        $birth_date,
        $profile_image,
        $_SESSION["user_id"]
    );

    if($update->execute()){

        $_SESSION["success"]="Profile updated successfully.";

        header("Location: profile.php");
        exit();

    }else{

        $message="<div class='alert alert-danger'>Update failed.</div>";

    }

    $update->close();

}

include "includes/header.php";
?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-7">

<div class="card shadow">

<div class="card-body">

<h2 class="text-center mb-4">

👤 Edit Profile

</h2>

<?php echo $message; ?>

<form method="POST" enctype="multipart/form-data">

<div class="text-center mb-4">

<?php
$image = !empty($user["profile_image"])
? "assets/profile/".clean($user["profile_image"])
: "assets/images/default-avatar.png";
?>

<img
id="profilePreview"
src="<?php echo $image; ?>"
class="rounded-circle"
style="width:140px;height:140px;object-fit:cover;">

</div>

<div class="mb-3">

<label>Profile Picture</label>

<input
type="file"
name="profile_image"
class="form-control"
accept=".jpg,.jpeg,.png,.webp"
id="profile_image">

</div>

<div class="mb-3">

<label>Username</label>

<input
class="form-control"
value="<?php echo clean($user["username"]); ?>"
disabled>

</div>

<div class="mb-3">

<label>Email</label>

<input
class="form-control"
value="<?php echo clean($user["email"]); ?>"
disabled>

</div>

<div class="mb-3">

<label>First Name</label>

<input
name="first_name"
class="form-control"
value="<?php echo clean($user["first_name"]); ?>">

</div>

<div class="mb-3">

<label>Last Name</label>

<input
name="last_name"
class="form-control"
value="<?php echo clean($user["last_name"]); ?>">

</div>

<div class="mb-4">

<label>Birth Date</label>

<input
type="date"
name="birth_date"
class="form-control"
value="<?php echo clean($user["birth_date"]); ?>">

</div>

<button class="btn btn-warning w-100">

💾 Save Changes

</button>

<a
href="profile.php"
class="btn btn-secondary w-100 mt-3">

⬅ Back

</a>

</form>

</div>

</div>

</div>

</div>

</div>

<script>

const input=document.getElementById("profile_image");

if(input){

input.addEventListener("change",function(){

if(this.files && this.files[0]){

const reader=new FileReader();

reader.onload=function(e){

document.getElementById("profilePreview").src=e.target.result;

};

reader.readAsDataURL(this.files[0]);

}

});

}

</script>

<?php include "includes/footer.php"; ?>
