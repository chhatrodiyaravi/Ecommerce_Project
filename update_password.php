<?php
include('db_connect.php');
session_start();

// Ensure OTP was verified and email exists in session
if (empty($_SESSION['email']) || empty($_SESSION['otp_verified'])) {
    echo "<script>alert('Unauthorized action. Please follow the password reset flow.');window.location='forgot_password.php';</script>";
    exit;
}

$email = $_SESSION['email'];
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (strlen($password) < 6) {
    echo "<script>alert('Password must be at least 6 characters.');window.location='reset_password.php';</script>";
    exit;
}
if ($password !== $confirm) {
    echo "<script>alert('Passwords do not match.');window.location='reset_password.php';</script>";
    exit;
}

$new_password = password_hash($password, PASSWORD_BCRYPT);

$update = $conn->prepare("UPDATE users SET password=?, otp=NULL, otp_expire=NULL WHERE email=?");
$update->bind_param("ss", $new_password, $email);

if ($update->execute()) {
    // clear session data related to OTP/reset
    unset($_SESSION['email'], $_SESSION['otp_verified']);
    session_destroy();
    echo "<script>alert('Password updated successfully!');window.location='login.php';</script>";
} else {
    echo "<script>alert('Failed to update password. Try again.');window.location='reset_password.php';</script>";
}
