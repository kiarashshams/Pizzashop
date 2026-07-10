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

    }

    else {



        // Check if email exists

        $check = $conn->prepare(
            "SELECT id FROM users WHERE email = ?"
        );


        $check->bind_param(
            "s",
            $email
        );


        $check->execute();


        $result = $check->get_result();



        if ($result->num_rows > 0) {


            $message = "
            <div class='alert alert-danger text-center'>
                Email already exists.
            </div>";



        } else {



            // Hash password

            $passwordHash = password_hash(
                $password,
                PASSWORD_DEFAULT
            );



            // Insert user

            $stmt = $conn->prepare(
                "INSERT INTO users
                (username, email, password)
                VALUES (?, ?, ?)"
            );


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



            } else {


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

required

minlength="6">


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



<?php

include "includes/footer.php";

?>