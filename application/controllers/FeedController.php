<?php

namespace BronyCenter\Controller;

class FeedController extends ControllerBase
{
    public function indexAction($request, $response, $arguments)
    {
        $this->services['request']['basePath'] = $request->getAttribute('currentBase');
        $this->services['request']['currentGroups'] = $request->getAttribute('currentGroups');

        return $this->view->render($response, 'social/index.twig', [
            'service' => $this->services,
            'indexed' => false,
        ]);
    }
}
