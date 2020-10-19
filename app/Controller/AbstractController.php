<?php

namespace App\Controller;



use App\Response\Response;

class AbstractController
{
    const TEMPLATE_DIR =  __DIR__ . "/../Views/";

    public function renderView($view, $params){
        extract($params);

        // TODO : Generate NotFoundException
        require(self::TEMPLATE_DIR . $view);

        return new Response($templateContent);
    }

}