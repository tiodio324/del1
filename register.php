<?php
session_start(); // Start the session

// Check if the user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Redirect to the main page if logged in
    exit();
}

// Include the database connection file
include 'inc/db_connect.php';

$errors = array(); // Array to store error messages
$success = false; // Flag for successful registration

// Function to sanitize user input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Handle registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize registration form data
    $name = sanitize($_POST["name"]);
    $email = sanitize($_POST["email"]);
    $password = $_POST["password"];

    // Validate registration form data
    if (empty($name)) {
        $errors[] = "Пожалуйста, введите ваше имя.";
    }
    if (empty($email)) {
        $errors[] = "Пожалуйста, введите ваш email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Пожалуйста, введите корректный email.";
    }
    if (empty($password)) {
        $errors[] = "Пожалуйста, введите пароль.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Пароль должен содержать не менее 6 символов.";
    }

    // If there are no registration errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare an insert statement
        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Bind parameters
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                $success = true;
                // Redirect to login page after successful registration
                header("Location: login.php");
                exit();
            } else {
                $errors[] = "Произошла ошибка при регистрации. Пожалуйста, попробуйте позже.";
            }
            // Close statement
            $stmt->close();
        } else {
            $errors[] = "Произошла ошибка при регистрации. Пожалуйста, попробуйте позже.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimCockStore - Регистрация</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="index.php">TimCockStore</a>
        </div>
        <nav>
            <ul>
                <li><a href="catalog.php">Каталог</a></li>
                <li><a href="cart.php">Корзина</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php">Личный кабинет</a></li>
                    <li><a href="logout.php">Выйти</a></li>
                <?php else: ?>
                    <li><a href="login.php">Вход</a></li>
                    <li><a href="register.php">Регистрация</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="search">
            <input type="text" placeholder="Поиск...">
            <button>Найти</button>
        </div>
    </header>

    <main class="register">
        <h1>Регистрация</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <p>Регистрация прошла успешно! Теперь вы можете <a href="login.php">войти</a>.</p>
        <?php else: ?>
            <form method="post">
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

                <button type="submit" class="button">Зарегистрироваться</button>
            </form>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <div class="footer-info">
                <p>© 2024 TimCockStore</p>
            </div>
            <div class="social-links">
                <!-- Здесь будут ссылки на социальные сети -->
            </div>
            <div class="contact-info">
                <!-- Здесь будет контактная информация -->
            </div>
        </div>
    </footer>
</body>
</html>

<?php
$conn->close();
?>