<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Your Turf</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            overflow-x: hidden;
            background: #fff;
            min-height: 100vh;
        }

        #header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 20px 100px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
        }

        #header .logo {
            color:rgb(9, 150, 98);
            font-weight: 700;
            font-size: 2.5em;
            text-decoration: none;
            transition: 0.3s;
        }

        #header .logo:hover {
            transform: scale(1.05);
        }

        #header ul {
            display: flex;
            gap: 20px;
            list-style: none;
        }

        .nav-button {
            padding: 10px 25px;
            background: #09962a;
            color: #fff;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .nav-button:hover {
            background: #0e6c24;
            transform: scale(1.05);
        }

        .nav-button.active {
            background: #0e6c24;
        }

        section {
        position: relative;
        width: 100%;
        min-height: 35vh;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        background: transparent; /* Remove background image */
        padding-top: 110px;
        z-index: 2; /* Ensure content appears above video */
        margin-bottom: 0; /* Remove bottom margin */
        padding-bottom: 0; /* Remove bottom padding */
        }

        #text {
            color: #0e6c24;
            font-size: 5vw;
            text-align: center;
            line-height: 1.2;
            margin-bottom: 30px;
            margin-top: -100px; /* Added to move text higher */
        }

        #text span {
            display: block;
            font-size: 0.5em;
            font-weight: 400;
        }
        .content {
        text-align: center;
        }

        #btn {
            display: inline-block;
            padding: 15px 40px;
            background: #09962a;
            color: #fff;
            font-size: 1.2em;
            font-weight: 500;
            border-radius: 30px;
            text-decoration: none;
            transition: all 0.3s ease;
            margin-left: 50px;
        }

        #btn:hover {
            background: #0e6c24;
            transform: scale(1.05);
        }

        .sec {
            padding: 100px 100px;
            background: #fff;
        }

        .sec h2 {
            color: #09962a;
            font-size: 2.5em;
            margin-bottom: 20px;
            text-align: center;
        }

        .sec p {
            color: #333;
            font-size: 1.1em;
            line-height: 1.8;
            text-align: justify;
        }
        .contact-container {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            width: 350px;
            border-radius: 15px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            z-index: 1001;
            text-align: center;  /* Center align content */
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            color: #09962a;
            font-size: 24px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 50%;
        }

        .close-btn:hover {
            background: rgba(9, 150, 42, 0.1);
        }
        .contact-container h3 {
            color: #09962a;
            margin-bottom: 20px;
            font-size: 24px;
            text-align: center;
        }

        .contact-info {
            color: #333;
            font-size: 1.1em;
            line-height: 2;
            margin-top: 10px;
            text-align: center;
        }
        .instagram-section {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #09962a;
        }

        .instagram-section h4 {
            color: #09962a;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .instagram-links a {
            color: #333;
            text-decoration: none;
            display: block;
            line-height: 2;
            transition: color 0.3s ease;
        }

        .instagram-links a:hover {
            color: #09962a;
        }
        /* Add these styles in the <style> section */
        .video-container {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
            margin-top: 80px; /* Adjust based on header height */
        }

        .video-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .second-video-container {
            width: 100%;
            height: 600px;
            margin-top: 0; /* Remove top margin */
            padding: 0; /* Remove padding */
            display: flex;
            justify-content: center;
            align-items: center;
            background: transparent;
        }

        .second-video-container video {
            width: 80%;
            height: 100%;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

    </style>
</head>
<body>

<!-- Modify header section -->
    <header id="header">
        <a href="#" class="logo">Find Your Turf.</a>
        <ul>
            <li><form action="#" method="GET" style="display: inline;">
                <button type="submit" class="nav-button active">Home</button>
            </form></li>
            <li><form action="login.php" method="GET" style="display: inline;">
                <button type="submit" class="nav-button">Log In</button>
            </form></li>
            <li><form action="signup.php" method="GET" style="display: inline;">
                <button type="submit" class="nav-button">Sign In</button>
            </form></li>
            <li><form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="GET" style="display: inline;">
                <button type="submit" name="contact" class="nav-button">Contact</button>
            </form></li>
        </ul>
    </header>

    <!-- Add this after header -->
    <?php
    if(isset($_GET['contact'])) {
        echo '<div class="contact-container">
                <form action="' . $_SERVER['PHP_SELF'] . '" method="GET" style="display: inline;">
                    <button type="submit" class="close-btn">&times;</button>
                </form>
                <h3>Contact Information</h3>
                <div class="contact-info">
                    <h4>Phone Numbers</h4>
                    01747572247<br>
                    01876891502<br>
                    01915485110
                </div>
                <div class="instagram-section">
                    <h4>Instagram</h4>
                    <div class="instagram-links">
                        <a href="https://instagram.com/ifaz_07" target="_blank">@ifaz_07</a>
                        <a href="https://instagram.com/mir_upoma" target="_blank">@mir_upoma</a>
                        <a href="https://instagram.com/humairamariaa" target="_blank">@humairamariaa</a>
                    </div>
                </div>
            </div>';
    }
    ?>
    <div class="video-container">
        <video autoplay muted loop>
            <source src="pcis\homevid.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
    <section>
        <div class="content">
            <h2 id="text">
                <span>Easy To Book</span>
                YOUR OWN TURF
            </h2>
            <a href="#" id="btn">Explore</a>
        </div>
    </section>
    <div class="second-video-container">
        <video autoplay muted loop>
            <source src="pcis\homevidddd.mp4" type="video/mp4">
        </video>
    </div>
    <div class="sec">
        <h2>WELCOME TO FIND YOUR TURF</h2>
        <p>Your ultimate destination for discovering and booking the best turfs in town! Whether you're looking for a friendly game with your mates or a professional venue for a team event, we've got you covered. Our platform makes it easy to explore available turfs by location, capacity, and price. With our simple and intuitive booking system, you can reserve your spot in no time and get ready to play. At FindYourTurf, we believe in bringing people together through the love of the game—book your turf, gather your players, and let the fun begin!</p>
    </div>
</body>
</html>
