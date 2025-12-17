<?php
require_once __DIR__ . '/inc/db_connect.php';
require_once __DIR__ . '/inc/header.php';

// Проверка: передан ли id заказа
if (!isset($_GET['order_id'])) {
    echo "<p class='error'>Заказ не найден.</p>";
    require_once __DIR__ . '/inc/footer.php';
    exit;
}

$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'] ?? 0;

// Получаем заказ
$orderQuery = $conn->prepare("
    SELECT * FROM orders 
    WHERE order_id = ? AND user_id = ?
");
$orderQuery->bind_param("ii", $order_id, $user_id);
$orderQuery->execute();
$order = $orderQuery->get_result()->fetch_assoc();

if (!$order) {
    echo "<p class='error'>У вас нет доступа к этому заказу.</p>";
    require_once __DIR__ . '/inc/footer.php';
    exit;
}

// Получаем товары заказа
$itemsQuery = $conn->prepare("
    SELECT p.name, oi.quantity, oi.price
    FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    WHERE oi.order_id = ?
");
$itemsQuery->bind_param("i", $order_id);
$itemsQuery->execute();
$items = $itemsQuery->get_result();
?>

<div class="container">
    <h2>Спасибо за заказ!</h2>

    <div class="order-box">
        <p><strong>Номер заказа:</strong> №<?= $order['order_id'] ?></p>
        <p><strong>Статус:</strong> <?= htmlspecialchars($order['status']) ?></p>
        <p><strong>Дата:</strong> <?= $order['created_at'] ?></p>
    </div>

    <h3>Состав заказа</h3>

    <table class="order-table" style="border: 2px solid black; margin-bottom: 15px">
        <tr>
            <th style="border: 2px solid black">Товар</th>
            <th style="border: 2px solid black">Количество</th>
            <th style="border: 2px solid black">Цена</th>
            <th style="border: 2px solid black">Сумма</th>
        </tr>

        <?php while ($item = $items->fetch_assoc()): ?>
        <tr>
            <td style="border: 2px solid black"><?= htmlspecialchars($item['name']) ?></td>
            <td style="border: 2px solid black"><?= $item['quantity'] ?></td>
            <td style="border: 2px solid black"><?= number_format($item['price'], 2, '.', ' ') ?> ₽</td>
            <td style="border: 2px solid black"><?= number_format($item['price'] * $item['quantity'], 2, '.', ' ') ?> ₽</td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="order-total" style="margin-bottom: 15px">
        <strong>Итого:</strong> <?= number_format($order['total_amount'], 2, '.', ' ') ?> ₽
    </div>

    <div class="order-actions">
        <a href="profile.php" class="btn">Мои заказы</a>
        <a href="support/new_ticket.php?order_id=<?= $order_id ?>" class="btn btn-secondary">
            Написать в поддержку
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/inc/footer.php'; ?>
