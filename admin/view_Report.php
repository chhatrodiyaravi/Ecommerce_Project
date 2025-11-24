<?php
session_start();
include("../db_connect.php"); // adjust path if needed

// check admin login
if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit();
}

// fetch orders
$result = mysqli_query($conn, "SELECT * FROM orders ORDER BY order_id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Orders - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <!-- Top bar with page title and dashboard button -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="mb-0">📦 Customer Orders</h1>
            <a href="dashboard.php" class="btn btn-primary">⬅ Back to Dashboard</a>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Order ID</th>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Total Amount</th>
                    <th>Payment Method</th>
                    <th>Order Date</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['order_id']; ?></td>
                        <td><?= htmlspecialchars($row['customer_name']); ?></td>
                        <td><?= $row['phone']; ?></td>
                        <td><?= nl2br(htmlspecialchars($row['address'])); ?></td>
                        <td>₹<?= number_format($row['total_amount'], 2); ?></td>
                        <td><?= $row['payment_method']; ?></td>
                        <td><?= date("d M Y, H:i", strtotime($row['order_date'])); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>