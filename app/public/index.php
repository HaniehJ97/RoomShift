<?php

/**
 * Main Application Entry Point
 */
// ====== SESSION CONFIGURATION ======
// Configure session BEFORE starting it
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

    // Home and Rooms
    $r->addRoute('GET', '/', ['App\Controllers\RoomController', 'index']);
    $r->addRoute('GET', '/rooms', ['App\Controllers\RoomController', 'index']);
    $r->addRoute('POST', '/rooms', ['App\Controllers\RoomController', 'store']);
    
    // Auth Routes
    $r->addRoute('GET', '/login', ['App\Controllers\AuthController', 'showLogin']);
    $r->addRoute('POST', '/login', ['App\Controllers\AuthController', 'login']);
    $r->addRoute('GET', '/register', ['App\Controllers\AuthController', 'showRegister']);
    $r->addRoute('POST', '/register', ['App\Controllers\AuthController', 'register']);
    $r->addRoute('GET', '/logout', ['App\Controllers\AuthController', 'logout']);
    
    // Admin Route
    $r->addRoute('GET', '/admin', ['App\Controllers\AdminController', 'dashboard']);
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

        // Instantiate the controller and call the method
        $controller = new $class();
        $controller->$method($vars);
        break;
}