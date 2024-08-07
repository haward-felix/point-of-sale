<?php
include 'db.php';
session_start();

// Check if user is logged in and has the role of 'admin'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch sales data for chart generation
$salesQuery = "SELECT DATE(created_at) as date, SUM(total) as total FROM sales GROUP BY DATE(created_at)";
$salesResult = $conn->query($salesQuery);

// Fetch inventory data for chart generation
$inventoryQuery = "SELECT name, stock FROM products";
$inventoryResult = $conn->query($inventoryQuery);

// Prepare data for charts
$salesData = [];
$inventoryData = [];

while ($row = $salesResult->fetch_assoc()) {
    $salesData['dates'][] = $row['date'];
    $salesData['totals'][] = $row['total'];
}

while ($row = $inventoryResult->fetch_assoc()) {
    $inventoryData['names'][] = $row['name'];
    $inventoryData['stocks'][] = $row['stock'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System Reports</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        /* Inline CSS styles for this specific file */
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
        .chart-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        h2 {
            margin-top: 0;
        }
        canvas {
            width: 100%;
            height: 400px;
        }
        .button {
            background-color: #007bff;
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
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>POS System Reports</h1>
    </header>

    <div class="container">
        <!-- Sales Chart -->
        <div class="chart-container">
            <h2>Sales Overview</h2>
            <canvas id="salesChart"></canvas>
        </div>

        <!-- Inventory Chart -->
        <div class="chart-container">
            <h2>Inventory Levels</h2>
            <canvas id="inventoryChart"></canvas>
        </div>

        <!-- Navigation Links -->
        <a href="admin_panel.php" class="button">Go Back To Admin Panel</a>
    </div>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Sales Chart
        var ctxSales = document.getElementById('salesChart').getContext('2d');
        var salesChart = new Chart(ctxSales, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($salesData['dates']); ?>,
                datasets: [{
                    label: 'Daily Sales Total',
                    data: <?php echo json_encode($salesData['totals']); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Total Sales ($)'
                        }
                    }
                }
            }
        });

        // Inventory Chart
        var ctxInventory = document.getElementById('inventoryChart').getContext('2d');
        var inventoryChart = new Chart(ctxInventory, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($inventoryData['names']); ?>,
                datasets: [{
                    label: 'Stock Level',
                    data: <?php echo json_encode($inventoryData['stocks']); ?>,
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Product'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Stock Level'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
