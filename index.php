<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>&#127839 - ETHIO FOOD</title>
    <link rel="stylesheet" href="styling/style.css">
    <link rel="stylesheet" href="styling/cookies.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <div class="container header-container">
        <a href="index.php" class="logo">
            &#127839 ETHIO FOOD
        </a>
        
        <nav>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="./restaurants/restaurants.php">Restaurants</a></li>
                <li><a href="./otherpart/aboutus.php">About Us</a></li>
                <li><a href="./otherpart/faq.php">FAQ</a></li>
            </ul>
        </nav>
        
        <div class="auth-buttons">
            <?php if (isset($_SESSION['full_name'])): ?>
                <a href="./customer/customer.php" class="btn"><i class="fas fa-user"></i>
                    <?php echo ($_SESSION['full_name']  ); 
?>  
                </a>
                <a href="./otherpart/logout.php" class="btn">Logout</a>
            <?php else: ?>
                <a href="./otherpart/login.php" class="btn btn-secondary">Login</a>
                <a href="./otherpart/login.php" class="btn btn-secondary">Sign Up</a>
            <?php endif; ?>
        </div>
    </div>

    
</header>
    <div id="cookie-banner" class="cookie-bar">
    <div class="cookie-content">
        <p>If you click "allow", we can use cookies to provide you with tailored content... See our <a href="./otherpart/privacy.html">Privacy Statement</a>.</p>
        <div class="cookie-buttons">
            <button id="cookie-deny" class="btn-text">Use only necessary</button>
            <a href="javascript:void(0)" id="open-cookie-settings">Manage Cookies</a>
            <button id="cookie-allow" class="btn-primary">Allow</button>
        </div>
    </div>
    
<div id="cookie-settings-modal" class="cookie-modal-overlay">
    <div class="cookie-modal-container">
        <div class="cookie-modal-sidebar">
            <h3>About Cookies</h3>
            <ul>
                <li class="active-tab">Advertisement <span class="status-icon blue"><input type="checkbox"></span></li>
                <li>Messages <span class="status-icon blue"><input type="checkbox"></span></li>
                <li>Information <span class="status-icon red"><input type="checkbox"></span></li>
                <li>Analytics <span class="status-icon red"><input type="checkbox"></span></li>
            </ul>
        </div>
        <div class="cookie-modal-content">
            <h2>About Cookies</h2>
            <p>This site uses cookies mainly to improve and analyze your experience on our websites and for marketing purposes. Because we respect your right to privacy, you can choose not to allow some types of cookies...</p>
            <div class="cookie-modal-footer">
                <button id="save-cookie-settings" class="btn-outline">Save settings</button>
                <button id="accept-all-cookies" class="btn-filled">Accept All</button>
            </div>
        </div>
    </div>
</div>
</div>



<div id="drawerOverlay" class="drawer-overlay" onclick="toggleDrawer()"></div>
    <section class="hero">
        <div class="container">
            <h1>Delicious Ethiopian Food Delivered to Your Door</h1>
            <p>Order from your favorite local restaurants and enjoy authentic Ethiopian cuisine in the comfort of your home.</p>
            <a href="./restaurants/restaurants.php" class="btn">Order Now</a>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <div class="section-title">
                <h2 style="color: white;">How It Works</h2>
                <p style="color: white;">Getting your favorite Ethiopian food has never been easier</p>
            </div>
            
            <div class="features-grid">
              <div class="search-bar">
                <div class="feature card">
                    <i class="fas fa-search-location"></i>
                    <h3>Search Restaurants</h3>
                    <p>Browse through our extensive list of Ethiopian restaurants in your area.</p>
                </div>
                </div>
                <div class="feature card">
                    <i class="fas fa-utensils"></i>
                    <h3>Choose Your Food</h3>
                    <p>Select from a variety of traditional Ethiopian dishes and customize your order.</p>
                </div>
                
                <div class="feature card">
                    <i class="fas fa-truck"></i>
                    <h3>Fast Delivery</h3>
                    <p>Enjoy quick and reliable delivery right to your doorstep.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="restaurants">
        <div class="container">
            <div class="section-title">
                <h2>Popular Restaurants</h2>
                <p>Discover the best Ethiopian restaurants in your city</p>
            </div>
            
            <div class="restaurants-grid">
                <div class="restaurant-card">
                    <div class="restaurant-img" style="background-image: url('./image/Vegetarischgerecht-450x300px.jpg');"></div>
                    <div class="restaurant-info">
                        <h3>Addis Ababa Restaurant</h3>
                        <p>Authentic Ethiopian cuisine with traditional coffee ceremony</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9734;
                            <span>4.5 (128 reviews)</span>
                        </div>
                        <a href="restaurants/menu/adisabeba.php" class="btn">View Menu</a>
                    </div>
                </div>
                
                <div class="restaurant-card">
                    <div class="restaurant-img" style="background-image: url('./image/res_food_back.avif');"></div>
                    <div class="restaurant-info">
                        <h3>Habesha Kitchen</h3>
                        <p>Modern twist on traditional Ethiopian dishes as your choice</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9734;
                            <span>4.0 (95 reviews)</span>
                        </div>
                        <a href="restaurants/menu/habesha.php" class="btn">View Menu</a>
                    </div>
                </div>
                
                <div class="restaurant-card">
                    <div class="restaurant-img" style="background-image: url('./image/res_food_back2.avif');"></div>
                    <div class="restaurant-info">
                        <h3>Blue Nile Ethiopian</h3>
                        <p>Family-owned restaurant with vegan options and good service</p>
                        <div class="rating">
                            &#9733;&#9733;&#9733;&#9733;&#9733;
                            <span>5.0 (210 reviews)</span>
                        </div>
                        <a href="restaurants/menu/blue.php" class="btn">View Menu</a>
                    </div>
                </div>
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
                        <a href="https://www.facebook.com/yourpage" target="_blank" rel="noopener"><img src="./image/facebook.svg" alt="#"></a>
                        <a href="https://www.instagram.com/yourpage" target="_blank" rel="noopener"><img src="./image/instagram.svg" alt="#"></a>
                        <a href="https://t.me/yourusername" target="_blank" rel="noopener"><img src="./image/telegram.svg" alt="#"></a>
                        <a href="https://twitter.com/yourhandle" target="_blank" rel="noopener"><img src="./image/twitter.svg" alt="#"></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="#">Restaurants</a></li>
                        <li><a href="./otherpart/howwork.php">How It Works</a></li>
                        <li><a href="./otherpart/aboutus.php">About Us</a></li>
                        <li><a href="./otherpart/faq.php">FAQ</a></li>
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
                        <li>☎ +25 973 391 342</li>
                        <li>✉ ethiofood@gmail.com</li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
<p>&copy; <?php echo date("Y"); ?> ETHIO FOOD Delivery Service. All rights reserved.</p>            </div>
        </div>
    </footer>
        <script src="scripting/main.js"></script>
</body>
</html>