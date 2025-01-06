
<?php
session_start();
include('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $turf_id = $_POST['turf_id'] ?? null;
    $time_slot = $_POST['time_slot'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;
    $booking_date = date('Y-m-d');

    // Validate inputs
    if (!$turf_id || !$time_slot || !$user_id) {
        echo "<div class='error'>Missing required fields</div>";
        exit;
    }

    // Check if slot is already taken
    $check_sql = "SELECT id FROM bookings 
                  WHERE turf_id = ? 
                  AND booking_date = ? 
                  AND time_slot = ? 
                  AND status IN ('hold', 'reserved')";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("iss", $turf_id, $booking_date, $time_slot);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        echo "<div class='error'>This slot is no longer available</div>";
        exit;
    }

    // Insert booking with 'hold' status
    $insert_sql = "INSERT INTO bookings (turf_id, user_id, booking_date, time_slot, status) 
                   VALUES (?, ?, ?, ?, 'hold')";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("iiss", $turf_id, $user_id, $booking_date, $time_slot);
    
    if ($insert_stmt->execute()) {
        $booking_id = $insert_stmt->insert_id;

        // Insert payment details
        $payment_sql = "INSERT INTO payments (booking_id, payment_method, phone_number, transaction_id, amount, card_number, card_holder_name, expiry_date, cvc) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $payment_stmt = $conn->prepare($payment_sql);
        $payment_stmt->bind_param("isssissss", $booking_id, $payment_method, $phone_number, $transaction_id, $amount, $card_number, $card_holder_name, $expiry_date, $cvc);

        if ($payment_stmt->execute()) {
            // Update booking status to 'reserved' after payment
            $update_sql = "UPDATE bookings SET status = 'reserved', payment_status = 'paid' WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $booking_id);

            if ($update_stmt->execute()) {
                echo "Booking confirmed and payment processed successfully!";
            } else {
                echo "Error updating booking status: " . $update_stmt->error;
            }
        } else {
            echo "Error inserting payment details: " . $payment_stmt->error;
        }

        $payment_stmt->close();
    } else {
        echo "Error inserting new booking: " . $insert_stmt->error;
    }

    $check_stmt->close();
    $insert_stmt->close();
}

// Close the database connection
$conn->close();
?>
