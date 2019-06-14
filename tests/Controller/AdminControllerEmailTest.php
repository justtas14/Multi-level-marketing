<?php


namespace App\Tests\Controller;

use App\Entity\EmailTemplate;
use App\Entity\User;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class AdminControllerEmailTest extends WebTestCase
{
    /**
     * @var ReferenceRepository
     */
    private $fixtures;

    /**
     * @group legacy
     */
    protected function setUp()
    {
        $this->fixtures = $this->loadFixtures([
            "App\DataFixtures\ORM\LoadUsers",
        ])->getReferenceRepository();
    }

    /**
     *  Testing fetcth email template service if it creates new default email template if it doesnt exist
     */
    public function testFetchEmail()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $client->request('GET', '/admin/emailtemplates');

        $emailTemplate = $em->getRepository(EmailTemplate::class)->findOneBy([]);

        $this->assertEquals("You got invited by {{senderName}}. ", $emailTemplate->getEmailSubject());
        $this->assertEquals("<br/> Here is your link {{link}} <br/><br/>", $emailTemplate->getEmailBody());
        $this->assertEquals("INVITATION", $emailTemplate->getEmailType());
    }
}
