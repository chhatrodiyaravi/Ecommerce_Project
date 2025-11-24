<?php
include 'db_connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $order_id = $_POST['order_id'];
    $rating = $_POST['rating'];
    $review_text = $_POST['review_text'];

    $sql = "INSERT INTO reviews (user_id, product_id, order_id, rating, review_text, created_at) 
            VALUES ('$user_id', '$product_id', '$order_id', '$rating', '$review_text', NOW())";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?id=$product_id&msg=Review added");
        exit();
    } else {
        echo "❌ Error: " . mysqli_error($conn);
    }
}
