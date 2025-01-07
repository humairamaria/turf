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

// Your existing PHP code for handling POST requests remains the same...
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
