<?php
require 'config.php';
require 'vendor/autoload.php'; // Dompdf
use Dompdf\Dompdf;
use Dompdf\Options;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bill_id'])) {
    $bill_id = $_POST['bill_id'];

    // Fetch bill details
    $stmt = $pdo->prepare("
        SELECT b.*, p.name AS patient_name, a.appointment_date
        FROM bills b
        JOIN patients p ON b.patient_id = p.id
        JOIN appointments a ON b.appointment_id = a.id
        WHERE b.id = ?
    ");
    $stmt->execute([$bill_id]);
    $bill = $stmt->fetch();

    if (!$bill) {
        header("Location: admin_billing.php?error=Bill+not+found");
        exit;
    }

    // Update status to Paid
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
    <p><span class="label">Patient:</span> ' . htmlspecialchars($bill['patient_name']) . '</p>
    <p><span class="label">Appointment Date:</span> ' . $bill['appointment_date'] . '</p>
    <p><span class="label">Amount Paid:</span> ৳' . number_format($bill['amount'], 2) . '</p>
    <p><span class="label">Marked Paid By:</span> Admin</p>
    <p><span class="label">Date:</span> ' . date('Y-m-d H:i:s') . '</p>
    ';

    $dompdf = new Dompdf(new Options(['isRemoteEnabled' => true]));
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Save PDF to file
    $filename = "receipts/receipt_bill_" . $bill['id'] . ".pdf";
    file_put_contents($filename, $dompdf->output());

    header("Location: admin_billing.php?success=Bill+marked+as+paid+and+receipt+generated");
    exit;
} else {
    header("Location: admin_billing.php?error=Invalid+request");
    exit;
}