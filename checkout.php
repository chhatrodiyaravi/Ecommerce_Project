<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout_form.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout_form.php');
    exit;
}

// Prevent checkout when cart is empty
if (empty($_SESSION['cart'])) {
    header('Location: checkout_form.php?error=empty_cart');
    exit;
}

$customer_name = trim($_POST['customer_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');
$total_amount = floatval($_POST['total_amount'] ?? 0);
$payment_method = $_POST['payment_method'] ?? '';

if (!$customer_name || !$phone || !$address || !$payment_method) {
    header('Location: checkout_form.php?error=missing');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Insert order and items in a transaction
$conn->begin_transaction();
try {
    $orderStmt = $conn->prepare("INSERT INTO orders (user_id, customer_name, phone, address, total_amount, payment_method, order_date) VALUES (?, ?, ?, ?, ?, ?, NOW())");
    if (!$orderStmt) throw new Exception($conn->error);
    $orderStmt->bind_param('isssds', $user_id, $customer_name, $phone, $address, $total_amount, $payment_method);
    if (!$orderStmt->execute()) throw new Exception($orderStmt->error);
    $order_id = $orderStmt->insert_id;
    $orderStmt->close();

    if (!empty($_SESSION['cart'])) {
        $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        if (!$itemStmt) throw new Exception($conn->error);
        foreach ($_SESSION['cart'] as $it) {
            $pid = (int) ($it['id'] ?? 0);
            $qty = (int) ($it['qty'] ?? 1);
            $price = (float) ($it['price'] ?? 0);
            if ($pid <= 0) continue;
            $itemStmt->bind_param('iiid', $order_id, $pid, $qty, $price);
            if (!$itemStmt->execute()) throw new Exception($itemStmt->error);
        }
        $itemStmt->close();
    }

    // commit and clear cart
    $conn->commit();
    $orderSaved = true;
    $savedOrderId = $order_id;
    $savedTotal = $total_amount;
    unset($_SESSION['cart']);
} catch (Exception $e) {
    $conn->rollback();
    error_log('Checkout error: ' . $e->getMessage());
    header('Location: checkout_form.php?error=server');
    exit;
}

// Fetch order items for display
$items = [];
$stmt = $conn->prepare("SELECT oi.product_id, oi.quantity, oi.price, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$stmt->bind_param('i', $savedOrderId);
$stmt->execute();
$res = $stmt->get_result();
while ($r = $res->fetch_assoc()) $items[] = $r;
$stmt->close();

include 'header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center">
                    <h3 class="text-success">✅ Order Placed Successfully</h3>
                    <p class="mb-1">Thank you, <strong><?php echo htmlspecialchars($customer_name); ?></strong>.</p>
                    <p class="mb-3">Your order number is <strong>#<?php echo htmlspecialchars($savedOrderId); ?></strong>.</p>

                    <h5 class="mt-4">Order Summary</h5>
                    <ul class="list-group mb-3">
                        <?php foreach ($items as $it): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="images/<?php echo htmlspecialchars($it['image']); ?>" alt="" style="width:60px;height:60px;object-fit:cover;margin-right:12px;">
                                    <div>
                                        <div><?php echo htmlspecialchars($it['name']); ?></div>
                                        <small class="text-muted">Qty: <?php echo (int)$it['quantity']; ?></small>
                                    </div>
                                </div>
                                <div>₹<?php echo number_format($it['price'] * $it['quantity'], 2); ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="d-flex justify-content-between">
                        <div>Total Paid</div>
                        <div><strong>₹<?php echo number_format($savedTotal, 2); ?></strong></div>
                    </div>

                    <div class="mt-4">
                        <a href="index.php" class="btn btn-primary me-2">Continue Shopping</a>
                        <a href="orders.php" class="btn btn-outline-secondary">View My Orders</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>