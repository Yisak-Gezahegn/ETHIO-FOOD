<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - ETHIO FOOD</title>
    <link rel="stylesheet" href="all.css">
        <link rel="stylesheet" href="styling/addpage.css">
    <link rel="stylesheet" href="styling/about.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <li><a href="./howwork.php">How It Works</a></li>
                    <li><a href="./aboutus.php" class="active">About Us</a></li>
                    <li><a href="./faq.php">FAQ</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <?php if (isset($_SESSION['full_name'])): ?>
                    <a href="./customer/customer.php" class="btn"><i class="fas fa-user"></i> <?php echo $_SESSION['full_name']; ?></a>
                    <a href="./logout.php" class="btn">Logout</a>
                <?php else: ?>
                    <a href="./login.php" style="width: 100px; padding: 12px 0;" class="btn btn-secondary">Login</a>
                    <a href="./login.php" style="width: 100px; padding: 12px 0;" class="btn btn-secondary">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="hero-section">
        <div class="container hero-grid">
            <div class="hero-text">
                <h1>Welcome to <br><span class="highlight">ETHIO FOOD</span></h1>
                <p>We are Ethiopia's premier food delivery platform, bridging the gap between hungry customers and the finest local kitchens. Experience tradition, delivered fast. our mission is to connect people with the best Ethiopian cuisine, delivered right to their doorstep. we have many restaurants and chefs who are passionate about bringing authentic flavors to your table.</p>
                <a href="../index.php" class="btn" style="background: var(--ethio-yellow); color:white; padding: 12px 30px;">Explore Our Menu</a>
            </div>
            <div class="hero-images">
                <img src="../image/back1_good.webp" alt="Injera" class="main-plate">
                <img src="../image/back2_res.jpg" alt="Tibs" class="sub-plate plate-top">
                <img src="../image/back2_res.webp" alt="Veggie" class="sub-plate plate-bottom">
            </div>
        </div>
    </section>

    <section class="mission-strip">
        <div class="container mission-grid">
            <div class="mission-info">
                <h2 style="color: var(--ethio-yellow);">Our Mission</h2>
                <p>To support local restaurant owners by providing them with a powerful digital storefront while offering customers a fast, reliable, and delicious experience.</p>
            </div>
            <div class="role-box">
                <i class="fas fa-heart"></i>
                <h3>Customers</h3>
                <p>Food lovers seeking authentic, fresh meals.</p>
            </div>
            <div class="role-box">
                <i class="fas fa-store"></i>
                <h3>Owners</h3>
                <p>Partners growing their business digitally.</p>
            </div>
        </div>
    </section>

    <section class="yellow-features">
        <div class="container features-grid">
            <div class="feature-item">
                <i class="fas fa-bolt"></i>
                <h4>Fast Delivery</h4>
                <p>Doorstep delivery in 30-40 mins.</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                <h4>Secure Payments</h4>
                <p>Your transactions are 100% safe.</p>
            </div>
            <div class="feature-item">
                <i class="fas fa-star"></i>
                <h4>Authentic Taste</h4>
                <p>The real flavor of Ethiopia.</p>
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
                        <li>&#127835 Injera & Wat</li>
                        <li>&#129367 Vegetarian</li>
                        <li>&#127831 Meat Dishes</li>
                        <li>&#127828 Humburger</li>
                        <li>&#127829 Pizza</li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3>Contact Us</h3>
                    <ul>
                        <li>📍 Haramaya, Ethiopia</li>
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