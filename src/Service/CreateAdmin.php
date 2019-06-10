<?php


namespace App\Service;

use App\Entity\Associate;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CreateAdmin
{
    /** @var EntityManagerInterface $em */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function createAdmin($email, $password, $fullName, $mobilePhone)
    {
        $associate = new Associate();
        $user = new User();

        $user->setEmail($email);
        $user->setPlainPassword($password);
        $associate->setFullName($fullName);
        $associate->setEmail($email);
        $associate->setMobilePhone($mobilePhone);
        $user->setAssociate($associate);
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        $this->em->persist($associate);
        $this->em->flush();
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
