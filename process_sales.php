<?php
include 'db.php';
session_start();

// Check if user is logged in and has the role of 'cashier'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'cashier') {
    header('Location: login.php');
    exit();
}

// Handle form submission for adding items to the cart
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $user_id = $_SESSION['user_id']; // Ensure the user ID is available in the session

    // Fetch product price
    $productQuery = "SELECT price FROM products WHERE id = ?";
    $stmt = $conn->prepare($productQuery);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $price = $product['price'];

    // Calculate total price
    $total = $price * $quantity;

    // Insert sale into sales table
    $insertSale = "INSERT INTO sales (user_id, total, created_at) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($insertSale);
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();
    $sale_id = $stmt->insert_id;

    // Insert items into sales_items table
    $insertSaleItem = "INSERT INTO sales_items (sale_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSaleItem);
    $stmt->bind_param("iiid", $sale_id, $product_id, $quantity, $price);
    $stmt->execute();

    // Update product stock
    $updateStock = "UPDATE products SET stock = stock - ? WHERE id = ?";
    $stmt = $conn->prepare($updateStock);
    $stmt->bind_param("ii", $quantity, $product_id);
    $stmt->execute();

    // Display success message and receipt
    echo "<div class='receipt'>";
    echo "<h1>Receipt</h1>";
    echo "<p><strong>Sale ID:</strong> " . htmlspecialchars($sale_id) . "</p>";
    echo "<p><strong>Total Amount:</strong> $" . htmlspecialchars($total) . "</p>";
    echo "<p><strong>Date:</strong> " . date('Y-m-d H:i:s') . "</p>";
    echo "<a href='cashier_panel.php' class='button'>Back to Cashier Panel</a>";
    echo "</div>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Sale</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 600px;
            padding: 2rem;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 1rem;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="number"],
        select {
            width: 100%;
            padding: 0.5rem;
            margin: 0.5rem 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
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

        .receipt {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .receipt p {
            font-size: 1.2rem;
            margin: 0.5rem 0;
        }

        .button {
            display: inline-block;
            padding: 0.5rem 1rem;
            margin: 1rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 1rem;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Process Sale</h1>
        <form method="post">
            Product:
            <select name="product_id" required>
                <?php
                // Fetch products for dropdown
                $productQuery = "SELECT * FROM products";
                $products = $conn->query($productQuery);
                while ($row = $products->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['name']) . " - $" . htmlspecialchars($row['price']) . "</option>";
                }
                ?>
            </select>
            Quantity: <input type="number" name="quantity" min="1" required>
            <input type="submit" value="Add to Cart">
        </form>
    </div>
</body>
</html>
