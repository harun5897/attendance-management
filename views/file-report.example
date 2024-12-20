<?php
session_start();
require_once '../FPDF/fpdf.php';
require_once '../databases/database.php';
require_once '../middleware/middleware.php';
$middleware = new Middleware();
$middleware->middlewarePage();

if (!isset($_GET['start_date']) || !isset($_GET['end_date'])) {
    echo "Rentang tanggal tidak disediakan. Harap tambahkan parameter `start_date` dan `end_date` di URL.";
    exit();
}
$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];

class PDF extends FPDF {
    // Header dokumen
    public function Header() {
        $this->SetFont('Arial', 'B', 18);
        $this->Cell(0, 8, 'Laporan Kehadiran Karyawan', 0, 1, 'C');
        $this->SetFont('Arial', '', 14);
        $this->Cell(0, 8, 'PT. SANDEN ELECTRONICS INDONESIA', 0, 1, 'C');
        $this->Cell(0, 8, 'Jln Aster, Lot SD21-SD22, Lobam Bintan Industrial Estate, Bintan Island, Kepulauan Riau, Indonesia', 0, 1, 'C');
        $this->Ln(5);
        $this->Line(0, 40, 297, 40); // Garis horizontal
        $this->Ln(10);
        // Heading tabel
        $this->SetFont('Arial', 'B', 12);
        // Lebar total tabel
        $tableWidth = 10 + 50 + 80 + 30 + 30 + 30 + 50; // Total semua lebar kolom
        $pageWidth = $this->GetPageWidth(); // Lebar halaman
        $marginLeft = ($pageWidth - $tableWidth) / 2; // Hitung margin kiri
        // Atur posisi heading tabel ke tengah
        $this->SetX($marginLeft);
        $this->Cell(10, 10, 'No', 1, 0, 'C');
        $this->Cell(50, 10, 'Kode Karyawan', 1, 0, 'C');
        $this->Cell(80, 10, 'Nama Karyawan', 1, 0, 'C');
        $this->Cell(30, 10, 'Jam Masuk', 1, 0, 'C');
        $this->Cell(30, 10, 'Overtime', 1, 0, 'C');
        $this->Cell(30, 10, 'Meal Box', 1, 0, 'C');
        $this->Cell(50, 10, 'Tanggal', 1, 1, 'C');
    }
    // Footer dokumen
    public function Footer() {
        date_default_timezone_set('Asia/Jakarta');
        $this->SetY(-50); // Posisi vertikal untuk tanggal
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Lobam, ' . date('d-m-Y'), 0, 1, 'R');
        // Tambahkan jarak vertikal untuk tanda tangan
        $this->Ln(20); // Menambah jarak vertikal sebesar 20mm
        $this->SetFont('Arial', '', 12);
        $this->Cell(0, 10, 'Manager Department', 0, 1, 'R');
    }
}
// Inisialisasi database
$database = new Database();
$conn = $database->connect();
// Query gabungan tabel
$query = "
    SELECT
        tb_attendance.code_employee,
        tb_employee.name_employee,
        tb_attendance.time,
        tb_attendance.overtime,
        tb_attendance.meal_box,
        tb_attendance.date_attendance
    FROM
        tb_attendance
    JOIN
        tb_employee
    ON
        tb_attendance.code_employee = tb_employee.code_employee
    WHERE
        tb_attendance.date_attendance BETWEEN :start_date AND :end_date
";
// Query untuk menghitung total meal box
$totalMealBoxQuery = "
    SELECT
        SUM(CASE WHEN meal_box = 'siang' THEN 1 ELSE 0 END) AS total_meal_box_siang,
        SUM(CASE WHEN meal_box = 'malam' THEN 1 ELSE 0 END) AS total_meal_box_malam,
        SUM(CASE WHEN meal_box = 'siang_malam' THEN 1 ELSE 0 END) AS total_meal_box_siang_malam
    FROM
        tb_attendance
    WHERE
        date_attendance BETWEEN :start_date AND :end_date
";
// Eksekusi query untuk data utama
$stmt = $conn->prepare($query);
$stmt->bindParam(':start_date', $startDate);
$stmt->bindParam(':end_date', $endDate);
$stmt->execute();
// Eksekusi query untuk menghitung total meal box
$totalStmt = $conn->prepare($totalMealBoxQuery);
$totalStmt->bindParam(':start_date', $startDate);
$totalStmt->bindParam(':end_date', $endDate);
$totalStmt->execute();
$totals = $totalStmt->fetch(PDO::FETCH_ASSOC);
// Inisialisasi PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P', 'A4');
// Menampilkan data pada tabel
$pdf->SetFont('Arial', '', 12);
$no = 1;
// Lebar total tabel (jumlah lebar kolom)
$tableWidth = 10 + 50 + 80 + 30 + 30 + 30 + 50;
$pageWidth = $pdf->GetPageWidth();
$marginLeft = ($pageWidth - $tableWidth) / 2;
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdf->SetX($marginLeft);
    $pdf->Cell(10, 10, $no++, 1, 0, 'C');
    $pdf->Cell(50, 10, $row['code_employee'], 1, 0, 'C');
    $pdf->Cell(80, 10, $row['name_employee'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['time'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['overtime'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['meal_box'], 1, 0, 'C');
    $pdf->Cell(50, 10, $row['date_attendance'], 1, 1, 'C');
}
//total meal box
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetX($marginLeft);
$pdf->Cell(0, 10, 'Total Meal Box:', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->SetX($marginLeft);
$pdf->Cell(0, 10, 'Siang: ' . $totals['total_meal_box_siang'], 0, 1, 'L');
$pdf->SetX($marginLeft);
$pdf->Cell(0, 10, 'Malam: ' . $totals['total_meal_box_malam'], 0, 1, 'L');
$pdf->SetX($marginLeft);
$pdf->Cell(0, 10, 'Siang dan Malam: ' . $totals['total_meal_box_siang_malam'], 0, 1, 'L');
// Tutup koneksi database
$database->disconnect();
// Output PDF
$pdf->Output();
?>
