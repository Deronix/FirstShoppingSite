/*
BURAYI SADECE KULLANICININ KAYITLIMI DEĞİLMİ OLUP OLMADIĞI KONTROL ETMEK İÇİN KOYDUM
*/




<?php
session_start();



$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kullanıcı_db";

$icon = '<i class="bx bx-home"></i>'; // Initialize the icon variable
$status_message = ""; // Initialize the status message variable

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to clear session and cookies
if (!function_exists('clearSessionAndCookies')) {
    function clearSessionAndCookies(): never
    {
        unset($_SESSION['key']);
        setcookie("user_key", "", time() - (86400 * 30), "/"); // Expire the cookie
        setcookie("username", "", time() - (86400 * 30), "/");
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page to clear cookies
        exit();
    }
}

// Check if user is logged in
$user_logged_in = false;
if (isset($_COOKIE['user_key'])) {
    $user_key = $_COOKIE['user_key'];
    $user_check_sql = "SELECT * FROM kullanıcı_verileri WHERE token='$user_key'";
    $user_result = $conn->query($user_check_sql);

    if ($user_result->num_rows > 0) {
        // User is logged in
        $user_logged_in = true;
        $status_message = "Welcome, " . $_COOKIE['username'] . "!";
    } else {
        // User data is not found, clear session and cookies
        clearSessionAndCookies();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        :root {
            /* Navbar Text and button colors */
            --orange: #ed8a34;
            /* Form colors */
            --frorange: #dd6300;
            /* Background color */
            --black: #1b1c1b;
            /* Text color */
            --white: #dbdbdb;
            /* Hover color */
            --hover: #ff7a0d;
        }

        /* General Styles */
        /* General Styles */
        body {
            margin: 0;
            font-family: "Helvetica Neue", Arial, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #272727;
            color: var(--white);
        }

        .navbar-link.linkofproducts {
            font-size: 1.8rem;
            text-align: center;
            place-items: center;
            justify-content: center;
            align-items: center;
        }

        .error-product-text {
            font-size: 1.8rem;
            text-align: center;
            place-items: center;
            justify-content: center;
            align-items: center;
        }

        /* Navbar Styles */
        .navbar {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            background-color: #1a1919;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 1vh 2vw;
            position: fixed;
            top: 0;
            width: 100%;
            box-sizing: border-box;
            height: 9vh;
            z-index: 1000;
            right: 0%;
            top: 0%;
        }

        .navbar div {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1vw;
        }

        .navbar div:first-child {
            justify-content: flex-start;
            grid-column: 1 / 2;
        }

        .navbar div:last-child {
            justify-content: flex-end;
            grid-column: 3 / 4;
        }

        .navbar a {
            text-decoration: none;
            color: var(--frorange);
            padding: 0.5vh 1vw;
            transition: color 0.3s ease-in-out;
        }

        .navbar a:hover {
            color: var(--hover);
        }

        /* Prevent horizontal overflow */
        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            overflow-x: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            place-items: center;

            min-height: 100vh;
        }

        /*/////////////////////////////*/

        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }

        .header,
        .footer {
            background: var(--black);
            color: var(--white);
            padding: 2vh 0;
            text-align: center;
            border-radius: 2vh;

        }

        .main-content {
            padding: 2vh;
            background: var(--black);

            border-radius: 2vh;

        }

        h1,
        h2 {
            color: var(--orange);
        }

        .section {
            margin-bottom: 20px;
        }

        a {
            color: var(--hover);
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
            color: var(--hover);
        }

        .footer {
            width: 100dvh;
        }

        .spacelarge {
            height: 16dvh;
            width: none;
        }

        .space {
            height: 12dvh;
            width: none;
        }

        .spacesmall {
            height: 3dvh;
            width: none;
        }
    </style>
</head>

<body>
    <div class="allclasses">
        <div class="navbar">
            <div>
                <!-- Home Link with Icon -->
                <a href="./../Anasayfa/anasayfa.php">
                    <?php echo "Pumpkin'Sway" . $icon; ?>
                </a>
                <?php if (isset($_COOKIE['user_key'])): ?>
                    <!-- Favorites Icon and Link to the favorites section in hesap.php -->
                    <a href="./../Favorites/favorite.php" class="navbar-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-heart">
                            <path
                                d="M12 21C12 21 18 16.9 18 12C18 9.5 16.2 7.3 14 7C12.8 7 12 8.4 12 8.4C12 8.4 11.2 7 10 7C7.8 7.3 6 9.5 6 12C6 16.9 12 21 12 21Z">
                            </path>
                        </svg>
                    </a>
                    <!-- Cart Icon and Link to hesap.php -->
                    <a href="./../Cart/hesap.php" class="navbar-link">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-shopping-cart">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2 9h11l2-9h4"></path>
                            <path d="M6 9h12l1 7H5l1-7z"></path>
                        </svg>
                    </a>
                <?php else: ?>
                    <!-- Favorites and Cart for Guests (Alert) -->
                    <a href="javascript:void(0);" onclick="alert('You must log in to access your favorites!');">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-heart">
                            <path
                                d="M12 21C12 21 18 16.9 18 12C18 9.5 16.2 7.3 14 7C12.8 7 12 8.4 12 8.4C12 8.4 11.2 7 10 7C7.8 7.3 6 9.5 6 12C6 16.9 12 21 12 21Z">
                            </path>
                        </svg>
                    </a>
                    <a href="javascript:void(0);" onclick="alert('You must log in to access your cart!');">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-shopping-cart">
                            <circle cx="9" cy="21" r="1"></circle>
                            <circle cx="20" cy="21" r="1"></circle>
                            <path d="M1 1h4l2 9h11l2-9h4"></path>
                            <path d="M6 9h12l1 7H5l1-7z"></path>

                        </svg>

                    </a>
                <?php endif; ?>
            </div>
            <div>
                <?php if (isset($_COOKIE['user_key'])): ?>
                    <!-- Products Icon and Link -->
                    <a href="./../Index/index.php" class="navbar-link linkofproducts">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-grid">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        Products
                    </a>
                <?php else: ?>
                    <a href="javascript:void(0);" class="error-product-text" onclick="alert('You must log in to access products!');">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="feather feather-grid">
                            <rect x="3" y="3" width="7" height="7"></rect>
                            <rect x="14" y="3" width="7" height="7"></rect>
                            <rect x="14" y="14" width="7" height="7"></rect>
                            <rect x="3" y="14" width="7" height="7"></rect>
                        </svg>
                        Products
                    </a>
                <?php endif; ?>
            </div>
            <div>
                <a href="./../1Place/place.php">Locations</a>
                <a href="./../1Contact/contact.php">Contact Us</a>
            </div>
        </div>
        <div class="space"></div>


        <div class="header">
            <h1>Take A Look At Our Locations</h1>
        </div>
        <div class="spacesmall"></div>

        <div class="container">
            <div class="main-content">
                <div class="section">
                    <h2>Places</h2>
                    <h3>Our classic location: </h3>
                    <li>Göztepe Vocational High School - Göztepe, İzmir</li>
                    </p>
                    <!-- Embedded Google Maps -->
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3126.8924780363136!2d27.092749127483383!3d38.39773507422303!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14bbdeb221381fff%3A0xdef21ec5e68fbba3!2sG%C3%B6ztepe%20Mesleki%20Ve%20Teknik%20Anadolu%20Lisesi!5e0!3m2!1sen!2str!4v1736695810330!5m2!1sen!2str"
                        width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <div class="section">
                        <h3>Here's a look at some of our other locations:</h3>
                        <ul>
                            <li>Hagia Sophia - Istanbul</li>
                            <li>Atakule Tower - Ankara</li>
                            <li>Konak Square - Izmir</li>
                            <li>Konyaaltı and Lara Beaches - Antalya</li>
                            <li>Uludağ - Bursa</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="spacesmall"></div>

        <div class="footer">
            <p>&copy; 2025 PumpkinSway. All rights reserved.</p>
        </div>







    </div>

    <script>
        // Add your JavaScript here
    </script>
</body>

</html>