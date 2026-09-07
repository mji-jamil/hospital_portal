<?php
require 'config.php';

// Fetch completed appointments without bills
$stmt = $pdo->query("
  SELECT a.id AS appointment_id, a.patient_id, p.name AS patient_name, d.name AS doctor_name, a.appointment_date
  FROM appointments a
  JOIN patients p ON a.patient_id = p.id
  JOIN doctors d ON a.doctor_id = d.id
  LEFT JOIN bills b ON a.id = b.appointment_id
  WHERE a.status = 'Completed' AND b.id IS NULL
");
$appointments = $stmt->fetchAll();

// Fetch all existing bills
$billStmt = $pdo->query("
  SELECT b.*, p.name AS patient_name, a.appointment_date
  FROM bills b
  JOIN patients p ON b.patient_id = p.id
  JOIN appointments a ON b.appointment_id = a.id
  ORDER BY b.created_at DESC
");
$bills = $billStmt->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
	<title>Admin Billing Panel</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
	<div class="container py-5">
		<h2 class="mb-4">🧾 Admin Billing Panel</h2>

		<?php if (isset($_GET['success'])): ?>
			<div class="alert alert-success"><?= htmlspecialchars($_GET['success']) ?></div>
		<?php endif; ?>

		<!-- SECTION 1: Generate New Bills -->
		<h4>Generate Bills for Completed Appointments</h4>
		<?php if ($appointments): ?>
			<table class="table table-bordered table-hover bg-white shadow-sm">
				<thead class="table-light">
					<tr>
						<th>Patient</th>
						<th>Doctor</th>
						<th>Date</th>
						<th>Amount</th>
						<th>Notes</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($appointments as $appt): ?>
						<tr>
							<td><?= htmlspecialchars($appt['patient_name']) ?></td>
							<td>Dr. <?= htmlspecialchars($appt['doctor_name']) ?></td>
							<td><?= $appt['appointment_date'] ?></td>
							<form method="POST" action="create_bill.php" class="d-flex flex-wrap gap-2 align-items-center">
								<input type="hidden" name="appointment_id" value="<?= $appt['appointment_id'] ?>">
								<input type="hidden" name="patient_id" value="<?= $appt['patient_id'] ?>">
								<td><input type="number" name="amount" class="form-control form-control-sm" placeholder="৳" required></td>
								<td><input type="text" name="notes" class="form-control form-control-sm" placeholder="Optional notes"></td>
								<td><button type="submit" class="btn btn-success btn-sm">Create Bill</button></td>
							</form>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else: ?>
			<div class="alert alert-info">✅ All completed appointments have been billed.</div>
		<?php endif; ?>

		<hr>

		<hr>
		<h4>Billing History</h4>

		<table class="table table-bordered table-hover bg-white shadow-sm">
			<thead class="table-light">
				<tr>
					<th>Bill ID</th>
					<th>Patient</th>
					<th>Appointment Date</th>
					<th>Amount</th>
					<th>Status</th>
					<th>Notes</th>
					<th>Issued</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($bills): ?>
					<?php foreach ($bills as $bill): ?>
						<tr>
							<td>#<?= $bill['id'] ?></td>
							<td><?= htmlspecialchars($bill['patient_name']) ?></td>
							<td><?= $bill['appointment_date'] ?></td>
							<td>৳<?= number_format($bill['amount'], 2) ?></td>
							<td>
								<?php if ($bill['status'] === 'Paid'): ?>
									<span class="badge bg-success">Paid</span>
								<?php else: ?>
									<span class="badge bg-warning text-dark">Unpaid</span>
								<?php endif; ?>
							</td>
							<td><?= htmlspecialchars($bill['notes']) ?></td>
							<td><?= $bill['created_at'] ?></td>
							<td>
								<!-- <a href="paynow.php?bill_id=<?= $bill['id'] ?>" class="btn btn-sm btn-outline-primary">
                View
                </a> -->
								<?php if ($bill['status'] === 'Paid'): ?>
									<?php $receiptPath = "receipts/receipt_bill_" . $bill['id'] . ".pdf"; ?>
									<?php if (file_exists($receiptPath)): ?>
										<a href="<?= $receiptPath ?>" class="btn btn-sm btn-outline-success" download>
											Download Receipt
										</a>
									<?php endif; ?>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="8" class="text-center text-muted">No bills found.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>


		<!-- SECTION 2: View and Manage Existing Bills -->
		<h4>All Bills</h4>
		<?php if ($bills): ?>
			<table class="table table-bordered table-striped bg-white shadow-sm">
				<thead class="table-light">
					<tr>
						<th>Patient</th>
						<th>Appointment Date</th>
						<th>Amount</th>
						<th>Status</th>
						<th>Notes</th>
						<th>Created</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($bills as $bill): ?>
						<tr>
							<td><?= htmlspecialchars($bill['patient_name']) ?></td>
							<td><?= $bill['appointment_date'] ?></td>
							<td>৳<?= number_format($bill['amount'], 2) ?></td>
							<td>
								<?php if ($bill['status'] === 'Paid'): ?>
									<span class="badge bg-success">Paid</span>
								<?php else: ?>
									<span class="badge bg-warning text-dark">Unpaid</span>
								<?php endif; ?>
							</td>
							<td><?= htmlspecialchars($bill['notes']) ?></td>
							<td><?= $bill['created_at'] ?></td>
							<td>
								<?php if ($bill['status'] === 'Unpaid'): ?>
									<form method="POST" action="mark_bill_paid.php" style="display:inline;">
										<input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">
										<button type="submit" class="btn btn-sm btn-primary">Mark as Paid</button>
									</form>
								<?php else: ?>
									<span class="text-muted">—</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php else: ?>
			<div class="alert alert-secondary">No bills found.</div>
		<?php endif; ?>

		<a href="admin_dashboard.php" class="btn btn-secondary mt-4">← Back to Admin Dashboard</a>
	</div>
</body>

</html>