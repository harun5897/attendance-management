<?php
    require_once '../controllers/EmployeeController.php';
    header('Content-Type: application/json');
    $employee = new EmployeeController();
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $fullPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $routePath = str_replace('/attendance/api/employee.php', '', $fullPath);

    switch ("{$requestMethod}_{$routePath}") {
        case 'POST_/get-employee':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($employee->getEmployee($requestBody));
            break;
        case 'POST_/detail-employee':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($employee->getDetailEmployee($requestBody));
            break;
        case 'POST_/create-employee':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($employee->createEmployee($requestBody));
            break;
        case 'PUT_/update-employee':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($employee->updateEmployee($requestBody));
            break;
        case 'DELETE_/delete-employee':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($employee->deleteEmployee($requestBody));
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
