<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

echo "<div class='dashboard'>";
echo "Welcome, " . $_SESSION['username'] . "!<br>";
echo "Your role is: " . $_SESSION['role'] . "<br>";

if ($_SESSION['role'] == 'admin') {
    echo "<a href='admin_panel.php'>Admin Panel</a>";
} elseif ($_SESSION['role'] == 'cashier') {
    echo "<a href='cashier_panel.php'>Cashier Panel</a>";
} elseif ($_SESSION['role'] == 'manager') {
    echo "<a href='manager_panel.php'>Manager Panel</a>";
}

echo "<a href='logout.php'>Logout</a>";
echo "</div>";
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
        height: 100vh;
    }

    .form-container {
        background: #fff;
        padding: 2rem;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 400px;
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

    .dashboard {
        text-align: center;
    }

    .dashboard a {
        display: inline-block;
        margin: 1rem;
        padding: 1rem 2rem;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .dashboard a:hover {
        background-color: #0056b3;
    }
</style>

</body>
