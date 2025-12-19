<?php
require_once __DIR__ . '/inc/admin-check.php';
require_once __DIR__ . '/../inc/db_connect.php';

$cats = $conn->query("SELECT * FROM categories");

/* Добавление товара */
if (isset($_POST['add'])) {
    $stmt = $conn->prepare("
        INSERT INTO products (name, price, category_id)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param(
        "sdi",
        $_POST['name'],
        $_POST['price'],
        $_POST['category_id']
    );
    $stmt->execute();
}

$products = $conn->query("
    SELECT p.*, c.name AS category
    FROM products p
    JOIN categories c ON c.id = p.category_id
");
?>

<h2>Товары</h2>

<form method="post">
    <input name="name" placeholder="Название">
    <input name="price" type="number" step="0.01">
    <select name="category_id">
        <?php while ($c = $cats->fetch_assoc()): ?>
            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
        <?php endwhile; ?>
    </select>
    <button name="add">Добавить</button>
</form>

<table border="1">
<tr><th>Название</th><th>Цена</th><th>Категория</th></tr>
<?php while ($p = $products->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($p['name']) ?></td>
    <td><?= $p['price'] ?></td>
    <td><?= $p['category'] ?></td>
</tr>
<?php endwhile; ?>
</table>

<a href="admin.php">← Назад</a>
