<?php
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "turf";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch bookings for the logged-in user
$user_id = $_SESSION['user_id'];
$sql = "
    SELECT 
        b.booking_id,
        t.turf_name, 
        t.location, 
        b.time_slot, 
        b.status, 
        b.created_at 
    FROM bookings b
    JOIN turfs t ON b.turf_id = t.turf_id
    WHERE b.user_id = '$user_id'
    ORDER BY b.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turf Booking History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }
        .history-item {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .history-item h2 {
            margin-top: 0;
            color: #333;
        }
        .history-item p {
            margin: 5px 0;
            color: #666;
        }
        .pay-now-btn {
            padding: 10px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .pay-now-btn:hover {
            background-color: #45a049;
        }
        .countdown {
            font-size: 14px;
            color: #e53935;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Your Turf Booking History</h1>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="history-item" id="booking-<?php echo $row['booking_id']; ?>">
                    <h2><?php echo htmlspecialchars($row['turf_name']); ?></h2>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                    <p><strong>Time Slot:</strong> <?php echo htmlspecialchars($row['time_slot']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($row['status']); ?></p>
                    <p><strong>Booked At:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
                    <?php if ($row['status'] === 'hold'): ?>
                        <button class="pay-now-btn" onclick="payNow(<?php echo $row['booking_id']; ?>)">Pay Now</button>
                        <p class="countdown" id="countdown-<?php echo $row['booking_id']; ?>"></p>
                        <script>
                            startCountdown(<?php echo $row['booking_id']; ?>, new Date("<?php echo date('Y-m-d H:i:s', strtotime($row['created_at'] . ' + 1 hour')); ?>").getTime());
                        </script>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No bookings found.</p>
        <?php endif; ?>

    </div>

    <script>
        function payNow(bookingId) {
            // Implement payment logic here
            alert('Payment functionality coming soon!');
        }

        function startCountdown(bookingId, endTime) {
            const countdownElement = document.getElementById(`countdown-${bookingId}`);
            const interval = setInterval(() => {
                const now = new Date().getTime();
                const distance = endTime - now;

                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                countdownElement.innerText = `Time left: ${hours}h ${minutes}m ${seconds}s`;

                if (distance < 0) {
                    clearInterval(interval);
                    countdownElement.innerText = 'Booking expired';
                    cancelBooking(bookingId);
                }
            }, 1000);
        }

        function cancelBooking(bookingId) {
            fetch('cancel_booking.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ booking_id: bookingId })
            }).then(response => response.json())
              .then(data => {
                  if (data.success) {
                      document.getElementById(`booking-${bookingId}`).remove();
                  } else {
                      alert('Failed to cancel the booking. Please try again.');
                  }
              });
        }

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php if ($row['status'] === 'hold'): ?>
                    const endTime = new Date('<?php echo date('Y-m-d H:i:s', strtotime($row['created_at'] . ' +1 hour')); ?>').getTime();
                    startCountdown(<?php echo $row['booking_id']; ?>, endTime);
                <?php endif; ?>
            <?php endwhile; ?>
        <?php endif; ?>
    </script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
