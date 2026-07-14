<?php

session_start();

if(!isset($_SESSION["user_id"])){

    header("Location: login.php");
    exit();

}

include "config.php";
include "security.php";

$stmt = $conn->prepare("
SELECT two_factor_enabled
FROM users
WHERE id = ?
");

$stmt->bind_param("i", $_SESSION["user_id"]);

$stmt->execute();

$user2fa = $stmt->get_result()->fetch_assoc();

$stmt->close();

if(

    $user2fa["two_factor_enabled"] == 1 &&

    !isset($_SESSION["password_2fa_verified"])

){

    $_SESSION["return_to"] = "change_password.php";

    header("Location: verify_password_2fa.php");

    exit();

}

$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $current = $_POST["current_password"];
    $new = $_POST["new_password"];
    $confirm = $_POST["confirm_password"];

    if($new != $confirm){

        $message = "
        <div class='alert alert-danger'>
            New passwords do not match.
        </div>";

    }

    elseif(strlen($new) < 8){

        $message = "
        <div class='alert alert-danger'>
            Password must be at least 8 characters.
        </div>";

    }
    elseif(
        !preg_match('/[A-Z]/', $new) ||
        !preg_match('/[a-z]/', $new) ||
        !preg_match('/[0-9]/', $new) ||
        !preg_match('/[\W_]/', $new)
    ){

        $message = "
        <div class='alert alert-danger'>

            Password must contain:

            <ul class='mb-0 mt-2'>
                <li>Minimum 8 characters</li>
                <li>One uppercase letter (A-Z)</li>
                <li>One lowercase letter (a-z)</li>
                <li>One number (0-9)</li>
                <li>One special character (!@#$%^&*)</li>
            </ul>

        </div>";

    }
    else{

        $stmt = $conn->prepare("
        SELECT password
        FROM users
        WHERE id = ?
        ");

        $stmt->bind_param("i", $_SESSION["user_id"]);
        $stmt->execute();

        $user = $stmt->get_result()->fetch_assoc();

        if(!password_verify($current, $user["password"])){

            $message = "
            <div class='alert alert-danger'>
                Current password is incorrect.
            </div>";

        }

        elseif(password_verify($new, $user["password"])){

            $message = "
            <div class='alert alert-warning'>
                New password must be different from your current password.
            </div>";

        }

        else{

            $hash = password_hash(
                $new,
                PASSWORD_DEFAULT
            );

            $update = $conn->prepare("
            UPDATE users
            SET password = ?
            WHERE id = ?
            ");

            $update->bind_param(
                "si",
                $hash,
                $_SESSION["user_id"]
            );

            $update->execute();
            $update->close();

            // Regenerate Session ID
            session_regenerate_id(true);

            // Remove temporary 2FA permission
            unset($_SESSION["password_2fa_verified"]);

            $_SESSION["success"] =
            "Password changed successfully.";

            header("Location: profile.php");
            exit();

        }

        $stmt->close();

    }

}

include "includes/header.php";

?>

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-6">

<div class="card shadow">

<div class="card-body">

<h3 class="text-center mb-4">

🔑 Change Password

</h3>

<?php echo $message; ?>

<form method="POST">

<div class="mb-3">

<label>

Current Password

</label>

<input
type="password"
name="current_password"
class="form-control"
required>

</div>

<div class="mb-3">

<label>

New Password

</label>

<input
type="password"
name="new_password"
id="new_password"
class="form-control"
required>

<div class="progress mt-2" style="height:8px;">

<div
id="strengthBar"
class="progress-bar bg-danger"
style="width:0%;">

</div>

</div>

<p
id="strengthText"
class="mt-2 fw-bold text-secondary">

Password Strength

</p>

<ul class="small list-unstyled">

<li id="ruleLength">❌ Minimum 8 characters</li>

<li id="ruleUpper">❌ One uppercase letter</li>

<li id="ruleLower">❌ One lowercase letter</li>

<li id="ruleNumber">❌ One number</li>

<li id="ruleSpecial">❌ One special character</li>

</ul>

<small class="text-muted">

Minimum 8 characters, including one uppercase letter, one lowercase letter, one number and one special character.

</small>

</div>

<div class="mb-3">

<label>

Confirm New Password

</label>

<input
type="password"
name="confirm_password"
class="form-control"
required>

</div>

<button
class="btn btn-primary w-100">

🔑 Change Password

</button>

</form>

</div>

</div>

</div>

</div>

</div>

<script>

const password=document.getElementById("new_password");

if(password){

password.addEventListener("input",function(){

const value=this.value;

let score=0;

const length=value.length>=8;
const upper=/[A-Z]/.test(value);
const lower=/[a-z]/.test(value);
const number=/[0-9]/.test(value);
const special=/[\W_]/.test(value);

document.getElementById("ruleLength").innerHTML=
(length?"✅":"❌")+" Minimum 8 characters";

document.getElementById("ruleUpper").innerHTML=
(upper?"✅":"❌")+" One uppercase letter";

document.getElementById("ruleLower").innerHTML=
(lower?"✅":"❌")+" One lowercase letter";

document.getElementById("ruleNumber").innerHTML=
(number?"✅":"❌")+" One number";

document.getElementById("ruleSpecial").innerHTML=
(special?"✅":"❌")+" One special character";

if(length) score++;
if(upper) score++;
if(lower) score++;
if(number) score++;
if(special) score++;

const bar=document.getElementById("strengthBar");
const text=document.getElementById("strengthText");

if(value.length===0){

bar.style.width="0%";
bar.className="progress-bar";
text.innerHTML="Password Strength";
text.className="mt-2 fw-bold text-secondary";

}
else if(score<=1){

bar.style.width="20%";
bar.className="progress-bar bg-danger";

text.innerHTML="🔴 Weak";
text.className="mt-2 fw-bold text-danger";

}
else if(score<=3){

bar.style.width="60%";
bar.className="progress-bar bg-warning";

text.innerHTML="🟡 Medium";
text.className="mt-2 fw-bold text-warning";

}
else{

bar.style.width="100%";
bar.className="progress-bar bg-success";

text.innerHTML="🟢 Strong";
text.className="mt-2 fw-bold text-success";

}

});

}

</script>

<?php

include "includes/footer.php";

?>