<?php
include 'db.php';
session_start();

// Check if user is logged in and has the role of 'admin'
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle adding a new user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $addUserQuery = "INSERT INTO users (username, password, role, created_at) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($addUserQuery);
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();
}

// Handle updating a user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $role = $_POST['role'];

    $updateUserQuery = "UPDATE users SET username = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($updateUserQuery);
    $stmt->bind_param("ssi", $username, $role, $user_id);
    $stmt->execute();
}

// Handle deleting a user
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    $deleteUserQuery = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($deleteUserQuery);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

// Fetch all users for display
$usersQuery = "SELECT * FROM users";
$users = $conn->query($usersQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
        .form-container, .users-container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .form-container h2, .users-container h2 {
            margin-top: 0;
            margin-bottom: 15px;
            color: #333;
        }
        .form-container form, .users-container form {
            display: flex;
            flex-direction: column;
        }
        .form-container label, .users-container label {
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        .form-container input[type="text"], .form-container input[type="password"], .form-container select, .users-container input[type="text"], .users-container select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
        }
        .form-container input[type="submit"], .users-container input[type="submit"] {
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
        .form-container input[type="submit"]:hover, .users-container input[type="submit"]:hover {
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
        <h1>Manage Users</h1>
    </header>

    <div class="container">
        <!-- Add User Form -->
        <div class="form-container">
            <h2>Add User</h2>
            <form method="post" action="manage_users.php">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="admin">Admin</option>
                    <option value="cashier">Cashier</option>
                </select>
                <input type="submit" name="add_user" value="Add User" class="button">
            </form>
        </div>

        <!-- Users List -->
        <div class="users-container">
            <h2>Users List</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['role']); ?></td>
                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                            <td>
                                <!-- Update User Form -->
                                <form method="post" action="manage_users.php" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <label for="username_<?php echo $user['id']; ?>">Username:</label>
                                    <input type="text" id="username_<?php echo $user['id']; ?>" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                                    <label for="role_<?php echo $user['id']; ?>">Role:</label>
                                    <select id="role_<?php echo $user['id']; ?>" name="role" required>
                                        <option value="admin" <?php echo ($user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                        <option value="cashier" <?php echo ($user['role'] == 'cashier') ? 'selected' : ''; ?>>Cashier</option>
                                    </select>
                                    <input type="submit" name="update_user" value="Update" class="button">
                                </form>
                                
                                <!-- Delete User Form -->
                                <form method="post" action="manage_users.php" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="submit" name="delete_user" value="Delete" class="button">
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
