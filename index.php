<?php
session_start(); // Start the session

// Include the database connection file
include 'inc/db_connect.php';

// Function to get popular products from the database
function getPopularProducts($conn, $limit = 6) {
    $sql = "SELECT id, name, price, image FROM products ORDER BY RAND() LIMIT $limit";
    $result = $conn->query($sql);
    $products = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}

// Get popular products
$popularProducts = getPopularProducts($conn);

// Function to get products on sale
function getSaleProducts($conn, $limit = 6) {
    $sql = "SELECT id, name, price, image FROM products ORDER BY RAND() LIMIT $limit";
    $result = $conn->query($sql);
    $products = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}

// Get products on sale
$SaleProducts = getSaleProducts($conn);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimCockStore - Главная</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
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

    <main>
        <section class="banner">
            <!-- Здесь будет баннер с рекламой -->
            <h1>Добро пожаловать в TimCockStore!</h1>
            <p>Лучшая техника Apple и других брендов по доступным ценам.</p>
        </section>

        <section class="popular-products">
            <h2>Популярные товары</h2>
            <div class="product-list">
                <?php foreach ($popularProducts as $product): ?>
                    <div class="product-item">
                        <img src="img/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>Цена: <?php echo htmlspecialchars($product['price']); ?> руб.</p>
                        <button>В корзину</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class=" акции">
            <h2>Акции</h2>
            <div class="product-list">
                <?php foreach ($SaleProducts as $product): ?>
                    <div class="product-item">
                        <img src="img/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p>Цена: <?php echo htmlspecialchars($product['price']); ?> руб.</p>
                        <button>В корзину</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
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