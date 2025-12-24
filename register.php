<?php
include 'config.php';

$message = "";

// Server-side validation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password_plain = $_POST['password'];

    // Basic server-side validation
    if (!preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username)) {
        $message = "❌ Username must be 3–20 characters (letters, numbers, underscore only).";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Invalid email address.";
    } elseif (strlen($password_plain) < 6) {
        $message = "❌ Password must be at least 6 characters.";
    } else {
        // Hash password
        $password = password_hash($password_plain, PASSWORD_DEFAULT);

        // Ensure DB connection is alive, try reconnect if needed
        if (!isset($conn) || (isset($conn) && !$conn->ping())) {
            try {
                $conn = new mysqli($host, $user, $pass, $dbname);
            } catch (Exception $e) {
                $message = "❌ Database Connection Error: " . $e->getMessage();
            }
            if (isset($conn) && $conn->connect_error) {
                $message = "❌ Database Connection Failed: " . $conn->connect_error;
            }
        }

        if (empty($message)) {
            // 1️⃣ Check if username exists (prepared)
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
            $stmt->bind_param('s', $username);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $message = "⚠️ Username already Exist. Please choose another.";
            }
            $stmt->close();
        }

        if (empty($message)) {
            // 2️⃣ Check if email exists (prepared)
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $message = "⚠️ Email already registered. <a href='login.php'>Login here</a>";
            }
            $stmt->close();
        }

        if (empty($message)) {
            // 3️⃣ Insert new record using prepared statement
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt) {
                $stmt->bind_param('sss', $username, $email, $password);
                if ($stmt->execute()) {
                    $message = "✅ Registration successful! <a href='login.php'>Login here</a>";
                } else {
                    $message = "❌ Database Error: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $message = "❌ Database Error: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Register - Camera Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-card {
            background: #fff;
            padding: 30px;
            border-radius: 15px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .register-card h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #2a5298;
        }

        .btn-custom {
            background: #2a5298;
            color: white;
            width: 100%;
        }

        .btn-custom:hover {
            background: #1e3c72;
        }
    </style>
</head>

<body>
    <div class="register-card">
        <h2>Create Account</h2>
        <?php if ($message) echo "<div class='alert alert-info'>$message</div>"; ?>
        <form method="POST" id="registerForm" class="needs-validation" novalidate>
            <!-- Username -->
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required
                    pattern="[A-Za-z0-9_]{3,20}">
                <div class="invalid-feedback">
                    Username must be 3–20 characters, letters/numbers/underscore only.
                </div>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
                <div class="invalid-feedback">
                    Please enter a valid email address.
                </div>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required minlength="6">
                <div class="invalid-feedback">
                    Password must be at least 6 characters long.
                </div>
            </div>

            <button class="btn btn-custom">Register</button>
        </form>
        <div class="text-center mt-3">
            <small>Already have an account? <a href="login.php">Login</a></small>
        </div>
    </div>

    <script>
        // Bootstrap custom validation script
        (() => {
            'use strict';
            const form = document.getElementById('registerForm');
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        })();
    </script>
</body>

</html>