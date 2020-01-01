<?php

namespace App\Tests\Reusables;

use App\Entity\User;
use PlumTreeSystems\UserBundle\Service\JWTManager;

trait LoginOperations
{
    public function getToken(JWTManager $jwtManager, User $user)
    {
        $token = $jwtManager->createToken($user);
        return $token;
    }
}
