// index.php
<?php
session_start();
// (Optional) Check if admin logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
header("Location: dashboard.php");
exit();
?>