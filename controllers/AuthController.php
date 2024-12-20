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
