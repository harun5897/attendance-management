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
            //Process to database
            $database = new Database();
            $conn = $database->connect();
            $query = "INSERT INTO tb_users (username, email, password, role) VALUES (:username, :email, :password, :role)";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':username', $requestBody['username']);
            $stmt->bindValue(':email', $requestBody['email']);
            $stmt->bindValue(':password', password_hash('default12345', PASSWORD_BCRYPT));
            $stmt->bindValue(':role', $requestBody['role']);
            $responseSaveUser = $stmt->execute();
            $database->disconnect();
            // Response error after proses data from database
            if (!$responseSaveUser) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Gagal menambahkan data ke database'
                ];
            }
            // Response success after proses data from database
            return [
                'success' => true,
                'data' => [
                    'username' => $requestBody['username'],
                    'email' => $requestBody['email'],
                    'role' => $requestBody['role']
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
            //Process to database
            $database = new Database();
            $conn = $database->connect();
            $query = "UPDATE tb_users SET username = :username, email = :email, role = :role WHERE id_user = :id_user";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':username', $requestBody['username']);
            $stmt->bindValue(':email', $requestBody['email']);
            $stmt->bindValue(':role', $requestBody['role']);
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
            $stmt->bindValue(':password', password_hash('12345', PASSWORD_BCRYPT));
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
                'message' => 'Berhasil, password baru setelah di reset adalah "12345"'
            ];
        }
    }
?>
