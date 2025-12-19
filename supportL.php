<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/inc/db_connect.php';

$user_id = $_SESSION['user_id'];

/* ===============================
   ПОЛУЧЕНИЕ ОБРАЩЕНИЙ
=============================== */
$stmt = $conn->prepare("
    SELECT
        st.id,
        st.order_id,
        st.subject,
        st.status,
        st.created_at
    FROM support_tickets st
    WHERE st.user_id = ?
    ORDER BY st.created_at DESC
");
$stmt->bind_param("i", $user_id);
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
    <title>TimCockStore — Поддержка</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
    <div class="logo">
        <a href="index.php">TimCockStore</a>
    </div>
    <nav>
        <ul>
            <li><a href="profile.php">Личный кабинет</a></li>
            <li><a href="cart.php">Корзина</a></li>
            <li><a href="logout.php">Выйти</a></li>
        </ul>
    </nav>
</header>

<main class="support">
    <h1>Мои обращения в поддержку</h1>

    <?php if (empty($tickets)): ?>
        <p>У вас пока нет обращений в поддержку.</p>
    <?php else: ?>
        <?php foreach ($tickets as $ticket): ?>
            <div class="support-ticket">
                <p><strong>Обращение №<?= $ticket['id'] ?></strong></p>
                <p>Заказ:
                    <a href="order-details.php?order_id=<?= $ticket['order_id'] ?>">
                        №<?= $ticket['order_id'] ?>
                    </a>
                </p>
                <p>Тема: <?= htmlspecialchars($ticket['subject']) ?></p>
                <p>Статус:
                    <?php
                    switch ($ticket['status']) {
                        case 'open': echo 'Открыто'; break;
                        case 'answered': echo 'Отвечено'; break;
                        case 'closed': echo 'Закрыто'; break;
                    }
                    ?>
                </p>
                <p>Дата: <?= htmlspecialchars($ticket['created_at']) ?></p>
                <hr>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="profile.php" class="button">← Назад в профиль</a>
</main>

<footer>
    <div class="container">
        <p>© 2024 TimCockStore</p>
    </div>
</footer>

</body>
</html>
