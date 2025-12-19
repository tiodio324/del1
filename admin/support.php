<?php
require_once __DIR__ . '/inc/admin-check.php';
require_once __DIR__ . '/../inc/db_connect.php';

$tickets = $conn->query("
    SELECT st.*, u.name AS user_name
    FROM support_tickets st
    JOIN users u ON u.id = st.user_id
");

$supports = $conn->query("SELECT id, name FROM users WHERE role='support'");
?>

<h2>Обращения в поддержку</h2>

<?php while ($t = $tickets->fetch_assoc()): ?>
<form method="post" action="support-assign.php">
    <strong>#<?= $t['id'] ?></strong> —
    <?= htmlspecialchars($t['subject']) ?>
    (<?= $t['status'] ?>)

    <input type="hidden" name="ticket_id" value="<?= $t['id'] ?>">

    <select name="support_id">
        <?php while ($s = $supports->fetch_assoc()): ?>
            <option value="<?= $s['id'] ?>">
                <?= htmlspecialchars($s['name']) ?>
            </option>
        <?php endwhile; ?>
    </select>

    <button>Назначить</button>
</form>
<hr>
<?php endwhile; ?>

<a href="admin.php">← Назад</a>
