<?php

/**
 * Main Application Entry Point
 */

require __DIR__ . '/../vendor/autoload.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// 1. Define Routes
$dispatcher = simpleDispatcher(function (RouteCollector $r) {

    // RoomShift routes - FIXED: No duplicates!
    $r->addRoute('GET', '/', ['App\Controllers\RoomController', 'index']);
    
    // Show all rooms + form to create a new one
    $r->addRoute('GET', '/rooms', ['App\Controllers\RoomController', 'index']);

    // Handle create-room form submission (POST)
    $r->addRoute('POST', '/rooms', ['App\Controllers\RoomController', 'store']);

    // Example route from lecture (optional - comment out if you don't have HelloController)
    // $r->addRoute('GET', '/hello/{name}', ['App\Controllers\HelloController', 'greet']);
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