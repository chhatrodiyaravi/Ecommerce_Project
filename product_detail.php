<?php
session_start();
include 'db_connect.php';

// ------------------- Validate Product ID -------------------
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid product ID.");
}

$product_id = intval($_GET['id']);

// ------------------- Fetch Product -------------------
$sql = "SELECT * FROM products WHERE id = $product_id";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    die("Product not found.");
}

$product = $result->fetch_assoc();

// ------------------- Handle Add to Cart -------------------
if (isset($_POST['add_to_cart'])) {
    $id = $product['id'];
    $name = $product['name'];
    $price = !empty($product['discount_price']) && $product['discount_price'] > 0
        ? $product['discount_price']
        : $product['price'];
    $qty = 1;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $found = false;
    foreach ($_SESSION['cart'] as &$item) {
        if ($item['id'] == $id) {
            $item['qty'] += $qty;
            $found = true;
            break;
        }
    }

    if (!$found) {
        $_SESSION['cart'][] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'qty' => $qty
        ];
    }

    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="container my-5">
        <div class="row">
            <!-- Product Image -->
            <div class="col-md-6 text-center">
                <img src="images/<?= htmlspecialchars($product['image']) ?>" class="img-fluid rounded shadow"
                    alt="<?= htmlspecialchars($product['name']) ?>" style="max-height:400px; object-fit:contain;">
            </div>



            <!-- Product Details -->
            <div class="col-md-6">
                <h2><?= htmlspecialchars($product['name']) ?></h2>
                <p class="text-muted"><?= htmlspecialchars($product['company']) ?></p>

                <!-- Price -->
                <?php if (!empty($product['discount_price']) && $product['discount_price'] > 0 && $product['discount_price'] < $product['price']): ?>
                    <?php
                    $discount_percent = round((($product['price'] - $product['discount_price']) / $product['price']) * 100);
                    ?>
                    <h4 class="text-danger">
                        ₹<?= number_format($product['discount_price'], 2) ?>
                        <small class="text-muted"><del>₹<?= number_format($product['price'], 2) ?></del></small>
                        <span class="text-success"><?= $discount_percent ?>% off</span>
                    </h4>
                <?php else: ?>
                    <h4 class="text-success">₹<?= number_format($product['price'], 2) ?></h4>
                <?php endif; ?>

                <!-- Ratings -->
                <p>
                    <?php
                    $rating = round($product['rating']);
                    for ($i = 1; $i <= 5; $i++):
                        echo ($i <= $rating) ? "⭐" : "☆";
                    endfor;
                    ?>
                    <small>(<?= $product['total_reviews'] ?> reviews)</small>
                </p>

                <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

                <!-- Add to Cart -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <form method="post">
                        <button type="submit" name="add_to_cart" class="btn btn-primary btn-lg mt-3">Add to Cart</button>
                    </form>
                <?php else: ?>
                    <a href="login.php?redirect=product_detail.php?id=<?= $product['id'] ?>"
                        class="btn btn-warning btn-lg mt-3">Add to Cart</a>
                <?php endif; ?>

                <div class="mt-4">
                    <a href="index.php" class="btn btn-outline-secondary">← Back to Shop</a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->
</body>

</html>