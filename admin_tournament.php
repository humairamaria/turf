<?php
session_start();
include('connect.php');
require 'vendor/autoload.php'; // Load Composer's autoloader for PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Ensure admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_id = mysqli_real_escape_string($conn, $_POST['request_id']); // Secure input

    if (isset($_POST['accept'])) {
        // Fetch request details
        $query = "SELECT * FROM tournament_requests WHERE request_id = '$request_id'";
        $result = mysqli_query($conn, $query);

        if ($row = mysqli_fetch_assoc($result)) {
            // Insert into tournaments table
            $insert_query = "INSERT INTO tournaments (turf_id, tournament_name, start_date, end_date, description)
                             VALUES ('{$row['turf_id']}', '{$row['tournament_name']}', '{$row['start_date']}', '{$row['end_date']}', '{$row['description']}')";
            mysqli_query($conn, $insert_query);

            // Update request status
            $update_query = "UPDATE tournament_requests SET status = 'accepted' WHERE request_id = '$request_id'";
            mysqli_query($conn, $update_query);

            // Send emails to users
            $users_query = "SELECT email FROM users";
            $users_result = mysqli_query($conn, $users_query);

            $mail = new PHPMailer(true);

            try {
                // SMTP server configuration
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'findyourturf00@gmail.com'; // Your email
                $mail->Password = 'gjkz drhj wnlk yavc'; // Your app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('findyourturf00@gmail.com', 'FindYourTurf');
                $mail->isHTML(true);
                $mail->Subject = "New Tournament Announcement: {$row['tournament_name']}";
                $mail->Body = "
                    <h3>A new tournament is coming up!</h3>
                    <p><strong>Tournament Name:</strong> {$row['tournament_name']}</p>
                    <p><strong>Turf ID:</strong> {$row['turf_id']}</p>
                    <p><strong>Start Date:</strong> {$row['start_date']}</p>
                    <p><strong>End Date:</strong> {$row['end_date']}</p>
                    <p><strong>Description:</strong> {$row['description']}</p>
                    <p>Don't miss it!</p>
                ";

                // Send email to each user
                while ($user = mysqli_fetch_assoc($users_result)) {
                    $mail->addAddress($user['email']); // Add recipient
                }

                $mail->send();
                echo "<script>alert('Tournament accepted and emails sent to users!');</script>";
            } catch (Exception $e) {
                echo "<script>alert('Tournament accepted, but email sending failed: {$mail->ErrorInfo}');</script>";
            }
        } else {
            echo "<script>alert('Invalid request ID.');</script>";
        }
    }

    if (isset($_POST['decline'])) {
        // Update request status to declined
        $update_query = "UPDATE tournament_requests SET status = 'declined' WHERE request_id = '$request_id'";
        mysqli_query($conn, $update_query);
        echo "<script>alert('Tournament request declined!');</script>";
    }
}

// Fetch pending tournament requests
$query = "SELECT * FROM tournament_requests WHERE status = 'pending'";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Tournament Requests</title>
</head>
<body>
    <h1>Tournament Requests</h1>
    <table border="1">
        <tr>
            <th>Request ID</th>
            <th>Turf ID</th>
            <th>Owner ID</th>
            <th>Tournament Name</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><?php echo $row['request_id']; ?></td>
                <td><?php echo $row['turf_id']; ?></td>
                <td><?php echo $row['owner_id']; ?></td>
                <td><?php echo $row['tournament_name']; ?></td>
                <td><?php echo $row['start_date']; ?></td>
                <td><?php echo $row['end_date']; ?></td>
                <td><?php echo $row['description']; ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                        <button type="submit" name="accept">Accept</button>
                        <button type="submit" name="decline">Decline</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
