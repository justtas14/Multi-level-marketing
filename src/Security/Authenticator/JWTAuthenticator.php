<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 2018-05-02
 * Time: 17:47
 */

namespace App\Security\Authenticator;

use PlumTreeSystems\UserBundle\Security\JWTAuthenticator as PtsJWTAuthenticator;
use Symfony\Component\HttpFoundation\Request;

class JWTAuthenticator extends PtsJWTAuthenticator
{
    public function supports(Request $request)
    {
        $cookie = $request->cookies->get('authToken');
        if ($cookie) {
            return true;
        }

        return parent::supports($request);
    }

    public function getCredentials(Request $request)
    {
        $cookie = $request->cookies->get('authToken');
        if ($cookie) {
            return $cookie;
        }
        return parent::getCredentials($request);
    }
}
