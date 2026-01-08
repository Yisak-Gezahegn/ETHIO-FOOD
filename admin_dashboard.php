<?php
session_start();
include 'include/db_connect.php'; 

// 1. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'developer') {
    header("Location: otherpart/login.php");
    exit();
}

// --- ACTION HANDLERS (Corrected Logic) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update User
    if (isset($_POST['action']) && $_POST['action'] == 'update_user_info') {
        $user_id = intval($_POST['user_id']);
        $new_name = mysqli_real_escape_string($conn, $_POST['fullname']);
        $new_email = mysqli_real_escape_string($conn, $_POST['email']);
        $new_role = $_POST['user_role'];
        mysqli_query($conn, "UPDATE users SET fullname='$new_name', email='$new_email', user_role='$new_role' WHERE id=$user_id");
        header("Location: ?page=users&msg=updated");
        exit();
    }
    // Add New Restaurant
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_restaurant') {
    $name = mysqli_real_escape_string($conn, $_POST['res_name']);
    $loc = mysqli_real_escape_string($conn, $_POST['res_location']);
    $desc = mysqli_real_escape_string($conn, $_POST['res_description']);
    $img = mysqli_real_escape_string($conn, $_POST['res_image']);
    $tags = mysqli_real_escape_string($conn, $_POST['res_tags']);
    $delivery = $_POST['res_delivery'];
    $time = mysqli_real_escape_string($conn, $_POST['res_time']);
    $rating = $_POST['res_rating'];

    // Update your SQL to include all new fields
    // Notice 'cuisine' instead of 'tags'
$sql = "INSERT INTO restaurants (name, location, description, image_url, cuisine, delivery_fee, delivery_time, rating, price_range) 
        VALUES ('$name', '$loc', '$desc', '$img', '$tags', '$delivery', '$time', '$rating', '$$')";
    
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('Restaurant Created Successfully!'); window.location.href='?page=restaurants';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

    // Update Restaurant
    if (isset($_POST['action']) && $_POST['action'] == 'update_restaurant_info') {
        $res_id = intval($_POST['res_id']);
        $name = mysqli_real_escape_string($conn, $_POST['res_name']);
        $location = mysqli_real_escape_string($conn, $_POST['res_location']);
        $rating = $_POST['res_rating'];
        mysqli_query($conn, "UPDATE restaurants SET name='$name', location='$location', rating='$rating' WHERE id=$res_id");
        header("Location: ?page=restaurants&msg=res_updated");
        exit();
    }
    // Update Order Status
    if (isset($_GET['action']) && $_GET['action'] == 'update_order') {
        $id = intval($_GET['id']);
        $new_status = mysqli_real_escape_string($conn, $_POST['status']);
        mysqli_query($conn, "UPDATE orders SET status = '$new_status' WHERE id = $id");
        header("Location: ?page=orders&msg=updated");
        exit();
    }
}

// GET Handlers
if (isset($_GET['action'])) {
    $id = intval($_GET['id'] ?? 0);
    if ($_GET['action'] == 'delete_user') {
        mysqli_query($conn, "DELETE FROM users WHERE id = $id");
        header("Location: ?page=users&msg=deleted");
    }
    if ($_GET['action'] == 'delete_restaurant') {
        mysqli_query($conn, "DELETE FROM restaurants WHERE id = $id");
        header("Location: ?page=restaurants&msg=res_deleted");
    }
}

// 2. Data Calculations
$total_money = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_price) as total FROM orders WHERE status = 'delivered'"))['total'] ?? 0;
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) as total FROM orders"))['total'] ?? 0;

$page = $_GET['page'] ?? 'overview'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>🍟 Ethio Food Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styling/admin.css">
    <script>
        // User Modal
        function openEditModal(id, name, email, role) {
            document.getElementById('edit_user_id').value = id;
            document.getElementById('edit_fullname').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;
            document.getElementById('editModal').style.display = 'block';
        }
        function closeModal() { document.getElementById('editModal').style.display = 'none'; }

        // Restaurant Modal
        function openResModal(id, name, location, rating) {
            document.getElementById('edit_res_id').value = id;
            document.getElementById('edit_res_name').value = name;
            document.getElementById('edit_res_location').value = location;
            document.getElementById('edit_res_rating').value = rating;
            document.getElementById('resModal').style.display = 'block';
        }
        function closeResModal() { document.getElementById('resModal').style.display = 'none'; }

        // Filter Logic
        function filterUsers() {
            let input = document.getElementById('userSearch').value.toLowerCase();
            let rows = document.querySelectorAll('#userTableBody tr');
            rows.forEach(row => {
                let name = row.cells[1].textContent.toLowerCase();
                row.style.display = name.includes(input) ? '' : 'none';
            });
        }
        function filterByRole(role) {
            let rows = document.querySelectorAll('#userTableBody tr');
            rows.forEach(row => {
                let userRole = row.cells[3].textContent.toLowerCase();
                row.style.display = (role === 'all' || userRole.includes(role.toLowerCase())) ? '' : 'none';
            });
        }
        // Search through Restaurant Names and Locations
function filterRestaurants() {
    let input = document.getElementById('resSearch').value.toLowerCase();
    let rows = document.querySelectorAll('#resTableBody tr'); // Targeted the specific tbody ID
    rows.forEach(row => {
        let name = row.cells[1].textContent.toLowerCase();
        let location = row.cells[2].textContent.toLowerCase();
        row.style.display = (name.includes(input) || location.includes(input)) ? '' : 'none';
    });
}

// Filter by Rating value
function filterByRating(ratingVal) {
    let rows = document.querySelectorAll('#resTableBody tr');
    rows.forEach(row => {
        let rowRating = row.cells[3].textContent.replace(/[^\d.]/g, ''); // Extract only the number
        if (ratingVal === 'all') {
            row.style.display = '';
        } else {
            row.style.display = (parseFloat(rowRating) >= parseFloat(ratingVal)) ? '' : 'none';
        }
    });
}

// Function to open the ADD modal
function openAddResModal() {
    document.getElementById('addResModal').style.display = 'block';
}

function closeAddResModal() {
    document.getElementById('addResModal').style.display = 'none';
}
    </script>
</head>
<body>

    <aside class="admin-sidebar">
        <h3>🍟 ETHIO FOOD</h3>
        <ul class="nav-links">
            <li><a href="?page=overview" class="<?= $page == 'overview' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Overview</a></li>
            <li><a href="?page=users" class="<?= $page == 'users' ? 'active' : '' ?>"><i class="fas fa-users"></i> Manage Users</a></li>
            <li><a href="?page=restaurants" class="<?= $page == 'restaurants' ? 'active' : '' ?>"><i class="fas fa-utensils"></i> Restaurants</a></li>
            <li><a href="?page=orders" class="<?= $page == 'orders' ? 'active' : '' ?>"><i class="fas fa-receipt"></i> Live Orders</a></li>
            <li><a href="otherpart/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </aside>

    <main class="admin-main">
        <?php switch($page) {
            case 'users': ?>
                <h1>User Management</h1>
                <div class="data-card">
                    <div style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
                        <input type="text" id="userSearch" onkeyup="filterUsers()" placeholder="Search user by name..." class="status-select" style="flex:1; min-width:200px;">
                        <button onclick="filterByRole('all')" class="btn-save">All Users</button>
                        <button onclick="filterByRole('developer')" class="btn-save" style="background:blue; color:white;">Admins</button>
                        <button onclick="filterByRole('owner')" class="btn-save" style="background:blue; color:white;">Owners</button>
                        <button onclick="filterByRole('customer')" class="btn-save" style="background:blue; color:white;">Customers</button>
                    </div>
                    <table class="admin-table">
                        <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
                        <tbody id="userTableBody">
                            <?php $users = mysqli_query($conn, "SELECT * FROM users");
                            while($u = mysqli_fetch_assoc($users)): ?>
                            <tr>
                                <td>#<?= $u['id'] ?></td>
                                <td><?= htmlspecialchars($u['fullname']) ?></td>
                                <td><?= htmlspecialchars($u['email']) ?></td>
                                <td><span class='status-badge'><?= $u['user_role'] ?></span></td>
                                <td>
                                    <button type="button" class="btn-edit" onclick="openEditModal(<?= $u['id'] ?>, '<?= addslashes($u['fullname']) ?>', '<?= $u['email'] ?>', '<?= $u['user_role'] ?>')"><i class="fas fa-edit"></i> Edit</button>
                                    <a href="?page=users&action=delete_user&id=<?= $u['id'] ?>" onclick="return confirm('Delete?')" class="btn-delete" style="color:red; margin-left:10px;"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php break;

            case 'orders': ?>
    <h1>Live Orders Control Center</h1>
    <div class="data-card">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ordered From</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                // We use GROUP_CONCAT to merge all food names for one order into a single string
               $orders_query = "SELECT o.*, r.name AS restaurant_name
                 FROM orders o 
                 JOIN restaurants r ON o.restaurant_id = r.id 
                 ORDER BY o.created_at DESC";
                
                $orders = mysqli_query($conn, $orders_query);
                while($o = mysqli_fetch_assoc($orders)): ?>

                <tr>
                    <td>#<?= $o['id'] ?></td>
                    <td style="font-weight: bold; color: #e67e22;">
    <?= htmlspecialchars($o['restaurant_name']) ?>
</td>
                    <td><?= number_format($o['total_price'], 2) ?> ETB</td>
                    <td><span class='status-badge status-<?= $o['status'] ?>'><?= $o['status'] ?></span></td>
                    <td><?= date('M d, H:i', strtotime($o['created_at'])) ?></td>
                    <td>
                        <form action="?page=orders&action=update_order&id=<?= $o['id'] ?>" method="POST" style="display:flex; gap:5px;">
                            <select name="status" class="status-select">
                                <option value="pending" <?= $o['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="delivered" <?= $o['status'] == 'delivered' ? 'selected' : '' ?>>Delivered</option>
                            </select>
                            <button type="submit" class="btn-save">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

            <?php break;

            case 'restaurants': ?>
    <h1>Restaurant Management</h1>
    <div class="data-card">
        <div style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
            <input type="text" id="resSearch" onkeyup="filterRestaurants()" placeholder="Search restaurant..." class="status-select" style="flex:1; min-width:200px;">
            
            <button onclick="filterByRating('all')" class="btn-save">All Restaurants</button>
            <button onclick="filterByRating('4.0')" class="btn-save" style="background:#f1c40f; color:black;">Top Rated (4+)</button>
            
            <button onclick="openAddResModal()" class="btn-save" style="background:#27ae60;">
                <i class="fas fa-plus"></i> Add New Restaurant
            </button>
        </div>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Restaurant Name</th>
                    <th>Location</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="resTableBody"> <?php 
                $res_query = mysqli_query($conn, "SELECT * FROM restaurants ORDER BY id DESC");
                while($r = mysqli_fetch_assoc($res_query)): ?>
                <tr>
                    <td>#<?= $r['id'] ?></td>
                    <td><strong><?= htmlspecialchars($r['name']) ?></strong></td>
                    <td><?= htmlspecialchars($r['location']) ?></td>
                    <td><?= $r['rating'] ?></td>
                    <td>
                        <button type="button" class="btn-edit" onclick="openResModal(<?= $r['id'] ?>, '<?= addslashes($r['name']) ?>', '<?= addslashes($r['location']) ?>', '<?= $r['rating'] ?>')"><i class="fas fa-edit"></i> Edit</button>
                        <a href="?page=restaurants&action=delete_restaurant&id=<?= $r['id'] ?>" onclick="return confirm('Delete?')" class="btn-delete" style="color:red; margin-left:10px;"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php break;

            default: // OVERVIEW ?>
                <h1>Dashboard Overview</h1>
                <div class="stats-container">
                    <div class="stat-card">
                        <div><span>Total Revenue</span><span class="stat-val"><?= number_format($total_money, 2) ?> ETB</span></div>
                        <i class="fas fa-money-check-alt fa-2x" style="color:#2ecc71"></i>
                    </div>
                    <div class="stat-card">
                        <div><span>Total Orders</span><span class="stat-val"><?= $total_orders ?></span></div>
                        <i class="fas fa-truck fa-2x" style="color:#3498db"></i>
                    </div>
                </div>
                <div class="data-card">
                    <h3>Recent Activity (Latest 5 Orders)</h3>
                    <table class="admin-table">
                        <thead><tr><th>Order ID</th><th>Amount</th><th>Status</th><th>Date</th></tr></thead>
                        <tbody>
                            <?php 
                            $recent = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
                            while($row = mysqli_fetch_assoc($recent)): ?>
                            <tr>
                                <td>#<?= $row['id'] ?></td>
                                <td><?= number_format($row['total_price'], 2) ?> ETB</td>
                                <td><span class="status-badge status-<?= $row['status'] ?>"><?= $row['status'] ?></span></td>
                                <td><?= $row['created_at'] ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php break;
        } ?>
    </main>

    <div id="editModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background: white; margin: 10% auto; padding: 30px; border-radius: 12px; width: 400px;">
            <h3>Edit User Information</h3><hr><br>
            <form action="?page=users" method="POST">
                <input type="hidden" name="action" value="update_user_info">
                <input type="hidden" name="user_id" id="edit_user_id">
                <label>Full Name</label><input type="text" name="fullname" id="edit_fullname" class="status-select" style="width:100%; margin-bottom:15px;" required>
                <label>Email</label><input type="email" name="email" id="edit_email" class="status-select" style="width:100%; margin-bottom:15px;" required>
                <label>User Role</label>
                <select name="user_role" id="edit_role" class="status-select" style="width:100%; margin-bottom:15px;">
                    <option value="customer">Customer</option>
                    <option value="developer">Developer</option>
                    <option value="owner">Owner</option>
                </select>
                <div style="display:flex; gap:10px;">
                    <button type="submit" class="btn-save" style="flex:1;">Save</button>
                    <button type="button" onclick="closeModal()" class="btn-delete" style="flex:1; background:none; border:1px solid red;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="resModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
        <div class="modal-content" style="background: white; margin: 10% auto; padding: 30px; border-radius: 12px; width: 400px;">
            <h3>Edit Restaurant</h3><hr><br>
            <form action="?page=restaurants" method="POST">
                <input type="hidden" name="action" value="update_restaurant_info">
                <input type="hidden" name="res_id" id="edit_res_id">
                <label>Restaurant Name</label><input type="text" name="res_name" id="edit_res_name" class="status-select" style="width:100%; margin-bottom:15px;" required>
                <label>Location</label><input type="text" name="res_location" id="edit_res_location" class="status-select" style="width:100%; margin-bottom:15px;" required>
                <label>Rating</label><input type="number" step="0.1" name="res_rating" id="edit_res_rating" class="status-select" style="width:100%; margin-bottom:15px;">
                <div style="display:flex; gap:10px;">
                    <button type="submit" class="btn-save" style="flex:1;">Update</button>
                    <button type="button" onclick="closeResModal()" class="btn-delete" style="flex:1; background:none; border:1px solid red;">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <div id="addResModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); overflow-y: auto;">
    <div class="modal-content" style="background: white; margin: 5% auto; padding: 30px; border-radius: 12px; width: 500px; box-shadow: 0 5px 15px rgba(0,0,0,0.3);">
        <h3 style="color: #2c3e50;"><i class="fas fa-utensils"></i> Add New Restaurant</h3>
        <p style="font-size: 0.8em; color: gray;">Fill in the details as seen in the restaurant cards.</p>
        <hr><br>
        
        <form action="?page=restaurants" method="POST">
            <input type="hidden" name="action" value="add_restaurant">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label>Restaurant Name</label>
                    <input type="text" name="res_name" class="status-select" style="width:100%; margin-bottom:15px;" placeholder="e.g. Rehoboth Grill" required>
                </div>
                <div>
                    <label>Location (Address)</label>
                    <input type="text" name="res_location" class="status-select" style="width:100%; margin-bottom:15px;" placeholder="e.g. Kazanchis, Addis" required>
                </div>
            </div>

            <label>Description</label>
            <textarea name="res_description" class="status-select" style="width:100%; height:80px; margin-bottom:15px; padding:10px;" placeholder="Specializing in grilled meats..."></textarea>

            <label>Image URL (from /assets/img/)</label>
            <input type="text" name="res_image" class="status-select" style="width:100%; margin-bottom:15px;" placeholder="rehoboth.jpg">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label>Cuisine Tags (Comma separated)</label>
                    <input type="text" name="res_tags" class="status-select" style="width:100%; margin-bottom:15px;" placeholder="Ethiopian, Grill">
                </div>
                <div>
                    <label>Delivery Fee (ETB)</label>
                    <input type="number" step="0.01" name="res_delivery" class="status-select" style="width:100%; margin-bottom:15px;" placeholder="6.50">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label>Delivery Time (min)</label>
                    <input type="text" name="res_time" class="status-select" style="width:100%; margin-bottom:15px;" placeholder="45-55">
                </div>
                <div>
                    <label>Initial Rating</label>
                    <input type="number" step="0.1" name="res_rating" value="4.5" class="status-select" style="width:100%; margin-bottom:15px;">
                </div>
            </div>
            
            <div style="display:flex; gap:10px; margin-top:10px;">
                <button type="submit" class="btn-save" style="flex:2; padding: 12px; font-weight: bold;">Create Restaurant</button>
                <button type="button" onclick="closeAddResModal()" class="btn-delete" style="flex:1; background:none; border:1px solid #ccc; color:#333;">Cancel</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>