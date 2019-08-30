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

    public function getParsedLandingContent($landingContent)
    {
        try {
            $parser = new \DBlackborough\Quill\Parser\Html();
            $renderer = new \DBlackborough\Quill\Renderer\Html();

            $parser->load($landingContent)->parse();

            $landingContent = $renderer->load($parser->deltas())->render();
        } catch (\Exception $exception) {
        }
        return $landingContent;
    }

    private function createConfiguration(): Configuration
    {
        $configuration = new Configuration();
        $configuration->setId(null);
        $configuration->setLandingContent("<h1>Prelaunch has ended!</h1>");
        $configuration->setMainLogo(null);
        $configuration->setTermsOfServices(null);
        $this->em->persist($configuration);
        $this->em->flush();
        return $configuration;
    }
}
