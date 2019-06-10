<?php


namespace App\Service;

use App\Entity\Configuration;
use Doctrine\ORM\EntityManagerInterface;

class ConfigurationManager
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function getConfiguration()
    {
        $configurationRepo = $this->em->getRepository(Configuration::class);

        $configuration = $configurationRepo->findOneBy([]);

        if (!$configuration) {
            $configuration = $this->createConfiguration();
        }
        return $configuration;
    }

    private function createConfiguration(): Configuration
    {
        $configuration = new Configuration();
        $configuration->setLandingContent("<h1>Prelaunch has ended!</h1>");
        $configuration->setMainLogo(null);
        $configuration->setTermsOfServices(null);
        $this->em->persist($configuration);
        $this->em->flush();
        return $configuration;
    }
}
