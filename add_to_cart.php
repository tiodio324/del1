<?php
session_start();
require_once 'inc/db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$product_id = (int)$_GET['id'];

// если корзины нет — создаём
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// если товара нет — добавляем 1шт, если есть — увеличиваем
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]++;
} else {
    $_SESSION['cart'][$product_id] = 1;
}

// назад на страницу, откуда пришёл пользователь
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
