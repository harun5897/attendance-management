<?php
    session_start();
    require_once '../middleware/middleware.php';
    $middleware = new Middleware();
    $middleware->middlewarePage();
    if($_SESSION['role'] != 'ADMIN') {
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
                    <h3>Data Pengguna</h3>
                    <hr>
                    <button type="button" class="btn btn-primary btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-create-user">
                        Tambah User
                    </button>
                    <!-- Modal Create User -->
                    <div class="modal fade" id="modal-create-user" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                        <select id="role" class="form-select mb-3" aria-label="Default select example">
                                            <option value="">Pilih Role</option>
                                            <option value="ADMIN">Admin</option>
                                            <option value="LEADER">Leader</option>
                                            <option value="MANAGER">Manager</option>
                                        </select>
                                        <select id="departement" class="form-select" aria-label="Default select example">
                                            <option value="">Pilih Departemen</option>
                                            <option value="M2">M2</option>
                                            <option value="TM">TM</option>
                                            <option value="SM">SM</option>
                                            <option value="QA">QA</option>
                                            <option value="PURCHASING">PURCHASING</option>
                                            <option value="PRODUCTION">PRODUCTION</option>
                                            <option value="PACKING">PE</option>
                                            <option value="ME">ME</option>
                                            <option value="MCA">MCA</option>
                                            <option value="M1">M1</option>
                                            <option value="LOGISTIC">LOGISTIC</option>
                                            <option value="ADMINISTRATION">ADMINISTRATION</option>
                                            <option value="ACCOUNTING">ACCOUNTING</option>
                                        </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-secondary" onclick="saveUser(event)">Simpan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Edit User -->
                    <div class="modal fade" id="modal-edit-user" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title" id="exampleModalLabel">Form Edit User</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="" onsubmit="return updateUser(event)">
                                        <input id="edit-id-user" type="hidden">
                                        <input id="edit-username" class="form-control mb-3" type="text" placeholder="Masukan username" aria-label="username">
                                        <input id="edit-email" class="form-control mb-3" type="text" placeholder="Masukan email" aria-label="email">
                                        <select id="edit-role" class="form-select mb-3" aria-label="Default select example">
                                            <option value="">Pilih Role</option>
                                            <option value="ADMIN">Admin</option>
                                            <option value="LEADER">Leader</option>
                                            <option value="MANAGER">Manager</option>
                                        </select>
                                        <select id="edit-departement" class="form-select" aria-label="Default select example">
                                            <option value="">Pilih Departemen</option>
                                            <option value="M2">M2</option>
                                            <option value="TM">TM</option>
                                            <option value="SM">SM</option>
                                            <option value="QA">QA</option>
                                            <option value="PURCHASING">PURCHASING</option>
                                            <option value="PRODUCTION">PRODUCTION</option>
                                            <option value="PACKING">PE</option>
                                            <option value="ME">ME</option>
                                            <option value="MCA">MCA</option>
                                            <option value="M1">M1</option>
                                            <option value="LOGISTIC">LOGISTIC</option>
                                            <option value="ADMINISTRATION">ADMINISTRATION</option>
                                            <option value="ACCOUNTING">ACCOUNTING</option>
                                        </select>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-warning" onclick="updateUser(event)">Simpan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-sm shadow-lg" style="font-size: 13px !important;">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">No</th>
                                <th scope="col">Username</th>
                                <th scope="col">Email</th>
                                <th scope="col">Role</th>
                                <th scope="col">Departemen</th>
                                <th scope="col" style="width: 280px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="user-table-body">
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation example">
                        <input type="hidden" id="total-pages">
                        <input type="hidden" id="current-page" value="1">
                        <ul class="pagination">
                            <li class="page-item"><a class="page-link" href="#" onclick="getUser('prev')">Previous</a></li>
                            <li class="page-item"><span id="display-current-page" class="page-link">1</span></li>
                            <li class="page-item"><a class="page-link" href="#" onclick="getUser('next')">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
<script src="../assets/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/@popperjs/core/dist/umd/popper.min.js"></script>
<script src="../assets/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="../utils/swalAlert.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        function toggleDepartementVisibility(roleSelectId, departementSelectId) {
            const roleSelect = document.getElementById(roleSelectId);
            const departementSelect = document.getElementById(departementSelectId);
            roleSelect.addEventListener("change", function () {
                const selectedRole = roleSelect.value.toUpperCase();
                if (selectedRole === "LEADER") {
                    departementSelect.style.display = "block";
                } else {
                    departementSelect.style.display = "none";
                    departementSelect.value = "";
                }
            });
            departementSelect.style.display = "none";
        }
        toggleDepartementVisibility("role", "departement");
        toggleDepartementVisibility("edit-role", "edit-departement");
    });
    getUser()
    async function getUser(navigationPage) {
        const totalPages = document.getElementById('total-pages').value;
        const currentPage = document.getElementById('current-page').value;
        let page = currentPage;
        if(navigationPage == 'prev' && parseInt(currentPage) > 1) {
            page = parseInt(currentPage) - 1;
        }
        if(navigationPage == 'next' && parseInt(currentPage) < parseInt(totalPages)) {
            page = parseInt(currentPage) + 1;
        }
        const responseGetUser = await fetch('/attendance/api/user.php/get-user', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                page: page,
            })
        }).then(response => response.json())
        let html_data = '';
        if (responseGetUser.data.length == 0 || !responseGetUser.success) {
            html_data += '<tr><td colspan="5" class="text-center">Data tidak tersedia</td></tr>';
            document.getElementById('user-table-body').innerHTML = html_data;
            return
        }
        const dataPerPage = responseGetUser.pagination.data_per_page;
        responseGetUser.data.forEach((user, index) => {
            const rowNumber = (page - 1) * dataPerPage + (index + 1);
            html_data += '<tr>';
            html_data += `<th scope="row" class="text-center">${rowNumber}</th>`;
            html_data += `<td>${user.username}</td>`;
            html_data += `<td>${user.email}</td>`;
            html_data += `<td><span class="badge text-bg-success">${user.role}</span></td>`;
            html_data += `<td><span class="badge text-bg-success">${!user.departement || user.departement == '' ? '-' : user.departement}</span></td>`;
            html_data += '<td>';
            html_data += `<button type="button" class="btn btn-sm btn-warning me-1" style="font-size: 12px;" onclick="getUserDetail(${user.id_user})">Edit</button>`;
            html_data += `<button type="button" class="btn btn-sm btn-danger me-1" style="font-size: 12px;" onclick="deleteUser(${user.id_user})">Hapus</button>`;
            html_data += `<button type="button" class="btn btn-sm btn-info" style="font-size: 12px;" onclick="resetPassword(${user.id_user})">Reset Password</button>`;
            html_data += '</td>';
            html_data += '</tr>';
        });
        document.getElementById('user-table-body').innerHTML = html_data;
        document.getElementById('current-page').value = responseGetUser.pagination.current_page;
        document.getElementById('display-current-page').innerHTML = responseGetUser.pagination.current_page;
        document.getElementById('total-pages').value = responseGetUser.pagination.total_pages;
    }
    async function saveUser(event) {
        event.preventDefault();
        const username = document.getElementById('username').value
        const email = document.getElementById('email').value
        const role =  document.getElementById('role').value
        const departement = document.getElementById('departement').value;

        if(!username) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data username wajib di isi.')
        }
        if(!email) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data email wajib di isi.')
        }
        if(!role) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data role wajib di isi.')
        }
        if (role === "LEADER" && !departement) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data departemen wajib diisi untuk role LEADER.');
        }
        const responseSaveUser = await fetch('/attendance/api/user.php/create-user', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                email: email,
                role: role,
                departement: departement ?? null,
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
        const responseDeleteUser = await fetch('/attendance/api/user.php/delete-user', {
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
    async function getUserDetail(idUser) {
        if(!idUser) {
            return SwalAlert.warning('Terjadi kesalahan!', 'ID user tidak ditemukan')
        }
        const responseDetailUser = await fetch('/attendance/api/user.php/detail-user', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                idUser: idUser,
            })
        }).then(response => response.json())
        if(!responseDetailUser.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseDetailUser.message)
        }
        const myModal = new bootstrap.Modal(document.getElementById("modal-edit-user"));
        myModal.show();
        const detailUser = responseDetailUser.data
        document.getElementById('edit-id-user').value = detailUser.id_user
        document.getElementById('edit-username').value = detailUser.username
        document.getElementById('edit-email').value = detailUser.email
        document.getElementById('edit-role').value = detailUser.role
        const editDepartement = document.getElementById('edit-departement');
        if (detailUser.role.toUpperCase() === "LEADER") {
            editDepartement.style.display = "block";
            editDepartement.value = detailUser.departement
        } else {
            editDepartement.style.display = "none";
            editDepartement.value = "";
        }
    }
    async function updateUser(event) {
        event.preventDefault();
        const idUser = document.getElementById('edit-id-user').value
        const username = document.getElementById('edit-username').value
        const email = document.getElementById('edit-email').value
        const role =  document.getElementById('edit-role').value
        const departement = document.getElementById('edit-departement').value;

        if(!idUser) {
            return SwalAlert.warning('Terjadi kesalahan!', 'ID tidak ditemukan.')
        }
        if(!username) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data username wajib di isi.')
        }
        if(!email) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data email wajib di isi.')
        }
        if(!role) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data role wajib di isi.')
        }
        if (role === "LEADER" && !departement) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data departemen wajib diisi untuk role LEADER.');
        }
        const responseUpdateUser = await fetch('/attendance/api/user.php/update-user', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                idUser: idUser,
                username: username,
                email: email,
                role: role,
                departement: departement ?? null
            })
        }).then(response => response.json())
        if(!responseUpdateUser.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseUpdateUser.message)
        }
        const modalElement = document.getElementById("modal-edit-user");
        const modal = bootstrap.Modal.getInstance(modalElement);
        modal.hide();
        SwalAlert.success(responseUpdateUser.message)
        setTimeout(() => {
            Swal.close()
            window.location.href = '/attendance/views/user.php';
        }, 1000);
    }
    async function resetPassword(idUser) {
        if(!idUser) {
            return SwalAlert.warning('Terjadi kesalahan!', 'ID tidak ditemukan.')
        }
        const responseResetPassword = await fetch('/attendance/api/user.php/reset-password', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                idUser: idUser,
            })
        }).then(response => response.json())
        if(!responseResetPassword.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseResetPassword.message)
        }
        SwalAlert.success(responseResetPassword.message)
    }
</script>
</body>
</html>