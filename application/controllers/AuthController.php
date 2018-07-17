<?php

namespace BronyCenter\Controller;

use BronyCenter\Core\Flash;
use BronyCenter\Core\Mail;
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

        // Check if user's account has been correctly created
        if (empty($user->getId())) {
            (new Flash($this->flash))->error(
                'Account couldn\'t be created due to unknown error.'
            );

            return $response->withRedirect(
                $this->router->pathFor('authIndex')
            );
        }

        // Create a key for e-mail confirmation
        $emailKeyRepository = new EmailKeyRepository($this->entityManager);
        $key = $emailKeyRepository->createKey([
            'user_id' => $user->getId(),
            'hash' => substr(md5(uniqid(rand(), true)), 0, 16),
            'email' => $postValues['email']
        ]);

        // Check if user's account has been correctly created
        if (empty($key->getId())) {
            (new Flash($this->flash))->error(
                'Unknown error occurred while trying to create verification link. You can try to create an account again.'
            );

            return $response->withRedirect(
                $this->router->pathFor('authIndex')
            );
        }

        // Send an email with e-mail confirmation link
        (new Mail())->sendAsTemplate($key->getEmail(), 'verification', [
            'display_name' => $key->getUser()->getDisplayName(),
            'verification_link' => $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() .
                $this->router->pathFor('authVerify') . '?user_id=' . $key->getUser()->getId() . '&hash=' . $key->getHash()
        ]);

        // Return a success flash message
        (new Flash($this->flash))->success(
            'Your account has been successfully created! ' .
            'Click on a verification link sent to your e-mail address to confirm your account. ' .
            'If you can\'t find it, check your spam folder.'
        );

        // Redirect into login page
        return $response->withRedirect(
            $this->router->pathFor('authIndex')
        );
    }
}
