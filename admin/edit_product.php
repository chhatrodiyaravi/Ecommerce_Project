<!-- admin/edit_product.php content goes here -->
<?php
session_start();
include("../db_connect.php");

// check admin login
if (!isset($_SESSION['admin_username'])) {
    header("Location: login.php");
    exit();
}

$message = "";

// get product id from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view_products.php"); // redirect if no product selected
    exit();
}

$product_id = intval($_GET['id']);

// fetch product data
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    $message = "Product not found.";
} else {
    $product = mysqli_fetch_assoc($result);
}

// update product
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $price = $_POST['price'];
    $discount_price = $_POST['discount_price'];
    $offer_text = mysqli_real_escape_string($conn, $_POST['offer_text']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // check if new image uploaded
    $image = $product['image']; // keep old image if not updated
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "../images/";
        $image = time() . "_" . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $image;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            // image updated
        } else {
            $message = "Image upload failed!";
        }
    }

    // update query
    $sql = "UPDATE products 
            SET name='$name', company='$company', price='$price', discount_price='$discount_price', 
                offer_text='$offer_text', description='$description', image='$image' 
            WHERE id=$product_id";

    if (mysqli_query($conn, $sql)) {
        $message = "✅ Product updated successfully!";
        // refresh product data
        $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$product_id"));
    } else {
        $message = "❌ Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Product - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Edit Product</h2>

        <?php if ($message): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <?php if (!empty($product)) { ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Product Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($product['name']); ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Company</label>
                    <input type="text" name="company" value="<?= htmlspecialchars($product['company']); ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Price</label>
                    <input type="number" name="price" value="<?= $product['price']; ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Discount Price</label>
                    <input type="number" name="discount_price" value="<?= $product['discount_price']; ?>" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Offer Text</label>
                    <input type="text" name="offer_text" value="<?= htmlspecialchars($product['offer_text']); ?>" class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($product['description']); ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">Current Image</label><br>
                    <img src="../images/<?= $product['image']; ?>" width="120"><br><br>
                    <label class="form-label">Upload New Image (optional)</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="view_products.php" class="btn btn-secondary">Back</a>
            </form>
        <?php } ?>
    </div>
</body>

</html>