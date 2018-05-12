<?php

/**
 * Session start
 */
session_start();

/**
 * Autoloader
 */
require_once dirname(__DIR__) . '/vendor/twig/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

spl_autoload_register('autoLoader');

function autoLoader($class)
{
    $root = dirname(__DIR__); //get the parent directory
    $file = $root . '/' . str_replace('\\', '/', $class) . '.php';

    if (is_readable($file)) {
        require $root . '/' . str_replace('\\', '/', $class) . '.php';
    }
}

/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('\Core\Error::errorHandler');
set_exception_handler('\Core\Error::exceptionHandler');


/**
 * Routing
 */
$router = new Core\Router();

//Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('users', ['controller' => 'Users', 'action' => 'index']);
$router->add('{controller}/{action}');
$router->add('{controller}/{id:\d+}/{action}');
$router->add('admin/{controller}/{action}', ['namespace' => 'Admin']);

$router->dispatch($_SERVER['QUERY_STRING']);