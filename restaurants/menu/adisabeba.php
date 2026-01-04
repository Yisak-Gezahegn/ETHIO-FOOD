<?php
session_start();
require_once '../db.php';

$restaurant_id = 1; // Blue Nile Restaurant

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
    <title>AddisAbaba Restaurant| FoodExpress</title>
    <link rel="stylesheet" href="../style/adisabeba.css">
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
                    <img src="../../image/Vegetarischgerecht-450x300px.jpg" alt="Addis Ababa Restaurant">
                </div>
                <div class="restaurant-info">
                    <h1>Addis Ababa Restaurant</h1>
                    <div class="rating">
                        <i class="fas fa-star"></i> 4.8
                    </div>
                    <p class="cuisine">Ethiopian & East African</p>
                    <p class="description">Experience authentic Ethiopian cuisine in the heart of the city. Our dishes are prepared with traditional spices and served in the customary Ethiopian style. Fresh injera baked daily, vegetarian options available.</p>
                    <div class="delivery-info">
                        <span><i class="fas fa-clock"></i> 25-35 min</span>
                        <span><i class="fas fa-shipping-fast"></i> ETB 2.99 delivery</span>
                        <span><i class="fas fa-fire"></i> Spicy options available</span>
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
                    <a href="../../otherpart/login.php" class="btn" style="text-align:center; display:block; text-decoration:none; background-color:red:">Login to Order</a>
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
                        <img src="../../image/food9.avif" alt="Doro Wat">
                        <span class="popular-badge">Popular</span>
                        <span class="spicy-badge">Spicy</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Doro Wat</h3>
                            <span class="item-price">ETB 18.99</span>
                        </div>
                        <p class="item-desc">Ethiopian spicy chicken stew simmered with berbere spices, onions, and served with hard-boiled eggs.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="1">
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
                        <img src="../../image/food5.jpg" alt="Kitfo">
                        <span class="spicy-badge">Spicy</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Kitfo</h3>
                            <span class="item-price">ETB 21.99</span>
                        </div>
                        <p class="item-desc">Minced raw beef marinated in mitmita and niter kibbeh, served with injera and ayib cheese.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="2">
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
                        <img src="../../image/food7.jpg" alt="Tibs">
                        <span class="popular-badge">Popular</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Tibs</h3>
                            <span class="item-price">ETB 16.99</span>
                        </div>
                        <p class="item-desc">Sautéed beef or lamb strips with onions, tomatoes, rosemary, and Ethiopian spices.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="3">
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
                        <img src="../../image/misirwet.webp" alt="Misir Wat">
                        <span class="veg-badge">Vegetarian</span>
                        <span class="spicy-badge">Spicy</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Misir Wat</h3>
                            <span class="item-price">ETB 14.99</span>
                        </div>
                        <p class="item-desc">Spicy red lentils cooked with berbere spice blend, onions, garlic, and ginger.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="4">
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
                        <img src="../../image/vegitirian.jpg" alt="Shiro">
                        <span class="veg-badge">Vegetarian</span>
                        <span class="popular-badge">Popular</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Shiro</h3>
                            <span class="item-price">ETB 13.99</span>
                        </div>
                        <p class="item-desc">Ground chickpea stew cooked with onions, garlic, and Ethiopian spices.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="5">
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
                        <img src="../../image/vegetable_combination.jpg" alt="Vegetable Combination">
                        <span class="veg-badge">Vegetarian</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Vegetable Combination</h3>
                            <span class="item-price">ETB 17.99</span>
                        </div>
                        <p class="item-desc">Sampler of 5 vegetarian dishes: misir wat, shiro, gomen, atakilt wat, and fasolia.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="6">
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
                        <img src="../../image/injera.jpg" alt="Fresh Injera">
                        <span class="veg-badge">Vegetarian</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Fresh Injera</h3>
                            <span class="item-price">ETB 4.99</span>
                        </div>
                        <p class="item-desc">Traditional Ethiopian sourdough flatbread made from teff flour.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="7">
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
                        <img src="../../image/food3.avif" alt="Ayib">
                        <span class="veg-badge">Vegetarian</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Ayib</h3>
                            <span class="item-price">ETB 3.99</span>
                        </div>
                        <p class="item-desc">Traditional Ethiopian cottage cheese, perfect complement to spicy dishes.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="8">
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
                        <img src="../../image/timatim_fitfit.jpg" alt="Timatim Fitfit">
                        <span class="veg-badge">Vegetarian</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Timatim Fitfit</h3>
                            <span class="item-price">ETB 5.99</span>
                        </div>
                        <p class="item-desc">Injera pieces mixed with tomato salad, onions, and spices.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="9">
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
            <p>&copy; 2023 FoodExpress. Taste of Ethiopia in every bite.</p>
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