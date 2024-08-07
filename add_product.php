<?php
include 'db.php';

// Fetch categories to populate the dropdown
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);

if ($result === false) {
    die("Error fetching categories: " . $conn->error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if all required POST variables are set
    if (isset($_POST['name'], $_POST['category_id'], $_POST['price'], $_POST['stock'])) {
        $name = $_POST['name'];
        $category_id = $_POST['category_id'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];

        // Prepare SQL query to insert product
        $sql = "INSERT INTO products (name, category_id, price, stock) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sidi", $name, $category_id, $price, $stock);

        if ($stmt->execute()) {
            $message = "Product added successfully";
        } else {
            $message = "Error: " . $stmt->error;
        }
    } else {
        $message = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Product</title>
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

        label {
            display: block;
            margin-bottom: 0.5rem;
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

        .message {
            margin: 1rem 0;
            text-align: center;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Add Product</h1>
        <?php if (isset($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <form method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required><br>

            <label for="category">Category:</label>
            <select id="category" name="category_id" required>
                <option value="">Select a category</option>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <option value="<?php echo htmlspecialchars($row['id']); ?>">
                        <?php echo htmlspecialchars($row['name']); ?>
                    </option>
                <?php } ?>
            </select><br>

            <label for="price">Price:</label>
            <input type="text" id="price" name="price" required><br>

            <label for="stock">Stock:</label>
            <input type="text" id="stock" name="stock" required><br>

            <input type="submit" value="Add Product">
        </form>
    </div>
</body>
</html>
