<?php
session_start();
include('connect.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Debug information
$current_datetime = date('Y-m-d H:i:s');
$current_user = $_SESSION['username'] ?? 'Guest';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $turf_id = $_POST['turf_id'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;
    $booking_date = $_POST['booking_date'] ?? null;
    $time_slot = $_POST['time_slot'] ?? null;
    $payment_method = $_POST['payment_method'] ?? null;
    $phone_number = $_POST['phone_number'] ?? null;
    $transaction_id = $_POST['transaction_id'] ?? null;
    $amount = $_POST['amount'] ?? null;

    // Validate required fields
    if (!$turf_id || !$user_id || !$booking_date || !$time_slot) {
        echo "<div class='message-container error'>
                <div class='message-text'>Missing required fields</div>
                <button onclick='window.location.href=\"user_dashboard.php\"' class='back-btn'>Back to Dashboard</button>
              </div>";
        exit;
    }

    try {
        // Start transaction
        $conn->begin_transaction();

        // Check if slot is already taken
        $check_sql = "SELECT booking_id FROM bookings 
                      WHERE turf_id = ? 
                      AND booking_date = ? 
                      AND time_slot = ? 
                      AND status IN ('hold', 'reserved')";
                      
        $check_stmt = $conn->prepare($check_sql);
        
        if ($check_stmt === false) {
            throw new Exception("Database error occurred");
        }

        $check_stmt->bind_param("iss", $turf_id, $booking_date, $time_slot);
        
        if (!$check_stmt->execute()) {
            throw new Exception("Error checking availability");
        }
        
        $check_result = $check_stmt->get_result();
        if ($check_result->num_rows > 0) {
            throw new Exception("This time slot is already booked");
        }

        // Insert booking
        $insert_sql = "INSERT INTO bookings (turf_id, user_id, booking_date, time_slot, status) 
                       VALUES (?, ?, ?, ?, 'hold')";
                       
        $insert_stmt = $conn->prepare($insert_sql);
        
        if ($insert_stmt === false) {
            throw new Exception("Database error occurred");
        }

        $insert_stmt->bind_param("iiss", $turf_id, $user_id, $booking_date, $time_slot);
        
        if (!$insert_stmt->execute()) {
            throw new Exception("Error creating booking");
        }
        
        $booking_id = $insert_stmt->insert_id;

        // If payment details are provided
        if ($payment_method && $phone_number && $transaction_id && $amount) {
            $payment_sql = "INSERT INTO payments (booking_id, payment_method, phone_number, 
                                                transaction_id, amount) 
                           VALUES (?, ?, ?, ?, ?)";
                           
            $payment_stmt = $conn->prepare($payment_sql);
            
            if ($payment_stmt === false) {
                throw new Exception("Database error occurred");
            }

            $payment_stmt->bind_param("isssd", $booking_id, $payment_method, $phone_number, 
                                             $transaction_id, $amount);
            
            if (!$payment_stmt->execute()) {
                throw new Exception("Error processing payment");
            }

            // Update booking status
            $update_sql = "UPDATE bookings SET status = 'reserved' WHERE booking_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            
            if ($update_stmt === false) {
                throw new Exception("Database error occurred");
            }

            $update_stmt->bind_param("i", $booking_id);
            
            if (!$update_stmt->execute()) {
                throw new Exception("Error updating booking status");
            }
        }

        // Commit transaction
        $conn->commit();
        
        echo "<div class='message-container success'>
                <div class='message-text'>Booking confirmed successfully!</div>
                <button onclick='window.location.href=\"user_dashboard.php\"' class='back-btn'>Back to Dashboard</button>
              </div>";

    } catch (Exception $e) {
        // Rollback on error
        if ($conn && $conn->connect_errno === 0) {
            $conn->rollback();
        }
        
        echo "<div class='message-container error'>
                <div class='message-text'>" . htmlspecialchars($e->getMessage()) . "</div>
                <button onclick='window.location.href=\"user_dashboard.php\"' class='back-btn'>Back to Dashboard</button>
              </div>";
    } finally {
        // Close statements properly
        if (isset($check_stmt) && $check_stmt !== false) $check_stmt->close();
        if (isset($insert_stmt) && $insert_stmt !== false) $insert_stmt->close();
        if (isset($payment_stmt) && $payment_stmt !== false) $payment_stmt->close();
        if (isset($update_stmt) && $update_stmt !== false) $update_stmt->close();
        if (isset($conn)) $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status</title>
    <style>
    .message-container {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
        z-index: 1000;
        min-width: 300px;
        background-color: white;
    }

    .error.message-container {
        background-color: #ffebee;
        color: #c62828;
        border: 1px solid #ffcdd2;
    }

    .success.message-container {
        background-color: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #c8e6c9;
    }

    .message-text {
        font-family: Arial, sans-serif;
        font-size: 16px;
        margin-bottom: 15px;
    }

    .back-btn {
        background-color: #2196f3;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .back-btn:hover {
        background-color: #1976d2;
    }

    .overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }
    </style>
</head>
<body>
    <div class="overlay"></div>
</body>
</html>
