<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
include('db_connect.php');
session_start();
date_default_timezone_set('Asia/Kolkata');

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    // reset any previous OTP verification flag
    unset($_SESSION['otp_verified']);
    $_SESSION['email'] = $email;


    // Check if email exists
    $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $otp = rand(100000, 999999);
        $expire = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $update = $conn->prepare("UPDATE users SET otp=?, otp_expire=? WHERE email=?");
        $update->bind_param("sss", $otp, $expire, $email);
        $update->execute();

        // --- Setup PHPMailer ---
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'trendcart04@gmail.com';  // Your Gmail
            $mail->Password   = 'tuel ujlr bqwe raol';    // App Password
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            // Recipients
            $mail->setFrom('yourgmail@gmail.com', 'Camera Store');
            $mail->addAddress($email);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Camera Store - Password Reset OTP';
            $mail->Body    = "<h3>Your OTP for password reset is: <b>$otp</b></h3><p>This OTP will expire in 10 minutes.</p>";

            $mail->send();

            // ensure otp_verified is cleared when a new OTP is sent
            unset($_SESSION['otp_verified']);
            $_SESSION['email'] = $email;
            echo "<script>alert('OTP sent successfully to your email!');window.location='verify_otp.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('Mailer Error: {$mail->ErrorInfo}');window.location='forgot_password.php';</script>";
        }
    } else {
        echo "<script>alert('Email not found!');window.location='forgot_password.php';</script>";
    }
}
