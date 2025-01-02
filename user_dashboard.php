<?php
session_start();
include 'connect.php';

// Check if the user is logged in and is a User
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'User') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_login = $_SESSION['username']; // Assuming you store username in session

// Get current UTC time
$current_utc = gmdate('Y-m-d H:i:s');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: #2196f3;
            color: #fff;
        }

        .header-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .datetime {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
            margin: 0;
        }

        .dashboard-header h1 {
            font-size: 24px;
            margin: 0;
        }

        .dashboard-header .menu {
            display: flex;
            gap: 15px;
        }

        .dashboard-header .menu a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
        }

        .filters {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .filters input, .filters select, .filters button {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .turfs-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .turf-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            width: 30%;
            padding: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .turf-card h3 {
            margin-top: 0;
            color: #333;
        }

        .turf-card p {
            margin: 5px 0;
            color: #666;
        }

        .turf-card button {
            padding: 10px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .turf-card button:hover {
            background-color: #45a049;
        }

        .logout-btn {
            padding: 10px;
            background-color: #e53935;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .logout-btn:hover {
            background-color: #d32f2f;
        }

        .reset-btn {
            padding: 10px;
            background-color: #ff9800;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }

        .reset-btn:hover {
            background-color: #f57c00;
        }

        .no-results {
            text-align: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }

        .no-results p {
            margin: 10px 0;
            color: #666;
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="header-info">
            <h1>Welcome, <?php echo htmlspecialchars($user_login); ?></h1>
            <p class="datetime">UTC: <?php echo $current_utc; ?></p>
        </div>
        <div class="menu">
            <a href="#booked">Booked Turfs</a>
            <a href="#sharing">Sharing Options</a>
            <form method="POST" action="logout.php" style="display:inline;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>

    <div class="container">
        <form method="GET" class="filters">
            <input type="text" name="location" placeholder="Location" value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">
            <input type="number" name="min_price" placeholder="Min Price" value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
            <input type="number" name="max_price" placeholder="Max Price" value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
            <input type="number" name="capacity" placeholder="Capacity" value="<?php echo isset($_GET['capacity']) ? htmlspecialchars($_GET['capacity']) : ''; ?>">
            <select name="time_slot">
                <option value="">Any Time</option>
                <?php
                for ($i = 0; $i < 24; $i++) {
                    $start = sprintf("%02d:00", $i);
                    $end = sprintf("%02d:00", ($i + 1) % 24);
                    echo "<option value='$start-$end' " . (isset($_GET['time_slot']) && $_GET['time_slot'] == "$start-$end" ? 'selected' : '') . ">$start - $end</option>";
                }
                ?>
            </select>
            <button type="submit">Filter</button>
            <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="reset-btn">Show All Turfs</a>
        </form>

        <section id="available">
            <h2>Available Turfs</h2>
            <div class="turfs-list">
                <?php
                $query = "SELECT * FROM turfs WHERE status = 'accepted'";
                $params = array();

                if (!empty($_GET['location'])) {
                    $location = mysqli_real_escape_string($conn, $_GET['location']);
                    $query .= " AND location LIKE '%$location%'";
                }

                if (!empty($_GET['min_price'])) {
                    $min_price = (int)$_GET['min_price'];
                    $query .= " AND price >= $min_price";
                }

                if (!empty($_GET['max_price'])) {
                    $max_price = (int)$_GET['max_price'];
                    $query .= " AND price <= $max_price";
                }

                if (!empty($_GET['capacity'])) {
                    $capacity = (int)$_GET['capacity'];
                    $query .= " AND capacity >= $capacity";
                }

                if (!empty($_GET['time_slot'])) {
                    $time_slot = mysqli_real_escape_string($conn, $_GET['time_slot']);
                    $query .= " AND time_slot = '$time_slot'";
                }

                $result = mysqli_query($conn, $query);

                if ($result && mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<div class='turf-card'>";
                        echo "<h3>" . htmlspecialchars($row['turf_name']) . "</h3>";
                        echo "<p>Location: " . htmlspecialchars($row['location']) . "</p>";
                        echo "<p>Price: $" . htmlspecialchars($row['price']) . " per hour</p>";
                        echo "<p>Capacity: " . htmlspecialchars($row['capacity']) . " players</p>";
                        echo "<form method='POST' action='book_turf.php'>";
                        echo "<label for='time_slot'>Time Slot:</label>";
                        echo "<select name='time_slot'>";
                        for ($i = 0; $i < 24; $i++) {
                            $start = sprintf("%02d:00", $i);
                            $end = sprintf("%02d:00", ($i + 1) % 24);
                            echo "<option value='$start-$end'>$start - $end</option>";
                        }
                        echo "</select>";
                        echo "<label for='share'>Share Turf:</label>";
                        echo "<input type='checkbox' name='share' value='1'>";
                        echo "<input type='hidden' name='turf_id' value='" . htmlspecialchars($row['turf_id']) . "'>";
                        echo "<button type='submit'>Book Now</button>";
                        echo "</form>";
                        echo "</div>";
                    }
                } else {
                    echo "<div class='no-results'>";
                    echo "<p>No turfs available matching your criteria.</p>";
                    echo "<p>Click 'Show All Turfs' to view all available turfs.</p>";
                    echo "</div>";
                }
                ?>
            </div>
        </section>

        <section id="sharing">
            <h2>Sharing Options</h2>
            <div class="turfs-list">
                <?php
                $shared_query = "SELECT b.*, t.turf_name, t.location FROM bookings b JOIN turfs t ON b.turf_id = t.turf_id WHERE b.share = 1 AND b.status = 'pending'";
                $shared_result = mysqli_query($conn, $shared_query);

                if ($shared_result && mysqli_num_rows($shared_result) > 0) {
                    while ($row = mysqli_fetch_assoc($shared_result)) {
                        echo "<div class='turf-card'>";
                        echo "<h3>" . htmlspecialchars($row['turf_name']) . "</h3>";
                        echo "<p>Location: " . htmlspecialchars($row['location']) . "</p>";
                        echo "<p>Time Slot: " . htmlspecialchars($row['time_slot']) . "</p>";
                        echo "<form method='POST' action='join_sharing.php'>";
                        echo "<input type='hidden' name='booking_id' value='" . htmlspecialchars($row['booking_id']) . "'>";
                        echo "<button type='submit'>Join</button>";
                        echo "</form>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No shared turfs available at the moment.</p>";
                }
                ?>
            </div>
        </section>

        <section id="booked">
            <h2>Booked Turfs</h2>
            <table>
                <thead>
                    <tr>
                        <th>Turf Name</th>
                        <th>Location</th>
                        <th>Time Slot</                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $history_query = "SELECT b.*, t.turf_name, t.location FROM bookings b JOIN turfs t ON b.turf_id = t.turf_id WHERE b.user_id = '$user_id'";
                    $history_result = mysqli_query($conn, $history_query);

                    if ($history_result && mysqli_num_rows($history_result) > 0) {
                        while ($row = mysqli_fetch_assoc($history_result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['turf_name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['time_slot']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4'>No bookings found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>
</body>
</html>
