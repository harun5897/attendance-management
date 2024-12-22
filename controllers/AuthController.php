<?php
    require_once '../databases/database.php';
    class AuthController {
        public function login($requestBody) {
            // Validation
            if (empty($requestBody['email']) || empty($requestBody['username']) || empty($requestBody['password'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Email, username dan password wajib diisi'
                ];
            }
            //Process to database
            $database = new Database();
            $conn = $database->connect();
            $query = "SELECT * FROM tb_users WHERE email = :email OR username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':email', $requestBody['email']);
            $stmt->bindValue(':username', $requestBody['username']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $database->disconnect();
            // Response error after proses data from database
            if (!$user || !password_verify($requestBody['password'], $user['password'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Email atau password yang anda masukkan salah'
                ];
            }
            // Set session
            session_start();
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['departement'] = $user['departement'];
            // Response success after proses data from database
            return [
                'success' => true,
                'data' => $user,
                'message' => 'Berhasil Login'
            ];
        }
        public function requestChangePassword($requestBody) {
            // Validation
            if (empty($requestBody['email']) || empty($requestBody['username'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Email dan username wajib diisi'
                ];
            }
            // Process to database
            $database = new Database();
            $conn = $database->connect();
            $query = "SELECT email, username FROM tb_users WHERE email = :email OR username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindValue(':email', $requestBody['email']);
            $stmt->bindValue(':username', $requestBody['username']);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $database->disconnect();
            // Response error if no data found
            if (!$user) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Email atau username tidak ditemukan'
                ];
            }
            // Set session
            session_start();
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['changePassword'] = true;
            // Response success with email and username
            return [
                'success' => true,
                'data' => [
                    'email' => $user['email'],
                    'username' => $user['username']
                ],
                'message' => 'Data ditemukan silahkan lakukan pergantian kata sandi'
            ];
        }
        public function changePasswordByRequest($requestBody) {
            // Validate email and username
            if (empty($requestBody['email']) || empty($requestBody['username'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Email dan username wajib diisi'
                ];
            }
            // Validate password fields
            if (empty($requestBody['newPassword']) || empty($requestBody['confirmationPassword'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Password baru dan konfirmasi password wajib diisi'
                ];
            }
            // Validate password length and character rules
            $passwordPattern = '/^[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};:\'"\\|,.<>\/\?]+$/';
            if (strlen($requestBody['newPassword']) < 6 || !preg_match($passwordPattern, $requestBody['newPassword'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Password baru minimal 6 karakter dan hanya boleh menggunakan huruf (A-Z, a-z), angka (0-9), dan karakter khusus ! @ # $ % ^ & * ( ) _ + - = [ ] { } ; : " \ | , . < > / ?'
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
            // Check session
            session_start();
            if ($_SESSION['username'] !== $requestBody['username'] || $_SESSION['email'] !== $requestBody['email'] || !isset($_SESSION['changePassword']) || $_SESSION['changePassword'] !== true) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Tidak memiliki izin untuk mengganti password'
                ];
            }
            // Process to database
            $database = new Database();
            $conn = $database->connect();
            // Fetch the old password from the database
            $querySelect = "SELECT password FROM tb_users WHERE email = :email AND username = :username";
            $stmtSelect = $conn->prepare($querySelect);
            $stmtSelect->bindValue(':email', $requestBody['email']);
            $stmtSelect->bindValue(':username', $requestBody['username']);
            $stmtSelect->execute();
            $user = $stmtSelect->fetch(PDO::FETCH_ASSOC);
            if (!$user) {
                $database->disconnect();
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Email atau username tidak ditemukan'
                ];
            }
            // Update the password in the database
            $queryUpdate = "UPDATE tb_users SET password = :password WHERE email = :email AND username = :username";
            $stmtUpdate = $conn->prepare($queryUpdate);
            $stmtUpdate->bindValue(':password', password_hash($requestBody['newPassword'], PASSWORD_BCRYPT));
            $stmtUpdate->bindValue(':email', $requestBody['email']);
            $stmtUpdate->bindValue(':username', $requestBody['username']);
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
                'data' => [
                    'email' => $requestBody['email'],
                    'username' => $requestBody['username']
                ],
                'message' => 'Password berhasil diubah'
            ];
        }
        public function logout($requestBody) {
             // Validation
            if (empty($requestBody['email']) || empty($requestBody['username']) || empty($requestBody['id_user'])) {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => 'Syarat untuk logout tidak lengkap, silahkan ulangi beberapa saat lagi.'
                ];
            }
            // Remove session
            session_start();
            session_destroy();
            // Response success destory session
            return [
                'success' => true,
                'data' => null,
                'message' => 'Berhasil Logut'
            ];
        }
    }
?>
