<?php
/**
 * Restaurants Listing Page
 * Food Ordering Website
 */

// Start session for cart functionality
session_start();

// Include database connection
require_once 'db.php';

// Initialize variables
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$cuisine_filter = isset($_GET['cuisine']) ? $_GET['cuisine'] : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'rating_desc';

// Build SQL query with filters
$sql = "SELECT * FROM restaurants WHERE 1=1";
$params = [];

// Search filter
if (!empty($search)) {
    $sql .= " AND (name LIKE :search OR description LIKE :search OR cuisine LIKE :search)";
    $params[':search'] = "%$search%";
}

// Cuisine filter
if (!empty($cuisine_filter) && $cuisine_filter !== 'all') {
    $sql .= " AND FIND_IN_SET(:cuisine, cuisine) > 0";
    $params[':cuisine'] = $cuisine_filter;
}

// Sorting
switch ($sort_by) {
    case 'rating_desc':
        $sql .= " ORDER BY rating DESC";
        break;
        
    case 'rating_asc':
        $sql .= " ORDER BY rating ASC";
        break;
        
    case 'name_asc':
        $sql .= " ORDER BY name ASC";
        break;

    case 'name_desc':
        $sql .= " ORDER BY name DESC";
        break;

    case 'delivery_time':
        $sql .= " ORDER BY delivery_time ASC";
        break;

    case 'delivery_fee':
        $sql .= " ORDER BY delivery_fee ASC";
        break;

    case 'newest':
        $sql .= " ORDER BY created_at DESC";
        break;

    default:
        $sql .= " ORDER BY rating DESC";
        break;
}

// Execute query
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_restaurants = count($restaurants);
} catch (PDOException $e) {
    die("Error fetching restaurants: " . $e->getMessage());
}

// Get unique cuisines for filter
try {
    $cuisine_query = "SELECT GROUP_CONCAT(DISTINCT cuisine) as all_cuisines FROM restaurants";
    $stmt = $pdo->query($cuisine_query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $all_cuisines = [];
    if ($result && !empty($result['all_cuisines'])) {
        $cuisine_array = explode(',', $result['all_cuisines']);
        foreach ($cuisine_array as $cuisine) {
            $cuisine = trim($cuisine);
            if (!empty($cuisine) && !in_array($cuisine, $all_cuisines)) {
                $all_cuisines[] = $cuisine;
            }
        }
        sort($all_cuisines);
    }
} catch (PDOException $e) {
    $all_cuisines = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ethiopian Restaurants | Taste of Ethiopia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./style/resturants.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍛</text></svg>">
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="../index.php">
                        &#127839
                        <span class="logo-text">Taste of <span class="highlight">Ethiopia</span></span>
                    </a>
                </div>
                
                <div class="search-bar">
                    <form method="GET" action="restaurants.php">
                        <div class="search-wrapper">
                            <input type="text" name="search" placeholder="Search restaurants, injera, wot, tibs..." 
                                   value="<?php echo htmlspecialchars($search); ?>">
                            <button type="submit" class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
                
                <nav class="main-nav">
                    <ul>
                        <li><a href="restaurants.php" class="active"><i class="fas fa-store"></i> Restaurants</a></li>
                        <li><a href="#"><i class="fas fa-percentage"></i> Specials</a></li>
                        <li><a href="#"><i class="fas fa-shopping-cart"></i> Cart <span class="cart-count">0</span></a></li>
                        <li><a href="../customer/customer.php"><i class="fas fa-user"></i> Account</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Page Title -->
            <div class="page-header">
                <h1><i class="fas fa-pepper-hot"></i> Discover Ethiopian Restaurants</h1>
                <p class="subtitle">Authentic Ethiopian cuisine delivered fresh to your door. Experience the rich flavors of Ethiopia!</p>
                <div class="restaurant-count">
                    <i class="fas fa-store"></i> 
                    <span><?php echo $total_restaurants; ?> Ethiopian restaurants available</span>
                </div>
            </div>
            
            <!-- Filters Section -->
            <div class="filters-section">
                <div class="filter-group">
                    <h3><i class="fas fa-filter"></i> Filter by Cuisine Type:</h3>
                    <div class="filter-buttons">
                        <a href="?<?php echo http_build_query(array_merge($_GET, ['cuisine' => 'all'])); ?>" 
                           class="filter-btn <?php echo ($cuisine_filter === 'all' || empty($cuisine_filter)) ? 'active' : ''; ?>">
                            All Ethiopian
                        </a>
                        <?php if (!empty($all_cuisines)): ?>
                            <?php foreach ($all_cuisines as $cuisine): ?>
                            <a href="?<?php echo http_build_query(array_merge($_GET, ['cuisine' => $cuisine])); ?>" 
                               class="filter-btn <?php echo ($cuisine_filter === $cuisine) ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($cuisine); ?>
                            </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="filter-group">
                    <h3><i class="fas fa-sort"></i> Sort by:</h3>
                    <div class="sort-options">
                        <select class="sort-select" onchange="window.location.href=this.value">
                            <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'rating_desc'])); ?>"
                                <?php echo ($sort_by === 'rating_desc') ? 'selected' : ''; ?>>Highest Rating</option>
                            <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'rating_asc'])); ?>"
                                <?php echo ($sort_by === 'rating_asc') ? 'selected' : ''; ?>>Lowest Rating</option>
                            <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'name_asc'])); ?>"
                                <?php echo ($sort_by === 'name_asc') ? 'selected' : ''; ?>>Name: A-Z</option>
                            <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'name_desc'])); ?>"
                                <?php echo ($sort_by === 'name_desc') ? 'selected' : ''; ?>>Name: Z-A</option>
                            <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'delivery_time'])); ?>"
                                <?php echo ($sort_by === 'delivery_time') ? 'selected' : ''; ?>>Fastest Delivery</option>
                            <option value="?<?php echo http_build_query(array_merge($_GET, ['sort' => 'delivery_fee'])); ?>"
                                <?php echo ($sort_by === 'delivery_fee') ? 'selected' : ''; ?>>Lowest Delivery Fee</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Restaurants Grid -->
            <div class="restaurants-grid">
                <?php if ($total_restaurants > 0): ?>
                    <?php foreach ($restaurants as $restaurant): ?>
                    <div class="restaurant-card">
                        <div class="card-image">
                            <img src="<?php echo htmlspecialchars($restaurant['image_url']); ?>" 
                                 alt="<?php echo htmlspecialchars($restaurant['name']); ?>"
                                 loading="lazy">
                            <div class="card-badge">
                                <span class="rating-badge">
                                    <i class="fas fa-star"></i> <?php echo number_format($restaurant['rating'], 1); ?>
                                </span>
                                <?php if ($restaurant['is_featured']): ?>
                                <span class="featured-badge">
                                    <i class="fas fa-crown"></i> Featured
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="card-content">
                            <div class="card-header">
                                <h3 class="restaurant-name"><?php echo htmlspecialchars($restaurant['name']); ?></h3>
                                <div class="restaurant-location">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo htmlspecialchars($restaurant['location']); ?></span>
                                </div>
                                <div class="cuisine-tags">
                                    <?php 
                                    $cuisines = explode(',', $restaurant['cuisine']);
                                    foreach ($cuisines as $tag): 
                                        $tag = trim($tag);
                                        if (!empty($tag)):
                                    ?>
                                    <span class="cuisine-tag"><?php echo htmlspecialchars($tag); ?></span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            </div>
                            
                            <p class="restaurant-desc"><?php echo htmlspecialchars($restaurant['description']); ?></p>
                            
                            <div class="restaurant-features">
                                <?php if ($restaurant['has_vegetarian']): ?>
                                <span class="feature-tag vegetarian"><i class="fas fa-leaf"></i> Vegetarian</span>
                                <?php endif; ?>
                                <?php if ($restaurant['has_vegan']): ?>
                                <span class="feature-tag vegan"><i class="fas fa-seedling"></i> Vegan Options</span>
                                <?php endif; ?>
                                <?php if ($restaurant['has_spicy']): ?>
                                <span class="feature-tag spicy"><i class="fas fa-pepper-hot"></i> Spicy</span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="card-footer">
                                <div class="delivery-info">
                                    <div class="info-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?php echo htmlspecialchars($restaurant['delivery_time']); ?>-<?php echo htmlspecialchars($restaurant['delivery_time'] + 10); ?> min</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-shipping-fast"></i>
                                        <span>
                                            <?php 
                                            if ($restaurant['delivery_fee'] == 0) {
                                                echo "Free delivery";
                                            } else {
                                                echo "$" . number_format($restaurant['delivery_fee'], 2) . " delivery";
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-dollar-sign"></i>
                                        <span><?php echo htmlspecialchars($restaurant['price_range']); ?></span>
                                    </div>
                                </div>
                                <button class="order-btn" onclick="orderFromRestaurant(<?php echo $restaurant['id']; ?>)">
                                    <i class="fas fa-shopping-basket"></i> View Menu
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-results">
                        <i class="fas fa-search fa-3x"></i>
                        <h3>No restaurants found</h3>
                        <p>Try adjusting your search or filter criteria</p>
                        <a href="restaurants.php" class="clear-filters-btn">
                            <i class="fas fa-times"></i> Clear All Filters
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Results Info -->
            <?php if ($total_restaurants > 0): ?>
            <div class="results-info">
                <p>Showing <?php echo $total_restaurants; ?> Ethiopian restaurant<?php echo ($total_restaurants !== 1) ? 's' : ''; ?></p>
                <?php if (!empty($search) || (!empty($cuisine_filter) && $cuisine_filter !== 'all')): ?>
                <a href="restaurants.php" class="clear-filters-btn">
                    <i class="fas fa-times"></i> Clear All Filters
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-pepper-hot"></i> Taste of Ethiopia</h3>
                    <p>Delivering authentic Ethiopian cuisine to your doorstep. Experience the rich flavors and traditions of Ethiopia.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="../index.php">Home</a></li>
                        <li><a href="#">Restaurants</a></li>
                        <li><a href="../otherpart/howwork.php">How It Works</a></li>
                        <li><a href="../otherpart/aboutus.php">About Us</a></li>
                        <li><a href="../otherpart/faq.php">FAQ</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Haramaya, Ethiopia</p>
                    <p><i class="fas fa-phone"></i> +251 911 234 567</p>
                    <p><i class="fas fa-envelope"></i> order@tasteofethiopia.com</p>
                    <p><i class="fas fa-clock"></i> Open daily 10 AM - 10 PM</p>
                </div>
                <div class="footer-section">
                    <h3>Popular Dishes</h3>
                    <ol>
                        <li>	&#127835 Injera & Wat</li>
                        <li>	&#129367 Vegetarian</li>
                        <li>	&#127831 Meat Dishes</li>
                        <li>	&#127828 Humburger</li>
                        <li>	&#127829 Pizza</li>
                </ol>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2023 Taste of Ethiopia. All rights reserved. | Experience the authentic taste of Ethiopia</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Order button functionality
        function orderFromRestaurant(restaurantId) {
            window.location.href = 'menu.php?restaurant_id=' + restaurantId;
        }
        
        // Add to cart animation
        document.querySelectorAll('.order-btn').forEach(button => {
            button.addEventListener('click', function() {
                const restaurantName = this.closest('.restaurant-card').querySelector('.restaurant-name').textContent;
                
                // Animation effect
                this.innerHTML = '<i class="fas fa-check"></i> Opening Menu...';
                this.style.backgroundColor = '#27ae60';
                
                setTimeout(() => {
                    this.innerHTML = '<i class="fas fa-shopping-basket"></i> View Menu';
                    this.style.backgroundColor = '#e74c3c';
                }, 1500);
                
                // Show notification
                showNotification('Opening ' + restaurantName + ' menu...');
            });
        });
        
        // Notification function
        function showNotification(message) {
            // Remove existing notification
            const existing = document.querySelector('.notification');
            if (existing) existing.remove();
            
            const notification = document.createElement('div');
            notification.className = 'notification';
            notification.innerHTML = `
                <i class="fas fa-utensils"></i>
                <span>${message}</span>
            `;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 300);
            }, 2000);
        }
        
        // Update cart count from session
        document.addEventListener('DOMContentLoaded', function() {
            const cartCount = document.querySelector('.cart-count');
            // In a real app, you would fetch this from session/cookie
            cartCount.textContent = '0';
        });
    </script>
</body>
</html>