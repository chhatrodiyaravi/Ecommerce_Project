<?php
session_start();
include 'db_connect.php';

// Handle search filters
$where = "1";

// Check if user typed in search box
if (!empty($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    // Match company name OR product name
    $where .= " AND (company LIKE '%$search%' OR name LIKE '%$search%')";
}

// Fetch products
$sql = "SELECT * FROM products WHERE $where";
$result = $conn->query($sql);
include 'header.php';
?>
<!-- css -->
<link rel="stylesheet" href="style.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>




<!-- Carousel Start -->
<div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
    </div>
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="images/carousel_1.jpg" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
            <img src="images/carousel_2.jpg" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
            <img src="images/carousel_4.jpg" class="d-block w-100" alt="...">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<!-- Carousel End -->


<!-- Product Listing -->
<div class="container mt-5">
    <div class="row">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <img src="images/<?= htmlspecialchars($row['image']) ?>"
                            class="card-img-top"
                            alt="<?= htmlspecialchars($row['name']) ?>"
                            style="height:250px; object-fit:contain;">

                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['name']) ?></h5>
                            <p class="text-muted"><?= htmlspecialchars($row['company']) ?></p>
                            <p><?= htmlspecialchars($row['description']) ?></p>

                            <!-- Price + Discount -->
                            <?php if (!empty($row['discount_price']) && $row['discount_price'] > 0 && $row['discount_price'] < $row['price']): ?>
                                <?php
                                $discount_percent = round((($row['price'] - $row['discount_price']) / $row['price']) * 100);
                                ?>
                                <h6 class="text-danger">
                                    ₹<?= number_format($row['discount_price'], 2) ?>
                                    <small class="text-muted">
                                        <del>₹<?= number_format($row['price'], 2) ?></del>
                                    </small>
                                    <span class="text-success">
                                        <?= $discount_percent ?>% off
                                    </span>
                                </h6>
                            <?php else: ?>
                                <h6 class="text-success">₹<?= number_format($row['price'], 2) ?></h6>
                            <?php endif; ?>


                            <!-- Ratings -->
                            <p>
                                <?php
                                $rating = round($row['rating']);
                                for ($i = 1; $i <= 5; $i++):
                                    if ($i <= $rating): ?>
                                        ⭐
                                    <?php else: ?>
                                        ☆
                                <?php endif;
                                endfor;
                                ?>
                                <small>(<?= $row['total_reviews'] ?> reviews)</small>
                            </p>

                            <!-- ✅ View Details button -->
                            <a href="product_detail.php?id=<?= $row['id'] ?>" class=" text-decoration-none mt-2">View Details</a>
                            <!-- Add to cart -->

                            <?php if (isset($_SESSION['user_id'])): ?>
                                <!-- If logged in → allow Add to Cart -->
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="product_name" value="<?= htmlspecialchars($row['name']) ?>">
                                    <input type="hidden" name="product_price"
                                        value="<?= !empty($row['discount_price']) ? $row['discount_price'] : $row['price'] ?>">
                                    <input type="hidden" name="product_qty" value="1">
                                    <button type="submit" class="btn btn-primary mt-2">Add to Cart</button>
                                </form>
                            <?php else: ?>
                                <!-- If not logged in → redirect to login -->
                                <a href="login.php" class="btn btn-warning mt-2">Add to Cart</a>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning">No products found.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include 'footer.php';
?>