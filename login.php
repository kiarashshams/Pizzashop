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



        if(password_verify($password, $row["password"])) {

            // Prevent Session Fixation
            session_regenerate_id(true);

            $_SESSION["user_id"] = $row["id"];

            $_SESSION["username"] = $row["username"];

            header("Location: profile.php");

            exit();





        } else {



            $message = "
            <div class='alert alert-danger text-center'>
                Wrong password!
            </div>";

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