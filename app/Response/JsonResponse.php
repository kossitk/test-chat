<?php


namespace App\Response;


class JsonResponse extends Response
{
    public function __construct($content = '', int $status = 200, array $headers = [])
    {
        $content = json_encode($content);
        $headers['Content-Type'] = 'application/json';

        parent::__construct($content, $status, $headers);
    }
}