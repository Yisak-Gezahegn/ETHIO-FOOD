<?php
session_start();
include 'include/db_connect.php'; 

// 1. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'developer') {
    header("Location: otherpart/login.php");
    exit();
}
// Handle Edit User Information
if (isset($_POST['action']) && $_POST['action'] == 'update_user_info') {
    $user_id = $_POST['user_id'];
    $new_name = mysqli_real_escape_string($conn, $_POST['fullname']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $new_role = $_POST['user_role'];

    $update_query = "UPDATE users SET fullname='$new_name', email='$new_email', user_role='$new_role' WHERE id=$user_id";
    
    if (mysqli_query($conn, $update_query)) {
        header("Location: ?page=users&msg=updated");
        exit();
    }
}
// --- ACTION HANDLER ---
if (isset($_GET['action'])) {
    $id = $_GET['id'];
    
    // Handle Delete User
    if ($_GET['action'] == 'delete_user') {
        mysqli_query($conn, "DELETE FROM users WHERE id = $id");
        header("Location: ?page=users&msg=deleted");
    }

    // Handle Update Order Status
    if ($_GET['action'] == 'update_order') {
        $new_status = $_POST['status'];
        mysqli_query($conn, "UPDATE orders SET status = '$new_status' WHERE id = $id");
        header("Location: ?page=orders&msg=updated");
    }
}
// Handle Delete Restaurant
if (isset($_GET['action']) && $_GET['action'] == 'delete_restaurant') {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM restaurants WHERE id = $id");
    header("Location: ?page=restaurants&msg=res_deleted");
    exit();
}

// Handle Update Restaurant Information
if (isset($_POST['action']) && $_POST['action'] == 'update_restaurant_info') {
    $res_id = $_POST['res_id'];
    $name = mysqli_real_escape_string($conn, $_POST['res_name']);
    $location = mysqli_real_escape_string($conn, $_POST['res_location']);
    $rating = $_POST['res_rating'];

    $update_query = "UPDATE restaurants SET name='$name', location='$location', rating='$rating' WHERE id=$res_id";
    
    if (mysqli_query($conn, $update_query)) {
        header("Location: ?page=restaurants&msg=res_updated");
        exit();
    }
}

// 2. Data Calculations (Always run these for the sidebar/header)
$rev_query = "SELECT SUM(total_price) as total_money FROM orders WHERE status = 'delivered'";
$rev_result = mysqli_query($conn, $rev_query);
$rev_data = mysqli_fetch_assoc($rev_result);
$total_money = $rev_data['total_money'] ?? 0;

$order_count_query = "SELECT COUNT(id) as total_orders FROM orders";
$order_count_result = mysqli_query($conn, $order_count_query);
$order_count_data = mysqli_fetch_assoc($order_count_result);
$total_orders = $order_count_data['total_orders'] ?? 0;

// Get current page from URL
$page = $_GET['page'] ?? 'overview'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>&#127839 Ethio Food Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="styling/admin.css"> </head>
    <script>
function openEditModal(id, name, email, role) {
    // Test if the function is even firing
    console.log("Opening modal for ID: " + id);
    
    const modal = document.getElementById('editModal');
    if(!modal) {
        alert("Error: Modal element not found in HTML!");
        return;
    }

    // Fill the inputs
    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_fullname').value = name;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_role').value = role;

    // Show it
    modal.style.display = 'block';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}
</script>
<body>

    <aside class="admin-sidebar">
        <h3>&#127839 ETHIO FOOD</h3>
        <ul class="nav-links">
    <li><a href="?page=overview" class="<?php echo $page == 'overview' ? 'active' : ''; ?>"><i class="fas fa-chart-line"></i> Overview</a></li>
    
    <li><a href="?page=users" class="<?php echo $page == 'users' ? 'active' : ''; ?>"><i class="fas fa-users"></i> Manage Users</a></li>
    
    <li><a href="?page=restaurants" class="<?php echo $page == 'restaurants' ? 'active' : ''; ?>"><i class="fas fa-utensils"></i> Restaurants</a></li>
    
    <li><a href="?page=orders" class="<?php echo $page == 'orders' ? 'active' : ''; ?>"><i class="fas fa-receipt"></i> Live Orders</a></li>
    <li><a href="otherpart/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
</ul>
    </aside>

    <main class="admin-main">
        <?php 
        // 3. THE DYNAMIC CONTENT SWITCHER
        switch($page) {
            
            case 'users': ?>
                <h1>User Management</h1>
                <div class="data-card">
                    <table class="admin-table">
                        <thead>
                            <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php
$users = mysqli_query($conn, "SELECT * FROM users");
while($u = mysqli_fetch_assoc($users)) {
    // We close the PHP tag here to write pure HTML
    ?>
    <tr>
        <td>#<?php echo $u['id']; ?></td>
        <td><?php echo $u['fullname']; ?></td>
        <td><?php echo $u['email']; ?></td>
        <td><span class='status-badge'><?php echo $u['user_role']; ?></span></td>
        <td>
            <button type="button" 
        class="btn-edit" 
        onclick="openEditModal(<?php echo $u['id']; ?>, '<?php echo addslashes($u['fullname']); ?>', '<?php echo $u['email']; ?>', '<?php echo $u['user_role']; ?>')">
    <i class="fas fa-edit"></i> Edit
</button>
            <a href="?page=users&action=delete_user&id=<?php echo $u['id']; ?>" 
               onclick="return confirm('Are you sure you want to delete this user?')" 
               class="btn-delete" style="color: red; margin-left: 10px;">
               <i class="fas fa-trash"></i>
            </a>
        </td>
    </tr>
    <?php 
} // End while 
?>
                        </tbody>
                    </table>
                </div>
            <?php break;

            case 'orders': ?>
                <h1>Order Control Center</h1>
                <div class="data-card">
                    <table class="admin-table">
                        <thead>
                            <tr><th>ID</th><th>Amount</th><th>Status</th><th>Date</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                           <?php
$orders = mysqli_query($conn, "SELECT * FROM orders ORDER BY created_at DESC");
while($o = mysqli_fetch_assoc($orders)) {
    ?>
    <tr>
        <td>#<?php echo $o['id']; ?></td>
        <td><?php echo number_format($o['total_price'], 2); ?> ETB</td>
        <td><span class='status-badge status-<?php echo $o['status']; ?>'><?php echo $o['status']; ?></span></td>
        <td><?php echo $o['created_at']; ?></td>
        <td>
            <form action="?page=orders&action=update_order&id=<?php echo $o['id']; ?>" method="POST" style="display:flex; gap:5px;">
                <select name="status" class="status-select">
                    <option value="pending" <?php echo ($o['status'] == 'pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="delivered" <?php echo ($o['status'] == 'delivered') ? 'selected' : ''; ?>>Delivered</option>
                </select>
                <button type="submit" class="btn-save">Update</button>
            </form>
        </td>
    </tr>
    <?php 
} // End while
?>
                        </tbody>
                    </table>
                </div>
            <?php break;
            case 'restaurants': ?>
    <h1>Restaurant Management</h1>
    <div class="data-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <p>Manage partner restaurants and their status.</p>
            <button class="btn-save" onclick="openAddModal()">
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
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // We use @ to suppress errors until you create the table
                $res_query = "SELECT * FROM restaurants ORDER BY id DESC";
                $res_result = @mysqli_query($conn, $res_query);

                if ($res_result && mysqli_num_rows($res_result) > 0) {
                    while($r = mysqli_fetch_assoc($res_result)) {
    // 1. Create the variables the buttons are looking for
    $id = $r['id'];
    $name = addslashes($r['name']);
    $loc = addslashes($r['location']);
    $rating = $r['rating'];
    
    echo "<tr>
        <td>#$id</td>
        <td><strong>{$r['name']}</strong></td>
        <td>$loc</td>
        <td>$rating</td>
        <td><span class='status-badge status-delivered'>Active</span></td>
        <td>
            <button type='button' class='btn-edit' 
                onclick=\"openResModal($id, '$name', '$loc', '$rating')\">
                <i class='fas fa-edit'></i> Edit
            </button>
            <a href='?page=restaurants&action=delete_restaurant&id=$id' 
               onclick=\"return confirm('Are you sure?')\" 
               class='btn-delete' style='color: red; margin-left: 10px;'>
               <i class='fas fa-trash'></i>
            </a>
        </td>
    </tr>";
}
                } else {
                    echo "<tr><td colspan='6' style='text-align:center; padding: 20px; color: var(--text-muted);'>
                            <i class='fas fa-store-slash fa-2x'></i><br>No restaurants found. Please create the 'restaurants' table.
                          </td></tr>";
                }
                ?>
                </tbody>
               </table>
              </div>
          <?php break;

            default: // OVERVIEW PAGE ?>
                <h1>Dashboard Overview</h1>
                <div class="stats-container">
                    <div class="stat-card">
                        <div><span>Total Revenue</span><span class="stat-val"><?php echo number_format($total_money, 2); ?> ETB</span></div>
                        <i class="fas fa-money-check-alt fa-2x" style="color:#2ecc71"></i>
                    </div>
                    <div class="stat-card">
                        <div><span>Total Orders</span><span class="stat-val"><?php echo $total_orders; ?></span></div>
                        <i class="fas fa-truck fa-2x" style="color:#3498db"></i>
                    </div>
                </div>
                <div class="data-card">
            <h3>Recent Activity</h3>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
            <?php break;
        } ?>
    </main>

</body>
<script src="scripting/admin.js"></script>
<div id="editModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background: white; margin: 10% auto; padding: 30px; border-radius: 12px; width: 400px; box-shadow: var(--shadow);">
        <h3>Edit User Information</h3>
        <hr><br>
        <form action="?page=users" method="POST">
            <input type="hidden" name="action" value="update_user_info">
            <input type="hidden" name="user_id" id="edit_user_id">
            
            <label>Full Name</label>
            <input type="text" name="fullname" id="edit_fullname" class="status-select" style="width:100%; margin-bottom:15px;" required>
            
            <label>Email</label>
            <input type="email" name="email" id="edit_email" class="status-select" style="width:100%; margin-bottom:15px;" required>
            
            <label>User Role</label>
            <select name="user_role" id="edit_role" class="status-select" style="width:100%; margin-bottom:15px;">
                <option value="customer">Customer</option>
                <option value="developer">Developer</option>
                <option value="owner">Owner</option>
            </select>
            
            <div style="display:flex; gap:10px; margin-top:10px;">
                <button type="submit" class="btn-save" style="flex:1;">Save Changes</button>
                <button type="button" onclick="closeModal()" class="btn-delete" style="flex:1; border:1px solid red; background:none;">Cancel</button>
            </div>
        </form>
    </div>
</div>
<div id="resModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
    <div class="modal-content" style="background: white; margin: 10% auto; padding: 30px; border-radius: 12px; width: 400px;">
        <h3>Edit Restaurant</h3>
        <hr><br>
        <form action="?page=restaurants" method="POST">
            <input type="hidden" name="action" value="update_restaurant_info">
            <input type="hidden" name="res_id" id="edit_res_id">
            
            <label>Restaurant Name</label>
            <input type="text" name="res_name" id="edit_res_name" class="status-select" style="width:100%; margin-bottom:15px;" required>
            
            <label>Location</label>
            <input type="text" name="res_location" id="edit_res_location" class="status-select" style="width:100%; margin-bottom:15px;" required>
            
            <label>Rating</label>
            <input type="number" step="0.1" name="res_rating" id="edit_res_rating" class="status-select" style="width:100%; margin-bottom:15px;">
            
            <div style="display:flex; gap:10px;">
                <button type="submit" class="btn-save" style="flex:1;">Update Restaurant</button>
                <button type="button" onclick="closeResModal()" class="btn-delete" style="flex:1; border:1px solid red; background:none;">Cancel</button>
            </div>
        </form>
    </div>
</div>

</html>