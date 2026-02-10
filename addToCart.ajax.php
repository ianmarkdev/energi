<?php

session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['cart'][] = [
        'type' => 'main',
        'name' => $_POST['name'],
        'quantity' => (int) $_POST['quantity'],
        'lieferstellen' => (int) $_POST['lieferstellen'],
        'price' => (float) $_POST['price']
    ];
}
