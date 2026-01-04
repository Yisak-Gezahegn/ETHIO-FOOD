<?php
session_start();
require_once '../db.php';

$restaurant_id = 2; // Blue Nile Restaurant

// Fetch restaurant details
$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = ?");
$stmt->execute([$restaurant_id]);
$restaurant = $stmt->fetch();

// Fetch menu items
$stmt = $pdo->prepare("SELECT * FROM menu_items WHERE restaurant_id = ? ORDER BY category, name");
$stmt->execute([$restaurant_id]);
$menu_items = $stmt->fetchAll();

// Group by category
$categories = [];
foreach ($menu_items as $item) {
    $categories[$item['category']][] = $item;
}

// Cart handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $item_id = $_POST['item_id'];
        $quantity = $_POST['quantity'] ?? 1;
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        if (isset($_SESSION['cart'][$item_id])) {
            $_SESSION['cart'][$item_id]['quantity'] += $quantity;
        } else {
            $stmt = $pdo->prepare("SELECT name, price FROM menu_items WHERE id = ?");
            $stmt->execute([$item_id]);
            $item_details = $stmt->fetch();
            
            $_SESSION['cart'][$item_id] = [
                'name' => $item_details['name'],
                'price' => $item_details['price'],
                'quantity' => $quantity,
                'restaurant_id' => $restaurant_id
            ];
        }
        $cart_message = "Item added to cart!";
    }
}

// Calculate cart totals
$cart_total = 0;
$cart_count = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $cart_total += $item['price'] * $item['quantity'];
        $cart_count += $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BlueNile Restaurant | FoodExpress</title>
    <link rel="stylesheet" href="../style/blue.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="main-nav">
        <div class="container">
            <div class="nav-content">
                <a href="../../index.php" class="logo">
                    &#127839 ETHIO FOOD
                </a>
                <div class="nav-links">
                    <a href="../../index.php"><i class="fas fa-home"></i> Home</a>
                    <a href="#" class="cart-btn">
                        <i class="fas fa-shopping-cart"></i> Cart
                        <span class="cart-count"><?php echo $cart_count; ?></span>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Restaurant Header -->
    <header class="restaurant-header">
        <div class="container">
            <div class="header-content">
                <div class="restaurant-image">
                    <img src="../../image/res_food_back2.avif" 
                         alt="Blue Nile Restaurant">
                </div>
                <div class="restaurant-info">
                    <h1>Blue Nile Ethiopia Restaurant</h1>
                    <div class="rating">
                        <i class="fas fa-star"></i> 4.6
                    </div>
                    <p class="cuisine">Blue Nile Region Ethiopian</p>
                    <p class="description">Named after the majestic river, we serve dishes from the Blue Nile region of Ethiopia. Specializing in unique regional recipes and fresh ingredients sourced from local farmers.</p>
                    <div class="delivery-info">
                        <span><i class="fas fa-clock"></i> 30-40 min</span>
                        <span><i class="fas fa-shipping-fast"></i> ETB 3.49 delivery</span>
                        <span><i class="fas fa-water"></i> Blue Nile Region</span>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Cart Sidebar -->
    <div class="cart-sidebar">
    <div class="cart-header">
        <h3><i class="fas fa-shopping-cart"></i> Your Order</h3>
        <button class="close-cart">&times;</button>
    </div>

    <form action="../../include/auth_handler.php" method="POST">
        <div class="cart-items">
            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                <?php foreach ($_SESSION['cart'] as $item_id => $item): ?>
                <div class="cart-item">
                    <div class="item-info">
                        <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                        <p>ETB<?php echo number_format($item['price'], 2); ?> × <?php echo $item['quantity']; ?></p>
                    </div>
                    <div class="item-total">
                        ETB<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="empty-cart">Your cart is empty</p>
            <?php endif; ?>
        </div>

        <div class="cart-footer">
            <div class="cart-total">
                <span>Total:</span>
                <span>ETB<?php echo number_format($cart_total, 2); ?></span>
                
                <input type="hidden" name="total_price" value="<?php echo $cart_total; ?>">
                <input type="hidden" name="restaurant_id" value="<?php echo $restaurant_id; ?>">
            </div>

            <?php if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])): ?>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <button type="submit" name="place_order" class="checkout-btn">Confirm & Place Order</button>
                <?php else: ?>
                    <a href="../../otherpart/login.php" class="btn" style="text-align:center; display:block; text-decoration:none;">Login to Order</a>
                <?php endif; ?>
            <?php else: ?>
                <button type="button" class="checkout-btn" disabled style="background: #ccc;">Cart Empty</button>
            <?php endif; ?>
        </div>
    </form>
</div>

    <!-- Main Menu -->
    <main class="container">
        <?php if (isset($cart_message)): ?>
        <div class="alert success">
            <i class="fas fa-check-circle"></i> <?php echo $cart_message; ?>
        </div>
        <?php endif; ?>

        <!-- Category Tabs -->
        <div class="category-tabs">
            <a href="#main-dishes" class="tab-link active">Main Dishes</a>
            <a href="#vegetarian" class="tab-link">Vegetarian</a>
            <a href="#injera" class="tab-link">Injera & Sides</a>
            <a href="#appetizers" class="tab-link">Appetizers</a>
            <a href="#drinks" class="tab-link">Drinks</a>
        </div>

        <!-- Menu Items -->
        <!-- Main Dishes -->
        <section class="menu-category" id="main-dishes">
            <h2 class="category-title">Main Dishes</h2>
            <div class="menu-grid">
                <div class="menu-item">
                    <div class="item-image">
                        <img src="../../image/fish.webp" alt="Blue Nile Fish">
                        <span class="popular-badge">Popular</span>
                        <span class="spicy-badge">Spicy</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Blue Nile Fish</h3>
                            <span class="item-price">ETB 24.99</span>
                        </div>
                        <p class="item-desc">Fresh tilapia marinated in Blue Nile spices, served with vegetables and special sauce.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="19">
                            <div class="quantity-selector">
                                <button type="button" class="qty-btn minus">-</button>
                                <input type="number" name="quantity" value="1" min="1" class="qty-input">
                                <button type="button" class="qty-btn plus">+</button>
                            </div>
                            <button type="submit" name="add_to_cart" class="add-cart-btn">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>

                <div class="menu-item">
                    <div class="item-image">
                        <img src="../../image/dulet.jpg" alt="Dulet">
                        <span class="spicy-badge">Spicy</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Dulet</h3>
                            <span class="item-price">ETB 17.99</span>
                        </div>
                        <p class="item-desc">Traditional mix of tripe, liver, lean meat, berbere, and Blue Nile spices.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="20">
                            <div class="quantity-selector">
                                <button type="button" class="qty-btn minus">-</button>
                                <input type="number" name="quantity" value="1" min="1" class="qty-input">
                                <button type="button" class="qty-btn plus">+</button>
                            </div>
                            <button type="submit" name="add_to_cart" class="add-cart-btn">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>

                <div class="menu-item">
                    <div class="item-image">
                        <img src="../../image/food7.jpg" alt="Special Tibs">
                        <span class="popular-badge">Popular</span>
                        <span class="spicy-badge">Spicy</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Special Tibs</h3>
                            <span class="item-price">ETB 20.99</span>
                        </div>
                        <p class="item-desc">Beef tibs with awaze sauce and special Blue Nile region spices.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="21">
                            <div class="quantity-selector">
                                <button type="button" class="qty-btn minus">-</button>
                                <input type="number" name="quantity" value="1" min="1" class="qty-input">
                                <button type="button" class="qty-btn plus">+</button>
                            </div>
                            <button type="submit" name="add_to_cart" class="add-cart-btn">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Vegetarian -->
        <section class="menu-category" id="vegetarian">
            <h2 class="category-title">Vegetarian Dishes</h2>
            <div class="menu-grid">
                <div class="menu-item">
                    <div class="item-image">
                        <img src="../../image/d61c92_137b357e1eff4e978b97db6aa36f51ab~mv2.avif" alt="Beyaynetu">
                        <span class="popular-badge">Popular</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Beyaynetu</h3>
                            <span class="item-price">ETB 15.99</span>
                        </div>
                        <p class="item-desc">Vegetarian combination platter with 7 different dishes from Blue Nile region.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="22">
                            <div class="quantity-selector">
                                <button type="button" class="qty-btn minus">-</button>
                                <input type="number" name="quantity" value="1" min="1" class="qty-input">
                                <button type="button" class="qty-btn plus">+</button>
                            </div>
                            <button type="submit" name="add_to_cart" class="add-cart-btn">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>

                <div class="menu-item">
                    <div class="item-image">
                        <img src="../../image/fasolia.jfif" alt="Fasolia">
                        <span class="veg-badge">Vegetarian</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Fasolia</h3>
                            <span class="item-price">ETB 10.99</span>
                        </div>
                        <p class="item-desc">String beans and carrots sautéed with onions, garlic, and turmeric.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="23">
                            <div class="quantity-selector">
                                <button type="button" class="qty-btn minus">-</button>
                                <input type="number" name="quantity" value="1" min="1" class="qty-input">
                                <button type="button" class="qty-btn plus">+</button>
                            </div>
                            <button type="submit" name="add_to_cart" class="add-cart-btn">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>

                <div class="menu-item">
                    <div class="item-image">
                        <img src="../../image/kik_alicha.jpg" alt="Kik Alicha">
                        <span class="veg-badge">Vegetarian</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Kik Alicha</h3>
                            <span class="item-price">ETB 12.99</span>
                        </div>
                        <p class="item-desc">Split peas cooked with turmeric, ginger, and mild Blue Nile spices.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="24">
                            <div class="quantity-selector">
                                <button type="button" class="qty-btn minus">-</button>
                                <input type="number" name="quantity" value="1" min="1" class="qty-input">
                                <button type="button" class="qty-btn plus">+</button>
                            </div>
                            <button type="submit" name="add_to_cart" class="add-cart-btn">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <!-- Injera & Sides -->
        <section class="menu-category" id="injera">
            <h2 class="category-title">Injera & Sides</h2>
            <div class="menu-grid">
                <div class="menu-item">
                    <div class="item-image">
                        <img src="../../image/injera.jpg" alt="Special Injera">
                        <span class="veg-badge">Vegetarian</span>
                        <span class="popular-badge">Popular</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Special Injera</h3>
                            <span class="item-price">ETB 5.99</span>
                        </div>
                        <p class="item-desc">Injera made with mixed teff and barley flour, Blue Nile style.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="25">
                            <div class="quantity-selector">
                                <button type="button" class="qty-btn minus">-</button>
                                <input type="number" name="quantity" value="1" min="1" class="qty-input">
                                <button type="button" class="qty-btn plus">+</button>
                            </div>
                            <button type="submit" name="add_to_cart" class="add-cart-btn">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>

                <div class="menu-item">
                    <div class="item-image">
                        <img src="../../image/kolo.webp" alt="Kolo">
                        <span class="veg-badge">Vegetarian</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Kolo</h3>
                            <span class="item-price">ETB 4.99</span>
                        </div>
                        <p class="item-desc">Roasted barley snack with nuts, chickpeas, and Blue Nile spices.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="26">
                            <div class="quantity-selector">
                                <button type="button" class="qty-btn minus">-</button>
                                <input type="number" name="quantity" value="1" min="1" class="qty-input">
                                <button type="button" class="qty-btn plus">+</button>
                            </div>
                            <button type="submit" name="add_to_cart" class="add-cart-btn">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>

                <div class="menu-item">
                    <div class="item-image">
                        <img src="../../image/tej.jfif" alt="Tej">
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Tej</h3>
                            <span class="item-price">ETB 9.99</span>
                        </div>
                        <p class="item-desc">Traditional Ethiopian honey wine, Blue Nile region recipe.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="27">
                            <div class="quantity-selector">
                                <button type="button" class="qty-btn minus">-</button>
                                <input type="number" name="quantity" value="1" min="1" class="qty-input">
                                <button type="button" class="qty-btn plus">+</button>
                            </div>
                            <button type="submit" name="add_to_cart" class="add-cart-btn">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="menu-footer">
        <div class="container">
            <p>&copy; 2023 FoodExpress. Blue Nile Ethiopia Restaurant.</p>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Cart toggle
        document.querySelector('.cart-btn').addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('.cart-sidebar').classList.add('show');
        });

        document.querySelector('.close-cart').addEventListener('click', function() {
            document.querySelector('.cart-sidebar').classList.remove('show');
        });

        // Quantity buttons
        document.querySelectorAll('.qty-btn').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.qty-input');
                let value = parseInt(input.value);
                
                if (this.classList.contains('plus')) {
                    value++;
                } else if (this.classList.contains('minus') && value > 1) {
                    value--;
                }
                
                input.value = value;
            });
        });

        // Category tabs
        document.querySelectorAll('.tab-link').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetSection = document.querySelector(targetId);
                
                // Scroll to section
                targetSection.scrollIntoView({ behavior: 'smooth' });
                
                // Highlight active tab
                document.querySelectorAll('.tab-link').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        // Checkout button
        document.querySelector('.checkout-btn').addEventListener('click', function() {
            if (<?php echo $cart_count; ?> === 0) {
                alert('Your cart is empty!');
                return;
            }
            alert('Proceeding to checkout! Total: $<?php echo number_format($cart_total, 2); ?>');
            // In real app: window.location.href = 'checkout.php';
        });
    </script>
</body>
</html>