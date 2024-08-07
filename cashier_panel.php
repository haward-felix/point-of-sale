<?php
include 'db.php';
session_start();

// Check if user is logged in and has the role of 'cashier'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'cashier') {
    header('Location: login.php');
    exit();
}

// Initialize the cart in the session if not already set
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Fetch products for the cashier to select
$productQuery = "SELECT * FROM products";
$products = $conn->query($productQuery);

// Get the logged-in cashier's user ID
$user_id = $_SESSION['user_id'];

// Fetch sales history for the cashier with username instead of user_id
$salesQuery = "
    SELECT 
        sales.id AS sale_id, 
        users.username AS sold_by, 
        sales.total, 
        sales.created_at 
    FROM 
        sales 
    JOIN 
        users 
    ON 
        sales.user_id = users.id 
    WHERE 
        sales.user_id = ? 
    ORDER BY 
        sales.created_at DESC 
    LIMIT 10
";
$stmt = $conn->prepare($salesQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$sales = $stmt->get_result();

// Handle adding items to the cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];

    // Fetch product details
    $productQuery = "SELECT name, price FROM products WHERE id = ?";
    $stmt = $conn->prepare($productQuery);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    
    if ($product) {
        // Add product to cart
        $item = [
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'total' => $product['price'] * $quantity
        ];
        $_SESSION['cart'][] = $item;
    }
}

// Handle deleting items from the cart
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_item'])) {
    $item_index = $_POST['item_index'];
    if (isset($_SESSION['cart'][$item_index])) {
        unset($_SESSION['cart'][$item_index]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // Reindex array
    }
}

// Handle finalizing sale
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['print_receipt'])) {
    // Calculate total amount
    $total_amount = array_sum(array_column($_SESSION['cart'], 'total'));

    // Insert sale into sales table
    $insertSale = "INSERT INTO sales (user_id, total, created_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($insertSale);
    $stmt->bind_param("id", $user_id, $total_amount);
    $stmt->execute();
    $sale_id = $stmt->insert_id;

    // Insert items into sales_items table
    $insertSaleItem = "INSERT INTO sales_items (sale_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSaleItem);
    foreach ($_SESSION['cart'] as $item) {
        $stmt->bind_param("iiid", $sale_id, $item['id'], $item['quantity'], $item['price']);
        $stmt->execute();
    }

    // Update product stock
    $updateStock = "UPDATE products SET stock = stock - ? WHERE id = ?";
    $stmt = $conn->prepare($updateStock);
    foreach ($_SESSION['cart'] as $item) {
        $stmt->bind_param("ii", $item['quantity'], $item['id']);
        $stmt->execute();
    }

    // Clear the cart
    $_SESSION['cart'] = [];

    // Redirect to receipt page
    header("Location: print_receipt.php?id=$sale_id");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 20px;
            text-align: center;
        }
        .container {
            width: 80%;
            margin: 20px auto;
        }
        .form-container, .cart-container, .sales-container {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .form-container h2, .cart-container h2, .sales-container h2 {
            margin-top: 0;
            font-size: 1.25rem;
            color: #333;
        }

        .form-container input, .form-container select {
            width: calc(50% - 1rem);
            padding: 0.5rem;
            margin: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            display: inline-block;
        }

        .form-container input[type="submit"] {
            width: calc(100% - 2rem);
            background-color: #007bff;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1rem;
        }

        .form-container input[type="submit"]:hover {
            background-color: #0056b3;
        }
        h2 {
            margin-top: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .button, .print-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }
        .print-button {
            background-color: #007bff;
        }
        .button:hover, .print-button:hover {
            background-color: #218838;
            color: white;
        }
        .print-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1 class="header-title">Cashier Panel</h1>
    </header>

    <div class="container">
        <!-- Product Selection and Sale Form -->
        <div class="form-container">
            <h2>Add Item to Cart</h2>
            <form method="post" action="cashier_panel.php">
                Product: 
                <select name="product_id" required>
                    <?php while ($row = $products->fetch_assoc()): ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?> - $<?php echo htmlspecialchars($row['price']); ?></option>
                    <?php endwhile; ?>
                </select><br>
                Quantity: <input type="number" name="quantity" min="1" required><br>
                <input type="submit" name="add_to_cart" value="Add to Cart" class="button">
            </form>
        </div>

        <!-- Cart -->
        <div class="cart-container">
            <h2>Cart</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_amount = 0;
                    foreach ($_SESSION['cart'] as $index => $item) {
                        $total_amount += $item['total'];
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($item['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($item['quantity']) . "</td>";
                        echo "<td>$" . htmlspecialchars($item['price']) . "</td>";
                        echo "<td>$" . htmlspecialchars($item['total']) . "</td>";
                        echo "<td>
                                <form method='post' action='cashier_panel.php'>
                                    <input type='hidden' name='item_index' value='$index'>
                                    <input type='submit' name='delete_item' value='Remove' class='button'>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <p><strong>Total Amount:</strong> $<?php echo htmlspecialchars($total_amount); ?></p>
            <form method="post" action="cashier_panel.php">
                <input type="submit" name="print_receipt" value="Print Receipt" class="print-button">
            </form>
        </div>

        <!-- Sales History -->
        <div class="sales-container">
            <h2>Sales History</h2>
            <table>
                <thead>
                    <tr>
                        <th>Sale ID</th>
                        <th>Sold By</th>
                        <th>Total</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($sale = $sales->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($sale['sale_id']); ?></td>
                            <td><?php echo htmlspecialchars($sale['sold_by']); ?></td>
                            <td>$<?php echo htmlspecialchars($sale['total']); ?></td>
                            <td><?php echo htmlspecialchars($sale['created_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Navigation Links -->
        <a href="logout.php" class="button">Logout</a>
    </div>
</body>
</html>
