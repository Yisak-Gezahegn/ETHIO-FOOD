<?php
session_start();
require_once '../db.php';


$restaurant_id = 4; // Rehoboth Restaurant

// Fetch restaurant details
$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE id = ?");
$stmt->execute([$restaurant_id]);
$restaurant = $stmt->fetch();

// Fetch menu items
$stmt = $pdo->prepare("SELECT * FROM menu_items WHERE restaurant_id = ? ORDER BY category, name");
$stmt->execute([$restaurant_id]);
$menu_items = $stmt->fetchAll();
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
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
    <title><?php echo htmlspecialchars($restaurant['name']); ?> | FoodExpress</title>
    <link rel="stylesheet" href="../style/rehobot.css">
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
                    <img src="../../image/food7.jpg" 
                         alt="Rehoboth Restaurant">
                </div>
                <div class="restaurant-info">
                    <h1>Rehoboth Ethiopian Fusion</h1>
                    <div class="rating">
                        <i class="fas fa-star"></i> 4.9
                    </div>
                    <p class="cuisine">Ethiopian Fusion</p>
                    <p class="description">A fusion of traditional Ethiopian flavors with modern culinary techniques. Experience unique coffee and dessert pairings in a contemporary setting.</p>
                    <div class="delivery-info">
                        <span><i class="fas fa-clock"></i> 15-25 min</span>
                        <span><i class="fas fa-shipping-fast"></i> ETB 1.99 delivery</span>
                        <span><i class="fas fa-mug-hot"></i> Coffee Fusion</span>
                        <span><i class="fas fa-cookie-bite"></i> Chocolate Specials</span>
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
                    <a href="../../otherpart/login.php" class="checkout-btn" style="text-align:center; display:block; text-decoration:none;">Login to Order</a>
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
            <a href="#fusion-dishes" class="tab-link active">Fusion Dishes</a>
            <a href="#vegetarian" class="tab-link">Vegetarian</a>
            <a href="#desserts" class="tab-link">Desserts</a>
            <a href="#coffee" class="tab-link">Coffee</a>
            <a href="#drinks" class="tab-link">Drinks</a>
        </div>

        <!-- Fusion Dishes -->
        <section class="menu-category" id="fusion-dishes">
            <h2 class="category-title">Fusion Dishes</h2>
            <div class="menu-grid">
                <div class="menu-item">
                    <div class="item-image">
                        <img src="../../image/food7.jpg" alt="Fusion Tibs">
                        <span class="popular-badge">Popular</span>
                        <span class="spicy-badge">Spicy</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Fusion Tibs</h3>
                            <span class="item-price">ETB 22.99</span>
                        </div>
                        <p class="item-desc">Sautéed beef with bell peppers, mushrooms, and special fusion sauce.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="28">
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
                        <img src="../../image/chicken.jfif" alt="Coffee-Rubbed Chicken">
                        <span class="popular-badge">Popular</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Coffee-Rubbed Chicken</h3>
                            <span class="item-price">ETB 20.99</span>
                        </div>
                        <p class="item-desc">Chicken marinated in Ethiopian coffee and fusion spices, grilled to perfection.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="29">
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
                        <img src="../../image/res_food_back2.avif" alt="Ethiopian Spiced Burger">
                        <span class="spicy-badge">Spicy</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Ethiopian Spiced Burger</h3>
                            <span class="item-price">ETB 18.99</span>
                        </div>
                        <p class="item-desc">Beef burger with berbere spices, special sauce, and fusion toppings.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="30">
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
            <h2 class="category-title">Vegetarian Fusion</h2>
            <div class="menu-grid">
                <div class="menu-item">
                    <div class="item-image">
                        <img src="../../image/lentil_burger.jfif" alt="Spicy Lentil Burger">
                        <span class="veg-badge">Vegetarian</span>
                        <span class="spicy-badge">Spicy</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Spicy Lentil Burger</h3>
                            <span class="item-price">ETB 16.99</span>
                        </div>
                        <p class="item-desc">Vegetarian burger with Ethiopian spiced lentils and fusion toppings.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="31">
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
                        <img src="../../image/teff_pancakes.jfif" alt="Teff Pancakes">
                        <span class="veg-badge">Vegetarian</span>
                        <span class="popular-badge">Popular</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Teff Pancakes</h3>
                            <span class="item-price">ETB 12.99</span>
                        </div>
                        <p class="item-desc">Pancakes made with teff flour, served with honey and fusion fruit compote.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="32">
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
                        <img src="../../image/fusion_veg_platter.jfif" alt="Fusion Veg Platter">
                        <span class="veg-badge">Vegetarian</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Fusion Veg Platter</h3>
                            <span class="item-price">ETB 18.99</span>
                        </div>
                        <p class="item-desc">Sampler of 5 fusion vegetarian dishes with modern twists.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="33">
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

        <!-- Desserts & Coffee -->
        <section class="menu-category" id="desserts">
            <h2 class="category-title">Desserts & Coffee</h2>
            <div class="menu-grid">
                <div class="menu-item">
                    <div class="item-image">
                        <img src="../../image/chocolate_berbere_cake.jfif" alt="Chocolate Berbere Cake">
                        <span class="veg-badge">Vegetarian</span>
                        <span class="popular-badge">Popular</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Chocolate Berbere Cake</h3>
                            <span class="item-price">ETB 8.99</span>
                        </div>
                        <p class="item-desc">Chocolate cake with a hint of berbere spice, served with coffee cream.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="34">
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
                        <img src="../../image/ethiopian_latte.jfif" alt="Ethiopian Latte">
                        <span class="veg-badge">Vegetarian</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Ethiopian Latte</h3>
                            <span class="item-price">ETB 6.49</span>
                        </div>
                        <p class="item-desc">Coffee with steamed milk, cardamom, and fusion spices.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="35">
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
                        <img src="../../image/coffee_creme_brulee.jpg" alt="Coffee Creme Brulee">
                        <span class="veg-badge">Vegetarian</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Coffee Crème Brûlée</h3>
                            <span class="item-price">ETB 7.99</span>
                        </div>
                        <p class="item-desc">Crème brûlée with Ethiopian coffee flavor and caramelized sugar top.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="36">
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
            <p>&copy; 2023 FoodExpress. Rehoboth Ethiopian Fusion.</p>
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