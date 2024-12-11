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
            $page = isset($requestBody['page']) ? (int)$requestBody['page'] : 1;
            $limit = 8;
            $offset = ($page - 1) * $limit;
            $query = "SELECT * FROM tb_employee LIMIT :limit OFFSET :offset";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Count paging info
            $totalQuery = "SELECT COUNT(*) as total FROM tb_employee";
            $totalStmt = $conn->prepare($totalQuery);
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
                    'data_per_page' => $limit
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
        public function updateEmployee() {
            return [
                'success' => true,
                'data' => null,
                'message' => 'Berhasil melakukan update data karyawan'
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
