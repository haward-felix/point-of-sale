<?php
include 'db.php';

// Pagination logic
$limit = 10; // Number of entries per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search logic
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Fetch products from the database
$sql = "SELECT * FROM products WHERE name LIKE ? LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$search_param = '%' . $search . '%';
$stmt->bind_param("sii", $search_param, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Fetch total number of products for pagination
$sql_total = "SELECT COUNT(*) as total FROM products WHERE name LIKE ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("s", $search_param);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$total_products = $result_total->fetch_assoc()['total'];
$total_pages = ceil($total_products / $limit);
?>

<body>
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

    .form-container {
        background: #fff;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 600px;
    }

    h1 {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        color: #333;
        text-align: center;
    }

    input[type="text"],
    input[type="password"],
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

    a {
        text-decoration: none;
        color: #007bff;
        margin: 0 0.5rem;
    }

    a:hover {
        color: #0056b3;
    }

    .pagination {
        margin-top: 1rem;
        text-align: center;
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
</style>

    <div class="form-container">
        <h1>Product List</h1>
        <form method="get">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo $search; ?>">
            <input type="submit" value="Search">
        </form>
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
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['category_id']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td><?php echo $row['stock']; ?></td>
                        <td>
                            <a href="update_product.php?id=<?php echo $row['id']; ?>">Update</a>
                            <a href="delete_product.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <div>
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>"><?php echo $i; ?></a>
            <?php } ?>
        </div>
    </div>
</body>
