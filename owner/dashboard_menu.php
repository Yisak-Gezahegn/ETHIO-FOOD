<?php
session_start();
// 1. Database Connection - MUST come first
include '../include/db_connect.php'; 

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 2. Security Check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: ../otherpart/login.php?error=unauthorized");
    exit();
}

$user_id = $_SESSION['user_id'];

// 3. Fetch Full User Data immediately (Fixes the "Undefined variable $user" error)
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($user_query);

// 4. HANDLE ALL PROFILE UPDATES (Including Image & Password)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_name = mysqli_real_escape_string($conn, $_POST['fullname']);
    $new_phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $new_loc = mysqli_real_escape_string($conn, $_POST['location']);
    $new_pass = $_POST['password'];

    // Handle Profile Image Upload
    $profile_pic = $user['profile_pic']; // Keep old one by default
    if (!empty($_FILES['profile_pic']['name'])) {
        $img_name = time() . '_' . $_FILES['profile_pic']['name'];
        if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], "../assets/img/profile/" . $img_name)) {
            $profile_pic = $img_name;
        }
    }

    $sql = "UPDATE users SET fullname = '$new_name', phone = '$new_phone', location = '$new_loc', profile_pic = '$profile_pic' WHERE id = '$user_id'";
    
    if (!empty($new_pass)) {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password = '$hashed' WHERE id = '$user_id'");
    }

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Profile updated successfully!'); window.location='dashboard_menu.php';</script>";
    }
}

// 5. Fetch Restaurant Data

$restaurant_id = $user['restaurant_id'] ?? null;
$my_restaurant = null;
if ($restaurant_id) {
    $res_query = mysqli_query($conn, "SELECT * FROM restaurants WHERE id = '$restaurant_id'");
    $my_restaurant = mysqli_fetch_assoc($res_query);
}

// FIXED LOGIC: Handles the submission
$success_msg = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_restaurant') {
    $name = mysqli_real_escape_string($conn, $_POST['res_name']);
    $loc = mysqli_real_escape_string($conn, $_POST['res_location']);
    $desc = mysqli_real_escape_string($conn, $_POST['res_description']);
    $img = mysqli_real_escape_string($conn, $_POST['res_image']);
    $tags = mysqli_real_escape_string($conn, $_POST['res_tags']);
    $delivery = mysqli_real_escape_string($conn, $_POST['res_delivery']);
    $time = mysqli_real_escape_string($conn, $_POST['res_time']);
    $rating = mysqli_real_escape_string($conn, $_POST['res_rating']);

    // INSERT logic
    $sql = "INSERT INTO restaurants (name, location, description, image_url, cuisine, delivery_fee, delivery_time, rating) 
            VALUES ('$name', '$loc', '$desc', '$img', '$tags', '$delivery', '$time', '$rating')";

    if (mysqli_query($conn, $sql)) {
        $new_id = mysqli_insert_id($conn);
        // CRITICAL: Link this new restaurant to the owner (you)
        mysqli_query($conn, "UPDATE users SET restaurant_id = '$new_id' WHERE id = '$user_id'");
        
        $success_msg = true; 
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}


$profile_img = !empty($user['profile_pic']) ? $user['profile_pic'] : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Owner Dashboard | Ethio Food</title>
    <link rel="stylesheet" href="./styling/style_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<div class="dashboard-container">
    <nav class="sidebar" style="background: linear-gradient(to bottom, #fe2020ff, #061246ff);">
        <div class="sidebar-profile">
            <img src="<?php echo $profile_img; ?>" style="border: 3px solid white;" alt="User Profile">
            <h3><?php echo htmlspecialchars($user['fullname']); ?></h3>
            <p>Restaurant Owner</p>
        </div>
        <hr>
        <ul>
            <li class="active"><a href="dashboard_menu.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="javascript:void(0)" onclick="toggleModal('profileModal', true)"><i class="fas fa-user-edit"></i> Edit Profile</a></li>
            <li><a href="javascript:void(0)" onclick="toggleModal('addModal', true)"><i class="fas fa-plus-circle"></i> Create Restaurant</a></li>
            <li><a href="../index.php"><i class="fas fa-sign-out-alt"></i> Back to Site</a></li>
        </ul>
    </nav>

    <main class="content">
        <header style="display:flex; justify-content:space-between; align-items:center;">
            <h1>Welcome, <?php echo htmlspecialchars($user['fullname']); ?></h1>
            <a href="../otherpart/logout.php" class="btn-login" style="background:#0015a0; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;">Logout</a>
        </header>

        <?php if ($my_restaurant): ?>
            <section class="my-res-card">
                <h3><i class="fas fa-utensils"></i> <?php echo $my_restaurant['name']; ?></h3>
                <p><i class="fas fa-map-marker-alt"></i> <?php echo $my_restaurant['location']; ?></p>
                <div style="margin-top:15px;">
                    <a href="restaurants_owner.php?id=<?php echo $my_restaurant['id']; ?>" class="btn-manage" style="background:#ff8c00; color:white; padding:10px; text-decoration:none; border-radius:5px;">Manage Menu</a>
                </div>
            </section>
        <?php else: ?>
            <div class="no-res" style="background:white; padding:40px; border-radius:10px; text-align:center; box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                <p>You don't have a restaurant yet.</p>
                <button onclick="toggleModal('addModal', true)" class="btn-create" style="background:#0015a0; color:white; border:none; padding:12px 25px; border-radius:5px; cursor:pointer;">Start Your Business Now</button>
            </div>
        <?php endif; ?>

        <hr style="margin: 30px 0;">

        <section class="search-area">
            <h3>Explore Other Partners</h3>
            <div class="search-box" style="position:relative; margin-bottom:20px;">
                <input type="text" id="searchInput" placeholder="Search by name, cuisine, or rating..." onkeyup="filterTable()" style="width:100%; padding:12px 40px; border-radius:25px; border:1px solid #ddd;">
                <i class="fas fa-search" style="position:absolute; left:15px; top:15px; color:#aaa;"></i>
            </div>

            <table id="resTable" style="width:100%; background:white; border-radius:10px; overflow:hidden; border-collapse:collapse;">
                <thead style="background:#f8f9fa;">
                    <tr>
                        <th style="padding:15px; text-align:left;">Name</th>
                        <th style="padding:15px; text-align:left;">Cuisine</th>
                        <th style="padding:15px; text-align:left;">Rating</th>
                        <th style="padding:15px; text-align:left;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $all = mysqli_query($conn, "SELECT * FROM restaurants");
                    while($row = mysqli_fetch_assoc($all)): ?>
                    <tr style="border-top:1px solid #eee;">
                        <td style="padding:15px;"><?php echo $row['name']; ?></td>
                        <td style="padding:15px;"><?php echo $row['cuisine']; ?></td>
                        <td style="padding:15px;">⭐ <?php echo $row['rating']; ?></td>
                        <td style="padding:15px;"><span style="color:green; font-weight:bold;">Active</span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>

<div id="profileModal" class="modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.7); overflow-y:auto;">
    <div class="modal-content" style="background:white; margin:5% auto; padding:30px; border-radius:10px; width:450px;">
        <h2><i class="fas fa-user-edit"></i> Edit Personal Info</h2>
        <form method="POST" enctype="multipart/form-data">
            <label>Full Name</label>
            <input type="text" name="fullname" value="<?php echo $user['fullname']; ?>" style="width:100%; padding:10px; margin:10px 0;" required>
            
            <label>Phone Number</label>
            <input type="text" name="phone" value="<?php echo $user['phone']; ?>" style="width:100%; padding:10px; margin:10px 0;" required>
            
            <label>Location</label>
            <input type="text" name="location" value="<?php echo $user['location']; ?>" style="width:100%; padding:10px; margin:10px 0;">
            
            <label>Update Profile Picture</label>
            <input type="file" name="profile_pic" style="margin:10px 0;">

            <label>New Password (Leave blank to keep current)</label>
            <input type="password" name="password" style="width:100%; padding:10px; margin:10px 0;" placeholder="Enter new password">
            
            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="submit" name="update_profile" style="flex:2; background:#0015a0; color:white; border:none; padding:12px; border-radius:5px; cursor:pointer;">Save Changes</button>
                <button type="button" onclick="toggleModal('profileModal', false)" style="flex:1; background:#eee; border:none; border-radius:5px; cursor:pointer;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="addModal" class="modal" style="display:none; position:fixed; z-index:9999; left:0; top:0; width:100%; height:100%; background:rgba(0,0,0,0.7); overflow-y:auto;">
    <div class="modal-content" style="background:white; margin:2% auto; padding:30px; border-radius:10px; width:550px;">
        <h3><i class="fas fa-plus-circle"></i> Add New Restaurant</h3>
        
        <form action="" method="POST">
            
            <input type="hidden" name="action" value="add_restaurant">

            <label>Restaurant Name</label>
            <input type="text" name="res_name" style="width:100%; padding:10px; margin:5px 0;" placeholder="e.g. Taste of Addis" required>
            
            <label>Location</label>
            <input type="text" name="res_location" style="width:100%; padding:10px; margin:5px 0;" placeholder="e.g. Bole, Addis Ababa" required>
            
            <label>Description</label>
            <textarea name="res_description" style="width:100%; padding:10px; margin:5px 0; height:80px;" placeholder="Describe your restaurant..."></textarea>
            
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                <div>
                    <label>Cuisine (Tags)</label>
                    <input type="text" name="res_tags" style="width:100%; padding:10px; margin:5px 0;" placeholder="Ethiopian, Pizza">
                </div>
                <div>
                    <label>Delivery Fee (ETB)</label>
                    <input type="number" step="0.01" name="res_delivery" style="width:100%; padding:10px; margin:5px 0;" placeholder="50.00">
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                <div>
                    <label>Delivery Time (min)</label>
                    <input type="text" name="res_time" style="width:100%; padding:10px; margin:5px 0;" placeholder="30-45">
                </div>
                <div>
                    <label>Initial Rating</label>
                    <input type="number" step="0.1" max="5" name="res_rating" value="4.5" style="width:100%; padding:10px; margin:5px 0;">
                </div>
            </div>

            <label>Image URL (e.g., res1.jpg)</label>
            <input type="text" name="res_image" style="width:100%; padding:10px; margin:5px 0;" placeholder="restaurant_image.jpg">

            <button type="submit" style="width:100%; background:#0015a0; color:white; border:none; padding:12px; margin-top:15px; border-radius:5px; font-weight:bold; cursor:pointer;">Create Restaurant</button>
            <button type="button" onclick="toggleModal('addModal', false)" style="width:100%; background:#eee; border:none; padding:10px; margin-top:5px; border-radius:5px; cursor:pointer;">Cancel</button>
        </form>
    </div>
</div>
<?php if ($success_msg): ?>
<div id="successPopup" style="position: fixed; top: 20px; right: 20px; background: #2ecc71; color: white; padding: 20px 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); z-index: 10000; display: flex; align-items: center; gap: 15px; animation: slideIn 0.5s ease-out;">
    <i class="fas fa-check-circle fa-2x"></i>
    <div>
        <strong style="display: block;">Success!</strong>
        <span>Restaurant added to your profile.</span>
    </div>
</div>

<style>
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
</style>
<?php endif; ?>
<script>
// 1. Sidebar and Modal Logic
function toggleModal(id, show) {
    const modal = document.getElementById(id);
    if (modal) {
        modal.style.display = show ? "block" : "none";
    }
}

// 2. Search Filter Logic
function filterTable() {
    let input = document.getElementById("searchInput").value.toUpperCase();
    let table = document.getElementById("resTable");
    let tr = table.getElementsByTagName("tr");

    for (let i = 1; i < tr.length; i++) {
        let textContent = tr[i].textContent || tr[i].innerText;
        tr[i].style.display = textContent.toUpperCase().indexOf(input) > -1 ? "" : "none";
    }
}

// 3. Success Message Logic (The "Safe" Way)
const popup = document.getElementById('successPopup');
if (popup) { // Only run this if the success message actually exists!
    setTimeout(() => {
        popup.style.animation = 'slideOut 0.5s ease-in';
        setTimeout(() => popup.remove(), 500);
    }, 4000);
}
</script>
</body>
</html>