<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../htdocs/index.php"); // Redirect to the login page if not logged in
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Include the database connection file
include 'db_connect.php';

$errors = array(); // Array to store error messages
$success = false; // Flag to indicate successful ticket creation

// Process the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate the form data
    $category_id = $_POST["category"]; // Get category ID from the form
    $description = trim($_POST["description"]);
    $title = trim($_POST["title"]); // Get title from the form

    // Validate category
    if (empty($category_id)) {
        $errors[] = "Пожалуйста, выберите категорию проблемы.";
    }

    // Validate description
    if (empty($description)) {
        $errors[] = "Пожалуйста, введите описание проблемы.";
    }

    // Validate title
    if (empty($title)) {
        $errors[] = "Пожалуйста, введите заголовок обращения.";
    }


    // If there are no errors, proceed with ticket creation
    if (empty($errors)) {
        // Find the support user with the fewest open tickets
        $sql_get_support = "SELECT id FROM users WHERE role = 'support'";
        $result_get_support = $conn->query($sql_get_support);

        $support_users = [];
        while ($support_user = $result_get_support->fetch_assoc()) {
            $support_users[] = $support_user['id'];
        }

        if (!empty($support_users)) {
            $min_tickets = PHP_INT_MAX;
            $assigned_support_id = null;

            foreach ($support_users as $support_id) {
                $sql_count_tickets = "SELECT COUNT(*) AS ticket_count FROM tickets WHERE assigned_to = ? AND status != 'resolved' AND status != 'rejected'";
                $stmt_count_tickets = $conn->prepare($sql_count_tickets);
                $stmt_count_tickets->bind_param("i", $support_id);
                $stmt_count_tickets->execute();
                $result_count_tickets = $stmt_count_tickets->get_result();
                $row_count_tickets = $result_count_tickets->fetch_assoc();
                $ticket_count = $row_count_tickets['ticket_count'];

                if ($ticket_count < $min_tickets) {
                    $min_tickets = $ticket_count;
                    $assigned_support_id = $support_id;
                }
                $stmt_count_tickets->close();
            }

            if ($assigned_support_id !== null) {
                // Prepare an insert statement
                $sql = "INSERT INTO tickets (user_id, category_id, title, description, status, assigned_to) VALUES (?, ?, ?, ?, 'new', ?)";
                $stmt = $conn->prepare($sql);

                if ($stmt) {
                    // Bind parameters
                    $stmt->bind_param("iisssi", $user_id, $category_id, $title, $description, $assigned_support_id); // Fixed typo and added 'i' for assigned_to
                    // Attempt to execute the prepared statement
                    if ($stmt->execute()) {
                        // Set success flag to true
                        $success = true;
                    } else {
                        $errors[] = "Произошла ошибка при создании обращения. Пожалуйста, попробуйте позже.";
                    }
                    // Close statement
                    $stmt->close();
                } else {
                    $errors[] = "Произошла ошибка при создании обращения. Пожалуйста, попробуйте позже.";
                }
            } else {
                $errors[] = "Не удалось назначить обращение сотруднику поддержки.";
            }

        } else {
            $errors[] = "В системе нет сотрудников поддержки.";
        }

        // Close connection
        $conn->close();
    }
}

// Fetch categories from the database for the dropdown list
$categories_sql = "SELECT id, name FROM categories";
$categories_result = $conn->query($categories_sql);

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создать обращение</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="container">
        <aside class="sidebar">
            <!-- Меню (как на главной странице клиента) -->
            <div class="sidebar-header">
                <h3>Меню</h3>
            </div>
            <nav>
                <ul>
                    <li><a href="client.php"><i class="fa fa-home"></i> Главная</a></li>
                    <li><a href="make.php"><i class="fa fa-plus"></i> Создать обращение</a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out"></i> Выйти</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Создать обращение</h1>
            </header>

            <section class="content">
                <h2>Заполните форму обращения</h2>
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        Обращение успешно создано!
                    </div>
                <?php endif; ?>
                <form id="createTicketForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                  <div class="form-group">
                      <label for="title">Заголовок:</label>
                      <input type="text" id="title" name="title" required>
                  </div>
                    <div class="form-group">
                        <label for="category">Категория проблемы:</label>
                        <select id="category" name="category">
                            <?php
                                if ($categories_result->num_rows > 0) {
                                    while($category = $categories_result->fetch_assoc()) {
                                        echo '<option value="' . htmlspecialchars($category["id"]) . '">' . htmlspecialchars($category["name"]) . '</option>';
                                    }
                                } else {
                                    echo '<option value="">Нет доступных категорий</option>';
                                }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">Описание проблемы:</label>
                        <textarea id="description" name="description" rows="5"></textarea>
                    </div>

                    <!--  Убрал возможность прикреплять файлы, так как это сложнее реализовать на данном этапе
                    <div class="form-group">
                        <label for="attachment">Прикрепить файл:</label>
                        <input type="file" id="attachment" name="attachment">
                    </div>
                     -->

                    <button type="submit" class="button create-ticket-button">Отправить обращение</button>
                </form>
            </section>
        </main>
    </div>
</body>
</html>

<?php
$conn->close();
?>