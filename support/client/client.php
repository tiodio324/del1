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

// SQL query to retrieve tickets for the current user
$sql = "SELECT id, title, status FROM tickets WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id); // "i" indicates the parameter is an integer
$stmt->execute();
$result = $stmt->get_result();
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
                    <li><a href="client.php"><i class="fa fa-home"></i> Главная</a></li>
                    <li><a href="make.php"><i class="fa fa-plus"></i> Создать обращение</a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out"></i> Выйти</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Главная страница (Клиент)</h1>
            </header>

            <section class="content">
                <h2>Добро пожаловать, Клиент!</h2>
                <p>Здесь вы можете управлять своими обращениями.</p>

                <a href="make.php" class="button create-ticket-button">Создать обращение</a>

                <h3>Список обращений</h3>
                <div class="ticket-list">
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while($row = $result->fetch_assoc()) {
                            $ticket_id = htmlspecialchars($row["id"]);
                            $ticket_title = htmlspecialchars($row["title"]);
                            $ticket_status = htmlspecialchars($row["status"]);

                            $status_class = "";
                            switch ($ticket_status) {
                                case "new":
                                    $status_class = "new";
                                    break;
                                case "in_progress":
                                    $status_class = "in-progress";
                                    break;
                                case "resolved":
                                    $status_class = "resolved";
                                    break;
                                case "rejected":
                                    $status_class = "rejected";
                                    break;
                            }

                            echo '<div class="ticket-item" data-ticket-id="' . $ticket_id . '" data-ticket-title="' . $ticket_title . '" data-ticket-status="' . $ticket_status . '">';
                            echo '<span class="ticket-id">#' . $ticket_id . '</span>';
                            echo '<span class="ticket-title">' . $ticket_title . '</span>';
                            echo '<span class="ticket-status ' . $status_class . '">' . ucfirst($ticket_status) . '</span>';
                            echo '</div>';
                        }
                    } else {
                        echo "<p>Нет обращений</p>";
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Модальное окно (оставил без изменений) -->
    <div class="modal" id="ticketModal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h2>Информация об обращении</h2>
            <p><b>ID:</b> <span id="modalTicketId"></span></p>
            <p><b>Тема:</b> <span id="modalTicketTitle"></span></p>
            <p><b>Статус:</b> <span id="modalTicketStatus"></span></p>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>