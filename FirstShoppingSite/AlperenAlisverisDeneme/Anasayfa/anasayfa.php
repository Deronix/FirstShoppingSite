<?php
session_start();

if (!isset($_SESSION['status_message']) || !is_array($_SESSION['status_message'])) {
    $_SESSION['status_message'] = []; // Initialize status_message as an array
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kullanıcı_db";

$icon = '<i class="bx bx-home"></i>';
$status_message = "";

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function clearSessionAndCookies()
{
    unset($_SESSION['key']);
    setcookie("user_key", "", time() - (86400 * 30), "/");
    setcookie("username", "", time() - (86400 * 30), "/");
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$user_logged_in = false;
if (isset($_COOKIE['user_key'])) {
    $user_key = $_COOKIE['user_key'];
    $stmt = $conn->prepare("SELECT * FROM kullanıcı_verileri WHERE token = ?");
    $stmt->bind_param("s", $user_key);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user_logged_in = true;
        $row = $user_result->fetch_assoc();
        /* $status_message = "Welcome, " . htmlspecialchars($_COOKIE['username']) . "!";  */
        $_SESSION['user_id'] = $row['id'];
    } else {
        clearSessionAndCookies();
    }
}

$user_username = isset($_POST['username']) ? $_POST['username'] : '';
$user_email = isset($_POST['email']) ? $_POST['email'] : '';
$user_password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_ARGON2ID) : '';
$user_phone = isset($_POST['phone']) ? $_POST['phone'] : '';

$user_logged_in = isset($_SESSION['key']); // Example check for user session

if ($_SERVER["REQUEST_METHOD"] == "POST" && !$user_logged_in) {
    $stmt = $conn->prepare("SELECT * FROM kullanıcı_verileri WHERE email = ?");
    $stmt->bind_param("s", $user_email);
    $stmt->execute();
    $email_result = $stmt->get_result();

    $stmt = $conn->prepare("SELECT * FROM kullanıcı_verileri WHERE phone = ?");
    $stmt->bind_param("s", $user_phone);
    $stmt->execute();
    $phone_result = $stmt->get_result();

    $stmt = $conn->prepare("SELECT * FROM kullanıcı_verileri WHERE username = ?");
    $stmt->bind_param("s", $user_username);
    $stmt->execute();
    $username_result = $stmt->get_result();

    if ($email_result->num_rows > 0) {
        $status_message = "This email address is already in use. Please try another.";
    } elseif ($phone_result->num_rows > 0) {
        $status_message = "This phone number is already in use. Please try another.";
    } elseif ($username_result->num_rows > 0) {
        $status_message = "This username is already in use. Please try another.";
    } else {
        $token = bin2hex(random_bytes(16));
        $stmt = $conn->prepare("INSERT INTO kullanıcı_verileri (username, email, password, phone, token) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $user_username, $user_email, $user_password, $user_phone, $token);
        if ($stmt->execute()) {
            $_SESSION['key'] = $token;
            setcookie("user_key", $token, time() + (86400 * 30), "/");
            setcookie("username", $user_username, time() + (86400 * 30), "/");
            $status_message = "Success! Registration completed.";
            echo '<script>
                    setTimeout(function() {
                        location.reload(true);
                    }, 2000);
                </script>';
        } else {
            $status_message = "Failed! An error occurred during registration: " . htmlspecialchars($stmt->error);
        }
    }
}


$user_data = [];
$user_sql = "SELECT id, username, email, password, phone, token FROM kullanıcı_verileri WHERE id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
}










if ($_SERVER["REQUEST_METHOD"] == "POST" && $user_logged_in) {
    if (isset($_POST['delete_account'])) {
        // Delete account from the SQL database
        $stmt = $conn->prepare("DELETE FROM kullanıcı_verileri WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        if ($stmt->execute()) {
            $status_message = "Your account has been deleted successfully.";
            // Clear session and cookies
            session_unset();
            session_destroy();
            setcookie("user_key", "", time() - 3600, "/");
            setcookie("username", "", time() - 3600, "/");
            header("Location: ./../Index/index.php");

        } else {
            $status_message = "Failed to delete your account: " . htmlspecialchars($stmt->error);
        }
    } elseif (isset($_POST['erase_cookies'])) {
        // Clear session and cookies
        session_unset();
        session_destroy();
        setcookie("user_key", "", time() - 3600, "/");
        setcookie("username", "", time() - 3600, "/");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $username = $_POST['edit_username'] ?? $user_data['username'];
        $email = $_POST['edit_email'] ?? $user_data['email'];
        $phone = $_POST['edit_phone'] ?? $user_data['phone'];
        $new_password = $_POST['edit_password'] ? password_hash($_POST['edit_password'], PASSWORD_ARGON2ID) : $user_data['password'];

        $stmt = $conn->prepare("UPDATE kullanıcı_verileri SET username = ?, email = ?, phone = ?, password = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $username, $email, $phone, $new_password, $_SESSION['user_id']);

        if ($stmt->execute()) {
            $status_message = "Successfully updated information.";
            setcookie("username", $username, time() + (86400 * 30), "/");
            $_SESSION['refresh_once'] = true;
            header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
            exit();
        } else {
            $status_message = "Failed to update account: " . htmlspecialchars($stmt->error);
        }
    }
}

$needsRefresh = false;
if (isset($_SESSION['refresh_once'])) {
    unset($_SESSION['refresh_once']);
    $needsRefresh = true;
}






// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="anasayfa.css">
    <script defer src="anasayfa.js"></script>
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
</head>
<style>
    /* Style for password visibility toggle */
    .password-visibility {
        display: flex;
        align-items: center;
    }

    .password-visibility i {
        cursor: pointer;
        margin-left: 10px;
    }

    .password-visibility i:hover {
        color: #007bff;
    }

    /* Show the edit user data form only when the user is logged in */
    .edit-userdata-form {
        display: none;
    }

    <?php if ($user_logged_in): ?>
        .edit-userdata-form {
            display: block;
        }

    <?php endif; ?>
</style>

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
        <div class="container">
            <div class="form-container <?php if ($user_logged_in)
                echo 'hidden'; ?>">
                <h2>Sign Up</h2>
                <form id="userForm" action="" method="post">
                    <div class="form-group">
                        <i class="bx bx-user"></i>
                        <input type="text" id="username" name="username" placeholder="Username" required>
                        <div class="status-icon" id="username-status"></div>
                        <div class="info-tooltip">
                            <i class="bx bx-info-circle"></i>
                            <span class="tooltip-text">Username must be at least 3 characters</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <i class="bx bx-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="Email" required>
                        <div class="status-icon" id="email-status"></div>
                        <div class="info-tooltip">
                            <i class="bx bx-info-circle"></i>
                            <span class="tooltip-text">Enter a valid email address</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <i class="bx bx-lock-alt"></i>
                        <input type="password" id="password" name="password" placeholder="Password" required>
                        <i id="toggle-password" class="bx bx-show" onclick="togglePasswordVisibility()"></i>
                        <div class="status-icon" id="password-status"></div>
                        <div class="info-tooltip">
                            <i class="bx bx-info-circle"></i>
                            <span class="tooltip-text">Password must be at least 8 characters, contain one uppercase letter, one lowercase letter, one number, and one special character (@, $, !, %, *, ?, &amp; or _).</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <i class="bx bx-phone"></i>
                        <input type="tel" id="phone" name="phone" placeholder="Phone Number" required>
                        <div class="status-icon" id="phone-status"></div>
                        <div class="info-tooltip">
                            <i class="bx bx-info-circle"></i>
                            <span class="tooltip-text">Phone number must be 10 characters</span>
                        </div>
                    </div>

                    <button type="submit" id="saveButton" class="submit-button" disabled>Sign up</button>
                </form>


                <!-- Durum Mesajı -->
                <?php if (!$user_logged_in): ?>
                    <div class="status-message">
                        <p><?php echo $status_message; ?></p>
                    </div>
                    <p>Please sign up or <a href="./../Giris/giris.php">log in</a> to view and edit your account.</p>
                <?php endif; ?>
            </div>


            <div class="edit-userdata-form" <?php if ($user_logged_in): ?><?php endif; ?>>
                <?php if ($user_logged_in):
                    echo "hidden" ?>
                    <div class="space"></div>
                    <div class="user-info">
                        <h3>Welcome, <?php echo htmlspecialchars($user_data['username']); ?>!</h3>
                        <p>Email: <?php echo htmlspecialchars($user_data['email']); ?></p>
                        <p>Phone: <?php echo htmlspecialchars($user_data['phone']); ?></p>
                    </div>
                    <form action="" method="post" class="edit-account-form"
                        style="display: flex; flex-direction: column; gap: 20px;">
                        <h3>Edit Account Information</h3>

                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <label for="edit_username">Username:</label>
                            <input type="text" id="edit_username" name="edit_username"
                                value="<?php echo isset($user_data['username']) ? htmlspecialchars($user_data['username']) : ''; ?>"
                                required>
                            <div class="status-icon" id="username-status"></div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <label for="edit_email">Email:</label>
                            <input type="email" id="edit_email" name="edit_email"
                                value="<?php echo isset($user_data['email']) ? htmlspecialchars($user_data['email']) : ''; ?>"
                                required>
                            <div class="status-icon" id="email-status"></div>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <label for="edit_phone">Phone:</label>
                            <input type="tel" id="edit_phone" name="edit_phone"
                                value="<?php echo isset($user_data['phone']) ? htmlspecialchars($user_data['phone']) : ''; ?>"
                                required>
                            <div class="status-icon" id="phone-status"></div>
                        </div>

                        <div class="password-visibility" style="display: flex; flex-direction: column; gap: 5px;">
                            <label for="edit_password">New Password: <i class="bx bx-show"
                                    onclick="atogglePasswordVisibility()"></i></label>
                            <input type="password" id="edit_password" name="edit_password"
                                placeholder="Leave blank to keep current password">
                        </div>

                        <input type="hidden" id="refreshFlag" name="refreshFlag" value="">

                        <div class="editformsubmit">
                            <button type="submit">Update Information</button>
                        </div>

                        <!-- Button to delete account -->
                        <div class="delete-account">
                            <button type="submit" name="delete_account"
                                onclick="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">Delete
                                Account</button>
                        </div>

                        <!-- Button to erase cookies -->
                        <div class="erase-cookies">
                            <button type="submit" name="erase_cookies">Log Out</button>
                        </div>

                        <div class="loginbuttonlink"><a href="./../Giris/giris.php">Login to another account.</a></div>

                        <!-- Display success message if set -->
                        <?php if (isset($status_message)): ?>
                            <p id="statusMessage"><?= $status_message; ?></p>
                        <?php endif; ?>

                        <!-- JavaScript for hard refresh after 2 seconds -->
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                if (<?php echo json_encode($needsRefresh); ?>) {
                                    setTimeout(function () {
                                        window.location.reload(true); // Perform a hard refresh
                                    }, 2000);
                                }
                            });

                            document.getElementById("edit_phone").addEventListener("input", aformatPhoneNumber);

                            function aformatPhoneNumber(aevent) {
                                var ainput = aevent.target.value.replace(/\D/g, "");

                                if (ainput.length > 0 && !ainput.startsWith("90")) {
                                    ainput = "90" + ainput;
                                }

                                if (ainput.length > 12) {
                                    ainput = ainput.substring(0, 12);
                                }

                                var aformatted = "+" + ainput.slice(0, 2);
                                if (ainput.length > 2) {
                                    aformatted += " " + ainput.slice(2, 5);
                                }
                                if (ainput.length > 5) {
                                    aformatted += " " + ainput.slice(5, 8);
                                }
                                if (ainput.length > 8) {
                                    aformatted += " " + ainput.slice(8, 10);
                                }
                                if (ainput.length > 10) {
                                    aformatted += " " + ainput.slice(10);
                                }

                                aevent.target.value = aformatted;
                            }

                            function atogglePasswordVisibility() {
                                var apasswordField = document.getElementById("edit_password");
                                apasswordField.type = apasswordField.type === "password" ? "text" : "password";
                            }

                            function avalidateForm() {
                                var ausername = document.getElementById("edit_username").value;
                                var aemail = document.getElementById("edit_email").value;
                                var apassword = document.getElementById("edit_password").value;
                                var aphone = document.getElementById("edit_phone").value.replace(/\D/g, "");
                                var asaveButton = document.querySelector(".editformsubmit button[type='submit']");

                                var ausernameRegex = /^[a-zA-Z0-9çÇğĞıİöÖşŞüÜ]{3,}$/;
                                var aemailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                                var apasswordRegex = /^(?=.*[A-ZÇĞİÖŞÜ])(?=.*[a-zçğıöşü])(?=.*\d)(?=.*[@$!%*?&#_])[A-Za-zçğıöşüÇĞİÖŞÜ\d@$!%*?&#_]{8,}$/;
                                var aphoneRegex = /^\d{12}$/;

                                avalidateField(ausername, ausernameRegex, "username-status");
                                avalidateField(aemail, aemailRegex, "email-status");
                                avalidateField(apassword, apasswordRegex, "password-status");
                                avalidateField(aphone, aphoneRegex, "phone-status");

                                if (
                                    ausernameRegex.test(ausername) &&
                                    aemailRegex.test(aemail) &&
                                    apasswordRegex.test(apassword) &&
                                    aphoneRegex.test(aphone)
                                ) {
                                    asaveButton.disabled = false;
                                } else {
                                    asaveButton.disabled = true;
                                }
                            }

                            function avalidateField(avalue, aregex, astatusElementId) {
                                var astatusElement = document.getElementById(astatusElementId);
                                if (aregex.test(avalue)) {
                                    astatusElement.innerHTML =
                                        '<i class="bx bx-check-circle" style="color: green;"></i>';
                                    astatusElement.className = "status-icon correct";
                                } else {
                                    astatusElement.innerHTML =
                                        '<i class="bx bx-x-circle" style="color: red;"></i>';
                                    astatusElement.className = "status-icon incorrect";
                                }
                            }

                            // Adding event listeners
                            document.getElementById("edit_phone").addEventListener("input", avalidateForm);
                            document.getElementById("edit_phone").addEventListener("input", aformatPhoneNumber);
                            document.getElementById("edit_password").addEventListener("input", atogglePasswordVisibility);
                            document.getElementById("edit_username").addEventListener("input", avalidateForm);
                            document.getElementById("edit_email").addEventListener("input", avalidateForm);
                        </script>
                    </form>
                <?php endif; ?>
            </div>






        </div>



        <script>
            <script defer src="anasayfa.js"></script>
        </script>
    </div>


</body>

</html>