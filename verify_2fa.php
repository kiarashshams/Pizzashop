<?php

session_start();

if (!isset($_SESSION["temp_user_id"])) {
    header("Location: login.php");
    exit();
}

require_once "vendor/autoload.php";
include "config.php";

use PragmaRX\Google2FA\Google2FA;

$google2fa = new Google2FA();

$message = "";

$user_id = $_SESSION["temp_user_id"];

// دریافت Secret کاربر
$stmt = $conn->prepare("
SELECT username, google2fa_secret
FROM users
WHERE id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $code = trim($_POST["code"]);

    $valid = $google2fa->verifyKey(
        $user["google2fa_secret"],
        $code
    );

    if ($valid) {

        // فعال کردن 2FA برای کاربر
        $update = $conn->prepare("
            UPDATE users
            SET two_factor_enabled = 1
            WHERE id = ?
        ");

        $update->bind_param("i", $user_id);
        $update->execute();
        $update->close();

        $_SESSION["user_id"] = $_SESSION["temp_user_id"];
        $_SESSION["username"] = $_SESSION["temp_username"];
        $_SESSION["is_admin"] = $_SESSION["temp_is_admin"];

        // پاک کردن اطلاعات موقت
        unset($_SESSION["temp_user_id"]);
        unset($_SESSION["temp_username"]);
        unset($_SESSION["temp_is_admin"]);

        // هدایت بر اساس نقش
        if($_SESSION["is_admin"] == 1){

            header("Location: admin_dashboard.php");

        }
        else{

            header("Location: profile.php");

        }

        exit();

    } else {

        $message = "
        <div class='alert alert-danger'>
            Invalid authentication code.
        </div>";

    }

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Google Authenticator</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-5">

<div class="card shadow">

<div class="card-body">

<h3 class="text-center">

Google Authenticator

</h3>

<p class="text-center">

Enter the 6-digit code

</p>

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
class="btn btn-primary w-100">

Verify

</button>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>