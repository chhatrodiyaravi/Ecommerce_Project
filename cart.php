<?php
// =======================
// Start session + DB
// =======================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'config.php';

// =======================
// If user logged in & no session cart → load from DB
// =======================
if (isset($_SESSION['user_id']) && empty($_SESSION['cart'])) {
    $user_id = $_SESSION['user_id'];

    // Join with your products table to get name/price (example)
    $sql = "SELECT uc.product_id, uc.qty, p.name, p.price
            FROM user_cart uc 
            JOIN products p ON uc.product_id = p.id
            WHERE uc.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    $_SESSION['cart'] = [];
    while ($row = $res->fetch_assoc()) {
        $_SESSION['cart'][] = [
            'id' => $row['product_id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'qty' => $row['qty']
        ];
    }
}

// =======================
// Handle Add to Cart
// =======================
if (isset($_POST['product_id'], $_POST['product_name'], $_POST['product_price'])) {
    $id = intval($_POST['product_id']);
    $name = $_POST['product_name'];
    $price = floatval($_POST['product_price']);
    $qty = intval($_POST['product_qty']) ?: 1;

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Find if product already exists in cart
    $foundKey = null;
    foreach ($_SESSION['cart'] as $key => $item) {
        if ($item['id'] == $id) {
            $foundKey = $key;
            break;
        }
    }

    if ($foundKey !== null) {
        $_SESSION['cart'][$foundKey]['qty'] += $qty;
    } else {
        $_SESSION['cart'][] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'qty' => $qty
        ];
    }

    // Save to DB if logged in
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("SELECT id FROM user_cart WHERE user_id=? AND product_id=?");
        $stmt->bind_param("ii", $user_id, $id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $stmt2 = $conn->prepare("UPDATE user_cart SET qty = qty + ? WHERE user_id=? AND product_id=?");
            $stmt2->bind_param("iii", $qty, $user_id, $id);
            $stmt2->execute();
        } else {
            $stmt2 = $conn->prepare("INSERT INTO user_cart (user_id, product_id, qty) VALUES (?, ?, ?)");
            $stmt2->bind_param("iii", $user_id, $id, $qty);
            $stmt2->execute();
        }
    }
}

// =======================
// Handle Increase Qty
// =======================
if (isset($_POST['increase_qty'])) {
    $key = intval($_POST['key']);
    if (isset($_SESSION['cart'][$key])) {
        $_SESSION['cart'][$key]['qty']++;

        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $pid = $_SESSION['cart'][$key]['id'];
            $stmt = $conn->prepare("UPDATE user_cart SET qty = qty + 1 WHERE user_id=? AND product_id=?");
            $stmt->bind_param("ii", $user_id, $pid);
            $stmt->execute();
        }
    }
}

// =======================
// Handle Decrease Qty
// =======================
if (isset($_POST['decrease_qty'])) {
    $key = intval($_POST['key']);
    if (isset($_SESSION['cart'][$key]) && $_SESSION['cart'][$key]['qty'] > 1) {
        $_SESSION['cart'][$key]['qty']--;

        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $pid = $_SESSION['cart'][$key]['id'];
            $stmt = $conn->prepare("UPDATE user_cart SET qty = qty - 1 WHERE user_id=? AND product_id=?");
            $stmt->bind_param("ii", $user_id, $pid);
            $stmt->execute();
        }
    }
}

// =======================
// Handle Remove Single Item
// =======================
if (isset($_POST['remove_item'])) {
    $key = intval($_POST['key']);
    if (isset($_SESSION['cart'][$key])) {
        if (isset($_SESSION['user_id'])) {
            $user_id = $_SESSION['user_id'];
            $pid = $_SESSION['cart'][$key]['id'];
            $stmt = $conn->prepare("DELETE FROM user_cart WHERE user_id=? AND product_id=?");
            $stmt->bind_param("ii", $user_id, $pid);
            $stmt->execute();
        }

        unset($_SESSION['cart'][$key]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex
    }
}

// =======================
// Handle Remove All Items
// =======================
if (isset($_GET['clear']) && $_GET['clear'] == 1) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $stmt = $conn->prepare("DELETE FROM user_cart WHERE user_id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
    }
    unset($_SESSION['cart']);
}

// =======================
// HTML Output of Cart
// =======================
include 'header.php'; // your header nav (shows cart count)
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container my-5">
        <h2>Your Cart</h2>
        <?php if (!empty($_SESSION['cart'])): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $grandTotal = 0;
                    foreach ($_SESSION['cart'] as $key => $item):
                        $total = $item['price'] * $item['qty'];
                        $grandTotal += $total;
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>₹<?= number_format($item['price'], 2) ?></td>
                            <td>
                                <!-- decrease -->
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="key" value="<?= $key ?>">
                                    <button type="submit" name="decrease_qty" class="btn btn-sm btn-secondary">-</button>
                                </form>

                                <?= $item['qty'] ?>

                                <!-- increase -->
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="key" value="<?= $key ?>">
                                    <button type="submit" name="increase_qty" class="btn btn-sm btn-secondary">+</button>
                                </form>
                            </td>
                            <td>₹<?= number_format($total, 2) ?></td>
                            <td>
                                <!-- remove item -->
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="key" value="<?= $key ?>">
                                    <button type="submit" name="remove_item" class="btn btn-sm btn-danger">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Grand Total</strong></td>
                        <td colspan="2"><strong>₹<?= number_format($grandTotal, 2) ?></strong></td>
                    </tr>
                </tbody>
            </table>

            <!-- buttons below table -->
            <a href="?clear=1" class="btn btn-warning">Clear Cart</a>
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>

            <!-- checkout form separately -->
            <form action="checkout.php" method="POST" style="display:inline;">
                <input type="hidden" name="total_amount" value="<?= $grandTotal ?>">
                <a href="checkout_form.php" class="btn btn-success">Proceed to Checkout</a>
            </form>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>

</body>

</html>