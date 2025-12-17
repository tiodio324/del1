<?php
require_once "../inc/db_connect.php";

$user_id = $_SESSION['user_id'];
$tickets = $conn->query("
    SELECT * FROM tickets WHERE user_id = $user_id
");
