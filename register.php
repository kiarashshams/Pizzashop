<?php
session_start();

include "config.php";
include "recaptcha.php";
include "csrf.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    verifyCSRFToken($_POST["csrf_token"] ?? "");

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Password validation
    if(strlen($password) < 8){

        $message = "
        <div class='alert alert-danger text-center'>
            Password must be at least 8 characters.
        </div>";

    }
    elseif(
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[\W_]/', $password)
    ){

        $message = "
        <div class='alert alert-danger text-center'>

        Password must contain:

        <br>• Minimum 8 characters
        <br>• One uppercase letter (A-Z)
        <br>• One lowercase letter (a-z)
        <br>• One number (0-9)
        <br>• One special character (!@#$%^&*)

        </div>";

    }
    else{

        // Verify reCAPTCHA

        $token = $_POST["recaptcha_token"] ?? "";

        $response = file_get_contents(
            "https://www.google.com/recaptcha/api/siteverify?secret="
            .$recaptcha_secret.
            "&response=".$token
        );

        $captcha = json_decode($response, true);

        if(
            !$captcha["success"] ||
            $captcha["score"] < 0.5
        ){

            $message = "
            <div class='alert alert-danger text-center'>
                Captcha verification failed.
            </div>";

        }else{

            // Check if username or email already exists

            $check = $conn->prepare("
            SELECT username, email
            FROM users
            WHERE username = ?
            OR email = ?
            ");

            $check->bind_param(
                "ss",
                $username,
                $email
            );

            $check->execute();

            $result = $check->get_result();

            if($result->num_rows > 0){

                $existing = $result->fetch_assoc();

                if($existing["username"] == $username){

                    $message = "
                    <div class='alert alert-danger text-center'>
                        Username already exists.
                    </div>";

                }else{

                    $message = "
                    <div class='alert alert-danger text-center'>
                        Email already exists.
                    </div>";

                }

            }else{

                // Hash password

                $passwordHash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                // Insert user

                $stmt = $conn->prepare("
                INSERT INTO users
                (username, email, password)
                VALUES (?, ?, ?)
                ");

                $stmt->bind_param(
                    "sss",
                    $username,
                    $email,
                    $passwordHash
                );

                if($stmt->execute()){

                    $message = "
                    <div class='alert alert-success text-center'>
                        Registration successful!
                    </div>";

                }else{

                    $message = "
                    <div class='alert alert-danger text-center'>
                        Registration failed.
                    </div>";

                }

                $stmt->close();

            }

            $check->close();

        }

    }

}

include "includes/header.php";

$csrf = generateCSRFToken();

echo $message;

?>

<div class="row justify-content-center mt-5">

<div class="col-md-5">

<div class="card shadow">

<div class="card-body">

<h2 class="text-center mb-4">

🍕 Create Account

</h2>

<form method="POST">

<input
type="hidden"
name="csrf_token"
value="<?php echo $csrf; ?>">

<div class="mb-3">

<label class="form-label">

Username

</label>

<input
type="text"
class="form-control"
name="username"
required>

</div>

<div class="mb-3">

<label class="form-label">

Email

</label>

<input
type="email"
class="form-control"
name="email"
required>

</div>

<div class="mb-3">

<label class="form-label">

Password

</label>

<input
type="password"
class="form-control"
name="password"
id="password"
required
minlength="8">

<div class="progress mt-2" style="height:8px;">

<div
id="strengthBar"
class="progress-bar bg-danger"
style="width:0%;">

</div>

</div>

<p id="strengthText" class="mt-2 fw-bold text-danger">

🔴 Weak

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

<input
type="hidden"
name="recaptcha_token"
id="recaptcha_token">

<button
type="submit"
class="btn btn-danger w-100">

Register

</button>

</form>

<hr>

<p class="text-center">

Already have an account?

<a href="login.php">

Login

</a>

</p>

</div>

</div>

</div>

</div>

<script src="https://www.google.com/recaptcha/api.js?render=6LcAg0stAAAAAMfvc1a3G2nmNdWt_6ZjE-gXPkzE"></script>

<script>

grecaptcha.ready(function(){

    grecaptcha.execute(
        '6LcAg0stAAAAAMfvc1a3G2nmNdWt_6ZjE-gXPkzE',
        {
            action:'register'
        }
    ).then(function(token){

        document.getElementById(
            'recaptcha_token'
        ).value = token;

    });

});

</script>


<script>
const password=document.getElementById("password");
if(password){
password.addEventListener("input",function(){
const value=this.value;
let score=0;
const length=value.length>=8;
const upper=/[A-Z]/.test(value);
const lower=/[a-z]/.test(value);
const number=/[0-9]/.test(value);
const special=/[\W_]/.test(value);
document.getElementById("ruleLength").innerHTML=(length?"✅":"❌")+" Minimum 8 characters";
document.getElementById("ruleUpper").innerHTML=(upper?"✅":"❌")+" One uppercase letter";
document.getElementById("ruleLower").innerHTML=(lower?"✅":"❌")+" One lowercase letter";
document.getElementById("ruleNumber").innerHTML=(number?"✅":"❌")+" One number";
document.getElementById("ruleSpecial").innerHTML=(special?"✅":"❌")+" One special character";
if(length)score++;
if(upper)score++;
if(lower)score++;
if(number)score++;
if(special)score++;
const bar=document.getElementById("strengthBar");
const txt=document.getElementById("strengthText");
if(score<=1){bar.style.width="20%";bar.className="progress-bar bg-danger";txt.innerHTML="🔴 Weak";txt.className="mt-2 fw-bold text-danger";}
else if(score<=3){bar.style.width="60%";bar.className="progress-bar bg-warning";txt.innerHTML="🟡 Medium";txt.className="mt-2 fw-bold text-warning";}
else{bar.style.width="100%";bar.className="progress-bar bg-success";txt.innerHTML="🟢 Strong";txt.className="mt-2 fw-bold text-success";}
});
}
</script>

<?php

include "includes/footer.php";

?>