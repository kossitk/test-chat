<?php


namespace App\Response;


class RedirectResponse extends Response
{
    public function __construct(string $url, int $status = 302, array $headers = [])
    {
        $content = $this->getContentUrl($url);
        $this->headers['Location'] = $url;
        parent::__construct($content, $status, $headers);
    }

    /**
     * Fix redirection problems within browser
     *
     * @param  string  $url
     * @return string
     */
    public function getContentUrl(string $url)
    {
        return
            sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="refresh" content="0;url=\'%1$s\'" />

        <title>Redirecting to %1$s</title>
    </head>
    <body>
        Redirecting to <a href="%1$s">%1$s</a>.
    </body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8'));

    }

}