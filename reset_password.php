<?php
session_start();
// ensure user verified OTP
if (!isset($_SESSION['email']) || empty($_SESSION['email']) || empty($_SESSION['otp_verified'])) {
    header('Location: forgot_password.php');
    exit;
}
include('layout.php'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h4 class="text-center mb-4">Reset Password</h4>
                    <form id="resetPassForm" action="update_password.php" method="POST" novalidate>
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="6">
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="6">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Password</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="login.php">Back to Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
<script>
    $(function() {
        $('#resetPassForm').validate({
            rules: {
                password: {
                    required: true,
                    minlength: 6
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    equalTo: '#password'
                }
            },
            messages: {
                password: {
                    required: 'Please enter a new password',
                    minlength: 'Minimum 6 characters'
                },
                confirm_password: {
                    required: 'Please confirm your password',
                    equalTo: 'Passwords do not match'
                }
            },
            errorClass: 'text-danger',
            errorElement: 'span'
        });
    });
</script>