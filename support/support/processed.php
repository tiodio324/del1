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

// SQL query to retrieve resolved or rejected tickets assigned to the current user
$sql = "SELECT id, title, status FROM tickets WHERE assigned_to = ? AND status IN ('resolved', 'rejected')";
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
                    <li><a href="support.php"><i class="fa fa-home"></i> Главная</a></li>
                    <li><a href="work.php"><i class="fa fa-inbox"></i> Входящие обращения</a></li>
                    <li><a href="processed.php"><i class="fa fa-check-square"></i> Обработанные обращения</a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out"></i> Выйти</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Обработанные обращения</h1>
            </header>

            <section class="content">
                <h2>Список обработанных обращений</h2>
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
                        echo "<p>Нет обработанных обращений</p>";
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>