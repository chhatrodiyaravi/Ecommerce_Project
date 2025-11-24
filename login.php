<?php
session_start();
include 'config.php';


$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            if (!empty($_GET['redirect'])) {
                header("Location: " . $_GET['redirect']);
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $message = "❌ Invalid password.";
        }
    } else {
        $message = "❌ User not found.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - Camera Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery Validation Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <style>
        body {
            height: 100vh;
            /* background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d); */
            background: #2a5298;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-card {
            max-width: 400px;
            width: 100%;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
            background: #fff;
            padding: 30px;
        }

        .login-card h2 {
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        .input-group-text {
            background: #f8f9fa;
        }

        .btn-custom {
            background: #1a2a6c;
            color: white;
            transition: 0.3s;
        }

        .btn-custom:hover {
            background: #0d1b4c;
        }
    </style>
</head>

<body>
    <div class="login-card">
        <h2><i class="bi bi-camera-fill me-2"></i>Camera Store</h2>
        <?php if ($message) echo "<div class='alert alert-danger'>$message</div>"; ?>
        <form method="POST" id="loginForm">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-primary w-100">Login</button>

            <div class="text-center mt-3">
                <a href="forgot_password.php" class="text-decoration-none">Forgot your password?</a>
            </div>
        </form>


        <div class="text-center mt-3">
            <p class="mb-1">Don't have an account?</p>
            <a href="register.php" class="btn btn-outline-secondary w-100">Sign up here</a>
        </div>

    </div>
    <script>
        $(document).ready(function() {
            $("#loginForm").validate({
                rules: {
                    username: {
                        required: true,
                        minlength: 3
                    },
                    password: {
                        required: true,
                        minlength: 6
                    }
                },
                messages: {
                    username: {
                        required: "Please enter your username",
                        minlength: "Username must be at least 3 characters long"
                    },
                    password: {
                        required: "Please enter your password",
                        minlength: "Password must be at least 6 characters long"
                    }
                },
                errorElement: "div",
                errorClass: "text-danger mt-1",
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