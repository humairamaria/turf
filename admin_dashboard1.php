<?php
session_start();
require 'connect.php';

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

// Handle deleting a turf
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_sql = "DELETE FROM turfs WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit;
}

// Handle adding a turf
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $turf_name = trim($_POST['turf_name']);
    $location = trim($_POST['location']);
    $contact = trim($_POST['contact']);
    $capacity = intval($_POST['capacity']);
    $price = floatval($_POST['price']);

    $add_sql = "INSERT INTO turfs (turf_name, location, contact, capacity, price) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($add_sql);
    $stmt->bind_param("sssii", $turf_name, $location, $contact, $capacity, $price);
    $stmt->execute();
    header("Location: admin_dashboard.php");
    exit;
}

// Fetch all turfs
$turf_sql = "SELECT * FROM turfs";
$turf_result = $conn->query($turf_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: url('football-field.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .dashboard-container {
            background-color: rgba(0, 0, 0, 0.8);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 1000px;
            color: #fff;
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #fff;
            padding-bottom: 10px;
        }
        .dashboard-header h1 {
            margin: 0;
        }
        .logout-button {
            background-color: #d9534f;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        table th {
            background-color: #4CAF50;
            color: white;
        }
        table tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .form-container {
            margin-top: 20px;
        }
        input, button {
            padding: 10px;
            margin: 5px;
            border-radius: 5px;
            border: 1px solid #ddd;
            width: calc(100% - 22px);
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
        }
        button:hover {
            background-color: #45a049;
        }
        .actions a {
            color: #d9534f;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1> Welcome Admin!</h1>
            <form action="logout.php" method="POST">
                <button type="submit" class="logout-button">Logout</button>
            </form>
        </div>

        <h2>Registered Turfs</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Turf Name</th>
                <th>Location</th>
                <th>Contact</th>
                <th>Capacity</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
            <?php if ($turf_result->num_rows > 0): ?>
                <?php while ($row = $turf_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['turf_name']; ?></td>
                        <td><?php echo $row['location']; ?></td>
                        <td><?php echo $row['contact']; ?></td>
                        <td><?php echo $row['capacity']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td class="actions">
                            <a href="admin_dashboard.php?delete_id=<?php echo $row['id']; ?>">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No turfs found.</td>
                </tr>
            <?php endif; ?>
        </table>

        <div class="form-container">
            <h3>Add New Turf</h3>
            <form method="POST" action="admin_dashboard.php">
                <input type="text" name="turf_name" placeholder="Turf Name" required>
                <input type="text" name="location" placeholder="Location" required>
                <input type="text" name="contact" placeholder="Contact" required>
                <input type="number" name="capacity" placeholder="Capacity" required>
                <input type="number" step="0.01" name="price" placeholder="Price" required>
                <button type="submit">Add Turf</button>
            </form>
        </div>
    </div>
</body>
</html>
