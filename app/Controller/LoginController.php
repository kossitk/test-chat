<?php

namespace App\Controller;



use App\Response\Response;

class LoginController extends AbstractController
{

    public function login()
    {
        $username = '';
        if (isset($_POST['username']) && isset($_POST['password'])) {

        }

        return $this->renderView('login.php', [
            'username' => $username,
        ]);
    }
}