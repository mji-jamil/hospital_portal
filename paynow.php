<?php
require 'config.php';
session_start();

if (!isset($_GET['bill_id'])) {
	header("Location: patient_dashboard.php?error=No+bill+selected");
	exit;
}

$bill_id = $_GET['bill_id'];

// Fetch bill info
$stmt = $pdo->prepare("
  SELECT b.*, a.appointment_date
  FROM bills b
  JOIN appointments a ON b.appointment_id = a.id
  WHERE b.id = ? AND b.patient_id = ?
");
$stmt->execute([$bill_id, $_SESSION['patient_id']]);
$bill = $stmt->fetch();

if (!$bill) {
	header("Location: patient_dashboard.php?error=Invalid+bill");
	exit;
}

$receiptPath = "receipts/receipt_bill_" . $bill['id'] . ".pdf";
?>

<!DOCTYPE html>
<html>

<head>
	<title>View Bill</title>
	<!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"> -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<style>
		.stamp {
			font-size: 2rem;
			font-weight: bold;
			color: white;
			padding: 0.5rem 1rem;
			display: inline-block;
			border-radius: 5px;
		}

		.paid {
			background-color: green;
		}

		.unpaid {
			background-color: red;
		}
	</style>
</head>

<body class="bg-light">
	<div class="container py-5">
		<h3>Bill #<?= $bill['id'] ?></h3>
		<p><strong>Appointment Date:</strong> <?= $bill['appointment_date'] ?></p>
		<p><strong>Amount:</strong> ৳<?= number_format($bill['amount'], 2) ?></p>
		<p><strong>Notes:</strong> <?= htmlspecialchars($bill['notes']) ?></p>
		<p><strong>Status:</strong>
			<?php if ($bill['status'] === 'Paid'): ?>
				<span class="stamp paid">PAID</span>
			<?php else: ?>
				<span class="stamp unpaid">UNPAID</span>
			<?php endif; ?>
		</p>

		<?php if ($bill['status'] === 'Unpaid'): ?>
			<hr>
			<h5>Submit Payment</h5>
			<form method="POST" action="submit_payment.php">
				<input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">

				<div class="mb-3">
					<label>Your Name:</label>
					<input type="text" name="payer_name" class="form-control" required>
				</div>

				<div class="mb-3">
					<label>Payment Method:</label>
					<select name="payment_method" class="form-select" required>
						<option value="">-- Select --</option>
						<option value="Cash">Cash</option>
						<option value="Card">Card</option>
						<option value="Mobile Payment">Mobile Payment</option>
					</select>
				</div>

				<div class="mb-3">
					<label>Payment Notes (optional):</label>
					<textarea name="payment_notes" class="form-control"></textarea>
				</div>

				<button type="submit" class="btn btn-success">Pay Now</button>
			</form>
		<?php else: ?>
			<hr>
			<p>Your payment has been received. You can download your receipt below:</p>
			<?php if (file_exists($receiptPath)): ?>
				<a href="<?= $receiptPath ?>" class="btn btn-primary" download>Download Receipt (PDF)</a>
			<?php else: ?>
				<div class="alert alert-warning">Receipt not found. Please contact support.</div>
			<?php endif; ?>
		<?php endif; ?>

		<a href="patient_dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
	</div>
</body>

</html>