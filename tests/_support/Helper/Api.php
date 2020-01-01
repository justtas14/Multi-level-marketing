<?php
namespace App\Tests\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use App\DataFixtures\ORM\LoadBlackListEmails;
use App\DataFixtures\ORM\LoadEmailTemplates;
use App\DataFixtures\ORM\LoadInvitations;
use App\DataFixtures\ORM\LoadUsers;
use App\Tests\ApiTester;
use App\Tests\Reusables\LoginOperations;

class Api extends \Codeception\Module
{
    public function getSymfonyModule()
    {
        return $this->getModule('Symfony');
    }
    public function getDoctrineModule()
    {
        return $this->getModule('Doctrine2');
    }
}
