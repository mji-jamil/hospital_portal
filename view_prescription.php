<?php

require 'config.php';
session_start();


/*
|--------------------------------------------------------------------------
| Only doctors can view prescriptions
|--------------------------------------------------------------------------
*/

if (!isset($_GET['appointment_id']) || !isset($_SESSION['doctor_id'])) {
	die("Unauthorized access.");
}


$appointment_id = (int) $_GET['appointment_id'];
$doctor_id = (int) $_SESSION['doctor_id'];


/*
|--------------------------------------------------------------------------
| Fetch prescription and related information
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        pr.*,
        p.name AS patient_name,
        d.name AS doctor_name,
        a.appointment_date
    FROM prescriptions pr
    JOIN patients p
        ON pr.patient_id = p.id
    JOIN doctors d
        ON pr.doctor_id = d.id
    JOIN appointments a
        ON pr.appointment_id = a.id
    WHERE pr.appointment_id = ?
      AND pr.doctor_id = ?
    ORDER BY pr.id DESC
    LIMIT 1
");

$stmt->execute([
	$appointment_id,
	$doctor_id
]);

$prescription = $stmt->fetch();


/*
|--------------------------------------------------------------------------
| Check prescription
|--------------------------------------------------------------------------
*/

if (!$prescription) {
	die("Prescription not found or access denied.");
}


/*
|--------------------------------------------------------------------------
| Fetch individual medicines
|--------------------------------------------------------------------------
*/

$medicineStmt = $pdo->prepare("
    SELECT
        id,
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

$medicineStmt->execute([
	$prescription['id']
]);

$medicines = $medicineStmt->fetchAll();

?>

<!DOCTYPE html>

<html>

<head>

	<title>View Prescription</title>

	<meta charset="UTF-8">

	<meta
		name="viewport"
		content="width=device-width, initial-scale=1.0">

	<!-- <link
		href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
		rel="stylesheet"> -->
		<link href="assets/css/bootstrap.min.css" rel="stylesheet">


	<style>
		body {
			background-color: #f8f9fa;
		}

		.prescription-container {
			max-width: 1000px;
			margin: auto;
		}

		.label {
			font-weight: 600;
		}

		.section {
			margin-bottom: 1.5rem;
		}

		.medicine-card {
			border: 1px solid #dee2e6;
			border-radius: 8px;
			margin-bottom: 15px;
			overflow: hidden;
		}

		.medicine-header {
			background-color: #f1f3f5;
			padding: 12px 15px;
			font-weight: 600;
		}

		.medicine-body {
			padding: 15px;
		}

		.medicine-table th {
			width: 25%;
		}

		@media print {

			.no-print {
				display: none !important;
			}

			body {
				background: white;
			}

			.card {
				box-shadow: none !important;
			}

		}
	</style>

</head>


<body>


	<div class="container py-5 prescription-container">


		<!-- ============================================================
         HEADER
         ============================================================ -->

		<div class="text-center mb-4">

			<h2 class="fw-bold">
				🩺 Medical Prescription
			</h2>

			<p class="text-muted mb-0">
				Medipulse
			</p>

		</div>


		<!-- ============================================================
         PATIENT / DOCTOR INFORMATION
         ============================================================ -->

		<div class="card shadow-sm mb-4">

			<div class="card-header">

				<h5 class="mb-0">
					👤 Patient Information
				</h5>

			</div>


			<div class="card-body">

				<div class="row">

					<div class="col-md-6 mb-3">

						<span class="label">
							Patient:
						</span>

						<?= htmlspecialchars(
							$prescription['patient_name']
						) ?>

					</div>


					<div class="col-md-6 mb-3">

						<span class="label">
							Patient ID:
						</span>

						<?= htmlspecialchars(
							$prescription['patient_id']
						) ?>

					</div>


					<div class="col-md-6 mb-3">

						<span class="label">
							Doctor:
						</span>

						Dr.
						<?= htmlspecialchars(
							$prescription['doctor_name']
						) ?>

					</div>


					<div class="col-md-6 mb-3">

						<span class="label">
							Date:
						</span>

						<?= htmlspecialchars(
							$prescription['appointment_date']
						) ?>

					</div>

				</div>

			</div>

		</div>


		<!-- ============================================================
         DIAGNOSIS
         ============================================================ -->

		<div class="card shadow-sm mb-4">

			<div class="card-header">

				<h5 class="mb-0">
					🩺 Diagnosis
				</h5>

			</div>


			<div class="card-body">

				<p class="mb-0">

					<?= nl2br(
						htmlspecialchars(
							$prescription['diagnosis']
						)
					) ?>

				</p>

			</div>

		</div>


		<!-- ============================================================
         MEDICINES
         ============================================================ -->

		<div class="card shadow-sm mb-4">

			<div class="card-header">

				<h5 class="mb-0">
					💊 Medicines
				</h5>

			</div>


			<div class="card-body">


				<?php if (!empty($medicines)): ?>


					<?php foreach ($medicines as $index => $medicine): ?>

						<div class="medicine-card">


							<!-- Medicine header -->

							<div class="medicine-header">

								Medicine <?= $index + 1 ?>:
								<?= htmlspecialchars(
									$medicine['medicine_name']
								) ?>

								<?php if (!empty($medicine['strength'])): ?>

									<span class="text-muted">

										—
										<?= htmlspecialchars(
											$medicine['strength']
										) ?>

									</span>

								<?php endif; ?>

							</div>


							<!-- Medicine details -->

							<div class="medicine-body">

								<div class="table-responsive">

									<table class="table table-bordered medicine-table mb-0">

										<tbody>


											<!-- Dosage -->

											<?php if (!empty($medicine['dosage'])): ?>

												<tr>

													<th>
														How Many
													</th>

													<td>
														<?= htmlspecialchars(
															$medicine['dosage']
														) ?>
													</td>

												</tr>

											<?php endif; ?>


											<!-- Frequency -->

											<?php if (!empty($medicine['frequency'])): ?>

												<tr>

													<th>
														Frequency
													</th>

													<td>
														<?= htmlspecialchars(
															$medicine['frequency']
														) ?>
													</td>

												</tr>

											<?php endif; ?>


											<!-- Meal timing -->

											<?php if (!empty($medicine['meal_timing'])): ?>

												<tr>

													<th>
														When to Take
													</th>

													<td>
														<?= htmlspecialchars(
															$medicine['meal_timing']
														) ?>
													</td>

												</tr>

											<?php endif; ?>


											<!-- Duration -->

											<?php if (!empty($medicine['duration'])): ?>

												<tr>

													<th>
														Duration
													</th>

													<td>
														<?= htmlspecialchars(
															$medicine['duration']
														) ?>
													</td>

												</tr>

											<?php endif; ?>


											<!-- Quantity -->

											<?php if (!empty($medicine['quantity'])): ?>

												<tr>

													<th>
														Quantity
													</th>

													<td>
														<?= htmlspecialchars(
															$medicine['quantity']
														) ?>
													</td>

												</tr>

											<?php endif; ?>


											<!-- Additional instructions -->

											<?php if (!empty($medicine['instructions'])): ?>

												<tr>

													<th>
														Instructions
													</th>

													<td>
														<?= nl2br(
															htmlspecialchars(
																$medicine['instructions']
															)
														) ?>
													</td>

												</tr>

											<?php endif; ?>


										</tbody>

									</table>

								</div>

							</div>

						</div>

					<?php endforeach; ?>


				<?php else: ?>


					<!-- Fallback for old prescriptions -->

					<?php if (!empty($prescription['medications'])): ?>

						<div class="alert alert-secondary">

							<strong>
								Medications:
							</strong>

							<div class="mt-2">

								<?= nl2br(
									htmlspecialchars(
										$prescription['medications']
									)
								) ?>

							</div>

						</div>

					<?php else: ?>

						<div class="alert alert-warning mb-0">

							No medicine information found.

						</div>

					<?php endif; ?>


				<?php endif; ?>

			</div>

		</div>


		<!-- ============================================================
         ADVICE
         ============================================================ -->

		<?php if (!empty($prescription['advice'])): ?>

			<div class="card shadow-sm mb-4">

				<div class="card-header">

					<h5 class="mb-0">
						📝 Advice
					</h5>

				</div>


				<div class="card-body">

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


		<!-- ============================================================
         ACTION BUTTONS
         ============================================================ -->

		<div class="mt-4 no-print">

			<a
				href="prescriptions/prescription_<?= $appointment_id ?>.pdf"
				class="btn btn-success">

				📄 Download PDF

			</a>


			<button
				onclick="window.print()"
				class="btn btn-primary ms-2">

				🖨️ Print Prescription

			</button>


			<a
				href="doctor_dashboard.php"
				class="btn btn-secondary ms-2">

				← Back to Dashboard

			</a>

		</div>


	</div>


</body>

</html>