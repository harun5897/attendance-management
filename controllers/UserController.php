<?php
    require_once '../databases/database.php';
    class UserController {
        public function getUser() {
            return [
                'success' => true,
                'data' => '',
                'message' => 'Berhasil'
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
                'message' => 'Berhasil'
            ];
        }
        public function updateUser() {
            return [
                'success' => true,
                'data' => '',
                'message' => 'Berhasil'
            ];
        }
        public function deleteUser() {
            return [
                'success' => true,
                'data' => '',
                'message' => 'Berhasil'
            ];
        }
    }
?>
