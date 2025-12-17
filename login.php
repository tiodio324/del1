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

// Function to sanitize user input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize login form data
    $email = sanitize($_POST["email"]);
    $password = $_POST["password"];

    // Validate login form data
    if (empty($email)) {
        $errors[] = "Пожалуйста, введите ваш email.";
    }
    if (empty($password)) {
        $errors[] = "Пожалуйста, введите ваш пароль.";
    }

    // If there are no login errors, attempt to authenticate the user
    if (empty($errors)) {
        // Prepare a select statement
        $sql = "SELECT id, password, role FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            // Bind parameters
            $stmt->bind_param("s", $email);

            // Attempt to execute the prepared statement
            $stmt->execute();

            // Store result
            $stmt->store_result();

            // Check if email exists, if yes then verify password
            if ($stmt->num_rows == 1) {
                $stmt->bind_result($user_id, $hashed_password, $role);
                if ($stmt->fetch()) {
                    if (password_verify($password, $hashed_password)) {
                        // Password is correct, so start a new session
                        session_start();

                        // Store data in session variables
                        $_SESSION["user_id"] = $user_id;
                        $_SESSION["role"] = $role;

                        // Redirect user to the appropriate page based on the role
                        switch ($role) {
                            case 'client':
                                header("Location: index.php"); // Change the redirect URL as needed
                                break;
                            case 'support':
                                header("Location: support.php"); // Change the redirect URL as needed
                                break;
                            case 'manager':
                                header("Location: manager.php"); // Change the redirect URL as needed
                                break;
                            default:
                                header("Location: index.php"); // Or a default page if the role is not recognized
                        }
                        exit();
                    } else {
                        // Password is not valid, display a generic error message
                        $errors[] = "Неверный пароль.";
                    }
                }
            } else {
                // Email doesn't exist, display a generic error message
                $errors[] = "Пользователь с таким email не найден.";
            }

            // Close statement
            $stmt->close();
        } else {
            $errors[] = "Произошла ошибка при входе. Пожалуйста, попробуйте позже.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TimCockStore - Вход</title>
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

    <main class="login">
        <h1>Вход</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?php echo htmlspecialchars($error); ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="button">Войти</button>
        </form>
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