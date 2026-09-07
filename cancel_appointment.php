<?php
session_start();
require 'config.php';

if (!isset($_SESSION['patient_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$appointment_id = $_POST['appointment_id'];

// Make sure the appointment belongs to the logged-in patient
$stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ? AND patient_id = ?");
$stmt->execute([$appointment_id, $_SESSION['patient_id']]);

header("Location: patient_dashboard.php?success=Appointment+cancelled");
exit;