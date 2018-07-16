<?php

namespace BronyCenter\Controller;

use Doctrine\ORM\EntityManager;
use Psr\Container\ContainerInterface;

class ControllerBase
{
    protected $container;

    protected $entityManager;
    protected $router;
    protected $view;
    protected $session;
    protected $flash;
    protected $user;

    protected $services;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->entityManager = $container[EntityManager::class];
        $this->router = $container->get('router');
        $this->view = $container->get('view');
        $this->session = $container->get('session');
        $this->flash = $container->get('flash');

        if ($this->session->exists('user') && $this->session->get('user', 'logged') == true) {
            $this->user = [
                'id' => $this->session->get('user', 'id'),
                'type' => $this->session->get('user', 'type'),
            ];
        } else {
            $this->user = null;
        }

        $this->services = [
            'router' => $this->router,
            'session' => $this->session,
            'flash' => [
                'messages' => $this->flash->getMessages()
            ],
            'user' => $this->user,
        ];
    }
}
