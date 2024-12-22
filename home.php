<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
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

        .home-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 400px;
            text-align: center;
        }

        .home-container h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .home-container button {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        .home-container button:hover {
            background-color: #45a049;
        }

        .home-container p {
            margin-top: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="home-container">
        <h1>Welcome to Turf Booking</h1>
        <p>Please select your account type:</p>
        <a href="signup_user.php"><button>User Sign Up</button></a>
        <a href="signup_owner.php"><button>Owner Sign Up</button></a>
    </div>
</body>
</html>
