<?php


namespace App\Controller;


use App\Database\Security;
use App\Model\Chat;
use App\Model\User;

class HomeController extends AbstractController
{
    public function home()
    {
        $security = new Security();
        $chatModel = new Chat();
        $chats = $chatModel->getUserChats($security->getInfos()['id']);

        return $this->renderView('home.php', [
            'chats' => $chats,
        ]);
    }

}