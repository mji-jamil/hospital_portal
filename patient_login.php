<?php
require 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	$stmt = $pdo->prepare("SELECT * FROM patients WHERE username = ?");
	$stmt->execute([$username]);
	$patient = $stmt->fetch();

	if ($patient && password_verify($password, $patient['password'])) {
		$_SESSION['patient_id'] = $patient['id'];
		$_SESSION['patient_name'] = $patient['name'];
		header("Location: patient_dashboard.php");
		exit;
	} else {
		header("Location: index.php?error=Invalid+patient+credentials");
		exit;
	}
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Patient Login</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
	<div class="container py-5">
		<h2>Patient Login</h2>
		<?php if (isset($error)): ?>
			<div class="alert alert-danger"><?php echo $error; ?></div>
		<?php endif; ?>
		<form method="POST">
			<div class="mb-3">
				<label>User Name:</label>
				<input type="text" name="username" class="form-control" required>
			</div>
			<div class="mb-3">
				<label>Password:</label>
				<input type="password" name="password" class="form-control" required>
			</div>
			<button type="submit" class="btn btn-primary">Login</button>
		</form>
	</div>
	<div class="container py-5">
		<p class="text-center mt-3">
			<a href="register_patient_form.php">New patient? Register here</a>
		</p>
	</div>
</body>

</html>