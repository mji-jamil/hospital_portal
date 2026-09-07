<?php
require 'config.php';
session_start();

if (!isset($_SESSION['doctor_id'])) {
	die("Unauthorized access.");
}

$doctor_id = $_SESSION['doctor_id'];

// Get patient ID
if (!isset($_GET['patient_id'])) {
	die("Patient ID is required.");
}

$patient_id = (int) $_GET['patient_id'];

// Verify patient exists
$patientStmt = $pdo->prepare("
    SELECT id, name
    FROM patients
    WHERE id = ?
");
$patientStmt->execute([$patient_id]);
$patient = $patientStmt->fetch();

if (!$patient) {
	die("Patient not found.");
}

// Fetch all prescriptions for this patient written by this doctor
$stmt = $pdo->prepare("
    SELECT 
        pr.id,
        pr.appointment_id,
        pr.diagnosis,
        pr.advice,
        pr.created_at,
        a.appointment_date
    FROM prescriptions pr
    JOIN appointments a ON pr.appointment_id = a.id
    WHERE pr.patient_id = ?
      AND pr.doctor_id = ?
    ORDER BY pr.id DESC
");

$stmt->execute([$patient_id, $doctor_id]);
$prescriptions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Prescription History</title>

	<!-- <link
		href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
		rel="stylesheet"> -->
		<link href="assets/css/bootstrap.min.css" rel="stylesheet">

	<style>
		body {
			background: #f5f7fa;
		}

		.history-card {
			border: none;
			border-radius: 12px;
			box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
		}

		.prescription-row {
			border-bottom: 1px solid #eee;
			padding: 18px 0;
		}

		.prescription-row:last-child {
			border-bottom: none;
		}

		.diagnosis {
			font-weight: 600;
		}

		.date {
			color: #6c757d;
			font-size: 14px;
		}
	</style>
</head>

<body>

	<div class="container py-5">

		<div class="d-flex justify-content-between align-items-center mb-4">

			<div>
				<h2 class="mb-1">Prescription History</h2>
				<p class="text-muted mb-0">
					Patient: <?= htmlspecialchars($patient['name']) ?>
				</p>
			</div>

			<a href="doctor_dashboard.php" class="btn btn-secondary">
				Back to Dashboard
			</a>

		</div>


		<div class="card history-card">
			<div class="card-body">

				<?php if (empty($prescriptions)): ?>

					<div class="text-center py-5">
						<h5>No prescriptions found</h5>
						<p class="text-muted">
							This patient does not have any prescriptions from you yet.
						</p>
					</div>

				<?php else: ?>

					<?php foreach ($prescriptions as $prescription): ?>

						<div class="prescription-row">

							<div class="row align-items-center">

								<div class="col-md-2">
									<strong>
										Prescription #<?= $prescription['id'] ?>
									</strong>
								</div>

								<div class="col-md-3">

									<div class="diagnosis">
										<?= htmlspecialchars($prescription['diagnosis']) ?>
									</div>

									<div class="date">
										Appointment:
										<?= htmlspecialchars($prescription['appointment_date']) ?>
									</div>

								</div>

								<div class="col-md-3">

									<div class="date">
										Created:
										<?= htmlspecialchars($prescription['created_at']) ?>
									</div>

								</div>

								<div class="col-md-4 text-md-end mt-3 mt-md-0">

									<a
										href="view_prescription.php?id=<?= $prescription['id'] ?>"
										class="btn btn-primary">
										View Prescription
									</a>

									<a
										href="prescriptions/prescription_<?= $prescription['appointment_id'] ?>.pdf"
										class="btn btn-success"
										download>
										PDF
									</a>

								</div>

							</div>

						</div>

					<?php endforeach; ?>

				<?php endif; ?>

			</div>
		</div>

	</div>

</body>

</html>