<?php

session_start();

if(
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["return_to"])
){

    header("Location: login.php");
    exit();

}

require_once "vendor/autoload.php";

include "config.php";

use PragmaRX\Google2FA\Google2FA;

$google2fa = new Google2FA();

$message = "";

$user_id = $_SESSION["user_id"];

// Get user secret

$stmt = $conn->prepare("
SELECT google2fa_secret
FROM users
WHERE id = ?
");

$stmt->bind_param("i",$user_id);

$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

$stmt->close();

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $code = trim($_POST["code"]);

    if(

        $google2fa->verifyKey(

            $user["google2fa_secret"],

            $code

        )

    ){

        $_SESSION["password_2fa_verified"] = true;

        $redirect = $_SESSION["return_to"];

        unset($_SESSION["return_to"]);

        header("Location: ".$redirect);

        exit();

    }
    else{

        $message = "
        <div class='alert alert-danger text-center'>
            Invalid authentication code.
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

🔐 Verify Identity

</h3>

<p class="text-center">

Enter the 6-digit code from Google Authenticator.

</p>

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<input
type="text"
name="code"
maxlength="6"
class="form-control text-center"
placeholder="123456"
required>

</div>

<button
class="btn btn-primary w-100">

Verify

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