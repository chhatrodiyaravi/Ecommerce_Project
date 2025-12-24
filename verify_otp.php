<?php include('layout.php'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h4 class="text-center mb-4">Verify OTP</h4>
                    <form id="verifyOtpForm" action="check_otp.php" method="POST" novalidate>
                        <div class="mb-3">
                            <label for="otp" class="form-label">Enter 6-digit OTP</label>
                            <input type="text" class="form-control" id="otp" name="otp" maxlength="6" inputmode="numeric" pattern="\d{6}" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100">Verify</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="forgot_password.php">Resend OTP</a>
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
        $('#verifyOtpForm').validate({
            rules: {
                otp: {
                    required: true,
                    digits: true,
                    minlength: 6,
                    maxlength: 6
                }
            },
            messages: {
                otp: {
                    required: 'Please enter the OTP sent to your email',
                    digits: 'Only digits allowed',
                    minlength: 'OTP must be 6 digits',
                    maxlength: 'OTP must be 6 digits'
                }
            },
            errorClass: 'text-danger',
            errorElement: 'span'
        });
    });
</script>