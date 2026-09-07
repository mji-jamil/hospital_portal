<?php
session_start();
require 'config.php';

if (!isset($_SESSION['patient_id']) || !isset($_GET['appointment_id'])) {
	header("Location: index.php");
	exit;
}

$appointment_id = $_GET['appointment_id'];

// Fetch the appointment to verify ownership
$stmt = $pdo->prepare("SELECT * FROM appointments WHERE id = ? AND patient_id = ?");
$stmt->execute([$appointment_id, $_SESSION['patient_id']]);
$appt = $stmt->fetch();

if (!$appt) {
	header("Location: patient_dashboard.php?error=Appointment+not+found");
	exit;
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Reschedule Appointment</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
	<div class="container py-5">
		<h3>Reschedule Appointment</h3>
		<form action="update_appointment.php" method="POST">
			<input type="hidden" name="appointment_id" value="<?php echo $appt['id']; ?>">

			<div class="mb-3">
				<label>Date:</label>
				<input type="date" name="appointment_date" class="form-control" value="<?php echo $appt['appointment_date']; ?>" required>
			</div>
			<div class="mb-3">
				<label>Time:</label>
				<input type="time" name="appointment_time" class="form-control" value="<?php echo $appt['appointment_time']; ?>" required>
			</div>
			<button type="submit" class="btn btn-primary">Update Appointment</button>
			<a href="patient_dashboard.php" class="btn btn-secondary">Cancel</a>
		</form>
	</div>
</body>

</html>