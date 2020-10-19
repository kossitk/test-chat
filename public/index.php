<?php
require_once __DIR__.'/../vendor/autoload.php';

use App\Router\Router;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//die(\App\Database\MyPdo::getInstance());

// return htmlspecialchars(strip_tags($url));
session_start();
$path_only = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router = new \App\Router\Router();
$router->resolve($path_only)->send();
