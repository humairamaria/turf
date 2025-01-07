<?php
session_start();
include('connect.php');

// Check if owner is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Owner') {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}

// Get owner-specific turf ID (assuming each owner is associated with a single turf)
// Adjust this logic if owners can manage multiple turfs.
$owner_id = $_SESSION['owner_id']; // Assuming owner ID is stored in session
$query_turf = "SELECT turf_id FROM turfs WHERE owner_id = '$owner_id'";
$result_turf = mysqli_query($conn, $query_turf);
$turf = mysqli_fetch_assoc($result_turf);
$turf_id = $turf['turf_id'];

// Fetch specific tournament details hosted by the owner's turf
$query = "SELECT tournament_id, tournament_name, start_date, end_date FROM tournaments WHERE turf_id = '$turf_id'";
$result = mysqli_query($conn, $query);

// Logout logic
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php'); // Redirect to login page after logout
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tournament History</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .dashboard-container {
            width: 90%;
            max-width: 800px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        h1 {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        th {
            background-color: #f2f2f2;
        }

        .btn-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .btn-container a {
            display: inline-block;
            padding: 10px 20px;
            font-size: 14px;
            background-color: #3498db;
            color: white;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
        }

        .btn-container a:hover {
            background-color: #2980b9;
        }

        .host-btn {
            padding: 10px 20px;
            font-size: 14px;
            background-color: #2ecc71;
            color: white;
            text-align: center;
            border-radius: 5px;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: none;
            cursor: pointer;
        }

        .host-btn:hover {
            background-color: #27ae60;
        }

        .no-data {
            text-align: center;
            color: #555;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Tournament History</h1>

        <div class="btn-container">
            <a href="owner_dashboard_login.php">Back to Dashboard</a>
            <a href="?logout=true">Logout</a>
        </div>

        <div style="text-align: right; margin-bottom: 10px;">
            <a href="owner_tournament.php" class="host-btn">Host a Tournament</a>
        </div>

        <table>
            <tr>
                <th>Tournament ID</th>
                <th>Tournament Name</th>
                <th>Start Date</th>
                <th>End Date</th>
            </tr>
            <?php if (mysqli_num_rows($result) > 0) { ?>
                <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $row['tournament_id']; ?></td>
                        <td><?php echo $row['tournament_name']; ?></td>
                        <td><?php echo $row['start_date']; ?></td>
                        <td><?php echo $row['end_date']; ?></td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr>
                    <td colspan="4" class="no-data">No tournaments hosted yet.</td>
                </tr>
            <?php } ?>
        </table>
    </div>
</body>
</html>
