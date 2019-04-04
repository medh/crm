<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

define('ROOT', dirname(__DIR__ . '/..'));
require ROOT . '/app/App.php';
App::load();

$routeType = isset($_GET['api']) ? 'api' : 'pages';
$router = new App\Components\Router\Router($_GET['api'] ?? $_GET['p'] ?? '/user/login');
$routes = json_decode(file_get_contents(ROOT . '/app/routes.json'), true)[$routeType];

foreach ($routes as $route) {
    $router->add(array_merge($route, ['type' => $routeType]));
}

try {
    $router->run();
} catch (\App\Components\Exception\CustomException $e) {
    $e->show();
}


