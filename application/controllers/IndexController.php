<?php

namespace BronyCenter\Controller;

class IndexController extends ControllerBase
{
    public function indexAction($request, $response, $arguments)
    {
        $this->services['request']['basePath'] = $request->getAttribute('currentBase');
        $this->services['request']['currentGroups'] = $request->getAttribute('currentGroups');

        return $this->view->render($response, 'index/index.twig', [
            'service' => $this->services,
            'indexed' => true,
        ]);
    }
}
