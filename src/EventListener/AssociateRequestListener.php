<?php


namespace App\EventListener;

use App\Entity\Configuration;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AssociateRequestListener
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var UrlGeneratorInterface $router */
    private $router;

    /** @var TokenStorageInterface $tokenStorage */
    private $tokenStorage;

    public function __construct(
        EntityManagerInterface $entityManager,
        UrlGeneratorInterface $router,
        TokenStorageInterface $tokenStorage
    ) {
        $this->em = $entityManager;
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if ($event->getController()[1] == 'landingPage') {
            return;
        }

        $token = $this->tokenStorage->getToken();

        if (!$token) {
            return;
        }

        /** @var User $user */
        $user = $token->getUser();
        if (!$user || !$user instanceof UserInterface) {
            return;
        }

        $configuration = $this->em->getRepository(Configuration::class)->findOneBy([]);
        if (!$configuration) {
            return;
        }

        if ($configuration->hasPrelaunchEnded() && !in_array('ROLE_ADMIN', $user->getRoles())) {
            $redirectUrl = $this->router->generate("landingpage");
            $event->setController(function () use ($redirectUrl) {
                return new RedirectResponse($redirectUrl);
            });
        }
        return;
    }
}
