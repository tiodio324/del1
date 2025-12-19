<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'support') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/inc/db_connect.php';

$support_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT
        st.id,
        st.order_id,
        st.subject,
        st.status,
        st.created_at,
        u.name AS user_name
    FROM support_tickets st
    JOIN users u ON u.id = st.user_id
    WHERE st.support_id = ?
    ORDER BY st.created_at DESC
");
$stmt->bind_param("i", $support_id);
$stmt->execute();
$result = $stmt->get_result();

$tickets = [];
while ($row = $result->fetch_assoc()) {
    $tickets[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Поддержка — обращения</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="logo"><a href="index.php">TimCockStore</a></div>
    <nav>
        <ul>
            <li><a href="support-dashboard.php">Обращения</a></li>
            <li><a href="logout.php">Выйти</a></li>
        </ul>
    </nav>
</header>

<main class="support">
    <h1>Мои обращения</h1>

    <?php if (empty($tickets)): ?>
        <p>Нет назначенных обращений.</p>
    <?php else: ?>
        <?php foreach ($tickets as $t): ?>
            <div class="support-ticket">
                <p><strong>Обращение №<?= $t['id'] ?></strong></p>
                <p>Пользователь: <?= htmlspecialchars($t['user_name']) ?></p>
                <p>Заказ №<?= $t['order_id'] ?></p>
                <p>Тема: <?= htmlspecialchars($t['subject']) ?></p>
                <p>Статус: <?= htmlspecialchars($t['status']) ?></p>
                <p>Дата: <?= $t['created_at'] ?></p>
                <a href="support-ticket.php?id=<?= $t['id'] ?>" class="button">
                    Открыть
                </a>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</main>

</body>
</html>
