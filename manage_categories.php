<?php
include 'db.php';
session_start();

// Check if user is logged in and has the role of 'admin'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle adding a new category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $name = $_POST['name'];

    $addCategoryQuery = "INSERT INTO categories (name, created_at) VALUES (?, NOW())";
    $stmt = $conn->prepare($addCategoryQuery);
    $stmt->bind_param("s", $name);
    $stmt->execute();
}

// Handle updating a category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_category'])) {
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];

    $updateCategoryQuery = "UPDATE categories SET name = ? WHERE id = ?";
    $stmt = $conn->prepare($updateCategoryQuery);
    $stmt->bind_param("si", $name, $category_id);
    $stmt->execute();
}

// Handle deleting a category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_category'])) {
    $category_id = $_POST['category_id'];

    $deleteCategoryQuery = "DELETE FROM categories WHERE id = ?";
    $stmt = $conn->prepare($deleteCategoryQuery);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
}

// Fetch all categories for display
$categoriesQuery = "SELECT * FROM categories";
$categories = $conn->query($categoriesQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
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
        .form-container, .categories-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .form-container h2, .categories-container h2 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333;
        }
        .form-container form, .categories-container form {
            display: flex;
            flex-direction: column;
        }
        .form-container label, .categories-container label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .form-container input[type="text"], .categories-container input[type="text"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-container input[type="submit"], .categories-container input[type="submit"] {
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
            transition: background-color 0.3s;
        }
        .form-container input[type="submit"]:hover, .categories-container input[type="submit"]:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
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
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <h1>Manage Categories</h1>
    </header>

    <div class="container">
        <!-- Add Category Form -->
        <div class="form-container">
            <h2>Add Category</h2>
            <form method="post" action="manage_categories.php">
                <label for="name">Category Name:</label>
                <input type="text" id="name" name="name" required>
                <input type="submit" name="add_category" value="Add Category" class="button">
            </form>
        </div>

        <!-- Categories List -->
        <div class="categories-container">
            <h2>Categories List</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($category = $categories->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['id']); ?></td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td><?php echo htmlspecialchars($category['created_at']); ?></td>
                            <td>
                                <!-- Update Category Form -->
                                <form method="post" action="manage_categories.php" style="display:inline;">
                                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                    <label for="name_<?php echo $category['id']; ?>">Name:</label>
                                    <input type="text" id="name_<?php echo $category['id']; ?>" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                                    <input type="submit" name="update_category" value="Update" class="button">
                                </form>
                                
                                <!-- Delete Category Form -->
                                <form method="post" action="manage_categories.php" style="display:inline;">
                                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                    <input type="submit" name="delete_category" value="Delete" class="button">
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Navigation Links -->
        <a href="admin_panel.php" class="button">Go Back To Admin Panel</a>
    </div>
</body>
</html>
