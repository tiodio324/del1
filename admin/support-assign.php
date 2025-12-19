<?php
require_once __DIR__ . '/inc/admin-check.php';
require_once __DIR__ . '/../inc/db_connect.php';

$stmt = $conn->prepare("
    UPDATE support_tickets
    SET support_id = ?
    WHERE id = ?
");
$stmt->bind_param("ii", $_POST['support_id'], $_POST['ticket_id']);
$stmt->execute();

header("Location: support.php");
