<?php
session_start();

// Check if the required data from index.php is present
if (!isset($_SESSION['key']) || !isset($_COOKIE['user_key'])) {
    // Redirect to index.php if the required data is not present
    header("Location: ./../Anasayfa/anasayfa.php");
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




$total_price = 0;
$cart_empty = true;
if ($user_logged_in) {
    $userId = $_SESSION['user_id'];
    $cart_sql = "SELECT uc.quantity, u.price FROM urunler_hesap_cart uc JOIN urunler u ON uc.product_id = u.id WHERE uc.user_id = ?";
    $cart_stmt = $conn->prepare($cart_sql);
    $cart_stmt->bind_param("i", $userId);
    $cart_stmt->execute();
    $cart_items = $cart_stmt->get_result();

    if ($cart_items->num_rows > 0) {
        $cart_empty = false;
        while ($item = $cart_items->fetch_assoc()) {
            $total_price += $item['price'] * $item['quantity'];
        }
    }
    $cart_stmt->close();
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && $user_logged_in) {
    if (isset($_POST['confirm-purchase'])) {
        if ($cart_empty) {
            $cart_empty_error = "Your cart is empty!";
        } else {
            $address = $_POST['address'];
            $card_number = $_POST['card_number'];
            $cvc = $_POST['cvc'];
            $userId = $_SESSION['user_id'];
            $delivery_code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

            $cart_sql = "SELECT uc.*, u.price FROM urunler_hesap_cart uc JOIN urunler u ON uc.product_id = u.id WHERE uc.user_id = ?";
            $cart_stmt = $conn->prepare($cart_sql);
            $cart_stmt->bind_param("i", $userId);
            $cart_stmt->execute();
            $cart_items = $cart_stmt->get_result();

            $total_price = 0;
            while ($item = $cart_items->fetch_assoc()) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                $total_price += $item['price'] * $quantity;
                $cart_data_sql = "INSERT INTO cart_data (user_id, product_id, quantity, address, card_number, cvc, delivery_code) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $cart_data_stmt = $conn->prepare($cart_data_sql);
                $cart_data_stmt->bind_param("iiissss", $userId, $product_id, $quantity, $address, $card_number, $cvc, $delivery_code);
                $cart_data_stmt->execute();
            }

            // Clear the cart
            $clear_cart_sql = "DELETE FROM urunler_hesap_cart WHERE user_id = ?";
            $clear_cart_stmt = $conn->prepare($clear_cart_sql);
            $clear_cart_stmt->bind_param("i", $userId);
            $clear_cart_stmt->execute();

            $_SESSION['refresh_once'] = true;
            $_SESSION['delivery_code'] = $delivery_code;
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
}

$needsRefresh = false;
if (isset($_SESSION['refresh_once'])) {
    unset($_SESSION['refresh_once']);
    $needsRefresh = true;
}

$delivery_code = isset($_SESSION['delivery_code']) ? $_SESSION['delivery_code'] : '';


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sat.css">
    <script defer src="sat.js"></script>
    <title>Document</title>

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
        <div class="space"></div>

        <div class="confirm-cart">
            <h2>Confirm Your Cart</h2>

            <?php if ($cart_empty): ?>
                <p style="color: red;">Your cart is empty.</p>
            <?php endif; ?>
            <p>Total Price: <?php echo $total_price; ?> USD</p>
            <form method="POST" onsubmit="return validateForm()">
                <label for="address">Address:</label>
                <input type="text" placeholder="Address" id="address" name="address" required>

                <br>
                <label for="card_number">Card Number:</label>
                <input type="text" placeholder="Card Number" id="card_number" name="card_number" maxlength="19"
                    required>
                <label for="cvc">CVC:</label>
                <input placeholder="CVC" type="text" id="cvc" name="cvc" required maxlength="3">
                <button type="submit" name="confirm-purchase" <?php if ($cart_empty)
                    echo 'disabled'; ?>>Confirm
                    Purchase</button>

                <p class="cartsend">Return to <a href="./../Cart/hesap.php">Cart</a></p>
            </form>
            <?php if ($delivery_code): ?>
                <p>Your last delivery code is: <strong><?php echo $delivery_code; ?></strong></p>
            <?php endif; ?>
        </div>

        <script>



            // Allow only numbers in CVC input
            document.getElementById('cvc').addEventListener('keypress', function (e) {
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault(); // Prevent any non-numeric input
                }
            });

            document.getElementById('cvc').addEventListener('input', function (e) {
                e.target.value = e.target.value.replace(/\D/g, ''); // Remove all non-digits
            });

            // Validate CVC
            function validateForm() {
                var cvc = document.getElementById('cvc').value;
                var cvcRegex = /^[0-9]{3}$/;

                if (!cvcRegex.test(cvc)) {
                    alert('CVC must be exactly 3 digits.');
                    return false;
                }

                // Validate and format card number
                var cardNumber = document.getElementById('card_number').value.replace(/\s+/g, '');
                var cardNumberRegex = /^[0-9]{16}$/;
                if (!cardNumberRegex.test(cardNumber)) {
                    alert('Card number must be exactly 16 digits.');
                    return false;
                }
                document.getElementById('card_number').value = cardNumber.replace(/(\d{4})(?=\d)/g, '$1 ');

                return true;
            }


            // Allow only numbers in card number input and format it
            document.getElementById('card_number').addEventListener('input', function (e) {
                var value = e.target.value.replace(/\D/g, ''); // Remove all non-digits
                if (value.length > 16) {
                    value = value.slice(0, 16); // Restrict to 16 digits
                }
                e.target.value = value.replace(/(\d{4})(?=\d)/g, '$1 '); // Add a space every 4 digits
            });

            document.addEventListener("DOMContentLoaded", function () {
                if (<?php echo json_encode($needsRefresh); ?>) {
                    window.location.reload(true);
                }
            });
        </script>


        <script>
            <script defer src="sat.js"></script>
        </script>
</body>

</html>