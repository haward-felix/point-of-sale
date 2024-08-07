<?php
include 'db.php';

// Set a low stock threshold
$low_stock_threshold = 10;

// Fetch products and stock levels
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// Handle restocking orders
if (isset($_POST['restock'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Update inventory
    $sql_restock = "UPDATE products SET stock = stock + ? WHERE id = ?";
    $stmt_restock = $conn->prepare($sql_restock);
    $stmt_restock->bind_param("ii", $quantity, $product_id);
    $stmt_restock->execute();

    echo "Restocking order placed successfully.";
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

        .inventory-form, .inventory-table {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
        }

        h1 {
            text-align: center;
            margin-bottom: 1rem;
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

        .low-stock {
            background-color: #f8d7da;
            color: #721c24;
            font-weight: bold;
        }

        input[type="number"] {
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
    </style>

    <div class="container">
        <div class="inventory-table">
            <h1>Inventory Management</h1>
            <table>
                <tr>
                    <th>Product ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr class="<?php echo $row['stock'] < $low_stock_threshold ? 'low-stock' : ''; ?>">
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td>$<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo $row['stock']; ?></td>
                        <td>
                            <?php if ($row['stock'] < $low_stock_threshold) { ?>
                                <a href="javascript:void(0);" onclick="showRestockForm(<?php echo $row['id']; ?>)">Restock</a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>

        <div class="inventory-form" id="restock-form" style="display: none;">
            <h2>Restock Product</h2>
            <form method="post">
                Product ID: <input type="number" name="product_id" id="product_id" readonly><br>
                Quantity: <input type="number" name="quantity" required><br>
                <input type="submit" name="restock" value="Place Restocking Order">
            </form>
        </div>
    </div>

    <script>
        function showRestockForm(productId) {
            document.getElementById('product_id').value = productId;
            document.getElementById('restock-form').style.display = 'block';
        }
    </script>
</body>
