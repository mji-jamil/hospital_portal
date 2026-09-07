<?php
session_start();
require 'config.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $doctor_id = $_POST['doctor_id'];
    $date = $_POST['appointment_date'];
    $time = $_POST['appointment_time'];
    $patient_id = $_SESSION['patient_id'];

    // Optional: Validate date/time here

    $stmt = $pdo->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time) VALUES (?, ?, ?, ?)");
    $stmt->execute([$patient_id, $doctor_id, $date, $time]);

    header("Location: patient_dashboard.php?success=Appointment+booked+successfully");
    exit;
}
?>