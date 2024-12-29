<?php
require 'connect.php';
session_start();

// Check if the owner is logged in
if (!isset($_SESSION['owner_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}

// Get the logged-in owner's ID from the session
$owner_id = $_SESSION['owner_id'];

// Fetch the owner's details from the database
$sql = "SELECT username, turf_name FROM owners WHERE owner_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $owner_id);
    $stmt->execute();
    $stmt->bind_result($username, $turf_name);
    $stmt->fetch();
    $stmt->close();
} else {
    die("Error fetching owner details: " . $conn->error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $turf_name = $_POST['turf_name'];
    $location = $_POST['location'];
    $contact = $_POST['contact'];
    $facilities = isset($_POST['facilities']) ? implode(', ', $_POST['facilities']) : '';
    $operating_hours = $_POST['operating_hours'];
    $special_notes = $_POST['special_notes'];
    $status = 'pending';

    // Insert into turfs table
    $query = "INSERT INTO turfs (turf_name, location, contact, facilities, operating_hours, special_notes, owner_id, status)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("ssssssis", $turf_name, $location, $contact, $facilities, $operating_hours, $special_notes, $owner_id, $status);
        if ($stmt->execute()) {
            $turf_id = $stmt->insert_id;

            // Insert type-specific details for each turf type
            foreach ($_POST['turf_types'] as $type) {
                $type_name = $type['type_name'];
                $capacity = max(0, $type['capacity']);
                $price_per_slot = max(0, $type['price_per_slot']);
                $slot_duration = $type['slot_duration'];

                $type_query = "INSERT INTO turf_types (turf_id, type_name, capacity, price_per_slot, slot_duration)
                               VALUES (?, ?, ?, ?, ?)";
                $type_stmt = $conn->prepare($type_query);
                if ($type_stmt) {
                    $type_stmt->bind_param("isiii", $turf_id, $type_name, $capacity, $price_per_slot, $slot_duration);
                    $type_stmt->execute();
                    $type_stmt->close();
                }
            }
            header("Location: success.php");
            exit;
        } else {
            die("Error inserting turf: " . $stmt->error);
        }
    } else {
        die("Error preparing turf query: " . $conn->error);
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
         body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8; /* Light background color */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* Dashboard Container */
        .dashboard-container {
            width: 90%;
            max-width: 500px; /* Smaller layout */
            padding: 20px;
            background-color: #ffffff; /* White container */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        /* Title */
        .dashboard-title {
            font-size: 24px; /* Smaller title font size */
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        /* Form Styles */
        .turf-form {
            display: flex;
            flex-direction: column;
            gap: 15px; /* Adjust gap for smaller layout */
        }

        /* Form Group */
        .form-group {
            text-align: left;
        }

        label {
            font-size: 14px; /* Smaller label font */
            font-weight: bold;
            margin-bottom: 5px;
        }

        input, select, textarea {
            width: 100%;
            padding: 8px; /* Smaller padding */
            font-size: 14px; /* Smaller input font */
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #3498db; /* Focus border color */
        }

        /* Add Type and Submit Button */
        .add-type-btn, .submit-btn {
            padding: 10px; /* Smaller button padding */
            font-size: 14px; /* Smaller button font size */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
        }

        .add-type-btn {
            background-color: #3498db; /* Blue color */
            color: white;
        }

        .add-type-btn:hover {
            background-color: #2980b9;
        }

        .submit-btn {
            background-color: #2ecc71; /* Green color */
            color: white;
        }

        .submit-btn:hover {
            background-color: #27ae60;
        }

        /* Type Section */
        .type-section {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            margin-top: 10px;
        }

        /* Remove Button */
        .remove-btn {
            color: red;
            font-size: 12px; /* Smaller font for remove button */
            cursor: pointer;
            text-align: right;
        }
        /* Same CSS as before */
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h1 class="dashboard-title">Owner Dashboard</h1>
        <form method="POST" action="" class="turf-form" id="turfForm">
            <div class="form-group">
                <label for="turf_name">Turf Name:</label>
                <input type="text" id="turf_name" name="turf_name" required>
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" required>
            </div>

            <div class="form-group">
                <label for="contact">Contact Information:</label>
                <input type="text" id="contact" name="contact" required>
            </div>

            <div class="form-group">
                <label>Facilities Provided:</label>
                <input type="checkbox" name="facilities[]" value="Floodlights"> Floodlights
                <input type="checkbox" name="facilities[]" value="Parking"> Parking
                <input type="checkbox" name="facilities[]" value="Changing Rooms"> Changing Rooms
                <input type="checkbox" name="facilities[]" value="Equipment Rental"> Equipment Rental
            </div>

            <div class="form-group">
                <label for="operating_hours">Operating Hours:</label>
                <input type="text" id="operating_hours" name="operating_hours" placeholder="e.g., 8 AM - 10 PM" required>
            </div>

            <div class="form-group">
                <label for="special_notes">Special Notes:</label>
                <textarea id="special_notes" name="special_notes" rows="4" placeholder="Any additional information"></textarea>
            </div>

            <div id="typeContainer">
                <h3>Types of Turf</h3>
                <div class="type-section" data-type-index="0">
                    <div class="form-group">
                        <label for="type_name_0">Type Name:</label>
                        <select id="type_name_0" name="turf_types[0][type_name]" required>
                            <option value="Football">Football</option>
                            <option value="Badminton">Badminton</option>
                            <option value="Cricket">Cricket</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="capacity_0">Capacity:</label>
                        <input type="number" id="capacity_0" name="turf_types[0][capacity]" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="price_per_slot_0">Price per Slot:</label>
                        <input type="number" id="price_per_slot_0" name="turf_types[0][price_per_slot]" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="slot_duration_0">Slot Duration (in hours):</label>
                        <input type="number" id="slot_duration_0" name="turf_types[0][slot_duration]" step="0.5" required>
                    </div>

                    <p class="remove-btn" onclick="removeType(this)">Remove Type</p>
                </div>
            </div>

            <button type="button" class="add-type-btn" onclick="addType()">Add Another Type</button>
            <button type="submit" class="submit-btn">Submit Request</button>
        </

