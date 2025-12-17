<?php
require_once "../inc/db_connect.php";

if ($_POST) {
    $subject = $_POST['subject'];
    $user_id = $_SESSION['user_id'];

    $conn->query("
        INSERT INTO tickets (user_id, subject, status)
        VALUES ($user_id, '$subject', 'open')
    ");

    header("Location: my_tickets.php");
}
