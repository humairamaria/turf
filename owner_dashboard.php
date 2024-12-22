<?php
// connect to the database
include('connect.php');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $turf_name = $_POST['turf_name'];
    $location = $_POST['location'];
    $capacity = max(0, $_POST['capacity']); // Ensure non-negative value
    $price = max(0, $_POST['price']); // Ensure non-negative value
    $contact = $_POST['contact'];
    $turf_types = isset($_POST['turf_type']) ? implode(', ', $_POST['turf_type']) : '';
    $facilities = isset($_POST['facilities']) ? implode(', ', $_POST['facilities']) : '';
    $operating_hours = $_POST['operating_hours'];
    $time_slots = $_POST['time_slots'];
    $special_notes = $_POST['special_notes'];
    $owner_id = 1; // Replace with the logged-in owner's ID (e.g., from a session variable)

    $query = "INSERT INTO turf_requests (turf_name, location, capacity, price, contact, turf_type, facilities, operating_hours, time_slots, special_notes, owner_id, status) 
              VALUES ('$turf_name', '$location', '$capacity', '$price', '$contact', '$turf_types', '$facilities', '$operating_hours', '$time_slots', '$special_notes', '$owner_id', 'pending')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Turf request sent successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
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
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .dashboard-container {
            width: 90%;
            max-width: 600px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .dashboard-title {
            font-size: 28px;
            margin-bottom: 20px;
            color: #2c3e50;
            font-weight: bold;
        }

        .turf-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            width: 100%;
            margin: 0 auto;
        }

        .form-group {
            text-align: left;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input:focus, textarea:focus, select:focus {
            border-color: #3498db;
        }

        .submit-btn {
            background-color: #3498db;
            color: white;
            padding: 12px 16px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s;
            text-transform: uppercase;
        }

        .submit-btn:hover {
            background-color: #2980b9;
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1 class="dashboard-title">Owner Dashboard</h1>
        <form method="POST" action="" class="turf-form">
            <div class="form-group">
                <label for="turf_name">Turf Name:</label>
                <input type="text" id="turf_name" name="turf_name" required>
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required>
            </div>

            <div class="form-group">
                <label for="capacity">Capacity:</label>
                <input type="number" id="capacity" name="capacity" min="0" required>
            </div>

            <div class="form-group">
                <label for="price">Price per Hour:</label>
                <input type="number" id="price" name="price" min="0" required>
            </div>

            <div class="form-group">
                <label for="contact">Contact Information (comma-separated):</label>
                <input type="text" id="contact" name="contact" required>
            </div>

            <div class="form-group">
                <label>Turf Types:</label>
                <input type="checkbox" name="turf_type[]" value="Football"> Football<br>
                <input type="checkbox" name="turf_type[]" value="Cricket"> Cricket<br>
                <input type="checkbox" name="turf_type[]" value="Badminton"> Badminton<br>
                <input type="checkbox" name="turf_type[]" value="Other"> Other<br>
            </div>

            <div class="form-group">
                <label>Facilities Provided:</label>
                <input type="checkbox" name="facilities[]" value="Floodlights"> Floodlights<br>
                <input type="checkbox" name="facilities[]" value="Parking"> Parking<br>
                <input type="checkbox" name="facilities[]" value="Changing Rooms"> Changing Rooms<br>
                <input type="checkbox" name="facilities[]" value="Equipment Rental"> Equipment Rental<br>
            </div>

            <div class="form-group">
                <label for="operating_hours">Operating Hours:</label>
                <input type="text" id="operating_hours" name="operating_hours" placeholder="e.g., 8 AM - 12 AM" required>
            </div>

            <div class="form-group">
                <label for="time_slots">Time Slot Duration (in hours):</label>
                <input type="text" id="time_slots" name="time_slots" placeholder="e.g., 1 or 1.5" required>
            </div>

            <div class="form-group">
                <label for="special_notes">Special Notes:</label>
                <textarea id="special_notes" name="special_notes" rows="4" placeholder="Any additional information"></textarea>
            </div>

            <button type="submit" class="submit-btn">Send Request</button>
        </form>
    </div>
</body>
</html>
