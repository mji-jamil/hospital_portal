<?php
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $name = trim($_POST['name']);

    if (empty($username) || empty($email) || empty($password) || empty($name)) {
        header("Location: index.php?error=All+fields+are+required");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: index.php?error=Invalid+email+address");
        exit;
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO patients (username, password, name, email) VALUES (?, ?, ?, ?)");

    try {
        $stmt->execute([$username, $hashedPassword, $name, $email]);
        header("Location: index.php?success=Patient+registered+successfully");
        exit;
    } catch (PDOException $e) {
        header("Location: index.php?error=Username+or+email+already+exists");
    }
}
?>