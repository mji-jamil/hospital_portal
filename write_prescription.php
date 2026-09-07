<?php

require 'config.php';
session_start();


/*
|--------------------------------------------------------------------------
| Only doctors can access this page
|--------------------------------------------------------------------------
*/

if (!isset($_GET['appointment_id']) || !isset($_SESSION['doctor_id'])) {
	die("Unauthorized access.");
}


$appointment_id = (int) $_GET['appointment_id'];
$doctor_id = (int) $_SESSION['doctor_id'];


/*
|--------------------------------------------------------------------------
| Get appointment and patient information
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        a.*,
        p.name AS patient_name,
        p.email AS patient_email
    FROM appointments a
    JOIN patients p ON a.patient_id = p.id
    WHERE a.id = ? AND a.doctor_id = ?
");

$stmt->execute([
	$appointment_id,
	$doctor_id
]);

$appt = $stmt->fetch();


if (!$appt) {
	die("Invalid appointment.");
}

?>

<!DOCTYPE html>
<html>

<head>

	<title>Write Prescription</title>

	<meta charset="UTF-8">

	<meta
		name="viewport"
		content="width=device-width, initial-scale=1.0">

	<!-- <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">

</head>


<body class="bg-light">


	<div class="container py-5">

		<div class="row justify-content-center">

			<div class="col-lg-11">


				<!-- ======================================================
                 PAGE HEADER
                 ====================================================== -->

				<div class="mb-4">

					<h2 class="fw-bold">
						🩺 Write Prescription
					</h2>

					<p class="text-muted mb-0">
						Create a prescription for the patient.
					</p>

				</div>


				<!-- ======================================================
                 PATIENT INFORMATION
                 ====================================================== -->

				<div class="card shadow-sm mb-4">

					<div class="card-header">

						<h5 class="mb-0">
							👤 Patient Information
						</h5>

					</div>


					<div class="card-body">

						<div class="row">

							<div class="col-md-6 mb-2">

								<strong>
									Patient:
								</strong>

								<?= htmlspecialchars($appt['patient_name']) ?>

							</div>


							<div class="col-md-6 mb-2">

								<strong>
									Patient ID:
								</strong>

								<?= htmlspecialchars($appt['patient_id']) ?>

							</div>


							<div class="col-md-6 mb-2">

								<strong>
									Appointment ID:
								</strong>

								<?= htmlspecialchars($appt['id']) ?>

							</div>


							<div class="col-md-6 mb-2">

								<strong>
									Doctor:
								</strong>

								<?= htmlspecialchars(
									$_SESSION['doctor_name'] ?? 'Doctor'
								) ?>

							</div>

						</div>

					</div>

				</div>


				<!-- ======================================================
                 PRESCRIPTION FORM
                 ====================================================== -->

				<form
					method="POST"
					action="submit_prescription.php"
					id="prescriptionForm">


					<input
						type="hidden"
						name="appointment_id"
						value="<?= $appt['id'] ?>">


					<input
						type="hidden"
						name="patient_id"
						value="<?= $appt['patient_id'] ?>">


					<!-- ==================================================
                     DIAGNOSIS
                     ================================================== -->

					<div class="card shadow-sm mb-4">

						<div class="card-header">

							<h5 class="mb-0">
								🩺 Diagnosis
							</h5>

						</div>


						<div class="card-body">

							<textarea
								name="diagnosis"
								class="form-control"
								rows="3"
								placeholder="Enter the patient's diagnosis..."
								required></textarea>

						</div>

					</div>


					<!-- ==================================================
                     MEDICINES
                     ================================================== -->

					<div class="card shadow-sm mb-4">

						<div class="card-header d-flex justify-content-between align-items-center">

							<h5 class="mb-0">
								💊 Medicines
							</h5>

							<button
								type="button"
								class="btn btn-primary btn-sm"
								onclick="addMedicine()">

								+ Add Medicine

							</button>

						</div>


						<div class="card-body">


							<div
								id="medicineContainer">


								<!-- ======================================
                                 FIRST MEDICINE
                                 ====================================== -->

								<div class="medicine-item border rounded p-3 mb-3">

									<div class="d-flex justify-content-between align-items-center mb-3">

										<h6 class="mb-0">
											Medicine 1
										</h6>

									</div>


									<div class="row">


										<!-- Medicine Name -->

										<div class="col-md-6 mb-3">

											<label class="form-label">
												Medicine Name
												<span class="text-danger">*</span>
											</label>

											<input
												type="text"
												name="medicine_name[]"
												class="form-control"
												placeholder="e.g. Metformin"
												required>

										</div>


										<!-- Strength -->

										<div class="col-md-3 mb-3">

											<label class="form-label">
												Strength
											</label>

											<input
												type="text"
												name="strength[]"
												class="form-control"
												placeholder="e.g. 500 mg">

										</div>


										<!-- Dosage -->

										<div class="col-md-3 mb-3">

											<label class="form-label">
												How Many Per Dose
											</label>

											<input
												type="text"
												name="dosage[]"
												class="form-control"
												placeholder="e.g. 1 tablet">

										</div>


										<!-- Frequency -->

										<div class="col-md-4 mb-3">

											<label class="form-label">
												Frequency
											</label>

											<select
												name="frequency[]"
												class="form-select">

												<option value="">
													-- Select Frequency --
												</option>

												<option value="Once daily">
													Once daily
												</option>

												<option value="Twice daily">
													Twice daily
												</option>

												<option value="Three times daily">
													Three times daily
												</option>

												<option value="Four times daily">
													Four times daily
												</option>

												<option value="Every 4 hours">
													Every 4 hours
												</option>

												<option value="Every 6 hours">
													Every 6 hours
												</option>

												<option value="Every 8 hours">
													Every 8 hours
												</option>

												<option value="Every 12 hours">
													Every 12 hours
												</option>

												<option value="As needed">
													As needed
												</option>

											</select>

										</div>


										<!-- Meal Timing -->

										<div class="col-md-4 mb-3">

											<label class="form-label">
												When to Take
											</label>

											<select
												name="meal_timing[]"
												class="form-select">

												<option value="">
													-- Select Timing --
												</option>

												<option value="Before meal">
													Before meal
												</option>

												<option value="With meal">
													With meal
												</option>

												<option value="After meal">
													After meal
												</option>

												<option value="Any time">
													Any time
												</option>

												<option value="At bedtime">
													At bedtime
												</option>

												<option value="Morning">
													Morning
												</option>

												<option value="Evening">
													Evening
												</option>

											</select>

										</div>


										<!-- Duration -->

										<div class="col-md-2 mb-3">

											<label class="form-label">
												Duration
											</label>

											<input
												type="text"
												name="duration[]"
												class="form-control"
												placeholder="e.g. 7 days">

										</div>


										<!-- Quantity -->

										<div class="col-md-2 mb-3">

											<label class="form-label">
												Quantity
											</label>

											<input
												type="text"
												name="quantity[]"
												class="form-control"
												placeholder="e.g. 14">

										</div>


										<!-- Instructions -->

										<div class="col-md-12 mb-2">

											<label class="form-label">
												Additional Instructions
											</label>

											<input
												type="text"
												name="instructions[]"
												class="form-control"
												placeholder="e.g. Take with a full glass of water">

										</div>

									</div>

								</div>


							</div>


							<div class="text-muted small">

								💡 You can add multiple medicines using
								<strong>+ Add Medicine</strong>.

							</div>

						</div>

					</div>


					<!-- ==================================================
                     ADVICE
                     ================================================== -->

					<div class="card shadow-sm mb-4">

						<div class="card-header">

							<h5 class="mb-0">
								📝 Advice
							</h5>

						</div>


						<div class="card-body">

							<textarea
								name="advice"
								class="form-control"
								rows="4"
								placeholder="Enter additional advice for the patient (optional)..."></textarea>

						</div>

					</div>


					<!-- ==================================================
                     SUBMIT
                     ================================================== -->

					<div class="d-flex gap-2">

						<button
							type="submit"
							class="btn btn-success btn-lg">

							💾 Save Prescription

						</button>


						<a
							href="doctor_dashboard.php"
							class="btn btn-secondary btn-lg">

							← Back to Dashboard

						</a>

					</div>


				</form>

			</div>

		</div>

	</div>


	<!-- ================================================================
     JAVASCRIPT
     ================================================================ -->

	<script>
		let medicineCount = 1;


		function addMedicine() {

			medicineCount++;

			const container =
				document.getElementById("medicineContainer");


			const medicine = document.createElement("div");

			medicine.className =
				"medicine-item border rounded p-3 mb-3";


			medicine.innerHTML = `

        <div class="d-flex justify-content-between align-items-center mb-3">

            <h6 class="mb-0">
                Medicine ${medicineCount}
            </h6>

            <button
                type="button"
                class="btn btn-outline-danger btn-sm"
                onclick="removeMedicine(this)">

                Remove

            </button>

        </div>


        <div class="row">


            <div class="col-md-6 mb-3">

                <label class="form-label">
                    Medicine Name
                    <span class="text-danger">*</span>
                </label>

                <input
                    type="text"
                    name="medicine_name[]"
                    class="form-control"
                    placeholder="e.g. Metformin"
                    required>

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    Strength
                </label>

                <input
                    type="text"
                    name="strength[]"
                    class="form-control"
                    placeholder="e.g. 500 mg">

            </div>


            <div class="col-md-3 mb-3">

                <label class="form-label">
                    How Many Per Dose
                </label>

                <input
                    type="text"
                    name="dosage[]"
                    class="form-control"
                    placeholder="e.g. 1 tablet">

            </div>


            <div class="col-md-4 mb-3">

                <label class="form-label">
                    Frequency
                </label>

                <select
                    name="frequency[]"
                    class="form-select">

                    <option value="">
                        -- Select Frequency --
                    </option>

                    <option value="Once daily">
                        Once daily
                    </option>

                    <option value="Twice daily">
                        Twice daily
                    </option>

                    <option value="Three times daily">
                        Three times daily
                    </option>

                    <option value="Four times daily">
                        Four times daily
                    </option>

                    <option value="Every 4 hours">
                        Every 4 hours
                    </option>

                    <option value="Every 6 hours">
                        Every 6 hours
                    </option>

                    <option value="Every 8 hours">
                        Every 8 hours
                    </option>

                    <option value="Every 12 hours">
                        Every 12 hours
                    </option>

                    <option value="As needed">
                        As needed
                    </option>

                </select>

            </div>


            <div class="col-md-4 mb-3">

                <label class="form-label">
                    When to Take
                </label>

                <select
                    name="meal_timing[]"
                    class="form-select">

                    <option value="">
                        -- Select Timing --
                    </option>

                    <option value="Before meal">
                        Before meal
                    </option>

                    <option value="With meal">
                        With meal
                    </option>

                    <option value="After meal">
                        After meal
                    </option>

                    <option value="Any time">
                        Any time
                    </option>

                    <option value="At bedtime">
                        At bedtime
                    </option>

                    <option value="Morning">
                        Morning
                    </option>

                    <option value="Evening">
                        Evening
                    </option>

                </select>

            </div>


            <div class="col-md-2 mb-3">

                <label class="form-label">
                    Duration
                </label>

                <input
                    type="text"
                    name="duration[]"
                    class="form-control"
                    placeholder="e.g. 7 days">

            </div>


            <div class="col-md-2 mb-3">

                <label class="form-label">
                    Quantity
                </label>

                <input
                    type="text"
                    name="quantity[]"
                    class="form-control"
                    placeholder="e.g. 14">

            </div>


            <div class="col-md-12 mb-2">

                <label class="form-label">
                    Additional Instructions
                </label>

                <input
                    type="text"
                    name="instructions[]"
                    class="form-control"
                    placeholder="e.g. Take with a full glass of water">

            </div>


        </div>

    `;


			container.appendChild(medicine);

		}


		function removeMedicine(button) {

			const medicine =
				button.closest(".medicine-item");

			medicine.remove();

		}
	</script>


</body>

</html>