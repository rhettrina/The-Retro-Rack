<?php
session_start();
include 'config.php'; // Ensure this path correctly points to your config file

// Fetch New Arrivals (e.g., latest 8 products)
$new_arrivals_query = "SELECT * FROM `products` ORDER BY `created_at` DESC LIMIT 8";
$new_arrivals_result = mysqli_query($conn, $new_arrivals_query);

// Fetch Featured Products
$featured_query = "SELECT * FROM `products` WHERE `is_featured` = 1 LIMIT 8";
$featured_result = mysqli_query($conn, $featured_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Boxicons -->
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <!-- Glide js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.4.1/css/glide.core.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.4.1/css/glide.theme.css">
    <!-- Custom StyleSheet -->
    <link rel="stylesheet" href="./css/styles.css" />
    <link rel="stylesheet" href="./css/index.css">
    <title>RetroRack - Home</title>
</head>

<body>
    <!-- Header -->
    <header class="header" id="header">

        <?php include 'visitorheader.php'; ?>

        <div class="hero">
            <div class="glide" id="glide_1">
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        <li class="glide__slide">
                            <div class="center">
                                <div class="left">
                                    <span class="">New Inspiration 2022</span>
                                    <h1 class="">NEW COLLECTION!</h1>
                                    <p>Trending from men's and women's style collection</p>
                                    <a href="product.php" class="hero-btn">SHOP NOW</a>
                                </div>
                                <div class="right">
                                    <img class="img1" src="./images/hero-1.png" alt="">
                                </div>
                            </div>
                        </li>
                        <li class="glide__slide">
                            <div class="center">
                                <div class="left">
                                    <span>New Inspiration 2022</span>
                                    <h1>THE PERFECT MATCH!</h1>
                                    <p>Trending from men's and women's style collection</p>
                                    <a href="product.php" class="hero-btn">SHOP NOW</a>
                                </div>
                                <div class="right">
                                    <img class="img2" src="./images/hero-2.png" alt="">
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </header>

    <!-- Categories Section -->
    <section class="section category">
        <div class="cat-center">
            <div class="cat">
                <img src="./images/cat3.jpg" alt="" />
                <div>
                    <p>WOMEN'S WEAR</p>
                </div>
            </div>
            <div class="cat">
                <img src="./images/cat2.jpg" alt="" />
                <div>
                    <p>ACCESSORIES</p>
                </div>
            </div>
            <div class="cat">
                <img src="./images/cat1.jpg" alt="" />
                <div>
                    <p>MEN'S WEAR</p>
                </div>
            </div>
        </div>
    </section>

    <!-- New Arrivals -->
    <section class="section new-arrival">
        <div class="title">
            <h1>NEW ARRIVALS</h1>
            <p>All the latest picks from the designers of our store</p>
        </div>

        <div class="product-center">
            <?php
            if (mysqli_num_rows($new_arrivals_result) > 0) {
                while ($product = mysqli_fetch_assoc($new_arrivals_result)) {
                    ?>
                    <div class="product-item">
                        <div class="overlay">
                            <a href="productDetails.php?id=<?php echo $product['id']; ?>" class="product-thumb">
                                <img src="<?php echo htmlspecialchars($product['image_path']); ?>"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>" />
                            </a>
                            <?php if ($product['stock'] == 0) { ?>
                                <span class="out-of-stock">Out of Stock</span>
                            <?php } ?>
                        </div>
                        <div class="product-info">
                            <span><?php echo htmlspecialchars($product['category']); ?></span>
                            <a
                                href="productDetails.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                            <h4>$<?php echo number_format($product['price'], 2); ?></h4>
                        </div>
                        <ul class="icons">
                            <li><i class="bx bx-heart"></i></li>
                            <li>
                                <a href="productDetails.php?id=<?php echo $product['id']; ?>">
                                    <i class="bx bx-show"></i>
                                </a>
                            </li>
                            <li>
                                <a href="add_to_cart.php?product_id=<?php echo $product['id']; ?>">
                                    <i class="bx bx-cart"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No new arrivals at the moment.</p>";
            }
            ?>
        </div>
    </section>

    <!-- Promo -->
    <section class="section banner">
        <div class="left">
            <span class="trend">Trend Design</span>
            <h1>New Collection 2022</h1>
            <p>New Arrival <span class="color">Sale 50% OFF</span> Limited Time Offer</p>
            <a href="product.php" class="btn btn-1">Discover Now</a>
        </div>
        <div class="right">
            <img src="./images/banner.png" alt="">
        </div>
    </section>



    <?php include 'visitorfooter.php'; ?>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Confirm Logout</h2>
            <p>Are you sure you want to logout?</p>
            <div class="modal-buttons">
                <button id="cancelBtn" class="btn cancel-btn">Cancel</button>
                <button id="logoutBtn" class="btn logout-btn">Logout</button>
            </div>
        </div>
    </div>

    <script>
        // Get modal element
        const modal = document.getElementById("logoutModal");

        // Get the logout icon/link by its ID
        const logoutLink = document.getElementById("logoutIcon");

        // Get the close button (X)
        const closeButton = document.querySelector(".close-button");

        // Get the Cancel and Logout buttons inside the modal
        const cancelBtn = document.getElementById("cancelBtn");
        const logoutBtn = document.getElementById("logoutBtn");

        // Function to open the modal
        function openModal(event) {
            event.preventDefault(); // Prevent the default logout action
            modal.style.display = "block";
        }

        // Function to close the modal
        function closeModal() {
            modal.style.display = "none";
        }

        // When the user clicks on the logout link/icon, open the modal
        if (logoutLink) {
            logoutLink.addEventListener("click", openModal);
        }

        // When the user clicks on the close button (X), close the modal
        if (closeButton) {
            closeButton.addEventListener("click", closeModal);
        }

        // When the user clicks on the Cancel button, close the modal
        if (cancelBtn) {
            cancelBtn.addEventListener("click", closeModal);
        }

        // When the user clicks on the Logout button, proceed with logout
        if (logoutBtn) {
            logoutBtn.addEventListener("click", function () {
                window.location.href = "logout.php";
            });
        }

        // When the user clicks anywhere outside of the modal, close it
        window.addEventListener("click", function (event) {
            if (event.target == modal) {
                closeModal();
            }
        });
    </script>

</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.4.1/glide.min.js"></script>
<script src="./js/slider.js"></script>
<script src="./js/index.js"></script>

</html>