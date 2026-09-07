<?php
date_default_timezone_set('Asia/Dhaka'); // or your local timezone
session_start();
require 'config.php';

if (!isset($_SESSION['patient_id'])) {
	header("Location: index.php");
	exit;
}

// Fetch all doctors
$stmt = $pdo->query("SELECT id, name, specialization FROM doctors");
$doctors = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
	<title>Patient Dashboard</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
	<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> -->
	 <link href="assets/css/bootstrap.min.css" rel="stylesheet">
	 <link href="assets/icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">
	<div class="container py-5">
		<h2>Welcome, <?php echo htmlspecialchars($_SESSION['patient_name']); ?> 👋</h2>
		<p>Select a doctor and schedule an appointment:</p>

		<?php if (isset($_GET['success'])): ?>
			<div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
		<?php endif; ?>

		<?php
		// Check if patient profile exists
		$profileStmt = $pdo->prepare("SELECT * FROM patient_profiles WHERE patient_id = ?");
		$profileStmt->execute([$_SESSION['patient_id']]);
		$profile = $profileStmt->fetch();
		?>

		<?php if ($profile): ?>
			<hr>
			<h4>Your Medical Profile</h4>
			<ul class="list-group mb-4">
				<li class="list-group-item"><strong>Age:</strong> <?= htmlspecialchars($profile['age']) ?></li>
				<li class="list-group-item"><strong>Gender:</strong> <?= htmlspecialchars($profile['gender']) ?></li>
				<li class="list-group-item"><strong>Symptoms:</strong> <?= nl2br(htmlspecialchars($profile['symptoms'])) ?></li>
				<li class="list-group-item"><strong>Medical History:</strong> <?= nl2br(htmlspecialchars($profile['medical_history'])) ?></li>
			</ul>
		<?php endif; ?>

		<!-- <?php if (!$profile): ?>
			<div class="alert alert-info">
				<h4>Complete Your Patient Profile</h4>
				<form method="POST" action="save_profile.php">
					<div class="mb-3">
						<label>Age:</label>
						<input type="number" name="age" class="form-control" required>
					</div>
					<div class="mb-3">
						<label>Gender:</label>
						<select name="gender" class="form-select" required>
							<option value="">-- Select --</option>
							<option value="Male">Male</option>
							<option value="Female">Female</option>
							<option value="Other">Other</option>
						</select>
					</div>
					<div class="mb-3">
						<label>Symptoms:</label>
						<textarea name="symptoms" class="form-control" required></textarea>
					</div>
					<div class="mb-3">
						<label>Medical History:</label>
						<textarea name="medical_history" class="form-control"></textarea>
					</div>
					<button type="submit" class="btn btn-success">Save Profile</button>
				</form>
			</div>
		<?php endif; ?> -->

		<?php if ($profile): ?>

			<hr>
			<h4>Your Medical Profile</h4>

			<div class="card shadow-sm mb-4">
				<div class="card-body">

					<div class="row">
						<div class="col-md-6 mb-3">
							<strong>Age:</strong>
							<?= htmlspecialchars($profile['age'] ?? '') ?>
						</div>

						<div class="col-md-6 mb-3">
							<strong>Gender:</strong>
							<?= htmlspecialchars($profile['gender'] ?? '') ?>
						</div>

						<div class="col-md-6 mb-3">
							<strong>Chest Pain Type:</strong>
							<?= htmlspecialchars($profile['cp'] ?? '') ?>
						</div>

						<div class="col-md-6 mb-3">
							<strong>Resting Blood Pressure:</strong>
							<?= htmlspecialchars($profile['trestbps'] ?? '') ?>
						</div>

						<div class="col-md-6 mb-3">
							<strong>Cholesterol:</strong>
							<?= htmlspecialchars($profile['chol'] ?? '') ?>
						</div>

						<div class="col-md-6 mb-3">
							<strong>Fasting Blood Sugar:</strong>
							<?= htmlspecialchars($profile['fbs'] ?? '') ?>
						</div>

						<div class="col-md-6 mb-3">
							<strong>Resting ECG:</strong>
							<?= htmlspecialchars($profile['restecg'] ?? '') ?>
						</div>

						<div class="col-md-6 mb-3">
							<strong>Maximum Heart Rate:</strong>
							<?= htmlspecialchars($profile['thalach'] ?? '') ?>
						</div>

						<div class="col-md-6 mb-3">
							<strong>Exercise Induced Angina:</strong>
							<?= htmlspecialchars($profile['exang'] ?? '') ?>
						</div>

						<div class="col-md-6 mb-3">
							<strong>Oldpeak:</strong>
							<?= htmlspecialchars($profile['oldpeak'] ?? '') ?>
						</div>

						<div class="col-md-6 mb-3">
							<strong>Slope:</strong>
							<?= htmlspecialchars($profile['slope'] ?? '') ?>
						</div>

						<div class="col-md-6 mb-3">
							<strong>Major Vessels (CA):</strong>
							<?= htmlspecialchars($profile['ca'] ?? '') ?>
						</div>

						<div class="col-md-6 mb-3">
							<strong>Thal:</strong>
							<?= htmlspecialchars($profile['thal'] ?? '') ?>
						</div>
					</div>

					<hr>

					<strong>Symptoms:</strong>
					<p><?= nl2br(htmlspecialchars($profile['symptoms'] ?? '')) ?></p>

					<strong>Medical History:</strong>
					<p><?= nl2br(htmlspecialchars($profile['medical_history'] ?? '')) ?></p>

					<a href="#edit-profile" class="btn btn-primary">
						Edit Medical Profile
					</a>

				</div>
			</div>


		<?php endif; ?>


		<!-- Profile form -->
		<div id="edit-profile" class="alert alert-info">

			<h4>
				<?= $profile ? 'Update Your Medical Profile' : 'Complete Your Patient Profile' ?>
			</h4>

			<p class="text-muted">
				Please provide your medical information accurately.
			</p>

			<form method="POST" action="save_profile.php">

				<div class="row">

					<!-- Age -->
					<div class="col-md-6 mb-3">
						<label class="form-label">Age</label>
						<input
							type="number"
							name="age"
							class="form-control"
							min="1"
							max="120"
							value="<?= htmlspecialchars($profile['age'] ?? '') ?>"
							required>
					</div>

					<!-- Gender -->
					<div class="col-md-6 mb-3">
						<label class="form-label">Gender</label>
						<select name="gender" class="form-select" required>
							<option value="">-- Select --</option>

							<option value="Male"
								<?= (($profile['gender'] ?? '') === 'Male') ? 'selected' : '' ?>>
								Male
							</option>

							<option value="Female"
								<?= (($profile['gender'] ?? '') === 'Female') ? 'selected' : '' ?>>
								Female
							</option>

							<option value="Other"
								<?= (($profile['gender'] ?? '') === 'Other') ? 'selected' : '' ?>>
								Other
							</option>

						</select>
					</div>


					<!-- Sex -->
					<div class="col-md-6 mb-3">
						<label class="form-label">Sex (ML value)</label>

						<select name="sex" class="form-select" required>
							<option value="">-- Select --</option>

							<option value="1"
								<?= (($profile['sex'] ?? '') == 1) ? 'selected' : '' ?>>
								Male (1)
							</option>

							<option value="0"
								<?= (($profile['sex'] ?? '') == 0) ? 'selected' : '' ?>>
								Female (0)
							</option>
						</select>
					</div>


					<!-- Chest Pain -->
					<div class="col-md-6 mb-3">
						<label class="form-label">Chest Pain Type (cp)</label>

						<select name="cp" class="form-select">
							<option value="">-- Select --</option>
							<option value="0" <?= (($profile['cp'] ?? '') == 0) ? 'selected' : '' ?>>0</option>
							<option value="1" <?= (($profile['cp'] ?? '') == 1) ? 'selected' : '' ?>>1</option>
							<option value="2" <?= (($profile['cp'] ?? '') == 2) ? 'selected' : '' ?>>2</option>
							<option value="3" <?= (($profile['cp'] ?? '') == 3) ? 'selected' : '' ?>>3</option>
						</select>
					</div>


					<!-- Resting BP -->
					<div class="col-md-6 mb-3">
						<label class="form-label">Resting Blood Pressure</label>

						<input
							type="number"
							name="trestbps"
							class="form-control"
							min="0"
							max="250"
							value="<?= htmlspecialchars($profile['trestbps'] ?? '') ?>">
					</div>


					<!-- Cholesterol -->
					<div class="col-md-6 mb-3">
						<label class="form-label">Cholesterol</label>

						<input
							type="number"
							name="chol"
							class="form-control"
							min="0"
							max="600"
							value="<?= htmlspecialchars($profile['chol'] ?? '') ?>">
					</div>


					<!-- FBS -->
					<div class="col-md-6 mb-3">
						<label class="form-label">Fasting Blood Sugar &gt; 120 mg/dl</label>

						<select name="fbs" class="form-select">
							<option value="">-- Select --</option>
							<option value="0" <?= (($profile['fbs'] ?? '') == 0) ? 'selected' : '' ?>>No (0)</option>
							<option value="1" <?= (($profile['fbs'] ?? '') == 1) ? 'selected' : '' ?>>Yes (1)</option>
						</select>
					</div>


					<!-- Rest ECG -->
					<div class="col-md-6 mb-3">
						<label class="form-label">Resting ECG (restecg)</label>

						<select name="restecg" class="form-select">
							<option value="">-- Select --</option>
							<option value="0" <?= (($profile['restecg'] ?? '') == 0) ? 'selected' : '' ?>>0</option>
							<option value="1" <?= (($profile['restecg'] ?? '') == 1) ? 'selected' : '' ?>>1</option>
							<option value="2" <?= (($profile['restecg'] ?? '') == 2) ? 'selected' : '' ?>>2</option>
						</select>
					</div>


					<!-- Max Heart Rate -->
					<div class="col-md-6 mb-3">
						<label class="form-label">Maximum Heart Rate (thalach)</label>

						<input
							type="number"
							name="thalach"
							class="form-control"
							min="0"
							max="250"
							value="<?= htmlspecialchars($profile['thalach'] ?? '') ?>">
					</div>


					<!-- Exercise Angina -->
					<div class="col-md-6 mb-3">
						<label class="form-label">Exercise Induced Angina</label>

						<select name="exang" class="form-select">
							<option value="">-- Select --</option>
							<option value="0" <?= (($profile['exang'] ?? '') == 0) ? 'selected' : '' ?>>No (0)</option>
							<option value="1" <?= (($profile['exang'] ?? '') == 1) ? 'selected' : '' ?>>Yes (1)</option>
						</select>
					</div>


					<!-- Oldpeak -->
					<div class="col-md-6 mb-3">
						<label class="form-label">Oldpeak (ST depression)</label>

						<input
							type="number"
							name="oldpeak"
							class="form-control"
							min="0"
							max="10"
							step="0.01"
							value="<?= htmlspecialchars($profile['oldpeak'] ?? '') ?>">
					</div>


					<!-- Slope -->
					<div class="col-md-6 mb-3">
						<label class="form-label">Slope</label>

						<select name="slope" class="form-select">
							<option value="">-- Select --</option>
							<option value="0" <?= (($profile['slope'] ?? '') == 0) ? 'selected' : '' ?>>0</option>
							<option value="1" <?= (($profile['slope'] ?? '') == 1) ? 'selected' : '' ?>>1</option>
							<option value="2" <?= (($profile['slope'] ?? '') == 2) ? 'selected' : '' ?>>2</option>
						</select>
					</div>


					<!-- CA -->
					<div class="col-md-6 mb-3">
						<label class="form-label">Number of Major Vessels (ca)</label>

						<select name="ca" class="form-select">
							<option value="">-- Select --</option>
							<option value="0" <?= (($profile['ca'] ?? '') == 0) ? 'selected' : '' ?>>0</option>
							<option value="1" <?= (($profile['ca'] ?? '') == 1) ? 'selected' : '' ?>>1</option>
							<option value="2" <?= (($profile['ca'] ?? '') == 2) ? 'selected' : '' ?>>2</option>
							<option value="3" <?= (($profile['ca'] ?? '') == 3) ? 'selected' : '' ?>>3</option>
							<option value="4" <?= (($profile['ca'] ?? '') == 4) ? 'selected' : '' ?>>4</option>
						</select>
					</div>


					<!-- Thal -->
					<div class="col-md-6 mb-3">
						<label class="form-label">Thal</label>

						<select name="thal" class="form-select">
							<option value="">-- Select --</option>
							<option value="0" <?= (($profile['thal'] ?? '') == 0) ? 'selected' : '' ?>>0</option>
							<option value="1" <?= (($profile['thal'] ?? '') == 1) ? 'selected' : '' ?>>1</option>
							<option value="2" <?= (($profile['thal'] ?? '') == 2) ? 'selected' : '' ?>>2</option>
							<option value="3" <?= (($profile['thal'] ?? '') == 3) ? 'selected' : '' ?>>3</option>
						</select>
					</div>


					<!-- Symptoms -->
					<div class="col-12 mb-3">
						<label class="form-label">Symptoms</label>

						<textarea
							name="symptoms"
							class="form-control"
							rows="3"><?= htmlspecialchars($profile['symptoms'] ?? '') ?></textarea>
					</div>


					<!-- Medical History -->
					<div class="col-12 mb-3">
						<label class="form-label">Medical History</label>

						<textarea
							name="medical_history"
							class="form-control"
							rows="3"><?= htmlspecialchars($profile['medical_history'] ?? '') ?></textarea>
					</div>

				</div>

				<button type="submit" class="btn btn-success">
					Save Medical Profile
				</button>

			</form>

		</div>




		<hr>
		<h4>Your Appointments</h4>

		<?php
		// Fetch appointments
		$stmt = $pdo->prepare("
        SELECT a.*, d.name AS doctor_name, d.specialization
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        WHERE a.patient_id = ?
        ORDER BY a.appointment_date ASC, a.appointment_time ASC
      ");
		$stmt->execute([$_SESSION['patient_id']]);
		$appointments = $stmt->fetchAll();

		// Fetch prescriptions
		$presStmt = $pdo->prepare("
    SELECT id, appointment_id
    FROM prescriptions
    WHERE patient_id = ?
    ORDER BY id DESC
");

		$presStmt->execute([$_SESSION['patient_id']]);
		$prescriptions = $presStmt->fetchAll();

		$prescriptionByAppointment = [];

		foreach ($prescriptions as $prescription) {
			// Keep the latest prescription for each appointment
			if (!isset($prescriptionByAppointment[$prescription['appointment_id']])) {
				$prescriptionByAppointment[$prescription['appointment_id']] = $prescription['id'];
			}
		}
		?>

		<table class="table table-bordered">
			<thead>
				<tr>
					<th>Doctor</th>
					<th>Specialization</th>
					<th>Date</th>
					<th>Time</th>
					<th>Status</th>
					<th>Actions</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($appointments): ?>
					<?php foreach ($appointments as $appt): ?>
						<?php
						$now = new DateTime();
						$apptDateTime = new DateTime($appt['appointment_date'] . ' ' . $appt['appointment_time']);
						$status = $appt['status'];

						if ($status === 'Scheduled' && $apptDateTime < $now) {
							$status = 'Expired';
						}
						?>
						<tr>
							<td>Dr. <?= htmlspecialchars($appt['doctor_name']); ?></td>
							<td><?= htmlspecialchars($appt['specialization']); ?></td>
							<td><?= $appt['appointment_date']; ?></td>
							<td><?= $appt['appointment_time']; ?></td>
							<td>
								<?php if ($status === 'Scheduled'): ?>
									<span class="badge bg-primary"><i class="bi bi-clock"></i> Scheduled</span>
								<?php elseif ($status === 'Completed'): ?>
									<span class="badge bg-success"><i class="bi bi-check-circle"></i> Completed</span>
								<?php elseif ($status === 'Expired'): ?>
									<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Expired</span>
								<?php else: ?>
									<span class="badge bg-secondary"><?= htmlspecialchars($status); ?></span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ($apptDateTime > $now && $status === 'Scheduled'): ?>
									<form action="cancel_appointment.php" method="POST" style="display:inline;">
										<input type="hidden" name="appointment_id" value="<?= $appt['id']; ?>">
										<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Cancel this appointment?')">Cancel</button>
									</form>

									<form action="reschedule_appointment.php" method="GET" style="display:inline;">
										<input type="hidden" name="appointment_id" value="<?= $appt['id']; ?>">
										<button type="submit" class="btn btn-sm btn-warning">Reschedule</button>
									</form>

								<?php elseif (
									$status === 'Completed' &&
									isset($prescriptionByAppointment[$appt['id']])
								): ?>
									<a
										href="view_prescription_patient.php?id=<?= $prescriptionByAppointment[$appt['id']]; ?>"
										class="btn btn-sm btn-info mt-1">
										View Prescription
									</a>
									<a href="prescriptions/prescription_<?= $appt['id']; ?>.pdf" class="btn btn-sm btn-outline-success mt-1" download>
										Download PDF
									</a>
								<?php else: ?>
									<span class="text-muted">—</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="6" class="text-center text-muted">No appointments yet.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>




		<hr>
		<h4>Your Bills</h4>

		<?php
		$billStmt = $pdo->prepare("
      SELECT b.*, a.appointment_date
      FROM bills b
      JOIN appointments a ON b.appointment_id = a.id
      WHERE b.patient_id = ?
      ORDER BY b.created_at DESC
    ");
		$billStmt->execute([$_SESSION['patient_id']]);
		$bills = $billStmt->fetchAll();
		?>

		<table class="table table-bordered table-hover bg-white shadow-sm">
			<thead class="table-light">
				<tr>
					<th>Bill ID</th>
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
								<a href="paynow.php?bill_id=<?= $bill['id'] ?>" class="btn btn-sm btn-outline-primary">
									View Details
								</a>
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
						<td colspan="7" class="text-center text-muted">No bills found.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>


		<table class="table">
			<thead>
				<tr>
					<th>Amount</th>
					<th>Status</th>
					<th>Notes</th>
					<th>Date</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php if ($bills): ?>
					<?php foreach ($bills as $bill): ?>
						<tr>
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
									<!--<form method="POST" action="pay_bill.php" onsubmit="return confirm('Confirm payment?');">
                    <input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-primary">Pay Now</button>
                  </form>
                -->

									<form method="GET" action="paynow.php">
										<input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">
										<button type="submit" class="btn btn-sm btn-primary">Pay Now</button>
									</form>

								<?php else: ?>
									<span class="text-muted">—</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="5" class="text-center text-muted">No bills found.</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>


		<form action="book_appointment.php" method="POST" class="mb-4">
			<div class="mb-3">
				<label for="doctor">Choose Doctor:</label>
				<select name="doctor_id" class="form-select" required>
					<option value="">-- Select Doctor --</option>
					<?php foreach ($doctors as $doc): ?>
						<option value="<?php echo $doc['id']; ?>">
							Dr. <?php echo htmlspecialchars($doc['name']) . " (" . htmlspecialchars($doc['specialization']) . ")"; ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="mb-3">
				<label>Date:</label>
				<input type="date" name="appointment_date" class="form-control" required>
			</div>
			<div class="mb-3">
				<label>Time:</label>
				<input type="time" name="appointment_time" class="form-control" required>
			</div>
			<button type="submit" class="btn btn-primary">Book Appointment</button>
		</form>

		<a href="logout.php" class="btn btn-danger">Logout</a>
	</div>
</body>

</html>