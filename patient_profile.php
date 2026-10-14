<?php

session_start();

if (!isset($_SESSION['doctor_id'])) {
	header("Location: doctor_login.php");
	exit();
}

$conn = new mysqli("localhost", "root", "", "hospital_db");

if ($conn->connect_error) {
	die("Database connection failed: " . $conn->connect_error);
}

$patientId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($patientId <= 0) {
	die("Invalid patient ID.");
}


/*
|--------------------------------------------------------------------------
| Save updated profile
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	$age = ($_POST['age'] ?? '') !== ''
		? (int)$_POST['age']
		: null;

	// Accept the doctor's selection; the form is prefilled from the database.
	$gender = trim($_POST['gender'] ?? '');
	if (!in_array($gender, ['Male', 'Female'], true)) {
		$error = 'Please select Male or Female for Sex (ML).';
		$gender = '';
	}

	// Map the selected gender to the ML feature: Male=1, Female=0.
	$sex = ($gender === 'Male') ? 1 : (($gender === 'Female') ? 0 : null);

	$cp = ($_POST['cp'] ?? '') !== ''
		? (int)$_POST['cp']
		: null;

	$trestbps = ($_POST['trestbps'] ?? '') !== ''
		? (float)$_POST['trestbps']
		: null;

	$chol = ($_POST['chol'] ?? '') !== ''
		? (float)$_POST['chol']
		: null;

	$fbs = ($_POST['fbs'] ?? '') !== ''
		? (int)$_POST['fbs']
		: null;

	$restecg = ($_POST['restecg'] ?? '') !== ''
		? (int)$_POST['restecg']
		: null;

	$thalach = ($_POST['thalach'] ?? '') !== ''
		? (float)$_POST['thalach']
		: null;

	$exang = ($_POST['exang'] ?? '') !== ''
		? (int)$_POST['exang']
		: null;

	$oldpeak = ($_POST['oldpeak'] ?? '') !== ''
		? (float)$_POST['oldpeak']
		: null;

	$slope = ($_POST['slope'] ?? '') !== ''
		? (int)$_POST['slope']
		: null;

	$ca = ($_POST['ca'] ?? '') !== ''
		? (int)$_POST['ca']
		: null;

	$thal = ($_POST['thal'] ?? '') !== ''
		? (int)$_POST['thal']
		: null;

	$symptoms = trim($_POST['symptoms'] ?? '');
	$medical_history = trim($_POST['medical_history'] ?? '');


	/*
    |--------------------------------------------------------------------------
    | Update profile
    |--------------------------------------------------------------------------
    */

	$stmt = $conn->prepare("
        UPDATE patient_profiles
        SET
            age = ?,
            gender = ?,
            sex = ?,
            cp = ?,
            trestbps = ?,
            chol = ?,
            fbs = ?,
            restecg = ?,
            thalach = ?,
            exang = ?,
            oldpeak = ?,
            slope = ?,
            ca = ?,
            thal = ?,
            symptoms = ?,
            medical_history = ?
        WHERE patient_id = ?
    ");

	$stmt->bind_param(
		"isiiddiididiisssi",
		$age,
		$gender,
		$sex,
		$cp,
		$trestbps,
		$chol,
		$fbs,
		$restecg,
		$thalach,
		$exang,
		$oldpeak,
		$slope,
		$ca,
		$thal,
		$symptoms,
		$medical_history,
		$patientId
	);

	if ($stmt->execute()) {
		header("Location: patient_profile.php?id=" . $patientId . "&updated=1");
		exit();
	} else {
		$error = "Failed to update patient information.";
	}

	$stmt->close();
}


/*
|--------------------------------------------------------------------------
| Fetch patient profile
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT *
    FROM patient_profiles
    WHERE patient_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $patientId);
$stmt->execute();

$result = $stmt->get_result();
$profile = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Fetch heart disease prediction history
|--------------------------------------------------------------------------
*/

$predictionStmt = $conn->prepare("
    SELECT
        hp.id,
        hp.prediction,
        hp.probability,
        hp.diagnosis,
        hp.created_at,
        d.name AS doctor_name
    FROM heart_disease_predictions hp
    JOIN doctors d ON hp.doctor_id = d.id
    WHERE hp.patient_id = ?
    ORDER BY hp.created_at DESC
");

$predictionStmt->bind_param("i", $patientId);
$predictionStmt->execute();

$predictionHistory = $predictionStmt->get_result();

?>

<!DOCTYPE html>
<html>

<head>

	<title>Patient Profile</title>

	<!-- <link
		href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
		rel="stylesheet"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">

</head>


<body class="bg-light">

	<div class="container py-5">

		<h2 class="mb-4">
			Patient Profile
		</h2>


		<?php if (isset($_GET['updated'])): ?>

			<div class="alert alert-success">
				Patient information updated successfully.
			</div>

		<?php endif; ?>


		<?php if (isset($error)): ?>

			<div class="alert alert-danger">
				<?= htmlspecialchars($error) ?>
			</div>

		<?php endif; ?>


		<?php if ($profile): ?>

			<form method="POST">


				<!-- Basic Information -->

				<div class="card shadow-sm mb-4">

					<div class="card-header">
						<h5 class="mb-0">
							Basic Information
						</h5>
					</div>

					<div class="card-body">

						<div class="row">

							<!-- Patient ID -->

							<div class="col-md-6 mb-3">

								<label class="form-label">
									Patient ID
								</label>

								<input
									type="text"
									class="form-control"
									value="<?= htmlspecialchars($profile['patient_id']) ?>"
									disabled>

							</div>

						</div>

					</div>

				</div>


				<!-- Symptoms / History -->

				<div class="card shadow-sm mb-4">

					<div class="card-header">

						<h5 class="mb-0">
							Symptoms & Medical History
						</h5>

					</div>


					<div class="card-body">

						<div class="mb-3">

							<label class="form-label">
								Symptoms
							</label>

							<textarea
								name="symptoms"
								class="form-control"
								rows="3"><?= htmlspecialchars($profile['symptoms'] ?? '') ?></textarea>

						</div>


						<div class="mb-3">

							<label class="form-label">
								Medical History
							</label>

							<textarea
								name="medical_history"
								class="form-control"
								rows="3"><?= htmlspecialchars($profile['medical_history'] ?? '') ?></textarea>

						</div>

					</div>

				</div>




								<!-- Heart Disease Information -->

				<div class="card shadow-sm mb-4">

					<div class="card-header">

						<h5 class="mb-0">
							Heart Disease Clinical Information
						</h5>

					</div>


					<div class="card-body">

						<div class="row">


							<!-- Age: ML input feature -->
							<div class="col-md-6 mb-3">
								<label class="form-label">Age</label>
								<input type="number" name="age" class="form-control" min="1" max="120" value="<?= htmlspecialchars($profile['age'] ?? '') ?>" required>
							</div>

			<!-- Sex (ML): prefilled from the database and editable by the doctor -->
			<div class="col-md-6 mb-3">
				<label class="form-label" for="gender">Sex (ML)</label>
				<select name="gender" id="gender" class="form-select" required>
					<option value="">-- Select --</option>
					<option value="Male" <?= (strcasecmp(trim((string)($profile['gender'] ?? '')), 'Male') === 0) ? 'selected' : '' ?>>Male (1)</option>
					<option value="Female" <?= (strcasecmp(trim((string)($profile['gender'] ?? '')), 'Female') === 0) ? 'selected' : '' ?>>Female (0)</option>
				</select>
			</div>
<!-- CP -->

							<div class="col-md-6 mb-3">

								<label class="form-label">
									Chest Pain Type (cp)
								</label>

								<select
									name="cp"
									class="form-select">

									<option value="">
										-- Not Provided --
									</option>

									<?php for ($i = 0; $i <= 3; $i++): ?>

										<option
											value="<?= $i ?>"
											<?= (($profile['cp'] ?? '') !== null && $profile['cp'] == $i) ? 'selected' : '' ?>>
											<?= $i ?>
										</option>

									<?php endfor; ?>

								</select>

							</div>


							<!-- BP -->

							<div class="col-md-6 mb-3">

								<label class="form-label">
									Resting Blood Pressure
								</label>

								<input
									type="number"
									name="trestbps"
									class="form-control"
									value="<?= htmlspecialchars($profile['trestbps'] ?? '') ?>"
									placeholder="e.g. 125">

							</div>


							<!-- Cholesterol -->

							<div class="col-md-6 mb-3">

								<label class="form-label">
									Cholesterol
								</label>

								<input
									type="number"
									name="chol"
									class="form-control"
									value="<?= htmlspecialchars($profile['chol'] ?? '') ?>"
									placeholder="e.g. 212">

							</div>


							<!-- FBS -->

							<div class="col-md-6 mb-3">

								<label class="form-label">
									Fasting Blood Sugar &gt; 120 mg/dl
								</label>

								<select
									name="fbs"
									class="form-select">

									<option value="">
										-- Not Provided --
									</option>

									<option
										value="0"
										<?= (($profile['fbs'] ?? '') !== null && $profile['fbs'] == 0) ? 'selected' : '' ?>>
										No (0)
									</option>

									<option
										value="1"
										<?= (($profile['fbs'] ?? '') !== null && $profile['fbs'] == 1) ? 'selected' : '' ?>>
										Yes (1)
									</option>

								</select>

							</div>


							<!-- Rest ECG -->

							<div class="col-md-6 mb-3">

								<label class="form-label">
									Resting ECG (restecg)
								</label>

								<select
									name="restecg"
									class="form-select">

									<option value="">
										-- Not Provided --
									</option>

									<?php for ($i = 0; $i <= 2; $i++): ?>

										<option
											value="<?= $i ?>"
											<?= (($profile['restecg'] ?? '') !== null && $profile['restecg'] == $i) ? 'selected' : '' ?>>
											<?= $i ?>
										</option>

									<?php endfor; ?>

								</select>

							</div>


							<!-- Thalach -->

							<div class="col-md-6 mb-3">

								<label class="form-label">
									Maximum Heart Rate (thalach)
								</label>

								<input
									type="number"
									name="thalach"
									class="form-control"
									value="<?= htmlspecialchars($profile['thalach'] ?? '') ?>"
									placeholder="e.g. 168">

							</div>


							<!-- Exang -->

							<div class="col-md-6 mb-3">

								<label class="form-label">
									Exercise Induced Angina
								</label>

								<select
									name="exang"
									class="form-select">

									<option value="">
										-- Not Provided --
									</option>

									<option
										value="0"
										<?= (($profile['exang'] ?? '') !== null && $profile['exang'] == 0) ? 'selected' : '' ?>>
										No (0)
									</option>

									<option
										value="1"
										<?= (($profile['exang'] ?? '') !== null && $profile['exang'] == 1) ? 'selected' : '' ?>>
										Yes (1)
									</option>

								</select>

							</div>


							<!-- Oldpeak -->

							<div class="col-md-6 mb-3">

								<label class="form-label">
									Oldpeak (ST Depression)
								</label>

								<input
									type="number"
									name="oldpeak"
									class="form-control"
									step="0.01"
									value="<?= htmlspecialchars($profile['oldpeak'] ?? '') ?>"
									placeholder="e.g. 1.0">

							</div>


							<!-- Slope -->

							<div class="col-md-6 mb-3">

								<label class="form-label">
									Slope
								</label>

								<select
									name="slope"
									class="form-select">

									<option value="">
										-- Not Provided --
									</option>

									<?php for ($i = 0; $i <= 2; $i++): ?>

										<option
											value="<?= $i ?>"
											<?= (($profile['slope'] ?? '') !== null && $profile['slope'] == $i) ? 'selected' : '' ?>>
											<?= $i ?>
										</option>

									<?php endfor; ?>

								</select>

							</div>


							<!-- CA -->

							<div class="col-md-6 mb-3">

								<label class="form-label">
									Number of Major Vessels (ca)
								</label>

								<select
									name="ca"
									class="form-select">

									<option value="">
										-- Not Provided --
									</option>

									<?php for ($i = 0; $i <= 4; $i++): ?>

										<option
											value="<?= $i ?>"
											<?= (($profile['ca'] ?? '') !== null && $profile['ca'] == $i) ? 'selected' : '' ?>>
											<?= $i ?>
										</option>

									<?php endfor; ?>

								</select>

							</div>


							<!-- Thal -->

							<div class="col-md-6 mb-3">

								<label class="form-label">
									Thal
								</label>

								<select
									name="thal"
									class="form-select">

									<option value="">
										-- Not Provided --
									</option>

									<?php for ($i = 0; $i <= 3; $i++): ?>

										<option
											value="<?= $i ?>"
											<?= (($profile['thal'] ?? '') !== null && $profile['thal'] == $i) ? 'selected' : '' ?>>
											<?= $i ?>
										</option>

									<?php endfor; ?>

								</select>

							</div>

						</div>

					</div>

				</div>


				<!-- Buttons -->

				<div class="d-flex gap-2">

					<button
						type="submit"
						class="btn btn-primary">
						Save Patient Information
					</button>

					<a
						href="predict_heart_disease.php?id=<?= $patientId ?>"
						class="btn btn-danger">
						❤️ Predict Heart Disease
					</a>

					<a
						href="doctor_dashboard.php"
						class="btn btn-secondary">
						Back to Dashboard
					</a>

				</div>

			</form>


			<!-- ==========================================================
			     HEART DISEASE PREDICTION HISTORY
			     ========================================================== -->

			<div class="card shadow-sm mt-4 mb-4">

				<div class="card-header">
					<h5 class="mb-0">
						❤️ Heart Disease Prediction History
					</h5>
				</div>

				<div class="card-body">

					<?php if ($predictionHistory->num_rows > 0): ?>

						<div class="table-responsive">

							<table class="table table-bordered table-striped align-middle">

								<thead class="table-light">

									<tr>
										<th>Date</th>
										<th>Doctor</th>
										<th>Prediction</th>
										<th>Probability</th>
										<th>Diagnosis</th>
									</tr>

								</thead>

								<tbody>

									<?php while ($prediction = $predictionHistory->fetch_assoc()): ?>

										<tr>

											<td>
												<?= htmlspecialchars($prediction['created_at']) ?>
											</td>

											<td>
												<?= htmlspecialchars($prediction['doctor_name']) ?>
											</td>

											<td>

												<?php if ($prediction['prediction'] == 1): ?>

													<span class="badge bg-danger">
														Positive
													</span>

												<?php else: ?>

													<span class="badge bg-success">
														Negative
													</span>

												<?php endif; ?>

											</td>

											<td>
												<strong>
													<?= number_format(
														$prediction['probability'] * 100,
														2
													) ?>%
												</strong>
											</td>

											<td>
												<?= htmlspecialchars($prediction['diagnosis']) ?>
											</td>

										</tr>

									<?php endwhile; ?>

								</tbody>

							</table>

						</div>

					<?php else: ?>

						<div class="alert alert-info mb-0">
							No heart disease predictions have been made
							for this patient yet.
						</div>

					<?php endif; ?>

				</div>

			</div>


		<?php else: ?>

			<div class="alert alert-warning">

				No profile found for this patient.

			</div>

			<a
				href="doctor_dashboard.php"
				class="btn btn-secondary">
				Back to Dashboard
			</a>

		<?php endif; ?>

	</div>

</body>

</html>

<?php

/*
|--------------------------------------------------------------------------
| Close database resources
|--------------------------------------------------------------------------
*/

$predictionStmt->close();
$conn->close();

?>