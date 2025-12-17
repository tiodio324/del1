<?php
session_start(); // Start the session

// Check if the user is logged in and has the role of support
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'support') {
    header("Location: ../htdocs/index.php"); // Redirect to the login page if not logged in or not a support user
    exit();
}

$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Include the database connection file
include 'db_connect.php';

// SQL queries to retrieve statistics for the current support user
// Replace the example queries with your actual logic
$sql_processed_yesterday = "SELECT COUNT(*) AS count FROM tickets WHERE assigned_to = ? AND status IN ('resolved', 'rejected') AND DATE(updated_at) = DATE(CURDATE() - INTERVAL 1 DAY)";
$sql_processed_this_week = "SELECT COUNT(*) AS count FROM tickets WHERE assigned_to = ? AND status IN ('resolved', 'rejected') AND WEEK(updated_at, 1) = WEEK(CURDATE(), 1)";
$sql_new_tickets = "SELECT COUNT(*) AS count FROM tickets WHERE assigned_to = ? AND status = 'new'";

// Prepare and execute the queries
$stmt_processed_yesterday = $conn->prepare($sql_processed_yesterday);
$stmt_processed_this_week = $conn->prepare($sql_processed_this_week);
$stmt_new_tickets = $conn->prepare($sql_new_tickets);

$stmt_processed_yesterday->bind_param("i", $user_id);
$stmt_processed_this_week->bind_param("i", $user_id);
$stmt_new_tickets->bind_param("i", $user_id);

$stmt_processed_yesterday->execute();
$stmt_processed_this_week->execute();
$stmt_new_tickets->execute();

$result_processed_yesterday = $stmt_processed_yesterday->get_result();
$result_processed_this_week = $stmt_processed_this_week->get_result();
$result_new_tickets = $stmt_new_tickets->get_result();

// Fetch the statistics data
$processed_yesterday = $result_processed_yesterday->fetch_assoc()['count'];
$processed_this_week = $result_processed_this_week->fetch_assoc()['count'];
$new_tickets = $result_new_tickets->fetch_assoc()['count'];

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
                    <li><a href="support.php"><i class="fa fa-home"></i> Главная</a></li>
                    <li><a href="work.php"><i class="fa fa-inbox"></i> Входящие обращения</a></li>
                    <li><a href="processed.php"><i class="fa fa-check-square"></i> Обработанные обращения</a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out"></i> Выйти</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Главная страница (Сотрудник поддержки)</h1>
            </header>

            <section class="content">
                <h2>Добро пожаловать, Сотрудник!</h2>

                <h3>Сводка по обращениям</h3>
                <div class="summary-cards">
                    <div class="summary-card">
                        <i class="fas fa-check-circle summary-icon"></i>
                        <div class="summary-text">
                            <span class="summary-value"><?php echo htmlspecialchars($processed_yesterday); ?></span>
                            <span class="summary-label">Обработано вчера</span>
                        </div>
                    </div>

                    <div class="summary-card">
                        <i class="fas fa-check-double summary-icon"></i>
                        <div class="summary-text">
                            <span class="summary-value"><?php echo htmlspecialchars($processed_this_week); ?></span>
                            <span class="summary-label">Обработано за неделю</span>
                        </div>
                    </div>

                    <div class="summary-card">
                        <i class="fas fa-exclamation-triangle summary-icon"></i>
                        <div class="summary-text">
                            <span class="summary-value"><?php echo htmlspecialchars($new_tickets); ?></span>
                            <span class="summary-label">Новых обращений</span>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

<?php
// Close the prepared statements
$stmt_processed_yesterday->close();
$stmt_processed_this_week->close();
$stmt_new_tickets->close();

// Close the database connection
$conn->close();
?>