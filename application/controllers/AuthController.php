<?php

namespace BronyCenter\Controller;

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
}
