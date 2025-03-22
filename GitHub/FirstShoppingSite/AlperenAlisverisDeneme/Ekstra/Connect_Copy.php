/*
BURAYI SADECE KULLANICININ KAYITLIMI DEĞİLMİ OLUP OLMADIĞI KONTROL ETMEK İÇİN KOYDUM
*/




<?php
session_start();

// Check if the required data from index.php is present
if (!isset($_SESSION['key']) || !isset($_COOKIE['user_key'])) {
    // Redirect to index.php if the required data is not present
    header("Location: index.php");
    exit();
}

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
        $status_message = "Hoş geldin, " . $_COOKIE['username'] . "!";
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
        /* Add your styles here */
    </style>
</head>

<body>
    <div class="allclasses">
        <!-- Add your HTML content here -->
    </div>

    <script>
        // Add your JavaScript here
    </script>
</body>

</html>