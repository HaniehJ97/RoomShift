<?php

declare(strict_types=1);

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

// show errors during development
ini_set('display_errors', '1');
error_reporting(E_ALL);

// session settings
ini_set('session.cookie_httponly', '1');
ini_set('session.use_strict_mode', '1');

session_name('ROOMSHIFT_SESSID');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// autoload classes
require __DIR__ . '/../vendor/autoload.php';

// define routes
$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    // rooms (player)
    $r->addRoute('GET', '/', ['App\Controllers\RoomController', 'index']);
    $r->addRoute('GET', '/rooms', ['App\Controllers\RoomController', 'index']);
    $r->addRoute('GET', '/rooms/{id:\d+}/play', ['App\Controllers\RoomController', 'play']);

    // auth
    $r->addRoute('GET',  '/login',    ['App\Controllers\AuthController', 'showLogin']);
    $r->addRoute('POST', '/login',    ['App\Controllers\AuthController', 'login']);
    $r->addRoute('GET',  '/register', ['App\Controllers\AuthController', 'showRegister']);
    $r->addRoute('POST', '/register', ['App\Controllers\AuthController', 'register']);
    $r->addRoute('GET',  '/logout',   ['App\Controllers\AuthController', 'logout']);

    // admin dashboard & users
    $r->addRoute('GET',  '/admin', ['App\Controllers\AdminController', 'dashboard']);
    $r->addRoute('GET',  '/admin/users', ['App\Controllers\AdminController', 'users']);
    $r->addRoute('POST', '/admin/users/{id:\d+}/role', ['App\Controllers\AdminController', 'updateUserRole']);
    
    // admin rooms management
    $r->addRoute('GET',  '/admin/rooms', ['App\Controllers\AdminController', 'rooms']);
    $r->addRoute('GET',  '/admin/rooms/create', ['App\Controllers\AdminController', 'createForm']);
    $r->addRoute('POST', '/admin/rooms', ['App\Controllers\AdminController', 'createRoom']);
    $r->addRoute('GET',  '/admin/rooms/{id:\d+}/edit', ['App\Controllers\AdminController', 'editRoomForm']);
    $r->addRoute('POST', '/admin/rooms/{id:\d+}/edit', ['App\Controllers\AdminController', 'updateRoom']);
    $r->addRoute('POST', '/admin/rooms/{id:\d+}/delete', ['App\Controllers\AdminController', 'deleteRoom']);
    $r->addRoute('POST', '/admin/rooms/{id:\d+}/publish', ['App\Controllers\AdminController', 'toggleRoomPublish']);

    // API endpoints for AJAX
    $r->addRoute('GET', '/api/rooms/{id:\d+}', ['App\Controllers\ApiController', 'getRoom']);
    $r->addRoute('POST', '/api/rooms', ['App\Controllers\ApiController', 'createRoom']);
});

// get request data
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// remove query string from url
if (false !== ($pos = strpos($uri, '?'))) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// match route
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// create repositories
$roomRepository = new \App\Repositories\RoomRepository();
$userRepository = new \App\Repositories\UserRepository();

// create services
$authService = new \App\Services\AuthService($userRepository);
$roomService = new \App\Services\RoomService($roomRepository);
$userService = new \App\Services\UserService($userRepository);
$adminService = new \App\Services\AdminService($userRepository, $roomRepository);

// create controller instances
$roomController = new \App\Controllers\RoomController($authService, $roomService, $userService);
$authController = new \App\Controllers\AuthController($authService);
$adminController = new \App\Controllers\AdminController($authService, $adminService, $roomService, $userService);
$apiController = new \App\Controllers\ApiController($authService, $roomService, $userService);

// handle request
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 - Page not found';
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 - Method not allowed';
        break;

    case Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $vars = $routeInfo[2];

        // create controller based on route
        $controller = match ($class) {
            'App\Controllers\RoomController' => $roomController,
            'App\Controllers\AuthController' => $authController,
            'App\Controllers\AdminController' => $adminController,
            'App\Controllers\ApiController' => $apiController,
            default => throw new Exception('Controller not found: ' . $class)
        };

        // call controller method
        try {
            $controller->$method($vars);
        } catch (Throwable $e) {
            // Log error
            error_log('Controller error: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            
            // If it's an AJAX request, return JSON error
            $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                     strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
            
            if ($isAjax) {
                http_response_code(500);
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Internal server error'
                ]);
            } else {
                // Regular request - show error page
                http_response_code(500);
                echo '500 - Internal Server Error';
                if (ini_get('display_errors')) {
                    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
                }
            }
        }
        break;
}