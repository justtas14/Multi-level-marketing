<?php


namespace App\Service;

use App\Entity\Associate;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CreateAdmin
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var AssociateManager $associateManager */
    private $associateManager;

    public function __construct(EntityManagerInterface $entityManager, AssociateManager $associateManager)
    {
        $this->em = $entityManager;
        $this->associateManager = $associateManager;
    }

    public function createAdmin($email, $password, $fullName, $mobilePhone)
    {
        $associate = new Associate();
        $user = new User();

        $user->setEmail($email);
        $user->setPlainPassword($password);
        $associate->setFullName($fullName);
        $invitationUserName = $this->associateManager->createUniqueUserNameInvitation($associate->getFullName());
        $associate->setInvitationUserName($invitationUserName);
        $associate->setEmail($email);
        $associate->setMobilePhone($mobilePhone);
        $user->setAssociate($associate);
        $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);

        $this->em->persist($associate);
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }
}
