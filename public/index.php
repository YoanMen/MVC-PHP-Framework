<?php

use App\Core\Router as Router;
use App\Core\Autoloader as Autoloader;

require_once "../App/Core/Autoloader.php";


session_start();

if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


DEBUG ? ini_set('display_errors', 1) : ini_set('display_errors', 0);



// ROUTING
$router = new Router;
$router->addRoute('GET', ROOT . '/error', 'PageNotFoundController', 'index');
$router->addRoute('GET', ROOT . '/', 'HomeController', 'index');
$router->addRoute('GET', ROOT . '/upload', 'UploadController', 'index');
$router->addRoute('POST', ROOT . '/upload', 'UploadController', 'uploadFile');

$router->addRoute('GET', ROOT . '/test/{paramName}', 'HomeController', 'withParams');

$router->addRoute('GET', ROOT . '/login', 'AuthController', 'login');
$router->addRoute('POST', ROOT . '/login', 'AuthController', 'login');
$router->addRoute('GET', ROOT . '/logout', 'AuthController', 'logout');

$router->addRoute('GET', ROOT . '/dashboard', 'AuthController', 'index');


$router->goRoute($router);

