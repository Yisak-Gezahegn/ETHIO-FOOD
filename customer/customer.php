<?php
session_start();
include '../include/db_connect.php'; 

if (!isset($_SESSION['user_id'])) {
    header("Location: ../otherpart/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Order query
$order_sql = "SELECT orders.*, restaurants.name as restaurant_name 
              FROM orders 
              JOIN restaurants ON orders.restaurant_id = restaurants.id 
              WHERE orders.user_id = '$user_id' 
              ORDER BY 
                CASE 
                    WHEN status = 'pending' THEN 1 
                    WHEN status = 'delivered' THEN 2 
                    WHEN status = 'canceled' THEN 3 
                    ELSE 4 
                END ASC, 
                restaurants.name ASC, 
                orders.order_date DESC";

$order_result = mysqli_query($conn, $order_sql);

$profile_img = !empty($user['profile_pic']) ? $user['profile_pic'] : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ethio Food | Dashboard</title>
    <link rel="stylesheet" href="styling/customer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="dashboard-wrapper">
    <aside class="sidebar" style="background: linear-gradient(to bottom, #fe2020ff, #061246ff);">
        <div class="sidebar-profile">
            <img src="<?php echo $profile_img; ?>" style="border: 3px solid white;" alt="User Profile">
            <h3><?php echo htmlspecialchars($user['fullname']); ?></h3>
        </div>

        <nav class="nav-list">
            <button class="nav-item active" style="color: orange;" onclick="openTab(event, 'info')">
                <span><i class="far fa-user"></i> Personal Info</span> 
                <i class="fas fa-chevron-right"></i>
            </button>
            <button class="nav-item" style="color:orange;" onclick="openTab(event, 'settings')">
                <span><i class="fas fa-cog"></i> Settings</span> 
                <i class="fas fa-chevron-right"></i>
            </button>
            <button class="nav-item" style="color: orange;" onclick="openTab(event, 'orders')">
                <span><i class="fas fa-shopping-bag"></i> My Orders</span> 
                <i class="fas fa-chevron-right"></i>
            </button>
            <a href="../otherpart/logout.php" class="nav-item logout-btn">
                <span><i class="fas fa-sign-out-alt"></i> Logout</span>
            </a>
        </nav>
    </aside>

    <main class="main-content">
        <div class="content-header">
            <a href="../index.php" class="back-circle"><i class="fas fa-arrow-left"></i></a>
            <h1 id="tab-title">Personal Information</h1>
        </div>

        <div id="info" class="tab-pane active">
            <div class="info-card">
                <h2>Account Overview</h2>
                <div class="info-grid">
                    <div class="detail-group">
                        <label>Full Name</label>
                        <p><?php echo htmlspecialchars($user['fullname']); ?></p>
                    </div>
                    <div class="detail-group">
                        <label>Email Address</label>
                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                    <div class="detail-group">
                        <label>Phone Number</label>
                        <p><?php echo !empty($user['phone']) ? $user['phone'] : 'Not Provided'; ?></p>
                    </div>
                    <div class="detail-group">
    <label>Location</label>
    <p><?php echo !empty($user['location']) ? htmlspecialchars($user['location']) : 'Not Provided'; ?></p>
</div>
                </div>
                <button class="primary-btn" style="background:#444; margin-top:30px; width: auto; padding: 12px 25px;" onclick="openTab(null, 'settings')">
                    Edit Profile Details
                </button>
            </div>
        </div>

        <div id="settings" class="tab-pane">
            <div class="info-card">
                <h2>Update Profile</h2>
                <form action="../include/auth_handler.php" method="POST" enctype="multipart/form-data">
                    <div class="input-box" style="text-align: center; background: linear-gradient(to left, #0f1e82ff, #e0e0e0); padding: 20px; border-radius: 15px; margin-bottom: 25px;">
                        <img src="<?php echo $profile_img; ?>" id="imgPreview" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #ddd;">
                        <br>
                        <input type="file" name="p_image" style="margin-top: 15px;" onchange="preview(event)">
                    </div>
                    
                    <div class="info-grid">
                        <div class="input-box">
                            <label>Full Name</label>
                            <input type="text" name="fname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                        </div>
                        <div class="input-box">
                            <label>Email</label>
                            <input type="email" name="u_email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="input-box">
                            <label>Phone Number</label>
                            <input type="text" name="u_phone" value="<?php echo $user['phone'] ?? ''; ?>">
                        </div>
                        <div class="input-box">
    <label>Location (City/Area)</label>
    <input type="text" name="u_location" value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" placeholder="e.g. Addis Ababa, Bole">
</div>
                        <div class="input-box">
                            <label>New Password</label>
                            <input type="password" name="u_pass" placeholder="Leave blank to keep current">
                        </div>
                    </div>
                    
                    <button type="submit" name="update_profile" class="primary-btn">Save All Changes</button>
                </form>
            </div>
        </div>

        <div id="orders" class="tab-pane">
    <div class="info-card">
        <h2>Order History</h2>
        <style>
    .order-table th, .order-table td {
        text-align: left;
        padding: 15px;
    }
    .col-id { width: 10%; }
    .col-rest { width: 30%; }
    .col-date { width: 25%; }
    .col-price { width: 20%; }
    .col-status { width: 15%; }
</style>

<table class="order-table">
    <thead>
        <tr>
            <th class="col-id">Order ID</th>
            <th class="col-rest">Restaurant</th>
            <th class="col-date">Date</th>
            <th class="col-price">Amount</th>
            <th class="col-status">Status</th>
        </tr>
    </thead>
            <tbody>
                <?php if(mysqli_num_rows($order_result) > 0): ?>
                    <?php while($order = mysqli_fetch_assoc($order_result)): ?>
                        <tr>
                            <td><strong>#<?php echo $order['id']; ?></strong></td>
                            <td style="font-weight: 500; color: #555;">
                                <?php echo htmlspecialchars($order['restaurant_name']); ?>
                            </td>
                            <td><?php echo date('M d, Y | h:i A', strtotime($order['order_date'])); ?></td>
                            <td><?php echo number_format($order['total_price'], 2); ?> ETB</td>
                            <td>
                                <?php 
                                    $status = strtolower($order['status']);
                                    // Stress Testing the Status logic:
                                    if ($status == 'pending') {
                                        $badgeStyle = 'background:#fff3e0; color:#ef6c00; border: 1px solid #ffe0b2;';
                                    } elseif ($status == 'delivered') {
                                        $badgeStyle = 'background:#e8f5e9; color:#2e7d32; border: 1px solid #c8e6c9;';
                                    } elseif ($status == 'canceled') {
                                        $badgeStyle = 'background:#ffebee; color:#c62828; border: 1px solid #ffcdd2;';
                                    } else {
                                        $badgeStyle = 'background:#f5f5f5; color:#616161;';
                                    }
                                ?>
                                <span class="status-badge" style="<?php echo $badgeStyle; ?> padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.8rem;">
                                    <?php echo ucfirst($status); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding: 40px; color: #777;">
                            <i class="fas fa-box-open" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                            No orders placed yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
    </main>
</div>

<script>
function openTab(event, tabId) {
    // Hide all tabs
    document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
    // Show selected tab
    document.getElementById(tabId).classList.add('active');
    
    // Manage sidebar active class
    document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
    
    if(event) {
        event.currentTarget.classList.add('active');
    } else {
        // Find the sidebar button for manual triggers
        const btn = document.querySelector(`button[onclick*='${tabId}']`);
        if(btn) btn.classList.add('active');
    }
    
    // Update Title
    const titles = { 'info': 'Personal Information', 'settings': 'Account Settings', 'orders': 'My Orders' };
    document.getElementById('tab-title').innerText = titles[tabId];
}

function preview(event) {
    const reader = new FileReader();
    reader.onload = function(){
        document.getElementById('imgPreview').src = reader.result;
    }
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>