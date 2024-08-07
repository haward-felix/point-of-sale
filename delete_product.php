<?php
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Product deleted successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<script>
    function confirmDelete(productId) {
        if (confirm("Are you sure you want to delete this product?")) {
            window.location.href = 'delete_product.php?id=' + productId;
        }
    }
</script>

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
</body>
