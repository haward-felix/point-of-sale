<?php
include 'db.php';

// Search functionality
$search = "";
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

// Fetch sales with pagination and search
$limit = 10; // Number of sales per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT sales.id, sales.user_id, sales.total, sales.created_at, users.username
        FROM sales
        LEFT JOIN users ON sales.user_id = users.id
        WHERE sales.created_at LIKE ? OR users.username LIKE ?
        ORDER BY sales.created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param("ssii", $search_param, $search_param, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Fetch total sales count for pagination
$total_result = $conn->query("SELECT COUNT(*) AS count FROM sales
                              LEFT JOIN users ON sales.user_id = users.id
                              WHERE sales.created_at LIKE '$search_param' OR users.username LIKE '$search_param'");
$total_rows = $total_result->fetch_assoc()['count'];
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Sales</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e3f2fd;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .header {
            background-color: #0277bd;
            color: white;
            padding: 1rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            width: 100%;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            padding: 1rem;
        }

        .main-content {
            padding: 2rem;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-container {
            margin-bottom: 2rem;
        }
        .form-container form input[type="text"] {
                width: calc(100% - 1.2rem); /* Adjust width considering padding and margins */
                max-width: 100%; /* Ensure it does not exceed the container's width */
            }

            .form-container form input[type="submit"] {
                width: 100%;
            }

        h1 {
            margin-bottom: 1rem;
            font-size: 1.5rem;
            border-bottom: 2px solid #0277bd;
            padding-bottom: 0.5rem;
            text-align: center;
            color: #0277bd;
        }

        .message {
            margin: 1rem 0;
            text-align: center;
            font-weight: bold;
            color: #d32f2f;
        }

        input[type="text"] {
            width: calc(100% - 2rem);
            padding: 0.5rem;
            margin: 0.5rem 0;
            border: 1px solid #90caf9;
            border-radius: 4px;
            font-size: 1rem;
        }

        input[type="submit"] {
            width: 100%;
            padding: 0.5rem;
            background-color: #0277bd;
            border: none;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        input[type="submit"]:hover {
            background-color: #01579b;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        table, th, td {
            border: 1px solid #90caf9;
        }

        th, td {
            padding: 0.75rem;
            text-align: left;
        }

        th {
            background-color: #0288d1;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f1f9fe;
        }

        .pagination {
            text-align: center;
            margin-top: 1rem;
        }

        .pagination a {
            text-decoration: none;
            color: #0277bd;
            padding: 0.5rem 1rem;
            border: 1px solid #90caf9;
            margin: 0 0.25rem;
            border-radius: 4px;
        }

        .pagination a:hover {
            background-color: #e1f5fe;
        }

        .pagination .current {
            background-color: #0277bd;
            color: white;
            border: 1px solid #0277bd;
        }

        .print-button {
            background-color: #0288d1;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }

        .print-button:hover {
            background-color: #01579b;
        }
    </style>
    <script>
        function printReceipt(saleId) {
            var printWindow = window.open('print_receipt.php?id=' + saleId, 'Print Receipt', 'width=800,height=600');
            printWindow.print();
        }
    </script>
</head>
<body>
    <div class="header">
        View Sales
    </div>
    <div class="container">
        <div class="main-content">
            <div class="form-container">
                <h1>Search Sales</h1>
                <form method="post">
                    <input type="text" name="search" placeholder="Search by username or date" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="submit" value="Search">
                </form>
            </div>

            <div class="form-container">
                <h1>Sales List</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Sale ID</th>
                            <th>User</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['total']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td>
                                    <button class="print-button" onclick="printReceipt(<?php echo htmlspecialchars($row['id']); ?>)">Print Receipt</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="pagination">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>">Previous</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" class="<?php echo $i == $page ? 'current' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>">Next</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
