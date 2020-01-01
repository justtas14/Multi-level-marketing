<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private $em;
    private $dispatcher;
    private $user;
    private $mainUrl;

    public function __construct(EntityManagerInterface $em, EventDispatcher $dispatcher, string $mainUrl)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->mainUrl = $mainUrl;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->user = $event->getAuthenticationToken()->getUser();
        $this->dispatcher->addListener(KernelEvents::RESPONSE, [$this, 'onKernelResponse']);
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($this->user && $event->getRequest()->getPathInfo() == '/login') {
            $event->setResponse(new RedirectResponse($this->mainUrl, 302, [
                'Access-Control-Allow-Origin' => 'True'
            ]));
        }
        return;
    }
}
