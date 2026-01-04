<?php
$conn = mysqli_connect("localhost", "root", "", "ethio_food_db");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
