<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=orders.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Fetch orders summary for user
$orders = [];
$stmt = $conn->prepare("SELECT o.order_id AS id, o.total_amount, o.order_date, o.payment_method, COUNT(oi.item_id) as item_count
    FROM orders o
    LEFT JOIN order_items oi ON o.order_id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.order_id
    ORDER BY o.order_date DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $orders[] = $row;
}
$stmt->close();

// If user requested a specific order, fetch its items (and verify ownership)
$orderDetails = null;
if (isset($_GET['order_id'])) {
    $order_id = (int) $_GET['order_id'];

    $v = $conn->prepare('SELECT order_id FROM orders WHERE order_id = ? AND user_id = ? LIMIT 1');
    $v->bind_param('ii', $order_id, $user_id);
    $v->execute();
    $vr = $v->get_result();
    if ($vr->num_rows === 1) {
        $v->close();

        $orderDetails = [
            'order' => null,
            'items' => []
        ];

        $oStmt = $conn->prepare('SELECT order_id AS id, total_amount, customer_name, phone, address, payment_method, order_date FROM orders WHERE order_id = ? LIMIT 1');
        $oStmt->bind_param('i', $order_id);
        $oStmt->execute();
        $oRes = $oStmt->get_result();
        $orderDetails['order'] = $oRes->fetch_assoc();
        $oStmt->close();

        $iStmt = $conn->prepare('SELECT oi.product_id, oi.quantity, oi.price, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?');
        $iStmt->bind_param('i', $order_id);
        $iStmt->execute();
        $iRes = $iStmt->get_result();
        while ($it = $iRes->fetch_assoc()) $orderDetails['items'][] = $it;
        $iStmt->close();
    } else {
        $v->close();
        // invalid order or not owner
        $orderDetails = null;
    }
}

include 'header.php';
?>

<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <h3>My Orders</h3>

            <?php if (empty($orders)): ?>
                <div class="card mt-3">
                    <div class="card-body text-center">
                        <i class="bi bi-bag-x display-4 text-muted"></i>
                        <p class="mt-3">You have not placed any orders yet.</p>
                        <a href="index.php" class="btn btn-primary">Start Shopping</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="list-group mt-3">
                    <?php foreach ($orders as $ord): ?>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">Order #<?= htmlspecialchars($ord['id']) ?></div>
                                <div class="muted">Placed: <?= date('d M Y, H:i', strtotime($ord['order_date'])) ?> — <?= (int)$ord['item_count'] ?> items</div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold">₹<?= number_format($ord['total_amount'], 2) ?></div>
                                <div class="mt-2">
                                    <a href="orders.php?order_id=<?= (int)$ord['id'] ?>" class="btn btn-sm btn-outline-primary">View</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Account</h5>
                    <p class="mb-1">Logged in as <strong><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></strong></p>
                    <a href="profile.php" class="btn btn-sm btn-outline-secondary mt-2">My Profile</a>
                    <a href="index.php" class="btn btn-sm btn-primary mt-2">Continue Shopping</a>
                </div>
            </div>

            <?php if ($orderDetails): ?>
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Order #<?= (int)$orderDetails['order']['id'] ?></h5>
                        <div class="muted">Placed: <?= date('d M Y, H:i', strtotime($orderDetails['order']['order_date'])) ?></div>
                        <div class="mt-3">
                            <strong>Items</strong>
                            <ul class="list-group mt-2">
                                <?php foreach ($orderDetails['items'] as $it): ?>
                                    <li class="list-group-item d-flex align-items-center">
                                        <img src="images/<?= htmlspecialchars($it['image']) ?>" alt="" style="width:56px;height:56px;object-fit:cover;margin-right:10px;">
                                        <div class="flex-grow-1">
                                            <div><?= htmlspecialchars($it['name']) ?></div>
                                            <small class="text-muted">Qty: <?= (int)$it['quantity'] ?> — ₹<?= number_format($it['price'], 2) ?></small>
                                        </div>
                                        <div>₹<?= number_format($it['price'] * $it['quantity'], 2) ?></div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="mt-3 d-flex justify-content-between">
                                <div class="muted">Payment</div>
                                <div><?= htmlspecialchars($orderDetails['order']['payment_method']) ?></div>
                            </div>
                            <div class="mt-2 d-flex justify-content-between">
                                <div class="fw-bold">Total</div>
                                <div class="fw-bold">₹<?= number_format($orderDetails['order']['total_amount'], 2) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>