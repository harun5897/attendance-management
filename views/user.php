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
                    <h3>Data Pengguna</h3>
                    <hr>
                    <button type="button" class="btn btn-primary btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-user">
                        Tambah User
                    </button>
                    <!-- Modal Create User -->
                    <div class="modal fade" id="modal-user" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-secondary text-white">
                                    <h5 class="modal-title" id="exampleModalLabel">Form Data User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="" onsubmit="return saveUser(event)">
                                        <input id="username" class="form-control mb-3" type="text" placeholder="Masukan username" aria-label="username">
                                        <input id="email" class="form-control mb-3" type="text" placeholder="Masukan email" aria-label="email">
                                        <select id="role" class="form-select" aria-label="Default select example">
                                            <option value="">Pilih Role</option>
                                            <option value="admin">Admin</option>
                                            <option value="leader">Leader</option>
                                            <option value="manager">Manager</option>
                                        </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary" onclick="saveUser(event)">Simpan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-sm shadow-lg" style="font-size: 14px !important;">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">No</th>
                                <th scope="col">Username</th>
                                <th scope="col">Email</th>
                                <th scope="col">Role</th>
                                <th scope="col" style="width: 300px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="user-table-body">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<script src="../assets/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/@popperjs/core/dist/umd/popper.min.js"></script>
<script src="../assets/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="../utils/swalAlert.js"></script>

<script>
    getUser()
    async function getUser() {
        const responseGetUser = await fetch('/attendance/api/user.php/user', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            },
        }).then(response => response.json())
        let html_data = '';
        if (responseGetUser.data.length == 0 || !responseGetUser.success) {
            html_data += '<tr><td colspan="5" class="text-center">Data tidak tersedia</td></tr>';
            document.getElementById('user-table-body').innerHTML = html_data;
            return
        }
        responseGetUser.data.forEach((user, index) => {
            html_data += '<tr>';
            html_data += `<th scope="row" class="text-center">${index + 1}</th>`;
            html_data += `<td>${user.username}</td>`;
            html_data += `<td>${user.email}</td>`;
            html_data += `<td><span class="badge text-bg-success">${user.role}</span></td>`;
            html_data += '<td>';
            html_data += `<button type="button" class="btn btn-sm btn-warning me-1">Edit</button>`;
            html_data += `<button type="button" class="btn btn-sm btn-danger me-1" onclick="deleteUser(${user.id_user})">Hapus</button>`;
            html_data += `<button type="button" class="btn btn-sm btn-info">Reset Password</button>`;
            html_data += '</td>';
            html_data += '</tr>';
        });
        document.getElementById('user-table-body').innerHTML = html_data;
    }
    async function saveUser(event) {
        event.preventDefault();
        const username = document.getElementById('username').value
        const email = document.getElementById('email').value
        const role =  document.getElementById('role').value

        if(!username) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data username wajib di isi.')
        }
        if(!email) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data email wajib di isi.')
        }
        if(!role) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data role wajib di isi.')
        }
        const responseSaveUser = await fetch('/attendance/api/user.php/user', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                email: email,
                role: role
            })
        }).then(response => response.json())
        if(!responseSaveUser.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseSaveUser.message)
        }
        SwalAlert.success(responseSaveUser.message)
        setTimeout(() => {
            Swal.close()
            window.location.href = '/attendance/views/user.php';
        }, 1000);
    }
    async function deleteUser(idUser) {
        if(!idUser) {
            return SwalAlert.warning('Terjadi kesalahan!', 'ID user tidak ditemukan')
        }
        const responseDeleteUser = await fetch('/attendance/api/user.php/user', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                idUser: idUser,
            })
        }).then(response => response.json())
        if(!responseDeleteUser.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseDeleteUser.message)
        }
        SwalAlert.success(responseDeleteUser.message)
        setTimeout(() => {
            Swal.close()
            window.location.href = '/attendance/views/user.php';
        }, 1000);
    }
</script>
</body>
</html>