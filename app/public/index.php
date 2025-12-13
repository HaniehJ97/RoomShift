<?php

/**
 * Main Application Entry Point
 */
// ====== SESSION CONFIGURATION ======
// Configure session BEFORE starting it
// session_start();
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? '1' : '0');
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_samesite', 'Strict');

// Set session name to prevent fixation
session_name('ROOMSHIFT_SESSID');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// 1. Define Routes
$dispatcher = simpleDispatcher(function (RouteCollector $r) {

    // ====== PUBLIC ROUTES ======
    $r->addRoute('GET', '/', ['App\Controllers\RoomController', 'index']);
    $r->addRoute('GET', '/rooms', ['App\Controllers\RoomController', 'index']);
    
    // Auth Routes
    $r->addRoute('GET', '/login', ['App\Controllers\AuthController', 'showLogin']);
    $r->addRoute('POST', '/login', ['App\Controllers\AuthController', 'login']);
    $r->addRoute('GET', '/register', ['App\Controllers\AuthController', 'showRegister']);
    $r->addRoute('POST', '/register', ['App\Controllers\AuthController', 'register']);
    $r->addRoute('GET', '/logout', ['App\Controllers\AuthController', 'logout']);
    
    // ====== CREATOR ROUTES ======
    $r->addRoute('GET', '/creator/rooms', ['App\Controllers\CreatorController', 'listRooms']);
    $r->addRoute('GET', '/creator/rooms/create', ['App\Controllers\CreatorController', 'createForm']);
    $r->addRoute('POST', '/creator/rooms', ['App\Controllers\CreatorController', 'createRoom']);
    
    // ====== ADMIN ROUTE ======
    $r->addRoute('GET', '/admin', ['App\Controllers\AdminController', 'dashboard']);
    $r->addRoute('GET', '/admin/users', ['App\Controllers\AdminController', 'listUsers']);
    $r->addRoute('POST', '/admin/users/{id}/role', ['App\Controllers\AdminController', 'updateUserRole']);
    $r->addRoute('GET', '/admin/rooms', ['App\Controllers\AdminController', 'listRooms']);
    $r->addRoute('POST', '/admin/rooms/{id}/publish', ['App\Controllers\AdminController', 'toggleRoomPublish']);    
});

// 2. Get Request Method and URI
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

// Dispatch the request
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

// 3. Handle the Route
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 Not Found';
        break;

    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 Method Not Allowed';
        break;

   case FastRoute\Dispatcher::FOUND:
    $class = $routeInfo[1][0];
    $method = $routeInfo[1][1];
    $vars = $routeInfo[2];

    // ====== CREATE DEPENDENCIES FIRST ======
    $userRepository = new \App\Repositories\UserRepository();
    $roomRepository = new \App\Repositories\RoomRepository();
    
    $authService = new \App\Services\AuthService($userRepository);
    $roomService = new \App\Services\RoomService($roomRepository);
    
    // ====== CREATE CONTROLLER WITH DEPENDENCIES ======
    $controller = match($class) {
    'App\Controllers\RoomController' => new \App\Controllers\RoomController($authService, $roomService),
    'App\Controllers\AuthController' => new \App\Controllers\AuthController($authService), // Fixed
    'App\Controllers\CreatorController' => new \App\Controllers\CreatorController($authService, $roomService),
    'App\Controllers\AdminController' => new \App\Controllers\AdminController($authService, new \App\Services\AdminService($userRepository, $roomRepository)),
    default => throw new Exception("Controller $class not configured")
    };  

    // Call the method
    $controller->$method($vars);
    break;
}