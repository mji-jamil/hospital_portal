<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username = $_POST['username'];
	$password = $_POST['password'];

	// Hardcoded admin credentials (or use a DB table if you prefer)
	if ($username === 'admin' && $password === 'admin123') {
		$_SESSION['admin'] = true;
		header("Location: admin_dashboard.php");
		exit;
	} else {
		$error = "Invalid credentials.";
	}
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Admin Login</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
	<div class="container py-5">
		<h2>Admin Login</h2>
		<?php if (isset($error)): ?>
			<div class="alert alert-danger"><?php echo $error; ?></div>
		<?php endif; ?>
		<form method="POST">
			<div class="mb-3">
				<label>Username:</label>
				<input type="text" name="username" class="form-control" required>
			</div>
			<div class="mb-3">
				<label>Password:</label>
				<input type="password" name="password" class="form-control" required>
			</div>
			<button type="submit" class="btn btn-dark">Login</button>
		</form>
	</div>
</body>

</html>