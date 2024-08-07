<?php
include 'db.php';

// Fetch categories to populate the dropdown
$sql_categories = "SELECT * FROM categories";
$result_categories = $conn->query($sql_categories);

$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $sql = "UPDATE products SET name = ?, category_id = ?, price = ?, stock = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sidii", $name, $category_id, $price, $stock, $id);

    if ($stmt->execute()) {
        $success_message = "Product updated successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
}

// Fetch the product details to populate the form
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
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
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-bottom: 1rem;
        }

        .success-message {
            text-align: center;
            margin-bottom: 1rem;
            color: green;
            font-weight: bold;
        }

        input[type="text"], select {
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
</head>
<body>
    <div class="form-container">
        <h1>Update Product</h1>
        <?php if ($success_message) : ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($product['id']); ?>">
            Name: <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required><br>
            Category: <select name="category_id">
                <?php while ($row = $result_categories->fetch_assoc()) { ?>
                    <option value="<?php echo htmlspecialchars($row['id']); ?>" <?php echo $product['category_id'] == $row['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($row['name']); ?></option>
                <?php } ?>
            </select><br>
            Price: <input type="text" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" required><br>
            Stock: <input type="text" name="stock" value="<?php echo htmlspecialchars($product['stock']); ?>" required><br>
            <input type="submit" value="Update Product">
        </form>
    </div>
</body>
</html>
