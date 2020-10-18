<?php


include 'classes/Autoloader.php';


// return htmlspecialchars(strip_tags($url));
session_start();
$path_only = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router = new \Router\Router();
$router->resolve($path_only)->send();
