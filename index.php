<?php

session_start();

include "includes/header.php";

?>

<!-- Hero Section -->

<div class="p-5 mb-5 bg-light rounded-4">

    <div class="container py-5">

        <h1 class="display-4 fw-bold">

            🍕 Fresh & Delicious Pizza

        </h1>

        <p class="fs-5">

            Welcome to Pizza Shop.
            Order your favorite pizza online quickly and easily.

        </p>

        <a href="menu.php" class="btn btn-danger btn-lg">

            Order Now

        </a>

    </div>

</div>

<!-- Popular Pizzas -->

<h2 class="mb-4">

Popular Pizzas

</h2>

<div class="row">

    <div class="col-md-4">

        <div class="card shadow">

            <img
            src="https://images.unsplash.com/photo-1513104890138-7c749659a591?w=600"
            class="card-img-top">

            <div class="card-body">

                <h5>Margherita</h5>

                <p>

                    Classic Italian Pizza

                </p>

                <button class="btn btn-danger">

                    €8

                </button>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card shadow">

            <img
            src="https://images.unsplash.com/photo-1594007654729-407eedc4be65?w=600"
            class="card-img-top">

            <div class="card-body">

                <h5>Pepperoni</h5>

                <p>

                    Extra Pepperoni

                </p>

                <button class="btn btn-danger">

                    €10

                </button>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card shadow">

            <img
            src="https://images.unsplash.com/photo-1541745537411-b8046dc6d66c?w=600"
            class="card-img-top">

            <div class="card-body">

                <h5>BBQ Chicken</h5>

                <p>

                    Chicken with BBQ Sauce

                </p>

                <button class="btn btn-danger">

                    €11

                </button>

            </div>

        </div>

    </div>

</div>

<?php

include "includes/footer.php";

?>