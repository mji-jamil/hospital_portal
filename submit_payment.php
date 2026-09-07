<?php
require 'config.php';
require 'vendor/autoload.php';
session_start();

use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$bill_id = $_POST['bill_id'];
	$payer_name = $_POST['payer_name'];
	$payment_method = $_POST['payment_method'];
	$payment_notes = $_POST['payment_notes'] ?? '';

	// Validate bill
	$stmt = $pdo->prepare("SELECT * FROM bills WHERE id = ? AND patient_id = ?");
	$stmt->execute([$bill_id, $_SESSION['patient_id']]);
	$bill = $stmt->fetch();

	if (!$bill) {
		header("Location: patient_dashboard.php?error=Invalid+bill");
		exit;
	}

	// Mark bill as paid
	$update = $pdo->prepare("UPDATE bills SET status = 'Paid' WHERE id = ?");
	$update->execute([$bill_id]);

	// Generate PDF receipt
	$html = '
    <style>
      body { font-family: DejaVu Sans, sans-serif; }
      h2 { color: #2c3e50; }
      .label { font-weight: bold; }
    </style>
    <h2>Payment Receipt</h2>
    <p><span class="label">Bill ID:</span> ' . $bill['id'] . '</p>
    <p><span class="label">Patient ID:</span> ' . $bill['patient_id'] . '</p>
    <p><span class="label">Amount Paid:</span> ৳' . number_format($bill['amount'], 2) . '</p>
    <p><span class="label">Paid By:</span> ' . htmlspecialchars($payer_name) . '</p>
    <p><span class="label">Payment Method:</span> ' . htmlspecialchars($payment_method) . '</p>
    <p><span class="label">Notes:</span> ' . nl2br(htmlspecialchars($payment_notes)) . '</p>
    <p><span class="label">Date:</span> ' . date('Y-m-d H:i:s') . '</p>
    ';

	$dompdf = new Dompdf(new Options(['isRemoteEnabled' => true]));
	$dompdf->loadHtml($html);
	$dompdf->setPaper('A4', 'portrait');
	$dompdf->render();

	// Save PDF to file
	$filename = "receipts/receipt_bill_" . $bill['id'] . ".pdf";
	file_put_contents($filename, $dompdf->output());

	// Show confirmation page
?>
	<!DOCTYPE html>
	<html>

	<head>
		<title>Payment Successful</title>
		<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
		<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	</head>

	<body class="bg-light">
		<div class="container py-5">
			<div class="alert alert-success">
				✅ Your payment was successfully submitted!
			</div>
			<p>You can now download your receipt:</p>
			<a href="<?= $filename ?>" class="btn btn-primary" download>Download Receipt (PDF)</a>
			<a href="patient_dashboard.php" class="btn btn-secondary ms-2">Back to Dashboard</a>
		</div>
	</body>

	</html>
<?php
	exit;
}
