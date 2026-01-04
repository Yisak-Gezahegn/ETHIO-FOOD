<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQs - QuickBite Food Ordering</title>
    <link rel="stylesheet" href="./styling/faq.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <header>
        <div class="container header-container">
            <a href="../index.php" class="logo">
                &#127839
                ETHIO FOOD
            </a>
            
            <div class="mobile-menu">
          
            </div>
            
            <nav>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../restaurants/restaurants.php">Restaurants</a></li>
                    <li><a href="./howwork.php">How It Works</a></li>
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
        </div>
    </header>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Hero Section -->
            <section class="hero">
                <div class="hero-content">
                    <h1>Frequently Asked Questions</h1>
                    <p>Find answers to common questions about ordering food, payments, deliveries, and more from your favorite restaurants.</p>
                    
                    <!-- Search Bar -->
                    <div class="search-container">
                        <i class="fas fa-search"></i>
                        <input type="text" id="faq-search" placeholder="Search for questions or keywords...">
                        <button id="search-btn">Search</button>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="../image/dulet.jpg" alt="Food delivery illustration">
                </div>
            </section>

            <!-- FAQ Categories -->
            <section class="categories">
                <h2>Browse by Category</h2>
                <div class="category-tags">
                    <button class="category-tag active" data-category="all">All Questions</button>
                    <button class="category-tag" data-category="ordering">Ordering</button>
                    <button class="category-tag" data-category="delivery">Delivery</button>
                    <button class="category-tag" data-category="payment">Payments</button>
                    <button class="category-tag" data-category="account">Account</button>
                    <button class="category-tag" data-category="restaurants">Restaurants</button>
                </div>
            </section>

            <!-- FAQ List -->
            <section class="faq-section">
                <h2>Common Questions</h2>
                <p class="results-info">Showing <span id="results-count"></span> questions</p>
                
                <div class="faq-container">
                    <!-- FAQ Item 1 -->
                    <div class="faq-item" data-category="ordering">
                        <div class="faq-question">
                            <h3>How do I place an order?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>To place an order, simply search for a restaurant or cuisine, select your desired items, customize them if needed, and proceed to checkout. You can pay online or choose cash on delivery.</p>
                            <p>You can also save your favorite restaurants for faster ordering in the future.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 2 -->
                    <div class="faq-item" data-category="delivery">
                        <div class="faq-question">
                            <h3>How long does delivery take?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Delivery times vary based on restaurant preparation time, distance, and traffic conditions. Typically, deliveries arrive within 30-45 minutes. You can track your order in real-time from the "My Orders" section.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 3 -->
                    <div class="faq-item" data-category="payment">
                        <div class="faq-question">
                            <h3>What payment methods do you accept?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>We accept various payment methods including credit/debit cards, digital wallets (PayPal, Apple Pay, Google Pay), and cash on delivery. Some restaurants may have specific payment options available.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 4 -->
                    <div class="faq-item" data-category="ordering">
                        <div class="faq-question">
                            <h3>Can I modify or cancel my order?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>You can modify or cancel your order within 5 minutes of placing it, as long as the restaurant hasn't started preparing it. Go to "My Orders" and click on the order you want to modify or cancel.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 5 -->
                    <div class="faq-item" data-category="account">
                        <div class="faq-question">
                            <h3>How do I reset my password?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Click on "Forgot Password" on the login page. Enter your registered email address, and we'll send you a link to reset your password. The link will expire in 24 hours for security reasons.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 6 -->
                    <div class="faq-item" data-category="delivery">
                        <div class="faq-question">
                            <h3>Is there a minimum order value?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Minimum order values vary by restaurant. You'll see the minimum order requirement on the restaurant's page before you start ordering. Some restaurants may not have a minimum order requirement.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 7 -->
                    <div class="faq-item" data-category="restaurants">
                        <div class="faq-question">
                            <h3>How are restaurants selected on your platform?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>We partner with restaurants that maintain high food quality, hygiene standards, and reliable service. All restaurants go through a verification process before joining our platform.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 8 -->
                    <div class="faq-item" data-category="payment">
                        <div class="faq-question">
                            <h3>Are there any delivery fees?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Delivery fees vary based on restaurant location and distance. The fee will be clearly displayed before you confirm your order. Some restaurants offer free delivery for orders above a certain amount.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 9 -->
                    <div class="faq-item" data-category="ordering">
                        <div class="faq-question">
                            <h3>Can I schedule orders for later?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Yes! During checkout, you can select "Schedule Order" and choose your preferred date and time. Scheduled orders can be placed up to 7 days in advance.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 10 -->
                    <div class="faq-item" data-category="account">
                        <div class="faq-question">
                            <h3>How do I update my delivery address?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Go to "Account Settings" and select "Addresses". You can add, edit, or delete delivery addresses. You can also set a default address for faster checkout.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 11 -->
                    <div class="faq-item" data-category="restaurants">
                        <div class="faq-question">
                            <h3>What if a restaurant is out of an item I ordered?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>If a restaurant runs out of an item you've ordered, they'll contact you directly to suggest a replacement or offer a refund for that item. You'll be notified through the app or via phone call.</p>
                        </div>
                    </div>
                    
                    <!-- FAQ Item 12 -->
                    <div class="faq-item" data-category="delivery">
                        <div class="faq-question">
                            <h3>What is your delivery radius?</h3>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                        <div class="faq-answer">
                            <p>Delivery radius varies by restaurant. Most restaurants deliver within a 5-8 km radius. You can check if a restaurant delivers to your location by entering your address on the restaurant page.</p>
                        </div>
                    </div>
                </div>
                
                <!-- No Results Message (hidden by default) -->
                <div class="no-results" id="no-results">
                    <i class="fas fa-search"></i>
                    <h3>No matching questions found</h3>
                    <p>Try searching with different keywords or browse by category</p>
                </div>
            </section>

            <!-- Contact CTA -->
            <section class="contact-cta">
                <div class="cta-content">
                    <h2>Still have questions?</h2>
                    <p>Our customer support team is here to help you 24/7</p>
                    <div class="cta-buttons">
                        <a href="#" class="btn btn-primary"><i class="fas fa-comments"></i> Live Chat</a>
                        <a href="tel:+18005551234" class="btn btn-secondary"><i class="fas fa-phone"></i> Call Support</a>
                        <a href="mailto:support@quickbite.com" class="btn btn-outline"><i class="fas fa-envelope"></i> Email Us</a>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
<footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3 style="color:white">ETHIO FOOD</h3>
                    <p>Delivering authentic Ethiopian cuisine to your doorstep. Fast, reliable, and delicious.</p>
                    <div class="social-links">
                        <a href="#"><img src="../image/facebook.svg" alt="#"></a>
                        <a href="#"><img src="../image/instagram.svg" alt="#"></a>
                        <a href="#"><img src="../image/telegram.svg" alt="#"></a>
                        <a href="#"><img src="../image/twitter.svg" alt="#"></a>
                    </div>
                </div>
                
                <div class="footer-col">
                    <h3 style="color:white">Quick Links</h3>
                    <ul>
                        <li><a href="../index.php">Home</a></li>
                        <li><a href="#">Restaurants</a></li>
                        <li><a href="howwork.php">How It Works</a></li>
                        <li><a href="aboutus.php">About Us</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3 style="color:white">Categories</h3>
                    <ul>
                        <li><a href="#">&#127835 Injera & Wat</a></li>
                        <li><a href="#">&#129367 Vegetarian</a></li>
                        <li><a href="#">&#127831 Meat Dishes</a></li>
                        <li><a href="#">&#127828 Humburger</a></li>
                        <li><a href="#">&#127829 Pizza</a></li>
                    </ul>
                </div>
                
                <div class="footer-col">
                    <h3 style="color:white">Contact Us</h3>
                   <ul>
                        <li>📍 Addis Ababa, Ethiopia</li>
                        <li>☎ +25 973 391 342</li>
                        <li>✉ info@ethiofood.com</li>
                    </ul>
                </div>
            </div>
            
            <div class="copyright">
                <p>&copy; 2023 ETHIO FOOD Delivery Service. All rights reserved.</p>
            </div>
        </div>
    </footer>
    </div>
    <script src="./scripting/faq.js"></script>
</body>
</html>