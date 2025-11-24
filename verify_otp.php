<?php include('layout.php'); ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg">
                <div class="card-body">
                    <h4 class="text-center mb-4">Verify OTP</h4>
                    <form action="check_otp.php" method="POST">
                        <div class="mb-3">
                            <label for="otp" class="form-label">Enter OTP</label>
                            <input type="text" class="form-control" id="otp" name="otp" required>
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
