<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/inc/db_connect.php';

$user_id = $_SESSION['user_id'];

if (!isset($_GET['order_id'])) {
    die("Заказ не указан.");
}

$order_id = (int)$_GET['order_id'];

/* ===============================
   ПРОВЕРКА ЗАКАЗА
=============================== */
$orderStmt = $conn->prepare("
    SELECT order_id
    FROM orders
    WHERE order_id = ? AND user_id = ?
");
$orderStmt->bind_param("ii", $order_id, $user_id);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();
$orderStmt->close();

if (!$order) {
    die("Заказ не найден или доступ запрещён.");
}

/* ===============================
   ОБРАБОТКА ФОРМЫ
=============================== */
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if ($subject === '') {
        $errors[] = "Укажите тему обращения.";
    }
    if ($message === '') {
        $errors[] = "Введите сообщение.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("
            INSERT INTO support_tickets (user_id, order_id, subject, message)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiss", $user_id, $order_id, $subject, $message);
        $stmt->execute();
        $stmt->close();

        header("Location: profile.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Обращение в поддержку</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php">TimCockStore</a>
    </div>
</header>

<main class="support">
    <h1>Обращение в поддержку</h1>

    <p><strong>Заказ №<?= $order_id ?></strong></p>

    <?php if (!empty($errors)): ?>
        <div class="error-message">
            <?php foreach ($errors as $error): ?>
                <p><?= htmlspecialchars($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Тема</label>
            <input type="text" name="subject" required>
        </div>

        <div class="form-group">
            <label>Сообщение</label>
            <textarea name="message" rows="5" required></textarea>
        </div>

        <button type="submit" class="button">Отправить</button>
    </form>

    <br>
    <a href="order-details.php?order_id=<?= $order_id ?>">← Назад к заказу</a>
</main>

</body>
</html>
