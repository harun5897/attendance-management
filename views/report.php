<?php
    session_start();
    require_once '../middleware/middleware.php';
    $middleware = new Middleware();
    $middleware->middlewarePage();
    if($_SESSION['role'] == 'MANAGER' && $_SESSION['role'] == 'ADMIN') {
        header("Location: /attendance/index.php");
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/main.css">
    <link rel="stylesheet" href="../assets/sweetalert2/dist/sweetalert2.min.css">
    <title>Attendance PT. Sanden</title>
</head>
<body>
    <div id="page-user">
        <input id="session_id_user" type="hidden" value="<?=$_SESSION['id_user']?>">
        <input id="session_username" type="hidden" value="<?=$_SESSION['username']?>">
        <input id="session_email" type="hidden" value="<?=$_SESSION['email']?>">
        
        <?php include_once '../components/Navbar.php'; ?>
        <div class="vh-100 d-flex">
            <?php include_once '../components/Sidebar.php'; ?>
            <div class="w-100">
                <br><br><br>
                <div id="content-page-views" class="px-5 py-2">
                    <h3>Kelola Laporan</h3>
                    <hr>
                    <div class="card w-50 shadow">
                        <form action="" onsubmit="return printReport(event)">
                            <div class="card-body mx-3 my-3">
                                <div class="mb-3">
                                    <label for="start-date" class="form-label fw-bold">Tanggal Mulai</label>
                                    <input type="date" class="form-control" id="start-date">
                                </div>
                                <div class="mb-3">
                                    <label for="end-date" class="form-label fw-bold">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="end-date">
                                </div>
                                <div class="mb-3">
                                    <label for="employee-attendance" class="form-label fw-bold">Kehadiran Karyawan</label>
                                    <select id="employee-attendance" class="form-select mb-3" aria-label="Default select example">
                                        <option value="">Pilih kehadiran karyawan</option>
                                        <option value="hadir">Hadir</option>
                                        <option value="tidak_hadir">Tidak Hadir</option>
                                        <option value="semua_data">Semua Data</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="departement" class="form-label fw-bold">Departemen</label>
                                    <select id="departement" class="form-select mb-3" aria-label="Default select example">
                                        <option value="">Pilih Departemen</option>
                                        <option value="M2">M2</option>
                                        <option value="TM">TM</option>
                                        <option value="SM">SM</option>
                                        <option value="QA">QA</option>
                                        <option value="PURCHASING">PURCHASING</option>
                                        <option value="PRODUCTION">PRODUCTION</option>
                                        <option value="PE">PE</option>
                                        <option value="ME">ME</option>
                                        <option value="MCA">MCA</option>
                                        <option value="M1">M1</option>
                                        <option value="LOGISTIC">LOGISTIC</option>
                                        <option value="ADMINISTRATION">ADMINISTRATION</option>
                                        <option value="ACCOUNTING">ACCOUNTING</option>
                                        <option value="semua_departemen">SEMUA DEPARTEMEN</option>
                                    </select>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary shadow rounded-0">Cetak Laporan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="../assets/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/@popperjs/core/dist/umd/popper.min.js"></script>
<script src="../assets/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="../utils/swalAlert.js"></script>

<script>
    function printReport(event) {
        event.preventDefault();
        const startDate = document.getElementById('start-date').value
        const endDate = document.getElementById('end-date').value
        const employeeAttendance = document.getElementById('employee-attendance').value
        const departement = document.getElementById('departement').value

        if(!startDate) {
            return SwalAlert.warning('Data tidak lengkap!', 'Tanggal mulai wajib diisi.')
        }
        if(!endDate) {
            return SwalAlert.warning('Data tidak lengkap!', 'Tanggal akhir wajib diisi.')
        }
        if(!employeeAttendance) {
            return SwalAlert.warning('Data tidak lengkap!', 'Kehadiran karyawan wajib dipilih')
        }
        if (new Date(startDate) > new Date(endDate)) {
            return SwalAlert.warning('Data tidak valid!', 'Tanggal mulai harus lebih kecil dari tanggal akhir.');
        }
        if(!departement) {
            return SwalAlert.warning('Data tidak lengkap!', 'Departemen wajib dipilih')
        }
        const url = `/attendance/views/file-report.php?start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}&employee_attendance=${encodeURIComponent(employeeAttendance)}&departement=${encodeURIComponent(departement)}`;
        window.location.href = url;
    }
</script>
</body>
</html>