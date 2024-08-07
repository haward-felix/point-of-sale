<?php
include 'db.php';

// Fetch metrics for the dashboard
$metrics = [
    'total_products' => $conn->query("SELECT COUNT(*) AS count FROM products")->fetch_assoc()['count'],
    'total_categories' => $conn->query("SELECT COUNT(*) AS count FROM categories")->fetch_assoc()['count'],
    'total_sales' => $conn->query("SELECT COUNT(*) AS count FROM sales")->fetch_assoc()['count'],
    'total_users' => $conn->query("SELECT COUNT(*) AS count FROM users")->fetch_assoc()['count'],
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

        .header {
            background-color: #007bff;
            color: white;
            padding: 1rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .container {
            display: flex;
            flex: 1;
        }

        .sidebar {
            background-color: #343a40;
            color: white;
            width: 250px;
            padding: 1rem;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            height: calc(100vh - 60px);
            position: fixed;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 0.5rem;
            margin: 0.5rem 0;
            border-radius: 4px;
            font-size: 1rem;
        }

        .sidebar a:hover {
            background-color: #495057;
        }

        .main-content {
            margin-left: 280px;
            padding: 2rem;
            flex: 1;
        }

        .section {
            margin-bottom: 2rem;
        }

        .section h2 {
            margin-bottom: 1rem;
            font-size: 1.5rem;
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
        }

        .metrics {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .metric-card {
            background-color: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: calc(50% - 1rem);
            margin-bottom: 1rem;
            text-align: center;
        }

        .metric-card h3 {
            margin: 0.5rem 0;
            font-size: 1.25rem;
        }

        .metric-card p {
            margin: 0;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .link-buttons a {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            margin: 0.5rem;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            background-color: #007bff;
            font-size: 1rem;
            text-align: center;
        }

        .link-buttons a:hover {
            background-color: #0056b3;
        }

        .link-buttons a.btn-danger {
            background-color: #dc3545;
        }

        .link-buttons a.btn-danger:hover {
            background-color: #c82333;
        }

        .link-buttons a.btn-warning {
            background-color: #ffc107;
            color: black;
        }

        .link-buttons a.btn-warning:hover {
            background-color: #e0a800;
        }
    </style>
</head>
<body>
    <div class="header">
        Admin Panel
    </div>
    <div class="container">
        <div class="sidebar">
            <a href="dashboard.php">Dashboard</a>
            <a href="add_product.php">Add Product</a>
            <a href="add_category.php">Add Category</a>
            <a href="manage_products.php">Manage Products</a>
            <a href="manage_categories.php">Manage Categories</a>
            <a href="view_sales.php">View Sales</a>
            <a href="manage_users.php">Manage Users</a>
            <a href="sales_report.php" class="btn-warning">Generate Reports</a>
        </div>
        <div class="main-content">
            <div class="section">
                <h2>Dashboard Overview</h2>
                <div class="metrics">
                    <div class="metric-card">
                        <h3>Total Products</h3>
                        <p><?php echo htmlspecialchars($metrics['total_products']); ?></p>
                    </div>
                    <div class="metric-card">
                        <h3>Total Categories</h3>
                        <p><?php echo htmlspecialchars($metrics['total_categories']); ?></p>
                    </div>
                    <div class="metric-card">
                        <h3>Total Sales</h3>
                        <p><?php echo htmlspecialchars($metrics['total_sales']); ?></p>
                    </div>
                    <div class="metric-card">
                        <h3>Total Users</h3>
                        <p><?php echo htmlspecialchars($metrics['total_users']); ?></p>
                    </div>
                </div>
            </div>
            <div class="section">
                <h2>Actions</h2>
                <div class="link-buttons">
                    <a href="add_product.php">Add Product</a>
                    <a href="add_category.php">Add Category</a>
                    <a href="manage_products.php">Manage Products</a>
                    <a href="manage_categories.php">Manage Categories</a>
                    <a href="view_sales.php">View Sales</a>
                    <a href="manage_users.php">Manage Users</a>
                    <a href="sales_report.php" class="btn-warning">Generate Reports</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
