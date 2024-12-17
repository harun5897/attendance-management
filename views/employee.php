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
                    <h3>Data Karyawan</h3>
                    <hr>
                    <button type="button" class="btn btn-primary btn-sm mb-2" data-bs-toggle="modal" data-bs-target="#modal-create-employee">
                        Tambah Karyawan
                    </button>
                    <!-- Modal Create Employee -->
                    <div class="modal fade" id="modal-create-employee" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-secondary text-white">
                                    <h5 class="modal-title" id="exampleModalLabel">Form Data Karyawan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="" onsubmit="return saveEmployee(event)">
                                        <input id="code-employee" class="form-control mb-3" type="text" placeholder="Masukan kode karyawan" aria-label="code-employee">
                                        <input id="name-employee" class="form-control mb-3" type="text" placeholder="Masukan nama karyawan" aria-label="name-employee">
                                        <input id="fingerprint" class="form-control mb-3" type="text" placeholder="Masukan ID fingerprint" aria-label="fingerprint">
                                        <input id="date-join" class="form-control mb-3" type="date" placeholder="Masukan tanggal join" aria-label="date-join">
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
                                    <button type="submit" class="btn btn-secondary" onclick="saveEmployee(event)">Simpan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Edit Employee -->
                    <div class="modal fade" id="modal-edit-employee" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-warning">
                                    <h5 class="modal-title" id="exampleModalLabel">Form Edit Karyawan</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="" onsubmit="return updateEmployee(event)">
                                        <input id="edit-code-employee" class="form-control mb-3" type="text" placeholder="Masukan kode karyawan" aria-label="code-employee" disabled>
                                        <input id="edit-name-employee" class="form-control mb-3" type="text" placeholder="Masukan nama karyawan" aria-label="name-employee">
                                        <input id="edit-fingerprint" class="form-control mb-3" type="text" placeholder="Masukan ID fingerprint" aria-label="fingerprint">
                                        <input id="edit-date-join" class="form-control mb-3" type="date" placeholder="Masukan tanggal join" aria-label="date-join">
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
                                    <button type="submit" class="btn btn-warning" onclick="updateEmployee(event)">Simpan</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered table-sm shadow-lg" style="font-size: 13px !important;">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">No</th>
                                <th scope="col">Kode Karyawan</th>
                                <th scope="col">Nama Karyawan</th>
                                <th scope="col">ID Fingerprint</th>
                                <th scope="col">Tanggal Join</th>
                                <th scope="col">Departemen</th>
                                <th scope="col" style="width: 150px;">Action</th>
                            </tr>
                        </thead>
                        <tbody id="employee-table-body">
                        </tbody>
                    </table>
                    <nav aria-label="Page navigation example">
                        <input type="hidden" id="total-pages">
                        <input type="hidden" id="current-page" value="1">
                        <ul class="pagination">
                            <li class="page-item"><a class="page-link" href="#" onclick="getEmployee('prev')">Previous</a></li>
                            <li class="page-item"><span id="display-current-page" class="page-link">1</span></li>
                            <li class="page-item"><a class="page-link" href="#" onclick="getEmployee('next')">Next</a></li>
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
    getEmployee()
    async function getEmployee(navigationPage) {
        const totalPages = document.getElementById('total-pages').value;
        const currentPage = document.getElementById('current-page').value;
        let page = currentPage;
        if(navigationPage == 'prev' && currentPage > 1) {
            page = parseInt(currentPage) - 1;
        }
        if(navigationPage == 'next' && currentPage < totalPages) {
            page = parseInt(currentPage) + 1;
        }
        const responseGetEmployee = await fetch('/attendance/api/employee.php/get-employee', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                page: page,
            })
        }).then(response => response.json())
        let html_data = '';
        if (responseGetEmployee.data.length == 0 || !responseGetEmployee.success) {
            html_data += '<tr><td colspan="7" class="text-center">Data tidak tersedia</td></tr>';
            document.getElementById('employee-table-body').innerHTML = html_data;
            return
        }
        const dataPerPage = responseGetEmployee.pagination.data_per_page;
        responseGetEmployee.data.forEach((employee, index) => {
            const rowNumber = (page - 1) * dataPerPage + (index + 1);
            html_data += '<tr>';
            html_data += `<th scope="row" class="text-center">${rowNumber}</th>`;
            html_data += `<td>${employee.code_employee}</td>`;
            html_data += `<td>${employee.name_employee}</td>`;
            html_data += `<td>${employee.fingerprint}</td>`;
            html_data += `<td>${employee.date_join}</td>`;
            html_data += `<td><span class="badge text-bg-success">${employee.departement}</span></td>`;
            html_data += '<td>';
            html_data += `<button type="button" class="btn btn-sm btn-warning me-1" style="font-size: 12px;" onclick="getEmployeeDetail(${employee.code_employee})">Edit</button>`;
            html_data += `<button type="button" class="btn btn-sm btn-danger me-1" style="font-size: 12px;" onclick="deleteEmployee(${employee.code_employee})">Hapus</button>`;
            html_data += '</td>';
            html_data += '</tr>';
        });
        document.getElementById('employee-table-body').innerHTML = html_data;
        document.getElementById('current-page').value = responseGetEmployee.pagination.current_page;
        document.getElementById('display-current-page').innerHTML = responseGetEmployee.pagination.current_page;
        document.getElementById('total-pages').value = responseGetEmployee.pagination.total_pages;
    }
    async function saveEmployee(event) {
        event.preventDefault();
        const codeEmployee = document.getElementById('code-employee').value
        const nameEmployee = document.getElementById('name-employee').value
        const fingerprint =  document.getElementById('fingerprint').value
        const dateJoin =  document.getElementById('date-join').value
        const departement =  document.getElementById('departement').value

        if(!codeEmployee) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data kode karyawan wajib di isi.')
        }
        if(!nameEmployee) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data nama karyawan wajib di isi.')
        }
        if(!fingerprint) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data ID fingerprint wajib di isi.')
        }
        if(!dateJoin) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data tanggal join wajib di isi.')
        }
        if(!departement) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data departemen wajib di isi.')
        }
        const responseSaveEmployee = await fetch('/attendance/api/employee.php/create-employee', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                codeEmployee: codeEmployee,
                nameEmployee: nameEmployee,
                fingerprint: fingerprint,
                dateJoin: dateJoin,
                departement: departement
            })
        }).then(response => response.json())
        if(!responseSaveEmployee.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseSaveEmployee.message)
        }
        SwalAlert.success(responseSaveEmployee.message)
        setTimeout(() => {
            Swal.close()
            window.location.href = '/attendance/views/employee.php';
        }, 1000);
    }
    async function deleteEmployee(codeEmployee) {
        if(!codeEmployee) {
            return SwalAlert.warning('Terjadi kesalahan!', 'Kode karyawan tidak ditemukan')
        }
        const responseDeleteEmployee = await fetch('/attendance/api/employee.php/delete-employee', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                codeEmployee: codeEmployee,
            })
        }).then(response => response.json())
        if(!responseDeleteEmployee.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseDeleteEmployee.message)
        }
        SwalAlert.success(responseDeleteEmployee.message)
        setTimeout(() => {
            Swal.close()
            window.location.href = '/attendance/views/employee.php';
        }, 1000);
    }
    async function getEmployeeDetail(codeEmployee) {
        if(!codeEmployee) {
            return SwalAlert.warning('Terjadi kesalahan!', 'Kode karyawan tidak ditemukan')
        }
        const responseDetailEmployee = await fetch('/attendance/api/employee.php/detail-employee', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                codeEmployee: codeEmployee,
            })
        }).then(response => response.json())
        if(!responseDetailEmployee.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseDetailEmployee.message)
        }
        const myModal = new bootstrap.Modal(document.getElementById("modal-edit-employee"));
        myModal.show();
        const detailEmployee = responseDetailEmployee.data
        document.getElementById('edit-code-employee').value = detailEmployee.code_employee
        document.getElementById('edit-name-employee').value = detailEmployee.name_employee
        document.getElementById('edit-fingerprint').value = detailEmployee.fingerprint
        document.getElementById('edit-date-join').value = detailEmployee.date_join
        document.getElementById('edit-departement').value = detailEmployee.departement
    }
    async function updateEmployee(event) {
        event.preventDefault();
        const codeEmployee = document.getElementById('edit-code-employee').value
        const nameEmployee = document.getElementById('edit-name-employee').value
        const fingerprint =  document.getElementById('edit-fingerprint').value
        const dateJoin =  document.getElementById('edit-date-join').value
        const departement =  document.getElementById('edit-departement').value

        if(!codeEmployee) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data kode karyawan wajib di isi.')
        }
        if(!nameEmployee) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data nama karyawan wajib di isi.')
        }
        if(!fingerprint) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data ID fingerprint wajib di isi.')
        }
        if(!dateJoin) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data tanggal join wajib di isi.')
        }
        if(!departement) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data departemen wajib di isi.')
        }
        const responseSaveEmployee = await fetch('/attendance/api/employee.php/update-employee', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                codeEmployee: codeEmployee,
                nameEmployee: nameEmployee,
                fingerprint: fingerprint,
                dateJoin: dateJoin,
                departement: departement
            })
        }).then(response => response.json())
        if(!responseSaveEmployee.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseSaveEmployee.message)
        }
        SwalAlert.success(responseSaveEmployee.message)
        setTimeout(() => {
            Swal.close()
            window.location.href = '/attendance/views/employee.php';
        }, 1000);
    }
</script>
</body>
</html>