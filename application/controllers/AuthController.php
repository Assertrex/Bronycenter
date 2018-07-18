<?php

namespace BronyCenter\Controller;

use BronyCenter\Core\Account;

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

    public function verifyAction($request, $response, $arguments)
    {
        $this->services['request']['basePath'] = $request->getAttribute('currentBase');
        $this->services['request']['currentGroups'] = $request->getAttribute('currentGroups');

        return $this->view->render($response, 'auth/index.twig', [
            'service' => $this->services,
            'indexed' => true,
        ]);
    }

    public function resendAction($request, $response, $arguments)
    {
        // Store values from query strings
        $queryValues['user_id'] = intval($request->getQueryParam('user_id', $default = null));
        $queryValues['hash'] = $request->getQueryParam('hash', $default = null);

        // Re-send mail with e-mail verification code
        (new Account($this->container))->resendVerificationCode($request, $queryValues);

        return $response->withRedirect(
            $this->router->pathFor('authIndex')
        );
    }

    public function loginProcessAction($request, $response, $arguments)
    {
        $postValues = $request->getParsedBody();
        $postValues['login_ip'] = $request->getAttribute('ip_address');

        // Find user and create a session
        if (!(new Account($this->container))->loginUser($request, $postValues)) {
            return $response->withRedirect(
                $this->router->pathFor('authIndex')
            );
        }

        // Redirect into social page
        return $response->withRedirect(
            $this->router->pathFor('socialIndex')
        );
    }

    public function registerProcessAction($request, $response, $arguments)
    {
        $postValues = $request->getParsedBody();
        $postValues['registration_ip'] = $request->getAttribute('ip_address');

        // Validate and create a new user
        (new Account($this->container))->createUser($request, $postValues);

        // Redirect into login page
        return $response->withRedirect(
            $this->router->pathFor('authIndex')
        );
    }
}
