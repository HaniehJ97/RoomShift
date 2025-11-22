<?php

/**
 * Main Application Entry Point
 */

require __DIR__ . '/../vendor/autoload.php'; // Setup from boilerplate repo

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;

// 1. Define Routes
$dispatcher = simpleDispatcher(function (RouteCollector $r) {

    // 👇 Make RoomShift the homepage
    $r->addRoute('GET', '/', ['App\Controllers\RoomController', 'index']);

    // Example route from lecture (you can keep using this)
    $r->addRoute('GET', '/hello/{name}', ['App\Controllers\HelloController', 'greet']);

    // RoomShift routes
    // Show all rooms + form to create a new one
    $r->addRoute('GET', '/rooms', ['App\Controllers\RoomController', 'index']);

    // Handle create-room form submission (POST)
    $r->addRoute('POST', '/rooms', ['App\Controllers\RoomController', 'store']);

    // If you still want your old Home page for testing:
    // $r->addRoute('GET', '/home', ['App\Controllers\HomeController', 'home']);
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

// 3. Handle the Route (same style as your class example)
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

        // Get the class name (e.g. App\Controllers\RoomController)
        $class = $routeInfo[1][0];

        // Get the method name (e.g. 'index', 'store', 'greet')
        $method = $routeInfo[1][1];

        // Instantiate the controller
        $controller = new $class();

        // Dynamic variables from URL (e.g. {name})
        $vars = $routeInfo[2];

        // Call the controller method and pass $vars (array)
        // Your controller methods should accept one parameter, e.g. function index(array $vars = []) {}
        $controller->$method($vars);

        break;
}