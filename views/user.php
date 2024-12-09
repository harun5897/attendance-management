<?php
    session_start();
    require_once '../middleware/middleware.php';
    $middleware = new Middleware();
    $middleware->middlewarePage();
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
            <div id="sidebar" class="w-25 border-end border-2 shadow-lg">
                <br><br><br>
                <div
                    class="sidebar-menu w-100 ps-3 py-3 border-bottom border-black"
                    onclick="window.location.href='#';"
                    tabindex="0"
                    role="button"
                >
                    Data Pengguna
                </div>
                <div
                    class="sidebar-menu w-100 ps-3 py-3 border-bottom border-black"
                    onclick="window.location.href='#';"
                    tabindex="0"
                    role="button"
                >
                    Data Karyawan
                </div>
                <div
                    class="sidebar-menu w-100 ps-3 py-3 border-bottom border-black"
                    onclick="window.location.href='#';"
                    tabindex="0"
                    role="button"
                >
                    Data Absensi
                </div>
                <div
                    class="sidebar-menu w-100 ps-3 py-3 border-bottom border-black"
                    onclick="window.location.href='#';"
                    tabindex="0"
                    role="button"
                >
                    Laporan
                </div>
                <div
                    class="sidebar-menu w-100 ps-3 py-3 border-bottom border-black"
                    onclick="logout()"
                    tabindex="0"
                    role="button"
                >
                    Keluar
                </div>
            </div>
            <div class="w-100">
                <h1>Content</h1>
            </div>
        </div>
    </div>
<script src="../assets/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/@popperjs/core/dist/umd/popper.min.js"></script>
<script src="../assets/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="../utils/swalAlert.js"></script>
<script>
    async function logout() {
        const idUser = document.getElementById('session_id_user').value
        const username = document.getElementById('session_username').value
        const email =  document.getElementById('session_email').value

        if(!idUser || !username || !email) {
            return SwalAlert.warning('Terjadi kesalahan', 'Syarat untuk logout tidak lengkap, silahkan ulangi beberapa saat lagi.')
        }
        const responseLogout = await fetch('http://localhost/attendance/api/auth.php/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                email: username,
                id_user: idUser
            })
        }).then(response => response.json())
        if(!responseLogout.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseLogin.message)
        }
        SwalAlert.success(responseLogout.message)
        setTimeout(() => {
            Swal.close()
            window.location.href = '/attendance/index.php';
        }, 1000);
    }
</script>
</body>
</html>