<?php
session_start();
include 'connect.php';



if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'User') {
    header("Location: login.php");
    exit;
}
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f4f8;
            font-size: 18px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 30px;
            background-color:  #2196f3;
            color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .dashboard-header h1 {
            font-size: 28px;
            margin: 0;
        }

        .dashboard-header .menu {
            display: flex;
            gap: 20px;
        }

        .dashboard-header .menu a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            padding: 10px 15px;
            background-color: #6bb1f4;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .dashboard-header .menu a:hover {
            background-color: #529ee0;
        }

        .logout-btn {
            padding: 10px 15px;
            background-color: #f44336;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .logout-btn:hover {
            background-color: #d32f2f;
        }

        .container {
            margin: 20px;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin: 30px;
            padding: 30px;
            background-color: #ffffff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .filters input, .filters select, .filters button {
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .filters button {
            background-color: #4caf50;
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .filters button:hover {
            background-color: #45a049;
        }
        .turfs-list {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            padding: 30px;
        }

        .turf-card {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            width: 30%;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .turf-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .turf-card h3 {
            margin-top: 0;
            font-size: 22px;
            color: #4a90e2;
        }

        .turf-card p {
            margin: 10px 0;
            color: #555;
            font-size: 16px;
        }

        .turf-card button {
            padding: 12px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .turf-card button:hover {
            background-color: #45a049;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            text-align: left;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .modal-content h3 {
            margin-top: 0;
            font-size: 28px;
            color: #4a90e2;
        }

        .modal-content p {
            font-size: 18px;
            color: #555;
            margin: 15px 0;
        }
        .modal-content select, .modal-content input {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .modal-buttons {
            display: flex;
            justify-content: space-around;
            gap: 15px;
            margin-top: 30px;
        }

        .modal-buttons button {
            flex: 1;
            padding: 15px;
            font-size: 18px;
            font-weight: bold;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal-buttons button:nth-child(1) {
            background-color: #f44336;
        }

        .modal-buttons button:nth-child(2) {
            background-color: #4a90e2;
        }

        .modal-buttons button:nth-child(3) {
            background-color: #4caf50;
        }

        .modal-buttons button:hover {
            opacity: 0.9;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 20px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #333;
        }
        .back-btn, .submit-btn {
        padding: 10px 20px;
        font-size: 16px;
        font-weight: bold;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        }

        .back-btn {
        background-color: #f44336; /* Red color for back button */
        }

        .back-btn:hover {
        background-color: #d32f2f; /* Darker red on hover */
        }

        /* Payment method forms */
        #bkash-form, #nagad-form, #rocket-form, #bank-form {
            display: none;
        }

        input[name="payment_method"]:checked + #bkash-form,
        input[name="payment_method"]:checked + #nagad-form,
        input[name="payment_method"]:checked + #rocket-form,
        input[name="payment_method"]:checked + #bank-form {
            display: block;
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <h1>Welcome, <?php echo htmlspecialchars($username);?>!</h1>
        <div class="menu">
            <a href="turf_history.php">Booked Turfs</a>
            <a href="sharing.php">Sharing Options</a>
            <form method="POST" action="logout.php" style="display:inline;">
                <button type="submit" class="logout-btn">Logout</button>
            </form>
        </div>
    </header>
    <div class="dashboard-header">
        <div class="menu">
            <!-- Other menu options -->
            <a href="user_profile.php" class="btn-profile">My Profile</a>
        </div>
    </div>
    <div class="container">
        <form method="GET" class="filters">
            <input type="text" name="location" placeholder="Location">
            <input type="number" name="min_price" placeholder="Min Price">
            <input type="number" name="max_price" placeholder="Max Price">
            <input type="number" name="capacity" placeholder="Capacity">
            <select name="time_slot">
                <option value="">Any Time</option>
                <?php
                for ($i = 0; $i < 24; $i++) {
                    $start = sprintf("%02d:00", $i);
                    $end = sprintf("%02d:00", ($i + 1) % 24);
                    echo "<option value='$start-$end'>$start - $end</option>";
                }
                ?>
            </select>
            <button type="submit">Filter</button>
        </form>

        <section id="available">
            <h2>Available Turfs</h2>
            <div class="turfs-list">
                <?php
                $query = "SELECT t.turf_id, t.turf_name, t.location, tt.capacity, tt.price_per_slot, t.opening_time, t.closing_time
                          FROM turfs t
                          JOIN turf_types tt ON t.turf_id = tt.turf_id
                          WHERE t.status = 'accepted'";

                if (!empty($_GET['location'])) {
                    $location = "%" . $_GET['location'] . "%";
                    $query .= " AND t.location LIKE '$location'";
                }

                if (!empty($_GET['min_price'])) {
                    $min_price = (float)$_GET['min_price'];
                    $query .= " AND tt.price_per_slot >= $min_price";
                }

                if (!empty($_GET['max_price'])) {
                    $max_price = (float)$_GET['max_price'];
                    $query .= " AND tt.price_per_slot <= $max_price";
                }

                if (!empty($_GET['capacity'])) {
                    $capacity = (int)$_GET['capacity'];
                    $query .= " AND tt.capacity >= $capacity";
                }

                $result = $conn->query($query);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $turf_name = htmlspecialchars($row['turf_name']);
                        $location = htmlspecialchars($row['location']);
                        $price = htmlspecialchars($row['price_per_slot']);
                        $capacity = htmlspecialchars($row['capacity']);
                        $turf_id = htmlspecialchars($row['turf_id']);
                        $opening_time = htmlspecialchars($row['opening_time']);
                        $closing_time = htmlspecialchars($row['closing_time']);

                        echo "<div class='turf-card'>";
                        echo "<h3>{$turf_name}</h3>";
                        echo "<p><strong>Location:</strong> {$location}</p>";
                        echo "<p><strong>Price:</strong> {$price} BDT per slot</p>";
                        echo "<p><strong>Capacity:</strong> {$capacity} players</p>";
                        echo "<form method='POST' action='user_dashboard.php'>";
                        echo "<input type='hidden' name='turf_id' value='{$turf_id}'>";
                        echo "<input type='hidden' name='turf_name' value='{$turf_name}'>";
                        echo "<input type='hidden' name='location' value='{$location}'>";
                        echo "<input type='hidden' name='price' value='{$price}'>";
                        echo "<input type='hidden' name='capacity' value='{$capacity}'>";
                        echo "<input type='hidden' name='opening_time' value='{$opening_time}'>";
                        echo "<input type='hidden' name='closing_time' value='{$closing_time}'>";
                        echo "<button type='submit' name='see_details'>See Details</button>";
                        echo "</form>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>No turfs available matching your criteria.</p>";
                }
                ?>
            </div>
        </section>
    </div>

    <?php
if (isset($_POST['see_details'])) {
    $turf_id = htmlspecialchars($_POST['turf_id']);
    $turf_name = htmlspecialchars($_POST['turf_name']);
    $location = htmlspecialchars($_POST['location']);
    $price = htmlspecialchars($_POST['price']);
    $capacity = htmlspecialchars($_POST['capacity']);
    $opening_time = htmlspecialchars($_POST['opening_time']);
    $closing_time = htmlspecialchars($_POST['closing_time']);
?>
    <div id="details-container" class="modal" style="display: flex;">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('details-container').style.display='none'">&times;</span>
            <h3><?php echo $turf_name; ?></h3>
            <p><strong>Location:</strong> <?php echo $location; ?></p>
            <p><strong>Price:</strong> <?php echo $price; ?> BDT per slot</p>
            <p><strong>Capacity:</strong> <?php echo $capacity; ?> players</p>

            <!-- Single Consolidated Form -->
            <form method="POST" action="submit_payment.php" class="payment-form">
                <!-- Hidden Fields -->
                <input type="hidden" name="turf_id" value="<?php echo htmlspecialchars($turf_id); ?>">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">
                <input type="hidden" name="price" value="<?php echo htmlspecialchars($price); ?>">

                <!-- Booking Section -->
                <div class="booking-section">
                    <div class="form-group">
                        <label>Select Date:</label>
                        <select name="booking_date" required class="form-control">
                            <?php
                            for($i = 0; $i < 7; $i++) {
                                $date = date('Y-m-d', strtotime("+$i days"));
                                $display_date = date('d M Y', strtotime("+$i days"));
                                echo "<option value='{$date}'>{$display_date}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Select Time Slot:</label>
                        <select name="time_slot" required class="form-control">
                            <?php
                            $start_time = strtotime($opening_time);
                            $end_time = strtotime($closing_time);
                            
                            // Get booked slots
                            $booked_query = "SELECT time_slot FROM bookings 
                                           WHERE turf_id = ? 
                                           AND booking_date = CURDATE()
                                           AND status IN ('hold', 'reserved')";
                            $stmt = $conn->prepare($booked_query);
                            $stmt->bind_param("i", $turf_id);
                            $stmt->execute();
                            $booked_result = $stmt->get_result();
                            $booked_slots = [];
                            
                            while($row = $booked_result->fetch_assoc()) {
                                $booked_slots[] = $row['time_slot'];
                            }
                            
                            while ($start_time < $end_time) {
                                $slot_start = date('H:i', $start_time);
                                $slot_end = date('H:i', strtotime('+1 hour', $start_time));
                                $time_slot = "$slot_start-$slot_end";
                                
                                if (!in_array($time_slot, $booked_slots)) {
                                    echo "<option value='$time_slot'>$slot_start - $slot_end</option>";
                                }
                                $start_time = strtotime('+1 hour', $start_time);
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Payment Section -->
                <div class="payment-section">
                    <div class="form-group">
                        <label>Payment Method:</label>
                        <select name="payment_method" required onchange="togglePaymentFields(this.value)">
                            <option value="">Select Payment Method</option>
                            <option value="bkash">bKash</option>
                            <option value="nagad">Nagad</option>
                            <option value="rocket">Rocket</option>
                        </select>
                    </div>

                    <div id="payment-fields" class="payment-fields" style="display:none;">
                        <div class="form-group">
                            <label>Phone Number:</label>
                            <input type="text" name="phone_number" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Transaction ID:</label>
                            <input type="text" name="transaction_id" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Amount:</label>
                            <input type="number" name="amount" value="<?php echo htmlspecialchars($price); ?>" readonly required class="form-control">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="submit-btn">Confirm Booking</button>
                </div>
            </form>
        </div>
    </div>

<style>
/* Modal container adjustments */
.modal {
    display: none;
    position: fixed;
    top: 10%;
    left: 50%;
    transform: translateX(-50%);
    width: 90%;
    max-width: 400px;  /* Set a max width for better fit */
    height: auto;
    background-color: rgba(0, 0, 0, 0.5);
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

/* Modal content adjustments */
.modal-content {
    background-color: #fff;
    padding: 15px;  /* Reduced padding */
    border-radius: 10px;
    width: 100%;
    max-width: 100%;
    position: relative;
    overflow-y: auto;
    max-height: 80vh;  /* Ensure the form is shorter and fits the screen */
}

/* Form control adjustments */
.form-control {
    width: 100%;
    padding: 8px;  /* Reduced padding */
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;  /* Smaller font size */
    margin-bottom: 10px; /* Add margin to separate fields */
}

/* Submit button adjustments */
.submit-btn {
    background-color: #4CAF50;
    color: white;
    padding: 8px 16px;  /* Reduced padding */
    border: none;
    border-radius: 5px;
    cursor: pointer;
    width: 100%;
    font-size: 14px;
    margin-top: 10px;
}

/* Additional adjustments for small screens */
@media (max-width: 768px) {
    .modal {
        width: 95%;
    }
    .modal-content {
        padding: 10px;
    }
    .form-control {
        font-size: 12px;
    }
    .submit-btn {
        padding: 8px 14px;
        font-size: 12px;
    }
}

</style>

<script>
function togglePaymentFields(paymentMethod) {
    const paymentFields = document.getElementById('payment-fields');
    const inputs = paymentFields.querySelectorAll('input');
    
    if (paymentMethod) {
        paymentFields.style.display = 'block';
        inputs.forEach(input => {
            if (input.type !== 'hidden' && input.name !== 'amount') {
                input.required = true;
            }
        });
    } else {
        paymentFields.style.display = 'none';
        inputs.forEach(input => {
            input.required = false;
        });
    }
}

</script>

<?php } ?>
