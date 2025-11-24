<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = $_GET['order_id'] ?? 0;

// Fetch products from this order (safe prepared statement)
$sql = "SELECT oi.product_id, p.name, p.image 
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $order_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Write Reviews</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
    </style>
</head>

<body>

    <!-- Center everything vertically & horizontally -->
    <div class="d-flex flex-column justify-content-center align-items-center min-vh-100 w-100">

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
            <div class="alert alert-success alert-dismissible fade show w-75 mb-4" role="alert">
                ✅ Review submitted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <h2 class="mb-4 text-center">Write a Review for Your Purchased Products</h2>

        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="card shadow p-4 mb-4" style="width: 500px; max-width: 95%;">
                <div class="text-center mb-3">
                    <img src="images/<?= htmlspecialchars($row['image']) ?>" class="img-fluid rounded" style="max-height:150px;" alt="<?= htmlspecialchars($row['name']) ?>">
                </div>
                <h5 class="text-center mb-3"><?= htmlspecialchars($row['name']) ?></h5>

                <form method="POST" action="save_review.php" class="reviewForm">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($row['product_id']) ?>">
                    <input type="hidden" name="order_id" value="<?= htmlspecialchars($order_id) ?>">

                    <div class="mb-3">
                        <label for="rating<?= $row['product_id'] ?>" class="form-label">Rating:</label>
                        <select id="rating<?= $row['product_id'] ?>" name="rating" class="form-select" required>
                            <option value="">Select</option>
                            <option value="1">⭐</option>
                            <option value="2">⭐⭐</option>
                            <option value="3">⭐⭐⭐</option>
                            <option value="4">⭐⭐⭐⭐</option>
                            <option value="5">⭐⭐⭐⭐⭐</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="review<?= $row['product_id'] ?>" class="form-label">Review:</label>
                        <textarea id="review<?= $row['product_id'] ?>" name="review_text" class="form-control" rows="3" required></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Submit Review</button>
                </form>
            </div>
        <?php endwhile; ?>

    </div>

    <!-- jQuery + jQuery Validation (Load First) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
            // Apply validation to each review form
            $(".reviewForm").each(function() {
                $(this).validate({
                    rules: {
                        rating: {
                            required: true
                        },
                        review_text: {
                            required: true,
                            minlength: 10
                        }
                    },
                    messages: {
                        rating: {
                            required: "Please select a rating"
                        },
                        review_text: {
                            required: "Please write a review",
                            minlength: "Review must be at least 10 characters long"
                        }
                    },
                    errorClass: "text-danger",
                    errorElement: "div",
                    highlight: function(element) {
                        $(element).addClass("is-invalid");
                    },
                    unhighlight: function(element) {
                        $(element).removeClass("is-invalid");
                    }
                });
            });
        });
    </script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>