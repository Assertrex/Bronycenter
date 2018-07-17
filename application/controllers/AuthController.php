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
