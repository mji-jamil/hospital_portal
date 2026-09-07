<?php
session_start();
require 'config.php';

if (!isset($_SESSION['doctor_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
	header("Location: index.php");
	exit;
}

$appointment_id = $_POST['appointment_id'];

// Update status to Completed if the appointment belongs to the doctor
$stmt = $pdo->prepare("UPDATE appointments SET status = 'Completed' WHERE id = ? AND doctor_id = ?");
$stmt->execute([$appointment_id, $_SESSION['doctor_id']]);

header("Location: doctor_dashboard.php?success=Appointment+marked+as+completed");
exit;
