<?php

namespace App\Service;

use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;

class Logging
{

    /** @var EntityManagerInterface $em */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function createLog($message, $type)
    {
        $log = new Log();
        $log->setLogMessage($message);
        $log->setLogType($type);

        $this->em->persist($log);
        $this->em->flush();
    }
}
