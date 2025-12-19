<?php
require_once __DIR__ . '/inc/admin-check.php';
require_once __DIR__ . '/../inc/db_connect.php';

/* Добавление */
if (isset($_POST['add'])) {
    $name = trim($_POST['name']);
    if ($name !== '') {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $name);
        $stmt->execute();
    }
}

/* Удаление */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM categories WHERE id = $id");
}

$categories = $conn->query("SELECT * FROM categories");
?>

<h2>Категории</h2>

<form method="post">
    <input name="name" placeholder="Название категории">
    <button name="add">Добавить</button>
</form>

<ul>
<?php while ($c = $categories->fetch_assoc()): ?>
    <li>
        <?= htmlspecialchars($c['name']) ?>
        <a href="?delete=<?= $c['id'] ?>">❌</a>
    </li>
<?php endwhile; ?>
</ul>

<a href="admin.php">← Назад</a>
