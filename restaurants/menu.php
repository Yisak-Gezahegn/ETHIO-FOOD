<?php
// 1. Get the ID from the "View Menu" click
$restaurant_id = isset($_GET['restaurant_id']) ? (int)$_GET['restaurant_id'] : 0;

// 2. The Transfer Logic (The "Router")
// We map the ID from the database to your specific physical files
switch ($restaurant_id) {
    case 1:
        header("Location: menu/adisabeba.php");
        exit();
    case 4:
        header("Location: menu/rehobot.php");
        exit();
    case 2:
        header("Location: menu/blue.php");
        exit();
    case 3:
        header("Location: menu/habesha.php");
        exit();
    default:
        // If the ID doesn't match your 4 restaurants, send them back to the list
        header("Location: ../restaurants.php?error=invalid_restaurant");
        exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $restaurant['name']; ?> - Menu</title>
    <link rel="stylesheet" href="./style/menu.css"> </head>
<body>
    <header>
        <div class="container header-container">
            <a href="../index.php" class="logo">
                &#127839 ETHIO FOOD
            </a>
            
            <nav>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../restaurants/restaurants.php">Restaurants</a></li>
                    <li><a href="../otherpart/aboutus.php">About Us</a></li>
                    <li><a href="../otherpart/faq.php">FAQ</a></li>
                </ul>
            </nav>
            
            <div class="auth-buttons">
                 
    
</body>
</html>