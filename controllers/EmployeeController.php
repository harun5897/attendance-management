<?php
    require_once '../databases/database.php';
    class EmployeeController {
        public function getEmployee() {
            return [
                'success' => true,
                'data' => null,
                'message' => 'Berhasil mengambil data karyawan'
            ];
        }
        public function getDetailEmployee() {
            return [
                'success' => true,
                'data' => null,
                'message' => 'Berhasil mengambil data detail karyawan'
            ];
        }
        public function createEmployee() {
            return [
                'success' => true,
                'data' => null,
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
        public function deleteEmployee() {
            return [
                'success' => true,
                'data' => null,
                'message' => 'Berhasil melakukan hapus data karyawan'
            ];
        }
    }
?>
