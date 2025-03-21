<?php
session_start();

if (!isset($_COOKIE['user_key']) || empty($_COOKIE['user_key'])) {
    echo "User is not logged in.";
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "kullanıcı_db";

$icon = '<i class="bx bx-home"></i>';
$status_message = "";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!function_exists('clearSessionAndCookies')) {
    function clearSessionAndCookies(): never
    {
        unset($_SESSION['key']);
        setcookie("user_key", "", time() - (86400 * 30), "/");
        setcookie("username", "", time() - (86400 * 30), "/");
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

$user_logged_in = false;
if (isset($_COOKIE['user_key'])) {
    $user_key = $_COOKIE['user_key'];
    $user_check_sql = "SELECT * FROM kullanıcı_verileri WHERE token=?";
    $stmt = $conn->prepare($user_check_sql);
    $stmt->bind_param("s", $user_key);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user_data = $user_result->fetch_assoc();
        $_SESSION['user_id'] = $user_data['id'];
        $_SESSION['key'] = $user_key;
        $_SESSION['username'] = $user_data['username'];
        $user_logged_in = true;
        $status_message = "Welcome, " . $user_data['username'] . "!";
    } else {
        clearSessionAndCookies();
    }

    $favorites = [];
    $favorites_sql = "SELECT urunler.id, urunler.name, urunler.price, urunler.image, urunler.image2, urunler.image3, urunler.description, 
                              urunler.information, urunler.comments, urunler.answer, urunler.question
                      FROM urunler_hesap_favorites
                      LEFT JOIN urunler ON urunler_hesap_favorites.product_id = urunler.id
                      WHERE urunler_hesap_favorites.user_id = ?";
    $stmt = $conn->prepare($favorites_sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    if ($stmt->execute()) {
        $favorites_result = $stmt->get_result();
        if ($favorites_result->num_rows > 0) {
            while ($row = $favorites_result->fetch_assoc()) {
                $favorites[] = $row;
            }
        }
    }

    $regular_products = [];
    $regular_products_sql = "SELECT id, name, price, image, image2, image3, description, information, answer, question, comments FROM urunler";
    $regular_result = $conn->query($regular_products_sql);
    if ($regular_result->num_rows > 0) {
        while ($row = $regular_result->fetch_assoc()) {
            $regular_products[] = $row;
        }
    }

    $popular_products = [];
    $popular_products_sql = "SELECT id, name, price, image, image2, image3, description, information, answer, question, comments FROM urun_popular";
    $popular_result = $conn->query($popular_products_sql);
    if ($popular_result->num_rows > 0) {
        while ($row = $popular_result->fetch_assoc()) {
            $popular_products[] = $row;
        }
    }

} else {
    echo "User is not logged in.";
}

$products_per_page = 3;
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1; // Initialize the current page
$offset = ($current_page - 1) * $products_per_page;



if ($_SERVER["REQUEST_METHOD"] == "POST" && $user_logged_in) {
    if (isset($_POST['remove-from-favorites'])) {
        $productId = $_POST['product_id'];
        $userId = $_SESSION['user_id']; // Assuming you store user id in the session

        $sql = "DELETE FROM urunler_hesap_favorites WHERE user_id = ? AND product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $userId, $productId);
        $stmt->execute();
        $stmt->close();

        $_SESSION['refresh_once'] = true;
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
        exit();
    }

    if (isset($_POST['add-to-cart'])) {
        $productId = $_POST['product_id'];
        $userId = $_SESSION['user_id']; // Assuming you store user id in the session

        // Check if the product already exists in the cart
        $checkSql = "SELECT * FROM urunler_hesap_cart WHERE user_id = ? AND product_id = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bind_param("ii", $userId, $productId);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result->num_rows == 0) {
            $sql = "INSERT INTO urunler_hesap_cart (user_id, product_id, quantity, added_at) VALUES (?, ?, 1, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $userId, $productId);
            $stmt->execute();
            $stmt->close();
        } else {
            // Update the quantity of the existing product in the cart
            $sql = "UPDATE urunler_hesap_cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $userId, $productId);
            $stmt->execute();
            $stmt->close();
        }
        $checkStmt->close();

        $_SESSION['refresh_once'] = true;
        header("Location: " . $_SERVER['PHP_SELF']); // Redirect to the same page
        exit();
    }
}

$needsRefresh = false;
if (isset($_SESSION['refresh_once'])) {
    unset($_SESSION['refresh_once']);
    $needsRefresh = true;
}






$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="./../../../../GLOBALFILE/GLOBAL.CSS">
    <link rel="stylesheet" href="favorite.css">

    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/google-libphonenumber/3.2.25/libphonenumber.js"></script>
    <script defer src="favorite.js"></script>
    <title>Hesap</title>
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



        <?php if ($user_logged_in): ?>
            <div class="space"></div>
            <!-- Shopping Form Layout -->
            <div class="shopping-form" id="shopping-site">
                <!-- Container for Side-by-Side Layout -->
                <div class="side-by-side-container">

                    <!-- Cart Section -->
                    <div class="section cart-section">
                        <h2>Your Favorites</h2>
                        <div class="items" id="product-grid-section">
                            <?php if (empty($favorites)): ?>
                                <p>Your cart is empty.</p>
                            <?php else: ?>
                                <div class="product-grid">
                                    <?php foreach ($favorites as $item): ?>
                                        <div class="product">
                                            <div class="image-container">
                                                <img id="main-image-cart-<?php echo $item['id']; ?>" class="main-image"
                                                    src="<?php echo htmlspecialchars($item['image']); ?>"
                                                    alt="<?php echo htmlspecialchars($item['name']); ?>" loading="lazy">
                                                <div id="image-count-cart-<?php echo $item['id']; ?>" class="image-count">1 / 3
                                                </div>
                                            </div>
                                            <!-- Combined Thumbnail Section -->
                                            <div class="thumbnail-container">
                                                <?php foreach (['image', 'image2', 'image3'] as $index => $imageKey): ?>
                                                    <?php if (!empty($item[$imageKey])): ?>
                                                        <img class="thumbnail" src="<?php echo htmlspecialchars($item[$imageKey]); ?>"
                                                            alt="<?php echo htmlspecialchars($item['name']) . ' ' . ($index + 1); ?>"
                                                            loading="lazy">
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </div>
                                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                                            <p><?php echo htmlspecialchars($item['description']); ?></p>
                                            <p>Price: <?php echo $item['price']; ?> USD</p>

                                            <!-- Info and Comments Button -->
                                            <div class="button-group">
                                                <button class="info-button" data-product-id="<?php echo $item['id']; ?>"
                                                    onclick="toggleProductDetails('info', <?php echo $item['id']; ?>)">Product
                                                    Info</button>
                                                <button class="comments-button" data-product-id="<?php echo $item['id']; ?>"
                                                    onclick="toggleProductDetails('comments', <?php echo $item['id']; ?>)">Reviews
                                                    & Questions</button>
                                            </div>
                                            <div class="info-group">
                                                <form method="POST">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                    <button type="submit" name="add-to-cart">Add To My Cart</button>
                                                </form>
                                                <form method="POST">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                    <button type="submit" name="remove-from-favorites">Remove From
                                                        Favorite</button>
                                                </form>
                                            </div>


                                            <script>
                                                document.addEventListener("DOMContentLoaded", function () {
                                                    if (<?php echo json_encode($needsRefresh); ?>) {
                                                        window.location.reload(true); // Hard refresh the page
                                                    }
                                                });
                                            </script>


                                            <!-- Info and Comments Sections (Hidden by default) -->
                                            <div id="info-<?php echo $item['id']; ?>" class="details-section info-section"
                                                style="display: none;">
                                                <div class="close-button" onclick="closeDetails(<?php echo $item['id']; ?>)">X</div>
                                                <h4>Product Information</h4>
                                                <p><?php echo !empty($item['information']) ? htmlspecialchars($item['information']) : 'No additional information available.'; ?>
                                                </p>
                                            </div>

                                            <div id="comments-<?php echo $item['id']; ?>" class="details-section comments-section"
                                                style="display: none;">
                                                <div class="close-button" onclick="closeDetails(<?php echo $item['id']; ?>)">X</div>
                                                <h4>Reviews and Questions</h4>
                                                <p>
                                                    <?php
                                                    if (!empty($item['comments'])) {
                                                        echo "<strong>Comments:</strong> " . nl2br(htmlspecialchars($item['comments'])) . "<br>";
                                                    }
                                                    if (!empty($item['question']) && !empty($item['answer'])) {
                                                        echo "<strong>Question:</strong> " . htmlspecialchars($item['question']) . "<br>";
                                                        echo "<strong>Answer:</strong> " . htmlspecialchars($item['answer']);
                                                    } elseif (!empty($item['question'])) {
                                                        echo "<strong>Question:</strong> " . htmlspecialchars($item['question']);
                                                    } elseif (!empty($item['answer'])) {
                                                        echo "<strong>Answer:</strong> " . htmlspecialchars($item['answer']);
                                                    } elseif (empty($item['comments']) && empty($item['question']) && empty($item['answer'])) {
                                                        echo 'No reviews or questions yet.';
                                                    }
                                                    ?>
                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php
                                $products_per_page = 3;
                                $current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                                $offset = ($current_page - 1) * $products_per_page;
                                $total_products = count($favorites);
                                $paged_products = array_slice($favorites, $offset, $products_per_page);
                                ?>
                                <?php if ($total_products >= 4): ?>
                                    <div class="pagination-controls">
                                        <?php if ($current_page > 1): ?>
                                            <a href="?page=<?php echo $current_page - 1; ?>" class="prev-all-button"
                                                aria-label="Previous Page">&lt;- Previous Page</a>
                                        <?php endif; ?>
                                        <span id="current-page">Page <?php echo $current_page; ?></span>
                                        <?php if ($total_products > $current_page * $products_per_page): ?>
                                            <a href="?page=<?php echo $current_page + 1; ?>" class="next-all-button"
                                                aria-label="Next Page">Next Page -&gt;</a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                            <?php endif; ?>
                        </div>
                    </div>
                </div> <!-- End of Side-by-Side Container -->

            </div>
        <?php else: ?>
            <div class="container">
                <h1>Access Denied</h1>
                <p>Your session has expired or you are not logged in. Please log in again.</p>
            </div>
        <?php endif; ?>


        <script>
            <script defer src="favorite.js"></script>
        </script>


    </div>


</body>

</html>