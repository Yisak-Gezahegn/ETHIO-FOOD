<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // --- 1. SIGNUP LOGIC ---
    if (isset($_POST['signup_btn'])) {
        $name     = mysqli_real_escape_string($conn, $_POST['full_name']);
        $email    = trim(mysqli_real_escape_string($conn, $_POST['email']));
        $phone    = mysqli_real_escape_string($conn, $_POST['phone']);
        $role     = mysqli_real_escape_string($conn, $_POST['user_role']);
        $location = mysqli_real_escape_string($conn, $_POST['location']);
        $pass     = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (fullname, email, phone, password, user_role, location) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $phone, $pass, $role, $location);
        
        if ($stmt->execute()) {
            header("Location: ../otherpart/login.php?signup=success");
            exit();
        } else {
            header("Location: ../otherpart/login.php?error=emailexists");
            exit();
        }
        $stmt->close();
    }
    
    // --- 2. LOGIN LOGIC ---
    if (isset($_POST['login_btn'])) {
        $email = trim($_POST['email']);
        $pass  = $_POST['password'];

        $stmt = $conn->prepare("SELECT id, fullname, password, user_role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($pass, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['full_name'] = $user['fullname'];
                $_SESSION['role'] = $user['user_role'];

                if ($user['user_role'] == 'developer') {
                    header("Location: ../admin_dashboard.php"); 
                } elseif ($user['user_role'] == 'owner') {
                    header("Location: ../owner/owner_dashboard.php"); 
                } else {
                    header("Location: ../index.php");
                }
                exit();
            } else {
                header("Location: ../otherpart/login.php?error=wrongpass");
                exit();
            }
        } else {
            header("Location: ../otherpart/login.php?error=nouser");
            exit();
        }
        $stmt->close();
    }

    // --- 3. UPDATE PROFILE LOGIC (Fixing the Location Edit) ---
    if (isset($_POST['update_profile']) && isset($_SESSION['user_id'])) {
        $uid      = $_SESSION['user_id'];
        $name     = mysqli_real_escape_string($conn, $_POST['fname']);
        $email    = mysqli_real_escape_string($conn, $_POST['u_email']);
        $phone    = mysqli_real_escape_string($conn, $_POST['u_phone']);
        $location = mysqli_real_escape_string($conn, $_POST['u_location']); // Location added here!
        
        $img_sql = "";
        if (!empty($_FILES['p_image']['name'])) {
            $file_name = time() . "_" . basename($_FILES['p_image']['name']);
            $target_path = "../uploads/" . $file_name;
            if (move_uploaded_file($_FILES['p_image']['tmp_name'], $target_path)) {
                $img_sql = ", profile_pic = '$target_path'";
            }
        }

        $pass_sql = "";
        if (!empty($_POST['u_pass'])) {
            $hash = password_hash($_POST['u_pass'], PASSWORD_DEFAULT);
            $pass_sql = ", password = '$hash'";
        }

        // Added 'location' to this UPDATE query
        $sql = "UPDATE users SET fullname='$name', email='$email', phone='$phone', location='$location' $img_sql $pass_sql WHERE id='$uid'";
        
        if (mysqli_query($conn, $sql)) {
            header("Location: ../customer/customer.php?msg=success");
            exit();
        } else {
            die("SQL Error: " . mysqli_error($conn));
        }
    }

    // --- 4. PLACE ORDER LOGIC ---
    if (isset($_POST['place_order'])) {
        if (!isset($_SESSION['user_id'])) {
            header("Location: ../otherpart/login.php");
            exit();
        }

        $user_id = $_SESSION['user_id'];
        $total_price = $_POST['total_price'];
        $restaurant_id = $_POST['restaurant_id'];
        $status = 'pending';

        // NOTE: Your order logic uses PDO, but the rest uses MySQLi. 
        // For consistency, here is the MySQLi version:
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status, restaurant_id, order_date) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("idsi", $user_id, $total_price, $status, $restaurant_id);
        
        if ($stmt->execute()) {
            unset($_SESSION['cart']);
            header("Location: ../customer/customer.php?msg=order_success");
            exit();
        } else {
            die("Order Error: " . $conn->error);
        }
    }
    // --- Inside auth_handler.php ---

// 1. Check if this is a "Forgot Password" request

// --- 5. FORGOT PASSWORD LOGIC (Reset to Original-style Code) ---
if (isset($_POST['email']) && isset($_POST['phone']) && !isset($_POST['login_btn']) && !isset($_POST['signup_btn'])) {
    
    ob_clean(); 
    header('Content-Type: application/json');

    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // 1. Check if the user exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND phone = ?");
    $stmt->bind_param("ss", $email, $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $userId = $user['id'];
        
        // 2. Generate a "Real" readable code (like a temporary password)
        $newPlainPassword = "ETHIO" . rand(1000, 9999); 
        
        // 3. Hash it so it works with your Login Logic
        $newHashedPassword = password_hash($newPlainPassword, PASSWORD_DEFAULT);

        // 4. Update the database with the new hashed version
        $updateStmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $updateStmt->bind_param("si", $newHashedPassword, $userId);
        
        if ($updateStmt->execute()) {
            // 5. Send the PLAIN code back to the JavaScript
            echo json_encode([
                'success' => true, 
                'code' => $newPlainPassword // This is what the user sees
            ]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Update failed']);
        }
        $updateStmt->close();
    } else {
        echo json_encode(['success' => false]);
    }
    
    $stmt->close();
    exit(); 
}
}
?>