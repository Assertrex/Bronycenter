<?php

namespace BronyCenter\Controller;

use BronyCenter\Repository\EmailKeyRepository;
use BronyCenter\Repository\UserRepository;

class AuthController extends ControllerBase
{
    public function indexAction($request, $response, $arguments)
    {
        $this->services['request']['basePath'] = $request->getAttribute('currentBase');
        $this->services['request']['currentGroups'] = $request->getAttribute('currentGroups');

        return $this->view->render($response, 'auth/index.twig', [
            'service' => $this->services,
            'indexed' => true,
        ]);
    }

    public function registerProcessAction($request, $response, $arguments)
    {
        $postValues = $request->getParsedBody();
        $postValues['registration_ip'] = $request->getAttribute('ip_address');

        // TODO: Validate user input

        // Add user to the database
        $userRepository = new UserRepository($this->entityManager);
        $user = $userRepository->createUser($postValues);

        // TODO: Return an error
        if (empty($user->getId())) {

        }

        // Create a key for e-mail confirmation
        $emailKeyRepository = new EmailKeyRepository($this->entityManager);
        var_dump($emailKeyRepository->createKey([
            'user_id' => $user->getId(),
            'hash' => substr(md5(uniqid(rand(), true)), 0, 16),
            'email' => $postValues['email']
        ]));

        // TODO: Send an email with e-mail confirmation link

        // TODO: Redirect back to the Index Auth page
    }
}
