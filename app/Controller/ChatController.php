<?php


namespace App\Controller;


use App\Database\Security;
use App\Helper\UserInputFilter;
use App\Model\Chat;
use App\Model\User;
use App\Response\RedirectResponse;

class ChatController extends AbstractController
{
    public function online()
    {
        $security   = new Security();
        $userUuid   = $security->getInfos()['uuid'];
        $usersModel = new User();

        $online = $usersModel->getConnectedUsers();

        return $this->renderView('online.php', [
            'users'    => $online,
            'userUuid' => $userUuid,
        ]);
    }

    public function createChat()
    {
        $security = new Security();
        $userUuid = $security->getInfos()['uuid'];
        if (isset($_GET['user']) && strlen(trim($_GET['user'])) > 20 && $userUuid != $_GET['user']) {
            $usersModel = new User();
            $user       = $usersModel->findByUuid($_GET['user']);
            if ($user) {
                // try to find a existing private chat
                $chatModel    = new Chat();
                $existingChat = $chatModel->hasPrivateChat($security->getInfos()['id'], $user['id']);
                if ($existingChat) {
                    $_SESSION['flash']['info'] = 'You have ongoing conversation with this user!';
                    return new RedirectResponse('/?chat='.$existingChat);
                } else {
                    $result = $chatModel->createChat([
                        'private'    => 1,
                        'admin'      => null,
                        'group_name' => null,
                        'members'    => [
                            $security->getInfos()['id'],
                            $user['id'],
                        ],
                    ]);
                    if ($result) {
                        $chat                         = $chatModel->find($result);
                        $_SESSION['flash']['success'] = 'Start new conversation';
                        return new RedirectResponse('/?chat='.$chat['uuid']);
                    } else {
                        $_SESSION['flash']['danger'] = 'User not found';
                    }
                }
            } else {
                $_SESSION['flash']['danger'] = 'User not found';
            }
        } else {
            $_SESSION['flash']['danger'] = 'Missing user in request';
        }

        return new RedirectResponse('/');
    }

    public function createGroup()
    {
        $security = new Security();
        $userId   = $security->getInfos()['id'];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validations = ['name' => 'words'];
            $sanitation  = ['name' => 'string'];
            $required    = ['name'];
            $validator   = new UserInputFilter($validations, $required, $sanitation);
            $userInput   = $validator->sanitize($_POST);
            $valid       = $validator->validate($userInput);

            if ($valid) {
                $chatModel = new Chat();
                $result    = $chatModel->createChat([
                    'private'    => 0,
                    'admin'      => $userId,
                    'group_name' => $userInput['name'],
                    'members'    => [$userId],
                ]);
                if ($result) {
                    $chat                         = $chatModel->find($result);
                    $_SESSION['flash']['success'] = 'Group created! Add users from online user\'s menu';
                    return new RedirectResponse('/?chat='.$chat['uuid']);
                } else {
                    $_SESSION['flash']['danger'] = 'User not found';
                }
            } else {
                $_SESSION['flash']['danger'] = 'Please review your input, there are some errors.';
            }
        }

        return new RedirectResponse('/');
    }

}