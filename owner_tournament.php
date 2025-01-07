<?php
session_start();
include('connect.php');


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Owner') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $turf_id = $_POST['turf_id'];
    $tournament_name = $_POST['tournament_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $description = $_POST['description'];

    $owner_id = $_SESSION['owner_id']; // Assuming owner's ID is in session

 
    $query = "INSERT INTO tournament_requests (turf_id, owner_id, tournament_name, start_date, end_date, description)
              VALUES ('$turf_id', '$owner_id', '$tournament_name', '$start_date', '$end_date', '$description')";
    mysqli_query($conn, $query);
    echo "<script>alert('Tournament request sent to admin!');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Tournament</title>
</head>
<style>
      body {
    font-family: 'Comic Sans MS', Arial, sans-serif;
    background-color: #fef5e7;
    margin: 0;
    padding: 10px;
}

.container {
    max-width: 500px;
    margin: 0 auto;
    padding: 10px;
    background-color: #fff5e1;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

h1 {
    color: #ff6f61;
    text-align: center;
    margin-bottom: 20px;
    font-size: 24px;
}

form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

label {
    font-weight: bold;
    color: #ff6f61;
    font-size: 14px;
}

input, textarea {
    width: 100%;
    padding: 6px;
    border: 1px solid #ffdab9;
    border-radius: 8px;
    font-size: 12px;
    margin-top: 2px;
    background-color: #fffaf0;
}

textarea {
    height: 60px;
    resize: vertical;
}

button {
    background-color: #ffb6c1;
    color: white;
    padding: 8px 14px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    margin-top: 10px;
    transition: background-color 0.3s ease;
}

button:hover {
    background-color: #ff69b4;
}
    </style>
</head>
<body>
    <h1>Request a Tournament</h1>
    <form method="POST">
        <label for="turf_id">Turf ID:</label>
        <input type="number" name="turf_id" required><br>

        <label for="tournament_name">Tournament Name:</label>
        <input type="text" name="tournament_name" required><br>

        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" required><br>

        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" required><br>

        <label for="description">Description:</label>
        <textarea name="description"></textarea><br>

        <button type="submit">Send Request</button>
    </form>
</body>
</html>




