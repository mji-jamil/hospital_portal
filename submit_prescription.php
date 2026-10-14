<?php

require 'config.php';
require 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

session_start();


/*
|--------------------------------------------------------------------------
| Only doctors can submit prescriptions
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['doctor_id'])) {
    die("Unauthorized access.");
}


/*
|--------------------------------------------------------------------------
| Only POST requests are allowed
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: doctor_dashboard.php?error=Invalid+request");
    exit;
}


/*
|--------------------------------------------------------------------------
| Get submitted data
|--------------------------------------------------------------------------
*/

$appointment_id = isset($_POST['appointment_id'])
    ? (int) $_POST['appointment_id']
    : 0;

$patient_id = isset($_POST['patient_id'])
    ? (int) $_POST['patient_id']
    : 0;

$doctor_id = (int) $_SESSION['doctor_id'];

$diagnosis = trim($_POST['diagnosis'] ?? '');

$advice = trim($_POST['advice'] ?? '');


/*
|--------------------------------------------------------------------------
| Basic validation
|--------------------------------------------------------------------------
*/

if ($appointment_id <= 0 || $patient_id <= 0) {
    die("Invalid appointment or patient.");
}

if ($diagnosis === '') {
    die("Diagnosis is required.");
}


/*
|--------------------------------------------------------------------------
| Get medicine arrays
|--------------------------------------------------------------------------
*/

$medicine_names = $_POST['medicine_name'] ?? [];
$strengths = $_POST['strength'] ?? [];
$dosages = $_POST['dosage'] ?? [];
$frequencies = $_POST['frequency'] ?? [];
$meal_timings = $_POST['meal_timing'] ?? [];
$durations = $_POST['duration'] ?? [];
$quantities = $_POST['quantity'] ?? [];
$instructions = $_POST['instructions'] ?? [];


/*
|--------------------------------------------------------------------------
| Check at least one medicine
|--------------------------------------------------------------------------
*/

if (!is_array($medicine_names) || count($medicine_names) === 0) {
    die("At least one medicine is required.");
}


/*
|--------------------------------------------------------------------------
| Verify appointment belongs to this doctor and patient
|--------------------------------------------------------------------------
*/

$appointmentStmt = $pdo->prepare("
    SELECT id, patient_id, doctor_id, appointment_date
    FROM appointments
    WHERE id = ?
      AND patient_id = ?
      AND doctor_id = ?
    LIMIT 1
");

$appointmentStmt->execute([
    $appointment_id,
    $patient_id,
    $doctor_id
]);

$appointment = $appointmentStmt->fetch();

if (!$appointment) {
    die("Invalid appointment or unauthorized access.");
}


/*
|--------------------------------------------------------------------------
| Prepare medicine data
|--------------------------------------------------------------------------
*/

$medicines = [];

foreach ($medicine_names as $index => $medicineName) {

    $medicineName = trim($medicineName);

    /*
    |--------------------------------------------------------------
    | Medicine name is required
    |--------------------------------------------------------------
    */

    if ($medicineName === '') {
        die("Medicine name is required for every medicine.");
    }


    $strength = trim($strengths[$index] ?? '');
    $dosage = trim($dosages[$index] ?? '');
    $frequency = trim($frequencies[$index] ?? '');
    $mealTiming = trim($meal_timings[$index] ?? '');
    $duration = trim($durations[$index] ?? '');
    $quantity = trim($quantities[$index] ?? '');
    $instruction = trim($instructions[$index] ?? '');


    $medicines[] = [
        'medicine_name' => $medicineName,
        'strength' => $strength,
        'dosage' => $dosage,
        'frequency' => $frequency,
        'meal_timing' => $mealTiming,
        'duration' => $duration,
        'quantity' => $quantity,
        'instructions' => $instruction
    ];
}


/*
|--------------------------------------------------------------------------
| Create old-format medication summary
|--------------------------------------------------------------------------
|
| We keep the existing prescriptions.medications field populated
| so old prescription pages continue to work.
|
*/

$medicationSummary = '';

foreach ($medicines as $index => $medicine) {

    $medicationSummary .= ($index + 1) . ". "
        . $medicine['medicine_name'];

    if ($medicine['strength'] !== '') {
        $medicationSummary .= " " . $medicine['strength'];
    }

    if ($medicine['dosage'] !== '') {
        $medicationSummary .= " - " . $medicine['dosage'];
    }

    if ($medicine['frequency'] !== '') {
        $medicationSummary .= " - " . $medicine['frequency'];
    }

    if ($medicine['meal_timing'] !== '') {
        $medicationSummary .= " - " . $medicine['meal_timing'];
    }

    if ($medicine['duration'] !== '') {
        $medicationSummary .= " - " . $medicine['duration'];
    }

    if ($medicine['quantity'] !== '') {
        $medicationSummary .= " - Qty: " . $medicine['quantity'];
    }

    if ($medicine['instructions'] !== '') {
        $medicationSummary .= " - " . $medicine['instructions'];
    }

    $medicationSummary .= "\n";
}


/*
|--------------------------------------------------------------------------
| Database transaction
|--------------------------------------------------------------------------
*/

try {

    $pdo->beginTransaction();


    /*
    |--------------------------------------------------------------------------
    | Insert main prescription
    |--------------------------------------------------------------------------
    */

    $prescriptionStmt = $pdo->prepare("
        INSERT INTO prescriptions
        (
            appointment_id,
            patient_id,
            doctor_id,
            diagnosis,
            medications,
            advice
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $prescriptionStmt->execute([
        $appointment_id,
        $patient_id,
        $doctor_id,
        $diagnosis,
        $medicationSummary,
        $advice
    ]);


    /*
    |--------------------------------------------------------------------------
    | Get newly created prescription ID
    |--------------------------------------------------------------------------
    */

    $prescription_id = $pdo->lastInsertId();


    /*
    |--------------------------------------------------------------------------
    | Insert individual medicines
    |--------------------------------------------------------------------------
    */

    $medicineStmt = $pdo->prepare("
        INSERT INTO prescription_items
        (
            prescription_id,
            medicine_name,
            strength,
            dosage,
            frequency,
            meal_timing,
            duration,
            quantity,
            instructions
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");


    foreach ($medicines as $medicine) {

        $medicineStmt->execute([
            $prescription_id,
            $medicine['medicine_name'],
            $medicine['strength'],
            $medicine['dosage'],
            $medicine['frequency'],
            $medicine['meal_timing'],
            $medicine['duration'],
            $medicine['quantity'],
            $medicine['instructions']
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | Fetch doctor and patient information
    |--------------------------------------------------------------------------
    */

    $infoStmt = $pdo->prepare("
        SELECT
            d.name AS doctor_name,
            p.name AS patient_name,
            p.email AS patient_email,
            a.appointment_date
        FROM doctors d
        JOIN patients p
            ON p.id = ?
        JOIN appointments a
            ON a.id = ?
        WHERE d.id = ?
        LIMIT 1
    ");

    $infoStmt->execute([
        $patient_id,
        $appointment_id,
        $doctor_id
    ]);

    $data = $infoStmt->fetch();


    if (!$data) {
        throw new Exception("Could not retrieve prescription information.");
    }


    /*
    |--------------------------------------------------------------------------
    | Prepare safe values for PDF
    |--------------------------------------------------------------------------
    */

    $doctorName = htmlspecialchars(
        $data['doctor_name'],
        ENT_QUOTES,
        'UTF-8'
    );

    $patientName = htmlspecialchars(
        $data['patient_name'],
        ENT_QUOTES,
        'UTF-8'
    );

    $appointmentDate = htmlspecialchars(
        $data['appointment_date'],
        ENT_QUOTES,
        'UTF-8'
    );

    $safeDiagnosis = nl2br(
        htmlspecialchars(
            $diagnosis,
            ENT_QUOTES,
            'UTF-8'
        )
    );

    $safeAdvice = nl2br(
        htmlspecialchars(
            $advice,
            ENT_QUOTES,
            'UTF-8'
        )
    );


    /*
    |--------------------------------------------------------------------------
    | Build medicine table for PDF
    |--------------------------------------------------------------------------
    */

    $medicineRows = '';

    foreach ($medicines as $index => $medicine) {

        $name = htmlspecialchars(
            $medicine['medicine_name'],
            ENT_QUOTES,
            'UTF-8'
        );

        $strength = htmlspecialchars(
            $medicine['strength'],
            ENT_QUOTES,
            'UTF-8'
        );

        $dosage = htmlspecialchars(
            $medicine['dosage'],
            ENT_QUOTES,
            'UTF-8'
        );

        $frequency = htmlspecialchars(
            $medicine['frequency'],
            ENT_QUOTES,
            'UTF-8'
        );

        $mealTiming = htmlspecialchars(
            $medicine['meal_timing'],
            ENT_QUOTES,
            'UTF-8'
        );

        $duration = htmlspecialchars(
            $medicine['duration'],
            ENT_QUOTES,
            'UTF-8'
        );

        $quantity = htmlspecialchars(
            $medicine['quantity'],
            ENT_QUOTES,
            'UTF-8'
        );

        $instruction = htmlspecialchars(
            $medicine['instructions'],
            ENT_QUOTES,
            'UTF-8'
        );


        $medicineRows .= "
            <tr>
                <td>" . ($index + 1) . "</td>
                <td>
                    <strong>{$name}</strong>
                    " . ($strength !== '' ? "<br>{$strength}" : "") . "
                </td>
                <td>{$dosage}</td>
                <td>{$frequency}</td>
                <td>{$mealTiming}</td>
                <td>{$duration}</td>
                <td>{$quantity}</td>
            </tr>
        ";


        if ($instruction !== '') {

            $medicineRows .= "
                <tr class='instruction-row'>
                    <td></td>
                    <td colspan='6'>
                        <strong>Instructions:</strong>
                        {$instruction}
                    </td>
                </tr>
            ";
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Generate PDF
    |--------------------------------------------------------------------------
    */

    $html = "
    <!DOCTYPE html>

    <html>

    <head>

        <meta charset='UTF-8'>

        <style>

            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 12px;
                color: #333;
            }

            h1 {
                text-align: center;
                color: #2c3e50;
                margin-bottom: 5px;
            }

            .subtitle {
                text-align: center;
                color: #666;
                margin-bottom: 20px;
            }

            .info-table {
                width: 100%;
                margin-bottom: 20px;
            }

            .info-table td {
                padding: 5px;
                vertical-align: top;
            }

            .label {
                font-weight: bold;
            }

            hr {
                border: 0;
                border-top: 1px solid #ccc;
                margin: 15px 0;
            }

            .section-title {
                font-size: 15px;
                font-weight: bold;
                color: #2c3e50;
                margin-top: 15px;
                margin-bottom: 8px;
            }

            .medicine-table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 10px;
            }

            .medicine-table th {
                background-color: #eeeeee;
                font-weight: bold;
                border: 1px solid #999;
                padding: 7px;
                text-align: left;
            }

            .medicine-table td {
                border: 1px solid #999;
                padding: 7px;
                vertical-align: top;
            }

            .instruction-row td {
                background-color: #f8f8f8;
                font-size: 11px;
            }

            .advice {
                border: 1px solid #ccc;
                padding: 10px;
                margin-top: 5px;
            }

            .footer {
                margin-top: 30px;
                font-size: 10px;
                color: #777;
                text-align: center;
            }

        </style>

    </head>

    <body>

        <h1>Medical Prescription</h1>

        <div class='subtitle'>
            Clinic Management System
        </div>

        <table class='info-table'>

            <tr>

                <td>
                    <span class='label'>Doctor:</span>
                    Dr. {$doctorName}
                </td>

                <td>
                    <span class='label'>Patient:</span>
                    {$patientName}
                </td>

            </tr>

            <tr>

                <td>
                    <span class='label'>Patient ID:</span>
                    {$patient_id}
                </td>

                <td>
                    <span class='label'>Date:</span>
                    {$appointmentDate}
                </td>

            </tr>

        </table>


        <hr>


        <div class='section-title'>
            Diagnosis
        </div>

        <p>
            {$safeDiagnosis}
        </p>


        <div class='section-title'>
            Medicines
        </div>


        <table class='medicine-table'>

            <thead>

                <tr>

                    <th>#</th>
                    <th>Medicine</th>
                    <th>How Many</th>
                    <th>Frequency</th>
                    <th>When to Take</th>
                    <th>Duration</th>
                    <th>Quantity</th>

                </tr>

            </thead>

            <tbody>

                {$medicineRows}

            </tbody>

        </table>


        <div class='section-title'>
            Advice
        </div>

        <div class='advice'>

            " . (
                $safeAdvice !== ''
                    ? $safeAdvice
                    : "No additional advice."
            ) . "

        </div>


        <div class='footer'>

            This prescription was generated by Medipulse.

        </div>

    </body>

    </html>
    ";


    /*
    |--------------------------------------------------------------------------
    | Create PDF
    |--------------------------------------------------------------------------
    */

    $options = new Options();

    $options->set('isRemoteEnabled', true);


    $dompdf = new Dompdf($options);

    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();


    /*
    |--------------------------------------------------------------------------
    | Save PDF
    |--------------------------------------------------------------------------
    */

    $prescriptionDirectory = __DIR__ . "/prescriptions";


    if (!is_dir($prescriptionDirectory)) {

        if (!mkdir($prescriptionDirectory, 0777, true)) {
            throw new Exception("Could not create prescriptions directory.");
        }
    }


    $filename =
        "prescription_" . $appointment_id . ".pdf";


    $filePath =
        $prescriptionDirectory . "/" . $filename;


    $pdfOutput = $dompdf->output();


    if (file_put_contents($filePath, $pdfOutput) === false) {
        throw new Exception("Could not save prescription PDF.");
    }


    /*
    |--------------------------------------------------------------------------
    | Save PDF path in database
    |--------------------------------------------------------------------------
    */

    $databaseFilePath =
        "prescriptions/" . $filename;


    $updateFileStmt = $pdo->prepare("
        UPDATE prescriptions
        SET file_path = ?
        WHERE id = ?
    ");

    $updateFileStmt->execute([
        $databaseFilePath,
        $prescription_id
    ]);


    /*
    |--------------------------------------------------------------------------
    | Commit everything
    |--------------------------------------------------------------------------
    */

    $pdo->commit();


    /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

    header(
        "Location: doctor_dashboard.php?success="
        . urlencode("Prescription submitted successfully")
    );

    exit;


} catch (Throwable $e) {


    /*
    |--------------------------------------------------------------------------
    | Rollback if anything fails
    |--------------------------------------------------------------------------
    */

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    die(
        "Failed to save prescription."
        . "<br><br>"
        . htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
    );
}