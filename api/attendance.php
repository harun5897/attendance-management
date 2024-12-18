<?php
    require_once '../controllers/AttendanceController.php';
    require_once '../middleware/middleware.php';

    header('Content-Type: application/json');
    $attendance = new AttendanceController();
    $middleware = new Middleware();
    $middleware->middlewareApi();
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $fullPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $routePath = str_replace('/attendance/api/attendance.php', '', $fullPath);

    switch ("{$requestMethod}_{$routePath}") {
        case 'POST_/upload-attendance':
            echo json_encode($attendance->uploadAttendance());
            break;
        case 'POST_/get-attendance':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($attendance->getAttendacne($requestBody));
            break;
        case 'POST_/submit-attendance':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($attendance->submitAttendance($requestBody));
            break;
        case 'POST_/get-attendance-actual':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($attendance->getAttendanceActual($requestBody));
            break;
        case 'DELETE_/delete-attendance-actual':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($attendance->deleteAttendanceActual($requestBody));
            break;
        case 'POST_/detail-attendance-actual':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($attendance->getDetailAttendanceActual($requestBody));
            break;
        case 'POST_/update-attendance-actual':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($attendance->updateAttendanceActual($requestBody));
            break;
        default:
            echo json_encode([
                'success' => false,
                'data' => null,
                'message' => 'Endpoint atau metode request tidak valid'
            ]);
            break;
    }
?>
