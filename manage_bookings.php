<?php
require 'connect.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['booking_id']) && isset($_POST['action'])) {
        $booking_id = $_POST['booking_id'];
        $action = $_POST['action'];

        if ($action === 'approve') {
            // Update both status and payment_status
            $sql = "UPDATE bookings SET status = 'reserved', payment_status = 'paid' WHERE booking_id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("i", $booking_id);
        } elseif ($action === 'reject') {
            $sql = "UPDATE bookings SET status = 'rejected' WHERE booking_id = ?";
            $stmt = $conn->prepare($sql);
            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("i", $booking_id);
        } else {
            die("Invalid action.");
        }

        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            die("Error executing query: " . $stmt->error);
        }
        $stmt->close();
    }
}

// Modified SELECT query to only show pending requests
$sql = "SELECT bookings.booking_id, users.username AS user_name, 
        turfs.turf_name AS turf_name, bookings.booking_date, 
        bookings.time_slot, bookings.status 
        FROM bookings 
        JOIN users ON bookings.user_id = users.user_id 
        JOIN turfs ON bookings.turf_id = turfs.turf_id 
        WHERE bookings.status = 'pending'  
        ORDER BY bookings.booking_date DESC";

$result = $conn->query($sql);
if (!$result) {
    die("Error fetching bookings: " . $conn->error);
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
            background-color: #f5f5f5;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .user-info {
            text-align: right;
            margin-bottom: 20px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .action-buttons button {
            margin: 0 5px;
            padding: 8px 15px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
        }
        .action-buttons button[value="approve"] {
            background-color: #4CAF50;
            color: white;
        }
        .action-buttons button[value="reject"] {
            background-color: #f44336;
            color: white;
        }
        .no-requests {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="user-info">
        
    </div>

    <h1>Pending Booking Requests</h1>

    <?php if ($result->num_rows > 0): ?>
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
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['turf_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['time_slot']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td class='action-buttons'>
                        <form method='post' style='display:inline-block;'>
                            <input type='hidden' name='booking_id' value='<?php echo htmlspecialchars($row['booking_id']); ?>'>
                            <button type='submit' name='action' value='approve'>Approve</button>
                            <button type='submit' name='action' value='reject'>Reject</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="no-requests">
            <p>No pending booking requests at this time.</p>
        </div>
    <?php endif; ?>

    <?php $conn->close(); ?>
</body>
</html>
