<?php
session_start();
include("../db_connect.php");

// check admin login
if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit();
}

// fetch products
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
        <h1 class="mb-4">📦 All Products</h1>

        <a href="add_product.php" class="btn btn-primary mb-3">+ Add New Product</a>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Price</th>
                    <th>Discount Price</th>
                    <th>Offer</th>
                    <th>Rating</th>
                    <th>Total Reviews</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['name']); ?></td>
                        <td><?= htmlspecialchars($row['company']); ?></td>
                        <td>₹<?= number_format($row['price'], 2); ?></td>
                        <td>₹<?= number_format($row['discount_price'], 2); ?></td>
                        <td><?= htmlspecialchars($row['offer_text']); ?></td>
                        <td><?= htmlspecialchars($row['rating']); ?></td>
                        <td><?= htmlspecialchars($row['total_reviews']); ?></td>
                        <td>
                            <?php if (!empty($row['image'])) { ?>
                                <img src="../uploads/<?= $row['image']; ?>" alt="product" width="60">
                            <?php } else { ?>
                                <span>No Image</span>
                            <?php } ?>
                        </td>
                        <td>
                            <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_product.php?id=<?= $row['id']; ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure you want to delete this product?');">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>