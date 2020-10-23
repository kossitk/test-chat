<?php

namespace App\Database;


use App\Config\Configuration;
use App\Helper\PasswordEncoder;
use App\Model\User;

class Security
{

    protected $connected = false;
    protected $infos = [];

    private $access = [
        [
            'path'  => '/register',
            'security' => false,
        ],
        [
            'path'  => '/login',
            'security' => false,
        ],
        [
            'path'  => '/',
            'security' => 'USER',
        ],
    ];


    public function __construct()
    {
        $this->retriveUserFromSession();
    }

    /**
     * @return bool
     */
    public function isConnected(): bool
    {
        return $this->connected;
    }

    protected function __clone() { } // Méthode de clonage en privé aussi.

    /**
     * Return connected user infos as array or false if no user found
     *
     * @return array|false
     */
    public function getInfos()
    {
        if ($this->connected){
            return $this->infos;
        }

        return false;
    }

    protected function retriveUserFromSession()
    {
        if (isset($_SESSION['user']) && isset($_SESSION['user']['id'])) {
            $this->infos     = $_SESSION['user'];
            $this->connected = true;
        } else {
            $this->infos     = [];
            $this->connected = false;
        }
    }

    public function loginUser($login, $password)
    {
        try {
            $model = new User();
            $user = $model->findBy(['email' => $login], [], true);
            if ($user) {
                if (PasswordEncoder::verify($password, $user['password'])) {
                    unset($user['password']);
                    $this->infos = $user;
                    $this->connected = true;
                    $_SESSION['user'] = $this->infos;
                    return true;
                }
            }
            $this->infos = [];
            $this->connected = false;
        } catch (\Exception $exception) {
            $this->infos = [];
            $this->connected = false;
        }

        return false;
    }

    public function loginUserById($id)
    {
        try {
            $model = new User();
            $user = $model->find($id);
            if ($user){
                unset($user['password']);
                $this->infos = $user;
                $this->connected = true;
                $_SESSION['user'] = $this->infos;
            }
        } catch (\Exception $exception) {
            $this->infos = [];
            $this->connected = false;
        }
    }

    /**
     * @return bool
     */
    public function logout()
    {
        unset($_SESSION['user']);

        return session_destroy();
    }


    public function isGranted(string $path)
    {
        foreach ($this->access as $access) {
            if (strpos($path, $access['path']) === 0) {
                if ($access['security'] === false){
                    return true;
                }
                else{
                    // Try to get User from session
                    $security = new Security();
                    if ($security->isConnected())
                    {
                        // User must have required role
                        $roles = json_decode($this->infos['roles']);
                        if (false !== in_array($access['security'], $roles)){
                            return true;
                        }
                        return false;
                    }
                }
                break;
            }
        }
        return false;
    }

}
