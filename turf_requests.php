<?php
session_start();
include('connect.php');


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php');
    exit;
}


$query = "SELECT * FROM turfs WHERE status = 'pending'";
$result = mysqli_query($conn, $query);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['accept'])) {
        $turf_id = $_POST['turf_id'];
        // Update turf status to 'accepted'
        $update_query = "UPDATE turfs SET status = 'accepted' WHERE turf_id = '$turf_id'";
        mysqli_query($conn, $update_query);
        echo "<script>alert('Turf accepted!'); window.location.href='turf_requests.php';</script>";
    }

    if (isset($_POST['decline'])) {
        $turf_id = $_POST['turf_id'];
       
        $update_query = "UPDATE turfs SET status = 'declined' WHERE turf_id = '$turf_id'";
        mysqli_query($conn, $update_query);
        echo "<script>alert('Turf declined!'); window.location.href='turf_requests.php';</script>";
    }
}


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
    <title>Turf Requests</title>
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
            max-width: 1000px;
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

        button {
            padding: 8px 12px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
        }

        .accept-btn {
            background-color: #2ecc71;
            color: white;
        }

        .accept-btn:hover {
            background-color: #27ae60;
        }

        .decline-btn {
            background-color: #e74c3c;
            color: white;
        }

        .decline-btn:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1>Turf Requests</h1>

        <div class="btn-container">
            <a href="admin_dashboard.php">Back to Dashboard</a>
            <a href="?logout=true">Logout</a>
        </div>

        <table>
            <tr>
                <th>Turf Name</th>
                <th>Location</th>
                <th>Contact</th>
                <th>Facilities</th>
                <th>Operating Hours</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['turf_name']; ?></td>
                    <td><?php echo $row['location']; ?></td>
                    <td><?php echo $row['contact']; ?></td>
                    <td><?php echo $row['facilities']; ?></td>
                    <td><?php echo $row['operating_hours']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <form method="POST" action="">
                            <input type="hidden" name="turf_id" value="<?php echo $row['turf_id']; ?>">
                            <button type="submit" name="accept" class="accept-btn">Accept</button>
                            <button type="submit" name="decline" class="decline-btn">Decline</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>

        <?php if (mysqli_num_rows($result) === 0) { ?>
            <p style="text-align: center; margin-top: 20px; color: #555;">No pending turf requests at the moment.</p>
        <?php } ?>
    </div>
</body>
</html>
