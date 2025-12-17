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

$message = ""; // General message to display
$errors = [];

// Handle user deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'delete' && isset($_POST['user_id'])) {
    $user_id_to_delete = sanitize($_POST['user_id']);

    // Prepare and execute the DELETE statement
    $sql_delete_user = "DELETE FROM users WHERE id = ?";
    $stmt_delete_user = $conn->prepare($sql_delete_user);
    $stmt_delete_user->bind_param("i", $user_id_to_delete);

    if ($stmt_delete_user->execute()) {
        $message = "Пользователь успешно удален.";
    } else {
        $message = "Ошибка при удалении пользователя: " . $stmt_delete_user->error;
    }
    $stmt_delete_user->close();
}

// Handle user creation or update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    if ($_POST['action'] == 'create') {
        // Handle user creation
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $password = $_POST['password'];
        $role = sanitize($_POST['role']);

        // Validate the data
        if (empty($name)) {
            $errors[] = "Пожалуйста, введите имя пользователя.";
        }
        if (empty($email)) {
            $errors[] = "Пожалуйста, введите email.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Неверный формат email.";
        }
        if (empty($password)) {
            $errors[] = "Пожалуйста, введите пароль.";
        } elseif (strlen($password) < 6) {
            $errors[] = "Пароль должен быть не менее 6 символов.";
        }
        if (empty($role)) {
            $errors[] = "Пожалуйста, выберите роль.";
        }

        if (empty($errors)) {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Prepare the SQL statement
            $sql_create_user = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt_create_user = $conn->prepare($sql_create_user);
            $stmt_create_user->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($stmt_create_user->execute()) {
                $message = "Пользователь успешно создан.";
            } else {
                $message = "Ошибка при создании пользователя: " . $stmt_create_user->error;
            }
            $stmt_create_user->close();
        } else {
            $message = "Ошибка при создании пользователя: " . implode(", ", $errors);
        }
    } elseif ($_POST['action'] == 'update' && isset($_POST['user_id'])) {
        // Handle user update
        $user_id_to_update = sanitize($_POST['user_id']);
        $name = sanitize($_POST['name']);
        $email = sanitize($_POST['email']);
        $role = sanitize($_POST['role']);
        $password = $_POST['password']; // Password might be updated or left blank

        // Validate the data
        if (empty($name)) {
            $errors[] = "Пожалуйста, введите имя пользователя.";
        }
        if (empty($email)) {
            $errors[] = "Пожалуйста, введите email.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Неверный формат email.";
        }
        if (empty($role)) {
            $errors[] = "Пожалуйста, выберите роль.";
        }

        if (empty($errors)) {
            $update_sql = "UPDATE users SET name = ?, email = ?, role = ?";
            $params = "sss";
            $bind_params = [$name, $email, $role];

            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $update_sql .= ", password = ?";
                $params .= "s";
                $bind_params[] = $hashed_password;
            }

            $update_sql .= " WHERE id = ?";
            $params .= "i";
            $bind_params[] = $user_id_to_update;

            $stmt_update_user = $conn->prepare($update_sql);
            $stmt_update_user->bind_param($params, ...$bind_params);

            if ($stmt_update_user->execute()) {
                $message = "Данные пользователя успешно обновлены.";
            } else {
                $message = "Ошибка при обновлении данных пользователя: " . $stmt_update_user->error;
            }
            $stmt_update_user->close();
        } else {
            $message = "Ошибка при обновлении пользователя: " . implode(", ", $errors);
        }
    }
}


// Fetch users from the database
$sql_get_users = "SELECT id, name, email, role FROM users";
$result_get_users = $conn->query($sql_get_users);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Управление пользователями</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Style for the edit form (optional, but improves the visual) */
        .edit-form {
            display: none; /* Hidden by default */
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
                <h1>Управление пользователями</h1>
            </header>

            <section class="content">
                <h2>Список пользователей</h2>
                <?php if (!empty($message)): ?>
                    <div class="alert <?php echo (strpos($message, 'успешно') !== false) ? 'alert-success' : 'alert-danger'; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Имя</th>
                            <th>Email</th>
                            <th>Роль</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result_get_users->num_rows > 0) {
                            while ($user = $result_get_users->fetch_assoc()) {
                                $user_id = htmlspecialchars($user['id']);
                                $user_name = htmlspecialchars($user['name']);
                                $user_email = htmlspecialchars($user['email']);
                                $user_role = htmlspecialchars($user['role']);

                                echo "<tr>";
                                echo "<td>" . $user_id . "</td>";
                                echo "<td>" . $user_name . "</td>";
                                echo "<td>" . $user_email . "</td>";
                                echo "<td>" . $user_role . "</td>";
                                echo "<td>";
                                echo "<button class='button edit-button' data-user-id='" . $user_id . "'>Редактировать</button>";
                                echo "<form method='post' style='display:inline;' onsubmit='return confirm(\"Вы уверены, что хотите удалить этого пользователя?\")'>";
                                echo "<input type='hidden' name='action' value='delete'>";
                                echo "<input type='hidden' name='user_id' value='" . $user_id . "'>";
                                echo "<button type='submit' class='button rejected'>Удалить</button>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";

                                // Edit Form
                                echo "<tr class='edit-form' data-user-id='" . $user_id . "'>";
                                echo "<td colspan='5'>";
                                echo "<form method='post'>";
                                echo "<input type='hidden' name='action' value='update'>";
                                echo "<input type='hidden' name='user_id' value='" . $user_id . "'>";
                                echo "<div class='form-group'>";
                                echo "<label for='name_".$user_id."'>Имя:</label>";
                                echo "<input type='text' id='name_".$user_id."' name='name' value='" . $user_name . "' required>";
                                echo "</div>";
                                echo "<div class='form-group'>";
                                echo "<label for='email_".$user_id."'>Email:</label>";
                                echo "<input type='email' id='email_".$user_id."' name='email' value='" . $user_email . "' required>";
                                echo "</div>";
                                echo "<div class='form-group'>";
                                echo "<label for='role_".$user_id."'>Роль:</label>";
                                echo "<select id='role_".$user_id."' name='role' required>";
                                echo "<option value='client' " . ($user_role == 'client' ? 'selected' : '') . ">Клиент</option>";
                                echo "<option value='support' " . ($user_role == 'support' ? 'selected' : '') . ">Сотрудник поддержки</option>";
                                echo "<option value='manager' " . ($user_role == 'manager' ? 'selected' : '') . ">Менеджер</option>";
                                echo "</select>";
                                echo "</div>";
                                echo "<div class='form-group'>";
                                echo "<label for='password_".$user_id."'>Пароль (оставьте пустым, чтобы не менять):</label>";
                                echo "<input type='password' id='password_".$user_id."' name='password'>";
                                echo "</div>";
                                echo "<button type='submit' class='button'>Сохранить</button>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Пользователи не найдены.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>

                <h2>Добавить пользователя</h2>
                <form method="post">
                    <input type="hidden" name="action" value="create">
                    <div class="form-group">
                        <label for="name">Имя:</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Пароль:</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Роль:</label>
                        <select id="role" name="role" required>
                            <option value="">Выберите роль</option>
                            <option value="client">Клиент</option>
                            <option value="support">Сотрудник поддержки</option>
                            <option value="manager">Менеджер</option>
                        </select>
                    </div>
                    <button type="submit" class="button">Добавить пользователя</button>
                </form>
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const editButtons = document.querySelectorAll('.edit-button');
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.dataset.userId;
                    const editForm = document.querySelector(`.edit-form[data-user-id="${userId}"]`);
                    if (editForm) {
                        // Toggle the 'active' class
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