<!DOCTYPE html>
<html>

<head>
	<title>Patient Registration</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
	<div class="container py-5">
		<h2>Register as Patient</h2>
		<form action="register_patient.php" method="POST">
			<div class="mb-3">
				<input name="name" class="form-control" placeholder="Full Name" required>
			</div>
			<div class="mb-3">
				<input name="username" class="form-control" placeholder="Username" required>
			</div>
			<div class="mb-3">
				<input name="email" type="email" class="form-control" placeholder="Email" required>
			</div>
			<div class="mb-3">
				<input name="password" type="password" class="form-control" placeholder="Password" required>
			</div>
			<button type="submit" class="btn btn-success">Register</button>
		</form>
		<p class="mt-3"><a href="index.php">Back to login</a></p>
	</div>
</body>

</html>