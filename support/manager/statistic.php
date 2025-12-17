<?php
session_start();

// Check if the user is logged in and has the role of manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'manager') {
    header("Location: ../htdocs/index.php");
    exit();
}

// Include the database connection file
include 'db_connect.php';

// Fetch data for the chart
$sql_category_stats = "SELECT c.name, COUNT(t.id) AS ticket_count
                       FROM categories c
                       LEFT JOIN tickets t ON c.id = t.category_id
                       GROUP BY c.id";
$result_category_stats = $conn->query($sql_category_stats);

$categories = [];
$ticket_counts = [];

if ($result_category_stats->num_rows > 0) {
    while ($row = $result_category_stats->fetch_assoc()) {
        $categories[] = htmlspecialchars($row['name']);
        $ticket_counts[] = intval($row['ticket_count']);
    }
}

// Fetch general statistics
$sql_total_tickets = "SELECT COUNT(*) AS total FROM tickets";
$sql_open_tickets = "SELECT COUNT(*) AS open FROM tickets WHERE status != 'resolved' AND status != 'rejected'";
$sql_resolved_tickets = "SELECT COUNT(*) AS resolved FROM tickets WHERE status = 'resolved'";
$sql_rejected_tickets = "SELECT COUNT(*) AS rejected FROM tickets WHERE status = 'rejected'";

$result_total_tickets = $conn->query($sql_total_tickets);
$result_open_tickets = $conn->query($sql_open_tickets);
$result_resolved_tickets = $conn->query($sql_resolved_tickets);
$result_rejected_tickets = $conn->query($sql_rejected_tickets);

$total_tickets = $result_total_tickets->num_rows > 0 ? intval($result_total_tickets->fetch_assoc()['total']) : 0;
$open_tickets = $result_open_tickets->num_rows > 0 ? intval($result_open_tickets->fetch_assoc()['open']) : 0;
$resolved_tickets = $result_resolved_tickets->num_rows > 0 ? intval($result_resolved_tickets->fetch_assoc()['resolved']) : 0;
$rejected_tickets = $result_rejected_tickets->num_rows > 0 ? intval($result_rejected_tickets->fetch_assoc()['rejected']) : 0;


$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Статистика</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <h1>Статистика</h1>
            </header>

            <section class="content">
                <h2>Общая статистика</h2>
                <div class="statistics">
                    <div class="statistic-item">
                        <span class="statistic-label">Всего обращений:</span>
                        <span class="statistic-value"><?php echo $total_tickets; ?></span>
                    </div>
                    <div class="statistic-item">
                        <span class="statistic-label">Новых обращений:</span>
                        <span class="statistic-value"><?php echo $open_tickets; ?></span>
                    </div>
                    <div class="statistic-item">
                        <span class="statistic-label">Решенных обращений:</span>
                        <span class="statistic-value"><?php echo $resolved_tickets; ?></span>
                    </div>
                    <div class="statistic-item">
                        <span class="statistic-label">Отклоненных обращений:</span>
                        <span class="statistic-value"><?php echo $rejected_tickets; ?></span>
                    </div>
                </div>

                <h2>Статистика по категориям</h2>
                <div class="category-statistics">
                    <canvas id="categoryChart" width="400" height="200"></canvas>
                </div>
            </section>
        </main>
    </div>

    <script>
        // Пример данных для графика (замените на реальные данные)
        const categoryData = {
            labels: [<?php echo '"' . implode('", "', $categories) . '"'; ?>],
            datasets: [{
                label: 'Количество обращений',
                data: [<?php echo implode(', ', $ticket_counts); ?>],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        };

        // Create the chart
        const ctx = document.getElementById('categoryChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'bar',
            data: categoryData,
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Количество обращений по категориям'
                    }
                }
            }
        });
    </script>
</body>
</html>