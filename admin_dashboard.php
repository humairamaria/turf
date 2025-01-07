<?php
session_start();
include('connect.php');


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}


$admin_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';


if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php'); // Redirect to login page after logout
    exit;
}


$tournament_request_query = "SELECT COUNT(*) AS pending_tournament_requests FROM tournament_requests WHERE status = 'pending'";
$tournament_request_result = $conn->query($tournament_request_query);
$pending_tournament_requests = $tournament_request_result->fetch_assoc()['pending_tournament_requests'];


$turf_request_query = "SELECT COUNT(*) AS pending_turf_requests FROM turfs WHERE status = 'pending'";
$turf_request_result = $conn->query($turf_request_query);
$pending_turf_requests = $turf_request_result->fetch_assoc()['pending_turf_requests'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #f5f7fa;
            display: flex;
        }

        /* Sidebar styling */
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
        }

        .sidebar h2 {
            margin-bottom: 20px;
        }

        .sidebar h2 a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            transition: background-color 0.3s;
        }

        .sidebar h2 a:hover {
            background-color: #2980b9;
            border-radius: 4px;
        }

        .sidebar a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            font-size: 16px;
            margin: 5px 0;
            position: relative;
        }

        .sidebar a:hover {
            background-color: #34495e;
            text-decoration: none;
        }

        .notification-dot {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 10px;
            height: 10px;
            background-color: red;
            border-radius: 50%;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #3498db;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
        }

        .admin-header h1 {
            margin: 0;
            font-size: 24px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 40px;
        }

        .button-container a {
            display: block;
            margin: 0 15px;
            padding: 15px 30px;
            background-color: #3498db;
            color: white;
            text-align: center;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
        }

        .button-container a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
 
    <div class="sidebar">
        <h2><a href="admin_profile.php">Admin Panel</a></h2>
        <a href="admin_manage.php">Manage Turf</a>
        <a href="admin_tournament.php">Tournament Requests<?php if ($pending_tournament_requests > 0) echo '<span class="notification-dot"></span>'; ?></a>
        <a href="turf_requests.php">Turf Requests<?php if ($pending_turf_requests > 0) echo '<span class="notification-dot"></span>'; ?></a>
        <a href="?logout=true">Logout</a>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="admin-header">
            <h1>Welcome, <?php echo htmlspecialchars($admin_name); ?>!</h1>
        </div>

        <div class="button-container">
            <a href="admin_user_info.php">User Info</a>
            <a href="admin_booked_turfs.php">Turfs</a>
            
        </div>
    </div>
</body>
</html>
