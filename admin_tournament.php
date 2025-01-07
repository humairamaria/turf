<?php
session_start();
include('connect.php');
require 'vendor/autoload.php';

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
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .requests-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
        }

        .requests-table th {
            background-color: #3498db;
            color: #fff;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }

        .requests-table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
        }

        .requests-table tr:last-child td {
            border-bottom: none;
        }

        .requests-table tr:hover {
            background-color: #f8f9fa;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .accept-btn, .decline-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s ease;
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

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pending {
            background-color: #f1c40f;
            color: #fff;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #34495e;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-bottom: 20px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #2c3e50;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .description-cell {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .requests-table {
                display: block;
                overflow-x: auto;
            }

            .action-buttons {
                flex-direction: column;
            }

            .accept-btn, .decline-btn {
                width: 100%;
                margin: 2px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>
        <h1>Tournament Requests</h1>

        <?php if(mysqli_num_rows($result) > 0): ?>
            <table class="requests-table">
                <thead>
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
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['request_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['turf_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['owner_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['tournament_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                            <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                            <td class="description-cell"><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>
                                <form method="POST" class="action-buttons">
                                    <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($row['request_id']); ?>">
                                    <button type="submit" name="accept" class="accept-btn">Accept</button>
                                    <button type="submit" name="decline" class="decline-btn">Decline</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">
                No pending tournament requests at this time.
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Optional: Add confirmation before accepting/declining
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const action = e.submitter.name === 'accept' ? 'accept' : 'decline';
                if (!confirm(`Are you sure you want to ${action} this tournament request?`)) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
