<?php
include 'db.php';

// Handle deletion
if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "Product deleted successfully";
    } else {
        $message = "Error: " . $stmt->error;
    }
}

// Search functionality
$search = "";
if (isset($_POST['search'])) {
    $search = $_POST['search'];
}

// Fetch products with pagination and search
$limit = 10; // Number of products per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$sql = "SELECT * FROM products WHERE name LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$search_param = "%$search%";
$stmt->bind_param("sii", $search_param, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Fetch total product count for pagination
$total_result = $conn->query("SELECT COUNT(*) AS count FROM products WHERE name LIKE '$search_param'");
$total_rows = $total_result->fetch_assoc()['count'];
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        .header {
            background-color: #007bff;
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
            background-color: #fff;
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
            border-bottom: 2px solid #007bff;
            padding-bottom: 0.5rem;
            text-align: center;
        }

        .message {
            margin: 1rem 0;
            text-align: center;
            font-weight: bold;
            color: #dc3545;
        }

        input[type="text"] {
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
            margin: 1rem 0;
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 0.75rem;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .pagination {
            text-align: center;
            margin-top: 1rem;
        }

        .pagination a {
            text-decoration: none;
            color: #007bff;
            padding: 0.5rem 1rem;
            border: 1px solid #ccc;
            margin: 0 0.25rem;
            border-radius: 4px;
        }

        .pagination a:hover {
            background-color: #f0f0f0;
        }

        .pagination .current {
            background-color: #007bff;
            color: white;
            border: 1px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="header">
        Manage Products
    </div>
    <div class="container">
        <div class="main-content">
            <div class="form-container">
                <h1>Search Products</h1>
                <form method="post">
                    <input type="text" name="search" placeholder="Search by product name" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="submit" value="Search">
                </form>
                <?php if (isset($message)): ?>
                    <div class="message"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>
            </div>

            <div class="form-container">
                <h1>Product List</h1>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                            // Fetch category name for the product
                            $cat_sql = "SELECT name FROM categories WHERE id = ?";
                            $cat_stmt = $conn->prepare($cat_sql);
                            $cat_stmt->bind_param("i", $row['category_id']);
                            $cat_stmt->execute();
                            $cat_result = $cat_stmt->get_result();
                            $category_name = $cat_result->fetch_assoc()['name'];
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($category_name); ?></td>
                                <td><?php echo htmlspecialchars($row['price']); ?></td>
                                <td><?php echo htmlspecialchars($row['stock']); ?></td>
                                <td>
                                    <a href="update_product.php?id=<?php echo htmlspecialchars($row['id']); ?>">Edit</a>
                                    <a href="manage_product.php?delete_id=<?php echo htmlspecialchars($row['id']); ?>" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
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
