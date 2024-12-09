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
        case 'GET_/user':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($user->getUser());
            break;
        case 'POST_/user':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($user->createUser($requestBody));
            break;
        case 'PUT_/user':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($user->updateUser());
            break;
        case 'DELETE_/user':
            $requestBody = json_decode(file_get_contents('php://input'), true);
            echo json_encode($user->deleteUser());
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
