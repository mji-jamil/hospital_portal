<?php

session_start();
require_once 'config.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: login.php");
    exit();
}

$patient_id = (int) $_SESSION['patient_id'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: patient_dashboard.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| Read submitted values
|--------------------------------------------------------------------------
*/

$age = ($_POST['age'] ?? '') !== ''
    ? (int) $_POST['age']
    : null;

$gender = ($_POST['gender'] ?? '') !== ''
    ? $_POST['gender']
    : null;

$sex = ($_POST['sex'] ?? '') !== ''
    ? (int) $_POST['sex']
    : null;

$cp = ($_POST['cp'] ?? '') !== ''
    ? (int) $_POST['cp']
    : null;

$trestbps = ($_POST['trestbps'] ?? '') !== ''
    ? (float) $_POST['trestbps']
    : null;

$chol = ($_POST['chol'] ?? '') !== ''
    ? (float) $_POST['chol']
    : null;

$fbs = ($_POST['fbs'] ?? '') !== ''
    ? (int) $_POST['fbs']
    : null;

$restecg = ($_POST['restecg'] ?? '') !== ''
    ? (int) $_POST['restecg']
    : null;

$thalach = ($_POST['thalach'] ?? '') !== ''
    ? (float) $_POST['thalach']
    : null;

$exang = ($_POST['exang'] ?? '') !== ''
    ? (int) $_POST['exang']
    : null;

$oldpeak = ($_POST['oldpeak'] ?? '') !== ''
    ? (float) $_POST['oldpeak']
    : null;

$slope = ($_POST['slope'] ?? '') !== ''
    ? (int) $_POST['slope']
    : null;

$ca = ($_POST['ca'] ?? '') !== ''
    ? (int) $_POST['ca']
    : null;

$thal = ($_POST['thal'] ?? '') !== ''
    ? (int) $_POST['thal']
    : null;

$symptoms = trim($_POST['symptoms'] ?? '');
$medical_history = trim($_POST['medical_history'] ?? '');


/*
|--------------------------------------------------------------------------
| Check whether this patient already has a profile
|--------------------------------------------------------------------------
*/

$check = $pdo->prepare("
    SELECT id
    FROM patient_profiles
    WHERE patient_id = ?
    LIMIT 1
");

$check->execute([$patient_id]);

$profile = $check->fetch();


/*
|--------------------------------------------------------------------------
| UPDATE or INSERT
|--------------------------------------------------------------------------
*/

if ($profile) {

    $stmt = $pdo->prepare("
        UPDATE patient_profiles
        SET
            age = :age,
            gender = :gender,
            sex = :sex,
            cp = :cp,
            trestbps = :trestbps,
            chol = :chol,
            fbs = :fbs,
            restecg = :restecg,
            thalach = :thalach,
            exang = :exang,
            oldpeak = :oldpeak,
            slope = :slope,
            ca = :ca,
            thal = :thal,
            symptoms = :symptoms,
            medical_history = :medical_history
        WHERE patient_id = :patient_id
    ");

} else {

    $stmt = $pdo->prepare("
        INSERT INTO patient_profiles
        (
            patient_id,
            age,
            gender,
            sex,
            cp,
            trestbps,
            chol,
            fbs,
            restecg,
            thalach,
            exang,
            oldpeak,
            slope,
            ca,
            thal,
            symptoms,
            medical_history
        )
        VALUES
        (
            :patient_id,
            :age,
            :gender,
            :sex,
            :cp,
            :trestbps,
            :chol,
            :fbs,
            :restecg,
            :thalach,
            :exang,
            :oldpeak,
            :slope,
            :ca,
            :thal,
            :symptoms,
            :medical_history
        )
    ");
}


/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

$stmt->execute([
    ':patient_id' => $patient_id,
    ':age' => $age,
    ':gender' => $gender,
    ':sex' => $sex,
    ':cp' => $cp,
    ':trestbps' => $trestbps,
    ':chol' => $chol,
    ':fbs' => $fbs,
    ':restecg' => $restecg,
    ':thalach' => $thalach,
    ':exang' => $exang,
    ':oldpeak' => $oldpeak,
    ':slope' => $slope,
    ':ca' => $ca,
    ':thal' => $thal,
    ':symptoms' => $symptoms,
    ':medical_history' => $medical_history
]);


/*
|--------------------------------------------------------------------------
| Return to patient dashboard
|--------------------------------------------------------------------------
*/

header("Location: patient_dashboard.php?profile=updated");
exit();

?>