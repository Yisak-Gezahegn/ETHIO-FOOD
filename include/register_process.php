<?php
$stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password, user_role) VALUES (?, ?, ?, ?, 'customer')");
$stmt->bind_param("ssss", $name, $email, $phone, $hashed_password);
$stmt->execute();
?>
