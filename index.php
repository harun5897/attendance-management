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
                        <div class="card-body">
                            <form action="" class="p-3" onsubmit="return login(event)">
                                <input id="email" class="form-control mb-3" type="text" placeholder="Masukan email atau username" aria-label="email">
                                <input id="password" class="form-control mb-3" type="password" placeholder="Masukan Password" aria-label="email">
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary" onclick="login(event)">Masuk</button>
                                    <a href="#" class="text-decoration-none">Lupa password ?</a>
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
    async function login (event) {
        event.preventDefault();
        const email = document.getElementById('email').value
        const username = document.getElementById('email').value
        const password = document.getElementById('password').value

        if(!email) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data email atau username wajib di isi.')
        }
        if(!email) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data password wajib di isi.')
        }

        const responseLogin = await fetch('http://localhost/attendance/api/auth.php/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                email: username,
                password: password
            })
        }).then(response => response.json())

        if(!responseLogin.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseLogin.message)
        }
        SwalAlert.success(responseLogin.message)
        setTimeout(() => {
            Swal.close()
            window.location.href = '/attendance/views/user.php';
        }, 1000);
    }
</script>
</body>
</html>