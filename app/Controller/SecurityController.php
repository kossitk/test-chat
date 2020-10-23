<?php

namespace App\Controller;



use App\Database\Security;
use App\Model\User;
use App\Response\JsonResponse;
use App\Response\RedirectResponse;
use App\Response\Response;
use App\Helper\UserInputFilter;

class SecurityController extends AbstractController
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

    public function register()
    {
        $formErrors = [];
        $userInput = [];
        $errorMessage = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validations = [
                'username'  => 'username',
                'email'     => 'email',
                'password'  => 'password',
                'confirm-password' => 'confirmation',
            ];
            $sanitation = [
                'username'  => 'string',
                'email'     => 'email',
                'password'  => false,
                'confirm-password' => false,
            ];
            $required = ['username', 'email', 'password', 'confirm-password'];
            $validator = new UserInputFilter($validations, $required, $sanitation);
            $userInput = $validator->sanitize($_POST);

            $valid = $validator->validate($userInput);
            $formErrors = $validator->getErrors();


            if ($valid) {
                $userModel = new User();
                $userInput['roles'] = json_encode(['USER']);
                $result = $userModel->createUser($userInput);

                if (true === $result['status']) {
                    $security = new Security();
                    $security->loginUserById($result['id']);

                    return new RedirectResponse('/');
                }
                else{
                    $errorMessage = $result['message'];
                }
            }
            else{
                $errorMessage = 'Please review your input, there are some errors.';
            }
        }

        return $this->renderView('login.php', compact('userInput', 'formErrors', 'errorMessage'));
    }

    public function logout()
    {
        $security = new Security();
        $security->logout();

        return new RedirectResponse('/login');
    }

}