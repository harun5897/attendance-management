<?php
    require_once '../controllers/UserController.php';
    require_once '../middleware/middleware.php';

    header('Content-Type: application/json');
    $user = new UserController();
    $middleware = new Middleware();
    $middleware->middlewareApi();
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $fullPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $routePath = str_replace('/attendance/api/user.php', '', $fullPath);

    switch ("{$requestMethod}_{$routePath}") {
        case 'POST_/get-user':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($user->getUser($requestBody));
            break;
        case 'POST_/detail-user':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($user->getDetailUser($requestBody));
            break;
        case 'POST_/create-user':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($user->createUser($requestBody));
            break;
        case 'PUT_/update-user':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($user->updateUser($requestBody));
            break;
        case 'DELETE_/delete-user':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($user->deleteUser($requestBody));
            break;
        case 'POST_/reset-password':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($user->resetPassword($requestBody));
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
