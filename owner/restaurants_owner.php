<?php
session_start();
include '../include/db_connect.php';

// 1. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: otherpart/login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];
$restaurant_id = $_SESSION['restaurant_id'] ?? 1;
// Add this line to specify which menu file to update
$menu_file_to_update = '../restaurants/menu/addisababa.php';
// Handle form submissions
$success_msg = '';
$error_msg = '';

// Handle Add Menu Item
// Handle Add Menu Item
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_menu_item'])) {
        // Get form data
        $item_name = $_POST['item_name'];
        $description = $_POST['description'] ?? '';
        $price = floatval($_POST['price']);
        $category = $_POST['category'];
        $is_available = isset($_POST['is_available']) ? 1 : 0;
        
        // Add to addisababa.php file
        $result = addMenuItemToFile($item_name, $description, $price, $category, $is_available);
        
        if ($result) {
            $success_msg = "Menu item added successfully to addisababa.php!";
        } else {
            $error_msg = "Failed to add menu item. Check file permissions.";
        }
    }
    // ... rest of your POST handlers
}

// Get current page
$page = $_GET['page'] ?? 'overview';
// Function to add menu item to addisababa.php
function addMenuItemToFile($name, $description, $price, $category, $available = 1) {
    // Path to your menu file
    $file_path = '../restaurants/menu/addisababa.php';
    
    // Read the current file
    $content = file_get_contents($file_path);
    
    // Look for the menu items array in the file
    // This pattern finds: $menu_items = array( ... );
    $pattern = '/(\$menu_items\s*=\s*array\s*\(\s*)(.*?)(\s*\);)/s';
    
    if (preg_match($pattern, $content, $matches)) {
        // Create the new item
        $new_item = "\n    array(\n";
        $new_item .= "        'name' => '$name',\n";
        $new_item .= "        'description' => '$description',\n";
        $new_item .= "        'price' => $price,\n";
        $new_item .= "        'category' => '$category',\n";
        $new_item .= "        'available' => " . ($available ? 'true' : 'false') . ",\n";
        $new_item .= "        'image' => 'images/default.jpg'\n";
        $new_item .= "    ),";
        
        // Insert the new item
        $new_content = $matches[1] . $new_item . "\n    " . $matches[2] . $matches[3];
        $content = str_replace($matches[0], $new_content, $content);
        
        // Write back to file
        return file_put_contents($file_path, $content) !== false;
    }
    
    return false;
}
// Function to update existing menu item
function updateMenuItemInFile($item_id, $price, $available) {
    $file_path = '../restaurants/menu/addisababa.php';
    $content = file_get_contents($file_path);
    
    // This is a simple example - you'll need to adjust based on your file structure
    // Look for the specific item by its name or ID
    
    // Write your update logic here based on your file's structure
    
    return file_put_contents($file_path, $content) !== false;
}
?>
<?php
// If they aren't logged in as an owner, kick them out
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../otherpart/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>&#127839 Restaurant Owner Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js">
    <link rel="stylesheet" href="styling/owner.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="scripting/owner.js"></script>
</head>
<body>
    <!-- Top Navigation Bar (No Sidebar) -->
    <nav class="top-nav">
        <div class="nav-left">
            <h1><i class="fas fa-utensils"></i> Addis Ababa Ethiopian Restaurant Dashboard</h1>
            <div class="nav-date" id="currentDateTime">
                <?php echo date('l, F j, Y - g:i A'); ?>
            </div>
        </div>
        <div class="nav-right">
            <div class="nav-buttons">
                <button class="nav-btn" onclick="location.href='?page=overview'">
                    <i class="fas fa-chart-line"></i> Overview
                </button>
                <button class="nav-btn" onclick="location.href='?page=orders'">
                    <i class="fas fa-shopping-cart"></i> Orders
                </button>
                <button class="nav-btn" onclick="location.href='?page=menu'">
                    <i class="fas fa-utensils"></i> Menu
                </button>
                <button class="nav-btn" onclick="location.href='?page=analytics'">
                    <i class="fas fa-chart-bar"></i> Analytics
                </button>
                <a href="../otherpart/logout.php" class="nav-btn logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="main-content">
        <?php if ($success_msg): ?>
            <div class="alert success-alert">
                <i class="fas fa-check-circle"></i> <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_msg): ?>
            <div class="alert error-alert">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>
        
        <!-- Quick Stats Bar -->
        <div class="quick-stats">
            <div class="stat-card">
                <div class="stat-icon revenue">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <h3>Today's Revenue</h3>
                    <p class="stat-value">ETB 12,450</p>
                    <p class="stat-change"><i class="fas fa-arrow-up"></i> 15% from yesterday</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon orders">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="stat-info">
                    <h3>Today's Orders</h3>
                    <p class="stat-value">42</p>
                    <p class="stat-change"><i class="fas fa-arrow-up"></i> 8% from yesterday</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-info">
                    <h3>Pending Orders</h3>
                    <p class="stat-value">5</p>
                    <p class="stat-change">Requires attention</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon customers">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Active Customers</h3>
                    <p class="stat-value">28</p>
                    <p class="stat-change"><i class="fas fa-arrow-up"></i> 12% growth</p>
                </div>
            </div>
        </div>

        <?php
        // Page content switcher
        switch($page) {
            case 'orders': ?>
                <!-- Orders Page -->
                <div class="page-header">
                    <h2><i class="fas fa-shopping-cart"></i> Order Management</h2>
                    <div class="page-actions">
                        <button class="btn-primary">
                            <i class="fas fa-print"></i> Print Reports
                        </button>
                        <select class="filter-select">
                            <option>Today</option>
                            <option>This Week</option>
                            <option>This Month</option>
                        </select>
                    </div>
                </div>
                
                <div class="content-grid">
                    <!-- Order Status Overview -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-pie"></i> Order Status</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="orderStatusChart" height="250"></canvas>
                        </div>
                    </div>
                    
                    <!-- Recent Orders Table -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-list-alt"></i> Recent Orders</h3>
                            <span class="badge">5 pending</span>
                        </div>
                        <div class="card-body">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#1001</td>
                                        <td>John Doe</td>
                                        <td>1x Doro Wat, 2x Injera, 1x Ethiopian Coffee</td>
<td>ETB 45.97</td>
                                        <td><span class="status-badge preparing">Preparing</span></td>
                                        <td>
                                            <select class="status-select" onchange="updateOrderStatus(1001, this.value)">
                                                <option value="pending">Pending</option>
                                                <option value="preparing" selected>Preparing</option>
                                                <option value="ready">Ready</option>
                                                <option value="completed">Completed</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <!-- More rows... -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php break;
            
            case 'menu': ?>
                <!-- Menu Management Page -->
                <div class="page-header">
                    <h2><i class="fas fa-utensils"></i> Menu Management</h2>
                    <div class="page-actions">
                        <button class="btn-primary" onclick="openAddMenuItemModal()">
                            <i class="fas fa-plus"></i> Add New Item
                        </button>
                    </div>
                </div>
                
                <div class="content-grid">
                    <!-- Menu Items Table -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-list"></i> Menu Items</h3>
                            <input type="text" placeholder="Search items..." class="search-input">
                        </div>
                        <div class="card-body">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Item Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   <tr>
    <td>Doro Wat</td>
    <td>Main Dishes</td>
    <td>ETB 18.99</td>
    <td><span class="status-badge active">Available</span></td>
    <td>
        <button class="btn-edit" onclick="openEditMenuItemModal(1, 'Doro Wat', 18.99, 1)">
            <i class="fas fa-edit"></i> Edit
        </button>
        <button class="btn-delete">
            <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>
<tr>
    <td>Kitfo</td>
    <td>Main Dishes</td>
    <td>ETB 21.99</td>
    <td><span class="status-badge active">Available</span></td>
    <td>
        <button class="btn-edit" onclick="openEditMenuItemModal(2, 'Kitfo', 21.99, 1)">
            <i class="fas fa-edit"></i> Edit
        </button>
        <button class="btn-delete">
            <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>
<tr>
    <td>Tibs</td>
    <td>Main Dishes</td>
    <td>ETB 16.99</td>
    <td><span class="status-badge active">Available</span></td>
    <td>
        <button class="btn-edit" onclick="openEditMenuItemModal(3, 'Tibs', 16.99, 1)">
            <i class="fas fa-edit"></i> Edit
        </button>
        <button class="btn-delete">
            <i class="fas fa-trash"></i>
        </button>
    </td>
</tr>
                                    <!-- More rows... -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Top Selling Items -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-star"></i> Top Selling Items</h3>
                        </div>
                        <div class="card-body">
                            <div class="top-items">
                                <div class="top-item">
    <span class="item-rank">1</span>
    <div class="item-info">
        <strong>Doro Wat</strong>
        <small>45 orders this week</small>
    </div>
    <span class="item-revenue">ETB 854.55</span>
</div>
                                <!-- More top items... -->
                            </div>
                        </div>
                    </div>
                </div>
            <?php break;
            
            case 'analytics': ?>
                <!-- Analytics Page -->
                <div class="page-header">
                    <h2><i class="fas fa-chart-bar"></i> Analytics & Reports</h2>
                    <div class="page-actions">
                        <select class="filter-select">
                            <option>Last 7 Days</option>
                            <option>Last 30 Days</option>
                            <option>Last 3 Months</option>
                        </select>
                    </div>
                </div>
                
                <div class="content-grid">
                    <!-- Revenue Chart -->
                    <div class="card full-width">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-line"></i> Revenue Trend</h3>
                            <div class="chart-controls">
                                <button class="chart-btn active">Daily</button>
                                <button class="chart-btn">Weekly</button>
                                <button class="chart-btn">Monthly</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart" height="300"></canvas>
                        </div>
                    </div>
                    
                    <!-- Customer Reviews -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-comments"></i> Customer Reviews</h3>
                            <span class="avg-rating">4.2 <i class="fas fa-star"></i></span>
                        </div>
                        <div class="card-body">
                            <div class="review">
                                <div class="review-header">
                                    <strong>Sarah Johnson</strong>
                                    <div class="stars">
                                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <p class="review-text">Excellent food and service! The pizza was amazing.</p>
                                <small class="review-date">2 days ago</small>
                            </div>
                            <!-- More reviews... -->
                        </div>
                    </div>
                    
                    <!-- Delivery Zones -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-map-marker-alt"></i> Top Delivery Zones</h3>
                        </div>
                        <div class="card-body">
                            <div class="zone-list">
                                <div class="zone-item">
                                    <div class="zone-info">
                                        <strong>Downtown Area</strong>
                                        <small>45 orders</small>
                                    </div>
                                    <span class="zone-revenue">ETB 15,250</span>
                                </div>
                                <!-- More zones... -->
                            </div>
                        </div>
                    </div>
                </div>
            <?php break;
            
            default: // Overview Page ?>
                <!-- Overview Page (Default) -->
                <div class="page-header">
                    <h2><i class="fas fa-tachometer-alt"></i> Dashboard Overview</h2>
                    <div class="page-actions">
                        <button class="btn-primary" onclick="printReport()">
                            <i class="fas fa-file-export"></i> Export Report
                        </button>
                    </div>
                </div>
                
                <!-- Inventory Alerts -->
                <div class="alert warning-alert">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Low stock alert: Tomatoes (12 units), Cheese (8 units)</span>
                    <a href="#" class="alert-link">Reorder Now</a>
                </div>
                
                <div class="content-grid">
                    <!-- Main Charts -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-chart-line"></i> Performance Overview</h3>
                            <select class="time-select" onchange="updateChart(this.value)">
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="year">This Year</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <canvas id="performanceChart" height="250"></canvas>
                        </div>
                    </div>
                    
                    <!-- Order Timeline -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-history"></i> Recent Activity</h3>
                            <span class="badge">Live</span>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker new-order"></div>
                                    <div class="timeline-content">
                                        <strong>New Order #1005</strong>
                                        <p>Table 4 - 2x Burger, 1x Fries</p>
                                        <small>2 minutes ago</small>
                                    </div>
                                </div>
                                <!-- More timeline items... -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions-grid">
                                <button class="quick-action" onclick="openAddMenuItemModal()">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Add Menu Item</span>
                                </button>
                                <button class="quick-action" onclick="openAddPromoModal()">
                                    <i class="fas fa-tag"></i>
                                    <span>Create Promotion</span>
                                </button>
                                <button class="quick-action" onclick="location.href='?page=analytics'">
                                    <i class="fas fa-chart-pie"></i>
                                    <span>View Reports</span>
                                </button>
                                <button class="quick-action" onclick="openAddStaffModal()">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Add Staff</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Promotions -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-percentage"></i> Active Promotions</h3>
                            <button class="btn-small" onclick="openAddPromoModal()">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="promo-list">
                                <div class="promo-item">
                                    <div class="promo-code">WEEKEND20</div>
                                    <div class="promo-details">
                                        <strong>20% Off Weekend Orders</strong>
                                        <small>Valid until: Dec 31, 2024</small>
                                    </div>
                                    <span class="promo-uses">45 uses</span>
                                </div>
                                <!-- More promos... -->
                            </div>
                        </div>
                    </div>
                    
                    <!-- Staff Management -->
                    <div class="card">
                        <div class="card-header">
                            <h3><i class="fas fa-users-cog"></i> Staff Management</h3>
                            <button class="btn-small" onclick="openAddStaffModal()">
                                <i class="fas fa-user-plus"></i> Add Staff
                            </button>
                        </div>
                        <div class="card-body">
                            <table class="staff-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Michael Chen</td>
                                        <td>Head Chef</td>
                                        <td><span class="status-badge active">On Duty</span></td>
                                        <td>
                                            <button class="btn-edit-small">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <!-- More staff... -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php break;
        } ?>
    </main>

    <!-- Modals -->
    <!-- Add Menu Item Modal -->
    <div id="addMenuItemModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-plus"></i> Add New Menu Item</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" name="item_name" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="3"></textarea>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Price (ETB)</label>
                            <input type="number" name="price" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category" required>
                                <option value="">Select Category</option>
                                <option value="appetizer">Appetizer</option>
                                <option value="main">Main Course</option>
                                <option value="dessert">Dessert</option>
                                <option value="drink">Drink</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_available" checked>
                            <span>Available for ordering</span>
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="add_menu_item" class="btn-primary">Add Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Menu Item Modal -->
    <div id="editMenuItemModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit"></i> Edit Menu Item</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="item_id" id="edit_item_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Item Name</label>
                        <input type="text" id="edit_item_name" readonly>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Price (ETB)</label>
                            <input type="number" name="price" id="edit_item_price" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label>Availability</label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_available" id="edit_item_available">
                                <span>Available</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="update_menu_item" class="btn-primary">Update Item</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Promo Modal -->
    <div id="addPromoModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-tag"></i> Add New Promotion</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Promo Code</label>
                        <input type="text" name="promo_code" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Discount Type</label>
                            <select name="discount_type" required>
                                <option value="percentage">Percentage</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Discount Value</label>
                            <input type="number" name="discount" step="0.01" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Valid Until</label>
                        <input type="date" name="valid_until" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="add_promo" class="btn-primary">Add Promotion</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Staff Modal -->
    <div id="addStaffModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-plus"></i> Add New Staff Member</h3>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="staff_name" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="staff_email" required>
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="tel" name="staff_phone" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <select name="staff_role" required>
                            <option value="chef">Chef</option>
                            <option value="waiter">Waiter</option>
                            <option value="manager">Manager</option>
                            <option value="delivery">Delivery</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" name="add_staff" class="btn-primary">Add Staff</button>
                </div>
            </form>
        </div>
    </div>

    <script src="scripting/owner.js"></scrip>
</body>
</html>