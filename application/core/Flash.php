<?php

namespace BronyCenter\Core;

class Flash
{
    private $flashService;

    public function __construct($flashService)
    {
        $this->flashService = $flashService;
    }

    public function success($message)
    {
        $this->flashService->addMessage('success', $message);
    }

    public function info($message)
    {
        $this->flashService->addMessage('info', $message);
    }

    public function warning($message)
    {
        $this->flashService->addMessage('warning', $message);
    }

    public function error($message)
    {
        $this->flashService->addMessage('danger', $message);
    }
}
