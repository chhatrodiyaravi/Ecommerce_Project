<?php
session_start();
include('db_connect.php');
date_default_timezone_set('Asia/Kolkata');

if (!isset($_SESSION['email'])) {
    echo "<script>alert('Session expired. Please request a new OTP.');window.location='forgot_password.php';</script>";
    exit;
}

$email = $_SESSION['email'];
$otp = trim($_POST['otp'] ?? '');

if ($otp === '') {
    echo "<script>alert('Please enter the OTP.');window.location='verify_otp.php';</script>";
    exit;
}

// Ensure OTP format (6 digits)
if (!preg_match('/^\d{6}$/', $otp)) {
    echo "<script>alert('Invalid OTP format.');window.location='verify_otp.php';</script>";
    exit;
}

// Prepare SQL
$query = $conn->prepare("SELECT id FROM users WHERE email=? AND otp=? AND otp_expire >= NOW()");
$query->bind_param("ss", $email, $otp);

// Execute
$query->execute();

// Get result
$result = $query->get_result();

// If record found -> mark verified and redirect to reset page
if ($result && $result->num_rows > 0) {
    $_SESSION['otp_verified'] = true;
    echo "<script>alert('OTP verified successfully!');window.location='reset_password.php';</script>";
} else {
    echo "<script>alert('Invalid or expired OTP!');window.location='verify_otp.php';</script>";
}
