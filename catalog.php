<?php
session_start(); // Start the session
include 'inc/db_connect.php';

// Function to get all categories
function getCategories($conn) {
    $sql = "SELECT id, name FROM categories";
    $result = $conn->query($sql);
    $categories = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $categories[] = $row;
        }
    }
    return $categories;
}

// Function to get products by category
function getProductsByCategory($conn, $category_id = null) {
    $sql = "SELECT id, name, price, image FROM products";
    if ($category_id !== null) {
        $sql .= " WHERE category_id = " . intval($category_id);
    }
    $result = $conn->query($sql);
    $products = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
    }
    return $products;
}

$categories = getCategories($conn);
$products = getProductsByCategory($conn, isset($_GET['category']) ? $_GET['category'] : null);

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimCockStore - Каталог</title>
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

    <main class="catalog">
        <h1>Каталог товаров</h1>

        <aside class="filters">
            <h3>Фильтры</h3>
            <div class="filter-group">
                <h4>Категории</h4>
                <ul>
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <a href="catalog.php?category=<?php echo htmlspecialchars($category['id']); ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </aside>

        <section class="product-list">
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <img src="img/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>Цена: <?php echo htmlspecialchars($product['price']); ?> руб.</p>
                    <?php echo '<a href="add_to_cart.php?id='.$product['id'].'" class="btn-buy">В корзину</a>'; ?>
                </div>
            <?php endforeach; ?>
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