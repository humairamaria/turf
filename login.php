<?php
require 'connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // SQL to check users, admins, and owners based on the username
    $sql = "SELECT 'User' AS role, username, password,user_id as id FROM users WHERE username = ?
            UNION
            SELECT 'Admin' AS role, username, password,admin_id as id FROM  admins WHERE username = ?
            UNION
            SELECT 'Owner' AS role, username, password,owner_id as id FROM owners WHERE username = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }

    $stmt->bind_param("sss", $username, $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password, $row['password'])) {
            
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];
            

            // Redirect based on the role
            if ($row['role'] === 'Admin') {
                $_SESSION['admin_id'] = $row['id'];
                header("Location: admin_dashboard.php");
            } elseif ($row['role'] === 'User') {
                $_SESSION['user_id'] = $row['id'];
                header("Location: user_dashboard.php");
            } elseif ($row['role'] === 'Owner') {
                $_SESSION['owner_id'] = $row['id'];
                header("Location: owner_dashboard_login.php");
            }
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "No account found with that username.";
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Turf Website</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        /* General Reset and Body Styling */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('pcis/loginbg.jpg') no-repeat center center/cover;
            overflow: hidden;
    
        }

        /* Background Blur Effect */
        .background-blur {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
            filter: blur(3px);
        }

        /* Login Container Styling */
        .login-container {
            background: rgba(255, 255, 255, 0.16); /* Transparent white for a modern look */
            padding: 20px 30px;
            border-radius: 20px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 70%;
            max-width: 350px;
            animation: fadeIn 0.9s ease-in-out;
            transition:transform 0.3s , box-shadow 0.3s;
            
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Header Styling */
        .login-container h1 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color:rgb(255, 255, 255);
            font-weight: 600;
        }

        /* Input and Button Styling */
        .login-container input, .login-container button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 10px;
            box-sizing: border-box;
            font-size: 1rem;
        }

        .login-container input {
            background-color: #f9f9f9;
            border: 1px solid #ccc;
            font-family: 'Roboto', sans-serif;
        }

        .login-container input:focus {
            border-color: #1abc9c;
            outline: none;
            box-shadow: 0 0 8px rgba(26, 188, 156, 0.4);
        }

        .login-container button {
            background-color:rgb(57, 101, 61);
            color: #fff;
            font-weight: 600;
            font-family: 'Roboto', sans-serif;
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .login-container button:hover {
            background-color: #16a085;
            box-shadow: 0px 5px 15px rgba(26, 188, 156, 0.4);
        }

        /* Link Styling */
        .login-container p {
            margin: 15px 0 0;
            font-size: 0.9rem;
            color: #666;
        }

        .login-container p a {
            color: rgb(57, 101, 61);
            text-decoration: none;
            font-weight: 500;
        }

        .login-container p a:hover {
            text-decoration: underline;
        }

        /* Message Styling */
        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 0.9rem;
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
        .video-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .video-background video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .login-container {
            position: relative;
            z-index: 1;
            background: rgba(73, 67, 131, 0.9);
        }
    </style>
</head>
<body>
    <!-- Background Blur -->
    <img src="pcis/loginbg.jpg" alt="Background Image" class="background-blur">

    <div class="login-container">
        <h1>Welcome Back</h1>
        <?php if (isset($error)): ?>
            <p class="message error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
        </form>
    </div>
    <div class="video-background">
        <video autoplay muted loop>
            <source src="pcis/login.mp4" type="video/mp4">
        </video>
    </div>
</body>
</html>
