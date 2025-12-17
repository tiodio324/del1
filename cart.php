<?php
session_start();
require_once __DIR__ . '/inc/db_connect.php';

// Инициализация корзины
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Добавление товара
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
}

// Удаление товара
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    unset($_SESSION['cart'][$id]);
}

// Получение товаров корзины
$cartItems = $_SESSION['cart'];
$totalPrice = 0;
$products = [];

if (!empty($cartItems)) {
    $ids = array_keys($cartItems);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));

    $stmt = $conn->prepare("
        SELECT id, name, price, image 
        FROM products 
        WHERE id IN ($placeholders)
    ");
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $quantity = $cartItems[$row['id']];
        $row['quantity'] = $quantity;
        $row['sum'] = $row['price'] * $quantity;
        $totalPrice += $row['sum'];
        $products[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimCockStore - Корзина</title>
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

<main class="cart">
    <h1>Корзина</h1>

    <?php if (empty($products)): ?>
        <p>Ваша корзина пуста.</p>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($products as $product): ?>
                <div class="cart-item">
                    <img src="img/<?= htmlspecialchars($product['image']) ?>"
                         alt="<?= htmlspecialchars($product['name']) ?>">
                    <div class="cart-item-info">
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <p>Цена: <?= number_format($product['price'], 2, '.', ' ') ?> руб.</p>
                        <p>Количество: <?= $product['quantity'] ?></p>
                        <a href="cart.php?action=remove&id=<?= $product['id'] ?>">Удалить</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-total">
            <p>Итого: <?= number_format($totalPrice, 2, '.', ' ') ?> руб.</p>

            <form action="checkout.php" method="post">
                <!-- Передаём сумму (для UI, НЕ для доверия) -->
                <input type="hidden" name="total_amount" value="<?= $totalPrice ?>">
                <button type="submit" class="button">Оформить заказ</button>
            </form>
        </div>
    <?php endif; ?>
</main>

<footer>
    <div class="container">
        <div class="footer-info">
            <p>© 2024 TimCockStore</p>
        </div>
        <div class="social-links"></div>
        <div class="contact-info"></div>
    </div>
</footer>

</body>
</html>

