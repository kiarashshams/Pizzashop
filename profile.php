<?php

session_start();

include "config.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "security.php";

$user_id = $_SESSION["user_id"];

// Get user security information
$stmtUser = $conn->prepare("
SELECT two_factor_enabled
FROM users
WHERE id = ?
");

$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();

$userInfo = $stmtUser->get_result()->fetch_assoc();
$stmtUser->close();

include "includes/header.php";

// Success Message
if(isset($_SESSION["success"])){

    echo "
    <div class='container mt-3'>
        <div class='alert alert-success text-center'>
            ".$_SESSION["success"]."
        </div>
    </div>";

    unset($_SESSION["success"]);
}

?>

<div class="container mt-5">

<!-- Welcome -->

<div class="card shadow-lg border-0 mb-5">

<div class="card-body text-center py-5">

<h2 class="fw-bold">

👋 Welcome,

<span class="text-danger">

<?php echo clean($_SESSION["username"]); ?>

</span>

</h2>

<p class="text-muted fs-5">

Welcome back to Pizza Shop.
Your account is protected with modern security features.

</p>

</div>

</div>

<!-- Cards -->

<div class="row g-4 mb-5">

<!-- Security -->

<div class="col-lg-4">

<div class="card shadow text-center h-100 p-4">

<h1 class="mb-3">

🔐

</h1>

<h3>

Security Center

</h3>

<p class="text-muted">

Manage your Two-Factor Authentication settings.

</p>

<?php if($userInfo["two_factor_enabled"] == 1){ ?>

<p class="text-success fw-bold">

🟢 Two-Factor Authentication Enabled

</p>

<a
href="disable_2fa.php"
class="btn btn-outline-danger"
onclick="return confirm('Are you sure you want to disable Two-Factor Authentication?');">

Disable 2FA

</a>

<?php } else { ?>

<p class="text-danger fw-bold">

🔴 Two-Factor Authentication Disabled

</p>

<a
href="enable_2fa.php"
class="btn btn-warning">

Enable Google Authenticator

</a>

<?php } ?>

</div>

</div>

<!-- Orders -->

<div class="col-lg-4">

<div class="card shadow text-center h-100 p-4">

<h1 class="mb-3">

🍕

</h1>

<h3>

Orders

</h3>

<p class="text-muted">

View your order history
and track your past orders.

</p>

<a
href="my_orders.php"
class="btn btn-primary">

📦 View Orders

</a>

</div>

</div>

<!-- Account -->

<div class="col-lg-4">

<div class="card shadow text-center h-100 p-4">

<h1 class="mb-3">

👤

</h1>

<h3>

Account Management

</h3>

<p class="text-muted">

Manage your account settings and security.

</p>

<a
href="change_password.php"
class="btn btn-info mb-3">

🔑 Change Password

</a>

<a
href="edit_profile.php"
class="btn btn-warning">

👤 Edit Profile

</a>

</div>

</div>

</div>

<!-- Order More -->

<div class="card shadow-lg border-0">

<div class="card-body text-center py-5">

<h2>

🍕 Hungry again?

</h2>

<p class="text-muted fs-5">

Order your favorite pizzas and drinks.

</p>

<a
href="menu.php"
class="btn btn-success btn-lg px-5">

🍕 Order More

</a>

</div>

</div>

</div>

<?php

include "includes/footer.php";

?>