<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['order_id'] ?? 0;

// Fetch products from this order (safe prepared statement)
$sql = "SELECT oi.product_id, p.name, p.image 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Write Reviews</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
    </style>
</head>

<body>

    <!-- Center everything vertically & horizontally -->
    <div class="d-flex flex-column justify-content-center align-items-center min-vh-100 w-100">

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show w-75 mb-4" role="alert">
                ✅ Review submitted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <h2 class="mb-4 text-center">Write a Review for Your Purchased Products</h2>

        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="card shadow p-4 mb-4" style="width: 500px; max-width: 95%;">
                <div class="text-center mb-3">
                    <img src="images/<?= htmlspecialchars($row['image']) ?>" class="img-fluid rounded" style="max-height:150px;" alt="<?= htmlspecialchars($row['name']) ?>">
                </div>
                <h5 class="text-center mb-3"><?= htmlspecialchars($row['name']) ?></h5>

                <div class="mb-3 text-center text-muted">Review submission has been disabled for this order.</div>
            </div>
        <?php endwhile; ?>

    </div>

    <!-- Review submission disabled: client-side validation removed -->

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>