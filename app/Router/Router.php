<?php

namespace App\Router;


use App\Database\Security;
use App\Response\RedirectResponse;
use App\Response\Response;

class Router
{
    private $loginPath = '/login';

    private $routes = [
        [
            'path'       => '/login',
            'controller' => '\App\Controller\SecurityController',
            'method'     => 'login',
        ],
        [
            'path'       => '/logout',
            'controller' => '\App\Controller\SecurityController',
            'method'     => 'logout',
        ],
        [
            'path'       => '/register',
            'controller' => '\App\Controller\SecurityController',
            'method'     => 'register',
        ],
        [
            'path'       => '/',
            'controller' => '\App\Controller\HomeController',
            'method'     => 'home',
        ],
    ];


    public function __construct()
    {
    }

    /**
     * @param $path
     * @return Response
     */
    public function resolve($path)
    {
        $found = false;
        $response = null;
        $path = $path == '' ? '/' : $path; // Fix empty path
        $security = new Security();

        if (!$security->isGranted($path))
        {
            return new RedirectResponse($this->loginPath);
        }

        foreach ($this->routes as $route) {
            if ($route['path'] == $path) { // TODO: Add regex validation
                $controller = new $route['controller']();
                $found      = true;

                $response   = $controller->{$route['method']}();
                break;
            }
        }

        if (!$found) {
            $response = new Response('Bad Request '. $path , 400);
        }

        if (!($response instanceof Response)) {
            $response = new Response('Internal server error', 500);
        }

        return $response;
    }


}
