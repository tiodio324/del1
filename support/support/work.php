<?php
session_start(); // Start the session

// Check if the user is logged in and has the role of support
if ($_SESSION['role'] !== 'support' && $_SESSION['role'] !== 'manager') {
    exit("Доступ запрещён");
}


$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Include the database connection file
include '../htdocs/db_connect.php';

// Function to sanitize user input
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

// Process the form submission for updating ticket status and adding comments
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ticket_id']) && isset($_POST['new_status']) && isset($_POST['comment'])) {
    $ticket_id = sanitize($_POST['ticket_id']);
    $new_status = sanitize($_POST['new_status']);
    $comment = sanitize($_POST['comment']);

    // Validate the data
    if (empty($ticket_id) || empty($new_status)) {
        echo json_encode(['status' => 'error', 'message' => 'Необходимо указать ID обращения и новый статус.']);
        exit;
    }

    // Prepare and execute the SQL query to update the ticket status
    $sql_update_ticket = "UPDATE tickets SET status = ? WHERE id = ? AND assigned_to = ?";
    $stmt_update_ticket = $conn->prepare($sql_update_ticket);
    $stmt_update_ticket->bind_param("sii", $new_status, $ticket_id, $user_id);

    if ($stmt_update_ticket->execute()) {
        // Prepare and execute the SQL query to insert the comment
        $sql_insert_comment = "INSERT INTO comments (ticket_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())";
        $stmt_insert_comment = $conn->prepare($sql_insert_comment);
        $stmt_insert_comment->bind_param("iis", $ticket_id, $user_id, $comment);

        if ($stmt_insert_comment->execute()) {
            // Return a success message
            echo json_encode(['status' => 'success', 'message' => 'Статус обращения успешно обновлен, и комментарий добавлен.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Ошибка при добавлении комментария: ' . $stmt_insert_comment->error]);
        }
        $stmt_insert_comment->close();
    } else {
        // Return an error message
        echo json_encode(['status' => 'error', 'message' => 'Ошибка при обновлении статуса обращения: ' . $stmt_update_ticket->error]);
    }

    // Close the statement
    $stmt_update_ticket->close();

    // Close the database connection
    $conn->close();
    exit;
}

// SQL query to retrieve new tickets assigned to the current user
$sql = "SELECT id, title, status FROM tickets WHERE assigned_to = ? AND status = 'new'";
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
    <title>Входящие обращения</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Styles for the modal */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto; /* 15% from the top and centered */
            padding: 20px;
            border: 1px solid #888;
            width: 80%; /* Could be more or less, depending on screen size */
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
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
                    <li><a href="support.php"><i class="fa fa-home"></i> Главная</a></li>
                    <li><a href="work.php"><i class="fa fa-inbox"></i> Входящие обращения</a></li>
                    <li><a href="processed.php"><i class="fa fa-check-square"></i> Обработанные обращения</a></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out"></i> Выйти</a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="main-header">
                <h1>Входящие обращения</h1>
            </header>

            <section class="content">
                <h2>Список входящих обращений</h2>
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
                            echo '<button class="button edit-ticket-button" data-ticket-id="' . $ticket_id . '">Изменить статус</button>';
                            echo '</div>';
                        }
                    } else {
                        echo "<p>Нет новых обращений</p>";
                    }
                    ?>
                </div>
            </section>
        </main>
    </div>

    <!-- The Modal -->
    <div id="ticketModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Изменить статус обращения</h2>
            <form id="updateTicketForm">
                <input type="hidden" id="modalTicketId" name="ticket_id">
                <div class="form-group">
                    <label for="newStatus">Новый статус:</label>
                    <select id="newStatus" name="new_status">
                        <option value="in_progress">В обработке</option>
                        <option value="resolved">Решено</option>
                        <option value="rejected">Отклонено</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">Комментарий:</label>
                    <textarea id="comment" name="comment" rows="4"></textarea>
                </div>
                <button type="submit" class="button">Сохранить изменения</button>
            </form>
            <div id="updateMessage"></div>
        </div>
    </div>

    <script>
        // Get the modal
        var modal = document.getElementById("ticketModal");

        // Get the button that opens the modal
        var buttons = document.querySelectorAll(".edit-ticket-button");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // Get the form and message elements
        var form = document.getElementById("updateTicketForm");
        var messageDiv = document.getElementById("updateMessage");
        var modalTicketId = document.getElementById("modalTicketId");

        // When the user clicks on a button, open the modal
        buttons.forEach(function(button) {
            button.onclick = function() {
                modal.style.display = "block";
                modalTicketId.value = this.dataset.ticketId; // Set the ticket ID in the hidden input
            }
        });

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Handle form submission
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Prevent the default form submission

            // Get the form data
            var formData = new FormData(form);

            // Send the data to the server using AJAX
            fetch("work.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Display the message from the server
                messageDiv.textContent = data.message;

                // If the update was successful, you might want to close the modal and refresh the ticket list
                if (data.status === "success") {
                    setTimeout(function() {
                        modal.style.display = "none";
                        window.location.reload(); // Refresh the page
                    }, 1500); // Close after 1.5 seconds
                }
            })
            .catch(error => {
                console.error("Error:", error);
                messageDiv.textContent = "Произошла ошибка при отправке запроса.";
            });
        });
    </script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>