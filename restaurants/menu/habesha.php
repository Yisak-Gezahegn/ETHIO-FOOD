<?php
session_start();
require_once '../db.php';

$restaurant_id = 3; // Habesha Restaurant

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
    <title>Habesha Restaurant| FoodExpress</title>
    <link rel="stylesheet" href="../style/habesha.css">
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
                    <img src="../../image/res_food_back.avif" 
                         alt="Habesha Restaurant">
                </div>
                <div class="restaurant-info">
                    <h1>Habesha Restaurant</h1>
                    <div class="rating">
                        <i class="fas fa-star"></i> 4.7
                    </div>
                    <p class="cuisine">Traditional Ethiopian</p>
                    <p class="description">Family-owned restaurant serving traditional Habesha recipes passed down through generations. Authentic flavors with a modern twist.</p>
                    <div class="delivery-info">
                        <span><i class="fas fa-clock"></i> 20-30 min</span>
                        <span><i class="fas fa-shipping-fast"></i> ETB 2.49 delivery</span>
                        <span><i class="fas fa-award"></i> Family Recipes</span>
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
                        <img src="../../image/keywet.jpg" alt="Key Wat">
                        <span class="popular-badge">Popular</span>
                        <span class="spicy-badge">Spicy</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Key Wat</h3>
                            <span class="item-price">ETB 19.99</span>
                        </div>
                        <p class="item-desc">Beef stew in spicy berbere sauce with onions and garlic, served with injera.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="10">
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
                        <img src="../../image/LEGA-TIBS.jpg" alt="Lega Tibs">
                        <span class="spicy-badge">Spicy</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Lega Tibs</h3>
                            <span class="item-price">ETB 22.99</span>
                        </div>
                        <p class="item-desc">Tender lamb tibs cooked with rosemary and special Habesha spices.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="11">
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
                        <img src="../../image/doroalicha.jfif" alt="Doro Alicha">
                        <span class="popular-badge">Popular</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Doro Alicha</h3>
                            <span class="item-price">ETB 17.99</span>
                        </div>
                        <p class="item-desc">Mild chicken stew with turmeric, ginger, garlic, and traditional spices.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="12">
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
                        <img src="../../image/gomen.jpg" alt="Gomen">
                        <span class="veg-badge">Vegetarian</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Gomen</h3>
                            <span class="item-price">ETB 12.99</span>
                        </div>
                        <p class="item-desc">Collard greens cooked with onions, garlic, ginger, and traditional spices.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="13">
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
                        <img src="../../image/atikilt.jpg" alt="Atakilt Wat">
                        <span class="veg-badge">Vegetarian</span>
                        <span class="popular-badge">Popular</span>
                    </div>
                    <div class="item-content">
                        <div class="item-header">
                            <h3>Atakilt Wat</h3>
                            <span class="item-price">ETB 11.99</span>
                        </div>
                        <p class="item-desc">Cabbage, potatoes, and carrots cooked in turmeric and ginger sauce.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="14">
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
                            <h3>Habesha Veg Platter</h3>
                            <span class="item-price">ETB 16.99</span>
                        </div>
                        <p class="item-desc">Sampler of 5 traditional vegetarian dishes including gomen, atakilt, and more.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="15">
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
                            <h3>Habesha Injera</h3>
                            <span class="item-price">ETB 4.99</span>
                        </div>
                        <p class="item-desc">Traditional sourdough flatbread made from authentic teff flour.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="16">
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
                            <h3>Fresh Ayib</h3>
                            <span class="item-price">ETB 3.99</span>
                        </div>
                        <p class="item-desc">Homemade Ethiopian cottage cheese, perfect with spicy dishes.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="17">
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
                            <h3>Timatim Salad</h3>
                            <span class="item-price">ETB 5.99</span>
                        </div>
                        <p class="item-desc">Fresh tomato salad with onions, peppers, and traditional dressing.</p>
                        <form method="POST" class="add-to-cart-form">
                            <input type="hidden" name="item_id" value="18">
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
            <p>&copy; 2023 FoodExpress. Traditional Habesha Flavors.</p>
        </div>
    </footer>

    <script>
        // JavaScript same as Addis Ababa
        document.querySelector('.cart-btn').addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelector('.cart-sidebar').classList.add('show');
        });

        document.querySelector('.close-cart').addEventListener('click', function() {
            document.querySelector('.cart-sidebar').classList.remove('show');
        });

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

        document.querySelectorAll('.tab-link').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetSection = document.querySelector(targetId);
                
                targetSection.scrollIntoView({ behavior: 'smooth' });
                
                document.querySelectorAll('.tab-link').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });

        document.querySelector('.checkout-btn').addEventListener('click', function() {
            if (<?php echo $cart_count; ?> === 0) {
                alert('Your cart is empty!');
                return;
            }
            alert('Proceeding to checkout! Total: $<?php echo number_format($cart_total, 2); ?>');
        });
    </script>
</body>
</html>