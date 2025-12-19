<?php
session_start(); // Start the session
include 'inc/db_connect.php';

// Get product ID from the query string
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Function to get product details
function getProductDetails($conn, $product_id) {
    $sql = "SELECT id, name, description, price, image FROM products WHERE id = " . $product_id;
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return null;
    }
}

$product = getProductDetails($conn, $product_id);

$conn->close();

if (!$product) {
    header("HTTP/1.0 404 Not Found");
    echo "<h1>Товар не найден</h1>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimCockStore - <?php echo htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body style="margin-bottom: 200px;">
    <header>
        <div class="logo">
            <a href="index.php">TimCockStore</a>
        </div>
        <nav>
            <ul>
                <li><a href="catalog.php">Каталог</a></li>
                <li><a href="cart.php">Корзина</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Личный кабинет</a></li>
                    <li><a href="logout.php">Выйти</a></li>
                <?php else: ?>
                    <li><a href="login.php">Вход</a></li>
                    <li><a href="register.php">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="search">
            <input type="text" placeholder="Поиск...">
            <button>Найти</button>
        </div>
    </header>

    <main class="product-details">
        <div class="product-image">
            <img src="" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="product-info">
            <h1><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="price">Цена: <?php echo htmlspecialchars($product['price']); ?> руб.</p>
            <p class="description"><?php echo htmlspecialchars($product['description']); ?></p>
            <button>В корзину</button>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-info">
                <p>© 2024 TimCockStore</p>
            </div>
            <div class="social-links">
                <!-- Здесь будут ссылки на социальные сети -->
            </div>
            <div class="contact-info">
                <!-- Здесь будет контактная информация -->
            </div>
        </div>
    </footer>
</body>
</html>