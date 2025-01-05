<?php
require 'connect.php';
session_start();
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $role = trim($_POST['role']);

    if ($role === 'user') {
        $gender = trim($_POST['gender']);
        $age = intval($_POST['age']);

        $sql = "INSERT INTO users (first_name, last_name, username, password, email, contact, gender, age) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssssssi", $first_name, $last_name, $username, $password, $email, $contact, $gender, $age);
            if ($stmt->execute()) {
                $message = "<div class='message success'>Registration successful!</div>";
                $_SESSION['user_id'] = $stmt->insert_id;
                header("Location: user_dashboard.php");
                exit;
            } else {
                $message = "<div class='message error'>Registration failed: " . $stmt->error . "</div>";
            }
        }
    } else if ($role === 'owner') {
        $turf_name = trim($_POST['turf_name']);

        $sql = "INSERT INTO owners (first_name, last_name, username, password, email, contact, turf_name) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssssss", $first_name, $last_name, $username, $password, $email, $contact, $turf_name);
            if ($stmt->execute()) {
                $message = "<div class='message success'>Registration successful!</div>";
                $_SESSION['owner_id'] = $stmt->insert_id;
                header("Location: owner_dashboard.php");
                exit;
            } else {
                $message = "<div class='message error'>Registration failed: " . $stmt->error . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .signup-container {
            background-color: #fff;
            padding: 20px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
            text-align: center;
        }

        .signup-container h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .signup-container input, .signup-container select, .signup-container button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .signup-container button {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        .signup-container button:hover {
            background-color: #45a049;
        }

        .signup-container p {
            margin-top: 10px;
            color: #666;
        }

        .signup-container p a {
            color: #4CAF50;
            text-decoration: none;
        }

        .signup-container p a:hover {
            text-decoration: underline;
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .role-specific {
            display: none;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h1>Sign Up</h1>
        <?php echo $message; ?>
        <form action="signup.php" method="POST">
            <div class="role-selector">
                <label>
                    <input type="radio" name="role" value="user" checked> User
                </label>
                <label>
                    <input type="radio" name="role" value="owner"> Owner
                </label>
            </div>

            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Last Name" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="contact" placeholder="Contact" required>

            <!-- User specific fields -->
            <div id="user-fields" class="role-specific">
                <select name="gender" required>
                    <option value="">Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
                <input type="number" name="age" placeholder="Age">
            </div>

            <!-- Owner specific fields -->
            <div id="owner-fields" class="role-specific">
                <input type="text" name="turf_name" placeholder="Turf Name">
            </div>

            <button type="submit">Sign Up</button>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>

    <script>
        document.querySelectorAll('input[name="role"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.getElementById('user-fields').style.display = 
                    this.value === 'user' ? 'block' : 'none';
                document.getElementById('owner-fields').style.display = 
                    this.value === 'owner' ? 'block' : 'none';
                
                const userFields = document.querySelectorAll('#user-fields input, #user-fields select');
                const ownerFields = document.querySelectorAll('#owner-fields input');
                
                userFields.forEach(field => field.required = (this.value === 'user'));
                ownerFields.forEach(field => field.required = (this.value === 'owner'));
            });
        });

        document.querySelector('input[name="role"]:checked').dispatchEvent(new Event('change'));
    </script>
</body>
</html>
