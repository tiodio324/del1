<?php
session_start();

// Check if the user is logged in and has the role of manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'manager') {
    header("Location: ../htdocs/index.php");
    exit();
}

// Include the database connection file
include 'db_connect.php';

// Function to sanitize user input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

$message = "";
$errors = [];

// Handle category deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['category_id'])) {
    $category_id_to_delete = sanitize($_POST['category_id']);

    // Prepare and execute the DELETE statement
    $sql_delete_category = "DELETE FROM categories WHERE id = ?";
    $stmt_delete_category = $conn->prepare($sql_delete_category);
    $stmt_delete_category->bind_param("i", $category_id_to_delete);

    if ($stmt_delete_category->execute()) {
        $message = "Категория успешно удалена.";
    } else {
        $message = "Ошибка при удалении категории: " . $stmt_delete_category->error;
    }
    $stmt_delete_category->close();
}

// Handle category creation or update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'create') {
        // Handle category creation
        $category_name = sanitize($_POST['categoryName']);
        $category_description = sanitize($_POST['categoryDescription']);

        // Validate the data
        if (empty($category_name)) {
            $errors[] = "Пожалуйста, введите название категории.";
        }
        if (empty($category_description)) {
            $errors[] = "Пожалуйста, введите описание категории.";
        }

        if (empty($errors)) {
            // Prepare the SQL statement
            $sql_create_category = "INSERT INTO categories (name, description) VALUES (?, ?)";
            $stmt_create_category = $conn->prepare($sql_create_category);
            $stmt_create_category->bind_param("ss", $category_name, $category_description);

            if ($stmt_create_category->execute()) {
                $message = "Категория успешно создана.";
            } else {
                $message = "Ошибка при создании категории: " . $stmt_create_category->error;
            }
            $stmt_create_category->close();
        } else {
            $message = "Ошибка при создании категории: " . implode(", ", $errors);
        }
    } elseif ($_POST['action'] == 'update' && isset($_POST['category_id'])) {
        // Handle category update
        $category_id_to_update = sanitize($_POST['category_id']);
        $category_name = sanitize($_POST['categoryName']);
        $category_description = sanitize($_POST['categoryDescription']);

        // Validate the data
        if (empty($category_name)) {
            $errors[] = "Пожалуйста, введите название категории.";
        }
        if (empty($category_description)) {
            $errors[] = "Пожалуйста, введите описание категории.";
        }

        if (empty($errors)) {
            // Prepare the SQL statement
            $sql_update_category = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
            $stmt_update_category = $conn->prepare($sql_update_category);
            $stmt_update_category->bind_param("ssi", $category_name, $category_description, $category_id_to_update);

            if ($stmt_update_category->execute()) {
                $message = "Категория успешно обновлена.";
            } else {
                $message = "Ошибка при обновлении категории: " . $stmt_update_category->error;
            }
            $stmt_update_category->close();
        } else {
            $message = "Ошибка при обновлении категории: " . implode(", ", $errors);
        }
    }
}

// Fetch categories from the database
$sql_get_categories = "SELECT id, name, description FROM categories";
$result_get_categories = $conn->query($sql_get_categories);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление категориями</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Style for the edit form */
        .edit-form {
            display: none;
            margin-top: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }
        .edit-form.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h3>Меню</h3>
            </div>
            <nav>
                <ul>
                    <li><a href="manager.php"><i class="fa fa-home"></i> Главная</a></li>
                    <li><a href="users.php"><i class="fa fa-users"></i> Управление пользователями</a></li>
                    <li><a href="categories.php"><i class="fa fa-list"></i> Управление категориями</a></li>
                    <li><a href="statistic.php"><i class="fa fa-chart-bar"></i> Статистика</a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out"></i> Выйти</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Управление категориями</h1>
            </header>

            <section class="content">
                <h2>Список категорий</h2>
                <?php if (!empty($message)): ?>
                    <div class="alert <?php echo (strpos($message, 'успешно') !== false) ? 'alert-success' : 'alert-danger'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <table class="category-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Название</th>
                            <th>Описание</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_get_categories->num_rows > 0) {
                            while ($category = $result_get_categories->fetch_assoc()) {
                                $category_id = htmlspecialchars($category['id']);
                                $category_name = htmlspecialchars($category['name']);
                                $category_description = htmlspecialchars($category['description']);

                                echo "<tr>";
                                echo "<td>" . $category_id . "</td>";
                                echo "<td>" . $category_name . "</td>";
                                echo "<td>" . $category_description . "</td>";
                                echo "<td>";
                                echo "<button class='button edit-button' data-category-id='" . $category_id . "'>Редактировать</button>";
                                echo "<form method='post' style='display:inline;' onsubmit='return confirm(\"Вы уверены, что хотите удалить эту категорию?\")'>";
                                echo "<input type='hidden' name='action' value='delete'>";
                                echo "<input type='hidden' name='category_id' value='" . $category_id . "'>";
                                echo "<button type='submit' class='button rejected'>Удалить</button>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";

                                // Edit Form
                                echo "<tr class='edit-form' data-category-id='" . $category_id . "'>";
                                echo "<td colspan='4'>";
                                echo "<form method='post'>";
                                echo "<input type='hidden' name='action' value='update'>";
                                echo "<input type='hidden' name='category_id' value='" . $category_id . "'>";
                                echo "<div class='form-group'>";
                                echo "<label for='categoryName_" . $category_id . "'>Название категории:</label>";
                                echo "<input type='text' id='categoryName_" . $category_id . "' name='categoryName' value='" . $category_name . "' required>";
                                echo "</div>";
                                echo "<div class='form-group'>";
                                echo "<label for='categoryDescription_" . $category_id . "'>Описание категории:</label>";
                                echo "<textarea id='categoryDescription_" . $category_id . "' name='categoryDescription' rows='3' required>" . $category_description . "</textarea>";
                                echo "</div>";
                                echo "<button type='submit' class='button'>Сохранить</button>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>Категории не найдены.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <h2>Добавить категорию</h2>
                <form method="post">
                    <input type="hidden" name="action" value="create">
                    <div class="form-group">
                        <label for="categoryName">Название категории:</label>
                        <input type="text" id="categoryName" name="categoryName" required>
                    </div>
                    <div class="form-group">
                        <label for="categoryDescription">Описание категории:</label>
                        <textarea id="categoryDescription" name="categoryDescription" rows="3" required></textarea>
                    </div>
                    <button type="submit" class="button">Добавить категорию</button>
                </form>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-button');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const categoryId = this.dataset.categoryId;
                    const editForm = document.querySelector(`.edit-form[data-category-id="${categoryId}"]`);
                    if (editForm) {
                        editForm.classList.toggle('active');
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
$conn->close();
?>