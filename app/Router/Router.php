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
        [
            'path'       => '/online',
            'controller' => '\App\Controller\ChatController',
            'method'     => 'online',
        ],
        [
            'path'       => '/private-chat',
            'controller' => '\App\Controller\ChatController',
            'method'     => 'createChat',
        ],
        [
            'path'       => '/create-group',
            'controller' => '\App\Controller\ChatController',
            'method'     => 'createGroup',
        ],
        [
            'path'       => '/messages',
            'controller' => '\App\Controller\MessagesController',
            'method'     => 'getMessages',
        ],
        [
            'path'       => '/messages-older',
            'controller' => '\App\Controller\MessagesController',
            'method'     => 'getOlderMessages',
        ],
        [
            'path'       => '/add-message',
            'controller' => '\App\Controller\MessagesController',
            'method'     => 'addMessage',
        ],
        [
            'path'       => '/unread-counter',
            'controller' => '\App\Controller\MessagesController',
            'method'     => 'getUnreadCounter',
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
