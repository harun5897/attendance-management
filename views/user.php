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
                <div id="content-page-views">
                    <h1>Data Pengguna</h1>
                </div>
            </div>
        </div>
    </div>
<script src="../assets/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/@popperjs/core/dist/umd/popper.min.js"></script>
<script src="../assets/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="../utils/swalAlert.js"></script>
</body>
</html>