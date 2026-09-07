<?php
require 'config.php';
session_start();

if (!isset($_SESSION['patient_id'])) {
	die("Unauthorized access.");
}

$prescription_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($prescription_id <= 0) {
	die("Prescription ID is required.");
}
$patient_id = (int) $_SESSION['patient_id'];


// Fetch the specific prescription
$stmt = $pdo->prepare("
    SELECT
        pr.*,
        d.name AS doctor_name,
        a.appointment_date
    FROM prescriptions pr
    JOIN doctors d ON pr.doctor_id = d.id
    JOIN appointments a ON pr.appointment_id = a.id
    WHERE pr.id = ?
      AND pr.patient_id = ?
");

$stmt->execute([
	$prescription_id,
	$patient_id
]);

$prescription = $stmt->fetch();

if (!$prescription) {
	die("Prescription not found or access denied.");
}


// Fetch structured medicine items
$itemStmt = $pdo->prepare("
    SELECT
        medicine_name,
        strength,
        dosage,
        frequency,
        meal_timing,
        duration,
        quantity,
        instructions
    FROM prescription_items
    WHERE prescription_id = ?
    ORDER BY id ASC
");

$itemStmt->execute([$prescription_id]);

$items = $itemStmt->fetchAll();


// Keep the existing PDF filename system
$pdfPath = "prescriptions/prescription_"
	. $prescription['appointment_id']
	. ".pdf";
?>

<!DOCTYPE html>
<html lang="en">

<head>

	<meta charset="UTF-8">

	<meta
		name="viewport"
		content="width=device-width, initial-scale=1.0">

	<title>Your Prescription</title>

	<!-- <link
		href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
		rel="stylesheet"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">

	<style>
		body {
			background: #f5f7fa;
		}

		.prescription-container {
			max-width: 950px;
			margin: auto;
		}

		.card {
			border: none;
			border-radius: 12px;
			box-shadow: 0 3px 12px rgba(0, 0, 0, 0.08);
		}

		.section-title {
			font-weight: 600;
			margin-bottom: 15px;
		}

		.medicine-card {
			border: 1px solid #dee2e6;
			border-radius: 10px;
			padding: 18px;
			margin-bottom: 15px;
			background: #fff;
		}

		.medicine-name {
			font-size: 18px;
			font-weight: 600;
		}

		.medicine-detail {
			margin-bottom: 5px;
		}

		.label {
			font-weight: 600;
		}

		.empty-message {
			color: #6c757d;
		}

		@media print {

			.no-print {
				display: none !important;
			}

			body {
				background: #fff;
			}

			.card {
				box-shadow: none;
			}

		}
	</style>

</head>


<body>

	<div class="container py-5 prescription-container">


		<!-- Header -->

		<div class="d-flex justify-content-between align-items-center mb-4">

			<div>

				<h2 class="mb-1">
					🩺 Your Prescription
				</h2>

				<p class="text-muted mb-0">
					Prescription #<?= $prescription['id'] ?>
				</p>

			</div>

			<div class="no-print">

				<a
					href="patient_prescription_history.php"
					class="btn btn-secondary">
					← Prescription History
				</a>

			</div>

		</div>


		<!-- Doctor / Appointment Information -->

		<div class="card mb-4">

			<div class="card-body">

				<div class="row">

					<div class="col-md-6">

						<p>
							<span class="label">Doctor:</span>
							Dr.
							<?= htmlspecialchars(
								$prescription['doctor_name']
							) ?>
						</p>

						<p>
							<span class="label">Appointment Date:</span>
							<?= htmlspecialchars(
								$prescription['appointment_date']
							) ?>
						</p>

					</div>


					<div class="col-md-6">

						<p>
							<span class="label">Prescription Date:</span>
							<?= htmlspecialchars(
								$prescription['created_at']
							) ?>
						</p>

					</div>

				</div>

			</div>

		</div>


		<!-- Diagnosis -->

		<div class="card mb-4">

			<div class="card-body">

				<h5 class="section-title">
					Diagnosis
				</h5>

				<p class="mb-0">
					<?= nl2br(
						htmlspecialchars(
							$prescription['diagnosis']
						)
					) ?>
				</p>

			</div>

		</div>


		<!-- Medications -->

		<div class="card mb-4">

			<div class="card-body">

				<h5 class="section-title">
					Medications
				</h5>


				<?php if (!empty($items)): ?>

					<?php foreach ($items as $index => $item): ?>

						<div class="medicine-card">

							<div class="medicine-name mb-3">

								<?= ($index + 1) ?>.
								<?= htmlspecialchars(
									$item['medicine_name']
								) ?>

								<?php if (!empty($item['strength'])): ?>

									<span class="text-muted">
										<?= htmlspecialchars(
											$item['strength']
										) ?>
									</span>

								<?php endif; ?>

							</div>


							<div class="row">


								<?php if (!empty($item['dosage'])): ?>

									<div class="col-md-4 medicine-detail">

										<span class="label">
											Dosage:
										</span>

										<?= htmlspecialchars(
											$item['dosage']
										) ?>

									</div>

								<?php endif; ?>


								<?php if (!empty($item['frequency'])): ?>

									<div class="col-md-4 medicine-detail">

										<span class="label">
											Frequency:
										</span>

										<?= htmlspecialchars(
											$item['frequency']
										) ?>

									</div>

								<?php endif; ?>


								<?php if (!empty($item['meal_timing'])): ?>

									<div class="col-md-4 medicine-detail">

										<span class="label">
											Meal Timing:
										</span>

										<?= htmlspecialchars(
											$item['meal_timing']
										) ?>

									</div>

								<?php endif; ?>


								<?php if (!empty($item['duration'])): ?>

									<div class="col-md-4 medicine-detail">

										<span class="label">
											Duration:
										</span>

										<?= htmlspecialchars(
											$item['duration']
										) ?>

									</div>

								<?php endif; ?>


								<?php if (!empty($item['quantity'])): ?>

									<div class="col-md-4 medicine-detail">

										<span class="label">
											Quantity:
										</span>

										<?= htmlspecialchars(
											$item['quantity']
										) ?>

									</div>

								<?php endif; ?>

							</div>


							<?php if (!empty($item['instructions'])): ?>

								<div class="mt-3">

									<span class="label">
										Instructions:
									</span>

									<div>
										<?= nl2br(
											htmlspecialchars(
												$item['instructions']
											)
										) ?>
									</div>

								</div>

							<?php endif; ?>

						</div>

					<?php endforeach; ?>


				<?php elseif (!empty($prescription['medications'])): ?>

					<!-- Compatibility with old prescriptions -->

					<p class="mb-0">
						<?= nl2br(
							htmlspecialchars(
								$prescription['medications']
							)
						) ?>
					</p>


				<?php else: ?>

					<p class="empty-message mb-0">
						No medication information available.
					</p>

				<?php endif; ?>

			</div>

		</div>


		<!-- Advice -->

		<?php if (!empty($prescription['advice'])): ?>

			<div class="card mb-4">

				<div class="card-body">

					<h5 class="section-title">
						Advice
					</h5>

					<p class="mb-0">
						<?= nl2br(
							htmlspecialchars(
								$prescription['advice']
							)
						) ?>
					</p>

				</div>

			</div>

		<?php endif; ?>


		<!-- Actions -->

		<div class="text-center mt-4 no-print">

			<a
				href="<?= htmlspecialchars($pdfPath) ?>"
				class="btn btn-success"
				download>
				📄 Download PDF
			</a>


			<button
				type="button"
				onclick="window.print()"
				class="btn btn-primary">
				🖨 Print
			</button>


			<a
				href="patient_prescription_history.php"
				class="btn btn-secondary">
				← Back to History
			</a>

		</div>

	</div>

</body>

</html>