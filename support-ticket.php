<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'support') {
    header("Location: login.php");
    exit();
}

require_once __DIR__ . '/inc/db_connect.php';

$support_id = $_SESSION['user_id'];
$ticket_id = (int)($_GET['id'] ?? 0);

/* ===============================
   ПРОВЕРКА ДОСТУПА
=============================== */
$stmt = $conn->prepare("
    SELECT *
    FROM support_tickets
    WHERE id = ? AND support_id = ?
");
$stmt->bind_param("ii", $ticket_id, $support_id);
$stmt->execute();
$ticket = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$ticket) {
    die("Обращение не найдено или не назначено вам.");
}

/* ===============================
   ДОБАВЛЕНИЕ ОТВЕТА
=============================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['message'])) {
        $msg = trim($_POST['message']);

        if ($msg !== '') {
            $stmt = $conn->prepare("
                INSERT INTO support_messages (ticket_id, sender, message)
                VALUES (?, 'support', ?)
            ");
            $stmt->bind_param("is", $ticket_id, $msg);
            $stmt->execute();
            $stmt->close();

            // Меняем статус
            $conn->query("
                UPDATE support_tickets
                SET status = 'answered'
                WHERE id = $ticket_id
            ");
        }
    }

    if (isset($_POST['status'])) {
        $status = $_POST['status'];
        $stmt = $conn->prepare("
            UPDATE support_tickets SET status = ?
            WHERE id = ?
        ");
        $stmt->bind_param("si", $status, $ticket_id);
        $stmt->execute();
        $stmt->close();
    }
}

/* ===============================
   ИСТОРИЯ СООБЩЕНИЙ
=============================== */
$msgs = $conn->query("
    SELECT sender, message, created_at
    FROM support_messages
    WHERE ticket_id = $ticket_id
    ORDER BY created_at
");
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Обращение №<?= $ticket_id ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<main class="support">
    <h1>Обращение №<?= $ticket_id ?></h1>

    <p><strong>Статус:</strong> <?= $ticket['status'] ?></p>

    <h3>Переписка</h3>
    <?php while ($m = $msgs->fetch_assoc()): ?>
        <p>
            <strong><?= $m['sender'] === 'support' ? 'Поддержка' : 'Клиент' ?>:</strong>
            <?= htmlspecialchars($m['message']) ?>
            <br><small><?= $m['created_at'] ?></small>
        </p>
    <?php endwhile; ?>

    <hr>

    <form method="post">
        <textarea name="message" rows="4" placeholder="Ответ клиенту"></textarea>
        <button class="button">Отправить ответ</button>
    </form>

    <form method="post">
        <select name="status">
            <option value="open">Открыто</option>
            <option value="answered">Отвечено</option>
            <option value="closed">Закрыто</option>
        </select>
        <button class="button">Изменить статус</button>
    </form>

    <br>
    <a href="support-dashboard.php">← Назад</a>
</main>

</body>
</html>
