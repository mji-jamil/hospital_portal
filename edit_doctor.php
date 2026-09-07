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

// Fetch doctor info
$stmt = $pdo->prepare("SELECT * FROM doctors WHERE id = ?");
$stmt->execute([$id]);
$doctor = $stmt->fetch();

if (!$doctor) {
	die("Doctor not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = $_POST['username'];
	$email = $_POST['email'];
	$specialization = $_POST['specialization'];

	$stmt = $pdo->prepare("UPDATE doctors SET name = ?, email = ?, specialization = ? WHERE id = ?");
	$stmt->execute([$name, $email, $specialization, $id]);

	header("Location: admin_dashboard.php");
	exit;
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Edit Doctor</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
	<div class="container py-5">
		<h2>Edit Doctor</h2>
		<form method="POST">
			<div class="mb-3">
				<label>Username:</label>
				<input type="text" name="username" class="form-control" value="<?= htmlspecialchars($doctor['name']) ?>" required>
			</div>
			<div class="mb-3">
				<label>Email:</label>
				<input type="email" name="email" class="form-control" value="<?= htmlspecialchars($doctor['email']) ?>">
			</div>
			<div class="mb-3">
				<label>Specialization:</label>
				<input type="text" name="specialization" class="form-control" value="<?= htmlspecialchars($doctor['specialization']) ?>" required>
			</div>
			<button type="submit" class="btn btn-warning">Update Doctor</button>
		</form>
	</div>
</body>

</html>