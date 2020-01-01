<?php
namespace App\Tests;

use App\Entity\File;
use App\Entity\Gallery;
use App\Entity\Log;
use App\Entity\User;
use App\Tests\Reusables\LoginOperations;
use Codeception\Scenario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class ApiTester extends \Codeception\Actor
{
    use _generated\ApiTesterActions;
    use LoginOperations;


    public function __construct(Scenario $scenario)
    {
        parent::__construct($scenario);
    }

    public function authenticate($user)
    {
        $jwtManager = $this->grabService('pts_user.jwt.manager');
        $token = $this->getToken($jwtManager, $user);
        $this->amBearerAuthenticated($token);
        return $token;
    }


    public function loadFiles()
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getEntityManager();

        $container = $this->grabService('kernel')->getContainer();

        $path = $container->getParameter('kernel.project_dir').'/tests/_data/notimage.txt';

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

    public function loadPictures()
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getEntityManager();

        $container = $this->grabService('kernel')->getContainer();

        $path = $container->getParameter('kernel.project_dir').'/tests/_data/test.png';

        $ptsFile = new File();

        $image  = new UploadedFile(
            $path,
            'test.png',
            'image/jpeg',
            null
        );

        $ptsFile->setUploadedFileReference($image);
        $ptsFile->setOriginalName('test.png');
        $ptsFile->setName('test.png');

        $galleryFile = new Gallery();
        $galleryFile->setGalleryFile($ptsFile);
        $galleryFile->setMimeType('image/jpeg');

        $em->persist($galleryFile);
        $em->flush();
    }

    public function loadLogs()
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getEntityManager();

        $messages = [
            'Profile Updated!',
            'Registered!',
            'Prelaunch ended!',
            'Profile Updated!',
            'Profile Updated!',
            'Registered!',
            'Prelaunch ended!',
        ];
        foreach ($messages as $message) {
            $log = new Log();
            $log->setCreated(new \DateTime());
            $log->setLogMessage($message);

            $em->persist($log);
        }
        $em->flush();
    }

    public function getEntityManager()
    {
        $doctrineModule = $this->getDoctrineModule();
        return $doctrineModule->em;
    }
}
