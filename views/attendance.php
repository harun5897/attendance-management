<?php
    session_start();
    require_once '../middleware/middleware.php';
    $middleware = new Middleware();
    $middleware->middlewarePage();
    $role = $_SESSION['role'];
    if($_SESSION['role'] != 'LEADER' && $_SESSION['role'] != 'ADMIN') {
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
                    <h3>Data Absensi</h3>
                    <hr>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Daftar Absensi</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Kelola Absensi</button>
                        </li>
                    </ul>
                    <hr style="margin-top: 0 !important;">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                            <table class="table table-bordered table-sm shadow-lg" style="font-size: 13px !important;">
                                <thead>
                                    <tr>
                                        <th scope="col" class="text-center">No</th>
                                        <th scope="col">Tanggal</th>
                                        <th scope="col">Kode Karyawan</th>
                                        <th scope="col">Nama Karyawan</th>
                                        <th scope="col">Jam Masuk</th>
                                        <th scope="col">Overtime</th>
                                        <th scope="col">Meal Box</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="attendance-table-body">
                                    <tr>
                                        <td colspan="21" class="text-center">Data tidak ditemukan</td>
                                    </tr>
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
                        <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                            <input id="path-file" type="hidden">
                            <?php if($role == "ADMIN") { ?>
                            <div>
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex">
                                        <button type="button" class="btn btn-primary btn-sm mb-2" onclick="uploadAttendance()"> Upload </button>
                                        <div class="mx-1">
                                            <input id="file-attendance" type="file" class="form-control form-control-sm" />
                                        </div>
                                    </div>
                                    <div class="">
                                        <select id="departement" class="form-select form-select-sm" aria-label="Default select example" onchange="getAttendance()">
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
                                </div>
                                <table class="table table-bordered table-sm shadow-lg" style="font-size: 13px !important;">
                                    <thead>
                                        <tr>
                                            <th scope="col" class="text-center">No</th>
                                            <th scope="col">Kode Karyawan</th>
                                            <th scope="col">Nama Karyawan</th>
                                            <th scope="col">Jam Masuk</th>
                                            <th scope="col">Overtime</th>
                                            <th scope="col">Meal Box</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attendance-excel-table-body">
                                        <tr>
                                            <td colspan="21" class="text-center">Data tidak ditemukan</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-between">
                                    <nav aria-label="Page navigation example">
                                        <ul class="pagination">
                                            <li class="page-item"><a class="page-link" href="#" onclick="prevPage('prev')">Previous</a></li>
                                            <li class="page-item"><span id="display-current-page" class="page-link">1</span></li>
                                            <li class="page-item"><a class="page-link" href="#" onclick="nextPage()">Next</a></li>
                                        </ul>
                                    </nav>
                                    <div class="">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="submitDataAttendance()">Submit Data Absensi Departemen</button>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="ms-3">
                                    <span>Maaf role anda tidak memiliki akses untuk mengelola absensi</span>
                                </div>
                            <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Detail Attendance -->
    <div class="modal fade" id="modal-detail-attendance" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="exampleModalLabel">Form Detail Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" onsubmit="return updateAttendance(event)">
                        <input id="code-employee" class="form-control mb-3" type="text" placeholder="Masukan kode karyawan" aria-label="code-employee" disabled>
                        <input id="name-employee" class="form-control mb-3" type="text" placeholder="Masukan nama karyawan" aria-label="name-employee">
                        <input id="time" class="form-control mb-3" type="text" placeholder="Masukan jam masuk karyawan" aria-label="fingerprint">
                        <select id="overtime" class="form-select mb-3" aria-label="Default select example">
                            <option value="">Pilih jam overtime</option>
                            <option value="1">1 Jam</option>
                            <option value="2">2 Jam</option>
                            <option value="3">3 Jam</option>
                            <option value="4">4 Jam</option>
                            <option value="5">5 Jam</option>
                        </select>
                        <select id="meal-box" class="form-select mb-3" aria-label="Default select example">
                            <option value="">Pilih meal box</option>
                            <option value="siang">siang</option>
                            <option value="malam">malam</option>
                            <option value="siang_malam">siang & malam</option>
                        </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning" onclick="updateAttendance(event)">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Detail Attendance Actual-->
    <div class="modal fade" id="modal-detail-attendance-actual" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title" id="exampleModalLabel">Form Detail Absensi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="" onsubmit="return updateAttendanceActual(event)">
                        <input id="code-employee-actual" class="form-control mb-3" type="text" placeholder="Masukan kode karyawan" aria-label="code-employee" disabled>
                        <input id="name-employee-actual" class="form-control mb-3" type="text" placeholder="Masukan nama karyawan" aria-label="name-employee" disabled>
                        <input id="time-actual" class="form-control mb-3" type="text" placeholder="Masukan jam masuk karyawan" aria-label="fingerprint">
                        <select id="overtime-actual" class="form-select mb-3" aria-label="Default select example">
                            <option value="">Pilih jam overtime</option>
                            <option value="1">1 Jam</option>
                            <option value="2">2 Jam</option>
                            <option value="3">3 Jam</option>
                            <option value="4">4 Jam</option>
                            <option value="5">5 Jam</option>
                        </select>
                        <select id="meal-box-actual" class="form-select mb-3" aria-label="Default select example">
                            <option value="">Pilih meal box</option>
                            <option value="siang">siang</option>
                            <option value="malam">malam</option>
                            <option value="siang_malam">siang & malam</option>
                        </select>
                        <input id="date-attendance-actual" class="form-control mb-3" type="date" aria-label="date-attendance-actual">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning" onclick="updateAttendanceActual(event)">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<script src="../assets/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/@popperjs/core/dist/umd/popper.min.js"></script>
<script src="../assets/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="../utils/swalAlert.js"></script>

<script>
    const myModal = new bootstrap.Modal(document.getElementById("modal-detail-attendance"));
    const myModalActual = new bootstrap.Modal(document.getElementById("modal-detail-attendance-actual"));
    let stateAttendanceFromExcel = [];
    let stateDateAttendance = '';
    let totalPages = 0;
    let currentPage = 1;
    let pageSize = 8;
    
    getAttendanceActual()
    async function uploadAttendance() {
        const fileAttendance = document.getElementById('file-attendance')
        const file = fileAttendance.files[0];
        const MAX_SIZE_MB = 20;
        if (!file) {
            return SwalAlert.warning('Data tidak lengkap!', 'File attendance wajib diunggah.');
        }
        if(file.size > MAX_SIZE_MB * 1024 * 1024) {
            SwalAlert.warning(
            'Ukuran file terlalu besar!',
            `Maksimal ukuran file adalah ${MAX_SIZE_MB} MB. File yang dipilih memiliki ukuran ${(file.size / (1024 * 1024)).toFixed(2)} MB.`
        );
        }
        let formData = new FormData();
        formData.append('file', fileAttendance.files[0]);
        const responseUploadAttendance = await fetch('/attendance/api/attendance.php/upload-attendance', {
            method: 'POST',
            body:formData
        }).then(response => response.json())
        if(!responseUploadAttendance.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseUploadAttendance.message)
        }
        SwalAlert.success(responseUploadAttendance.message)
        document.getElementById('path-file').value = responseUploadAttendance.data.path_file
    }
    async function getAttendance() {
        const pathFile = document.getElementById('path-file').value
        const departement = document.getElementById('departement').value
        if(!pathFile) {
            return SwalAlert.warning('Data tidak lengkap!', 'Silahkan upload file absensi terlebih dahulu.')
        }
        const responseGetAttendance = await fetch('/attendance/api/attendance.php/get-attendance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                pathFile: pathFile,
                departement: departement,
            })
        }).then(response => response.json())
        if(!responseGetAttendance.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseGetAttendance.message)
        }
        let html_data = '';
        stateAttendanceFromExcel = responseGetAttendance.data
        stateDateAttendance = responseGetAttendance.date_attendance
        renderPage();
    }
    function renderPage() {
        const total_pages = Math.ceil(stateAttendanceFromExcel.length / pageSize);
        if (currentPage > total_pages) currentPage = total_pages;
        if (currentPage < 1) currentPage = 1;
        const startIndex = (currentPage - 1) * pageSize;
        const dataTempAttendance = stateAttendanceFromExcel.slice(startIndex, startIndex + pageSize);
        let html_data = '';
        dataTempAttendance.forEach((attendance, index) => {
            const rowNumber = startIndex + index + 1;
            html_data += '<tr>';
            html_data += `<th scope="row" class="text-center">${rowNumber}</th>`;
            html_data += `<td>${attendance.code_employee}</td>`;
            html_data += `<td>${attendance.name_employee}</td>`;
            html_data += `<td class="text-center">${attendance.time ?? "-"}</td>`;
            html_data += `<td class="text-center">${attendance.overtime ? attendance.overtime+" Jam" : "-"}</td>`;
            html_data += `<td class="text-center">${attendance.meal_box ?? "-"}</td>`;
            html_data += '<td>';
            html_data += `<button type="button" class="btn btn-sm btn-warning me-1" style="font-size: 12px;" onclick="getDetailAttendance('${attendance.code_employee}')">Edit</button>`;
            html_data += `<button type="button" class="btn btn-sm btn-danger me-1" style="font-size: 12px;" onclick="deleteAttendance('${attendance.code_employee}')">Hapus</button>`;
            html_data += '</td>';
            html_data += '</tr>';
        });
        document.getElementById('attendance-excel-table-body').innerHTML = '';
        document.getElementById('attendance-excel-table-body').innerHTML = html_data;
        document.getElementById('display-current-page').innerHTML = currentPage;
    }
    function nextPage() {
        currentPage++;
        renderPage();
    }
    function prevPage() {
        currentPage--;
        renderPage();
    }
    function getDetailAttendance(codeEmployee) {
        myModal.show();
        const dataFilterEmployee = stateAttendanceFromExcel.filter(data => data.code_employee == codeEmployee)
        document.getElementById('code-employee').value = dataFilterEmployee[0].code_employee
        document.getElementById('name-employee').value = dataFilterEmployee[0].name_employee
        document.getElementById('time').value = dataFilterEmployee[0].time
        document.getElementById('overtime').value = !dataFilterEmployee[0].overtime ? '' : dataFilterEmployee[0].overtime
        document.getElementById('meal-box').value = !dataFilterEmployee[0].meal_box ? '' : dataFilterEmployee[0].meal_box
    }
    function updateAttendance(event) {
        event.preventDefault();
        const codeEmployee = document.getElementById('code-employee').value
        const nameEmployee = document.getElementById('name-employee').value
        const time = document.getElementById('time').value
        const overtime = document.getElementById('overtime').value
        const mealBox =  document.getElementById('meal-box').value

        if(!codeEmployee) {
            return SwalAlert.warning('Data tidak lengkap!', 'Kode karyawan tidak boleh kosong.')
        }
        if(!nameEmployee) {
            return SwalAlert.warning('Data tidak lengkap!', 'Nama karyawan tidak boleh kosong.')
        }
        if(time.length > 5) {
            return SwalAlert.warning('Terjadi kesalahan!', 'Maksimal jam masuk adalah 5 karakter.')
        }
        const updatedData = {
            code_employee: codeEmployee,
            name_employee: nameEmployee,
            time: !time ? null : time,
            overtime: !overtime ? null : overtime,
            meal_box: !mealBox ? null : mealBox
        };
        const updatedAttendance = stateAttendanceFromExcel.map(data => {
            if (data.code_employee == codeEmployee) {
                return { ...data, ...updatedData };
            }
            return data;
        });
        stateAttendanceFromExcel = ''
        stateAttendanceFromExcel = updatedAttendance
        renderPage();
        SwalAlert.success('Data berhasil diubah.')
        myModal.hide();
    }
    function deleteAttendance(codeEmployee) {
        const filteredAttendance = stateAttendanceFromExcel.filter(data => data.code_employee !== codeEmployee);
        stateAttendanceFromExcel = ''
        stateAttendanceFromExcel = filteredAttendance
        renderPage();
        SwalAlert.success('Data berhasil dihapus.')
    }
    async function submitDataAttendance() {
        if(stateAttendanceFromExcel.length < 1) {
            return SwalAlert.warning('Data tidak lengkap!', 'Data absensi departemen tidak boleh kosong.')
        }
        const responseSubmitAttendance = await fetch('/attendance/api/attendance.php/submit-attendance', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                dataAttendance: stateAttendanceFromExcel,
                dateAttendance: convertDateFormat(stateDateAttendance)
            })
        }).then(response => response.json())
        if(!responseSubmitAttendance.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseSubmitAttendance.message)
        }
        SwalAlert.success(responseSubmitAttendance.message)
        setTimeout(() => {
            Swal.close()
            window.location.href = '/attendance/views/attendance.php';
        }, 1000);
    }
    function convertDateFormat(inputDate) {
        const months = {
            "Januari": "01",
            "Februari": "02",
            "Maret": "03",
            "April": "04",
            "Mei": "05",
            "Juni": "06",
            "Juli": "07",
            "Agustus": "08",
            "September": "09",
            "Oktober": "10",
            "November": "11",
            "Desember": "12"
        };
        // ["Mon,", "02", "Desember", "2024"]
        const parts = inputDate.split(" ");
        const day = parts[1];
        const month = months[parts[2]];
        const year = parts[3];
        return `${year}-${month}-${day}`;
    }
    async function getAttendanceActual(navigationPage) {
        const totalPages = document.getElementById('total-pages').value;
        const currentPage = document.getElementById('current-page').value;
        let page = currentPage;
        if(navigationPage == 'prev' && currentPage > 1) {
            page = parseInt(currentPage) - 1;
        }
        if(navigationPage == 'next' && currentPage < totalPages) {
            page = parseInt(currentPage) + 1;
        }
        const responseGetAttendance = await fetch('/attendance/api/attendance.php/get-attendance-actual', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                page: page,
            })
        }).then(response => response.json())
        let html_data = '';
        if (responseGetAttendance.data.length == 0 || !responseGetAttendance.success) {
            html_data += '<tr><td colspan="25" class="text-center">Data tidak tersedia</td></tr>';
            document.getElementById('attendance-table-body').innerHTML = html_data;
            return
        }
        const dataPerPage = responseGetAttendance.pagination.data_per_page;
        responseGetAttendance.data.forEach((attendance, index) => {
            const rowNumber = (page - 1) * dataPerPage + (index + 1);
            html_data += '<tr>';
            html_data += `<th scope="row" class="text-center">${rowNumber}</th>`;
            html_data += `<td>${attendance.date_attendance}</td>`;
            html_data += `<td>${attendance.code_employee}</td>`;
            html_data += `<td>${attendance.name_employee}</td>`;
            html_data += `<td class="text-center">${attendance.time ?? "-"}</td>`;
            html_data += `<td class="text-center">${attendance.overtime ? attendance.overtime+ " Jam": "-"}</td>`;
            html_data += `<td class="text-center">${attendance.meal_box ?? "-"}</td>`;
            html_data += '<td>';
            html_data += `<button type="button" class="btn btn-sm btn-warning me-1" style="font-size: 12px;" onclick="detailAttendanceActual(${attendance.code_employee})">Edit</button>`;
            html_data += `<button type="button" class="btn btn-sm btn-danger me-1" style="font-size: 12px;" onclick="deleteAttendanceActual(${attendance.code_employee})">Hapus</button>`;
            html_data += '</td>';
            html_data += '</tr>';
        });
        document.getElementById('attendance-table-body').innerHTML = html_data;
        document.getElementById('current-page').value = responseGetAttendance.pagination.current_page;
        document.getElementById('display-current-page').innerHTML = responseGetAttendance.pagination.current_page;
        document.getElementById('total-pages').value = responseGetAttendance.pagination.total_pages;
    }
    async function deleteAttendanceActual(codeEmployee) {
        if(!codeEmployee) {
            return SwalAlert.warning('Terjadi kesalahan!', 'Kode karyawan tidak ditemukan')
        }
        const responseDeleteAttendanceActual = await fetch('/attendance/api/attendance.php/delete-attendance-actual', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                codeEmployee: codeEmployee,
            })
        }).then(response => response.json())
        if(!responseDeleteAttendanceActual.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseDeleteAttendanceActual.message)
        }
        SwalAlert.success(responseDeleteAttendanceActual.message)
        setTimeout(() => {
            Swal.close()
            window.location.href = '/attendance/views/attendance.php';
        }, 1000);
    }
    async function detailAttendanceActual(codeEmployee) {
        if(!codeEmployee) {
            return SwalAlert.warning('Terjadi kesalahan!', 'Kode karyawan tidak ditemukan')
        }
        const responseDetailAttendanceActual = await fetch('/attendance/api/attendance.php/detail-attendance-actual', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                codeEmployee: codeEmployee,
            })
        }).then(response => response.json())
        if(!responseDetailAttendanceActual.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseDetailAttendanceActual.message)
        }
        console.log(responseDetailAttendanceActual.data)
        myModalActual.show()
        document.getElementById('code-employee-actual').value = responseDetailAttendanceActual.data[0].code_employee
        document.getElementById('name-employee-actual').value = responseDetailAttendanceActual.data[0].name_employee
        document.getElementById('time-actual').value = responseDetailAttendanceActual.data[0].time
        document.getElementById('overtime-actual').value = !responseDetailAttendanceActual.data[0].overtime ? '' : responseDetailAttendanceActual.data[0].overtime
        document.getElementById('meal-box-actual').value = !responseDetailAttendanceActual.data[0].meal_box ? '' : responseDetailAttendanceActual.data[0].meal_box
        document.getElementById('date-attendance-actual').value = responseDetailAttendanceActual.data[0].date_attendance
    }
    async function updateAttendanceActual() {
        event.preventDefault();
        const codeEmployee = document.getElementById('code-employee-actual').value
        const nameEmployee = document.getElementById('name-employee-actual').value
        const time = document.getElementById('time-actual').value
        const overtime = document.getElementById('overtime-actual').value
        const mealBox = document.getElementById('meal-box-actual').value
        const dateAttendance = document.getElementById('date-attendance-actual').value
        if(!codeEmployee) {
            return SwalAlert.warning('Terjadi kesalahan!', 'Kode karyawan tidak ditemukan')
        }
        if(!nameEmployee) {
            return SwalAlert.warning('Terjadi kesalahan!', 'Nama karyawan wajib diisi')
        }
        const responseUpdateAttendanceActual = await fetch('/attendance/api/attendance.php/update-attendance-actual', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                codeEmployee: codeEmployee,
                time: time,
                overtime: overtime,
                mealbox: mealBox,
                dateAttendance: dateAttendance
            })
        }).then(response => response.json())
        if(!responseUpdateAttendanceActual.success) {
            return SwalAlert.warning('Terjadi kesalahan', responseUpdateAttendanceActual.message)
        }
        SwalAlert.success(responseUpdateAttendanceActual.message)
        setTimeout(() => {
            Swal.close()
            window.location.href = '/attendance/views/attendance.php';
        }, 1000);
    }
</script>
</body>
</html>
