<?php
session_start();
include('connect.php');


if (!isset($_SESSION['owner_id'])) {
    header("Location: login.php");
    exit;
}

$owner_id = $_SESSION['owner_id'];


$query = "
    SELECT 
        t.turf_id, 
        t.turf_name, 
        b.booking_date, 
        b.time_slot, 
        u.username 
    FROM turfs t
    LEFT JOIN bookings b ON t.turf_id = b.turf_id
    LEFT JOIN users u ON b.user_id = u.user_id
    WHERE t.owner_id = ?
    ORDER BY t.turf_id, b.booking_date, b.time_slot";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();


$turf_bookings = [];
while ($row = $result->fetch_assoc()) {
    $turf_id = $row['turf_id'];
    $turf_name = $row['turf_name'];

    if (!isset($turf_bookings[$turf_id])) {
        $turf_bookings[$turf_id] = [
            'turf_name' => $turf_name,
            'bookings' => [],
        ];
    }

    if ($row['booking_date'] !== null) {
        $turf_bookings[$turf_id]['bookings'][] = [
            'username' => $row['username'],
            'booking_date' => $row['booking_date'],
            'time_slot' => $row['time_slot'],
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turf Bookings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .turf-section {
            margin-bottom: 30px;
        }
        .turf-section h2 {
            color: #4CAF50;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #4CAF50;
            color: white;
        }
        table td {
            background-color: #f9f9f9;
        }
        .no-bookings {
            font-style: italic;
            color: #999;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            margin-top: 20px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Your Turf Bookings</h1>
    <div style="text-align: center; margin-top: 20px;">
        <a href="owner_dashboard_login.php" class="btn">Back to Dashboard</a>
    </div>
    <?php if (empty($turf_bookings)) { ?>
        <p>No turfs or bookings found.</p>

    <?php } else { ?>
        <?php foreach ($turf_bookings as $turf_id => $turf_data) { ?>
            <div class="turf-section">
                <h2><?php echo htmlspecialchars($turf_data['turf_name']); ?></h2>
                <?php if (empty($turf_data['bookings'])) { ?>
                    <p class="no-bookings">No bookings for this turf.</p>
                <?php } else { ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Booking Date</th>
                                <th>Time Slot</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($turf_data['bookings'] as $booking) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($booking['username']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['booking_date']); ?></td>
                                    <td><?php echo htmlspecialchars($booking['time_slot']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>

</div>

</body>
</html>
