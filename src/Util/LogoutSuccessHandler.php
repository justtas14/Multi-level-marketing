<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface
{
    private $mainUrl;

    public function __construct(string $mainUrl)
    {
        $this->mainUrl = $mainUrl;
    }

    public function onLogoutSuccess(Request $request)
    {
        return new RedirectResponse($this->mainUrl);
    }
}
