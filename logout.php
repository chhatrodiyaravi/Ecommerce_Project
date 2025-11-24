<!-- logout.php content goes here -->
<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login page (or homepage)
header("Location: index.php");
exit();
?>

<a href="logout.php">Logout</a>