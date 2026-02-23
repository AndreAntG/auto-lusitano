<?php
// logout.php - Handle user logout

require_once 'session.php';

// Check if user is logged in before logging out
if (isLoggedIn()) {
    logout();
} else {
    header('Location: login.php');
    exit();
}
?>