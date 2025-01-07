<?php
session_start();
include('connect.php');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Debug information
echo "Current Date and Time (UTC): " . date('Y-m-d H:i:s') . "<br>";
echo "Current User's Login: " . ($_SESSION['username'] ?? 'Guest') . "<br>";
echo "<pre>POST Data: ";
print_r($_POST);
echo "</pre>";

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
        die("<div class='error'>Missing required fields: " . 
            (!$turf_id ? 'turf_id ' : '') .
            (!$user_id ? 'user_id ' : '') .
            (!$booking_date ? 'booking_date ' : '') .
            (!$time_slot ? 'time_slot' : '') . "</div>");
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
            throw new Exception("Prepare statement failed: " . $conn->error);
        }

        $check_stmt->bind_param("iss", $turf_id, $booking_date, $time_slot);
        
        if (!$check_stmt->execute()) {
            throw new Exception("Check execution failed: " . $check_stmt->error);
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
            throw new Exception("Prepare insert statement failed: " . $conn->error);
        }

        $insert_stmt->bind_param("iiss", $turf_id, $user_id, $booking_date, $time_slot);
        
        if (!$insert_stmt->execute()) {
            throw new Exception("Insert execution failed: " . $insert_stmt->error);
        }
        
        $booking_id = $insert_stmt->insert_id;

        // If payment details are provided
        if ($payment_method && $phone_number && $transaction_id && $amount) {
            $payment_sql = "INSERT INTO payments (booking_id, payment_method, phone_number, 
                                                transaction_id, amount) 
                           VALUES (?, ?, ?, ?, ?)";
                           
            $payment_stmt = $conn->prepare($payment_sql);
            
            if ($payment_stmt === false) {
                throw new Exception("Prepare payment statement failed: " . $conn->error);
            }

            $payment_stmt->bind_param("isssd", $booking_id, $payment_method, $phone_number, 
                                             $transaction_id, $amount);
            
            if (!$payment_stmt->execute()) {
                throw new Exception("Payment insert failed: " . $payment_stmt->error);
            }

            // Update booking status
            $update_sql = "UPDATE bookings SET status = 'reserved' WHERE booking_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            
            if ($update_stmt === false) {
                throw new Exception("Prepare update statement failed: " . $conn->error);
            }

            $update_stmt->bind_param("i", $booking_id);
            
            if (!$update_stmt->execute()) {
                throw new Exception("Update status failed: " . $update_stmt->error);
            }
        }

        // Commit transaction
        $conn->commit();
        echo "<div class='success'>Booking confirmed successfully!</div>";

    } catch (Exception $e) {
        // Rollback on error
        if ($conn->connect_errno === 0) {
            $conn->rollback();
        }
        echo "<div class='error'>" . $e->getMessage() . "</div>";
    } finally {
        // Close statements properly
        if (isset($check_stmt) && $check_stmt !== false) $check_stmt->close();
        if (isset($insert_stmt) && $insert_stmt !== false) $insert_stmt->close();
        if (isset($payment_stmt) && $payment_stmt !== false) $payment_stmt->close();
        if (isset($update_stmt) && $update_stmt !== false) $update_stmt->close();
        if ($conn) $conn->close();
    }
}
?>

<style>
.error {
    background-color: #ffebee;
    color: #c62828;
    padding: 15px;
    margin: 20px 0;
    border-radius: 4px;
    border: 1px solid #ffcdd2;
    font-family: Arial, sans-serif;
    font-size: 14px;
}

.success {
    background-color: #e8f5e9;
    color: #2e7d32;
    padding: 15px;
    margin: 20px 0;
    border-radius: 4px;
    border: 1px solid #c8e6c9;
    font-family: Arial, sans-serif;
    font-size: 14px;
}
</style>
