<?php
// Handle form submission (Optional: Save to DB or send email)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // Example: Just display a success message
    $success = "Thank you, $name. We have received your message!";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Camera Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery and Validation plugin -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <style>
        /* Error styling */
        .text-danger {
            font-size: 0.875rem;
            color: #dc3545 !important;
            display: block;
            margin-top: 4px;
        }

        .is-invalid {
            border-color: #dc3545 !important;
        }
    </style>
</head>

<body>
    <?php include 'header.php'; ?>

    <!-- Contact Form -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Contact Us</h2>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <h4>Send us a message</h4>

                <!-- Add ID to form -->
                <form action="" method="POST" id="contactForm">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" id="name">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" id="subject">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="5" id="message"></textarea>
                    </div>
                    <button class="btn btn-primary">Send Message</button>
                </form>
            </div>

            <div class="col-md-6">
                <h4>Our Contact Details</h4>
                <p><strong>Address:</strong> 123 Camera Street, City, Country</p>
                <p><strong>Email:</strong> support@camerastore.com</p>
                <p><strong>Phone:</strong> +91 98765 43210</p>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <!-- jQuery Validation Rules -->
    <script>
        $(document).ready(function() {
            $("#contactForm").validate({
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    subject: {
                        required: true,
                        minlength: 3
                    },
                    message: {
                        required: true,
                        minlength: 10
                    }
                },
                messages: {
                    name: {
                        required: "⚠️ Please enter your name",
                        minlength: "Name must be at least 3 characters long"
                    },
                    email: {
                        required: "⚠️ Please enter your email",
                        email: "Please enter a valid email address"
                    },
                    subject: {
                        required: "⚠️ Please enter a subject",
                        minlength: "Subject must be at least 3 characters long"
                    },
                    message: {
                        required: "⚠️ Please enter your message",
                        minlength: "Message should be at least 10 characters long"
                    }
                },
                errorElement: "div",
                errorClass: "text-danger",
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid");
                }
            });
        });
    </script>

</body>

</html>