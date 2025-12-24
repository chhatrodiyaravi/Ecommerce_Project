<?php
session_start();
include 'header.php';

// Build cart summary
$cart = $_SESSION['cart'] ?? [];
$subtotal = 0.0;
foreach ($cart as $item) {
    $subtotal += ($item['price'] ?? 0) * ($item['qty'] ?? 1);
}

// Simple shipping and tax rules (adjust as needed)
$shipping = $subtotal > 0 ? 50.00 : 0.00;
$tax = round($subtotal * 0.05, 2); // 5% tax
$total = round($subtotal + $shipping + $tax, 2);
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Shipping & Billing</h5>
                    <form id="checkoutForm" action="checkout.php" method="POST">
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" name="phone" id="phone" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Full Address</label>
                            <textarea name="address" id="address" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" id="payment_method" class="form-select" required>
                                <option value="">Choose...</option>
                                <option value="COD">Cash on Delivery</option>
                                <option value="Online">Online Payment</option>
                            </select>
                        </div>

                        <input type="hidden" name="total_amount" value="<?php echo htmlspecialchars($total); ?>">
                        <button class="btn btn-primary">Place Order</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Order Summary</h5>
                    <?php if (empty($cart)): ?>
                        <div class="alert alert-warning">Your cart is empty.</div>
                    <?php else: ?>
                        <ul class="list-group mb-3">
                            <?php foreach ($cart as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <div><strong><?php echo htmlspecialchars($item['name'] ?? $item['product_name'] ?? 'Product'); ?></strong></div>
                                        <small class="text-muted">Qty: <?php echo (int)($item['qty'] ?? 1); ?></small>
                                    </div>
                                    <span>₹<?php echo number_format((($item['price'] ?? 0) * ($item['qty'] ?? 1)), 2); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="d-flex justify-content-between">
                            <div>Subtotal</div>
                            <div>₹<?php echo number_format($subtotal, 2); ?></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>Shipping</div>
                            <div>₹<?php echo number_format($shipping, 2); ?></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <div>Tax (5%)</div>
                            <div>₹<?php echo number_format($tax, 2); ?></div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <div>Total</div>
                            <div>₹<?php echo number_format($total, 2); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- client-side validation -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>
<script>
    $(function() {
        $('#checkoutForm').validate({
            rules: {
                customer_name: {
                    required: true,
                    minlength: 3
                },
                phone: {
                    required: true,
                    digits: true,
                    minlength: 10
                },
                address: {
                    required: true,
                    minlength: 10
                },
                payment_method: {
                    required: true
                }
            },
            errorClass: 'text-danger',
            errorElement: 'div',
            errorPlacement: function(err, el) {
                err.insertAfter(el);
            }
        });
    });
</script>

<?php include 'footer.php'; ?>