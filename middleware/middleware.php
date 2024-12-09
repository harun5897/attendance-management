<?php
    class Middleware {
        public function __construct() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
        }
        public function middlewareApi () {
            if (!isset($_SESSION['id_user']) || !isset($_SESSION['username']) || !isset($_SESSION['email'])) {
                echo json_encode([
                    'success' => false,
                    'data' => null,
                    'message' => 'User belum login'
                ]);
                exit();
            }
        }
        public function middlewarePage () {
            if (!isset($_SESSION['id_user']) || !isset($_SESSION['username']) || !isset($_SESSION['email'])) {
                header("Location: /attendance/index.php");
                exit();
            }
        }
    }
?>