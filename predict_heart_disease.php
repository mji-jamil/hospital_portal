<?php

session_start();

/*
|--------------------------------------------------------------------------
| Only doctors can access prediction
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['doctor_id'])) {
	header("Location: doctor_login.php");
	exit();
}


/*
|--------------------------------------------------------------------------
| Database
|--------------------------------------------------------------------------
| Uses the centralized config.php
|--------------------------------------------------------------------------
*/

require_once 'config.php';


/*
|--------------------------------------------------------------------------
| Patient ID
|--------------------------------------------------------------------------
*/

$patientId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($patientId <= 0) {
	die("Invalid patient ID.");
}


/*
|--------------------------------------------------------------------------
| Get patient's clinical information
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT *
    FROM patient_profiles
    WHERE patient_id = ?
    LIMIT 1
");

$stmt->execute([$patientId]);

$profile = $stmt->fetch();


/*
|--------------------------------------------------------------------------
| Check profile
|--------------------------------------------------------------------------
*/

if (!$profile) {
	die("Patient profile not found.");
}


/*
|--------------------------------------------------------------------------
| Check all 13 ML fields
|--------------------------------------------------------------------------
*/

$requiredFields = [
	'age',
	'sex',
	'cp',
	'trestbps',
	'chol',
	'fbs',
	'restecg',
	'thalach',
	'exang',
	'oldpeak',
	'slope',
	'ca',
	'thal'
];

$missingFields = [];

foreach ($requiredFields as $field) {

	if (
		!isset($profile[$field]) ||
		$profile[$field] === '' ||
		$profile[$field] === null
	) {
		$missingFields[] = $field;
	}
}


/*
|--------------------------------------------------------------------------
| Stop if information is missing
|--------------------------------------------------------------------------
*/

if (!empty($missingFields)) {
?>

	<!DOCTYPE html>

	<html>

	<head>

		<meta charset="UTF-8">

		<meta name="viewport" content="width=device-width, initial-scale=1.0">

		<title>Heart Disease Prediction</title>

		<link href="assets/css/bootstrap.min.css" rel="stylesheet">

		<link href="assets/icons/font/bootstrap-icons.css" rel="stylesheet">

	</head>

	<body class="bg-light">

		<div class="container py-5">

			<div class="card shadow-sm">

				<div class="card-body">

					<div class="alert alert-warning mb-4">

						<h4 class="alert-heading">
							⚠️ Information Missing
						</h4>

						<p>
							The following information is required before
							heart disease prediction:
						</p>

						<ul class="mb-3">

							<?php foreach ($missingFields as $field): ?>

								<li>
									<?= htmlspecialchars($field) ?>
								</li>

							<?php endforeach; ?>

						</ul>

						<p class="mb-0">
							Please update the patient's clinical information
							first.
						</p>

					</div>

					<a
						href="patient_profile.php?id=<?= $patientId ?>"
						class="btn btn-primary">

						← Back to Patient Profile

					</a>

				</div>

			</div>

		</div>

		<script src="assets/js/bootstrap.bundle.min.js"></script>

	</body>

	</html>

<?php

	exit();
}


/*
|--------------------------------------------------------------------------
| Prepare data for FastAPI
|--------------------------------------------------------------------------
*/

$inputData = [

	"age" => (int) $profile['age'],

	"sex" => (int) $profile['sex'],

	"cp" => (int) $profile['cp'],

	"trestbps" => (float) $profile['trestbps'],

	"chol" => (float) $profile['chol'],

	"fbs" => (int) $profile['fbs'],

	"restecg" => (int) $profile['restecg'],

	"thalach" => (float) $profile['thalach'],

	"exang" => (int) $profile['exang'],

	"oldpeak" => (float) $profile['oldpeak'],

	"slope" => (int) $profile['slope'],

	"ca" => (int) $profile['ca'],

	"thal" => (int) $profile['thal']

];


/*
|--------------------------------------------------------------------------
| Call FastAPI
|--------------------------------------------------------------------------
| LOCAL DEVELOPMENT
|--------------------------------------------------------------------------
*/

$apiUrl = "http://127.0.0.1:8000/predict-heart-disease";

$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

curl_setopt($ch, CURLOPT_POST, true);

curl_setopt(
	$ch,
	CURLOPT_HTTPHEADER,
	[
		"Content-Type: application/json"
	]
);

curl_setopt(
	$ch,
	CURLOPT_POSTFIELDS,
	json_encode($inputData)
);

$response = curl_exec($ch);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

$curlError = curl_error($ch);

curl_close($ch);


/*
|--------------------------------------------------------------------------
| API error
|--------------------------------------------------------------------------
*/

if ($response === false || $httpCode !== 200) {

	die("Could not connect to the Heart Disease Prediction API."
		. "<br><br>"
		. "HTTP Code: "
		. htmlspecialchars((string) $httpCode)
		. "<br>"
		. "Error: "
		. htmlspecialchars($curlError));
}


/*
|--------------------------------------------------------------------------
| Decode result
|--------------------------------------------------------------------------
*/

$result = json_decode($response, true);


/*
|--------------------------------------------------------------------------
| Validate API response
|--------------------------------------------------------------------------
*/

if (
	!is_array($result) ||
	!isset(
		$result['prediction'],
		$result['probability'],
		$result['diagnosis']
	)
) {

	die("Invalid response from prediction API.");
}


/*
|--------------------------------------------------------------------------
| Get prediction result
|--------------------------------------------------------------------------
*/

$prediction = (int) $result['prediction'];

$probability = (float) $result['probability'];

$diagnosis = $result['diagnosis'];


/*
|--------------------------------------------------------------------------
| Save prediction result to database
|--------------------------------------------------------------------------
*/

$doctorId = (int) $_SESSION['doctor_id'];

$insert = $pdo->prepare("
    INSERT INTO heart_disease_predictions
    (
        patient_id,
        doctor_id,
        prediction,
        probability,
        diagnosis
    )
    VALUES (?, ?, ?, ?, ?)
");

try {

	$insert->execute([
		$patientId,
		$doctorId,
		$prediction,
		$probability,
		$diagnosis
	]);
} catch (PDOException $e) {

	die("Prediction was generated but could not be saved.");
}

?>

<!DOCTYPE html>

<html>

<head>

	<meta charset="UTF-8">

	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>Heart Disease Prediction</title>

	<link href="assets/css/bootstrap.min.css" rel="stylesheet">

	<link href="assets/icons/font/bootstrap-icons.css" rel="stylesheet">

</head>


<body class="bg-light">

	<div class="container py-5">

		<div class="row justify-content-center">

			<div class="col-lg-8">

				<h2 class="mb-4 text-center">

					❤️ Heart Disease Prediction

				</h2>


				<!-- Patient Information -->

				<div class="card shadow-sm mb-4">

					<div class="card-body">

						<div class="row">

							<div class="col-md-6">

								<strong>
									Patient ID:
								</strong>

								<?= htmlspecialchars($patientId) ?>

							</div>


							<div class="col-md-6">

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


				<!-- Prediction Result -->

				<div class="card shadow-sm">

					<div class="card-body text-center p-4">

						<h5 class="text-muted mb-3">

							Heart Disease Probability

						</h5>


						<h1 class="display-3 fw-bold mb-4">

							<?= number_format(
								$probability * 100,
								2
							) ?>%

						</h1>


						<?php if ($prediction == 1): ?>

							<div class="alert alert-danger py-4">

								<h3 class="mb-3">

									⚠️
									<?= htmlspecialchars($diagnosis) ?>

								</h3>

								<p class="mb-0">

									The machine-learning model predicts a
									higher likelihood of heart disease.

								</p>

							</div>

						<?php else: ?>

							<div class="alert alert-success py-4">

								<h3 class="mb-3">

									✅
									<?= htmlspecialchars($diagnosis) ?>

								</h3>

								<p class="mb-0">

									The machine-learning model predicts a
									lower likelihood of heart disease.

								</p>

							</div>

						<?php endif; ?>


						<!-- Prediction Details -->

						<div class="card bg-light border-0 mt-4">

							<div class="card-body text-start">

								<h5 class="mb-3">

									Prediction Details

								</h5>


								<div class="row">

									<div class="col-md-6 mb-2">

										<strong>
											Prediction:
										</strong>

										<?php if ($prediction == 1): ?>

											<span class="badge bg-danger">

												Positive

											</span>

										<?php else: ?>

											<span class="badge bg-success">

												Negative

											</span>

										<?php endif; ?>

									</div>


									<div class="col-md-6 mb-2">

										<strong>
											Probability:
										</strong>

										<?= number_format(
											$probability * 100,
											2
										) ?>%

									</div>


									<div class="col-md-6 mb-2">

										<strong>
											Patient ID:
										</strong>

										<?= htmlspecialchars($patientId) ?>

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


						<!-- Important Notice -->

						<div class="alert alert-warning text-start mt-4">

							<h6>

								⚠️ Important Notice

							</h6>

							<p class="mb-0">

								This result is generated by a
								machine-learning model and should not be
								considered a medical diagnosis. The final
								clinical decision must be made by the doctor
								based on the patient's complete medical
								evaluation.

							</p>

						</div>


						<!-- Buttons -->

						<div class="d-flex justify-content-center gap-2 mt-4">

							<a
								href="patient_profile.php?id=<?= $patientId ?>"
								class="btn btn-primary">

								← Back to Patient Profile

							</a>


							<a
								href="doctor_dashboard.php"
								class="btn btn-secondary">

								Back to Dashboard

							</a>

						</div>

					</div>

				</div>

			</div>

		</div>

	</div>


	<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>

</html>