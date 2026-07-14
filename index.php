<?php

session_start();

include "includes/header.php";

?>

<!-- Hero Section -->

<div id="heroCarousel"
class="carousel slide carousel-fade mb-5"
data-bs-ride="carousel"
data-bs-interval="4000">

<div class="carousel-inner">

<div class="carousel-item active">

<img src="assets/images/hero1.jpg"
class="d-block w-100 hero-img">

<div class="carousel-caption">

<h1 class="display-3 fw-bold">

🍕 Fresh & Delicious Pizza

</h1>

<p class="fs-4">

Welcome to Pizza Shop.<br>
Order your favorite pizza online quickly and easily.

</p>

<a href="menu.php"
class="btn btn-danger btn-lg">

Order Now

</a>

</div>

</div>

<div class="carousel-item">

<img src="assets/images/hero2.jpg"
class="d-block w-100 hero-img">

<div class="carousel-caption">

<h1 class="display-3 fw-bold">

 Try the Original and Tasty Italian Pizza

</h1>

<p class="fs-4">

Made with fresh ingredients
and authentic Italian recipes.

</p>

<a href="menu.php"
class="btn btn-danger btn-lg">

Order Now

</a>

</div>

</div>

<div class="carousel-item">

<img src="assets/images/hero3.jpg"
class="d-block w-100 hero-img">

<div class="carousel-caption">

<h1 class="display-3 fw-bold">

❤️ Everything is Better with Pizza

</h1>

<p class="fs-4">

Life tastes better
with pizza and friends.

</p>

<a href="menu.php"
class="btn btn-danger btn-lg">

Order Now

</a>

</div>

</div>

</div>

<button class="carousel-control-prev"
type="button"
data-bs-target="#heroCarousel"
data-bs-slide="prev">

<span class="carousel-control-prev-icon"></span>

</button>

<button class="carousel-control-next"
type="button"
data-bs-target="#heroCarousel"
data-bs-slide="next">

<span class="carousel-control-next-icon"></span>

</button>

</div>

<!-- Popular Pizzas -->

<h2 class="mb-4">

Popular Pizzas

</h2>

<div class="row">

    <div class="col-md-4">

        <div class="card shadow">

            <img
            src="https://media-assets.lacucinaitaliana.it/photos/61fd3af553797678cdac0c1f/1:1/w_2560%2Cc_limit/Pizza-margherita.jpg"
            class="card-img-top">

            <div class="card-body">

                <h5>Margherita</h5>

                <p>

                    Classic Italian Pizza

                </p>

                <a href="menu.php" class="btn btn-outline-danger">

                    View Menu

                </a>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card shadow">

            <img
            src="https://cdn.aniagotuje.com/pictures/articles/2022/08/31553211-v-1080x1080.jpg"
            class="card-img-top">

            <div class="card-body">

                <h5>Pepperoni</h5>

                <p>

                    Extra Pepperoni

                </p>

                <a href="menu.php" class="btn btn-outline-danger">

                    View Menu

                </a>

            </div>

        </div>

    </div>

    <div class="col-md-4">

        <div class="card shadow">

            <img
            src="https://www.recipetineats.com/uploads/2024/05/Pizza-Capricciosa_8.jpg"
            class="card-img-top">

            <div class="card-body">

                <h5>Capricciosa</h5>

                <p>

                    tomato sauce, mozzarella , mushrooms, prosciutto and black olives

                </p>

                <a href="menu.php" class="btn btn-outline-danger">

                    View Menu

                </a>

            </div>

        </div>

    </div>

</div>

<?php

include "includes/footer.php";

?>