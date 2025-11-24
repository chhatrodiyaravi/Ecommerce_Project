<!-- order_success.php content goes here -->
<?php
session_start();

// Calculate total from cart
$total = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['qty'];
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f9fa;
        }

        .checkout-container {
            width: 400px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            box-shadow: 0px 0px 10px #ccc;
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        input,
        textarea,
        select,
        button {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background: #28a745;
            color: white;
            font-size: 16px;
            cursor: pointer;
            border: none;
        }

        button:hover {
            background: #218838;
        }

        .total {
            font-weight: bold;
            margin: 10px 0;
            text-align: right;
        }

        /* error messages */
        .text-danger {
            font-size: 0.85rem;
            color: #dc3545 !important;
            /* force red */
            margin-top: 5px;
            display: block;
            /* ensures line below input */
        }

        /* highlight invalid fields */
        .is-invalid {
            border-color: #dc3545 !important;
            /* red border */
            box-shadow: none !important;
        }

        /* optional: valid fields */
        .is-valid {
            border-color: #28a745 !important;
        }
    </style>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- jQuery Validation Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.min.js"></script>

</head>

<body>

    <div class="checkout-container">
        <h2>Checkout</h2>
        <p class="total">Total Amount: ₹<?php echo number_format($total, 2); ?></p>

        <form action="checkout.php" method="POST" id="checkoutForm">
            <input type="text" name="customer_name" id="customer_name" placeholder="Full Name">
            <input type="text" name="phone" id="phone" placeholder="Phone">
            <textarea name="address" id="address" placeholder="Full Address"></textarea>

            <input type="hidden" name="total_amount" value="<?php echo $total; ?>">

            <select name="payment_method" id="payment_method">
                <option value="">Select Payment Method</option>
                <option value="COD">Cash on Delivery</option>
                <option value="Online">Online Payment</option>
            </select>

            <button type="submit">Place Order</button>
        </form>

    </div>

    <script>
        $(document).ready(function() {
            $("#checkoutForm").validate({
                rules: {
                    customer_name: {
                        required: true,
                        minlength: 3
                    },
                    phone: {
                        required: true,
                        digits: true,
                        minlength: 10,
                        maxlength: 10
                    },
                    address: {
                        required: true,
                        minlength: 10
                    },
                    payment_method: {
                        required: true
                    }
                },
                messages: {
                    customer_name: {
                        required: " Please enter your full name",
                        minlength: "Name must be at least 3 characters long"
                    },
                    phone: {
                        required: " Please enter your phone number",
                        digits: "Only numbers allowed",
                        minlength: "Phone must be 10 digits",
                        maxlength: "Phone must be 10 digits"
                    },
                    address: {
                        required: " Please enter your address",
                        minlength: "Address should be at least 10 characters long"
                    },
                    payment_method: {
                        required: " Please select a payment method"
                    }
                },
                errorElement: "div",
                errorClass: "text-danger",
                // place the error after the element
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                highlight: function(element) {
                    $(element).addClass("is-invalid").removeClass("is-valid");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid").addClass("is-valid");
                }
            });
        });
    </script>



</body>

</html>