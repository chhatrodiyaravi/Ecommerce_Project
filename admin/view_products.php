<?php
session_start();
include("../db_connect.php"); // adjust path if needed

// ✅ Check if admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit();
}

// ✅ Fetch all products
$result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>View Products - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h1 class="mb-4">📷 Manage Products</h1>

        <a href="add_product.php" class="btn btn-success mb-3">➕ Add New Product</a>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Price</th>
                    <th>Discount Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td>
                            <?php if (!empty($row['image'])): ?>
                                <img src="../images/<?= htmlspecialchars($row['image']); ?>"
                                    width="80" style="object-fit:contain;">
                            <?php else: ?>
                                No Image
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['name']); ?></td>
                        <td><?= htmlspecialchars($row['company']); ?></td>
                        <td>₹<?= number_format($row['price'], 2); ?></td>
                        <td>
                            <?= $row['discount_price'] > 0 ? "₹" . number_format($row['discount_price'], 2) : "—"; ?>
                        </td>
                        <td>
                            <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-primary btn-sm">✏️ Edit</a>
                            <a href="delete_product.php?id=<?= $row['id']; ?>"
                                onclick="return confirm('Are you sure you want to delete this product?');"
                                class="btn btn-danger btn-sm">🗑️ Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <a href="dashboard.php" class="btn btn-secondary">⬅️ Back to Dashboard</a>
    </div>
</body>

</html>