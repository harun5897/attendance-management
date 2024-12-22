<?php
    require_once '../databases/database.php';
    class UserController {
        public function getUser($requestBody) {
            if(empty($requestBody['page'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'page tidak ditemukan'
                ];
            }
            //Process to database
            $database = new Database();
            $conn = $database->connect();
            $page = isset($requestBody['page']) ? (int)$requestBody['page'] : 1;
            $limit = 8;
            $offset = ($page - 1) * $limit;
            $query = "SELECT * FROM tb_users LIMIT :limit OFFSET :offset";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Count paging info
            $totalQuery = "SELECT COUNT(*) as total FROM tb_users";
            $totalStmt = $conn->prepare($totalQuery);
            $totalStmt->execute();
            $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
            $totalData = (int)$totalResult['total'];
            $totalPages = ceil($totalData / $limit);
            $database->disconnect();
            // Response success after proses data from database
            return [
                'success' => true,
                'data' => $users,
                'pagination' => [
                    'current_page' => $page,
                    'total_pages' => $totalPages,
                    'total_data' => $totalData,
                    'data_per_page' => $limit
                ],
                'message' => 'Berhasil mengambil daftar data user'
            ];
        }
        public function getDetailUser($requestBody) {
            // Validation
            if(empty($requestBody['idUser'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'ID user tidak ditemukan'
                ];
            }
            //Process to database
            $database = new Database();
            $conn = $database->connect();
            $query = "SELECT * FROM tb_users WHERE id_user = :id_user";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':id_user', $requestBody['idUser'], PDO::PARAM_INT);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $database->disconnect();
            // Response error after proses data from database
            if(!$user) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Data tidak ditemukan'
                ];
            }
            // Response success after proses data from database
            return [
                'success' => true,
                'data' => $user,
                'message' => 'Berhasil mengambil detail data user'
            ];
        }
        public function createUser($requestBody) {
            // Validation
            if (empty($requestBody['username']) || empty($requestBody['email']) || empty($requestBody['role'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Username, email dan role wajib diisi'
                ];
            }
            $regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
            if (!preg_match($regex, $requestBody['email'])) {
                return [
                    'success' => false,
                    'data' => $requestBody['email'],
                    'message' => 'Format email tidak sesuai'
                ];
            }
            if (
                strlen($requestBody['username']) < 5 || strlen($requestBody['username']) > 30 ||
                strlen($requestBody['email']) < 5 || strlen($requestBody['email']) > 30 ||
                strlen($requestBody['role']) < 5 || strlen($requestBody['role']) > 30
            ) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Username, email, dan role harus memiliki panjang 5-20 karakter'
                ];
            }
            if ($requestBody['role'] === 'LEADER' && empty($requestBody['departement'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Departemen wajib diisi untuk role LEADER'
                ];
            }
            // Process to database
            $database = new Database();
            $conn = $database->connect();
            // Check if email or username already exists
            $checkQuery = "SELECT id_user FROM tb_users WHERE email = :email OR username = :username";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bindValue(':email', $requestBody['email']);
            $checkStmt->bindValue(':username', $requestBody['username']);
            $checkStmt->execute();
            $existingUser = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if ($existingUser) {
                $database->disconnect();
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Email atau username sudah terdaftar dalam database'
                ];
            }
            // Insert new user
            $query = "INSERT INTO tb_users (username, email, password, role, departement)
                        VALUES (:username, :email, :password, :role, :departement)";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':username', $requestBody['username']);
            $stmt->bindValue(':email', $requestBody['email']);
            $stmt->bindValue(':password', password_hash('default12345', PASSWORD_BCRYPT));
            $stmt->bindValue(':role', $requestBody['role']);
            $stmt->bindValue(':departement', $requestBody['departement'] ?? null);
            $responseSaveUser = $stmt->execute();
            $database->disconnect();
            // Response error after process data from database
            if (!$responseSaveUser) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Gagal menambahkan data ke database'
                ];
            }
            // Response success after process data from database
            return [
                'success' => true,
                'data' => [
                    'username' => $requestBody['username'],
                    'email' => $requestBody['email'],
                    'role' => $requestBody['role'],
                    'departement' => $requestBody['departement'] ?? null
                ],
                'message' => 'Berhasil membuat user baru'
            ];
        }
        public function updateUser($requestBody) {
            // Validation
            if(empty($requestBody['idUser'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'ID user tidak ditemukan'
                ];
            }
            if (empty($requestBody['username']) || empty($requestBody['email']) || empty($requestBody['role'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Username, email dan role wajib diisi'
                ];
            }
            $regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
            if (!preg_match($regex, $requestBody['email'])) {
                return [
                    'success' => false,
                    'data' => $requestBody['email'],
                    'message' => 'Format email tidak sesuai'
                ];
            }
            if (
                strlen($requestBody['username']) < 5 || strlen($requestBody['username']) > 30 ||
                strlen($requestBody['email']) < 5 || strlen($requestBody['email']) > 30 ||
                strlen($requestBody['role']) < 5 || strlen($requestBody['role']) > 30
            ) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Username, email, dan role harus memiliki panjang 5-20 karakter'
                ];
            }
            if ($requestBody['role'] === 'LEADER' && empty($requestBody['departement'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Departemen wajib diisi untuk role LEADER'
                ];
            }
            //Process to database
            $database = new Database();
            $conn = $database->connect();
            // Query dengan departement
            $query = "UPDATE tb_users
                        SET username = :username,
                            email = :email,
                            role = :role,
                            departement = :departement
                        WHERE id_user = :id_user";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':username', $requestBody['username']);
            $stmt->bindValue(':email', $requestBody['email']);
            $stmt->bindValue(':role', $requestBody['role']);
            $stmt->bindValue(':departement', $requestBody['departement'] ?? null);
            $stmt->bindValue(':id_user', $requestBody['idUser'], PDO::PARAM_INT);
            $responseUpdateUser = $stmt->execute();
            $database->disconnect();
            // Response error after proses data from database
            if (!$responseUpdateUser) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Gagal melakukan update data user'
                ];
            }
            // Response success after proses data from database
            return [
                'success' => true,
                'data' => null,
                'message' => 'Berhasil melakukan update data user'
            ];
        }
        public function deleteUser($requestBody) {
            // Validation
            if(empty($requestBody['idUser'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'ID user tidak ditemukan'
                ];
            }
            //Process to database
            $database = new Database();
            $conn = $database->connect();
            $query = "DELETE FROM tb_users WHERE id_user = :id_user";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':id_user', $requestBody['idUser']);
            $responseDeleteUser = $stmt->execute();
            $database->disconnect();
            // Response error after proses data from database
            if (!$responseDeleteUser) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Gagal menghapus data user'
                ];
            }
            // Response success after proses data from database
            return [
                'success' => true,
                'data' => '',
                'message' => 'Berhasil menghapus user'
            ];
        }
        public function resetPassword($requestBody) {
            // Validation
            if(empty($requestBody['idUser'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'ID user tidak ditemukan'
                ];
            }
            //Process to database
            $database = new Database();
            $conn = $database->connect();
            $query = "UPDATE tb_users SET password = :password WHERE id_user = :id_user";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':password', password_hash('default12345', PASSWORD_BCRYPT));
            $stmt->bindValue(':id_user', $requestBody['idUser'], PDO::PARAM_INT);
            $responseResetPassword = $stmt->execute();
            $database->disconnect();
            // Response error after proses data from database
            if (!$responseResetPassword) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Gagal melakukan reset password'
                ];
            }
            // Response success after proses data from database
            return [
                'success' => true,
                'data' => $requestBody['idUser'],
                'message' => 'Berhasil, password baru setelah di reset adalah "default12345"'
            ];
        }
        public function changePassword($requestBody) {
            // Validate idUser
            if (empty($requestBody['idUser'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'ID user wajib diisi'
                ];
            }
            // Validation for other fields
            if (empty($requestBody['newPassword']) || empty($requestBody['oldPassword']) || empty($requestBody['confirmationPassword'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Semua field password wajib diisi'
                ];
            }
            // Validate password length and character rules
            $passwordPattern = '/^[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};:\'"\\|,.<>\/?]+$/';
            if (strlen($requestBody['newPassword']) < 6 || !preg_match($passwordPattern, $requestBody['newPassword'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Password baru minimal 6 karakter dan hanya boleh menggunakan huruf (A-Z, a-z), angka (0-9), dan karakter khusus ! @ # $ % ^ & * ( ) _ + - = [ ] { } ; : \" \ | , . < > / ?'
                ];
            }
            // Validate newPassword and oldPassword difference
            if ($requestBody['newPassword'] === $requestBody['oldPassword']) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Password baru tidak boleh sama dengan password lama'
                ];
            }
            // Validate confirmationPassword matches newPassword
            if ($requestBody['newPassword'] !== $requestBody['confirmationPassword']) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Konfirmasi password harus sama dengan password baru'
                ];
            }
            // Process to database
            $database = new Database();
            $conn = $database->connect();
            // Fetch the old password from the database
            $querySelect = "SELECT password FROM tb_users WHERE id_user = :id_user";
            $stmtSelect = $conn->prepare($querySelect);
            $stmtSelect->bindValue(':id_user', $requestBody['idUser'], PDO::PARAM_INT);
            $stmtSelect->execute();
            $user = $stmtSelect->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                $database->disconnect();
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'ID user tidak ditemukan'
                ];
            }
            // Verify old password
            if (!password_verify($requestBody['oldPassword'], $user['password'])) {
                $database->disconnect();
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Password lama tidak sesuai'
                ];
            }
            // Update the password in the database
            $queryUpdate = "UPDATE tb_users SET password = :password WHERE id_user = :id_user";
            $stmtUpdate = $conn->prepare($queryUpdate);
            $stmtUpdate->bindValue(':password', password_hash($requestBody['newPassword'], PASSWORD_BCRYPT));
            $stmtUpdate->bindValue(':id_user', $requestBody['idUser'], PDO::PARAM_INT);
            $responseUpdate = $stmtUpdate->execute();
            $database->disconnect();
            // Response error after processing data from database
            if (!$responseUpdate) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Gagal mengubah password'
                ];
            }
            // Response success after processing data from database
            return [
                'success' => true,
                'data' => $requestBody['idUser'],
                'message' => 'Password berhasil diubah'
            ];
        }
    }
?>
