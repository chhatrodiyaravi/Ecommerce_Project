<?php
session_start();
include("../db_connect.php");

// check admin login
if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $price = $_POST['price'];
    $discount_price = $_POST['discount_price'];
    $offer_text = mysqli_real_escape_string($conn, $_POST['offer_text']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // image upload
    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../images/";
        $image = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $image;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // uploaded successfully
        } else {
            $message = "Image upload failed!";
        }
    }

    // insert query
    $sql = "INSERT INTO products (name, company, price, discount_price, offer_text, description, image, rating, total_reviews) 
            VALUES ('$name', '$company', '$price', '$discount_price', '$offer_text', '$description', '$image', 0, 0)";

    if (mysqli_query($conn, $sql)) {
        $message = "✅ Product added successfully!";
    } else {
        $message = "❌ Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Product - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Add New Product</h2>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Company</label>
                <input type="text" name="company" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Price</label>
                <input type="number" name="price" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Discount Price</label>
                <input type="number" name="discount_price" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Offer Text</label>
                <input type="text" name="offer_text" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="4"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Product Image</label>
                <input type="file" name="image" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Add Product</button>
            <a href="dashboard.php" class="btn btn-secondary">Back</a>

        </form>
    </div>
</body>

</html>