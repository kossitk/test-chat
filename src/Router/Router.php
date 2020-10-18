<?php

namespace Router;


use Response\Response;

class Router
{

    private $routes = [
        [
            'path'       => '/',
            'controller' => '\App\Controller\TokenController',
            'method'     => 'requestToken',
        ],
        [
            'path'       => '/bitrix/token/validity',
            'controller' => '\App\Controller\TokenController',
            'method'     => 'checkToken',
        ],
        [
            'path'       => '/bitrix/token/refresh',
            'controller' => '\App\Controller\TokenController',
            'method'     => 'refreshToken',
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

        foreach ($this->routes as $route) {
            if ($route['path'] == $path) {
                $controller = new $route['controller']();
                $response   = $controller->{$route['method']}();
                $found      = true;
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
