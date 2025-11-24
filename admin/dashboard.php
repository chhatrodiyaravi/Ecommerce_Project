<?php include("header.php"); ?>
<?php include("sidebar.php"); ?>

<!-- Main Content -->
<div class="content mt-5">
    <div class="container-fluid">
        <h1 class="mb-4">Welcome, <?= $_SESSION['admin_username']; ?> 🎉</h1>
        <div class="row">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Orders</h5>
                        <p class="card-text">Manage customer orders.</p>
                        <a href="view_orders.php" class="btn btn-primary">View Orders</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Products</h5>
                        <p class="card-text">Add, edit, or delete products.</p>
                        <a href="add_product.php" class="btn btn-success">Manage Products</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Reports</h5>
                        <p class="card-text">View sales & stock reports.</p>
                        <a href="view_Report.php" class="btn btn-warning">View Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("footer.php"); ?>