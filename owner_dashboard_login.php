<?php
// Include database connection file
include('connect.php');

// Start session to handle logged-in owner
session_start();

// Check if the owner is logged in


// Get the owner ID from session
$owner_id = $_SESSION['owner_id'];

// Fetch turfs belonging to the logged-in owner with an 'accepted' status
$query = "SELECT * FROM turfs WHERE owner_id = '$owner_id' AND status = 'accepted'";
$result = mysqli_query($conn, $query);

// Check if the owner has any accepted turfs
if (mysqli_num_rows($result) == 0) {
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
        h1 {
            font-size: 28px;
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
    </style>
</head>
<body>

    <div class="container">
        <h1>Owner Dashboard</h1>

        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>

        <div class="turfs-list">
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='turf'>";
                    echo "<h3>" . $row['turf_name'] . "</h3>";
                    echo "<p><strong>Location:</strong> " . $row['location'] . "</p>";
                    echo "<p><strong>Contact:</strong> " . $row['contact'] . "</p>";
                    echo "<p><strong>Facilities:</strong> " . $row['facilities'] . "</p>";
                    echo "<p><strong>Operating Hours:</strong> " . $row['operating_hours'] . "</p>";
                    echo "<p><strong>Status:</strong> " . $row['status'] . "</p>";
                    echo "<p><strong>Special Notes:</strong> " . $row['special_notes'] . "</p>";
                    echo "</div>";
                }
            }
            ?>
        </div>

        <a href="logout.php" class="logout-btn">Log Out</a>
    </div>

</body>
</html>
