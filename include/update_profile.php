<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $name = mysqli_real_escape_string($conn, $_POST['fullname']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // If password is provided, hash it. If not, don't update that column.
    if (!empty($password)) {
        $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET fullname='$name', phone='$phone', email='$email', password='$hashed_pw' WHERE id='$uid'";
    } else {
        $sql = "UPDATE users SET fullname='$name', phone='$phone', email='$email' WHERE id='$uid'";
    }

    if (mysqli_query($conn, $sql)) {
        header("Location: ../customer/customer.php?status=success");
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>