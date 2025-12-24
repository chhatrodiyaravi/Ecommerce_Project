<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camera Store</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- jQuery (needed for validation plugin) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery Validation Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="images/logo.png" alt="SnapShot" style="height: 40px; margin-right: 8px;">
                SnapShot
            </a>

            <!-- Navbar Toggler for Mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Centered Search Form -->
                <form id="searchForm" class="d-flex mx-auto align-items-start" action="index.php" method="GET" style="width: 400px;">

                    <div class="w-100 me-2 position-relative">
                        <input class="form-control" id="search" type="text" name="search"
                            placeholder="Search For Products,Brands and More">

                        <!-- Error will appear here -->
                        <span id="search-error" class="text-danger small" style="position:absolute; left:0; top:40px;"></span>
                    </div>

                    <button class="btn btn-outline-light" type="submit">Search</button>
                </form>



                <!-- Navigation Links (Right Aligned) -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php
                        // Default display name
                        $displayName = $_SESSION['username'] ?? '';
                        // Prefer session-stored avatar (set after upload) to avoid timing issues
                        $avatarSrc = $_SESSION['profile_photo'] ?? '';
                        // Try to get profile photo and username from DB if not available in session
                        if (empty($avatarSrc)) {
                            if (!isset($conn)) {
                                @include_once 'config.php';
                            }
                            if (isset($conn)) {
                                $stmt = $conn->prepare("SELECT profile_photo, username FROM users WHERE id = ? LIMIT 1");
                                $stmt->bind_param('i', $_SESSION['user_id']);
                                if ($stmt->execute()) {
                                    $res = $stmt->get_result();
                                    if ($res && $res->num_rows > 0) {
                                        $row = $res->fetch_assoc();
                                        if (!empty($row['profile_photo'])) $avatarSrc = $row['profile_photo'];
                                        if (!empty($row['username'])) $displayName = $row['username'];
                                    }
                                }
                                $stmt->close();
                            }
                        }
                        ?>
                        <li class="nav-item">
                            <a class="nav-link d-flex align-items-center" href="profile.php" title="View Profile">
                                <span class="me-2" style="color:inherit; font-weight:500"><?php echo htmlspecialchars($displayName); ?></span>
                                <?php if (!empty($avatarSrc)): ?>
                                    <img src="<?php echo htmlspecialchars($avatarSrc); ?>" alt="avatar" style="width:34px;height:34px;object-fit:cover;border-radius:50%;">
                                <?php else: ?>
                                    <i class="bi bi-person-circle" style="font-size:1.6rem;color:#fff"></i>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">
                            🛒 Cart
                            <?php if (!empty($_SESSION['cart'])): ?>
                                <span class="badge bg-danger"><?= count($_SESSION['cart']); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>



    <script>
        $(document).ready(function() {

            $("#searchForm").validate({
                rules: {
                    search: {
                        required: true,
                        minlength: 2
                    }
                },
                messages: {
                    search: {
                        required: "Please enter a search",
                        minlength: "Type at least 2 characters"
                    }
                },
                errorElement: "span",
                errorClass: "text-danger small", // smaller text for navbar
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                }
            });

        });
    </script>