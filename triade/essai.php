<?php
define('FPDF_FONTPATH','./librairie_pdf/fpdf/font/');
include('./librairie_pdf/fpdf/fpdf.php');
include('./librairie_pdf/fpdf/html2pdf.php');

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'Hello World !');
$pdf->Output();
?>
