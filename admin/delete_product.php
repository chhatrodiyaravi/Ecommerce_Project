<?php
session_start();
include("../db_connect.php");

// check if admin logged in
if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit();
}

// check if product id is provided
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);

    // first fetch product image (to delete from /images folder)
    $result = mysqli_query($conn, "SELECT image FROM products WHERE id = $id");
    $product = mysqli_fetch_assoc($result);

    if ($product) {
        // delete product from DB
        $delete_sql = "DELETE FROM products WHERE id = $id";
        if (mysqli_query($conn, $delete_sql)) {
            // also delete image file if exists
            if (!empty($product['image']) && file_exists("../images/" . $product['image'])) {
                unlink("../images/" . $product['image']);
            }

            // redirect back with success message
            header("Location: view_products.php?msg=deleted");
            exit();
        } else {
            die("Error deleting product: " . mysqli_error($conn));
        }
    } else {
        header("Location: view_products.php?msg=notfound");
        exit();
    }
} else {
    header("Location: view_products.php?msg=invalid");
    exit();
}

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
        <h1 class="mb-4">📦 Manage Products</h1>
        <a href="add_product.php" class="btn btn-success mb-3">➕ Add New Product</a>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?= $row['id']; ?></td>
                        <td><?= htmlspecialchars($row['name']); ?></td>
                        <td>₹<?= number_format($row['price'], 2); ?></td>
                        <td><?= htmlspecialchars($row['description']); ?></td>
                        <td>
                            <?php if (!empty($row['image'])) { ?>
                                <img src="../uploads/<?= htmlspecialchars($row['image']); ?>" alt="Product Image" width="80">
                            <?php } else { ?>
                                No Image
                            <?php } ?>
                        </td>
                        <td>
                            <!-- <a href="edit_product.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">✏️ Edit</a> -->
                            <!-- <a href="delete_product.php?id=<?= $row['id']; ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this product?');">
                                🗑️ Delete
                            </a> -->
                            <a href="delete_product.php?id=<?= $row['id']; ?>"
                                onclick="return confirm('Are you sure you want to delete this product?');"
                                class="btn btn-danger btn-sm">🗑️ Delete</a>

                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>

</html>