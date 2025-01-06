<?php
// Database connection
require 'connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug: Test database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Handle status update (Approve or Reject)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<pre>POST Data: " . print_r($_POST, true) . "</pre>"; // Debug: Check POST data

    if (isset($_POST['id']) && isset($_POST['action'])) {
        $booking_id = $_POST['id'];
        $action = $_POST['action'];

        if ($action === 'approve') {
            $status = 'reserved';
        } elseif ($action === 'reject') {
            $status = 'rejected';
        } else {
            die("Invalid action.");
        }

        $sql = "UPDATE bookings SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            die("Prepare failed: " . $conn->error); // Debug: Check prepare error
        }
        $stmt->bind_param("si", $status, $booking_id);

        if ($stmt->execute()) {
            // Debug: Successful query
            echo "<script>alert('Booking status updated successfully!');</script>";
            header("Location: " . $_SERVER['PHP_SELF']); // Redirect to refresh page
            exit();
        } else {
            die("Error executing query: " . $stmt->error); // Debug: SQL error
        }
        $stmt->close();
    } else {
        echo "Required POST parameters are missing.";
    }
}

// Fetch all bookings
$sql = "SELECT bookings.id, users.username AS user_name, 
        turfs.turf_name AS turf_name, bookings.booking_date, 
        bookings.time_slot, bookings.status 
        FROM bookings
        JOIN users ON bookings.user_id = users.user_id
        JOIN turfs ON bookings.turf_id = turfs.turf_id
        ORDER BY bookings.booking_date DESC";

$result = $conn->query($sql);
if (!$result) {
    die("Error fetching bookings: " . $conn->error); // Debug: SQL error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .action-buttons button {
            margin: 0 5px;
            padding: 5px 10px;
            cursor: pointer;
        }
        .action-buttons button[value="approve"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 3px;
        }
        .action-buttons button[value="reject"] {
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <h1>Manage Bookings</h1>

    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>User</th>
                <th>Turf</th>
                <th>Date</th>
                <th>Time Slot</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($row['id']) . "</td>
                            <td>" . htmlspecialchars($row['user_name']) . "</td>
                            <td>" . htmlspecialchars($row['turf_name']) . "</td>
                            <td>" . htmlspecialchars($row['booking_date']) . "</td>
                            <td>" . htmlspecialchars($row['time_slot']) . "</td>
                            <td>" . htmlspecialchars($row['status']) . "</td>
                            <td class='action-buttons'>
                                <form method='post' style='display:inline-block;'>
                                    <input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>
                                    <button type='submit' name='action' value='approve'>Approve</button>
                                    <button type='submit' name='action' value='reject'>Reject</button>
                                </form>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='7'>No bookings found.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <?php $conn->close(); ?>
</body>
</html>                                                              
