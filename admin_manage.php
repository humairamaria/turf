<?php
session_start();
include('connect.php');


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php'); // Redirect to login page if not logged in
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update'])) {
        $turf_id = $_POST['turf_id'];
        $turf_name = $_POST['turf_name'];
        $location = $_POST['location'];
        $operating_hours = $_POST['operating_hours'];

      
        $update_turf_query = "
            UPDATE turfs 
            SET turf_name = '$turf_name', location = '$location', operating_hours = '$operating_hours' 
            WHERE turf_id = '$turf_id'
        ";
        mysqli_query($conn, $update_turf_query);

        $price_per_slot = $_POST['price_per_slot'];
        $slot_duration = $_POST['slot_duration'];
        $capacity = $_POST['capacity'];

       
        $update_type_query = "
            UPDATE turf_types 
            SET price_per_slot = '$price_per_slot', slot_duration = '$slot_duration', capacity = '$capacity' 
            WHERE turf_id = '$turf_id'
        ";
        mysqli_query($conn, $update_type_query);

        echo "<script>alert('Turf updated successfully!');</script>";
    }

    if (isset($_POST['delete'])) {
        $turf_id = $_POST['turf_id'];

       
        $delete_type_query = "DELETE FROM turf_types WHERE turf_id = '$turf_id'";
        mysqli_query($conn, $delete_type_query);

        $delete_turf_query = "DELETE FROM turfs WHERE turf_id = '$turf_id'";
        mysqli_query($conn, $delete_turf_query);

        echo "<script>alert('Turf deleted successfully!');</script>";
    }
}


$query = "
    SELECT t.turf_id, t.turf_name, t.owner_id, t.location, tt.price_per_slot, tt.slot_duration, tt.capacity, 
           tt.type_name AS turf_type, t.operating_hours
    FROM turfs t
    JOIN turf_types tt ON t.turf_id = tt.turf_id
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Turfs</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f4f8;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 14px;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ccc;
        }

        th {
            background-color: #f2f2f2;
        }

        .button {
            padding: 6px 10px;
            font-size: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-transform: uppercase;
        }

        .update-btn {
            background-color: #3498db;
            color: white;
        }

        .update-btn:hover {
            background-color: #2980b9;
        }

        .delete-btn {
            background-color: #e74c3c;
            color: white;
        }

        .delete-btn:hover {
            background-color: #c0392b;
        }
    </style>
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this turf?");
        }
    </script>
</head>
<body>
    <h1>Manage Turfs</h1>
    <table>
        <tr>
            <th>Turf ID</th>
            <th>Turf Name</th>
            <th>Owner ID</th>
            <th>Location</th>
            <th>Price Per Slot</th>
            <th>Duration Per Slot</th>
            <th>Capacity</th>
            <th>Turf Type</th>
            <th>Operating Hours</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <form method="POST" action="">
                    <td><?php echo $row['turf_id']; ?></td>
                    <td>
                        <input type="text" name="turf_name" value="<?php echo $row['turf_name']; ?>">
                    </td>
                    <td><?php echo $row['owner_id']; ?></td>
                    <td>
                        <input type="text" name="location" value="<?php echo $row['location']; ?>">
                    </td>
                    <td>
                        <input type="number" name="price_per_slot" step="0.01" value="<?php echo $row['price_per_slot']; ?>">
                    </td>
                    <td>
                        <input type="number" name="slot_duration" step="0.1" value="<?php echo $row['slot_duration']; ?>">
                    </td>
                    <td>
                        <input type="number" name="capacity" value="<?php echo $row['capacity']; ?>">
                    </td>
                    <td><?php echo $row['turf_type']; ?></td>
                    <td>
                        <input type="text" name="operating_hours" value="<?php echo $row['operating_hours']; ?>">
                    </td>
                    <td>
                        <input type="hidden" name="turf_id" value="<?php echo $row['turf_id']; ?>">
                        <button type="submit" name="update" class="button update-btn">Update</button>
                        <button type="submit" name="delete" class="button delete-btn" onclick="return confirmDelete();">Delete</button>
                    </td>
                </form>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
