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

// Fetch doctor info (optional, for display)
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->execute([$id]);
$doctor = $stmt->fetch();

if (!$doctor) {
	die("Doctor not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$newPassword = $_POST['new_password'];
	$hashed = password_hash($newPassword, PASSWORD_DEFAULT);

	$stmt = $pdo->prepare("UPDATE doctors SET password = ? WHERE id = ?");
	$stmt->execute([$hashed, $id]);

	header("Location: admin_dashboard.php?success=Password+reset+successfully");
	exit;
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Reset Doctor Password</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
	<div class="container py-5">
		<h2>Reset Password for <?= htmlspecialchars($doctor['name']) ?></h2>
		<form method="POST">
			<div class="mb-3">
				<label>New Password:</label>
				<input type="password" name="new_password" class="form-control" required>
			</div>
			<button type="submit" class="btn btn-primary">Reset Password</button>
			<a href="admin_dashboard.php" class="btn btn-secondary">Cancel</a>
		</form>
	</div>
</body>

</html>