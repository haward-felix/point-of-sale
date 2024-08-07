<?php
include 'db.php';

if (isset($_GET['id'])) {
    $saleId = $_GET['id'];

    // Fetch sale details
    $sql = "SELECT sales.id, sales.user_id, sales.total, sales.created_at, users.username
            FROM sales
            LEFT JOIN users ON sales.user_id = users.id
            WHERE sales.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $saleId);
    $stmt->execute();
    $sale = $stmt->get_result()->fetch_assoc();

    // Fetch sale items
    $sql = "SELECT products.name, sales_items.quantity, sales_items.price
            FROM sales_items
            LEFT JOIN products ON sales_items.product_id = products.id
            WHERE sales_items.sale_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $saleId);
    $stmt->execute();
    $items = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }

        .receipt {
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .receipt h1 {
            text-align: center;
            color: #28a745;
        }

        .receipt .details {
            margin-bottom: 20px;
        }

        .receipt .details div {
            margin-bottom: 10px;
        }

        .receipt table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .receipt table, th, td {
            border: 1px solid #ddd;
        }

        .receipt th, td {
            padding: 8px;
            text-align: left;
        }

        .receipt th {
            background-color: #28a745;
            color: white;
        }

        .receipt .total {
            text-align: right;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <h1>Receipt</h1>
        <div class="details">
            <div><strong>Sale ID:</strong> <?php echo htmlspecialchars($sale['id']); ?></div>
            <div><strong>Cashier:</strong> <?php echo htmlspecialchars($sale['username']); ?></div>
            <div><strong>Date:</strong> <?php echo htmlspecialchars($sale['created_at']); ?></div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $items->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($item['price']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div class="total">
            <strong>Total:</strong> <?php echo htmlspecialchars($sale['total']); ?>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
