<?php

session_start();

include "config.php";

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}


// XSS protection function
include "security.php";


$user_id = $_SESSION["user_id"];


// Prepared Statement

$stmt = $conn->prepare(
    "SELECT * FROM orders
     WHERE user_id = ?
     ORDER BY order_date DESC"
);


$stmt->bind_param("i", $user_id);

$stmt->execute();


$result = $stmt->get_result();



include "includes/header.php";

?>


<div class="container mt-5">


<div class="card shadow mb-4">


<div class="card-body">


<h2>

👋 Welcome,

<?php echo clean($_SESSION["username"]); ?>

</h2>


<p class="text-muted">

You are logged in successfully.

</p>


</div>


</div>



<h3 class="mb-3">

📦 My Orders

</h3>



<?php

if($result->num_rows == 0){

?>


<div class="alert alert-warning">

You don't have any orders yet.

</div>


<?php

}

else{

?>


<table class="table table-striped">


<thead>

<tr>

<th>Order ID</th>

<th>Total</th>

<th>Date</th>

</tr>


</thead>



<tbody>


<?php

while($order = $result->fetch_assoc()){


?>


<tr>


<td>

#<?php echo clean($order["id"]); ?>

</td>



<td>

€ <?php echo number_format($order["total_price"],2); ?>

</td>



<td>

<?php echo clean($order["order_date"]); ?>

</td>



</tr>


<?php

}

?>


</tbody>


</table>


<?php

}

?>



<div class="mt-4">


<a href="menu.php" class="btn btn-primary">

🍕

Order More

</a>



<a 
href="logout.php"
class="btn btn-danger"
onclick="return confirm('Are you sure you want to logout?');">

Logout

</a>


</div>



</div>



<?php

include "includes/footer.php";

?>