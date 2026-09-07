<?php
require 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	$stmt = $pdo->prepare("SELECT * FROM doctors WHERE username = ?");
	$stmt->execute([$username]);
	$doctor = $stmt->fetch();

	if ($doctor && password_verify($password, $doctor['password'])) {
		$_SESSION['doctor_id'] = $doctor['id'];
		$_SESSION['doctor_name'] = $doctor['name'];
		header("Location: doctor_dashboard.php");
		exit;
	} else {
		header("Location: index.php?error=Invalid+doctor+credentials");
		exit;
	}
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Doctor Login</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
	<div class="container py-5">
		<h2>Doctor Login</h2>
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
			<button type="submit" class="btn btn-success">Login</button>
		</form>
	</div>
</body>

</html>