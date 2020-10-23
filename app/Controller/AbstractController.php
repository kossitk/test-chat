<?php

namespace App\Controller;



use App\Database\Security;
use App\Response\Response;

abstract class AbstractController
{
    const TEMPLATE_DIR =  __DIR__ . "/../Views/";

    public function renderView($view, $params){
        extract($params);

        //Inject security object in template
        $security = new Security();

        // TODO : Generate NotFoundException
        require(self::TEMPLATE_DIR . $view);

        return new Response($templateFullContent);
    }

}