<?php

session_start();

if(!isset($_SESSION["user_id"])){
    header("Location: login.php");
    exit();
}

require_once "vendor/autoload.php";
include "config.php";

use PragmaRX\Google2FA\Google2FA;

$google2fa = new Google2FA();

$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare("
SELECT
    google2fa_secret,
    two_factor_enabled
FROM users
WHERE id = ?
");

$stmt->bind_param("i",$user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(
    $user["two_factor_enabled"] == 0 ||
    empty($user["google2fa_secret"])
){

    $_SESSION["success"] =
    "Two-Factor Authentication is already disabled.";

    header("Location: profile.php");
    exit();

}
$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $code = trim($_POST["code"]);

    if(
        $google2fa->verifyKey(
            $user["google2fa_secret"],
            $code
        )
    ){

        $update = $conn->prepare("
        UPDATE users
        SET
            google2fa_secret = NULL,
            two_factor_enabled = 0
        WHERE id = ?
        ");

        $update->bind_param("i",$user_id);
        $update->execute();
        $update->close();

        $_SESSION["success"] =
        "Two-Factor Authentication disabled successfully.";

        header("Location: profile.php");
        exit();

    }else{

        $message = "
        <div class='alert alert-danger text-center'>
            Invalid Google Authenticator code.
        </div>";

    }

}

include "includes/header.php";

?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow">

<div class="card-body">

<h3 class="text-center mb-4">

Disable Google Authenticator

</h3>

<div class="alert alert-warning">

⚠️
You are about to disable Two-Factor Authentication.

To continue, enter the current 6-digit code
from Google Authenticator.

</div>

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<input
type="text"
name="code"
maxlength="6"
class="form-control"
placeholder="123456"
required>

</div>

<button
class="btn btn-danger w-100">

Disable 2FA

</button>

</form>

</div>

</div>

</div>

</div>

</div>

<?php

include "includes/footer.php";

?>