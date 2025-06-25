<?php
require 'db.php';
require_once('fpdf/fpdf.php'); // You need to place FPDF in backend/fpdf/

// Get all stock history (no time limit)
$res = $conn->query("SELECT h.*, s.name as stock_name, u.username FROM stock_history h JOIN stock s ON h.stock_id = s.id JOIN users u ON h.user_id = u.id ORDER BY h.date DESC");

$history = [];
while ($row = $res->fetch_assoc()) {
    $history[] = $row;
}

if (count($history) === 0) {
    echo json_encode(['success' => false, 'message' => 'No history found.']);
    exit();
}

// Generate PDF
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'All Stock History', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Date', 1);
$pdf->Cell(50, 10, 'Stock Item', 1);
$pdf->Cell(30, 10, 'User', 1);
$pdf->Cell(20, 10, 'Type', 1);
$pdf->Cell(20, 10, 'Qty', 1);
$pdf->Ln();
$pdf->SetFont('Arial', '', 12);
foreach ($history as $h) {
    $pdf->Cell(40, 10, $h['date'], 1);
    $pdf->Cell(50, 10, $h['stock_name'], 1);
    $pdf->Cell(30, 10, $h['username'], 1);
    $pdf->Cell(20, 10, strtoupper($h['type']), 1);
    $pdf->Cell(20, 10, $h['qty'], 1);
    $pdf->Ln();
}

// Create export directory if not exists
$exportDir = '../exports/';
if (!is_dir($exportDir)) {
    mkdir($exportDir, 0777, true);
}
$filename = 'stock_history_' . date('Ymd_His') . '.pdf';
$filepath = $exportDir . $filename;
$pdf->Output('F', $filepath);

// Delete exported history
$conn->query("DELETE FROM stock_history");

echo json_encode(['success' => true, 'file' => $filepath]);
