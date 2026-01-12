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

// autoload classes
require __DIR__ . '/../vendor/autoload.php';

// define routes
$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    // rooms
    $r->addRoute('GET', '/', ['App\Controllers\RoomController', 'index']);
    $r->addRoute('GET', '/rooms', ['App\Controllers\RoomController', 'index']);
    $r->addRoute('GET', '/rooms/{id:\d+}/play', ['App\Controllers\RoomController', 'play']);

    // auth
    $r->addRoute('GET',  '/login',    ['App\Controllers\AuthController', 'showLogin']);
    $r->addRoute('POST', '/login',    ['App\Controllers\AuthController', 'login']);
    $r->addRoute('GET',  '/register', ['App\Controllers\AuthController', 'showRegister']);
    $r->addRoute('POST', '/register', ['App\Controllers\AuthController', 'register']);
    $r->addRoute('GET',  '/logout',   ['App\Controllers\AuthController', 'logout']);

    // creator 
    $r->addRoute('GET',  '/creator/rooms', ['App\Controllers\RoomController', 'creatorRooms']);
    $r->addRoute('POST', '/creator/rooms', ['App\Controllers\RoomController', 'createRoom']);
    $r->addRoute('GET',  '/creator/rooms/{id:\d+}/edit', ['App\Controllers\RoomController', 'editRoomForm']);
    $r->addRoute('POST', '/creator/rooms/{id:\d+}/edit', ['App\Controllers\RoomController', 'updateRoom']);
    $r->addRoute('POST', '/creator/rooms/{id:\d+}/delete', ['App\Controllers\RoomController', 'deleteRoom']);
    $r->addRoute('GET',  '/creator/rooms/{id:\d+}/level', ['App\Controllers\RoomController', 'levelEditor']);
    $r->addRoute('POST', '/creator/rooms/{id:\d+}/level', ['App\Controllers\RoomController', 'saveLevel']);

    // admin
    $r->addRoute('GET',  '/admin', ['App\Controllers\RoomController', 'adminDashboard']);
    $r->addRoute('GET',  '/admin/users', ['App\Controllers\RoomController', 'adminUsers']);
    $r->addRoute('POST', '/admin/users/{id:\d+}/role', ['App\Controllers\RoomController', 'updateUserRole']);
    $r->addRoute('GET',  '/admin/rooms', ['App\Controllers\RoomController', 'adminRooms']);
    $r->addRoute('POST', '/admin/rooms/{id:\d+}/publish', ['App\Controllers\RoomController', 'toggleRoomPublish']);
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
$userRepository = new \App\Repositories\UserRepository();
$roomRepository = new \App\Repositories\RoomRepository();
$roomLevelRepository = new \App\Repositories\RoomLevelRepository();

// create services
$authService = new \App\Services\AuthService($userRepository);
$roomService = new \App\Services\RoomService($roomRepository);
$adminService = new \App\Services\AdminService($userRepository, $roomRepository);
$roomLevelService = new \App\Services\RoomLevelService($roomLevelRepository);

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

        // create controller
        $controller = match ($class) {
            'App\Controllers\RoomController' =>
               new \App\Controllers\RoomController($authService, $roomService, $adminService, $roomLevelService),

            'App\Controllers\AuthController' =>
                new \App\Controllers\AuthController($authService),

            default => throw new Exception('Controller not found')
        };

        // call controller method
        $controller->$method($vars);
        break;
}