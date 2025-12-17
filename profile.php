<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/inc/db_connect.php';

$user_id = $_SESSION['user_id'];

/* ===============================
   ДАННЫЕ ПОЛЬЗОВАТЕЛЯ
=============================== */
$userStmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ?");
$userStmt->bind_param("i", $user_id);
$userStmt->execute();
$user = $userStmt->get_result()->fetch_assoc();
$userStmt->close();

if (!$user) {
    die("Ошибка: пользователь не найден.");
}

/* ===============================
   ЗАКАЗЫ ПОЛЬЗОВАТЕЛЯ
=============================== */
$orderStmt = $conn->prepare("
    SELECT order_id, total_amount, status, created_at
    FROM orders
    WHERE user_id = ?
    ORDER BY order_id DESC
");
$orderStmt->bind_param("i", $user_id);
$orderStmt->execute();
$ordersResult = $orderStmt->get_result();

$orders = [];
while ($row = $ordersResult->fetch_assoc()) {
    $orders[] = $row;
}
$orderStmt->close();

/* ===============================
   СОСТАВ ЗАКАЗОВ
=============================== */
/* ===============================
   СОСТАВ ЗАКАЗОВ
=============================== */
$orderItems = [];

if (!empty($orders)) {

    $orderIds = [];
    foreach ($orders as $order) {
        if (isset($order['id'])) {
            $orderIds[] = (int)$order['id'];
        }
    }

    // 🔒 ЖЕЛЕЗНАЯ ЗАЩИТА ОТ IN ()
    if (count($orderIds) > 0) {

        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        $types = str_repeat('i', count($orderIds));

        $sql = "
            SELECT
                oi.order_id,
                p.name,
                oi.quantity,
                oi.price
            FROM order_items oi
            JOIN products p ON p.id = oi.product_id
            WHERE oi.order_id IN ($placeholders)
            ORDER BY oi.order_id
        ";

        $itemsStmt = $conn->prepare($sql);
        $itemsStmt->bind_param($types, ...$orderIds);
        $itemsStmt->execute();

        $itemsResult = $itemsStmt->get_result();
        while ($item = $itemsResult->fetch_assoc()) {
            $orderItems[$item['order_id']][] = $item;
        }

        $itemsStmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimCockStore - Личный кабинет</title>
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
            <li><a href="profile.php">Личный кабинет</a></li>
            <li><a href="logout.php">Выйти</a></li>
        </ul>
    </nav>
    <div class="search">
        <input type="text" placeholder="Поиск...">
        <button>Найти</button>
    </div>
</header>

<main class="profile">
    <h1>Личный кабинет</h1>

    <section class="profile-info">
        <p><strong>ID:</strong> <?= htmlspecialchars($user['id']) ?></p>
        <p><strong>Имя:</strong> <?= htmlspecialchars($user['name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    </section>

    <h2>Мои заказы</h2>

    <?php if (empty($orders)): ?>
        <p>У вас пока нет заказов.</p>
    <?php else: ?>
        <?php foreach ($orders as $order): ?>
            <div class="order-card">
                <p><strong>Заказ №<?= $order['order_id'] ?></strong></p>
                <p>Дата: <?= htmlspecialchars($order['created_at']) ?></p>
                <p>Статус: <?= htmlspecialchars($order['status']) ?></p>
                <p><strong>Сумма:</strong>
                    <?= number_format($order['total_amount'], 2, '.', ' ') ?> руб.
                </p>

                <?php if (!empty($orderItems[$order['order_id']])): ?>
                    <div class="order-items">
                        <h4>Состав заказа:</h4>
                        <ul>
                            <?php foreach ($orderItems[$order['id']] as $item): ?>
                                <li>
                                    <?= htmlspecialchars($item['name']) ?> —
                                    <?= $item['quantity'] ?> ×
                                    <?= number_format($item['price'], 2, '.', ' ') ?> руб.
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <p>Нет данных о составе заказа.</p>
                <?php endif; ?>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="logout.php" class="button">Выйти</a>
</main>

<footer>
    <div class="container">
        <div class="footer-info">
            <p>© 2024 TimCockStore</p>
        </div>
    </div>
</footer>

</body>
</html>
