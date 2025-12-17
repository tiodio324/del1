<?php
session_start(); // Start the session

// Check if the user is logged in and has the role of manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'manager') {
    header("Location: ../htdocs/index.php"); // Redirect to the login page if not logged in or not a manager
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Include the database connection file
include 'db_connect.php';

// SQL queries to retrieve statistics
$sql_total_tickets = "SELECT COUNT(*) AS total FROM tickets";
$sql_open_tickets = "SELECT COUNT(*) AS open FROM tickets WHERE status != 'resolved' AND status != 'rejected'";
$sql_resolved_tickets = "SELECT COUNT(*) AS resolved FROM tickets WHERE status = 'resolved'";
$sql_rejected_tickets = "SELECT COUNT(*) AS rejected FROM tickets WHERE status = 'rejected'";
// Add more queries as needed for your statistics

// Prepare and execute the queries
$stmt_total_tickets = $conn->prepare($sql_total_tickets);
$stmt_open_tickets = $conn->prepare($sql_open_tickets);
$stmt_resolved_tickets = $conn->prepare($sql_resolved_tickets);
$stmt_rejected_tickets = $conn->prepare($sql_rejected_tickets);


$stmt_total_tickets->execute();
$stmt_open_tickets->execute();
$stmt_resolved_tickets->execute();
$stmt_rejected_tickets->execute();

$result_total_tickets = $stmt_total_tickets->get_result();
$result_open_tickets = $stmt_open_tickets->get_result();
$result_resolved_tickets = $stmt_resolved_tickets->get_result();
$result_rejected_tickets = $stmt_rejected_tickets->get_result();


// Fetch the statistics data
$total_tickets = $result_total_tickets->fetch_assoc()['total'];
$open_tickets = $result_open_tickets->fetch_assoc()['open'];
$resolved_tickets = $result_resolved_tickets->fetch_assoc()['resolved'];
$rejected_tickets = $result_rejected_tickets->fetch_assoc()['rejected'];
// Fetch more statistics data as needed

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Система управления обращениями</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                <h1>Главная страница (Менеджер)</h1>
            </header>

            <section class="content">
                <h2>Добро пожаловать, Менеджер!</h2>
                <p>Здесь вы можете управлять системой и просматривать статистику.</p>

                <h3>Общая статистика</h3>
                <div class="statistics">
                    <div class="statistic-item">
                        <span class="statistic-label">Всего обращений:</span>
                        <span class="statistic-value"><?php echo htmlspecialchars($total_tickets); ?></span>
                    </div>
                    <div class="statistic-item">
                        <span class="statistic-label">Новых обращений:</span>
                        <span class="statistic-value"><?php echo htmlspecialchars($open_tickets); ?></span>
                    </div>
                    <div class="statistic-item">
                        <span class="statistic-label">Решенных обращений:</span>
                        <span class="statistic-value"><?php echo htmlspecialchars($resolved_tickets); ?></span>
                    </div>
                    <div class="statistic-item">
                        <span class="statistic-label">Отклоненных обращений:</span>
                        <span class="statistic-value"><?php echo htmlspecialchars($rejected_tickets); ?></span>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

<?php
// Close the prepared statements
$stmt_total_tickets->close();
$stmt_open_tickets->close();
$stmt_resolved_tickets->close();
$stmt_rejected_tickets->close();

// Close the database connection
$conn->close();
?>