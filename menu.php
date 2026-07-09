<?php

session_start();

include "config.php";

include "security.php";

include "includes/header.php";


$sql = "SELECT * FROM pizzas";

$result = $conn->query($sql);

?>


<h1 class="mb-4 text-center">

🍕 Our Pizza Menu

</h1>



<div class="row">



<?php

while($pizza = $result->fetch_assoc()){


?>


<div class="col-md-4 mb-4">



<div class="card shadow h-100">



<img

src="assets/images/<?php echo clean($pizza['image']); ?>"

class="card-img-top"

alt="<?php echo clean($pizza['name']); ?>"

>




<div class="card-body text-center">



<h4>

<?php echo clean($pizza['name']); ?>

</h4>



<h5 class="text-danger">

€ <?php echo number_format($pizza['price'],2); ?>

</h5>




<a

href="add_to_cart.php?id=<?php echo $pizza['id']; ?>"

class="btn btn-danger w-100">


Add To Cart


</a>



</div>



</div>



</div>



<?php

}

?>



</div>



<?php

include "includes/footer.php";

?>