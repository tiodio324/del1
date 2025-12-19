<?php
require_once __DIR__ . '/inc/admin-check.php';
require_once __DIR__ . '/../inc/db_connect.php';

/* Изменение роли */
if (isset($_POST['role'])) {
    $stmt = $conn->prepare("UPDATE users SET role=? WHERE id=?");
    $stmt->bind_param("si", $_POST['role'], $_POST['id']);
    $stmt->execute();
}

/* Удаление */
if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM users WHERE id=".(int)$_GET['delete']);
}

$users = $conn->query("SELECT * FROM users");
?>

<h2>Пользователи</h2>

<?php while ($u = $users->fetch_assoc()): ?>
<form method="post">
    <?= htmlspecialchars($u['email']) ?>
    <input type="hidden" name="id" value="<?= $u['id'] ?>">
    <select name="role">
        <option <?= $u['role']=='user'?'selected':'' ?>>user</option>
        <option <?= $u['role']=='support'?'selected':'' ?>>support</option>
        <option <?= $u['role']=='manager'?'selected':'' ?>>manager</option>
    </select>
    <button>Сохранить</button>
    <a href="?delete=<?= $u['id'] ?>">❌</a>
</form>
<?php endwhile; ?>

<a href="admin.php">← Назад</a>
