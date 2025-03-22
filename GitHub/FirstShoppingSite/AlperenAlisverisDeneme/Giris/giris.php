<?php
session_start();

if (!isset($_SESSION['status_message']) || !is_array($_SESSION['status_message'])) {
    $_SESSION['status_message'] = []; // Initialize status_message as an array
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kullanici_db";

$icon = '<i class="bx bx-home"></i>';
$status_message2 = "";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function clearSessionAndCookies()
{
    session_unset(); // Clear all session variables
    session_destroy(); // Destroy the session
    setcookie("user_key", "", time() - 3600, "/");
    setcookie("username", "", time() - 3600, "/");
}

$user_logged_in = false;
if (isset($_COOKIE['user_key'])) {
    $user_key = $_COOKIE['user_key'];
    $stmt = $conn->prepare("SELECT * FROM kullanıcı_verileri WHERE token = ?");
    $stmt->bind_param("s", $user_key);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows > 0) {
        $row = $user_result->fetch_assoc();
        $user_logged_in = true;
        $_SESSION['user_id'] = $row['id'];
    } else {
        clearSessionAndCookies();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login_username = $_POST['username'];
    $login_password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM kullanıcı_verileri WHERE username = ?");
    $stmt->bind_param("s", $login_username);
    $stmt->execute();
    $login_result = $stmt->get_result();

    if ($login_result->num_rows > 0) {
        $row = $login_result->fetch_assoc();
        if (password_verify($login_password, $row['password'])) {
            clearSessionAndCookies(); // Clear old session and cookies to prevent conflicts

            $token = bin2hex(random_bytes(16));
            $stmt = $conn->prepare("UPDATE kullanıcı_verileri SET token = ? WHERE id = ?");
            $stmt->bind_param("si", $token, $row['id']);
            if ($stmt->execute()) {
                session_start(); // Start a new session
                $_SESSION['key'] = $token;
                setcookie("user_key", $token, time() + (86400 * 30), "/");
                setcookie("username", $login_username, time() + (86400 * 30), "/");
                $_SESSION['user_id'] = $row['id'];
                header("Location: ./../Anasayfa/anasayfa.php");
                exit();
            } else {
                $status_message2 = "Failed! An error occurred: " . htmlspecialchars($stmt->error);
            }
        } else {
            $status_message2 = "Wrong password please try again.";
        }
    } else {
        $status_message2 = "Username not found please sign up.";
    }
}

$conn->close();
?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="giris.css">
    <script defer src="giris.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
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

                    <a href="javascript:void(0);" class="error-product-text"
                        onclick="alert('You must log in to access products!');">
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

        <div class="form-container">
            <h2>Login</h2>
            <form id="loginForm" action="" method="post">
                <!-- Username Input -->
                <div class="form-group">
                    <i class="bx bx-user"></i>
                    <input type="text" id="login_username" name="username" placeholder="Uername" required
                        autocomplete="username">
                    <div class="status-icon" id="login-username-status"></div>
                </div>

                <!-- Password Input -->
                <div class="form-group">
                    <i class="bx bx-lock-alt"></i>
                    <input type="password" id="login_password" name="password" placeholder="Password" required
                        autocomplete="current-password">
                    <i id="toggle-login-password" class="bx bx-show" onclick="toggleLoginPasswordVisibility()"></i>
                    <div class="status-icon" id="login-password-status"></div>
                </div>

                <!-- Submit Button -->
                <button type="submit" id="loginButton" class="submit-button">Sign in</button>


                <p>Don' have an account?<a href="./../Anasayfa/anasayfa.php"> Sign up </a>to get an account.</p>
            </form>


            <!-- Status Message -->
            <?php if (!empty($status_message2) && !$user_logged_in): ?>
                <div class="status-message">
                    <p><?php echo htmlspecialchars($status_message2); ?></p>
                </div>
            <?php endif; ?>
        </div>



    </div>
</body>

</html>