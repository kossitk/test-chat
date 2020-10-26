<?php


namespace App\Controller;


use App\Database\Security;
use App\Helper\UserInputFilter;
use App\Model\Chat;
use App\Model\Member;
use App\Model\Message;
use App\Model\User;
use App\Response\JsonResponse;
use App\Response\RedirectResponse;

class MessagesController extends AbstractController
{
    public function getMessages()
    {
        $security = new Security();
        $userId   = $security->getInfos()['id'];
        $data     = ['success' => false];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validations = ['chat' => 'uuid4', 'lastMessage' => 'uuid4'];
            $sanitation  = ['chat' => 'string'];
            $required    = ['chat'];
            $validator   = new UserInputFilter($validations, $required, $sanitation);
            $userInput   = $validator->sanitize($_POST);
            $valid       = $validator->validate($userInput);

            if ($valid) {
                $chatModel = new Chat();
                $chat      = $chatModel->findUserChatByUuid($userInput['chat'], $userId);
                if ($chat) {
                    $messagesModel = new Message();
                    $data['success'] = true;
                    $data['messages'] = $messagesModel->getMessages($chat['id'], @$userInput['lastMessage'], true, true, $userId);
                } else {
                    $data['message'] = "conversation not found";
                }
            } else {
                $data['message'] = "Invalid input";
            }
        }

        return new JsonResponse($data);
    }

    public function getOlderMessages()
    {
        $security = new Security();
        $userId   = $security->getInfos()['id'];
        $data     = ['success' => false];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validations = ['chat' => 'uuid4', 'first' => 'uuid4'];
            $sanitation  = ['chat' => 'string', 'first' => 'string'];
            $required    = ['chat', 'first'];
            $validator   = new UserInputFilter($validations, $required, $sanitation);
            $userInput   = $validator->sanitize($_POST);
            $valid       = $validator->validate($userInput);
            $data['userInput'] = $userInput;

            if ($valid) {
                $chatModel = new Chat();
                $chat      = $chatModel->findUserChatByUuid($userInput['chat'], $userId);
                if ($chat) {
                    $messagesModel = new Message();
                    $data['success'] = true;
                    $data['messages'] = $messagesModel->getMessages($chat['id'], $userInput['first'], false, false);
                } else {
                    $data['message'] = "conversation not found";
                }
            } else {
                $data['message'] = "Invalid input";
            }
        }

        return new JsonResponse($data);
    }

    public function addMessage()
    {
        $security = new Security();
        $userId   = $security->getInfos()['id'];
        $data     = ['success' => false];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validations = ['chat' => 'uuid4', 'message' => 'anything', 'lastMessage' => 'uuid4'];
            $sanitation  = ['chat' => 'string', 'message' => 'string'];
            $required    = ['chat', 'message'];
            $validator   = new UserInputFilter($validations, $required, $sanitation);
            $userInput   = $validator->sanitize($_POST);
            $valid       = $validator->validate($userInput);

            if ($valid) {
                $chatModel = new Chat();
                $chat      = $chatModel->findByUuid($userInput['chat']);
                if ($chat) {
                    $messagesModel = new Message();
                    $data['success'] = $messagesModel->addMessage($chat['id'], $userId, trim($userInput['message']));
                    $data['messages'] = $messagesModel->getMessages($chat['id'], @$userInput['lastMessage'], true, true, $userId, 200);
                } else {
                    //$data['payload'] = [$chat, $userInput];
                    $data['message'] = "conversation not found";
                }
            } else {
                $data['message'] = "Invalid input";
            }
        }

        return new JsonResponse($data);
    }

    public function getUnreadCounter()
    {
        $security = new Security();
        $userId   = $security->getInfos()['id'];
        $memberModel = new Member();

        return new JsonResponse($memberModel->getUnreadCounter($userId));
    }
}