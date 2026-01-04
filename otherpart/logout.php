<link rel="stylesheet" href="./styling/loginstyle.css">

<?php
// 1. Start the session to access it
session_start();

// 2. Unset all session variables (ID, Name, Role)
$_SESSION = array();

// 3. Destroy the session cookie to be 100% secure
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Finally, destroy the session on the server
session_destroy();

// 5. Redirect the user back to the login page with a message
header("Location: login.php?message=loggedout");
exit();
?>
<script src="script.js"></script>