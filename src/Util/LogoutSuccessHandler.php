<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function onLogoutSuccess(Request $request)
    {
        $redirectUri = $this->session->get('redirect_uri');

        if (!$redirectUri) {
            throw new HttpException(400, 'Cannot find redirect uri session variable');
        }

        return new RedirectResponse($redirectUri);
    }
}
