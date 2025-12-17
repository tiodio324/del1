<?php
session_start();
session_destroy();
header("Location: ../htdocs/index.php"); // Или на другую страницу авторизации/регистрации
exit();
?>