<?php

session_start();

include "config.php";
include "csrf.php";


$message = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {


    // CSRF Protection
    verifyCSRFToken($_POST["csrf_token"]);



    $username = trim($_POST["username"]);

    $password = $_POST["password"];



    // Prepared Statement

    $stmt = $conn->prepare(
        "SELECT * FROM users WHERE username = ?"
    );


    $stmt->bind_param(
        "s",
        $username
    );


    $stmt->execute();



    $result = $stmt->get_result();



    if ($result->num_rows == 1) {


        $row = $result->fetch_assoc();
        // Check if account is temporarily locked

            if(
                !empty($row["locked_until"]) &&
                strtotime($row["locked_until"]) > time()
            ){

                $message = "
                <div class='alert alert-danger text-center'>
                    Too many failed login attempts.<br>
                    Please try again in 5 minutes.
                </div>";

                include "includes/header.php";
                echo $message;
                include "includes/footer.php";
                exit();}



        if(password_verify($password, $row["password"])) {
            // Reset failed attempts after successful login

            $reset = $conn->prepare("
            UPDATE users
            SET
            failed_attempts = 0,
            locked_until = NULL
            WHERE id = ?
            ");

            $reset->bind_param("i",$row["id"]);
            $reset->execute();
            $reset->close();

            // Prevent Session Fixation
             session_regenerate_id(true);

            // اگر 2FA فعال باشد
            if($row["two_factor_enabled"] == 1){

                $_SESSION["temp_user_id"] = $row["id"];
                $_SESSION["temp_username"] = $row["username"];
                $_SESSION["temp_is_admin"] = $row["is_admin"];
                
                header("Location: verify_2fa.php");
                exit();

            }

            // ورود معمولی
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["is_admin"] = $row["is_admin"];
            if($row["is_admin"] == 1){
                header("Location: admin_dashboard.php");
            }
            else{
            header("Location: profile.php");
            }
            exit();

}   else {

    $attempts = $row["failed_attempts"] + 1;

    if($attempts >= 3){

        $update = $conn->prepare("
        UPDATE users
        SET
            failed_attempts = ?,
            locked_until = DATE_ADD(NOW(), INTERVAL 5 MINUTE)
        WHERE id = ?
        ");

        $update->bind_param("ii",$attempts,$row["id"]);
        $update->execute();
        $update->close();

        $message = "
        <div class='alert alert-danger text-center'>
            Too many failed login attempts.<br>
            Your account has been locked for 5 minutes.
        </div>";

    }else{

        $update = $conn->prepare("
        UPDATE users
        SET failed_attempts = ?
        WHERE id = ?
        ");

        $update->bind_param("ii",$attempts,$row["id"]);
        $update->execute();
        $update->close();

        $remaining = 3 - $attempts;

        $message = "
        <div class='alert alert-danger text-center'>
            Wrong password!<br>
            Remaining attempts: ".$remaining."
        </div>";

    }

        }



    } else {



        $message = "
        <div class='alert alert-danger text-center'>
            User not found!
        </div>";

    }



    $stmt->close();


}



include "includes/header.php";


echo $message;


?>


<div class="row justify-content-center">


<div class="col-md-5">


<div class="card shadow p-4">



<h2 class="text-center mb-4">

🍕 Pizza Shop Login

</h2>




<form method="POST">



<input

type="hidden"

name="csrf_token"

value="<?php echo generateCSRFToken(); ?>">





<div class="mb-3">


<label class="form-label">

Username

</label>



<input

class="form-control"

type="text"

name="username"

required>



</div>





<div class="mb-3">


<label class="form-label">

Password

</label>



<input

class="form-control"

type="password"

name="password"

required>



</div>





<button

class="btn btn-danger w-100"

type="submit">


Login


</button>




</form>





<p class="text-center mt-3">


Don't have an account?


<a href="register.php">

Register

</a>


</p>




</div>


</div>


</div>




<?php

include "includes/footer.php";

?>