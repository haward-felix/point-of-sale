<?php
include 'db.php';

if (isset($_POST['id'], $_POST['product_name'], $_POST['price'], $_POST['category_id'])) {
    $id = $_POST['id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];

    $sql = "UPDATE products SET product_name = ?, price = ?, category_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdii", $product_name, $price, $category_id, $id);

    if ($stmt->execute()) {
        echo "Product updated successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
}