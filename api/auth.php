<?php
    require_once '../controllers/AuthController.php';
    header('Content-Type: application/json');
    $auth = new AuthController();
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $fullPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $routePath = str_replace('/attendance/api/auth.php', '', $fullPath);

    switch ("{$requestMethod}_{$routePath}") {
        case 'POST_/login':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($auth->login($requestBody));
            break;
        case 'POST_/request-change-password':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($auth->requestChangePassword($requestBody));
            break;
        case 'POST_/change-password-by-request':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($auth->changePasswordByRequest($requestBody));
            break;
        case 'POST_/logout':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($auth->logout($requestBody));
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
