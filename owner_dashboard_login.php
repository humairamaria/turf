<?php
session_start();

include('connect.php');


if (!isset($_SESSION['owner_id'])) {
    echo "Session not set. Debug your login process.";
    header("Location: login.php"); 
    exit;
}


$owner_id =$_SESSION['owner_id'] ;



$owner_query = "SELECT username FROM owners WHERE owner_id = ?";
$owner_stmt = $conn->prepare($owner_query);
$owner_stmt->bind_param("i", $owner_id);
$owner_stmt->execute();
$owner_result = $owner_stmt->get_result();

if ($owner_result && $owner_result->num_rows > 0) {
    $owner_row = $owner_result->fetch_assoc();
    $owner_name = $owner_row['username'];
} else {
    $owner_name = "Owner"; // Default name if not found
}


$query = "SELECT * FROM turfs WHERE owner_id = ? AND status = 'accepted'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();


if ($result->num_rows == 0) {
    $message = "You do not have any accepted turfs yet.";
} else {
    $message = "Here are your accepted turfs:";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f8;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1100px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #333;
            text-align: center;
        }
        .turfs-list {
            margin-top: 20px;
        }
        .turf {
            border: 1px solid #ddd;
            margin: 10px 0;
            padding: 15px;
            border-radius: 6px;
            background-color: #f9f9f9;
        }
        .turf h3 {
            color: #3498db;
        }
        .turf p {
            font-size: 16px;
            color: #555;
        }
        .logout-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            margin-top: 20px;
        }
        .logout-btn:hover {
            background-color: #c0392b;
        }
        
        .btn-container {
            margin: 20px 0;
            text-align: right;
        }
        
        .tournament-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-left: 10px;
        }
        
        .tournament-btn:hover {
            background-color: #45a049;
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
        <h1>Welcome, <?php echo htmlspecialchars($owner_name); ?>!</h1>
        <h2>Owner Dashboard</h2>
        
        <div class="btn-container">
            <a href="tournament_history.php" class="tournament-btn">Tournament History</a>
        </div>
        <div style="text-align: center; margin-top: 20px;">
             <a href="owner_bookCheck.php" class="btn">View Your Turf Bookings</a>
        </div>

        <?php if (isset($message)) { echo "<p>" . htmlspecialchars($message) . "</p>"; } ?>

        <div class="turfs-list">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='turf'>";
                    echo "<h3>" . htmlspecialchars($row['turf_name']) . "</h3>";
                    echo "<p><strong>Location:</strong> " . htmlspecialchars($row['location']) . "</p>";
                    echo "<p><strong>Contact:</strong> " . htmlspecialchars($row['contact']) . "</p>";
                    echo "<p><strong>Facilities:</strong> " . htmlspecialchars($row['facilities']) . "</p>";
                    echo "<p><strong>Operating Hours:</strong> " . htmlspecialchars($row['operating_hours']) . "</p>";
                    echo "<p><strong>Special Notes:</strong> " . htmlspecialchars($row['special_notes']) . "</p>";
                    echo "</div>";
                }
            }
            ?>
        </div>

        <a href="logout.php" class="logout-btn">Log Out</a>
    </div>

</body>
</html>
