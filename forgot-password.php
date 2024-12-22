<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/main.css">
    <link rel="stylesheet" href="./assets/sweetalert2/dist/sweetalert2.min.css">
    <title>Attendance PT. Sanden</title>
</head>
<body>
    <div id="login">
        <div class="container vh-100 d-flex flex-column justify-content-center">
            <div class="d-flex justify-content-center">
                <div id="form-login">
                    <div class="card">
                        <div class="card-header bg-secondary">
                            <div class="text-white p-3 fs-5">
                                Sistem Manajemen Absensi Karyawan
                            </div>
                        </div>
                        <input id="response-username" type="hidden">
                        <input id="response-email" type="hidden">
                        <div class="card-body">
                            <form id="form-request-change-password" action="" class="p-3" onsubmit="return requestChangePassword(event)">
                                <label for="forgot-password" class="form-label fw-bold">Lupa Password</label>
                                <input id="email" class="form-control mb-3" type="text" placeholder="Masukan email atau username" aria-label="email">
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary" onclick="requestChangePassword(event)">Kirim</button>
                                    <a href="/attendance/index.php" class="text-decoration-none">Kembali kehalaman login ?</a>
                                </div>
                            </form>
                            <form id="form-change-password-by-request" action="" class="p-3" onsubmit="return changePasswordByRequest(event)">
                                <label for="forgot-password" class="form-label fw-bold">Masukan Kata sandi</label>
                                <input id="new-password" class="form-control mb-3" type="password" placeholder="Masukan kata sandi baru" aria-label="password">
                                <input id="confirmation-password" class="form-control mb-3" type="password" placeholder="Konfirmasi kata sandi baru" aria-label="password">
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary" onclick="changePasswordByRequest(event)">Simpan</button>
                                    <a href="/attendance/index.php" class="text-decoration-none">Kembali kehalaman login ?</a>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="p-2 text-black-50">
                                &copy; PT. Sanden Electronics Indonesia
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script src="./assets/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="./assets/@popperjs/core/dist/umd/popper.min.js"></script>
<script src="./assets/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="./utils/swalAlert.js"></script>
<script>
    document.getElementById('form-change-password-by-request').style.display = 'none';
    
    async function requestChangePassword (event) {
        event.preventDefault();
        const email = document.getElementById('email').value
        const username = document.getElementById('email').value
        if(!email || !username) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data email atau username wajib di isi.')
        }
        const responseRequestChangePassword = await fetch('http://localhost/attendance/api/auth.php/request-change-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                email: username,
            })
        }).then(response => response.json())

        if(!responseRequestChangePassword.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseRequestChangePassword.message)
        }
        SwalAlert.success(responseRequestChangePassword.message)
        document.getElementById('form-request-change-password').style.display = 'none';
        document.getElementById('form-change-password-by-request').style.display = 'block';
        document.getElementById('response-username').value = responseRequestChangePassword.data.username
        document.getElementById('response-email').value = responseRequestChangePassword.data.email
    }
    async function changePasswordByRequest(event) {
        event.preventDefault();
        const username = document.getElementById('response-username').value
        const email = document.getElementById('response-email').value
        const newPassword = document.getElementById('new-password').value
        const confirmationPassword = document.getElementById('confirmation-password').value

        if(!email || !username) {
            return SwalAlert.warning('Data tidak lengkap!', 'Anda belum melakukan permintaan pergantian kata sandi')
        }
        if(!newPassword || !confirmationPassword) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data kata sandi baru dan konfirmasi kata sandi wajib diisi')
        }
        if(newPassword !== confirmationPassword) {
            return SwalAlert.warning('Terjadi kesalahan!', 'Kata sandi baru dan konfirmasi kata sandi tidak sama')
        }
        const responseChangePasswordByRequest = await fetch('/attendance/api/auth.php/change-password-by-request', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                email: email,
                newPassword: newPassword,
                confirmationPassword: confirmationPassword
            })
        }).then(response => response.json())
        if(!responseChangePasswordByRequest.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseChangePasswordByRequest.message)
        }
        SwalAlert.success(responseChangePasswordByRequest.message)
        setTimeout(() => {
            Swal.close()
            window.location.href = '/attendance/';
        }, 1000);
    }
</script>
</body>
</html>