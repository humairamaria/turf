<?php
session_start();
include 'connect.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];


$stmt = $conn->prepare("SELECT username, email, contact FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $contact);
$stmt->fetch();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $new_username = $_POST['username'];
        $new_email = $_POST['email'];
        $new_contact = $_POST['contact'];

       
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, contact = ? WHERE user_id = ?");
        $stmt->bind_param("sssi", $new_username, $new_email, $new_contact, $user_id);

        if ($stmt->execute()) {
            $_SESSION['success'] = "Profile updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update profile. Please try again.";
        }

        $stmt->close();
    }

    if (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        
        if (password_verify($current_password, $hashed_password)) {
          
            if ($new_password === $confirm_password) {
               
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
                $stmt->bind_param("si", $new_hashed_password, $user_id);

                if ($stmt->execute()) {
                    $_SESSION['success'] = "Password changed successfully.";
                } else {
                    $_SESSION['error'] = "Failed to change password. Please try again.";
                }

                $stmt->close();
            } else {
                $_SESSION['error'] = "New password and confirm password do not match.";
            }
        } else {
            $_SESSION['error'] = "Current password is incorrect.";
        }
    }

    header("Location: user_profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background-color: #4CAF50;
            color: #fff;
        }

        .dashboard-header h1 {
            font-size: 26px;
            margin: 0;
        }

        .dashboard-header .menu a {
            color: #fff;
            text-decoration: none;
            font-weight: bold;
            padding: 8px 16px;
            background-color: #007BFF;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .dashboard-header .menu a:hover {
            background-color: #0056b3;
        }

        .container {
            padding: 30px;
        }

        .form-section {
            background-color: #ffffff;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 500px;
            margin: 20px auto;
        }

        .form-section h3 {
            margin-top: 0;
            color: #333;
        }

        .form-section label {
            font-weight: bold;
            margin-bottom: 8px;
            display: block;
            color: #555;
        }

        .form-section input {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .form-section button {
            padding: 12px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s;
        }

        .form-section button:hover {
            background-color: #218838;
        }

        .toggle-password-btn {
            margin-top: 10px;
            padding: 12px;
            background-color: #ffc107;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            text-align: center;
            transition: background-color 0.3s;
        }

        .toggle-password-btn:hover {
            background-color: #e0a800;
        }

        .success {
            color: green;
            text-align: center;
            font-size: 14px;
            margin-top: 15px;
        }

        .error {
            color: red;
            text-align: center;
            font-size: 14px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="dashboard-header">
        <h1>User Profile</h1>
        <div class="menu">
            <a href="user_dashboard.php">Back to Dashboard</a>
        </div>
    </div>

    <div class="container">
        <!-- Update Profile Section -->
        <div class="form-section">
            <h3>Edit Profile</h3>
            <form id="update-profile-form" method="post">
                <label>Username:</label>
                <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
                <label>Contact:</label>
                <input type="text" name="contact" value="<?php echo htmlspecialchars($contact); ?>" required>
                <button type="submit" name="update_profile">Save Changes</button>
            </form>
            <button id="toggle-password-form" class="toggle-password-btn">Update Password</button>
        </div>

        <!-- Update Password Section (Initially Hidden) -->
        <div class="form-section" id="password-section" style="display: none;">
            <h3>Update Password</h3>
            <form id="update-password-form" method="post">
                <label>Current Password:</label>
                <input type="password" name="current_password" required>
                <label>New Password:</label>
                <input type="password" name="new_password" required>
                <label>Confirm New Password:</label>
                <input type="password" name="confirm_password" required>
                <button type="submit" name="update_password">Update Password</button>
            </form>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
    </div>

    <script>
        const togglePasswordFormButton = document.getElementById('toggle-password-form');
        const passwordSection = document.getElementById('password-section');

        togglePasswordFormButton.addEventListener('click', () => {
            passwordSection.style.display = passwordSection.style.display === 'none' ? 'block' : 'none';
        });
    </script>
</body>
</html>
