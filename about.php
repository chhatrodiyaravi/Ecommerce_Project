<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Camera Store</title>
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> -->
</head>

<body>
    <?php include 'header.php'; ?>

    <!-- About Us Content -->
    <div class="container my-5">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4">
                <img src="images/canon2.webp" class="img-fluid rounded shadow" alt="Camera Store">
            </div>
            <div class="col-lg-6">
                <h1 class="mb-4">About Camera Store</h1>
                <p class="lead">
                    Welcome to <strong>Camera Store</strong> — your one-stop destination for premium cameras, lenses, and accessories.
                    Our mission is to bring you the latest photography technology at unbeatable prices.
                </p>
                <p>
                    Since our inception in 2020, we have been committed to serving photography enthusiasts and professionals
                    with top-notch products, expert advice, and exceptional customer service.
                </p>
                <ul>
                    <li>Wide range of DSLR, Mirrorless, and Action Cameras</li>
                    <li>Trusted brands like Canon, Nikon, Sony, Fujifilm, and more</li>
                    <li>Fast and reliable shipping across India</li>
                    <li>Easy returns and warranty support</li>
                </ul>
                <a href="index.php" class="btn btn-warning mt-3">Shop Now</a>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>


    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
</body>

</html>