<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin'])) {
	header("Location: admin_login.php");
	exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
	die("Invalid doctor ID.");
}

// Delete doctor
$stmt = $pdo->prepare("DELETE FROM doctors WHERE id = ?");
$stmt->execute([$id]);

header("Location: admin_dashboard.php");
exit;
