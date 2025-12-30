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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
        .cart-item img {
            max-width: 100px;
            max-height: 80px;
            object-fit: cover;
        }

        .qty-controls button {
            width: 36px;
        }

        .muted {
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <h2 class="mb-4">Shopping Cart</h2>

        <div class="row g-4">
            <div class="col-lg-8">
                <?php if (!empty($_SESSION['cart'])): ?>
                    <div class="list-group">
                        <?php
                        $grandTotal = 0;
                        $imgStmt = $conn->prepare("SELECT image FROM products WHERE id = ? LIMIT 1");
                        foreach ($_SESSION['cart'] as $key => $item):
                            $total = $item['price'] * $item['qty'];
                            $grandTotal += $total;
                            $imagePath = null;
                            if ($imgStmt) {
                                $pid = (int)$item['id'];
                                $imgStmt->bind_param('i', $pid);
                                $imgStmt->execute();
                                $res = $imgStmt->get_result();
                                if ($rowImg = $res->fetch_assoc()) {
                                    $imagePath = $rowImg['image'];
                                }
                            }
                        ?>
                            <div class="list-group-item py-3 cart-item">
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0">
                                        <?php if (!empty($imagePath) && file_exists($imagePath)): ?>
                                            <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="rounded">
                                        <?php else: ?>
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width:100px;height:80px;">
                                                <i class="bi bi-camera fs-3 muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h5 class="mb-1"><?= htmlspecialchars($item['name']) ?></h5>
                                                <div class="muted">Price: ₹<?= number_format($item['price'], 2) ?></div>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold">₹<?= number_format($total, 2) ?></div>
                                                <small class="muted">Subtotal</small>
                                            </div>
                                        </div>

                                        <div class="mt-3 d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center gap-2 qty-controls">
                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="key" value="<?= $key ?>">
                                                    <button type="submit" name="decrease_qty" class="btn btn-outline-secondary btn-sm">-</button>
                                                </form>

                                                <div class="px-2"><?= $item['qty'] ?></div>

                                                <form method="post" style="display:inline;">
                                                    <input type="hidden" name="key" value="<?= $key ?>">
                                                    <button type="submit" name="increase_qty" class="btn btn-outline-secondary btn-sm">+</button>
                                                </form>
                                            </div>

                                            <div>
                                                <form method="post" onsubmit="return confirm('Remove this item from cart?');" style="display:inline;">
                                                    <input type="hidden" name="key" value="<?= $key ?>">
                                                    <button type="submit" name="remove_item" class="btn btn-sm btn-outline-danger">Remove</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-cart-x display-1 muted"></i>
                        <h4 class="mt-3">Your cart is empty</h4>
                        <p class="muted">Browse our products and add items to your cart.</p>
                        <a href="index.php" class="btn btn-primary">Start Shopping</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Order Summary</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="muted">Items Total</div>
                            <div>₹<?= number_format($grandTotal ?? 0, 2) ?></div>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <div class="muted">Shipping</div>
                            <div>Free</div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <div class="fw-bold">Total</div>
                            <div class="fw-bold">₹<?= number_format($grandTotal ?? 0, 2) ?></div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="index.php" class="btn btn-outline-primary">Continue Shopping</a>

                            <a href="?clear=1" class="btn btn-outline-warning" id="clearCartBtn">Clear Cart</a>

                            <?php if (!empty($_SESSION['cart'])): ?>
                                <form action="checkout.php" method="POST">
                                    <input type="hidden" name="total_amount" value="<?= $grandTotal ?? 0 ?>">
                                    <button type="submit" class="btn btn-success">Proceed to Checkout</button>
                                </form>
                            <?php else: ?>
                                <button type="button" id="proceedEmptyBtn" class="btn btn-success">Proceed to Checkout</button>
                                <script>
                                    document.getElementById('proceedEmptyBtn')?.addEventListener('click', function() {
                                        alert('Your cart is empty. Add items before proceeding to checkout.');
                                    });
                                </script>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('clearCartBtn')?.addEventListener('click', function(e) {
            if (!confirm('Clear all items from cart?')) {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>