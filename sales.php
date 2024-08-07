<?php
include 'db.php';

// Start the session to manage the cart
session_start();

// Fetch products to display in the sales interface
$sql_products = "SELECT * FROM products";
$result_products = $conn->query($sql_products);

// Handle adding products to the cart
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch product details
    $sql_product = "SELECT * FROM products WHERE id = ?";
    $stmt_product = $conn->prepare($sql_product);
    $stmt_product->bind_param("i", $product_id);
    $stmt_product->execute();
    $product = $stmt_product->get_result()->fetch_assoc();

    // Add to cart
    $_SESSION['cart'][] = [
        'product_id' => $product_id,
        'name' => $product['name'],
        'price' => $product['price'],
        'quantity' => $quantity
    ];

    header("Location: sales.php");
    exit();
}

// Handle processing the sale
if (isset($_POST['process_sale'])) {
    $discount = $_POST['discount'];
    $total_price = 0;

    // Calculate total price
    foreach ($_SESSION['cart'] as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }

    // Apply discount
    $total_price -= ($total_price * $discount / 100);

    // Update inventory and clear cart
    foreach ($_SESSION['cart'] as $item) {
        $product_id = $item['product_id'];
        $quantity = $item['quantity'];

        // Update inventory
        $sql_update = "UPDATE products SET stock = stock - ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("ii", $quantity, $product_id);
        $stmt_update->execute();
    }

    // Clear the cart
    unset($_SESSION['cart']);

    echo "Sale processed successfully. Total price: $" . number_format($total_price, 2);
}
?>

<body>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
        }

        .container {
            margin: 2rem auto;
            max-width: 1000px;
            padding: 1rem;
        }

        .sales-form, .cart-summary {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        h1, h2 {
            text-align: center;
            margin-bottom: 1rem;
        }

        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 0.5rem;
            margin: 0.5rem 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1rem;
        }

        input[type="submit"] {
            width: 100%;
            padding: 0.5rem;
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        input[type="submit"]:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 0.5rem;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .cart-summary {
            margin-top: 1rem;
        }
    </style>

    <div class="container">
        <div class="sales-form">
            <h1>Sales Interface</h1>
            <form method="post">
                <h2>Add Product to Cart</h2>
                Product:
                <select name="product_id">
                    <?php while ($row = $result_products->fetch_assoc()) { ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?> - $<?php echo $row['price']; ?></option>
                    <?php } ?>
                </select><br>
                Quantity: <input type="number" name="quantity" min="1" required><br>
                <input type="submit" name="add_to_cart" value="Add to Cart">
            </form>
        </div>

        <div class="cart-summary">
            <h2>Cart Summary</h2>
            <form method="post">
                <table>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                    <?php
                    $total = 0;
                    if (isset($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $item) {
                            $item_total = $item['price'] * $item['quantity'];
                            $total += $item_total;
                            ?>
                            <tr>
                                <td><?php echo $item['name']; ?></td>
                                <td>$<?php echo number_format($item['price'], 2); ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td>$<?php echo number_format($item_total, 2); ?></td>
                            </tr>
                        <?php }
                    } ?>
                    <tr>
                        <td colspan="3" style="text-align: right;"><strong>Total Price:</strong></td>
                        <td>$<?php echo number_format($total, 2); ?></td>
                    </tr>
                </table>
                <br>
                Discount (%): <input type="number" name="discount" min="0" max="100" value="0"><br>
                <input type="submit" name="process_sale" value="Process Sale">
            </form>
        </div>
    </div>
</body>
