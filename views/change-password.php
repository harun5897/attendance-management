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
            <?php include_once '../components/Sidebar.php'; ?>
            <div class="w-100">
                <br><br><br>
                <div id="content-page-views" class="px-5 py-2">
                    <h3>Ganti Kata Sandi</h3>
                    <hr>
                    <div class="card w-50 shadow">
                        <form action="" onsubmit="return changePassword(event)">
                            <div class="card-body mx-3 my-3">
                                <div class="mb-3">
                                    <label for="old-password" class="form-label fw-bold">Kata Sandi Lama</label>
                                    <input type="password" class="form-control" id="old-password" placeholder="Masukan kata sandi lama">
                                </div>
                                <div class="mb-3">
                                    <label for="new-password" class="form-label fw-bold">Kata Sandi Baru</label>
                                    <input type="password" class="form-control" id="new-password" placeholder="Masukan kata sandi baru">
                                </div>
                                <div class="mb-3">
                                    <label for="confirmation-password" class="form-label fw-bold">Konfirmasi Kata Sandi</label>
                                    <input type="password" class="form-control" id="confirmation-password" placeholder="Masukan kata sandi baru">
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary shadow rounded-0">Simpan</button>
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
    async function changePassword(event) {
        event.preventDefault();
        const idUser = document.getElementById('session_id_user').value
        const oldPassword = document.getElementById('old-password').value
        const newPassword = document.getElementById('new-password').value
        const confirmationPassword = document.getElementById('confirmation-password').value

        if(!oldPassword) {
            return SwalAlert.warning('Data tidak lengkap!', 'Kata sandi lama wajib diisi')
        }
        if(!newPassword) {
            return SwalAlert.warning('Data tidak lengkap!', 'Kata sandi baru wajib diisi')
        }
        if(!confirmationPassword) {
            return SwalAlert.warning('Data tidak lengkap!', 'Konfirmasi Kata sandi wajib diisi')
        }
        if(confirmationPassword !== newPassword) {
            return SwalAlert.warning('Terjadi kesalahan!', 'Kata sandi baru dan konfirmasi kata sandi harus sama')
        }
        if(oldPassword === newPassword || oldPassword == confirmationPassword) {
            return SwalAlert.warning('Terjadi kesalahan!', 'Kata sandi baru tidak boleh sama dengan kata sandi lama')
        }
        if(!idUser) {
            return SwalAlert.warning('Terjadi kesalahan!', 'ID user tidak ditemukan')
        }
        const responseChangePassword = await fetch('/attendance/api/user.php/change-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                idUser: idUser,
                oldPassword: oldPassword,
                newPassword: newPassword,
                confirmationPassword: confirmationPassword
            })
        }).then(response => response.json())
        if(!responseChangePassword.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseChangePassword.message)
        }
        SwalAlert.success(responseChangePassword.message,)
        setTimeout(() => {
            Swal.close()
            window.location.href = '/attendance/views/change-password.php';
        }, 1000);
    }
</script>
</body>
</html>