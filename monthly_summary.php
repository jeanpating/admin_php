<?php
require_once('tcpdf/tcpdf.php');

error_log('Monthly Summary PDF generation started.');

// Replace with your actual database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "attendancedb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the current month and year
$currentDate = date("d_m_Y"); // Format the date as "24_11_2023"

// Construct the table name
$tableName = "attendance_table_" . $currentDate;

// Query to get the monthly summary
$sql = "SELECT status, COUNT(*) as total FROM $tableName GROUP BY status";
$result = $conn->query($sql);

if ($result === false) {
    echo 'Error executing the query: ' . $conn->error;
} else {
    $summary = array('early' => 0, 'late' => 0, 'absent' => 0);

    // Calculate the totals for each status
    while ($row = $result->fetch_assoc()) {
        $status = strtolower($row['status']);
        $summary[$status] = $row['total'];
    }

    // Generate PDF
    $pdf = new TCPDF();
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // Add content to the PDF
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 10, 'Monthly Summary', 0, 1, 'C');

    // Create a table to display the summary
    $pdf->SetFillColor(200, 220, 255);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.3);

    $header = array('Status', 'Total');
    $w = array(60, 40);
    for ($i = 0; $i < count($header); ++$i) {
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
    }
    $pdf->Ln();

    foreach ($summary as $status => $total) {
        $pdf->Cell($w[0], 6, ucfirst($status), 'LR');
        $pdf->Cell($w[1], 6, $total, 'LR', 1, 'R');
    }

    $pdf->Output('monthly_summary.pdf', 'D');
}

$conn->close();
error_log('Monthly Summary PDF generation completed.');
?>
