<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>How It Works - ETHIO FOOD</title>
    <link rel="stylesheet" href="styling/addpage.css">
    <link rel="stylesheet" href="styling/about.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styling/how.css">
</head>
<body>
    <header>
        <div class="container header-container">
            <a href="../index.php" class="logo">
                &#127839 ETHIO FOOD
            </a>
            <nav>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../restaurants/restaurants.php">Restaurants</a></li>
                    <li><a href="./howwork.php" class="active">How It Works</a></li>
                    <li><a href="./aboutus.php">About Us</a></li>
                    <li><a href="./faq.php">FAQ</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if (isset($_SESSION['full_name'])): ?>
                    <a href="./customer/customer.php" class="btn"><i class="fas fa-user"></i> <?php echo $_SESSION['full_name']; ?></a>
                    <a href="./otherpart/logout.php" class="btn">Logout</a>
                <?php else: ?>
                    <a href="./otherpart/login.php" style="width: 100px; padding: 12px 0;" class="btn btn-secondary">Login</a>
                    <a href="./otherpart/login.php" style="width: 100px; padding: 12px 0;" class="btn btn-secondary">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="steps-container">
        <div class="container">
            <div class="section-title">
                <h1>How It Works</h1>
                <div class="underline"></div>
                <p>Simple steps to get your favorite Ethiopian dishes.</p>
            </div>

            <div class="how-grid">
                <div class="how-content">
                    <div class="step-number">01</div>
                    <h2>Browse & Select</h2>
                    <p>Explore over 15+ local restaurants at your fingertips. From spicy Doro Wat to healthy Beyaynetu, find exactly what you're craving today.</p>
                </div>
                <div class="how-image">
                    <img src="../image/back1_good.webp" alt="Browse Food">
                </div>
            </div>

            <div class="how-grid">
                <div class="how-content">
                    <div class="step-number">02</div>
                    <h2>Easy Checkout</h2>
                    <p>Add items to your cart and checkout in seconds. Our platform ensures your data is safe and your order is sent directly to the chef.</p>
                </div>
                <div class="how-image">
                    <img src="../image/back2_res.jpg" alt="Checkout">
                </div>
            </div>

            <div class="how-grid">
                <div class="how-content">
                    <div class="step-number">03</div>
                    <h2>We Prepare & Deliver</h2>
                    <p>The restaurant starts cooking immediately. Once ready, our professional riders pick it up and race to your location in 30-40 minutes.</p>
                </div>
                <div class="how-image">
                    <img src="../image/back2_res.webp" alt="Delivery">
                </div>
            </div>

            <div class="how-grid">
                <div class="how-content">
                    <div class="step-number">04</div>
                    <h2>Enjoy Your Meal</h2>
                    <p>Track your delivery in real-time. Receive your fresh, hot food and enjoy the authentic taste of Ethiopia from the comfort of your home.</p>
                </div>
                <div class="how-image">
                    <img src="../image/back4log.avif" alt="Enjoying Meal">
                </div>
            </div>

            <div class="cta-strip">
                <h3>Hungry Yet?</h3>
                <a href="../index.php" class="btn" style="background: #fff; color: #f1b922; padding: 15px 40px; font-weight: bold;">Start Ordering Now</a>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>ETHIO FOOD</h3>
                    <p>Delivering authentic Ethiopian cuisine to your doorstep. Fast, reliable, and delicious.</p>
                    <div class="social-links">
                        <a href="#"><img src="../image/facebook.svg" alt="FB"></a>
                        <a href="#"><img src="../image/instagram.svg" alt="IG"></a>
                        <a href="#"><img src="../image/telegram.svg" alt="TG"></a>
                        <a href="#"><img src="../image/twitter.svg" alt="TW"></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="../index.php">Home</a></li>
                        <li><a href="howwork.php">How It Works</a></li>
                        <li><a href="aboutus.php">About Us</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Categories</h3>
                    <ul>
                        <li><a href="#">Injera & Wat</a></li>
                        <li><a href="#">Vegetarian</a></li>
                        <li><a href="#">Meat Dishes</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h3>Contact Us</h3>
                    <ul>
                        <li>📍 Addis Ababa, Ethiopia</li>
                        <li>☎ +251 973 391 342</li>
                        <li>✉ info@ethiofood.com</li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 ETHIO FOOD Delivery Service. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>