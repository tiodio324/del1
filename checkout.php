<?php
session_start();
require_once __DIR__ . '/inc/db_connect.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Проверка корзины
if (empty($_SESSION['cart'])) {
    die("Корзина пуста");
}

$errors = [];

// Обработка формы
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Валидация
    if ($name === '') $errors[] = "Пожалуйста, введите ваше имя.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = "Пожалуйста, введите корректный email.";
    if ($address === '') $errors[] = "Пожалуйста, введите адрес доставки.";
    if ($phone === '') $errors[] = "Пожалуйста, введите номер телефона.";

    if (empty($errors)) {

        $user_id = $_SESSION['user_id'];
        $total_amount = 0;

        /* ===============================
           ПЕРЕСЧЁТ СУММЫ ЗАКАЗА
        =============================== */
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");

        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();

            if ($product) {
                $total_amount += $product['price'] * $quantity;
            }
        }
        $stmt->close();

        /* ===============================
           СОЗДАНИЕ ЗАКАЗА
        =============================== */
        $orderStmt = $conn->prepare("
            INSERT INTO orders (user_id, total_amount, status)
            VALUES (?, ?, 'new')
        ");
        $orderStmt->bind_param("id", $user_id, $total_amount);
        $orderStmt->execute();
        $order_id = $orderStmt->insert_id;
        $orderStmt->close();

        /* ===============================
           СОХРАНЕНИЕ ТОВАРОВ ЗАКАЗА
        =============================== */
        $itemStmt = $conn->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");

        $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");

        foreach ($_SESSION['cart'] as $product_id => $quantity) {
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $product = $stmt->get_result()->fetch_assoc();

            if ($product) {
                $price = $product['price'];
                $itemStmt->bind_param("iiid", $order_id, $product_id, $quantity, $price);
                $itemStmt->execute();
            }
        }

        $stmt->close();
        $itemStmt->close();

        // Очистка корзины
        unset($_SESSION['cart']);

        // Переход к подтверждению заказа
        header("Location: order-confirmation.php?order_id=" . $order_id);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimCockStore - Оформление заказа</title>
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

<main class="checkout">
    <h1>Оформление заказа</h1>

    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Имя:</label>
            <input type="text" name="name" required>
        </div>

        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" required>
        </div>

        <div class="form-group">
            <label>Адрес доставки:</label>
            <input type="text" name="address" required>
        </div>

        <div class="form-group">
            <label>Телефон:</label>
            <input type="tel" name="phone" required>
        </div>

        <button type="submit" class="button">Подтвердить заказ</button>
    </form>
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
