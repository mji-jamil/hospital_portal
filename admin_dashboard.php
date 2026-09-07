<?php
session_start();

require 'config.php';

if (!isset($_SESSION['admin'])) {
	header("Location: admin_login.php");
	exit;
}


$stmt = $pdo->query("SELECT * FROM doctors");
$doctors = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html>

<head>
	<title>Admin Login</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
	<div class="container py-5">
		<h2>Admin Login</h2>
		<?php if (isset($error)): ?>
			<div class="alert alert-danger"><?php echo $error; ?></div>
		<?php endif; ?>

		<h2>Welcome, Mr Admin</h2>
		<p>Here you can manage doctors and patients.</p>

		<h4 class="mt-5">👨‍⚕️ All Doctors</h4>
		<a href="add_doctor.php" class="btn btn-success btn-sm mb-2">+ Add Doctor</a>

		<div class="table-responsive">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Name</th>
						<th>USer Name</th>
						<th>Specialization</th>
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($doctors as $doc): ?>
						<tr>
							<td><?= htmlspecialchars($doc['name']) ?></td>
							<td><?= htmlspecialchars($doc['username']) ?></td>
							<td><?= htmlspecialchars($doc['specialization']) ?></td>
							<td>
								<a href="reset_doctor_password.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-secondary">Reset Password</a>
								<a href="edit_doctor.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
								<a href="delete_doctor.php?id=<?= $doc['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this doctor?')">Delete</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<a href="logout.php" class="btn btn-danger float-end">
			<i class="bi bi-box-arrow-right"></i> Logout
		</a>
		<br>
		<h2>Welcome, Admin 👋</h2>
		<a href="admin_billing.php" class="btn btn-outline-primary mb-3">
			💰 Manage Billing
		</a>

	</div>


</body>

</html>