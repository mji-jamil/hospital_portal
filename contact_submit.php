<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $log = "Name: $name\nEmail: $email\nMessage: $message\n---\n";
    file_put_contents('contacts.txt', $log, FILE_APPEND);

    // ✅ Set a session flag
    $_SESSION['contact_success'] = true;

    // ✅ Redirect to home without query string
    header("Location: home.php");
    exit;
} else {
    header("Location: home.php");
    exit;
}