<?php

namespace App\Tests\Controller;

use App\Entity\Configuration;
use App\Entity\EmailTemplate;
use App\Entity\File;
use App\Entity\Gallery;
use App\Entity\Invitation;
use App\Entity\InvitationBlacklist;
use App\Entity\User;
use App\Tests\Reusables\LoginOperations;
use DateTime;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\ORM\EntityManager;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AdminControllerTest extends WebTestCase
{
    use LoginOperations;

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
            "App\DataFixtures\ORM\LoadEmailTemplates",
            "App\DataFixtures\ORM\LoadBlackListEmails",
            "App\DataFixtures\ORM\LoadInvitations"
        ])->getReferenceRepository();

        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        $container = $this->getContainer();

        $path = $container->getParameter('kernel.project_dir').'/var/test_files/notimage.txt';

        for ($i = 0; $i < 30; $i++) {
            $galleryFile = new Gallery();
            $galleryFile->setId($i+1);

            $date = new \DateTime();

            $date->setTimestamp((time() + $i*100));

            $galleryFile->setCreated($date);
            $uploadedFile  = new UploadedFile(
                $path,
                'PtsFileName'.($i+1),
                'text/html',
                null
            );

            $ptsFile = new File();
            $ptsFile->setName('PtsFileName'.($i+1));
            $ptsFile->setOriginalName('PtsFileName'.($i+1));
            $ptsFile->setUploadedFileReference($uploadedFile);

            $galleryFile->setGalleryFile($ptsFile);

            $em->persist($ptsFile);
            $em->persist($galleryFile);
        }
        $em->flush();
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown() : void
    {
        parent::tearDown();
        $this->removeCreatedFiles();
    }

    private function removeCreatedFiles()
    {
        $container = $this->getContainer();

        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        $gaufretteFilteManager = $container->get('pts_file.manager');

        $fileObj = $em->getRepository(File::class);

        $allFiles = $fileObj->findAll();

        foreach ($allFiles as $file) {
            $gaufretteFilteManager->remove($file);
        }
    }

    /**
     *  Testing resend invitation functionality.
     *
     *  - Login as associate, get invitation fixture and call to '/api/associate/invite'
     * api with a param of invitation id
     *  - Expected successfully to be sent invite mail to fixture invitation email.
     *
     *  - This time call to '/api/associate/invite' api with not existing invitation id.
     *  - Expected to get not found error.
     */
    public function testResendInvitation()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user4');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $jwtManager = $client->getContainer()->get('pts_user.jwt.manager');

        $token = $this->getToken($jwtManager, $user);

        $invitation = $this->fixtures->getReference('invitation2');

        $client->enableProfiler();

        $client->xmlHttpRequest(
            'POST',
            '/api/associate/invite',
            ['invitationId' => $invitation->getId()],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame('You got invited by Bailey Brookes. ', $message->getSubject());
        $this->assertSame("noreply@plumtreesystems.com", key($message->getFrom()));
        $this->assertSame('jonas@gmail.com', key($message->getTo()));

        $client->xmlHttpRequest(
            'POST',
            '/api/associate/invite',
            ['invitationId' => -1000],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }


    /**
     *  Testing send method functionality if it sends correctly and content is the same as expected
     *
     *  - Send invitation which has 2 atributes:
     * Email: 'myemail@gmail.com'
     * Full name: 'myemail'
     *  - Expected to get one email with appropriate subject. Expected invitation entity to be added in database.
     * Email expected to get from sender email.
     * Email expected to sent to invitation set email. Expected to get appropriate api response that email is sent
     *
     *  - This time change invitationEmailTemplate body to delta format. Then send invitation which has 4 atributes:
     * Email: 'myemail@gmail.com'
     * Full name: 'myemail'
     *  - Expected to get one email with appropriate subject. Expected invitation entity to be added in database.
     * Email expected to get from sender email.
     * Email expected to sent to invitation set email.
     *
     *  - Send invitation but with invalid email address.
     *  - Expected to get error message that email is invalid.
     *
     *  - Send invitation but with oput out of invitations list email
     *  - Expected to get error message that associate is opt out of service..
     *
     *  - Send invitation but with already registered  associate email address.
     *  - Expected to get error message that associate is already exist.
     *
     */
    public function testMailIsSentAndContentIsOk()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $jwtManager = $client->getContainer()->get('pts_user.jwt.manager');

        $token = $this->getToken($jwtManager, $user);

        $client->enableProfiler();

        $invitationRepository = $em->getRepository(Invitation::class);

        $invitations = $invitationRepository->findAll();

        $this->assertEquals(6, sizeof($invitations));

        $client->xmlHttpRequest(
            'POST',
            '/api/associate/invite',
            ['email' => 'myemail@gmail.com', 'fullName' => 'myemail'],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(true, $responseArr['sent']['completed']);
        $this->assertEquals('myemail@gmail.com', $responseArr['sent']['address']);

        $invitations = $invitationRepository->findAll();

        $this->assertEquals(7, sizeof($invitations));

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame('You got invited by Connor Vaughan. ', $message->getSubject());
        $this->assertSame("noreply@plumtreesystems.com", key($message->getFrom()));
        $this->assertSame('myemail@gmail.com', key($message->getTo()));

        /** @var EmailTemplate $emailTemplateInvitation */
        $emailTemplateInvitation = $this->fixtures->getReference('emailTemplateInvitation');

        $emailTemplateInvitation->setEmailBody('{"ops":[{"insert":" Here is your "},'.
            '{"attributes":{"link":"{{link}}"},"insert":"link"},{"attributes":{"header":3},"insert":"\n"},'.
            '{"insert":" \nTo opt out of this service click \n"},'.'
            {"attributes":{"link":"{{ optOutUrl }}"},"insert":"this"},{"insert":"\n link\n"}]}');

        $em->persist($emailTemplateInvitation);
        $em->flush();

        $client->xmlHttpRequest(
            'POST',
            '/api/associate/invite',
            ['email' => 'myemail@gmail.com', 'fullName' => 'myemail'],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(true, $responseArr['sent']['completed']);
        $this->assertEquals('myemail@gmail.com', $responseArr['sent']['address']);

        $invitations = $invitationRepository->findAll();

        $this->assertEquals(8, sizeof($invitations));

        $this->assertSame(1, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertSame('You got invited by Connor Vaughan. ', $message->getSubject());
        $this->assertSame("noreply@plumtreesystems.com", key($message->getFrom()));
        $this->assertSame('myemail@gmail.com', key($message->getTo()));

        $client->xmlHttpRequest(
            'POST',
            '/api/associate/invite',
            ['email' => 'myemaifa', 'fullName' => 'myemail'],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals('Invalid email', $responseArr['formErrors']['invalidEmail']);

        $client->xmlHttpRequest(
            'POST',
            '/api/associate/invite',
            ['email' => 'AidanNewman@dayrep.com', 'fullName' => 'myemail'],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals('Associate already exists', $responseArr['formErrors']['invalidEmail']);

        /** @var InvitationBlacklist $invitationBlackList */
        $invitationBlackList = $this->fixtures->getReference('invitationBlackListEmail');
        $em->refresh($invitationBlackList);

        $client->xmlHttpRequest(
            'POST',
            '/api/associate/invite',
            ['email' => $invitationBlackList->getEmail(), 'fullName' => 'myemail'],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(
            'The person with this email has opted out of this service',
            $responseArr['formErrors']['invalidEmail']
        );
    }

    /**
     *  Testing end prelaunch feature
     *
     *  - Call end prelaunch api /api/admin/endprelaunch without data.
     *  - Expected to get appropriate json response data.
     *
     *  - Submit end prelaunch while calling to api /api/admin/endprelaunch
     * with correct prelaunchEnded and changeContent data.
     *  - Expected to get appropriate json response data that prelaunch has ended and form submitted succesfully without
     * errors.
     *
     *  - Submit end prelaunch while calling to api /api/admin/endprelaunch
     * with correct prelaunchEnded and empty changeContent data.
     *  - Expected to get appropriate json response data that changeContent value cannot be empty.
     *
     */
    public function testEndPrelaunch()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $jwtManager = $client->getContainer()->get('pts_user.jwt.manager');

        $token = $this->getToken($jwtManager, $user);

        $client->xmlHttpRequest(
            'POST',
            '/api/admin/endprelaunch',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals('', $responseArr['errorMessage']);
        $this->assertEquals(false, $responseArr['formSuccess']);
        $this->assertEquals('<h1>Prelaunch has ended!</h1>', $responseArr['configurationContent']);
        $this->assertEquals(false, $responseArr['hasPrelaunchEnded']);

        $client->xmlHttpRequest(
            'POST',
            '/api/admin/endprelaunch',
            ['prelaunchEnded' => 'true', 'landingContent' => '<h1>Prelaunch has ended!!!</h1>'],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals('', $responseArr['errorMessage']);
        $this->assertEquals(true, $responseArr['formSuccess']);
        $this->assertEquals('<h1>Prelaunch has ended!!!</h1>', $responseArr['configurationContent']);
        $this->assertEquals(true, $responseArr['hasPrelaunchEnded']);

        $client->xmlHttpRequest(
            'POST',
            '/api/admin/endprelaunch',
            ['prelaunchEnded' => 'true', 'landingContent' => ''],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals('Landing content cannot be empty!', $responseArr['errorMessage']);
        $this->assertEquals(false, $responseArr['formSuccess']);
        $this->assertEquals(null, $responseArr['configurationContent']);
        $this->assertEquals(true, $responseArr['hasPrelaunchEnded']);
    }

    /**
     *  Testing getCompanyRoot controller if it returns correct json response
     *
     *  - Request to /api/admin/explorer.
     *  - Expect to get json response about company
     *
     *  - Request to /api/admin/explorer with parameter id of 3
     *  - Expected to get 2 associates which has parent id 3 and appropriate values of json response.
     */
    public function testGetCompanyRoot()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $jwtManager = $client->getContainer()->get('pts_user.jwt.manager');

        $token = $this->getToken($jwtManager, $user);

        $client->xmlHttpRequest(
            'GET',
            '/api/admin/explorer',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(4, sizeof($responseArr));

        $this->assertEquals('-1', $responseArr['id']);
        $this->assertEquals("Company", $responseArr['title']);
        $this->assertEquals('-2', $responseArr['parentId']);
        $this->assertEquals('1', $responseArr['numberOfChildren']);

        $client->xmlHttpRequest(
            'GET',
            '/api/admin/explorer',
            ['id' => '3'],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $user6 = $em->getRepository(User::class)->find(6);
        $user7 = $em->getRepository(User::class)->find(7);

        $this->assertEquals(2, sizeof($responseArr));

        $usersArray = [
            [
                'id' => $user6->getId(),
                'title' => $user6->getAssociate()->getFullName(),
                'parentId' => $user6->getAssociate()->getParentId(),
                'numberOfChildren' => '1'
            ],
            [
                'id' => $user7->getId(),
                'title' => $user7->getAssociate()->getFullName(),
                'parentId' => $user7->getAssociate()->getParentId(),
                'numberOfChildren' => '3'
            ]
        ];

        $this->assertContains(
            $usersArray[0],
            $responseArr
        );

        $this->assertContains(
            $usersArray[1],
            $responseArr
        );
    }


    /**
     *  Testing downloadable csv file
     *
     *  - Request to /api/admin/csv api.
     *  - Expected to get headers which states that it returns attachment with a filename and it can be downloadable.
     */
    public function testExportToCsv()
    {
        $this->setOutputCallback(function () {
        });

        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $jwtManager = $client->getContainer()->get('pts_user.jwt.manager');

        $token = $this->getToken($jwtManager, $user);

        $client->xmlHttpRequest(
            'HEAD',
            '/api/admin/csv',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(
            "attachment; filename=associates.csv",
            $client->getResponse()->headers->all()['content-disposition']['0']
        );
    }

    /**
     *  Testing /api/admin/uploadGalleryFile api whether it returns serialized file.
     *
     *  - Request to /api/admin/uploadGalleryFile api with POST method with additional file param.
     *  - Expected to get 200 status code and serialized appropriate gallery file to be returned.
     *
     *  - Request to /api/admin/uploadGalleryFile api with POST method with additional not file param.
     *  - Expected to get '' response content as form is not valid.
     */
    public function testUploadGalleryFile()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $jwtManager = $client->getContainer()->get('pts_user.jwt.manager');

        $token = $this->getToken($jwtManager, $user);

        $path = $client->getContainer()->getParameter('kernel.project_dir').'/var/test_files/test.png';

        $image = new UploadedFile(
            $path,
            'test.png',
            'image/png',
            null
        );

        $client->xmlHttpRequest(
            'POST',
            '/api/admin/uploadGalleryFile',
            [],
            ['galleryFile' => $image],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $responseContent = json_decode($client->getResponse()->getContent(), true);

        $responseFile = $responseContent['file'];

        $this->assertArrayHasKey('id', $responseFile);
        $this->assertArrayHasKey('galleryFile', $responseFile);
        $this->assertArrayHasKey('filePath', $responseFile);
        $this->assertArrayHasKey('created', $responseFile);
        $this->assertEquals('image/png', $responseFile['mimeType']);

        $wrongFile = new UploadedFile(
            $path,
            'test.png',
            'image/png',
            404
        );

        $client->xmlHttpRequest(
            'POST',
            '/api/admin/uploadGalleryFile',
            [],
            ['galleryFile' => $wrongFile],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertEquals(
            '',
            $client->getResponse()->getContent()
        );
    }

    private function loadImages()
    {
        $container = $this->getContainer();

        $client = $this->makeClient();

        $em = $container->get('doctrine.orm.default_entity_manager');

        $ptsFile = new File();

        $path = $client->getContainer()->getParameter('kernel.project_dir').'/var/test_files/test.png';

        $image  = new UploadedFile(
            $path,
            'test.png',
            'image/png',
            null
        );

        $ptsFile->setUploadedFileReference($image);
        $ptsFile->setOriginalName('test.png');
        $ptsFile->setName('test.png');

        $galleryFile = new Gallery();
        $galleryFile->setGalleryFile($ptsFile);
        $galleryFile->setMimeType('image/png');

        $em->persist($galleryFile);
        $em->persist($ptsFile);
        $em->flush();

        return ['galleryId' => $galleryFile->getId(), 'fileId' => $ptsFile->getId()];
    }

    /**
     *  Testing remove gallery and pts file functionality
     *
     *  - Request with get method to /admin/remove api.
     *  - Expected to get '' content.
     *
     *  - Create pts and gallery files, and request with post method to /admin/remove api.
     *  - Expected to get OK status code and files to be deleted in database.
     *
     *  - Set change content termsOfServices file. THen attempt to remove inserted file by calling /admin/removeFile api
     *  with inserted file id.
     *  - Expected to not delete inserted file and expected response fileInUse to be true.
     */
    public function testRemoveFile()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        $container = $this->getContainer();

        $_SERVER['REQUEST_URI'] = "/api/admin/changecontent";

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $jwtManager = $client->getContainer()->get('pts_user.jwt.manager');

        $token = $this->getToken($jwtManager, $user);

        $em = $container->get('doctrine.orm.default_entity_manager');

        $ptsRepository = $em->getRepository(\App\Entity\File::class);
        $galleryRepo = $em->getRepository(Gallery::class);

        $fileIds = $this->loadImages();

        $galleryId = $fileIds['galleryId'];
        $fileId = $fileIds['fileId'];

        $allPtsFiles = $ptsRepository->findAll();
        $allGalleryFiles = $galleryRepo->findAll();

        $this->assertEquals(31, sizeof($allPtsFiles));
        $this->assertEquals(31, sizeof($allGalleryFiles));

        $params = [
            'params' => [
                'galleryId' => $galleryId,
                'fileId' => $fileId
            ]
        ];

        $params = json_encode($params);

        $client->xmlHttpRequest(
            'DELETE',
            '/api/admin/removeFile',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token],
            $params
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $allPtsFiles = $ptsRepository->findAll();
        $allGalleryFiles = $galleryRepo->findAll();

        $this->assertEquals(30, sizeof($allPtsFiles));
        $this->assertEquals(30, sizeof($allGalleryFiles));

        $fileIds = $this->loadImages();

        $galleryId = $fileIds['galleryId'];
        $fileId = $fileIds['fileId'];

        $client->xmlHttpRequest(
            'POST',
            '/api/admin/changecontent',
            ['hiddenMainLogoFile' => $galleryId],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $configuration = $em->getRepository(Configuration::class)->findOneBy([]);

        $em->refresh($configuration);

        $this->assertNotNull($configuration->getMainLogo());

        $allPtsFiles = $ptsRepository->findAll();
        $allGalleryFiles = $galleryRepo->findAll();

        $this->assertEquals(31, sizeof($allPtsFiles));
        $this->assertEquals(31, sizeof($allGalleryFiles));

        $params = [
            'params' => [
                'galleryId' => $galleryId,
                'fileId' => $fileId
            ]
        ];

        $params = json_encode($params);

        $client->xmlHttpRequest(
            'DELETE',
            '/api/admin/removeFile',
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token],
            $params
        );

        $allPtsFiles = $ptsRepository->findAll();
        $allGalleryFiles = $galleryRepo->findAll();

        $this->assertEquals(31, sizeof($allPtsFiles));
        $this->assertEquals(31, sizeof($allGalleryFiles));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(true, $responseArr['fileInUse']);
    }

    /**
     *  Testing jsonGallery api whether it returns correct serialized json format gallery files.
     *
     *  - Request to jsonGallery GET api with a parameter imageLimit 20.
     *  - Expecetd to get 20 files, each has appropriate download link. Also expected to get image extensions and
     * appropriate info about number of pages (current page - 1 and number of pages - 2).
     *
     *  - Request to jsonGallery GET api with parameter imageLimit 20 and this time with a page 2.
     *  - Expected to get 10 more files as there are 30 files. Also expected to get first and last file with appropriate
     *  download url. Also expected to get number of page value 2 and current page value 2.
     *
     *  - Request to jsonGallery GET api with parameter imageLimit 20 and this time with a page 3.
     *  - Expected to get 404 error as there is max 2 pages and there is no page 3.
     *
     *  - Request to jsonGallery GET api with parameter imageLimit 20 and this time with a page called 'notPage'.
     *  - Expected to get 404 error as page must be of type number.
     *
     *  - Request to jsonGallery GET api with a parameter imageLimit 20, page 1 and this time with a category images
     *  - Expecetd to get 0 files as there is no images currently in database.
     *
     *  - Request to jsonGallery GET api with a parameter imageLimit 20, page 1 and this time with a catetogry files
     *  - Expecetd to get 20 files, each has appropriate download link. Also expected to get image extensions and
     * appropriate info about number of pages (current page - 1 and number of pages - 2).
     *
     *  - Request to jsonGallery GET api with a parameter imageLimit 20, page 1 and this time with a catetogry 'unknown'
     *  - Expecetd to get 404 error as there is no category named 'unknown'.
     *
     */
    public function testJsonGallery()
    {
        /** @var EntityManager $em */
        $em = $this->fixtures->getManager();

        /** @var User $user */
        $user = $this->fixtures->getReference('user1');

        $em->refresh($user);
        $this->loginAs($user, 'main');

        $client = $this->makeClient();

        $jwtManager = $client->getContainer()->get('pts_user.jwt.manager');

        $token = $this->getToken($jwtManager, $user);

        $client->xmlHttpRequest(
            'GET',
            '/api/admin/jsonGallery',
            ['imageLimit' => 20],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(20, sizeof($responseArr['files']));

        $this->assertEquals('30', $responseArr['files']['0']['id']);
        $this->assertEquals('/api/download/30', $responseArr['files']['0']['filePath']);

        $this->assertEquals('11', $responseArr['files']['19']['id']);
        $this->assertEquals('/api/download/11', $responseArr['files']['19']['filePath']);

        $this->assertEquals(2, $responseArr['pagination']['numberOfPages']);
        $this->assertEquals(1, $responseArr['pagination']['currentPage']);

        $client->xmlHttpRequest(
            'GET',
            '/api/admin/jsonGallery',
            ['imageLimit' => 20, 'page' => 2],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(10, sizeof($responseArr['files']));

        $this->assertEquals('10', $responseArr['files']['0']['id']);
        $this->assertEquals('/api/download/10', $responseArr['files']['0']['filePath']);

        $this->assertEquals('1', $responseArr['files']['9']['id']);
        $this->assertEquals('/api/download/1', $responseArr['files']['9']['filePath']);

        $this->assertEquals(2, $responseArr['pagination']['numberOfPages']);
        $this->assertEquals(2, $responseArr['pagination']['currentPage']);

        $client->xmlHttpRequest(
            'GET',
            '/api/admin/jsonGallery',
            ['imageLimit' => 20, 'page' => 3],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $client->xmlHttpRequest(
            'GET',
            '/api/admin/jsonGallery',
            ['imageLimit' => 20, 'page' => 'notPage'],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());

        $client->xmlHttpRequest(
            'GET',
            '/api/admin/jsonGallery',
            ['imageLimit' => 20, 'category' => 'images', 'page' => 1],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(0, sizeof($responseArr['files']));

        $this->assertEquals(1, $responseArr['pagination']['numberOfPages']);
        $this->assertEquals(1, $responseArr['pagination']['currentPage']);

        $client->xmlHttpRequest(
            'GET',
            '/api/admin/jsonGallery',
            ['imageLimit' => 20, 'category' => 'files', 'page' => 1],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $jsonResponse = $client->getResponse()->getContent();

        $responseArr = json_decode($jsonResponse, true);

        $this->assertEquals(20, sizeof($responseArr['files']));

        $this->assertEquals('30', $responseArr['files']['0']['id']);
        $this->assertEquals('/api/download/30', $responseArr['files']['0']['filePath']);

        $this->assertEquals('11', $responseArr['files']['19']['id']);
        $this->assertEquals('/api/download/11', $responseArr['files']['19']['filePath']);

        $this->assertEquals(2, $responseArr['pagination']['numberOfPages']);
        $this->assertEquals(1, $responseArr['pagination']['currentPage']);

        $client->xmlHttpRequest(
            'GET',
            '/api/admin/jsonGallery',
            ['imageLimit' => 20, 'category' => 'unknown', 'page' => 1],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer '.$token]
        );

        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
