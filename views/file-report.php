<?php
// Memulai sesi
session_start();

// Memuat library dan dependensi
require_once '../FPDF/fpdf.php';
require_once '../databases/database.php';
require_once '../middleware/middleware.php';

// Middleware untuk validasi akses
$middleware = new Middleware();
$middleware->middlewarePage();

// Validasi hak akses
if ($_SESSION['role'] != 'MANAGER' && $_SESSION['role'] != 'ADMIN') {
    header("Location: /attendance/index.php");
    exit();
}

// Validasi parameter yang diperlukan
if (!isset($_GET['start_date'], $_GET['end_date'], $_GET['employee_attendance'], $_GET['departement'])) {
    echo "Parameter tidak lengkap. Harap tambahkan `start_date`, `end_date`, `employee_attendance`, dan `departement` di URL.";
    exit();
}

// Mengambil parameter dari URL
$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];
$employeeAttendance = $_GET['employee_attendance'];
$departement = $_GET['departement'];

// Validasi parameter departement
if (empty($departement)) {
    echo "Parameter `departement` tidak boleh kosong.";
    exit();
}

// Kelas PDF untuk membuat laporan
class PDF extends FPDF {
    public function Header() {
        global $departement;

        $this->SetFont('Arial', 'B', 13);
        $this->Cell(0, 8, 'Laporan Kehadiran Karyawan', 0, 1, 'C');
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 8, 'PT. SANDEN ELECTRONICS INDONESIA', 0, 1, 'C');
        $this->Cell(0, 8, 'Jln Aster, Lot SD21-SD22, Lobam Bintan Industrial Estate, Bintan Island, Kepulauan Riau, Indonesia', 0, 1, 'C');
        $this->Cell(0, 8, 'Departemen ' . ($departement === 'semua_departemen' ? 'Semua Departemen' : $departement), 0, 1, 'C');
        $this->Ln(5);
        $this->Line(5, 43, 205, 43);
        $this->Ln(5);

        // Header tabel
        $this->SetFont('Arial', 'B', 7);
        $tableWidth = 175; // Total lebar tabel
        $marginLeft = ($this->GetPageWidth() - $tableWidth) / 2;
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
}

// Inisialisasi koneksi database
$database = new Database();
$conn = $database->connect();

// Query untuk mengambil data kehadiran
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

// Tambahkan filter departemen jika tidak memilih semua_departemen
if ($departement !== 'semua_departemen') {
    $query .= " AND tb_employee.departement = :departement";
}

// Tambahkan filter kehadiran
if ($employeeAttendance === 'hadir') {
    $query .= " AND tb_attendance.time IS NOT NULL";
} elseif ($employeeAttendance === 'tidak_hadir') {
    $query .= " AND tb_attendance.time IS NULL";
}

$stmt = $conn->prepare($query);
$stmt->bindParam(':start_date', $startDate);
$stmt->bindParam(':end_date', $endDate);
if ($departement !== 'semua_departemen') {
    $stmt->bindParam(':departement', $departement);
}
$stmt->execute();

// Query untuk menghitung total meal box
$totalMealBoxQuery = "
    SELECT
        SUM(CASE WHEN meal_box = 'siang' THEN 1 ELSE 0 END) AS total_meal_box_siang,
        SUM(CASE WHEN meal_box = 'malam' THEN 1 ELSE 0 END) AS total_meal_box_malam,
        SUM(CASE WHEN meal_box = 'siang_malam' THEN 1 ELSE 0 END) AS total_meal_box_siang_malam
    FROM
        tb_attendance
    JOIN
        tb_employee
    ON
        tb_attendance.code_employee = tb_employee.code_employee
    WHERE
        tb_attendance.date_attendance BETWEEN :start_date AND :end_date
";

if ($departement !== 'semua_departemen') {
    $totalMealBoxQuery .= " AND tb_employee.departement = :departement";
}
if ($employeeAttendance === 'hadir') {
    $totalMealBoxQuery .= " AND tb_attendance.time IS NOT NULL";
} elseif ($employeeAttendance === 'tidak_hadir') {
    $totalMealBoxQuery .= " AND tb_attendance.time IS NULL";
}

$totalStmt = $conn->prepare($totalMealBoxQuery);
$totalStmt->bindParam(':start_date', $startDate);
$totalStmt->bindParam(':end_date', $endDate);
if ($departement !== 'semua_departemen') {
    $totalStmt->bindParam(':departement', $departement);
}
$totalStmt->execute();
$totals = $totalStmt->fetch(PDO::FETCH_ASSOC);

// Inisialisasi PDF
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage('P', 'A4');

// Menampilkan data tabel
$pdf->SetFont('Arial', '', 7);
$no = 1;
$tableWidth = 175; // Total lebar tabel
$marginLeft = ($pdf->GetPageWidth() - $tableWidth) / 2;

// Tetapkan jumlah maksimum baris per halaman
$maxRowsPerPage = 25;
$currentRowCount = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    if ($currentRowCount >= $maxRowsPerPage) {
        // Buat halaman baru
        $pdf->AddPage('P', 'A4');
        $currentRowCount = 0; // Reset jumlah baris untuk halaman baru
    }

    // Tampilkan data
    $pdf->SetX($marginLeft);
    $pdf->Cell(10, 7, $no++, 1, 0, 'C');
    $pdf->Cell(22, 7, $row['code_employee'], 1, 0, 'C');
    $pdf->Cell(45, 7, $row['name_employee'], 1, 0, 'C');
    $pdf->Cell(20, 7, $row['time'], 1, 0, 'C');
    $pdf->Cell(20, 7, $row['overtime'], 1, 0, 'C');
    $pdf->Cell(20, 7, $row['meal_box'], 1, 0, 'C');
    $pdf->Cell(20, 7, $row['description'], 1, 0, 'C');
    $pdf->Cell(18, 7, $row['date_attendance'], 1, 1, 'C');

    $currentRowCount++; // Tambahkan jumlah baris saat ini
}


// Menampilkan total meal box di sebelah kiri
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetX($marginLeft);
$pdf->Cell(0, 7, 'Total Meal Box:', 0, 1, 'L');
$pdf->SetFont('Arial', '', 7);
$pdf->SetX($marginLeft);
$pdf->Cell(0, 7, 'Siang: ' . $totals['total_meal_box_siang'], 0, 1, 'L');
$pdf->SetX($marginLeft);
$pdf->Cell(0, 7, 'Malam: ' . $totals['total_meal_box_malam'], 0, 1, 'L');
$pdf->SetX($marginLeft);
$pdf->Cell(0, 7, 'Siang dan Malam: ' . $totals['total_meal_box_siang_malam'], 0, 1, 'L');

// Menampilkan total meal box di sebelah kiri
$pdf->SetY($pdf->GetY() - 27);
$pdf->SetFont('Arial', '', 7);
$pageWidth = $pdf->GetPageWidth();
$marginRight = 17;
$xPosition = $pageWidth - $marginRight;
$pdf->SetX($xPosition - 50);
$pdf->Cell(50, 7, 'Lobam, ' . date('d-m-Y'), 0, 1, 'R');
$pdf->Ln(15);
$pdf->SetX($xPosition - 50);
$pdf->Cell(50, 7, 'Manager Departemen', 0, 1, 'R');

$pdf->SetY($pdf->GetY() - 29);
$pdf->SetFont('Arial', '', 7);
$pageWidth = $pdf->GetPageWidth();
$marginRight = 57;
$xPosition = $pageWidth - $marginRight;
$pdf->SetX($xPosition - 50);
$pdf->Cell(50, 7, '', 0, 1, 'R');
$pdf->Ln(15);
$pdf->SetX($xPosition - 50);
$pdf->Cell(50, 7, 'Leader Departemen', 0, 1, 'R');

$pdf->SetY($pdf->GetY() - 29);
$pdf->SetFont('Arial', '', 7);
$pageWidth = $pdf->GetPageWidth();
$marginRight = 100;
$xPosition = $pageWidth - $marginRight;
$pdf->SetX($xPosition - 50);
$pdf->Cell(50, 7, ' ', 0, 1, 'R');
$pdf->Ln(15);
$pdf->SetX($xPosition - 50);
$pdf->Cell(50, 7, 'Admin', 0, 1, 'R');

// Menutup koneksi database
$database->disconnect();

// Output PDF
$pdf->Output();
?>
