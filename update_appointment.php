<?php
session_start();
require 'config.php';

if (!isset($_SESSION['patient_id']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$appointment_id = $_POST['appointment_id'];
$new_date = $_POST['appointment_date'];
$new_time = $_POST['appointment_time'];

// Update only if the appointment belongs to the patient
$stmt = $pdo->prepare("UPDATE appointments SET appointment_date = ?, appointment_time = ? WHERE id = ? AND patient_id = ?");
$stmt->execute([$new_date, $new_time, $appointment_id, $_SESSION['patient_id']]);

header("Location: patient_dashboard.php?success=Appointment+rescheduled");
exit;