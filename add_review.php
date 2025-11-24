<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $user_name  = $_POST['user_name'];
    $rating     = $_POST['rating'];
    $review     = $_POST['review'];

    $stmt = $conn->prepare("INSERT INTO reviews (product_id, user_name, rating, review_text) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $product_id, $user_name, $rating, $review);
    $stmt->execute();

    // Update avg rating
    $avg = $conn->query("SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews FROM reviews WHERE product_id=$product_id")->fetch_assoc();
    $conn->query("UPDATE products SET rating=" . $avg['avg_rating'] . ", total_reviews=" . $avg['total_reviews'] . " WHERE id=$product_id");

    header("Location: product_detail.php?id=$product_id");
    exit;
}
