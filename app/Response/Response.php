<?php

namespace App\Response;


class Response {

    /**
     * @var array
     */
    public $headers;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var int
     */
    protected $statusCode;


    public function __construct($content = '', int $status = 200, array $headers = [], bool $json = false)
    {
        if ($json) {
            $content = json_encode($content);
            $headers['Content-Type'] = 'application/json';
        }
        $this->headers = $headers;

        $this->content = $content;
        $this->statusCode = $status;
    }

    /**
     * Sends HTTP headers and content.
     *
     * @return $this
     */
    public function send()
    {
        $this->sendHeaders();
        echo $this->content;

        return $this;
    }

    public function sendHeaders()
    {
        if (headers_sent()) {
            return $this;
        }

        // Fix Content-Type charset
        $charset = 'UTF-8';
        if (!isset($headers['Content-Type'])) {
            $headers['Content-Type'] = 'text/html; charset='.$charset;
        } elseif (0 === stripos($headers['Content-Type'], 'text/') && false === stripos($headers['Content-Type'], 'charset')) {
            // add the charset
            $headers->set('Content-Type', $headers->get('Content-Type').'; charset='.$charset);
        }

        // headers
        foreach ($this->headers as $name => $values) {
            $replace = 0 === strcasecmp($name, 'Content-Type');
            foreach ($values as $value) {
                header($name.': '.$value, $replace, $this->statusCode);
            }
        }

        return $this;
    }

}