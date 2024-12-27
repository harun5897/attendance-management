<?php
session_start();
require_once '../FPDF/fpdf.php';
require_once '../databases/database.php';
require_once '../middleware/middleware.php';

$middleware = new Middleware();
$middleware->middlewarePage();

if ($_SESSION['role'] == 'MANAGER' && $_SESSION['role'] == 'ADMIN') {
    header("Location: /attendance/index.php");
}
if (!isset($_GET['start_date']) || !isset($_GET['end_date']) || !isset($_GET['employee_attendance'])) {
    echo "Parameter tidak lengkap. Harap tambahkan `start_date`, `end_date`, dan `employee_attendance` di URL.";
    exit();
}

$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];
$employeeAttendance = $_GET['employee_attendance'];

class PDF extends FPDF {
    public function Header() {
        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 8, 'Laporan Kehadiran Karyawan', 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 8, 'PT. SANDEN ELECTRONICS INDONESIA', 0, 1, 'C');
        $this->Cell(0, 8, 'Jln Aster, Lot SD21-SD22, Lobam Bintan Industrial Estate, Bintan Island, Kepulauan Riau, Indonesia', 0, 1, 'C');
        $this->Ln(5);
        $this->Line(5, 40, 205, 40);
        $this->Ln(10);
        $this->SetFont('Arial', 'B', 7);
        $tableWidth = 10 + 22 + 45 + 20 + 20 + 20 + 20 + 18;
        $pageWidth = $this->GetPageWidth();
        $marginLeft = ($pageWidth - $tableWidth) / 2;
        $this->SetX($marginLeft);
        $this->Cell(10, 7, 'No', 1, 0, 'C');
        $this->Cell(22, 7, 'Kode Karyawan', 1, 0, 'C');
        $this->Cell(45, 7, 'Nama Karyawan', 1, 0, 'C');
        $this->Cell(20, 7, 'Jam Masuk', 1, 0, 'C');
        $this->Cell(20, 7, 'Overtime', 1, 0, 'C');
        $this->Cell(20, 7, 'Meal Box', 1, 0, 'C');
        $this->Cell(20, 7, 'Keterangan', 1, 0, 'C');
        $this->Cell(18, 7, 'Tanggal', 1, 1, 'C');
    }

    public function Footer() {
        date_default_timezone_set('Asia/Jakarta');
        $this->SetY(-50);
        $this->SetFont('Arial', 'I', 7);
        $this->Cell(166, 0, 'Lobam, ' . date('d-m-Y'), 0, 1, 'R');
        $this->Ln(20);
        $this->SetFont('Arial', '', 9);
        $this->Cell(170, 0, 'Manager Department', 0, 1, 'R');
    }
}

$database = new Database();
$conn = $database->connect();

// Query utama dengan logika filter berdasarkan employeeAttendance
$query = "
    SELECT
        tb_attendance.code_employee,
        tb_employee.name_employee,
        tb_attendance.time,
        tb_attendance.overtime,
        tb_attendance.meal_box,
        tb_attendance.description,
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

// Tambahkan filter berdasarkan nilai employeeAttendance
if ($employeeAttendance === 'hadir') {
    $query .= " AND tb_attendance.time IS NOT NULL";
} elseif ($employeeAttendance === 'tidak_hadir') {
    $query .= " AND tb_attendance.time IS NULL";
}

$stmt = $conn->prepare($query);
$stmt->bindParam(':start_date', $startDate);
$stmt->bindParam(':end_date', $endDate);
$stmt->execute();

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

// Tambahkan filter berdasarkan nilai employeeAttendance
if ($employeeAttendance === 'hadir') {
    $totalMealBoxQuery .= " AND tb_attendance.time IS NOT NULL";
} elseif ($employeeAttendance === 'tidak_hadir') {
    $totalMealBoxQuery .= " AND tb_attendance.time IS NULL";
}
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
$pdf->SetFont('Arial', '', 7);
$no = 1;
$tableWidth = 10 + 22 + 45 + 20 + 20 + 20 + 20 + 18;
$pageWidth = $pdf->GetPageWidth();
$marginLeft = ($pageWidth - $tableWidth) / 2;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pdf->SetX($marginLeft);
    $pdf->Cell(10, 7, $no++, 1, 0, 'C');
    $pdf->Cell(22, 7, $row['code_employee'], 1, 0, 'C');
    $pdf->Cell(45, 7, $row['name_employee'], 1, 0, 'C');
    $pdf->Cell(20, 7, $row['time'], 1, 0, 'C');
    $pdf->Cell(20, 7, $row['overtime'], 1, 0, 'C');
    $pdf->Cell(20, 7, $row['meal_box'], 1, 0, 'C');
    $pdf->Cell(20, 7, $row['description'], 1, 0, 'C');
    $pdf->Cell(18, 7, $row['date_attendance'], 1, 1, 'C');
}

// Total Meal Box
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetX($marginLeft);
$pdf->Cell(0, 7, 'Total Meal Box :', 0, 1, 'L');
$pdf->SetFont('Arial', '', 7);
$pdf->SetX($marginLeft);
$pdf->Cell(0, 7, 'Siang : ' . $totals['total_meal_box_siang'], 0, 1, 'L');
$pdf->SetX($marginLeft);
$pdf->Cell(0, 7, 'Malam : ' . $totals['total_meal_box_malam'], 0, 1, 'L');
$pdf->SetX($marginLeft);
$pdf->Cell(0, 7, 'Siang dan Malam : ' . $totals['total_meal_box_siang_malam'], 0, 1, 'L');

// Tutup koneksi database
$database->disconnect();

// Output PDF
$pdf->Output();
?>
