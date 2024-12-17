<?php

use PhpOffice\PhpSpreadsheet\Worksheet\Row;

require_once '../databases/database.php';
require_once '../vendor/autoload.php';

class AttendanceController {
    public function uploadAttendance() {
        $file = $_FILES['file'];
        // Validation
        if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return [
                'success' => false,
                'data' => $file,
                'message' => 'Terjadi kesalahan dalam proses pengunggahan file.'
            ];
        }
        $allowedExtensions = ['xls', 'xlsx'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        // Validation extension
        if (!in_array($fileExtension, $allowedExtensions)) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'File yang diunggah harus berformat .xls atau .xlsx.'
            ];
        }
        // Sub folder
        $uploadDir = '../public/data-attendance/';
        // Create folder if folder not exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $newFilePath = $uploadDir . basename($file['name']);
        // Resonponse error when move data to new folder
        if (!move_uploaded_file($file['tmp_name'], $newFilePath)) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Gagal memindahkan file ke server.'
            ];
        }
        // Response success
        return [
            'success' => true,
            'data' => [
                'path_file' => $newFilePath,
                'file_name' => $file['name'],
                'extension' => $fileExtension,
            ],
            'message' => 'Berhasil upload file attendance'
        ];
    }
    public function getAttendacne($requestBody) {
        if (empty($requestBody['pathFile'])) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Path file wajib diisi.'
            ];
        }
        if (empty($requestBody['departement'])) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Departemen wajib diisi.'
            ];
        }
        $pathFile = $requestBody['pathFile'];
        if (!file_exists($pathFile)) {
            return [
                'success' => false,
                'data' => $pathFile,
                'message' => 'File tidak ditemukan pada server.'
            ];
        }
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($pathFile);
        if (!$spreadsheet->sheetNameExists($requestBody['departement'])) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Nama departemen "' . $requestBody['departement'] . '" tidak ditemukan.'
            ];
        }
        $sheet = $spreadsheet->getSheetByName($requestBody['departement']);
        $data = [];
        $maxRow = 100;
        for ($row = 9; $row <= $maxRow; $row++) {
            $kodeKaryawan = $sheet->getCell("B{$row}")->getValue();
            $namaKaryawan = $sheet->getCell("E{$row}")->getValue();
            $jam = $sheet->getCell("G{$row}")->getValue();
            $menit = $sheet->getCell("H{$row}")->getValue();
            $kolomA = $sheet->getCell("A{$row}")->getValue();
            if (empty($kolomA)) {
                break;
            }
            $data[] = [
                'code_employee' => $kodeKaryawan,
                'name_employee' => $namaKaryawan,
                'time' => !is_null($jam) && !is_null($menit)
                            ? sprintf('%02d:%02d', $jam, $menit)
                            : null,
                'overtime' => null,
                'meal_box' => null
            ];
        }
        array_pop($data);
        return [
            'success' => true,
            'data' => $data,
            'date_attendance' => $sheet->getCell("C5")->getValue(),
            'message' => 'Berhasil mengambil data attendance.'
        ];
    }
    public function submitAttendance($requestBody) {
        // Validation
        if (empty($requestBody['dataAttendance']) || !is_array($requestBody['dataAttendance'])) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Data attendance wajib diisi dan harus berupa array'
            ];
        }
        if (empty($requestBody['dateAttendance'])) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Tanggal attendance wajib diisi'
            ];
        }
        $dateAttendance = $requestBody['dateAttendance'];
        $database = new Database();
        $conn = $database->connect();
        try {
            // Mulai transaksi
            $conn->beginTransaction();
            // 1. Ambil semua code_employee dari tb_employee untuk validasi
            $queryCheckEmployee = "SELECT code_employee FROM tb_employee";
            $stmtCheck = $conn->prepare($queryCheckEmployee);
            $stmtCheck->execute();
            $validEmployees = $stmtCheck->fetchAll(PDO::FETCH_COLUMN);
            // 2. Validasi code_employee dari dataAttendance
            foreach ($requestBody['dataAttendance'] as $attendance) {
                if (!in_array($attendance['code_employee'], $validEmployees)) {
                    throw new UnexpectedValueException("Kode karyawan '{$attendance['code_employee']}' tidak terdaftar pada data karyawan");
                }
            }
            // 3. Validasi duplikasi data di tb_attendance
            // Ambil semua code_employee dan departemen yang ingin di-insert
            $codeEmployees = array_map(fn($item) => $item['code_employee'], $requestBody['dataAttendance']);
            $placeholders = implode(',', array_fill(0, count($codeEmployees), '?'));
            // Query untuk memeriksa duplikasi
            $queryCheckDuplication = "
                SELECT COUNT(*) as count
                FROM tb_attendance a
                JOIN tb_employee e ON a.code_employee = e.code_employee
                WHERE a.date_attendance = ? AND a.code_employee IN ($placeholders)
            ";
            $stmtCheckDuplication = $conn->prepare($queryCheckDuplication);
            $stmtCheckDuplication->execute(array_merge([$dateAttendance], $codeEmployees));
            $resultDuplication = $stmtCheckDuplication->fetch(PDO::FETCH_ASSOC);
            if ($resultDuplication['count'] > 0) {
                throw new UnexpectedValueException("Data attendance untuk departemen ini pada tanggal $dateAttendance sudah ada di sistem.");
            }
            // 4. Query untuk insert data ke tb_attendance
            $queryInsert = "INSERT INTO tb_attendance (code_employee, time, overtime, meal_box, date_attendance)
                            VALUES (:code_employee, :time, :overtime, :meal_box, :date_attendance)";
            $stmtInsert = $conn->prepare($queryInsert);
            // 5. Loop dataAttendance untuk insert ke tb_attendance
            foreach ($requestBody['dataAttendance'] as $attendance) {
                $stmtInsert->bindValue(':code_employee', $attendance['code_employee']);
                $stmtInsert->bindValue(':time', $attendance['time'] ?? null);
                $stmtInsert->bindValue(':overtime', $attendance['overtime'] ?? null);
                $stmtInsert->bindValue(':meal_box', $attendance['meal_box'] ?? null);
                $stmtInsert->bindValue(':date_attendance', $dateAttendance);
                $stmtInsert->execute();
            }
            // Commit transaksi jika semua proses sukses
            $conn->commit();
            $database->disconnect();
            return [
                'success' => true,
                'data' => null,
                'message' => 'Berhasil menambahkan data attendance'
            ];
        } catch (Exception $e) {
            // Rollback jika terjadi kesalahan
            $conn->rollBack();
            $database->disconnect();
            return [
                'success' => false,
                'data' => null,
                'message' => 'Gagal menambahkan data attendance: ' . $e->getMessage()
            ];
        }
    }
    public function getAttendanceActual($requestBody) {
        if (empty($requestBody['page'])) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'page tidak ditemukan'
            ];
        }
        $database = new Database();
        $conn = $database->connect();
        $page = isset($requestBody['page']) ? (int)$requestBody['page'] : 1;
        $limit = 8;
        $offset = ($page - 1) * $limit;
        try {
            $query = "
                SELECT
                    e.code_employee,
                    e.name_employee,
                    a.time,
                    a.overtime,
                    a.meal_box,
                    a.date_attendance
                FROM
                    tb_attendance AS a
                INNER JOIN
                    tb_employee AS e
                ON
                    a.code_employee = e.code_employee
                LIMIT :limit OFFSET :offset
            ";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $attendanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Query untuk menghitung total data
            $totalQuery = "
                SELECT COUNT(*) as total
                FROM tb_attendance AS a
                INNER JOIN tb_employee AS e
                ON a.code_employee = e.code_employee
            ";
            $totalStmt = $conn->prepare($totalQuery);
            $totalStmt->execute();
            $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
            $totalData = (int)$totalResult['total'];
            $totalPages = ceil($totalData / $limit);
            $database->disconnect();
            // Response sukses
            return [
                'success' => true,
                'data' => $attendanceData,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_data' => $totalData,
                    'data_per_page' => $limit
                ],
                'message' => 'Berhasil mengambil data kehadiran karyawan'
            ];
        } catch (Exception $e) {
            $database->disconnect();
            return [
                'success' => false,
                'data' => null,
                'message' => 'Gagal mengambil data: ' . $e->getMessage()
            ];
        }
    }
    public function deleteAttendanceActual($requestBody) {
        // Validasi input
        if (empty($requestBody['codeEmployee'])) {
            return [
                'success' => false,
                'data' => null,
                'message' => 'Kode karyawan tidak ditemukan'
            ];
        }
        // Koneksi ke database
        $database = new Database();
        $conn = $database->connect();
        try {
            // Query DELETE berdasarkan code_employee
            $query = "DELETE FROM tb_attendance WHERE code_employee = :code_employee";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':code_employee', $requestBody['codeEmployee']);
            $responseDeleteAttendance = $stmt->execute();
            $database->disconnect();
            // Jika proses delete gagal
            if (!$responseDeleteAttendance) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Gagal menghapus data kehadiran karyawan'
                ];
            }
            // Jika proses delete sukses
            return [
                'success' => true,
                'data' => '',
                'message' => 'Berhasil menghapus data kehadiran karyawan'
            ];
        } catch (Exception $e) {
            // Tangani error jika terjadi exception
            $database->disconnect();
            return [
                'success' => false,
                'data' => null,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ];
        }
    }
}
?>
