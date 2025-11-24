<?php
include 'db_connect.php';
session_start();

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout_form.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $customer_name = $_POST['customer_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $total_amount = $_POST['total_amount'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';

    if ($customer_name && $phone && $address && $total_amount && $payment_method) {
        // Insert into orders with logged-in user_id
        $user_id = $_SESSION['user_id'];
        $sql = "INSERT INTO orders (user_id, customer_name, phone, address, total_amount, payment_method, order_date) 
                VALUES ('$user_id', '$customer_name', '$phone', '$address', '$total_amount', '$payment_method', NOW())";

        if (mysqli_query($conn, $sql)) {
            $order_id = mysqli_insert_id($conn);

            // Save order items
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $product_id = $item['id'];
                    $quantity = $item['qty'];
                    $price = $item['price'];

                    mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) 
                                          VALUES ('$order_id', '$product_id', '$quantity', '$price')");
                }
            }

            // Clear cart
            unset($_SESSION['cart']);

            // ✅ Redirect to review page with success message
            header("Location: review.php?order_id=$order_id&msg=success");
            exit();
        } else {
            echo "❌ Error: " . mysqli_error($conn);
        }
    } else {
        echo "❌ Missing required fields!";
    }
} else {
    header("Location: checkout_form.php");
    exit;
}
?>

