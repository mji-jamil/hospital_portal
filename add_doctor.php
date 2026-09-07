<?php
session_start();
require 'config.php';
if (!isset($_SESSION['admin'])) {
	header("Location: admin_login.php");
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = $_POST['name'];
	$username = $_POST['username'];
	$specialization = $_POST['specialization'];
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

	$stmt = $pdo->prepare("INSERT INTO doctors (name, username, specialization, password) VALUES (?, ?, ?, ?)");
	$stmt->execute([$name, $username, $specialization, $password]);

	header("Location: admin_dashboard.php");
	exit;
}
?>

<!DOCTYPE html>
<html>

<head>
	<title>Add Doctor</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
	<div class="container py-5">
		<h2>Add New Doctor</h2>
		<form method="POST">
			<div class="mb-3"><label>Name:</label><input type="text" name="name" class="form-control" required></div>
			<div class="mb-3"><label>User Name:</label><input type="text" name="username" class="form-control" required></div>
			<div class="mb-3"><label>Specialization:</label><input type="text" name="specialization" class="form-control" required></div>
			<div class="mb-3"><label>Password:</label><input type="password" name="password" class="form-control" required></div>
			<button type="submit" class="btn btn-success">Add Doctor</button>
		</form>
	</div>
</body>

</html>