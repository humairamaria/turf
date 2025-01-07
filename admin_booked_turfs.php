<?php
session_start();
include('connect.php');

// Check if admin is logged in
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

// Handle deletion
if (isset($_POST['delete_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    try {
        // Start transaction
        $conn->begin_transaction();

        // Delete payment record first (due to foreign key constraint)
        $delete_payment = "DELETE FROM payments WHERE booking_id = ?";
        $stmt_payment = $conn->prepare($delete_payment);
        $stmt_payment->bind_param("i", $booking_id);
        $stmt_payment->execute();

        // Then delete booking record
        $delete_booking = "DELETE FROM bookings WHERE booking_id = ?";
        $stmt_booking = $conn->prepare($delete_booking);
        $stmt_booking->bind_param("i", $booking_id);
        $stmt_booking->execute();

        // Commit transaction
        $conn->commit();

        // Show success message
        echo "<script>alert('Booking deleted successfully!');</script>";
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollback();
        echo "<script>alert('Error deleting booking: " . $e->getMessage() . "');</script>";
    }
}

// Fetch all bookings ordered by newest first
$query = "SELECT b.booking_id, b.turf_id, b.user_id, b.booking_date, b.time_slot, 
                 b.status, b.created_at, t.turf_name, u.username, p.payment_method, 
                 p.amount, p.transaction_id
          FROM bookings b
          LEFT JOIN turfs t ON b.turf_id = t.turf_id
          LEFT JOIN users u ON b.user_id = u.user_id
          LEFT JOIN payments p ON b.booking_id = p.booking_id
          ORDER BY b.created_at DESC";

$result = $conn->query($query);

// Your existing notification queries remain the same
$tournament_request_query = "SELECT COUNT(*) AS pending_tournament_requests FROM tournament_requests WHERE status = 'pending'";
$tournament_request_result = $conn->query($tournament_request_query);
$pending_tournament_requests = $tournament_request_result->fetch_assoc()['pending_tournament_requests'];

$turf_request_query = "SELECT COUNT(*) AS pending_turf_requests FROM turfs WHERE status = 'pending'";
$turf_request_result = $conn->query($turf_request_query);
$pending_turf_requests = $turf_request_result->fetch_assoc()['pending_turf_requests'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Bookings</title>
    <!-- Your existing styles remain the same, just add these new styles -->
    <style>
        /* Your existing styles here... */

        /* New styles for delete button */
      /* General Styles */
body {
    font-family: Arial, sans-serif;
    background-color: #f4f7fc;
    margin: 0;
    padding: 0;
}

/* Main Content Styles */
.main-content {
    margin-left: 220px;
    padding: 20px;
    background-color: #fff;
}

.bookings-header h1 {
    font-size: 2rem;
    color: #333;
    margin-bottom: 20px;
}

/* Table Styles */
.bookings-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.bookings-table th, .bookings-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.bookings-table th {
    background-color: #4CAF50;
    color: white;
    font-size: 14px;
}

.bookings-table td {
    font-size: 14px;
}

.status-badge {
    padding: 5px 10px;
    border-radius: 4px;
    text-transform: capitalize;
}

.status-pending {
    background-color: #ffeb3b;
    color: #6c6c6c;
}

.status-reserved {
    background-color: #4caf50;
    color: white;
}

.status-cancelled {
    background-color: #f44336;
    color: white;
}

/* Search Bar Styles */
.search-bar {
    margin-bottom: 20px;
    text-align: right;
}

.search-bar input {
    padding: 8px;
    width: 250px;
    border-radius: 4px;
    border: 1px solid #ddd;
}

/* Delete Button Styles */
.delete-btn {
    background-color: #dc3545;
    color: white;
    border: none;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.delete-btn:hover {
    background-color: #c82333;
}

/* Confirmation Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border-radius: 5px;
    width: 80%;
    max-width: 400px;
    text-align: center;
}

.modal-buttons {
    margin-top: 20px;
}

.modal-buttons button {
    margin: 0 10px;
    padding: 8px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
}

.confirm-delete {
    background-color: #dc3545;
    color: white;
}

.cancel-delete {
    background-color: #6c757d;
    color: white;
}

/* Modal Button Hover Effects */
.confirm-delete:hover {
    background-color: #c82333;
}

.cancel-delete:hover {
    background-color: #5a6268;
}

/* Modal Close Button Styles */
.modal-content button {
    font-size: 16px;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .main-content {
        margin-left: 0;
    }

    .bookings-table th, .bookings-table td {
        font-size: 12px;
        padding: 8px;
    }

    .bookings-header h1 {
        font-size: 1.5rem;
    }

    .search-bar input {
        width: 100%;
        font-size: 14px;
    }
}

      

    </style>
</head>
<body>
    <!-- Your existing sidebar remains the same -->

    <!-- Main Content -->
    <div class="main-content">
        <div class="bookings-header">
            <h1>All Bookings</h1>
        </div>

        <div class="bookings-container">
            <div class="search-bar">
                <input type="text" id="searchInput" onkeyup="searchBookings()" placeholder="Search by username, turf name, or date...">
            </div>

            <table class="bookings-table" id="bookingsTable">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Turf Name</th>
                        <th>User</th>
                        <th>Date</th>
                        <th>Time Slot</th>
                        <th>Payment Method</th>
                        <th>Amount</th>
                        <th>Transaction ID</th>
                        <th>Status</th>
                        <th>Booked On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['booking_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['turf_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['booking_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['time_slot']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($row['amount']); ?> BDT</td>
                        <td><?php echo htmlspecialchars($row['transaction_id']); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                <?php echo htmlspecialchars($row['status']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($row['created_at']))); ?></td>
                        <td>
                            <button class="delete-btn" onclick="confirmDelete(<?php echo $row['booking_id']; ?>)">
                                Delete
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Confirm Deletion</h3>
            <p>Are you sure you want to delete this booking?</p>
            <div class="modal-buttons">
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="booking_id" id="deleteBookingId">
                    <button type="submit" name="delete_booking" class="confirm-delete">Yes, Delete</button>
                    <button type="button" class="cancel-delete" onclick="closeModal()">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    // Your existing search function remains the same

    function confirmDelete(bookingId) {
        document.getElementById('deleteModal').style.display = 'block';
        document.getElementById('deleteBookingId').value = bookingId;
    }

    function closeModal() {
        document.getElementById('deleteModal').style.display = 'none';
    }

    // Close modal if clicking outside
    window.onclick = function(event) {
        var modal = document.getElementById('deleteModal');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
    </script>
</body>
</html>
