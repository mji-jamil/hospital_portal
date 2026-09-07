<?php
require 'config.php';
session_start();

if (!isset($_SESSION['patient_id'])) {
	die("Unauthorized access.");
}

$patient_id = (int) $_SESSION['patient_id'];


// Fetch patient information
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


// Fetch all prescriptions belonging to this patient
$stmt = $pdo->prepare("
    SELECT
        pr.id,
        pr.appointment_id,
        pr.diagnosis,
        pr.advice,
        pr.created_at,
        d.name AS doctor_name,
        a.appointment_date
    FROM prescriptions pr
    JOIN doctors d ON pr.doctor_id = d.id
    JOIN appointments a ON pr.appointment_id = a.id
    WHERE pr.patient_id = ?
    ORDER BY pr.id DESC
");

$stmt->execute([$patient_id]);
$prescriptions = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="UTF-8">

	<meta
		name="viewport"
		content="width=device-width, initial-scale=1.0">

	<title>My Prescription History</title>

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


		<!-- Header -->

		<div class="d-flex justify-content-between align-items-center mb-4">

			<div>

				<h2 class="mb-1">
					🩺 My Prescription History
				</h2>

				<p class="text-muted mb-0">
					Patient: <?= htmlspecialchars($patient['name']) ?>
				</p>

			</div>

			<a
				href="patient_dashboard.php"
				class="btn btn-secondary">
				Back to Dashboard
			</a>

		</div>


		<!-- Prescription List -->

		<div class="card history-card">

			<div class="card-body">

				<?php if (empty($prescriptions)): ?>

					<div class="text-center py-5">

						<h5>No prescriptions found</h5>

						<p class="text-muted">
							You do not have any prescriptions yet.
						</p>

					</div>

				<?php else: ?>


					<?php foreach ($prescriptions as $prescription): ?>

						<div class="prescription-row">

							<div class="row align-items-center">


								<!-- Prescription ID -->

								<div class="col-md-2">

									<strong>
										Prescription #<?= $prescription['id'] ?>
									</strong>

								</div>


								<!-- Diagnosis -->

								<div class="col-md-3">

									<div class="diagnosis">

										<?= htmlspecialchars(
											$prescription['diagnosis']
										) ?>

									</div>

									<div class="date">

										Appointment:
										<?= htmlspecialchars(
											$prescription['appointment_date']
										) ?>

									</div>

								</div>


								<!-- Doctor -->

								<div class="col-md-3">

									<div>

										<span class="date">
											Doctor:
										</span>

										Dr.
										<?= htmlspecialchars(
											$prescription['doctor_name']
										) ?>

									</div>

									<div class="date">

										Created:
										<?= htmlspecialchars(
											$prescription['created_at']
										) ?>

									</div>

								</div>


								<!-- Actions -->

								<div class="col-md-4 text-md-end mt-3 mt-md-0">

									<a
										href="view_prescription_patient.php?id=<?= $prescription['id'] ?>"
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