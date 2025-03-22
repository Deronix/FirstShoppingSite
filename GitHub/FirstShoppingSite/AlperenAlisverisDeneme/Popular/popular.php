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
        $status_message = "Welcome, " . htmlspecialchars($_COOKIE['username']) . "!";
        $_SESSION['user_id'] = $row['id'];
    } else {
        clearSessionAndCookies();
    }
}

$user_username = isset($_POST['username']) ? $_POST['username'] : '';
$user_email = isset($_POST['email']) ? $_POST['email'] : '';
$user_password = isset($_POST['password']) ? password_hash($_POST['password'], PASSWORD_ARGON2ID) : '';
$user_phone = isset($_POST['phone']) ? $_POST['phone'] : '';

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

$products = [];
$product_sql = "SELECT id, name, price, description, image, image2, image3, answer, question, comments, information FROM urun_popular";
$result = $conn->query($product_sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}


// Prevent re-adding the product if the page is refreshed
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $user_logged_in) {
    $action = $_POST['action'];
    $product_id = (int) $_POST['product_id'];
    $user_id = $_SESSION['user_id'];

    // Initialize session variable for status message
    $status_message = '';

    if ($action == 'cart') {
        // Prevent re-adding the same product to the cart
        $stmt = $conn->prepare("SELECT quantity FROM urunler_hesap_cart WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Update the quantity if the product is already in the cart
            $row = $result->fetch_assoc();
            $new_quantity = $row['quantity'] + 1;
            $update_stmt = $conn->prepare("UPDATE urunler_hesap_cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
            $update_stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
            $update_stmt->execute();
            $_SESSION['status_message'][$product_id] = 'Product added to cart successfully!';
        } else {
            // If the product is not in the cart, add it
            $quantity = 1;
            $stmt = $conn->prepare("INSERT INTO urunler_hesap_cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $user_id, $product_id, $quantity);
            $stmt->execute();
            $_SESSION['status_message'][$product_id] = 'Product added to cart successfully!';
        }
    } elseif ($action == 'favorites') {
        // Check if the product is already in the user's favorites
        $stmt = $conn->prepare("SELECT * FROM urunler_hesap_favorites WHERE user_id = ? AND product_id = ?");
        $stmt->bind_param("ii", $user_id, $product_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Product is already in favorites
            $_SESSION['status_message'][$product_id] = 'The item is already in your favorites!';
        } else {
            // Add the product to favorites since it's not there yet
            $stmt = $conn->prepare("INSERT INTO urunler_hesap_favorites (user_id, product_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $user_id, $product_id);
            $stmt->execute();
            $_SESSION['status_message'][$product_id] = 'Product added to favorites!';
        }
    }


    // Set flag to prevent redirect loop
    $_SESSION['form_processed'] = true;


}

// Check if form has been processed and unset flag
if (isset($_SESSION['form_processed'])) {
    unset($_SESSION['form_processed']);
}


// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Login</title>

    <link rel="stylesheet" href="./../../GLOBALFILE/GLOBAL.CSS">
    <link rel="stylesheet" href="popular.css">

    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/google-libphonenumber/3.2.25/libphonenumber.js"></script>
    <script defer src="popular.js"></script>

    <!-- CSS Styles -->

</head>

<body>

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
            <h2>User Login</h2>
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
                        <span class="tooltip-text">Password must be at least 8 characters, contain one uppercase letter, one lowercase letter, one number, and one special character (@, $, !, %, *, ?, & or _).</span>
                    </div>
                </div>

                <div class="form-group">
                    <i class="bx bx-phone"></i>
                    <input type="tel" id="phone" name="phone" placeholder="Phone" required>
                    <div class="status-icon" id="phone-status"></div>
                    <div class="info-tooltip">
                        <i class="bx bx-info-circle"></i>
                        <span class="tooltip-text">Phone number must be 10 characters</span>
                    </div>
                </div>

                <button type="submit" id="saveButton" class="submit-button" disabled>Save</button>
            </form>

            <!-- Status Message -->
            <?php if ($status_message && !$user_logged_in): ?>
                <div class="status-message">
                    <p><?php echo $status_message; ?></p>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($user_logged_in): ?>
            <div class="product-grid-section">
                <div class="space"></div>
                <h2>Popular Products</h2>
                <div class="product-grid" id="productGrid">
                    <?php
                    $products_per_page = 3;
                    $current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                    $offset = ($current_page - 1) * $products_per_page;
                    $paged_products = array_slice($products, $offset, $products_per_page);

                    if (!empty($paged_products)): ?>
                        <?php foreach ($paged_products as $product): ?>
                            <article class="product">
                                <!-- Status message specific to this product -->


                                <div class="image-container">
                                    <img id="main-image-regular-<?php echo $product['id']; ?>" class="main-image"
                                        src="<?php echo htmlspecialchars($product['image']); ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy">
                                    <div id="image-count-regular-<?php echo $product['id']; ?>" class="image-count">1 / 3</div>
                                </div>

                                <div class="thumbnail-container">
                                    <img class="thumbnail" src="<?php echo htmlspecialchars($product['image']); ?>"
                                        alt="<?php echo htmlspecialchars($product['name']); ?>" loading="lazy"
                                        onclick="changeImage('regular', '<?php echo $product['id']; ?>', '<?php echo addslashes($product['image']); ?>', 1)">
                                    <?php if (!empty($product['image2'])): ?>
                                        <img class="thumbnail" src="<?php echo htmlspecialchars($product['image2']); ?>"
                                            alt="<?php echo htmlspecialchars($product['name']); ?> 2" loading="lazy"
                                            onclick="changeImage('regular', '<?php echo $product['id']; ?>', '<?php echo addslashes($product['image2']); ?>', 2)">
                                    <?php endif; ?>
                                    <?php if (!empty($product['image3'])): ?>
                                        <img class="thumbnail" src="<?php echo htmlspecialchars($product['image3']); ?>"
                                            alt="<?php echo htmlspecialchars($product['name']); ?> 3" loading="lazy"
                                            onclick="changeImage('regular', '<?php echo $product['id']; ?>', '<?php echo addslashes($product['image3']); ?>', 3)">
                                    <?php endif; ?>
                                </div>

                                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p><?php echo htmlspecialchars($product['description']); ?></p>
                                <h3>Price: $<?php echo htmlspecialchars($product['price']); ?></h3>
                                <?php if (isset($_SESSION['status_message'][$product['id']])): ?>
                                    <div class="status-message"
                                        style="color: green; padding: 10px; background: #e0ffe0; border-radius: 5px;">
                                        <?php echo $_SESSION['status_message'][$product['id']]; ?>
                                    </div>
                                    <?php unset($_SESSION['status_message'][$product['id']]); // Clear the specific product message ?>
                                <?php endif; ?>


                                <div class="button-group">
                                    <form method="POST" action="">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="action" value="cart">
                                        <button type="submit" class="add-to-cart">Add To My Cart</button>
                                    </form>

                                    <form method="POST" action="">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <input type="hidden" name="action" value="favorites">
                                        <button type="submit" class="add-to-favorites">Add To Favorites</button>
                                    </form>
                                </div>

                                <div class="info-group" data-product-id="<?php echo $product['id']; ?>">
                                    <button class="info-button" data-product-id="<?php echo $product['id']; ?>"
                                        onclick="toggleProductDetails('info', <?php echo $product['id']; ?>)">Profuct's Info</button>
                                    <button class="comments-button" data-product-id="<?php echo $product['id']; ?>"
                                        onclick="toggleProductDetails('comments', <?php echo $product['id']; ?>)">Reviews & Questions
                                        </button>
                                </div>

                                <div id="info-<?php echo $product['id']; ?>" class="details-section info-section"
                                    style="display: none;">
                                    <div class="close-button" onclick="closeDetails(<?php echo $product['id']; ?>)">X</div>
                                    <h4>Product Information</h4>
                                    <p>
                                        <?php echo !empty($product['information']) ? htmlspecialchars($product['information']) : 'No additional information available.'; ?>
                                    </p>
                                </div>

                                <div id="comments-<?php echo $product['id']; ?>" class="details-section comments-section"
                                    style="display: none;">
                                    <div class="close-button" onclick="closeDetails(<?php echo $product['id']; ?>)">X</div>
                                    <h4>Reviews and Questions</h4>
                                    <p>
                                        <?php
                                        if (!empty($product['comments'])) {
                                            echo "<strong>Comments:</strong> " . nl2br(htmlspecialchars($product['comments'])) . "<br>";
                                        }
                                        if (!empty($product['question']) && !empty($product['answer'])) {
                                            echo "<strong>Question:</strong> " . htmlspecialchars($product['question']) . "<br>";
                                            echo "<strong>Answer:</strong> " . htmlspecialchars($product['answer']);
                                        } elseif (!empty($product['question'])) {
                                            echo "<strong>Question:</strong> " . htmlspecialchars($product['question']);
                                        } elseif (!empty($product['answer'])) {
                                            echo "<strong>Answer:</strong> " . htmlspecialchars($product['answer']);
                                        } elseif (empty($product['comments']) && empty($product['question']) && empty($product['answer'])) {
                                            echo 'No reviews or questions yet.';
                                        }
                                        ?>
                                    </p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No products available at this time.</p>
                    <?php endif; ?>
                </div>

                <div class="pagination-controls">
                    <?php if ($current_page > 1): ?>
                        <a href="?page=<?php echo $current_page - 1; ?>" id="prev-all-button" aria-label="Previous Page">&lt;-
                            Previous Page</a>
                    <?php endif; ?>
                    <span id="current-page">Page <?php echo $current_page; ?></span>
                    <?php if (count($products) > $current_page * $products_per_page): ?>
                        <a href="?page=<?php echo $current_page + 1; ?>" id="next-all-button" aria-label="Next Page">Next Page
                            -&gt;</a>
                    <?php endif; ?>
                </div>

                <div class="regular-products-button-container">
                    <button id="popular-products-button" class="popular-products-button">
                        <a href="./../Index/index.php">See all of our products</a>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <script>
            <script defer src="popular.js"></script>
        </script>

    </div>

</body>

</html>