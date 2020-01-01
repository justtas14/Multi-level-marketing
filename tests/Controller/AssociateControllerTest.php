<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class AssociateControllerTest extends WebTestCase
{
    /**
     * @var ReferenceRepository
     */
    private $fixtures;

    /**
     * @group legacy
     */
    protected function setUp() : void
    {
        parent::setUp();
        $this->fixtures = $this->loadFixtures([
            "App\DataFixtures\ORM\LoadUsers",
            "App\DataFixtures\ORM\LoadEmailTemplates"
        ])->getReferenceRepository();
    }
}
