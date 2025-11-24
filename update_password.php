<?php
include('db_connect.php');
session_start();

$email = $_SESSION['email'];
$new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);

$update = $conn->prepare("UPDATE users SET password=?, otp=NULL, otp_expire=NULL WHERE email=?");
$update->bind_param("ss", $new_password, $email);

if ($update->execute()) {
    session_destroy();
    echo "<script>alert('Password updated successfully!');window.location='login.php';</script>";
} else {
    echo "<script>alert('Failed to update password. Try again.');window.location='reset_password.php';</script>";
}
?>
