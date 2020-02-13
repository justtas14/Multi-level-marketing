<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{
    private $em;
    private $dispatcher;
    private $user;
    private $session;

    public function __construct(EntityManagerInterface $em, EventDispatcher $dispatcher, SessionInterface $session)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->session = $session;
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->user = $event->getAuthenticationToken()->getUser();
        $this->dispatcher->addListener(KernelEvents::RESPONSE, [$this, 'onKernelResponse']);
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $redirectUri = $this->session->get('redirect_uri');
        if ($this->user && $event->getRequest()->getPathInfo() == '/login') {
            if (!$redirectUri) {
                throw new HttpException(400, 'Cannot find redirect uri session variable');
            }
            $event->setResponse(new RedirectResponse($redirectUri, 302, []));
        }
        return;
    }
}
