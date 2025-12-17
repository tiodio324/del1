<?php
session_start(); // Start the session

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../htdocs/index.php"); // Redirect to the main page if logged in
    exit();
}

$errors = array(); // Array to store error messages

// Process the registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Include the database connection file
    
$servername = "sql309.infinityfree.com"; //  Замените на имя вашего сервера
$username = "if0_39045192";   //  Замените на ваше имя пользователя базы данных
$password = "0XDe6EsLHTXr";   //  Замените на ваш пароль базы данных
$dbname = "if0_39045192_practic";    //  Замените на имя вашей базы данных

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}


    // Validate the form data
    $name = trim($_POST["register-name"]);
    $email = trim($_POST["register-email"]);
    $password = $_POST["register-password"];

    // Validate name
    if (empty($name)) {
        $errors[] = "Пожалуйста, введите ваше имя.";
    }

    // Validate email
    if (empty($email)) {
        $errors[] = "Пожалуйста, введите ваш email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Пожалуйста, введите корректный email.";
    }

    // Validate password
    if (empty($password)) {
        $errors[] = "Пожалуйста, введите пароль.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Пароль должен содержать не менее 6 символов.";
    }

    // If there are no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare an insert statement
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'client')";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Bind parameters
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to login page after successful registration
                header("Location: index.php?success=true");
                exit();
            } else {
                $errors[] = "Произошла ошибка при регистрации. Пожалуйста, попробуйте позже.";
            }

            // Close statement
            $stmt->close();
        } else {
            $errors[] = "Произошла ошибка при регистрации. Пожалуйста, попробуйте позже.";
        }

        // Close connection
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация и Регистрация</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-tabs">
                <button class="tab-button active" data-tab="login">Авторизация</button>
                <button class="tab-button" data-tab="register">Регистрация</button>
            </div>

            <div class="auth-forms">
                <form id="login-form" class="auth-form active">
                    <h2>Авторизация</h2>
                    <div class="form-group">
                        <label for="login-email">Email:</label>
                        <input type="email" id="login-email" name="login-email" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Пароль:</label>
                        <input type="password" id="login-password" name="login-password" required>
                    </div>
                    <button type="submit" class="button">Войти</button>
                </form>

                <form id="register-form" class="auth-form" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <h2>Регистрация</h2>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_GET['success']) && $_GET['success'] == 'true'): ?>
                        <div class="alert alert-success">
                            Регистрация прошла успешно! Теперь вы можете войти.
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="register-name">Имя:</label>
                        <input type="text" id="register-name" name="register-name" required>
                    </div>
                    <div class="form-group">
                        <label for="register-email">Email:</label>
                        <input type="email" id="register-email" name="register-email" required>
                    </div>
                    <div class="form-group">
                        <label for="register-password">Пароль:</label>
                        <input type="password" id="register-password" name="register-password" required>
                    </div>
                    <button type="submit" class="button">Зарегистрироваться</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const tabButtons = document.querySelectorAll('.tab-button');
        const authForms = document.querySelectorAll('.auth-form');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Деактивируем все кнопки
                tabButtons.forEach(btn => btn.classList.remove('active'));
                // Скрываем все формы
                authForms.forEach(form => form.classList.remove('active'));

                // Активируем текущую кнопку
                button.classList.add('active');
                // Показываем соответствующую форму
                const tab = button.dataset.tab;
                document.getElementById(`${tab}-form`).classList.add('active');
            });
        });
    </script>
</body>
</html>