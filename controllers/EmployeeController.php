<?php
    require_once '../databases/database.php';
    class EmployeeController {
        public function getEmployee($requestBody) {
            if (empty($requestBody['page'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'page tidak ditemukan'
                ];
            }
            // Process to database
            $database = new Database();
            $conn = $database->connect();
            // Mengambil halaman saat ini
            $page = isset($requestBody['page']) ? (int)$requestBody['page'] : 1;
            $limit = 8;
            $offset = ($page - 1) * $limit;
            // Memeriksa apakah ada data departement di session
            $departement = isset($_SESSION["departement"]) ? $_SESSION["departement"] : null;
            // Membuat query dasar
            $query = "SELECT * FROM tb_employee";
            // Jika ada departement di session, tambahkan filter pada query
            if ($departement) {
                $query .= " WHERE departement = :departement";
            }
            $query .= " LIMIT :limit OFFSET :offset";
            // Persiapkan dan eksekusi query
            $stmt = $conn->prepare($query);
            // Jika ada filter departement, bind nilai departement
            if ($departement) {
                $stmt->bindValue(':departement', $departement, PDO::PARAM_STR);
            }
            // Bind limit dan offset
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Query untuk menghitung total data (dengan atau tanpa filter departement)
            $totalQuery = "SELECT COUNT(*) as total FROM tb_employee";
            if ($departement) {
                $totalQuery .= " WHERE departement = :departement";
            }
            $totalStmt = $conn->prepare($totalQuery);
            // Jika ada filter departement, bind nilai departement
            if ($departement) {
                $totalStmt->bindValue(':departement', $departement, PDO::PARAM_STR);
            }
            $totalStmt->execute();
            $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
            $totalData = (int)$totalResult['total'];
            $totalPages = ceil($totalData / $limit);
            $database->disconnect();
            // Response success after processing data from database
            return [
                'success' => true,
                'data' => $employees,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_data' => $totalData,
                    'data_per_page' => $limit,
                ],
                'message' => 'Berhasil mengambil daftar data karyawan'
            ];
        }
        public function getDetailEmployee($requestBody) {
            // Validation
            if (empty($requestBody['codeEmployee'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Kode karyawan tidak ditemukan'
                ];
            }
            // Process to database
            $database = new Database();
            $conn = $database->connect();
            $query = "SELECT * FROM tb_employee WHERE code_employee = :code_employee";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':code_employee', $requestBody['codeEmployee'], PDO::PARAM_STR);
            $stmt->execute();
            $employee = $stmt->fetch(PDO::FETCH_ASSOC);
            $database->disconnect();
            // Response error after processing data from database
            if (!$employee) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Data tidak ditemukan'
                ];
            }
            // Response success after processing data from database
            return [
                'success' => true,
                'data' => $employee,
                'message' => 'Berhasil mengambil detail data karyawan'
            ];
        }
        public function createEmployee($requestBody) {
            // Validation
            if (empty($requestBody['codeEmployee']) || empty($requestBody['nameEmployee']) || empty($requestBody['fingerprint']) || empty($requestBody['dateJoin']) || empty($requestBody['departement'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Kode karyawan, nama, ID fingerprint, tanggal join dan departemen wajib diisi'
                ];
            }
            if (strlen($requestBody['nameEmployee']) < 5 || strlen($requestBody['nameEmployee']) > 30) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Nama karyawan harus memiliki panjang 5-30 karakter'
                ];
            }
            if (!preg_match('/^[a-zA-Z.\s]+$/', $requestBody['nameEmployee'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Nama karyawan hanya boleh mengandung huruf, spasi, dan karakter titik (.)'
                ];
            }
            if (!preg_match('/\d{3,10}$/', $requestBody['codeEmployee'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Kode karyawan harus berupa angka 1-9 dan memiliki panjang 3-10 karakter.'
                ];
            }
            if (preg_match('/^0+$/', $requestBody['codeEmployee'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Kode karyawan tidak boleh bernilai 0'
                ];
            }
            if (!preg_match('/\d{3,10}$/', $requestBody['fingerprint'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'ID fingerprint harus berupa angka 1-9 dan memiliki panjang 3-10 karakter.'
                ];
            }
            if (preg_match('/^0+$/', $requestBody['fingerprint'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'ID fingerprint tidak boleh bernilai 0'
                ];
            }
            // Check code employee exist ?
            $database = new Database();
            $conn = $database->connect();
            $checkQuery = "SELECT COUNT(*) FROM tb_employee WHERE code_employee = :code_employee";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bindValue(':code_employee', $requestBody['codeEmployee']);
            $checkStmt->execute();
            // Validation code employee exist
            if ($checkStmt->fetchColumn() > 0) {
                $database->disconnect();
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Kode karyawan sudah ada, harap gunakan kode lain'
                ];
            }
            // Check fingerprint exist ?
            $checkFingerprintQuery = "SELECT COUNT(*) FROM tb_employee WHERE fingerprint = :fingerprint";
            $checkFingerprintStmt = $conn->prepare($checkFingerprintQuery);
            $checkFingerprintStmt->bindValue(':fingerprint', $requestBody['fingerprint']);
            $checkFingerprintStmt->execute();
            // Validation fingerprint exist
            if ($checkFingerprintStmt->fetchColumn() > 0) {
                $database->disconnect();
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Fingerprint karyawan sudah ada, harap gunakan fingerprint lain'
                ];
            }
            // Insert data employee to database
            $query = "INSERT INTO tb_employee (code_employee, name_employee, fingerprint, date_join, departement) VALUES (:code_employee, :name_employee, :fingerprint, :date_join, :departement)";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':code_employee', $requestBody['codeEmployee']);
            $stmt->bindValue(':name_employee', strtoupper($requestBody['nameEmployee']));
            $stmt->bindValue(':fingerprint', $requestBody['fingerprint']);
            $stmt->bindValue(':date_join', $requestBody['dateJoin']);
            $stmt->bindValue(':departement', $requestBody['departement']);
            $responseSaveEmployee = $stmt->execute();
            $database->disconnect();
            // Response error after process to database
            if (!$responseSaveEmployee) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Gagal menambahkan data ke database'
                ];
            }
            // Response success after process to database
            return [
                'success' => true,
                'data' => $requestBody,
                'message' => 'Berhasil membuat data karyawan'
            ];
        }
        public function updateEmployee($requestBody) {
            // Validation
            if (empty($requestBody['codeEmployee']) || empty($requestBody['nameEmployee']) || empty($requestBody['fingerprint']) || empty($requestBody['dateJoin']) || empty($requestBody['departement'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Kode karyawan, nama, ID fingerprint, tanggal join dan departemen wajib diisi'
                ];
            }
            if (strlen($requestBody['nameEmployee']) < 5 || strlen($requestBody['nameEmployee']) > 30) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Nama karyawan harus memiliki panjang 5-30 karakter'
                ];
            }
            if (!preg_match('/^[a-zA-Z.\s]+$/', $requestBody['nameEmployee'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Nama karyawan hanya boleh mengandung huruf, spasi, dan karakter titik (.)'
                ];
            }
            if (!preg_match('/\d{3,10}$/', $requestBody['fingerprint'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'ID fingerprint harus berupa angka 1-9 dan memiliki panjang 3-10 karakter.'
                ];
            }
            if (preg_match('/^0+$/', $requestBody['fingerprint'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'ID fingerprint tidak boleh bernilai 0'
                ];
            }
            // Database connection
            $database = new Database();
            $conn = $database->connect();
            // Check if code_employee exists
            $checkCodeQuery = "SELECT * FROM tb_employee WHERE code_employee = :code_employee";
            $checkCodeStmt = $conn->prepare($checkCodeQuery);
            $checkCodeStmt->bindValue(':code_employee', $requestBody['codeEmployee']);
            $checkCodeStmt->execute();
            $employeeData = $checkCodeStmt->fetch(PDO::FETCH_ASSOC);
            if (!$employeeData) {
                $database->disconnect();
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Kode karyawan tidak ditemukan'
                ];
            }
            // Check if fingerprint exists for another employee
            $checkFingerprintQuery = "SELECT COUNT(*) FROM tb_employee WHERE fingerprint = :fingerprint AND code_employee != :code_employee";
            $checkFingerprintStmt = $conn->prepare($checkFingerprintQuery);
            $checkFingerprintStmt->bindValue(':fingerprint', $requestBody['fingerprint']);
            $checkFingerprintStmt->bindValue(':code_employee', $requestBody['codeEmployee']);
            $checkFingerprintStmt->execute();
            if ($checkFingerprintStmt->fetchColumn() > 0) {
                $database->disconnect();
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Fingerprint karyawan sudah ada, harap gunakan fingerprint lain'
                ];
            }
            // Update employee data in database
            $updateQuery = "UPDATE tb_employee SET name_employee = :name_employee, fingerprint = :fingerprint, date_join = :date_join, departement = :departement WHERE code_employee = :code_employee";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bindValue(':name_employee', strtoupper($requestBody['nameEmployee']));
            $updateStmt->bindValue(':fingerprint', $requestBody['fingerprint']);
            $updateStmt->bindValue(':date_join', $requestBody['dateJoin']);
            $updateStmt->bindValue(':departement', $requestBody['departement']);
            $updateStmt->bindValue(':code_employee', $requestBody['codeEmployee']);
            $responseUpdateEmployee = $updateStmt->execute();
            $database->disconnect();
            // Response after process to database
            if (!$responseUpdateEmployee) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Gagal mengupdate data ke database'
                ];
            }
            // Response success after update
            return [
                'success' => true,
                'data' => $requestBody,
                'message' => 'Berhasil mengupdate data karyawan'
            ];
        }
        public function deleteEmployee($requestBody) {
            // Validation
            if (empty($requestBody['codeEmployee'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Kode karyawan tidak ditemukan'
                ];
            }
            // Process to database
            $database = new Database();
            $conn = $database->connect();
            $query = "DELETE FROM tb_employee WHERE code_employee = :code_employee";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':code_employee', $requestBody['codeEmployee']);
            $responseDeleteEmployee = $stmt->execute();
            $database->disconnect();
            // Response error after processing data from database
            if (!$responseDeleteEmployee) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Gagal menghapus data karyawan'
                ];
            }
            // Response success after processing data from database
            return [
                'success' => true,
                'data' => '',
                'message' => 'Berhasil menghapus karyawan'
            ];
        }
    }
?>
