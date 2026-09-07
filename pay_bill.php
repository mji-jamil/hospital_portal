<?php
require 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bill_id'])) {
	$bill_id = $_POST['bill_id'];

	// Optional: verify the bill belongs to the logged-in patient
	$stmt = $pdo->prepare("SELECT * FROM bills WHERE id = ? AND patient_id = ?");
	$stmt->execute([$bill_id, $_SESSION['patient_id']]);
	$bill = $stmt->fetch();

	if ($bill) {
		$update = $pdo->prepare("UPDATE bills SET status = 'Paid' WHERE id = ?");
		$update->execute([$bill_id]);

		header("Location: patient_dashboard.php?success=Bill+paid+successfully");
		exit;
	} else {
		header("Location: patient_dashboard.php?error=Unauthorized+bill+access");
		exit;
	}
} else {
	header("Location: patient_dashboard.php?error=Invalid+request");
	exit;
}
