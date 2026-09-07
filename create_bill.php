<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$appointment_id = $_POST['appointment_id'];
	$patient_id = $_POST['patient_id'];
	$amount = $_POST['amount'];
	$notes = $_POST['notes'] ?? '';

	// Insert the bill
	$stmt = $pdo->prepare("INSERT INTO bills (appointment_id, patient_id, amount, notes) VALUES (?, ?, ?, ?)");
	$stmt->execute([$appointment_id, $patient_id, $amount, $notes]);

	// Fetch patient name for display
	$patientStmt = $pdo->prepare("SELECT name FROM patients WHERE id = ?");
	$patientStmt->execute([$patient_id]);
	$patient = $patientStmt->fetch();
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Bill Created</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
	<div class="container py-5">
		<div class="alert alert-success">
			✅ Bill successfully created for <strong><?= htmlspecialchars($patient['name']) ?></strong>
		</div>

		<h4>Bill Details</h4>
		<ul class="list-group mb-4">
			<li class="list-group-item"><strong>Amount:</strong> ৳<?= number_format($amount, 2) ?></li>
			<li class="list-group-item"><strong>Status:</strong> Unpaid</li>
			<li class="list-group-item"><strong>Notes:</strong> <?= htmlspecialchars($notes) ?: '—' ?></li>
		</ul>

		<a href="admin_billing.php" class="btn btn-primary">← Back to Billing Page</a>
	</div>
</body>

</html>