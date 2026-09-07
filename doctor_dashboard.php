<?php
date_default_timezone_set('Asia/Dhaka');
session_start();
require 'config.php';

if (!isset($_SESSION['doctor_id'])) {
	header("Location: index.php");
	exit;
}

// Fetch appointments with filters
$conditions = ["a.doctor_id = ?"];
$params = [$_SESSION['doctor_id']];

if (!empty($_GET['patient_name'])) {
	$conditions[] = "p.name LIKE ?";
	$params[] = '%' . $_GET['patient_name'] . '%';
}

if (!empty($_GET['appointment_date'])) {
	$conditions[] = "a.appointment_date = ?";
	$params[] = $_GET['appointment_date'];
}

if (!empty($_GET['status']) && $_GET['status'] !== 'Expired') {
	$conditions[] = "a.status = ?";
	$params[] = $_GET['status'];
}

$whereClause = implode(" AND ", $conditions);

$stmt = $pdo->prepare("
  SELECT a.*, p.name AS patient_name, p.email
  FROM appointments a
  JOIN patients p ON a.patient_id = p.id
  WHERE $whereClause
  ORDER BY a.appointment_date ASC, a.appointment_time ASC
");
$stmt->execute($params);
$appointments = $stmt->fetchAll();

// Fetch prescribed appointment IDs
$prescribedStmt = $pdo->prepare("SELECT appointment_id FROM prescriptions WHERE doctor_id = ?");
$prescribedStmt->execute([$_SESSION['doctor_id']]);
$prescribedAppointments = array_column($prescribedStmt->fetchAll(), 'appointment_id');
?>

<!DOCTYPE html>
<html>

<head>
	<title>Doctor Dashboard</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
	<div class="container py-5">
		<h2>Welcome, Dr. <?= htmlspecialchars($_SESSION['doctor_name']); ?> 👨‍⚕️</h2>
		<p>Here are your upcoming appointments:</p>

		<form method="GET" class="row g-3 mb-4">
			<div class="col-md-4">
				<input type="text" name="patient_name" class="form-control" placeholder="Search by patient name"
					value="<?= isset($_GET['patient_name']) ? htmlspecialchars($_GET['patient_name']) : ''; ?>">
			</div>
			<div class="col-md-3">
				<input type="date" name="appointment_date" class="form-control"
					value="<?= isset($_GET['appointment_date']) ? $_GET['appointment_date'] : ''; ?>">
			</div>
			<div class="col-md-3">
				<select name="status" class="form-select">
					<option value="">All Statuses</option>
					<option value="Scheduled" <?= (isset($_GET['status']) && $_GET['status'] === 'Scheduled') ? 'selected' : ''; ?>>Scheduled</option>
					<option value="Completed" <?= (isset($_GET['status']) && $_GET['status'] === 'Completed') ? 'selected' : ''; ?>>Completed</option>
					<option value="Expired" <?= (isset($_GET['status']) && $_GET['status'] === 'Expired') ? 'selected' : ''; ?>>Expired</option>
				</select>
			</div>
			<div class="col-md-2">
				<button type="submit" class="btn btn-primary w-100">Filter</button>
			</div>
		</form>

		<table class="table table-bordered table-hover bg-white shadow-sm">
			<thead class="table-light">
				<tr>
					<th>Patient</th>
					<th>Email</th>
					<th>Date</th>
					<th>Time</th>
					<th>Status</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($appointments):
					foreach ($appointments as $appt): ?>
						<?php
						$now = new DateTime();
						$apptDateTime = new DateTime($appt['appointment_date'] . ' ' . $appt['appointment_time']);
						$status = $appt['status'];

						if ($status === 'Scheduled' && $apptDateTime < $now) {
							$status = 'Expired';
						}
						?>
						<tr>
							<td><?= htmlspecialchars($appt['patient_name']); ?></td>
							<td><?= htmlspecialchars($appt['email']); ?></td>
							<td><?= $appt['appointment_date']; ?></td>
							<td><?= $appt['appointment_time']; ?></td>
							<td>
								<?php if ($status === 'Scheduled'): ?>
									<span class="badge bg-primary">Scheduled</span>
								<?php elseif ($status === 'Completed'): ?>
									<span class="badge bg-success">Completed</span>
								<?php elseif ($status === 'Expired'): ?>
									<span class="badge bg-danger">Expired</span>
								<?php else: ?>
									<span class="badge bg-secondary"><?= htmlspecialchars($status); ?></span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ($status === 'Scheduled'): ?>
									<form action="doctor_cancel_appointment.php" method="POST" style="display:inline;">
										<input type="hidden" name="appointment_id" value="<?= $appt['id']; ?>">
										<button type="submit" class="btn btn-sm btn-danger"
											onclick="return confirm('Cancel this appointment?')">Cancel</button>
									</form>

									<form action="mark_completed.php" method="POST" style="display:inline;">
										<input type="hidden" name="appointment_id" value="<?= $appt['id']; ?>">
										<button type="submit" class="btn btn-sm btn-success">Done</button>
									</form>
								<?php endif; ?>

								<?php if ($status === 'Completed'): ?>
									<?php if (!in_array($appt['id'], $prescribedAppointments)): ?>
										<a href="write_prescription.php?appointment_id=<?= $appt['id']; ?>"
											class="btn btn-sm btn-primary mt-1">
											Write Prescription
										</a>
									<?php else: ?>
										<a href="view_prescription.php?appointment_id=<?= $appt['id']; ?>"
											class="btn btn-sm btn-info mt-1">
											View Prescription
										</a>
										<a href="prescriptions/prescription_<?= $appt['id']; ?>.pdf" download
											class="btn btn-sm btn-outline-success mt-1">
											Download PDF
										</a>
									<?php endif; ?>
								<?php endif; ?>

								<a href="patient_profile.php?id=<?= $appt['patient_id']; ?>" class="btn btn-sm btn-info mt-1">
									View Profile
								</a>
							</td>
						</tr>
					<?php endforeach;
				else: ?>
					<tr>
						<td colspan="6" class="text-center text-muted">No appointments found.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>

		<a href="logout.php" class="btn btn-danger">Logout</a>
	</div>
</body>

</html>