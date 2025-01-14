<div id="sidebar" class="w-25 border-end border-2 shadow-lg">
    <br><br><br>
    <?php if($_SESSION['role'] == 'ADMIN'): ?>
    <div
        class="sidebar-menu w-100 ps-3 py-3 border-bottom border-black"
        onclick="window.location.href='/attendance/views/user.php';"
        tabindex="0"
        role="button"
    >
        Data Pengguna
    </div>
    <?php endif; ?>
    <?php if($_SESSION['role'] == 'LEADER' || $_SESSION['role'] == 'ADMIN'): ?>
    <div
        class="sidebar-menu w-100 ps-3 py-3 border-bottom border-black"
        onclick="window.location.href='/attendance/views/employee.php';"
        tabindex="0"
        role="button"
    >
        Data Karyawan
    </div>
    <?php endif; ?>
    <?php if($_SESSION['role'] == 'LEADER' || $_SESSION['role'] == 'ADMIN'): ?>
    <div
        class="sidebar-menu w-100 ps-3 py-3 border-bottom border-black"
        onclick="window.location.href='/attendance/views/attendance.php';"
        tabindex="0"
        role="button"
    >
        Data Absensi
    </div>
    <?php endif; ?>
    <?php if($_SESSION['role'] == 'MANAGER' || $_SESSION['role'] == 'ADMIN'): ?>
    <div
        class="sidebar-menu w-100 ps-3 py-3 border-bottom border-black"
        onclick="window.location.href='/attendance/views/report.php';"
        tabindex="0"
        role="button"
    >
        Laporan
    </div>
    <?php endif; ?>
    <div
        class="sidebar-menu w-100 ps-3 py-3 border-bottom border-black"
        onclick="window.location.href='/attendance/views/change-password.php';"
        tabindex="0"
        role="button"
    >
        Ganti Kata Sandi
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