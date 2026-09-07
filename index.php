<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Hospital Portal Login</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="style.css" rel="stylesheet">
</head>

<body>
	<div class="container d-flex justify-content-center align-items-center min-vh-100">
		<div class="card shadow p-4" style="width: 100%; max-width: 400px;">
			<div class="text-center mb-4">
				<img
					src="assets/images/hospital_plus.png"
					alt="Hospital Icon"
					width="60">
				<h3 class="mt-2">Hospital Portal</h3>
				<p class="text-muted">Login to your account</p>
			</div>



			<?php if (isset($_GET['error'])): ?>
				<div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
			<?php endif; ?>

			<?php if (isset($_GET['error']) || isset($_GET['success'])): ?>
				<script>
					if (window.history.replaceState) {
						window.history.replaceState(null, null, window.location.pathname);
					}
				</script>
			<?php endif; ?>

			<?php if (isset($_GET['success'])): ?>
				<div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
			<?php endif; ?>

			<ul class="nav nav-tabs mb-3" id="loginTabs" role="tablist">
				<li class="nav-item" role="presentation">
					<button class="nav-link active" id="doctor-tab" data-bs-toggle="tab" data-bs-target="#doctor"
						type="button" role="tab">Doctor</button>
				</li>
				<li class="nav-item" role="presentation">
					<button class="nav-link" id="patient-tab" data-bs-toggle="tab" data-bs-target="#patient"
						type="button" role="tab">Patient</button>
				</li>
			</ul>

			<div class="tab-content" id="loginTabsContent">
				<!-- Doctor Login -->
				<div class="tab-pane fade show active" id="doctor" role="tabpanel">
					<form action="doctor_login.php" method="POST">
						<div class="mb-3">
							<input type="text" name="username" class="form-control" placeholder="Doctor ID" required>
						</div>
						<div class="mb-3">
							<input type="password" name="password" class="form-control" placeholder="Password" required>
						</div>
						<button type="submit" class="btn btn-primary w-100">Login as Doctor</button>
					</form>
				</div>

				<!-- Patient Login -->
				<div class="tab-pane fade" id="patient" role="tabpanel">
					<form action="patient_login.php" method="POST">
						<div class="mb-3">
							<input type="text" name="username" class="form-control" placeholder="Patient ID or Email"
								required>
						</div>
						<div class="mb-3">
							<input type="password" name="password" class="form-control" placeholder="Password" required>
						</div>
						<button type="submit" class="btn btn-success w-100">Login as Patient</button>
					</form>
					<!-- New Patient Registration -->
					<p class="text-center mt-3">
						<a href="register_patient_form.php">New patient? Register here</a>
					</p>
				</div>
			</div>
		</div>
	</div>

	<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>
</body>

</html>